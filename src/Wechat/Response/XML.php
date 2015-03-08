<?php

namespace Wechat\Response;

abstract class XML implements \Wechat\Response
{
    protected $funcFlag;
    protected $xml;

    public function __construct($toUserName, $fromUserName, $funcFlag)
    {
        $root = new XML\Element('<root/>');
        $root->xml = null;
        $this->xml = $root->xml;
        $this->xml->addChild('ToUserName', new XML\CData($toUserName));
        $this->xml->addChild('FromUserName', new XML\CData($fromUserName));
        $this->xml->CreateTime = time();
        $this->xml->FuncFlag = $funcFlag;
    }

    public function __toString()
    {
        return $this->xml->asXML();
    }
}
