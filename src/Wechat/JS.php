<?php

namespace Wechat;

class JS
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function getSignPackage($url)
    {
        $ticket = $this->app->getTicket('jsapi');
        $timestamp = time();
        $nonceStr = $this->_createNonceStr();

        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $string = "jsapi_ticket={$ticket}&noncestr={$nonceStr}&timestamp={$timestamp}&url={$url}";

        $signature = sha1($string);

        $signPackage = [
            "appId"     => $this->app->getAppId(),
            "nonceStr"  => $nonceStr,
            "timestamp" => $timestamp,
            "url"       => $url,
            "signature" => $signature,
            "rawString" => $string,
        ];

        return $signPackage;
    }

    private function _createNonceStr($length = 16)
    {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }

        return $str;
    }
}
