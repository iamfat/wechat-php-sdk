<?php

namespace Wechat;

class App
{
    private $_appId;
    private $_appSecret;
    private $_http;

    public function __construct($appId, $appSecret)
    {
        $this->_appId = $appId;
        $this->_appSecret = $appSecret;
        $this->_http = new HTTP();
    }

    public function getAppId()
    {
        return $this->_appId;
    }

    public function getAppSecret()
    {
        return $this->_appSecret;
    }

    protected function getStoragePath($id)
    {
        return sys_get_temp_dir()."/wechat_{$this->_appId}_{$id}";
    }

    public function getAccessToken()
    {
        $file = $this->getStoragePath("token.json");
        $data = @json_decode(file_get_contents($file), true);
        if (!isset($data['token']) || $data['etime'] < time()) {
            $response = $this->_http->get('https://api.weixin.qq.com/cgi-bin/token', [
               'grant_type' => 'client_credential',
               'appid' => $this->_appId,
               'secret' => $this->_appSecret
            ]);

            $rdata = @json_decode($response->body, true);
            if (isset($rdata['access_token'])) {
                $data['token'] = $rdata['access_token'];
                $data['etime'] =  time() + $rdata['expires_in'];
                // update to permanent storage
                file_put_contents($file, J($data));
            }
        }

        return $data['token'];
    }

    public function getUserInfo($openid)
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return false;
        }

        $response = $this->_http
            ->get("https://api.weixin.qq.com/cgi-bin/user/info", [
                'access_token' => $token,
                'openid' => $openid,
            ]);

        return @json_decode($response->body, true);
    }

    public function getTicket($type = 'jsapi')
    {
        // jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
        $file = $this->getStoragePath("ticket_{$type}.json");
        $data = @json_decode(file_get_contents($file), true);
        if (!isset($data['ticket']) || $data['etime'] < time()) {
            $token = $this->getAccessToken();
            if (!$token) {
                return false;
            }

            $http = new HTTP();
            $response = $http->get('https://api.weixin.qq.com/cgi-bin/ticket/getticket', [
                'type' => $type,
                'access_token' => $token,
            ]);

            $rdata = @json_decode($response->body, true);
            if (isset($rdata['ticket'])) {
                $data['ticket'] = $rdata['ticket'];
                $data['etime'] = time() + $data['expires_in'];
                file_put_contents($file, J($data));
            }
        }

        return $data['ticket'];
    }

    public function getQRCode($sceneId, $permanent = false)
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return false;
        }

        $url = URL('https://api.weixin.qq.com/cgi-bin/qrcode/create', ['access_token' => $token]);
        $response = $this->_http->post($url, J([
            'action_name' => $permanent ? 'QR_LIMIT_SCENE' : 'QR_SCENE',
            'action_info' => [
                'scene' => is_numeric($sceneId)
                    ? ['scene_id' => $sceneId] : ['scene_str' => $sceneId],
            ],
        ]));

        return @json_decode($response->body, true) ?: false;
    }

    public function getLoginQRCodeUrl($redirectUri=null) {
        if ($redirectUri === null) {
            $redirectUri = URL('', $_GET);
        }

        return URL('https://open.weixin.qq.com/connect/qrconnect#wechat_redirect', [
            'appid' => $this->_appId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'snsapi_login',
        ]);
    }

    public function getOAuth()
    {
        return new OAuth($this->_appId, $this->_appSecret);
    }
}
