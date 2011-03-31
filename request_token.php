<?php
require('OAuth.php');

// request token
$url = "http://www.douban.com/service/auth/request_token";
$hmac_method = new OAuthSignatureMethod_HMAC_SHA1();
$sig_method = $hmac_method;
$key = '0fddce7bb96c9ce52f996b732c45d6af';
$secret = '39d462490180bf67';

$test_consumer = new OAuthConsumer($key, $secret, NULL);

$req_req = OAuthRequest::from_consumer_and_token($test_consumer, NULL, "GET", $url, array());
$req_req->sign_request($sig_method, $test_consumer, NULL);
var_dump($req_req->to_url());

$ch = curl_init();
curl_setopt($ch,CURLOPT_HEADER,1);
curl_setopt($ch,CURLOPT_URL, $req_req->to_url());
curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch);
curl_close($ch);
parse_str($result, $params);
var_dump($result);

$access_url = 'http://www.douban.com/service/auth/access_token';

$token = new OAuthConsumer($params['oauth_token'], $params['oauth_token_secret']);
$acc_req = OAuthRequest::from_consumer_and_token($test_consumer, $token, "GET", $access_url, $params);
$acc_req->sign_request($sig_method, $test_consumer, $token);

var_dump($acc_req->to_url());
?>
