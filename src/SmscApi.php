<?php

namespace Maestro\SmscApi;


class SmscApi extends Smsc
{

    private $curl;

    public function sendSms($phones, $message, $time = 0, $format = SmscFormats::VIBER, $sender = false, $query = "", $files = [])
    {
        $this->setFiles($files);
        $this->setPhone($phones)->setMessage($message)->setTime($time)->setQuery($query)->setFormats($format)->setSender($sender);

        $m = $this->smscSendCmd();

        if ($this->debug) {
            if ($m[1] > 0) {
                echo "Сообщение отправлено успешно. ID: $m[0], всего SMS: $m[1], стоимость: $m[2], баланс: $m[3].\n";
            } else {
                echo "Ошибка №", -$m[1], $m[0] ? ", ID: " . $m[0] : "", "\n";
            }
        }
        return $m;
    }

    public function getStatus($id, $phone, $all = 2)
    {
        $this->setCmd('status');
        $m = $this->smscSendCmd("login=" . urlencode($this->login) . "&psw=" . urlencode($this->password) . "&fmt=2&phone=" . urlencode($phone) . "&id=" . urlencode($id) . "&all=" . (int)$all);
        $this->setCmd('send');

        return $m;
    }

    private function smscSendCmd($arg = null)
    {
        $files = $this->getFiles();
        $url = $_url = ($arg == null) ? $this->toUrl() :
            ($this->https ? "https" : "http") . "://smsc.ru/sys/$this->cmd.php?" . $this->charset . "&" . $arg;

        $i = 0;
        do {
            if ($i++) {
                $url = str_replace('://smsc.ru/', '://www' . $i . '.smsc.ru/', $_url);
            }

            $ret = $this->smscReadUrl($url, $files, 3 + $i);
        } while ($ret == "" && $i < 5);


        return $ret;
    }

    private function smscReadUrl($url, $files, $tm = 5)
    {
        $ret = "";
        $post = $this->post || strlen($url) > 2000 || $files;

        if (function_exists("curl_init")) {
            static $c = 0;

            if (!$c) {
                $this->curl = curl_init();
                $c = &$this->curl;
                curl_setopt_array(
                    $c, array(
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CONNECTTIMEOUT => $tm,
                    CURLOPT_TIMEOUT => 60,
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_HTTPHEADER => array("Expect:")
                    )
                );
            }

            curl_setopt($c, CURLOPT_POST, $post);

            if ($post) {
                list($url, $post) = explode("?", $url, 2);

                if ($files) {
                    parse_str($post, $m);

                    foreach ($m as $k => $v) {
                        $m[$k] = isset($v[0]) && $v[0] == "@" ? sprintf("\0%s", $v) : $v;
                    }

                    $post = $m;
                    foreach ($files as $i => $path) {
                        if (file_exists($path)) {
                            $post["file" . $i] = function_exists("curl_file_create") ? curl_file_create($path) : "@" . $path;
                        }
                    }
                }

                curl_setopt($c, CURLOPT_POSTFIELDS, $post);
            }

            curl_setopt($c, CURLOPT_URL, $url);

            $ret = curl_exec($c);
            $this->setInfo(curl_getinfo($c));
        } elseif ($files) {
            if ($this->debug) {
                echo "Не установлен модуль curl для передачи файлов\n";
            }
        } else {
            if (!$this->https && function_exists("fsockopen")) {
                $m = parse_url($url);

                if (!$fp = fsockopen($m["host"], 80, $errno, $errstr, $tm)) {
                    $fp = fsockopen("212.24.33.196", 80, $errno, $errstr, $tm);
                }

                if ($fp) {
                    stream_set_timeout($fp, 60);

                    fwrite($fp, ($post ? "POST $m[path]" : "GET $m[path]?$m[query]") . " HTTP/1.1\r\nHost: smsc.ru\r\nUser-Agent: PHP" . ($post ? "\r\nContent-Type: application/x-www-form-urlencoded\r\nContent-Length: " . strlen($m['query']) : "") . "\r\nConnection: Close\r\n\r\n" . ($post ? $m['query'] : ""));

                    while (!feof($fp)) {
                        $ret .= fgets($fp, 1024);
                    }
                    list(, $ret) = explode("\r\n\r\n", $ret, 2);

                    fclose($fp);
                }
            } else {
                $ret = file_get_contents($url);
            }
        }

        return $ret;
    }

    public function error()
    {
        return curl_errno($this->curl);
    }

}
