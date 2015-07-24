<?php

namespace Wechat;

/**
 * 微信公众平台 PHP SDK (Modified)
 *
 * @author NetPuter <netputer@gmail.com>, Jia Huang <iamfat@gmail.com>
 */

/**
 * 微信公众平台服务器类
 */
class Server
{
    /**
     * 微信服务器的口令牌
     *
     * @var string
     */
    private $token;

    /**
     * 以数组的形式保存微信服务器每次发来的请求
     *
     * @var array
     */
    private $request;

    /**
     * 初始化，判断此次请求是否为验证请求，并以数组形式保存
     *
     * @param string $token 验证信息
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * 判断此次请求是否为验证请求
     *
     * @return boolean
     */
    private function isValid(array $get)
    {
        return isset($get['echostr']);
    }

    /**
     * 验证此次请求的签名信息
     *
     * @param  string  $token 验证信息
     * @return boolean
     */
    private function validateSignature(array $get)
    {
        if (! (isset($get['signature']) && isset($get['timestamp']) && isset($get['nonce']))) {
            return false;
        }
        $token = $this->token;
        $signature = $get['signature'];
        $timestamp = $get['timestamp'];
        $nonce = $get['nonce'];

        $signatureArray = array($token, $timestamp, $nonce);
        sort($signatureArray, SORT_STRING);

        return sha1(implode($signatureArray)) == $signature;
    }

    /**
     * 获取本次请求中的参数，不区分大小
     *
     * @param  string $param 参数名，默认为无参
     * @return mixed
     */
    protected function getRequest($param = false)
    {
        if ($param === false) {
            return $this->request;
        }

        $param = strtolower($param);

        if (isset($this->request[$param])) {
            return $this->request[$param];
        }

        return;
    }

    /**
     * 用户关注时触发，用于子类重写
     *
     * @return void
     */
    protected function onSubscribe()
    {
    }

    /**
     * 用户取消关注时触发，用于子类重写
     *
     * @return void
     */
    protected function onUnsubscribe()
    {
    }

    /**
     * 收到文本消息时触发，用于子类重写
     *
     * @return void
     */
    protected function onText()
    {
    }

    /**
     * 收到图片消息时触发，用于子类重写
     *
     * @return void
     */
    protected function onImage()
    {
    }

    /**
     * 收到地理位置消息时触发，用于子类重写
     *
     * @return void
     */
    protected function onLocation()
    {
    }

    /**
     * 收到链接消息时触发，用于子类重写
     *
     * @return void
     */
    protected function onLink()
    {
    }

    /**
     * 收到自定义菜单消息时触发，用于子类重写
     *
     * @return void
     */
    protected function onClick()
    {
    }

    /**
     * 收到自定义菜单跳转链接时触发，用于子类重写
     *
     * @return void
     */
    protected function onView()
    {
    }

    /**
     * 收到地理位置事件消息时触发，用于子类重写
     *
     * @return void
     */
    protected function onEventLocation()
    {
    }

    /**
     * 收到语音消息时触发，用于子类重写
     *
     * @return void
     */
    protected function onVoice()
    {
    }

    /**
     * 扫描二维码时触发，用于子类重写
     *
     * @return void
     */
    protected function onScan()
    {
    }

    protected function onScanCodePush()
    {
    }

    protected function onScanCodeWaitMsg()
    {
    }

    /**
     * 收到未知类型消息时触发，用于子类重写
     *
     * @return void
     */
    protected function onUnknown()
    {
    }

    /**
     * 回复文本消息
     *
     * @param  string  $content  消息内容
     * @param  integer $funcFlag 默认为0，设为1时星标刚才收到的消息
     * @return void
     */
    protected function respondText($content, $funcFlag = 0)
    {
        return new Response\Text($this->getRequest('fromusername'), $this->getRequest('tousername'), $content, $funcFlag);
    }

    /**
     * 回复音乐消息
     *
     * @param  string  $title       音乐标题
     * @param  string  $description 音乐描述
     * @param  string  $musicUrl    音乐链接
     * @param  string  $hqMusicUrl  高质量音乐链接，Wi-Fi 环境下优先使用
     * @param  integer $funcFlag    默认为0，设为1时星标刚才收到的消息
     * @return void
     */
    protected function respondMusic($title, $description, $musicUrl, $hqMusicUrl, $funcFlag = 0)
    {
        return new Response\Music($this->getRequest('fromusername'), $this->getRequest('tousername'), $title, $description, $musicUrl, $hqMusicUrl, $funcFlag);
    }

    /**
     * 回复图文消息
     * @param  array   $items    由单条图文消息类型 Response\NewsItem() 组成的数组
     * @param  integer $funcFlag 默认为0，设为1时星标刚才收到的消息
     * @return void
     */
    protected function respondNews($items, $funcFlag = 0)
    {
        return new Response\News($this->getRequest('fromusername'), $this->getRequest('tousername'), $items, $funcFlag);
    }

    /**
     * 分析消息类型，并分发给对应的函数
     *
     * @return void
     */
    public function handleRequest(array $form)
    {
        if (!$this->validateSignature($form['get'])) {
            return new Response\Raw('签名验证失败');
        }

        if ($this->isValid($form['get'])) {
            // 网址接入验证
            return new Response\Raw($form['get']['echostr']);
        }

        if (!is_string($form['post'])) {
            return new Response\Raw('缺少数据');
        }

        $xml = (array) simplexml_load_string($form['post'], '\\SimpleXMLElement', LIBXML_NOCDATA);
        $this->request = array_change_key_case($xml, CASE_LOWER);
        // 将数组键名转换为小写，提高健壮性，减少因大小写不同而出现的问题
        switch ($this->getRequest('msgtype')) {
        case 'event':
            switch ($this->getRequest('event')) {
            case 'subscribe':
                return $this->onSubscribe();

            case 'unsubscribe':
                return $this->onUnsubscribe();

            case 'scancode_waitmsg':
                return $this->onScanCodeWaitMsg();

            case 'scancode_push':
                return $this->onScanCodePush();

            case 'SCAN':
                return $this->onScan();

            case 'VIEW':
                return $this->onView();

            case 'LOCATION':
                return $this->onEventLocation();

            case 'CLICK':
                return $this->onClick();
            }

            return $this->onUnknown();

        case 'text':
            return $this->onText();

        case 'image':
            return $this->onImage();

        case 'location':
            return $this->onLocation();

        case 'link':
            return $this->onLink();

        case 'voice':
            return $this->onVoice();

        default:
            return $this->onUnknown();
        }
    }
}
