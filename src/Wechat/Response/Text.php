<?php

namespace Wechat\Response;

/**
 * 用于回复的文本消息类型
 */
class Text extends XML
{
    public function __construct($toUserName, $fromUserName, $content, $funcFlag = 0)
    {
        parent::__construct($toUserName, $fromUserName, $funcFlag);
        $this->xml->addChild('MsgType', new XML\CData('text'));
        $this->xml->addChild('Content', new XML\CData($content));
    }
}
