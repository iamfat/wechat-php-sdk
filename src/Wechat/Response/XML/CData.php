<?php

namespace Wechat\Response\XML;

class CData
{
    private $_text;

    public function __construct($text)
    {
        $this->_text = $text;
    }

    public function __toString()
    {
        return $this->_text;
    }
}
