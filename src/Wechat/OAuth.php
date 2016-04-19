<?php

namespace Wechat;

class OAuth
{
    private $_token;
    private $_http;
    private $_appId;
    private $_appSecret;

    public function __construct($appId, $appSecret)
    {
        $this->_appId = $appId;
        $this->_appSecret = $appSecret;
        $this->_http = new HTTP();
    }

    public function getAccessToken()
    {
        if (!isset($_SESSION['wechat.access_token'][$this->_appId])) {
            if (isset($_GET['code'])) {
                // got authorization code, try to acquire access token
                $url = URL('https://api.weixin.qq.com/sns/oauth2/access_token', [
                    'appid' => $this->_appId,
                    'secret' => $this->_appSecret,
                    'grant_type' => 'authorization_code',
                    'code' => $_GET['code']
                ]);
                $response = $this->_http->get($url, '');
                $rdata = @json_decode($response->body, true);
                if (isset($rdata['access_token'])) {
                    $_SESSION['wechat.access_token'][$this->_appId] = $rdata;
                }
            } else {
                // start oauth process...
                $state = md5(uniqid(rand(), true));
                $url = URL('https://open.weixin.qq.com/connect/oauth2/authorize', [
                    'appid' => $this->_appId,
                    'redirect_uri' =>  URL('', $_GET),
                    'response_type' => 'code',
                    'scope' => 'snsapi_userinfo,snsapi_login',
                    'state' => $state,
                ], 'wechat_redirect');
                header('Location: '.$url);
                exit;
            }
        }

        return $_SESSION['wechat.access_token'][$this->_appId] ?: false;
    }

    public function getOpenId()
    {
        $token = $this->getAccessToken();
        return isset($token['openid']) ? $token['openid'] : false;
    }

    public function getUserInfo($openid)
    {
        $token = $this->getAccessToken();
        $response = $this->_http
            ->get("https://api.weixin.qq.com/sns/userinfo", [
                'access_token' => $token['access_token'],
                'openid' => $openid,
            ]);

        return @json_decode($response->body, true);
    }

}
