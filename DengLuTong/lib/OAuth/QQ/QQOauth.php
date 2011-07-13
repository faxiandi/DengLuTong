<?php
namespace DengLuTong\lib\OAuth\QQ;
class QQOauth{/*just for autoload*/}
/**
 * @brief 对参数进行字典升序排序
 *
 * @param $params 参数列表
 *
 * @return 排序后用&链接的key-value对（key1=value1&key2=value2...)
 */
namespace DengLuTong\lib\OAuth\QQ\QQOauth;
function get_normalized_string($params)
{
    ksort($params);
    $normalized = array();
    foreach($params as $key => $val)
    {
        $normalized[] = $key."=".$val;
    }

    return implode("&", $normalized);
}

/**
 * @brief 使用HMAC-SHA1算法生成oauth_signature签名值 
 *
 * @param $key  密钥
 * @param $str  源串
 *
 * @return 签名值
 */

function get_signature($str, $key)
{
    $signature = "";
    if (function_exists('hash_hmac'))
    {
        $signature = base64_encode(hash_hmac("sha1", $str, $key, true));
    }
    else
    {
        $blocksize	= 64;
        $hashfunc	= 'sha1';
        if (strlen($key) > $blocksize)
        {
            $key = pack('H*', $hashfunc($key));
        }
        $key	= str_pad($key,$blocksize,chr(0x00));
        $ipad	= str_repeat(chr(0x36),$blocksize);
        $opad	= str_repeat(chr(0x5c),$blocksize);
        $hmac 	= pack(
            'H*',$hashfunc(
                ($key^$opad).pack(
                    'H*',$hashfunc(
                        ($key^$ipad).$str
                    )
                )
            )
        );
        $signature = base64_encode($hmac);
    }

    return $signature;
} 

/**
 * @brief 对字符串进行URL编码，遵循rfc1738 urlencode
 *
 * @param $params
 *
 * @return URL编码后的字符串
 */
function get_urlencode_string($params)
{
    ksort($params);
    $normalized = array();
    foreach($params as $key => $val)
    {
        $normalized[] = $key."=".rawurlencode($val);
    }

    return implode("&", $normalized);
}

/**
 * @brief 检查openid是否合法
 *
 * @param $openid  与用户QQ号码一一对应
 * @param $timestamp　时间戳
 * @param $sig　　签名值
 *
 * @return true or false
 */
function is_valid_openid($openid, $timestamp, $sig)
{
    $key = $_SESSION["appkey"];
    $str = $openid.$timestamp;
    $signature = get_signature($str, $key);

    //echo "sig:$sig\n";
    //echo "str:$str\n";

    return $sig == $signature; 
}

/**
 * @brief 所有Get请求都可以使用这个方法
 *
 * @param $url
 * @param $appid
 * @param $appkey
 * @param $access_token
 * @param $access_token_secret
 * @param $openid
 *
 * @return true or false
 */
function do_get($url, $appid, $appkey, $access_token, $access_token_secret, $openid)
{
    $sigstr = "GET"."&".rawurlencode("$url")."&";

    //必要参数, 不要随便更改!!
    $params = $_GET;
    $params["oauth_version"]          = "1.0";
    $params["oauth_signature_method"] = "HMAC-SHA1";
    $params["oauth_timestamp"]        = time();
    $params["oauth_nonce"]            = mt_rand();
    $params["oauth_consumer_key"]     = $appid;
    $params["oauth_token"]            = $access_token;
    $params["openid"]                 = $openid;
    unset($params["oauth_signature"]);

    //参数按照字母升序做序列化
    $normalized_str = get_normalized_string($params);
    $sigstr        .= rawurlencode($normalized_str);

    //签名,确保php版本支持hash_hmac函数
    $key = $appkey."&".$access_token_secret;
    $signature = get_signature($sigstr, $key);
    $url      .= "?".$normalized_str."&"."oauth_signature=".rawurlencode($signature);

    //echo "$url\n";
    return file_get_contents($url);
}

/**
 * @brief 所有multi-part post 请求都可以使用这个方法
 *
 * @param $url
 * @param $appid
 * @param $appkey
 * @param $access_token
 * @param $access_token_secret
 * @param $openid
 *
 */
function do_multi_post($url, $appid, $appkey, $access_token, $access_token_secret, $openid)
{
    //构造签名串.源串:方法[GET|POST]&uri&参数按照字母升序排列
    $sigstr = "POST"."&"."$url"."&";

    //必要参数,不要随便更改!!
    $params = $_POST;
    $params["oauth_version"]          = "1.0";
    $params["oauth_signature_method"] = "HMAC-SHA1";
    $params["oauth_timestamp"]        = time();
    $params["oauth_nonce"]            = mt_rand();
    $params["oauth_consumer_key"]     = $appid;
    $params["oauth_token"]            = $access_token;
    $params["openid"]                 = $openid;
    unset($params["oauth_signature"]);


    //获取上传图片信息
    foreach ($_FILES as $filename => $filevalue)
    {
        if ($filevalue["error"] != UPLOAD_ERR_OK)
        {
            //echo "upload file error $filevalue['error']\n";
            //exit;
        } 
        $params[$filename] = file_get_contents($filevalue["tmp_name"]);
    }

    //对参数按照字母升序做序列化
    $sigstr .= get_normalized_string($params);

    //签名,需要确保php版本支持hash_hmac函数
    $key = $appkey."&".$access_token_secret;
    $signature = get_signature($sigstr, $key);
    $params["oauth_signature"] = $signature; 

    //处理上传图片
    foreach ($_FILES as $filename => $filevalue)
    {
        $tmpfile = dirname($filevalue["tmp_name"])."/".$filevalue["name"];
        move_uploaded_file($filevalue["tmp_name"], $tmpfile);
        $params[$filename] = "@$tmpfile";
    }

    /*
    echo "len: ".strlen($sigstr)."\n";
    echo "sig: $sigstr\n";
    echo "key: $appkey&\n";
    */

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
    curl_setopt($ch, CURLOPT_POST, TRUE); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params); 
    curl_setopt($ch, CURLOPT_URL, $url);
    $ret = curl_exec($ch);
    //$httpinfo = curl_getinfo($ch);
    //print_r($httpinfo);

    curl_close($ch);
    //删除上传临时文件
    unlink($tmpfile);
    return $ret;

}


/**
 * @brief 所有post 请求都可以使用这个方法
 *
 * @param $url
 * @param $appid
 * @param $appkey
 * @param $access_token
 * @param $access_token_secret
 * @param $openid
 *
 */
function do_post($url, $appid, $appkey, $access_token, $access_token_secret, $openid)
{
    //构造签名串.源串:方法[GET|POST]&uri&参数按照字母升序排列
    $sigstr = "POST"."&".rawurlencode($url)."&";

    //必要参数,不要随便更改!!
    $params = $_POST;
    $params["oauth_version"]          = "1.0";
    $params["oauth_signature_method"] = "HMAC-SHA1";
    $params["oauth_timestamp"]        = time();
    $params["oauth_nonce"]            = mt_rand();
    $params["oauth_consumer_key"]     = $appid;
    $params["oauth_token"]            = $access_token;
    $params["openid"]                 = $openid;
    unset($params["oauth_signature"]);

    //对参数按照字母升序做序列化
    $sigstr .= rawurlencode(get_normalized_string($params));

    //签名,需要确保php版本支持hash_hmac函数
    $key = $appkey."&".$access_token_secret;
    $signature = get_signature($sigstr, $key); 
    $params["oauth_signature"] = $signature; 

    $postdata = get_urlencode_string($params);

    //echo "$sigstr******\n";
    //echo "$postdata\n";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
    curl_setopt($ch, CURLOPT_POST, TRUE); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata); 
    curl_setopt($ch, CURLOPT_URL, $url);
    $ret = curl_exec($ch);

    curl_close($ch);
    return $ret;
}


   /**
   * @brief 请求临时token.请求需经过URL编码，编码时请遵循 RFC 1738
   *  
   * @param $appid
   * @param $appkey
   *
   * @return 返回字符串格式为：oauth_token=xxx&oauth_token_secret=xxx
   */
  function get_request_token($appid, $appkey)
  {
      //请求临时token的接口地址, 不要更改!!
      $url    = "http://openapi.qzone.qq.com/oauth/qzoneoauth_request_token?";
  
  
      //生成oauth_signature签名值。签名值生成方法详见（http://wiki.opensns.qq.com/wiki/【QQ登录】签名参数oauth_signature的说明）
      //（1） 构造生成签名值的源串（HTTP请求方式 & urlencode(uri) & urlencode(a=x&b=y&...)）
  	$sigstr = "GET"."&".rawurlencode("http://openapi.qzone.qq.com/oauth/qzoneoauth_request_token")."&";
  
  	//必要参数
      $params = array();
      $params["oauth_version"]          = "1.0";
      $params["oauth_signature_method"] = "HMAC-SHA1";
      $params["oauth_timestamp"]        = time();
      $params["oauth_nonce"]            = mt_rand();
      $params["oauth_consumer_key"]     = $appid;
  
      //对参数按照字母升序做序列化
      $normalized_str = get_normalized_string($params);
      $sigstr        .= rawurlencode($normalized_str);
     
  	
  	//（2）构造密钥
      $key = $appkey."&";
  
  
   	//（3）生成oauth_signature签名值。这里需要确保PHP版本支持hash_hmac函数
      $signature = get_signature($sigstr, $key);
      
  		
  	//构造请求url
      $url      .= $normalized_str."&"."oauth_signature=".rawurlencode($signature);
  
      //echo "$sigstr\n";
      //echo "$url\n";

      return file_get_contents($url);
  }

  
    

  /**
   * @brief 跳转到QQ登录页面.请求需经过URL编码，编码时请遵循 RFC 1738
   *
   * @param $appid
   * @param $appkey
   * @param $callback
   *
   * @return 返回字符串格式为：oauth_token=xxx&openid=xxx&oauth_signature=xxx&timestamp=xxx&oauth_vericode=xxx
   */
  function redirect_to_login($appid, $appkey, $callback)
  {
      //跳转到QQ登录页的接口地址, 不要更改!!
      $redirect = "http://openapi.qzone.qq.com/oauth/qzoneoauth_authorize?oauth_consumer_key=$appid&";
  
      //调用get_request_token接口获取未授权的临时token
      $result = array();
      $request_token = get_request_token($appid, $appkey);
      parse_str($request_token, $result);
  
      //request token, request token secret 需要保存起来
      //在demo演示中，直接保存在全局变量中.
      //为避免网站存在多个子域名或同一个主域名不同服务器造成的session无法共享问题
      //请开发者按照本SDK中comm/session.php中的注释对session.php进行必要的修改，以解决上述2个问题，
      $_SESSION["token"]        = $result["oauth_token"];
      $_SESSION["secret"]       = $result["oauth_token_secret"];
  
      if ($result["oauth_token"] == "")
      {
          //示例代码中没有对错误情况进行处理。真实情况下网站需要自己处理错误情况
          exit;
      }
  
      ////构造请求URL
      $redirect .= "oauth_token=".$result["oauth_token"]."&oauth_callback=".rawurlencode($callback);
      header("Location:$redirect");die;
  }

  

/**
 * @brief 获取access_token。请求需经过URL编码，编码时请遵循 RFC 1738
 *
 * @param $appid
 * @param $appkey
 * @param $request_token
 * @param $request_token_secret
 * @param $vericode
 *
 * @return 返回字符串格式为：oauth_token=xxx&oauth_token_secret=xxx&openid=xxx&oauth_signature=xxx&oauth_vericode=xxx&timestamp=xxx
 */

function get_access_token($appid, $appkey, $request_token, $request_token_secret, $vericode)
{
    //请求具有Qzone访问权限的access_token的接口地址, 不要更改!!
    $url    = "http://openapi.qzone.qq.com/oauth/qzoneoauth_access_token?";
   
    //生成oauth_signature签名值。签名值生成方法详见（http://wiki.opensns.qq.com/wiki/【QQ登录】签名参数oauth_signature的说明）
    //（1） 构造生成签名值的源串（HTTP请求方式 & urlencode(uri) & urlencode(a=x&b=y&...)）
	$sigstr = "GET"."&".rawurlencode("http://openapi.qzone.qq.com/oauth/qzoneoauth_access_token")."&";

    //必要参数，不要随便更改!!
    $params = array();
    $params["oauth_version"]          = "1.0";
    $params["oauth_signature_method"] = "HMAC-SHA1";
    $params["oauth_timestamp"]        = time();
    $params["oauth_nonce"]            = mt_rand();
    $params["oauth_consumer_key"]     = $appid;
    $params["oauth_token"]            = $request_token;
    $params["oauth_vericode"]         = $vericode;

    //对参数按照字母升序做序列化
    $normalized_str = get_normalized_string($params);
    $sigstr        .= rawurlencode($normalized_str);

    //echo "sigstr = $sigstr";

	//（2）构造密钥
    $key = $appkey."&".$request_token_secret;

	//（3）生成oauth_signature签名值。这里需要确保PHP版本支持hash_hmac函数
    $signature = get_signature($sigstr, $key);
    
	
	//构造请求url
    $url      .= $normalized_str."&"."oauth_signature=".rawurlencode($signature);

    return file_get_contents($url);
}
  
  
/**
 * <textarea  name="feeds_data" rows="20" cols="50" ></textarea>
 * @brief 发布一条动态（feeds）到QQ空间中，展现给好友.请求需经过URL编码，编码时请遵循 RFC 1738
 *
 * @param $appid
 * @param $appkey
 * @param $access_token
 * @param $access_token_secret
 * @param $openid
 */
function add_feeds($appid, $appkey, $access_token, $access_token_secret, $openid)
{
	//发布一条动态的接口地址, 不要更改!!
    $url    = "http://openapi.qzone.qq.com/feeds/add_feeds";
    echo do_post($url, $appid, $appkey, $access_token, $access_token_secret, $openid);
}  
  
  
  
/**
 * @brief 获取用户信息.请求需经过URL编码，编码时请遵循 RFC 1738
 * 
 * @param $appid
 * @param $appkey
 * @param $access_token
 * @param $access_token_secret
 * @param $openid
 *
 */
function get_user_info($appid, $appkey, $access_token, $access_token_secret, $openid)
{
	//获取用户信息的接口地址, 不要更改!!
    $url    = "http://openapi.qzone.qq.com/user/get_user_info";
    $info   = do_get($url, $appid, $appkey, $access_token, $access_token_secret, $openid);
    $arr = json_decode($info, true);
    return $arr;
}

  
  
  
  
  //code by DengLuTong, FXXKing QQ
  function getRequestToken($appid, $appkey)
  {

  
      //调用get_request_token接口获取未授权的临时token
      $result = array();
      $request_token = get_request_token($appid, $appkey);
      parse_str($request_token, $result);
  
      //request token, request token secret 需要保存起来
      //在demo演示中，直接保存在全局变量中.
      //为避免网站存在多个子域名或同一个主域名不同服务器造成的session无法共享问题
      //请开发者按照本SDK中comm/session.php中的注释对session.php进行必要的修改，以解决上述2个问题，
      //$_SESSION["token"]        = $result["oauth_token"];
      //$_SESSION["secret"]       = $result["oauth_token_secret"];
      return $result;    
  }
  
  function gotoAuthorizeURL($appid, $appkey,$keys,$callback)
  {
      ////构造请求URL
      //跳转到QQ登录页的接口地址, 不要更改!!
      $redirect = "http://openapi.qzone.qq.com/oauth/qzoneoauth_authorize?oauth_consumer_key=$appid&";      
      $redirect .= "oauth_token=".$keys["oauth_token"]."&oauth_callback=".rawurlencode($callback);
      header("Location:$redirect");die;    
  }
  
  function file_get_contents($url)
  {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
      curl_setopt($ch, CURLOPT_POST, FALSE); 
      curl_setopt($ch, CURLOPT_URL, $url);
      $ret = curl_exec($ch);
      return $ret;
  }

