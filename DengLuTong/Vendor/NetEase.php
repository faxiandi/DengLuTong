<?php
namespace DengLuTong;
use DengLuTong\lib\OAuth\NetEase\NetEaseOauth;
class Vendor_NetEase extends lib\Vendor
{
  static $site='NetEase';
  static $name='网易';
  static $logo='';
  static $theme='';
  protected $oauth,$client;
  function __construct()
  {
    parent::__construct(self::$site);
    $this->oauth=new NetEaseOauth($this->appid, $this->secid);
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

		$keys = $this->oauth->oauth->getRequestToken();
		if($keys)
		{
  		$url = $this->oauth->oauth->getAuthorizeURL( $keys['oauth_token'] , $this->callback );
  		DengLuTong::setKeys($keys);
  		header('Location: '.$url);
  		die;
		}else{
		  throw new Exception('Get Request Token Error.');
		}
  }

  
  function getAccessToken($args=array())
  {
    if (!empty($args['oauth_token']) && !empty($args['oauth_token_secret'])) {
			$this->oauth=new NetEaseOauth($this->appid, $this->secid,$args['oauth_token'],$args['oauth_token_secret']);
  		$keys = $this->oauth->oauth->getAccessToken(  $args['oauth_token'] ) ;
  		if(isset($keys['oauth_token'])){
  			DengLuTong::setKeys($keys);	
  			return $keys;
  		}else {
  			return FALSE;		
  		}
    }
  }
  

  function showUser($keys=array())
  {
    $ne_user=$this->oauth->verify_credentials();
    if(isset($ne_user['id']))
    {
      $user=array(
        'id'=>$ne_user['id'],
        'name'=>$ne_user['name'],
        'screen_name'=>$ne_user['name'],
        'desc'=>$ne_user['description'],
        'url'=>$ne_user['url'],
        'img'=>$ne_user['profile_image_url'],
        'gender'=>$ne_user['gender']?$ne_user['gender']:'',
        'email'=>'',
        'location'=>'',
      );
      return $user;
    }
    return FALSE;
    
  }

}
