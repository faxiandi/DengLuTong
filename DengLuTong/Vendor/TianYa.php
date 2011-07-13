<?php
namespace DengLuTong;
use DengLuTong\lib\Log;

use DengLuTong\lib\OAuth\TianYa\TianYaOauth;
class Vendor_TianYa extends lib\Vendor
{
  static $site='TianYa';
  static $name='天涯';
  static $logo='';
  static $theme='';
  protected $oauth,$client;
  function __construct()
  {
    parent::__construct(self::$site);
    $this->oauth=new TianYaOauth($this->appid, $this->secid);
  }
  

  
  /**
   * 获取登录条
   * @param string $url	链接地址
   */
  static function getBar($theme='',$url='',$vendor='',$name='')
  {
    return parent::getBar($url,self::$site,self::$name,$theme);
  }
  
  function gotoLoginPage()
  {
    $this->getRequestToken();
  }
  
  function getRequestToken()
  {

		$keys = $this->oauth->getRequestToken();
		if($keys)
		{
  		$url = $this->oauth->getAuthorizeURL( $keys['oauth_token'] ,FALSE, $this->callback );
  		DengLuTong::setKeys($keys);
  		header('Location: '.$url);
  		die;
		}else{
		  Log::getInstance()->tofile(__CLASS__.' -> '.__METHOD__."\r\nError : ".serialize($keys));
		  throw new Exception('Get Request Token Error.');
		}
  }

  
  function getAccessToken($args=array())
  {
    if (!empty($args['oauth_token']) && !empty($args['oauth_token_secret'])) {
			$this->oauth=new TianYaOauth($this->appid, $this->secid,$args['oauth_token'],$args['oauth_token_secret']);
  		$keys = $this->oauth->getAccessToken(  $args['oauth_verifier'] ) ;
  		if(isset($keys['oauth_token'])){
  			DengLuTong::setKeys($keys);	
  			return $keys;
  		}else {
  		  Log::getInstance()->tofile(__CLASS__.' -> '.__METHOD__."\r\nError : ".serialize($keys));
  			return FALSE;		
  		}
    }
  }
  

  function showUser($keys=array())
  {
    $_user=$this->get_user_info($this->appid,$this->secid,$keys['oauth_token'],$keys['oauth_token_secret']);
    if($_user && isset($_user['user']['user_id']))
    {
      $ty_user=$_user['user'];
      $user=array(
        'id'=>$ty_user['user_id'],
        'name'=>$ty_user['user_name'],
        'screen_name'=>$ty_user['user_name'],
        'desc'=>$ty_user['describe'],
        'url'=>$ty_user['url'],
        'img'=>$ty_user['head'],
        'gender'=>$ty_user['sex']?$ty_user['sex']:'',
        'email'=>'',
        'location'=>$ty_user['location'],
      );
      return $user;
    }
    Log::getInstance()->tofile(__CLASS__.' -> '.__METHOD__."\r\nError : ".serialize($user));
    return FALSE;
    
  }


  
  
  
  function get_user_info($appkey,$appsecret,$oauth_token,$oauth_token_secret)
  {
  	$url = 'http://open.tianya.cn/api/user/info.php'; //发微博接口地址
  	//do_post和do_get为请求接口的公用方法，可以对应线上接口直接使用
  	$data = $this->do_post($url,$appkey,$appsecret,$oauth_token,$oauth_token_secret);
  	$json=json_decode($data,true);
  	if($json)
  	{
  	  return $json;
  	}
  	Log::getInstance()->tofile(__CLASS__.' -> '.__METHOD__."\r\nError : ".serialize($data));
  	return FALSE;
  }  
  
  
  
	function do_get($url,$appkey,$appsecret,$oauth_token,$oauth_token_secret,$param=null)
	{
		$param['timestamp'] = time();
		$param['appkey'] = $appkey;
		$param['tempkey'] = strtoupper(md5($param['timestamp'].$appkey.$oauth_token.$oauth_token_secret.$appsecret));
		$param['oauth_token'] = $oauth_token;
		$param['oauth_token_secret'] = $oauth_token_secret;
		$addstr = http_build_query($param);
		$url.='?'.$addstr;
		return $this->request($url,null,'get');
	}
	function do_post($url,$appkey,$appsecret,$oauth_token,$oauth_token_secret,$param=null)
	{
		$param['timestamp'] = time();
		$param['appkey'] = $appkey;
		$param['tempkey'] = strtoupper(md5($param['timestamp'].$appkey.$oauth_token.$oauth_token_secret.$appsecret));
		$param['oauth_token'] = $oauth_token;
		if(isset($param['media']) && realpath($param['media'])) $param['media'] = '@'.realpath($param['media']);
		$param['oauth_token_secret'] = $oauth_token_secret;
		//var_dump($param);
		return $this->request($url,$param);
		
	}
	function request($url,$param=null,$method='post')
	{
		if($method=='get')
		{
			$send_data.= http_build_query($param);
			if(eregi('\?',$url))
			{
				$url.= '&'.$send_data;
			}
			else
			{
				$url.= '?'.$send_data;
			}
			
		}
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url); 
		if($method=='post')
		{
			$send_data = $param;
			curl_setopt($ch, CURLOPT_POST, 1);
			//添加变量
			curl_setopt($ch, CURLOPT_POSTFIELDS, $send_data);
		}
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
		$MySources = curl_exec ($ch); 
		curl_close($ch); 
		return $MySources; 
	}
	  
  
  
  
  
  
}
