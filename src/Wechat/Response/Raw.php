<?php

namespace Wechat\Response;

class Raw implements \Wechat\Response
{
    protected $content;

    public function __construct($content)
    {
        $this->content = $content;
    }

    public function __toString()
    {
        return $this->content;
    }
}
