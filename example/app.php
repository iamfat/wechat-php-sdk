<?php

$app_id = 'weixin';
$app_secret = 'weixin.tiaoshi';
$app = new \Wechat\App($app_id, $app_secret);

$token = $app->token();
$userinfo = $app->getUser("a user openid");
