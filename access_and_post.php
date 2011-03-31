<?php
/**
 * 获取 Access Token 并发送数据
 *
 * 这是 OAuth 验证的下半部分，上半部分请看 request_token.php
 */

// 包含相应文件
require('OAuth.php');
require('config.inc');

// 获取之前的 oauth_token 。在上一步授权之后会带着 oauth_token 参数跳转到本页，见 request_token.php
$oauth_token = $_REQUEST['oauth_token'];

$request_token = new OAuthConsumer($params['oauth_token'], $params['oauth_token_secret']);
$acc_req = OAuthRequest::from_consumer_and_token($test_consumer, $token, "GET", $access_url, $params);
$acc_req->sign_request($sig_method, $test_consumer, $token);

var_dump($acc_req->to_url());

$key = '0fddce7bb96c9ce52f996b732c45d6af';
$secret = '39d462490180bf67';
$token = "5e407df212fd5c4e1ac179d638f814ea";
$token_secret = "6664b89e1b07aac7";
$user_id = '1534763';

$url = 'http://api.douban.com/miniblog/saying';
$test_consumer = new OAuthConsumer($key, $secret, NULL);
$token = new OAuthConsumer($token, $token_secret);
$acc_req = OAuthRequest::from_consumer_and_token($test_consumer, $token, "POST", $url, array());
$acc_req->sign_request($sig_method, $test_consumer, $token);


$header = array('Content-Type: application/atom+xml', $acc_req->to_header('http://www.zhuoqun.net'));
$requestBody = "<?xml version='1.0' encoding='UTF-8'?>
<entry xmlns:ns0=\"http://www.w3.org/2005/Atom\" xmlns:db=\"http://www.douban.com/xmlns/\">
<content>哈哈哈哈testtest</content>
</entry>";
var_dump($header);

$ch = curl_init();
curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
curl_setopt($ch,CURLOPT_HEADER,1);
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch,CURLOPT_POST,1); 
curl_setopt($ch,CURLOPT_POSTFIELDS, $requestBody);
$result = curl_exec($ch);
curl_close($ch);
var_dump($result);
?>
