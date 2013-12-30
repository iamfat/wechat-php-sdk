<?php

require_once __DIR__ . '/SdkTestBase.php';

class SimpleXMLTest extends WechatSdkTestBase {
    
    public function testCData() {
        
        $root = new \Wechat\SimpleXMLElement('<root/>');
        $root->addChild('cdata', new \Wechat\SimpleXMLCData('This is CDATA'));

        $this->assertEquals('<cdata><![CDATA[This is CDATA]]></cdata>', $root->cdata->asXML());

    }

    public function testAppend() {
        
        $root = new \Wechat\SimpleXMLElement('<root/>');
        
        $children = new \Wechat\SimpleXMLElement('<a><b>bb</b><c>cc</c></a>');
        $root->append($children);

        $this->assertEquals('<a><b>bb</b><c>cc</c></a>', $root->a->asXML());
        $this->assertEquals('<b>bb</b>', $root->a->b->asXML());
        $this->assertEquals('<c>cc</c>', $root->a->c->asXML());

    }

}
