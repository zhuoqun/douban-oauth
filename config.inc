<?php
/* 全局配置文件 */

// request token 获取地址
$request_token_url = 'http://www.douban.com/service/auth/request_token';
// 用户授权页面
$authorize_url = 'http://www.douban.com/service/auth/authorize';
// access token 获取地址
$access_token_url = 'http://www.douban.com/service/auth/access_token';

// 你的 API Key
$api_key = 'your douban api key';
// 你的私钥
$api_key_secret = 'your douban api secret key';

// OAuth 验证方法，这里选择 HMAC_SHA1
$hmac_method = new OAuthSignatureMethod_HMAC_SHA1();
$sig_method = $hmac_method;
?>
