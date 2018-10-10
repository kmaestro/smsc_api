<?php

namespace Maestro\SmscApi;


class SmscMessage
{
    public $from = '';
    public $content = '';

    public static function create($content = '')
    {
        return new static($content);
    }

    public function __construct($content = '')
    {
        $this->content($content);
    }

    public function content($content)
    {
        $this->content = ($content != null ? '&mes=' . urlencode($content) : '');
        return $this;
    }

    public function from($from)
    {
        $this->from = ($from != null ? '&phones=' . ($from) : '');
        return $this;
    }
}