<?php

namespace Maestro\SmscApi;

abstract class Smsc
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
    private $sender;
    private $formats;
    private $phone;
    private $message;
    private $fmt = '&fmt=2';
    private $files = [];



    public function connection($login, $password)
    {
        $this->login = $login;
        $this->password = $password;
    }

    public function toUrl()
    {
        return ($this->https ? "https" : "http") . "://smsc.ru/sys/".$this->cmd.".php?cost=3&login=" . urlencode($this->login) . "&psw=" . urlencode($this->password)
            . $this->message . $this->phone
            . $this->charset . urlencode($this->query) . $this->time . $this->fmt . $this->formats . $this->sender;
    }

    public function setCmd($cmd)
    {
        $this->cmd = $cmd;
        return $this;
    }

    public function setQuery($query)
    {
        $this->query = ($query ? "&$query" : "");
        return $this;
    }

    public function setTime($time)
    {
        $this->time = ($time ? "&time=" . urlencode($time) : "");
        return $this;
    }

    public function setSender($sender)
    {
        $this->sender = ($sender === false ? "" : "&sender=" . urlencode($sender));
        return $this;
    }

    public function setFormats($format)
    {
        $this->formats = ($format != null ? "&" . $format : '');
        return $this;
    }

    public function setFmt($fmt)
    {
        $this->fmt = ($fmt != null ? '&fmt=' . $fmt : '');
        return $this;
    }

    public function setPhone($phones)
    {
        $this->phone = ($phones != null ? '&phones=' . ($phones) : '');
        return $this;
    }

    public function setMessage($message)
    {
        $this->message = ($message != null ? '&mes=' . urlencode($message) : '');
        return $this;
    }

    public function setFiles($files)
    {
        $this->files = (!empty($files) ? $files : '');
        return $this;
    }

    public function getFiles()
    {
        return $this->files;
    }

    protected function setInfo($info)
    {
        $this->info = $info;
        return true;
    }

    public function getInfo()
    {
        return $this->info;
    }


}