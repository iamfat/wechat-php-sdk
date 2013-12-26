<?php
/**
* 微信公众平台 PHP SDK
*
* @author     Ian Li <i@techotaku.net>, NetPuter <netputer@gmail.com>, Jia Huang <iamfat@gmail.com>
* @license    MIT License
*/
  
require_once __DIR__ . '/SdkTestBase.php';

/**
* General Test
*/
class WechatSdkGeneralTest extends WechatSdkTestBase {

    protected function setUp() {
        parent::setUp();
    }

    public function testApiValidation() {
        $echostr = '9eabb7918cbad53305f7eae647cf1402e2fc7836';
        $_GET['echostr'] = $echostr;

        $wechat = new MyWechat($this->token);
        $response = $wechat->handleRequest(['get'=>$_GET]);
        $this->assertEquals($echostr, (string) $response, 'Wechat API validation output should match the input.');
    }

    public function testBlankSignature() {
        $_GET['signature'] = '';
        $wechat = new MyWechat($this->token);
        $response = $wechat->handleRequest(['get'=>$_GET]);
        $this->assertEquals('签名验证失败', (string) $response, 'Signature verification should fail.');
    }

    public function testEmptyPOST() {
        $wechat = new MyWechat($this->token);
        $response = $wechat->handleRequest(['get'=>$_GET]);
        $this->assertEquals('缺少数据', (string) $response, 'SDK should output "no data" (in chinese, utf-8).');
    }

}
