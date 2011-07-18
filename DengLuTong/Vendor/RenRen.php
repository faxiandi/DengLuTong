<?php
namespace DengLuTong;
use DengLuTong\lib\OAuth\RenRen\RenRenOauth;
class Vendor_RenRen extends lib\Vendor
{
  static $site='RenRen';
  static $name='人人';
  static $logo='';
  //static $theme='';
  protected $oauth,$client;
  function __construct()
  {
    parent::__construct(self::$site);
    //$this->oauth=new lib\RenRenOauth($this->appid, $this->secid);
    $this->callback=urlencode($this->callback);
  }
  

  
  /**
   * 获取登录条
   * @param string $url	链接地址
   */
//  static function getBar($theme='',$url='',$vendor='',$name='')
//  {
//    return parent::getBar($url,self::$site,self::$name,$theme);
//  }
  
  function gotoLoginPage()
  {
    $url = "https://graph.renren.com/oauth/authorize?client_id=".$this->appid."&redirect_uri=".$this->callback."&response_type=code";
    header('Location: '.$url);
    die;
  }
  
  function getRequestToken()
  {
    
  }

  
  function getAccessToken($args=array())
  {
  	$url = "https://graph.renren.com/oauth/token?client_id=" . $this->appid . "&client_secret=" . $this->secid . "&redirect_uri=".$this->callback."&grant_type=authorization_code&code=".$args['code'];
		$file = $this->file_get_contents ( $url );
		$json = json_decode ( $file );
		if($json)
		{
			$url = "https://graph.renren.com/renren_api/session_key?oauth_token=" . $json->access_token;		
			$file = $this->file_get_contents ( $url );
			$info=json_decode($file,true);
			if($info)
			{
  			$keys=array('session_secret'=>$info['renren_token']['session_secret'],
  			'expires_in'=>$info['renren_token']['expires_in'],
  			'session_key'=>$info['renren_token']['session_key'],'oauth_token'=>$info['oauth_token'],'user_id'=>$info['user']['id']);
  			DengLuTong::setKeys($keys);	
  			return $info;
			}
			return FALSE;
		}else{
			return FALSE;
		}
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
  

  function showUser($keys=array())
  {
    if(isset($keys['user']['id']))
    {
      $this->oauth=new RenRenOauth($this->appid, $this->secid,$keys['renren_token']['session_key']);
      $rr_user=$this->oauth->users('getInfo',array('uids'=>$keys['user']['id']));
      if($rr_user && isset($rr_user[0]['uid']))
      {
        $user=array(
          'id'=>$rr_user[0]['uid'],
          'name'=>$rr_user[0]['name'],
          'screen_name'=>$rr_user[0]['name'],
          'desc'=>'',
          'url'=>'',
          'img'=>urldecode($rr_user[0]['tinyurl']),
          'gender'=>$rr_user[0]['sex'],
          'email'=>'',
          'location'=>$rr_user[0]['hometown_location']['province'].$rr_user[0]['hometown_location']['city'],
        );
        return $user;
      }
    }
    return FALSE;
    
  }

}
