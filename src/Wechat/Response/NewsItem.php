<?php

namespace Wechat\Response;

/**
 * 单条图文消息类型
 */
class NewsItem
{
    public $xml;

    public function __construct($title, $description, $picUrl, $url)
    {
        $this->xml = new XML\Element('<item/>');

        $this->xml->addChild('Title', new XML\CData($title));
        $this->xml->addChild('Description', new XML\CData($description));
        $this->xml->addChild('PicUrl', new XML\CData($picUrl));
        $this->xml->addChild('Url', new XML\CData($url));
    }
}
