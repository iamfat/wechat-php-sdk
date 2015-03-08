<?php

namespace Wechat\Response;

/**
 * 用于回复的音乐消息类型
 */
class Music extends XML
{
    public function __construct($toUserName, $fromUserName, $title, $description,
        $musicUrl, $hqMusicUrl, $funcFlag)
    {
        parent::__construct($toUserName, $fromUserName, $funcFlag);

        $this->xml->addChild('MsgType', new XML\CData('music'));

        $this->xml->Music = null;
        $music = $this->xml->Music;

        $music->addChild('Title', new XML\CData($title));
        $music->addChild('Description', new XML\CData($description));
        $music->addChild('MusicUrl', new XML\CData($musicUrl));
        $music->addChild('HQMusicUrl', new XML\CData($hqMusicUrl));
    }
}
