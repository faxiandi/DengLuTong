<?php
namespace DengLuTong;
use DengLuTong\lib\OAuth\Sina\WeiboOauth ;
use DengLuTong\lib\OAuth\Sina\WeiboClient ;
class Vendor_Sina extends lib\Vendor
{
  static $site='Sina';
  static $name='新浪';
  static $logo='';
  //static $theme='';
  protected $oauth,$client;
  function __construct()
  {
    parent::__construct(self::$site);
    $this->oauth=new WeiboOauth($this->appid, $this->secid);
  }
  
  function setClient($keys=array())
  {
    $this->client = new WeiboClient($this->appid, $this->secid, $keys['oauth_token'], $keys['oauth_token_secret']);
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
    $this->getRequestToken();
  }
  
  function getRequestToken()
  {
		$keys = $this->oauth->getRequestToken();
		if($keys)
		{
  		$url = $this->oauth->getAuthorizeURL( $keys['oauth_token'] ,false , $this->callback );
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
			//$this->oauth->token = new lib\OAuthConsumer($args['oauth_token'], $args['oauth_token_secret']);
      $this->oauth->token->key=$args['oauth_token'];
      $this->oauth->token->secret=$args['oauth_token_secret'];			
  		$keys = $this->oauth->getAccessToken(  $args['oauth_verifier'] ) ;
  		if(isset($keys['user_id'])){
  			DengLuTong::setKeys($keys);	
  			return $keys;
  		}else {
  			return FALSE;		
  		}
    }
  }
  

  function showUser($keys=array())
  {
    $this->setClient($keys);
    $sina_user = $this->client->show_user($keys['user_id']);
    if(isset($sina_user['error_code']))
    {
      //$this->error=$sina_user;
      //throw new Exception($sina_user['error']);
      return FALSE;
    }else{
      $user=array(
        'id'=>$sina_user['id'],
        'name'=>$sina_user['name'],
        'screen_name'=>$sina_user['screen_name'],
        'desc'=>$sina_user['description'],
        'url'=>$sina_user['url'],
        'img'=>$sina_user['profile_image_url'],
        'gender'=>$sina_user['gender'],
        'email'=>'',
        'location'=>'',
      );
      return $user;
    }
    
  }

}
