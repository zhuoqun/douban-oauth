<?php
/**
 * 获取 Request Token 并请求授权
 */

// 包含相应文件
require('OAuth.php');
require('config.inc');

// 创建一个 OAuthConsumer 对象。
$consumer = new OAuthConsumer($api_key, $api_key_secret);

/* 
 * 利用静态方法创建一个 OAuthRequest 对象。这里需要四个参数：
 *
 * $consumer          : 利用 API Key 和 API Key secret 创建的 OAuthConsumer 对象
 * NULL               : token 对象（这一步不需要，所以传入 NULL）
 * "GET"              : HTTP 方法，（GET 或者 POST）
 * $request_token_url : Request Token 的获取地址
 */
$req_req = OAuthRequest::from_consumer_and_token($consumer, NULL, "GET", $request_token_url);

/*
 * 构造签名和验证所需要的参数，包括 oauth_signature, oauth_timestamp 等。参数说明：
 * $sig_method : 签名方式，这里是 OAuthSignatureMethod_HMAC_SHA1 对象。
 * $consumer   : 利用 API Key 和 API Key secret 创建的 OAuthConsumer 对象
 * NULL        : token 对象（这一步不需要，所以传入 NULL）
 */
$req_req->sign_request($sig_method, $consumer, NULL);

/*
构造后的 $req_req 对象结构如下：

object(OAuthRequest)#3 (4) {
  ["parameters":protected]=>
  array(6) {
    ["oauth_version"]=>
    string(3) "1.0"
    ["oauth_nonce"]=>
    string(32) ""
    ["oauth_timestamp"]=>
    int()
    ["oauth_consumer_key"]=>
    string(32) ""
    ["oauth_signature_method"]=>
    string(9) "HMAC-SHA1"
    ["oauth_signature"]=>
    string(28) ""
  }
  ["http_method":protected]=>
  string(3) "GET"
  ["http_url":protected]=>
  string(48) "http://www.douban.com/service/auth/request_token"
  ["base_string"]=>
  string(257) ""
}

它可以通过 to_url() 方法输出相应的合法URL，例如：

http://www.douban.com/service/auth/request_token?oauth_version=1.0&oauth_nonce=xxxx

也可以通过 to_header() 方法输出适用于 http header 的数据，在使用 POST 方法发送数据时会用到。
*/ 

/*
 * 使用 curl 模拟 HTTP 请求。你也可以打印出 URL 信息：
 *
 * var_dump($req_req->to_url());
 *
 * 然后把 URL 复制到浏览器地址栏中打开，也可以看到页面上出现下面的 result 结果。
 */
$ch = curl_init();
curl_setopt($ch,CURLOPT_URL, $req_req->to_url());
curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
$result = curl_exec($ch);
curl_close($ch);
parse_str($result, $arr);
$param_str = 'oauth_token=' . $arr['oauth_token'];
/*
 * 这里的 result 结果是这样的字符串：
 *
 * oauth_token_secret=aaaaaaaa&oauth_token=bbbbbbbbb
 *
 * 然后利用它来构造一个授权 URL，这里注意， 请先保存 oauth_token_secret，不要明文传递 token_secret. 如下：
 *
 */
session_start();
$_SESSION['request_token_secret'] = $arr['oauth_token_secret'];

$callback = urlencode("http://yourappdomain.com/access_and_post.php");
$authorize_request_url = $authorize_url . "?" . $param_str . "&oauth_callback=" . $callback;

/*
 * $authorize_request_url 的形式如下：
 * http://www.douban.com/service/auth/authorize?oauth_token=xxxxx&oauth_callback=http%3A%2F%2Fyourappdomain.com%2Faccess_and_post.php
 *
 * 访问这个 URL 会出现豆瓣请求授权的页面（第三方应用需要访问你在豆瓣上的个人数据，允许？不允许）。在这个页面中用户如果点击“允许”，豆瓣就会同意授权。然后页面会跳转到 oauth_callback 指向的页面
 *
 * 接下来就是获取 access token。
 */

header('location:'.$authorize_request_url);

/*
 * 接下来的部分请看另外一个文件：access_and_post.php
 */

?>
