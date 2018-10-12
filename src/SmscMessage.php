<?php

namespace Maestro\SmscApi;


/**
 * Class SmscMessage
 * @package Maestro\SmscApi
 */
class SmscMessage
{
    public $from = '';
    public $content = '';

    /**
     * @param string $content
     * @return static
     */
    public static function create($content = '')
    {
        return new static($content);
    }

    /**
     * SmscMessage constructor.
     * @param string $content
     */
    public function __construct($content = '')
    {
        $this->content($content);
    }

    /**
     * @param $content
     * @return $this
     */
    public function content($content)
    {
        $this->content = ($content != null ? '&mes=' . urlencode($content) : '');
        return $this;
    }

    /**
     * @param $from
     * @return $this
     */
    public function from($from)
    {
        $this->from = ($from != null ? '&phones=' . ($from) : '');
        return $this;
    }
}