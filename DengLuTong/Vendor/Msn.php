<?php
namespace DengLuTong;
use DengLuTong\lib\OAuth\Msn\MsnOauth;
class Vendor_Msn extends lib\Vendor
{
  static $site='Msn';
  static $name='Msn';
  static $logo='';
  static $theme='';
  protected $oauth,$client;
  function __construct()
  {
    parent::__construct(self::$site);
    $this->oauth=new MsnOauth($this->appid, $this->secid);
    $this->callback=urlencode($this->callback);
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
    $url = "https://oauth.live.com/authorize?scope=wl.basic wl.emails wl.signin&response_type=code&client_id=".$this->appid."&redirect_uri=".$this->callback."&state=1";
    header('Location: '.$url);
    die;
  }
  
  function getRequestToken()
  {
    
  }
  
  function logout()
  {
    $url='https://oauth.taobao.com/logoff?client_id='.$this->appid.'&redirect_uri='.str_replace('callback','logout',$this->callback);    
    header('Location: '.$url);
    die;
  }

  
  function getAccessToken($args=array())
  {
    if(!$args['code'])return FALSE;
    $params=array(
      'client_id'=>$this->appid,
      'client_secret'=>$this->secid,
      'redirect_uri'=>urldecode($this->callback),
      'code'=>$args['code'],
      'grant_type'=>'authorization_code',
    );
  	$url = "https://oauth.live.com/token";
		$file = $this->oauth->curl( $url ,$params);
		$keys = json_decode ( $file ,true);
		if($keys)
		{
  			DengLuTong::setKeys($keys);	
  			return $keys;

		}else{
			return FALSE;
		}
  }


  function showUser($keys=array())
  {
    $imgurl = "https://apis.live.net/v5.0/me/picture?access_token=".$keys['access_token'];
    if(!empty($keys['access_token']))
    {
      $url="https://apis.live.net/v5.0/me?access_token=".$keys['access_token'];
      $json=$this->oauth->curl($url);
      $msn_user=json_decode($json,TRUE);
      if($msn_user && isset($msn_user['id']))
      {
        $user=array(
          'id'=>$msn_user['id'],
          'name'=>$msn_user['first_name'].' '.$msn_user['last_name'],
          'screen_name'=>$msn_user['name'],
          'desc'=>'',
          'url'=>$msn_user['link'],
          'img'=>"https://apis.live.net/v5.0/".$msn_user['id']."/picture",
          //'img'=>$imgurl,
          'gender'=>$msn_user['gender']?$msn_user['gender']:'',
          'email'=>$msn_user['emails']['account'],
          'location'=>$msn_user['locale'],
        );
        return $user;
      }
    }
    return FALSE;
    
  }

}
