<?php
/**
* 微信公众平台 PHP SDK
*
* @author     Ian Li <i@techotaku.net>, NetPuter <netputer@gmail.com>
* @license    MIT License
*/
  
require_once __DIR__ . '/SdkTestBase.php';

/**
* Event Test
*/
class WechatSdkEventTest extends WechatSdkTestBase {
    protected $mockBuilder;

    protected function setUp() {
        parent::setUp();

        $this->mockBuilder = $this->getMockBuilder('MyWechat')
            ->setMethods(array('onSubscribe', 'onUnsubscribe', 'onText', 'onImage', 'onLocation', 'onLink', 'onUnknown'))
                ->setConstructorArgs(array($this->token));
    }

    public function testGeneralFields() {

        $this->fillTextMsg('填充消息');
        $wechat = $this->mockBuilder->getMock();
        $response = $wechat->handleRequest(['get'=>$_GET, 'post'=>$GLOBALS['HTTP_RAW_POST_DATA']]);

        // 无需执行run()， 所有字段应已解析完毕
        $this->assertEquals($this->toUser, $wechat->publicGetRequest('tousername'));
        $this->assertEquals($this->fromUser, $wechat->publicGetRequest('fromusername'));
        $this->assertEquals($this->time, $wechat->publicGetRequest('createtime'));
        $this->assertEquals($this->msgid, $wechat->publicGetRequest('msgid'));

    }

    public function testEventOnSubscribe() {

        $this->fillEvent('subscribe');
        $wechat = $this->mockBuilder->getMock();
        $wechat->expects($this->once())
            ->method('onSubscribe');

        $response = $wechat->handleRequest(['get'=>$_GET, 'post'=>$GLOBALS['HTTP_RAW_POST_DATA']]);

        $this->assertEquals('', $wechat->publicGetRequest('eventkey'));
    }

    public function testEventOnUnsubscribe() {

        $this->fillEvent('unsubscribe');
        $wechat = $this->mockBuilder->getMock();
        $wechat->expects($this->once())
            ->method('onUnsubscribe');

        $response = $wechat->handleRequest(['get'=>$_GET, 'post'=>$GLOBALS['HTTP_RAW_POST_DATA']]);

        $this->assertEquals('', $wechat->publicGetRequest('eventkey'));

    }

    public function testEventOnUnknown() {

        $this->fillUnknown('unknown info');
        $wechat = $this->mockBuilder->getMock();
        $wechat->expects($this->once())
            ->method('onUnknown');

        $response = $wechat->handleRequest(['get'=>$_GET, 'post'=>$GLOBALS['HTTP_RAW_POST_DATA']]);

        $this->assertEquals('unknown info', $wechat->publicGetRequest('unknown'));
    }

    public function testEventOnText() {

        $this->fillTextMsg('填充文本消息');
        $wechat = $this->mockBuilder->getMock();
        $wechat->expects($this->once())
            ->method('onText');

        $response = $wechat->handleRequest(['get'=>$_GET, 'post'=>$GLOBALS['HTTP_RAW_POST_DATA']]);

        $this->assertEquals('填充文本消息', $wechat->publicGetRequest('content'));
    }

    public function testEventOnImage() {

        $this->fillImageMsg('https://travis-ci.org/netputer/wechat-php-sdk.png');
        $wechat = $this->mockBuilder->getMock();
        $wechat->expects($this->once())
            ->method('onImage');

        $response = $wechat->handleRequest(['get'=>$_GET, 'post'=>$GLOBALS['HTTP_RAW_POST_DATA']]);

        $this->assertEquals('https://travis-ci.org/netputer/wechat-php-sdk.png', $wechat->publicGetRequest('picurl'));
    }

    public function testEventOnLocation() {
        $this->fillLocationMsg('23.134521', '113.358803');
        $wechat = $this->mockBuilder->getMock();
        $wechat->expects($this->once())
            ->method('onLocation');

        $response = $wechat->handleRequest(['get'=>$_GET, 'post'=>$GLOBALS['HTTP_RAW_POST_DATA']]);

        $this->assertEquals('23.134521', $wechat->publicGetRequest('location_x'));
        $this->assertEquals('113.358803', $wechat->publicGetRequest('location_y'));
    }

    public function testEventOnLink() {
        $this->fillLinkMsg('netputer/wechat-php-sdk', '微信公众平台 PHP SDK', 'https://github.com/netputer/wechat-php-sdk');
        $wechat = $this->mockBuilder->getMock();
        $wechat->expects($this->once())
            ->method('onLink');

        $response = $wechat->handleRequest(['get'=>$_GET, 'post'=>$GLOBALS['HTTP_RAW_POST_DATA']]);

        $this->assertEquals('netputer/wechat-php-sdk', $wechat->publicGetRequest('title'));
        $this->assertEquals('微信公众平台 PHP SDK', $wechat->publicGetRequest('description'));
        $this->assertEquals('https://github.com/netputer/wechat-php-sdk', $wechat->publicGetRequest('url'));
    }

}
