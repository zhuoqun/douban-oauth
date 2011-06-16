<?php
/**
 * 获取 Access Token 并发送数据
 *
 * 这是 OAuth 验证的下半部分，上半部分请看 request_token.php
 */

// 包含相应文件
require('OAuth.php');
require('config.inc');

// 获取之前的 oauth_token 和 oauth_token_secret 。在上一步授权之后会带着这两个参数跳转到本页，见 request_token.php
$oauth_token = $_REQUEST['oauth_token'];

session_start();
$oauth_token_secret = $_SESSION['request_token_secret'];

// 创建一个 OAuthConsumer 对象。
$consumer = new OAuthConsumer($api_key, $api_key_secret);

// 创建一个 token 对象，参数是上一步获取到的 oauth_token 和 oauth_token_secret
$request_token = new OAuthConsumer($oauth_token, $oauth_token_secret);

/* 
 * 利用静态方法创建一个 OAuthRequest 对象。这里需要四个参数：
 *
 * $consumer          : 利用 API Key 和 API Key secret 创建的 OAuthConsumer 对象
 * $request_token     : token 对象
 * "GET"              : HTTP 方法，（GET 或者 POST）
 * $access_token_url  : Access Token 的获取地址
 */
$acc_req = OAuthRequest::from_consumer_and_token($consumer, $request_token, "GET", $access_token_url);
$acc_req->sign_request($sig_method, $consumer, $request_token);


/*
 * 使用 curl 模拟 HTTP 请求。你也可以打印出 URL 信息：
 *
 * var_dump($acc_req->to_url());
 *
 * 然后把 URL 复制到浏览器地址栏中打开，也可以看到页面上出现下面的 result 结果。
 */
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $acc_req->to_url());
curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch);
curl_close($ch);

/*
 * 这里的 result 结果是这样的字符串：
 *
 * oauth_token_secret=xxxx&oauth_token=xxxx&douban_user_id=123456
 *
 * 至此，我们已经成功获得了认证，拿到了 Access Token， Access Token Secret 以及 用户的豆瓣 ID。
 * 下面利用获取的 Access Token 发一条“我说”。
 *
 */
parse_str($result, $params);

$token = $params['oauth_token'];
$token_secret = $params['oauth_token_secret'];

$url = 'http://api.douban.com/miniblog/saying';
$acc_token = new OAuthConsumer($token, $token_secret);
$acc_req = OAuthRequest::from_consumer_and_token($consumer, $acc_token, "POST", $url);
$acc_req->sign_request($sig_method, $consumer, $acc_token);

/**
 * 构造 http header. 因为豆瓣 API 现在不接受在参数中传递 OAuth 信息，详见：
 *
 * http://www.douban.com/service/apidoc/auth#%E5%B8%B8%E8%A7%81%E9%97%AE%E9%A2%98
 *
 */
$header = array('Content-Type: application/atom+xml', $acc_req->to_header('http://www.yourappdomain.com'));
$requestBody = "<?xml version='1.0' encoding='UTF-8'?>
<entry xmlns:ns0=\"http://www.w3.org/2005/Atom\" xmlns:db=\"http://www.douban.com/xmlns/\">
<content>哈哈哈哈test</content>
</entry>";

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
// 应该会发送成功，并且以XML格式返回你发送的这条我说。
// Enjoy!
?>
