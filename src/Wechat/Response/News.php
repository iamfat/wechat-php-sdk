<?php

namespace Wechat\Response;

/**
 * 用于回复的图文消息类型
 */
class News extends XML
{
    public function __construct($toUserName, $fromUserName, $items, $funcFlag)
    {
        parent::__construct($toUserName, $fromUserName, $funcFlag);

        $this->xml->addChild('MsgType', new XML\CData('news'));

        $this->xml->ArticleCount = count($items);
        $this->xml->Articles = null;
        $articles = $this->xml->Articles;

        foreach ($items as $item) {
            $articles->append($item->xml);
        }
    }
}
