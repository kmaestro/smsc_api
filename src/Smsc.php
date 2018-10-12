<?php

namespace Maestro\SmscApi;

class Smsc
{
    protected $login;
    protected $password;
    protected $post = 0;
    protected $https = 0;
    protected $charset = '&charset=windows-1251';
    protected $debug = 0;
    protected $fromEmail = '';
    protected $url;
    protected $info;
    protected $cmd = 'send';
    private $query;
    private $time;
    private $valid;
    private $sender;
    private $formats;
    private $phone;
    private $message;
    private $fmt = '&fmt=2';
    private $files = [];


    /**
     * @param $login
     * @param $password
     */
    public function connection($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function toUrl()
    {
        return ($this->https ? "https" : "http") . "://smsc.ru/sys/" . $this->cmd . ".php?cost=3&login=" . urlencode($this->login) . "&psw=" . urlencode($this->password)
            . $this->message . $this->phone
            . $this->charset . urlencode($this->query) . $this->time . $this->fmt . $this->formats . $this->sender.$this->valid;
    }

    /**
     * @param $cmd
     * @return $this
     */
    public function setCmd($cmd)
    {
        $this->cmd = $cmd;
        return $this;
    }

    /**
     * @param $query
     * @return $this
     */
    public function setQuery($query)
    {
        $this->query = ($query ? "&$query" : "");
        return $this;
    }

    /**
     * @param $time
     * @return $this
     */
    public function setTime($time)
    {
        $this->time = ($time ? "&time=" . urlencode($time) : "");
        return $this;
    }

    /**
     * @param $sender
     * @return $this
     */
    public function setSender($sender)
    {
        $this->sender = ($sender === false ? "" : "&sender=" . urlencode($sender));
        return $this;
    }

    /**
     * @param $format
     * @return $this
     */
    public function setFormats($format)
    {
        $this->formats = ($format != null ? "&" . $format : '');
        return $this;
    }

    /**
     * @param $fmt
     * @return $this
     */
    public function setFmt($fmt)
    {
        $this->fmt = ($fmt != null ? '&fmt=' . $fmt : '');
        return $this;
    }

    /**
     * @param $phones
     * @return $this
     */
    public function setPhone($phones)
    {
        $this->phone = ($phones != null ? '&phones=' . ($phones) : '');
        return $this;
    }

    /**
     * @param $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = ($message != null ? '&mes=' . urlencode($message) : '');
        return $this;
    }

    /**
     * @param $files
     * @return $this
     */
    public function setFiles($files)
    {
        $this->files = (!empty($files) ? $files : '');
        return $this;
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param $info
     * @return bool
     */
    protected function setInfo($info)
    {
        $this->info = $info;
        return true;
    }

    /**
     * @return mixed
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @param $valid
     * @return $this
     */
    public function setValid($valid)
    {
        $valid = (int)preg_replace('~[^0-9]~', '', $valid);
        if ($valid < 60) {
            $date = '00:01';
        } elseif ($valid > (60 * 60 * 24)) {
            $date = '24:00';
        } else {
            $date = gmdate("H:i", $valid);
        }
        $this->valid = ($valid != 0)?'&valid=' . $date:'';

        return $this;
    }

    /**
     * @return string
     */
    public function getValid()
    {
        return $this->valid;
    }


}