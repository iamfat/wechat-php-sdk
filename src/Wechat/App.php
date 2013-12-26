<?php

namespace Wechat {
    
    class App {
        
        private $_appId;
        private $_appSecret;
        
        function __construct($appId, $appSecret) {
            $this->_appId = $appId;
            $this->_appSecret = $appSecret;
        }
        
        private function _token() {
            if (!isset($_SESSION['wechat.access_token']['token']) || $_SESSION['wechat.access_token']['etime'] < time()) {

                $http = new HTTP;
                $response = $http->get('https://api.weixin.qq.com/cgi-bin/token', [
                    'grant_type' => 'client_credential',
                    'appid' => $this->_appId,
                    'secret' => $this->_appSecret,
                ]);

                $data = @json_decode($response->body, true);
                if (!$data || !isset($data['access_token'])) {
                    return null;
                }
            
                $_SESSION['wechat.access_token'] = [
                    'token' => $data['access_token'],
                    'etime' => time() + $data['expires_in']
                ];
            }
            
            return $_SESSION['wechat.access_token']['token'];
        }
        
        function token() {
            return $this->_token();
        }
        
        function getUser($openid) {

            $token = $this->_token();
            
            $http = new \Model\HTTP;
            $response = $http
                ->get("https://api.weixin.qq.com/cgi-bin/user/info", [
                    'access_token'=>$token,
                    'openid'=>$openid,
                ]);
            
            return @json_decode($response->body, true);
        }
        
        
    }
    
}