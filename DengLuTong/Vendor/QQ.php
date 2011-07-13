<?php
namespace DengLuTong;

use DengLuTong\lib\OAuth\QQ\QQOauth;

class Vendor_QQ extends lib\Vendor
{
  static $site='QQ';
  static $name='腾讯QQ';
  static $logo='';
  static $theme='';
  protected $oauth,$client;
  function __construct()
  {
    parent::__construct(self::$site);
    $this->oauth=new QQOauth();
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
		$keys = QQOauth\getRequestToken($this->appid, $this->secid, $this->callback);
		if($keys)
		{
  		DengLuTong::setKeys($keys);	
  		QQOauth\gotoAuthorizeURL($this->appid, $this->secid,$keys , $this->callback);
  		die;
		}else{
		  throw new Exception('Get Request Token Error.');
		}
  }

  
  function getAccessToken($args=array())
  {
    if (!empty($args['oauth_token']) && !empty($args['oauth_token_secret'])) {
  		$result =QQOauth\get_access_token($this->appid, $this->secid, $args['oauth_token'], $args['oauth_token_secret'], $args['oauth_vericode']);
  		$keys=array() ;
  		parse_str($result, $keys);
  		if(isset($keys['openid'])){
  			DengLuTong::setKeys($keys);	
  			return $keys;
  		}else {
  			return FALSE;		
  		}
    }
  }
  
  function showUser($keys=array())
  {
    $qq_user = QQOauth\get_user_info($this->appid, $this->secid, $keys['oauth_token'], $keys['oauth_token_secret'], $keys['openid']);
    if($qq_user && $qq_user['ret']==0)
    {
      $_keys=DengLuTong::getKeys();
      $user=array(
        'id'=>$_keys['openid'],
        'name'=>$qq_user['nickname'],
        'screen_name'=>$qq_user['nickname'],
        'desc'=>'',
        'url'=>'',
        'img'=>$qq_user['figureurl_1'],  /*尺寸 figureurl 30*30,figureurl_1 50*50,figureurl_2 100*100*/
        'gender'=>'',
        'email'=>'',
        'location'=>'',
      );
      return $user;
    }
    return FALSE;
  }
  
}
