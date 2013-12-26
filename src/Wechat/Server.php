<?php

namespace Wechat {

    /**
    * 微信公众平台 PHP SDK (Modified)
    *
    * @author NetPuter <netputer@gmail.com>, Jia Huang <iamfat@gmail.com>
    */

    /**
    * 微信公众平台服务器类
    */
    class Server {

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
        public function __construct($token) {
            $this->token = $token;
        }

        /**
        * 判断此次请求是否为验证请求
        *
        * @return boolean
        */
        private function isValid() {
            return isset($_GET['echostr']);
        }

        /**
        * 验证此次请求的签名信息
        *
        * @param  string $token 验证信息
        * @return boolean
        */
        private function validateSignature(array $get) {
            if ( ! (isset($get['signature']) && isset($get['timestamp']) && isset($get['nonce']))) {
                return false;
            }
      
            $token = $this->token;
            $signature = $get['signature'];
            $timestamp = $get['timestamp'];
            $nonce = $get['nonce'];

            $signatureArray = array($token, $timestamp, $nonce);
            sort($signatureArray);

            return sha1(implode($signatureArray)) == $signature;
        }

        /**
        * 获取本次请求中的参数，不区分大小
        *
        * @param  string $param 参数名，默认为无参
        * @return mixed
        */
        protected function getRequest($param = FALSE) {
            if ($param === FALSE) {
                return $this->request;
            }

            $param = strtolower($param);

            if (isset($this->request[$param])) {
                return $this->request[$param];
            }

            return null;
        }

        /**
        * 用户关注时触发，用于子类重写
        *
        * @return void
        */
        protected function onSubscribe() {
        
        }

        /**
        * 用户取消关注时触发，用于子类重写
        *
        * @return void
        */
        protected function onUnsubscribe() {
            
        }

        /**
        * 收到文本消息时触发，用于子类重写
        *
        * @return void
        */
        protected function onText() {
                
        }

        /**
        * 收到图片消息时触发，用于子类重写
        *
        * @return void
        */
        protected function onImage() {
                    
        }

        /**
        * 收到地理位置消息时触发，用于子类重写
        *
        * @return void
        */
        protected function onLocation() {
                        
        }

        /**
        * 收到链接消息时触发，用于子类重写
        *
        * @return void
        */
        protected function onLink() {
                            
        }

        /**
        * 收到自定义菜单消息时触发，用于子类重写
        *
        * @return void
        */    
        protected function onClick() {
                                
        }

        /**
        * 收到地理位置事件消息时触发，用于子类重写
        *
        * @return void
        */    
        protected function onEventLocation() {
                                    
        }

        /**
        * 收到语音消息时触发，用于子类重写
        *
        * @return void
        */        
        protected function onVoice() {
                                        
        }

        /**
        * 扫描二维码时触发，用于子类重写
        *
        * @return void
        */        
        protected function onScan() {
                                            
        }

        /**
        * 收到未知类型消息时触发，用于子类重写
        *
        * @return void
        */
        protected function onUnknown() {
                                                
        }

        /**
        * 回复文本消息
        *
        * @param  string  $content  消息内容
        * @param  integer $funcFlag 默认为0，设为1时星标刚才收到的消息
        * @return void
        */
        protected function responseText($content, $funcFlag = 0) {
            return new TextResponse($this->getRequest('fromusername'), $this->getRequest('tousername'), $content, $funcFlag);
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
        protected function responseMusic($title, $description, $musicUrl, $hqMusicUrl, $funcFlag = 0) {
            return new MusicResponse($this->getRequest('fromusername'), $this->getRequest('tousername'), $title, $description, $musicUrl, $hqMusicUrl, $funcFlag);
        }

        /**
        * 回复图文消息
        * @param  array   $items    由单条图文消息类型 NewsResponseItem() 组成的数组
        * @param  integer $funcFlag 默认为0，设为1时星标刚才收到的消息
        * @return void
        */
        protected function responseNews($items, $funcFlag = 0) {
            return new NewsResponse($this->getRequest('fromusername'), $this->getRequest('tousername'), $items, $funcFlag);
        }

        /**
        * 分析消息类型，并分发给对应的函数
        *
        * @return void
        */
        public function handleRequest($form=null) {
            
            if (!isset($form)) $form = [];
            if (!isset($form['get'])) $form['get'] = $_GET;
             
            if (!$this->validateSignature($form['get'])) {
                return new RawResponse('签名验证失败');
            }
      
            if ($this->isValid($form['get'])) {
                // 网址接入验证
                return new RawResponse($form['get']['echostr']);
            }
      
            if (!isset($form['post'])) {
                if (!isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
                    return new RawResponse('缺少数据');
                }
                $form['post'] = $GLOBALS['HTTP_RAW_POST_DATA'];
            }

            if (!is_string($form['post'])) {
                return new RawResponse('缺少数据');
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

                    case 'SCAN':
                    return $this->onScan();

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

    interface Response {
        public function __toString();
    }

    class RawResponse implements Response {
       
        protected $content;
        
        public function __construct($content) {
            $this->content = $content;
        }
        
        public function __toString() {
            return $this->content;
        }

    }
    
    class SimpleXMLElement extends \SimpleXMLElement {

        public function append($append) {
            if (!is_object($append)) return;

            if ($append->children()->count() > 0) {
                $xml = $this->addChild($append->getName()); 
                foreach($append->children() as $child) { 
                    $xml->append($child); 
                } 
            } 
            else { 
                $xml = $this->addChild($append->getName(), (string) $append); 
            }
         
            foreach($append->attributes() as $n => $v) { 
                $xml->addAttribute($n, $v); 
            } 

        }
        
    }

    /**
    * 用于回复的基本消息类型
    */
    abstract class XMLResponse implements Response {

        protected $funcFlag;
        protected $xml;

        public function __construct($toUserName, $fromUserName, $funcFlag) {
            $root = new SimpleXMLElement('<root/>');
            $root->xml = '';
            $this->xml = $root->xml;
            $this->xml->ToUserName = $toUserName;
            $this->xml->FromUserName = $fromUserName;
            $this->xml->CreateTime = time();
            $this->xml->FuncFlag = $funcFlag;
        }

        public function __toString() {
            return $this->xml->asXML();
        }
    
    }

    /**
    * 用于回复的文本消息类型
    */
    class TextResponse extends XMLResponse {

        public function __construct($toUserName, $fromUserName, $content, $funcFlag = 0) {
            parent::__construct($toUserName, $fromUserName, $funcFlag);
            $this->xml->MsgType = 'text';
            $this->xml->Content = $content;
        }

    }

    /**
    * 用于回复的音乐消息类型
    */
    class MusicResponse extends XMLResponse {

        public function __construct($toUserName, $fromUserName, $title, $description, 
            $musicUrl, $hqMusicUrl, $funcFlag) {
            
            parent::__construct($toUserName, $fromUserName, $funcFlag);

            $this->xml->CreateTime = time();
            $this->xml->MsgType = 'music';
            $music = $this->xml->addChild('Music');
            $music->Title = $title;
            $music->Description = $description;
            $music->MusicUrl = $musicUrl;
            $music->HQMusicUrl = $hqMusicUrl;
            $this->xml->FuncFlag = $funcFlag;
        }

    }

    /**
    * 用于回复的图文消息类型
    */
    class NewsResponse extends XMLResponse {

        public function __construct($toUserName, $fromUserName, $items, $funcFlag) {
            parent::__construct($toUserName, $fromUserName, $funcFlag);

            $this->xml->MsgType = 'news';
            $this->xml->ArticleCount = count($items);
            $this->xml->Articles = '';
                    
            foreach ($items as $item) {
                $this->xml->Articles->append($item->xml);
            }
                    
        }

    }

    /**
    * 单条图文消息类型
    */
    class NewsResponseItem {

        public $xml;

        public function __construct($title, $description, $picUrl, $url) {
            $this->xml = new SimpleXMLElement('<item/>');
                        
            $this->xml->Title = $title;
            $this->xml->Description = $description;
            $this->xml->PicUrl = $picUrl;
            $this->xml->Url = $url;
        }

    }

}

