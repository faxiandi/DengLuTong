<?php
namespace DengLuTong;
use DengLuTong\lib\LightOpenID;

class Vendor_KaiXin extends lib\Vendor
{
  static $site='KaiXin';
  static $name='开心网';
  static $logo='';
  static $theme='';
  protected $oauth,$client;
  public $v='1.0';
  function __construct()
  {
    parent::__construct(self::$site);
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
    $callback=str_replace('//','',$this->callback);
    $callback=urlencode(substr($callback, strpos( $callback,'/')));
    //$url = "http://www.kaixin001.com/oauth2/authorize.php?client_id=".$this->appid."&redirect_uri=".$this->callback."&response_type=code";
    $url='http://www.kaixin001.com/login/connect.php?appkey='.$this->appid.'&re='.$callback.'&t='.rand(1,99);
    header('Location: '.$url);
    die;
  }
  
  function getRequestToken()
  {
    
  }

  
  function getAccessToken($args=array())
  {
    if(isset($_GET['session_key']))
    {
      $session_key=$_GET['session_key'];
      setcookie("kx_connect_session_key", $session_key, time()+3600*6);
    }else{
      $session_key =  $_COOKIE["kx_connect_session_key"];
    }
    if($session_key)
    {
      $keys['session_key']=$session_key;
      DengLuTong::setKeys($keys);
      return $session_key;
    }else{
      return FALSE;
    }
//  	$url = "http://www.kaixin001.com/oauth2/token.php";
//  	$url.="?client_id=" .$this->appid. 
//  	            "&client_secret=" . $this->secid . 
//  	            "&grant_type=authorization_code".
//  							"&code=".$args['code'].
//  	            "&redirect_uri=".$this->callback;
//  	$params=array(
//    	'client_id'=>$this->appid,
//    	'client_secret'=>$this->secid,
//    	'grant_type'=>'authorization_code',
//    	'code'=>$args['code'],
//    	'redirect_uri'=>$this->callback,
//  	);
//  	$string=$this->buildQuery($params);
//		$file = $this->file_get_contents ( $url ,FALSE,$string);
//		$keys = json_decode ( $file,TRUE );
//		print_r($keys);die;
//		if($keys)
//		{
//			$_SESSION['keys']=$keys;
//			return $keys;
//		}else{
//			return FALSE;
//		}
  }
  
  
  function showUser($keys)
  {
    $param = array(
    	'method' => 'users.getLoggedInUser',
    	'format' => 'json',
    	'mode' => 0,
      'api_key'=>$this->appid,
      'session_key'=>$keys,
    );
    $query = $this->buildQuery($param);
    $json = $this->postRequest('http://www.kaixin001.com/rest/rest.php', $query);
    $result=json_decode($json,TRUE);
    if($result['error']['code'])
    {
      return FALSE;
    }else{
      $uid=$result['result'];
      $param = array(
      	'method' => 'users.getInfo',
      	'format' => 'json',
      	'mode' => 0,
      	'uids' => $uid,
        'api_key'=>$this->appid,
      	'session_key'=>$keys,
      );
      $query = $this->buildQuery($param);
      $json = $this->postRequest('http://www.kaixin001.com/rest/rest.php', $query);
      $result=json_decode($json,TRUE);
      if($result)
      {
        $user=array(
          'id'=>$result[0]['uid'],
          'name'=>$result[0]['name'],
          'screen_name'=>$result[0]['name'],
          'email'=>'',
          'desc'=>'',
          'url'=>'',
          'img'=>$result[0]['logo50'],
          'gender'=>$result[0]['gender'],
          'location'=>$result[0]['city'],
          'birthday'=>$result[0]['birthday'],
        );
        return $user;        
      }
      return FALSE;
    }
    
  }  
  
  
	public  function postRequest($url,$query)
	{
		$post_string = $query;
		$result='';
		if (function_exists('curl_init'))
		{
			$useragent = 'kaixin001.com API PHP5 Client 1.1 (curl) ' . phpversion();
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			if (strlen($post_string) >= 3)
			{
				curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
			}
			//use https
			//curl_setopt($ch, CURLOPT_USERPWD, "username:password");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			$result = curl_exec($ch);
			curl_close($ch);
		}
		return $result;
	}  
  
  
  
  
  
  function file_get_contents($url,$post=FALSE,$post_string='')
  {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
      curl_setopt($ch, CURLOPT_POST, $post); 
      curl_setopt($ch, CURLOPT_URL, $url);
  		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
  		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
  		curl_setopt($ch, CURLOPT_USERAGENT, 'kaixin001.com API PHP5 Client 1.1 (curl) ' . phpversion());
      if($post)
      {
  			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
  			//curl_setopt($ch, CURLOPT_USERPWD, "username:password");
      }
      $ret = curl_exec($ch);
      return $ret;    
  }
	public function buildQuery($param)
	{
		$param['call_id'] = microtime(true);
		$param['v'] = $this->v;
		ksort($param);
		$request_str = '';
		foreach ($param as $key => $value)
		{
			$request_str .= $key . '=' . $value.'';
		}
		//$request_str=rtrim($request_str,'&');
		$sig = $request_str . $this->secid;
		$sig = md5($sig);
		$param['sig'] = $sig;
		$query = http_build_query($param);
		return $query;
	}  

  
}
