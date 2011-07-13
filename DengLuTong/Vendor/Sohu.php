<?php
namespace DengLuTong;
use DengLuTong\lib\OAuth\Sohu\SohuOauth;
class Vendor_Sohu extends lib\Vendor
{
  static $site='Sohu';
  static $name='搜狐';
  static $logo='';
  static $theme='';
  protected $oauth,$client;
  function __construct()
  {
    parent::__construct(self::$site);
    $this->oauth=new SohuOauth($this->appid, $this->secid);
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
		$keys = $this->oauth->getRequestToken( $this->callback);
		if($keys)
		{
  		$url = $this->oauth->getAuthorizeUrl1($keys['oauth_token'], urlencode($this->callback));
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
      $this->oauth->token->key=$args['oauth_token'];
      $this->oauth->token->secret=$args['oauth_token_secret'];
      
  		$keys = $this->oauth->getAccessToken(  $args['oauth_verifier'] ) ;
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
    $this->oauth->token->key=$keys['oauth_token'];
    $this->oauth->token->secret=$keys['oauth_token_secret'];
    /*使用open api*/
    $url = 'http://api.t.sohu.com/users/show.json';
    $sh_user = $this->oauth->get($url);
    if(isset($sh_user['id']))
    {
      $user=array(
        'id'=>$sh_user['id'],
        'name'=>$sh_user['name']?$sh_user['name']:$sh_user['screen_name'],
        'screen_name'=>$sh_user['screen_name'],
        'desc'=>$sh_user['description'],
        'url'=>$sh_user['url'],
        'img'=>$sh_user['profile_image_url'],
        'gender'=>'',
        'email'=>'',
        'location'=>'',
      );
      return $user;
    }
    return FALSE;
    
  }

}
