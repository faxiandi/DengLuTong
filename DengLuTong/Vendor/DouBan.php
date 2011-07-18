<?php
namespace DengLuTong;
use DengLuTong\lib\OAuth\DouBan\DouBanOauth;
class Vendor_DouBan extends lib\Vendor
{
  static $site='DouBan';
  static $name='豆瓣';
  static $logo='';
  //static $theme='';
  protected $oauth,$client;
  function __construct()
  {
    parent::__construct(self::$site);
    $this->oauth=new DouBanOauth($this->appid, $this->secid);
  }
  
  function setClient($keys=array())
  {
  }
  
  /**
   * 获取登录条
   * @param string $url	链接地址
   */
//  static function getBar($theme='',$url='',$vendor='',$name='')
//  {
//    return parent::getBar($url,self::$site,self::$name,$theme);
//  }
  
  /**
   * 显示登录页面
   */
  function gotoLoginPage()
  {
    $this->getRequestToken();
  }
  
  /**
   * 先获取request token，然后跳转至服务商登录页面
   */
  function getRequestToken()
  {
		$keys = $this->oauth->getRequestToken();
		if($keys)
		{
  		$url = $this->oauth->getAuthorizeURL( $keys['oauth_token'] ,$keys['oauth_token_secret'] , $this->callback );
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
  		$keys = $this->oauth->getAccessToken( array('key'=>$args['oauth_token'],'secret'=>$args['oauth_token_secret']) ) ;
  		if(isset($keys['douban_user_id'])){
  			DengLuTong::setKeys($keys);
  			return $keys;
  		}else {
  			return FALSE;		
  		}
    }
  }
  
  /**
   * 获取用户信息
   * @param array $keys
   */
  function showUser($keys=array())
  {
    $this->oauth->setToken($keys['oauth_token'], $keys['oauth_token_secret']);
    $url='http://api.douban.com/people/'.urlencode('@me');
    $xml=$this->oauth->accessResource('GET',$url);
    preg_match_all('/<title>(.*?)<\/title>(\s+)<link href="(.*?)" rel="self"(.*?)link href="(.*?)" rel="alternate(.*?)link href="(.*?)" rel="icon"(.*?)<db\:location id="(.*?)">(.*?)<\/db\:location>(.*?)<db\:uid>(.*?)<\/db\:uid>/is',$xml,$matches);
    if($matches && $matches[12])
    {
      $user=array(
        'id'=>$matches[12][0],
        'name'=>$matches[1][0],
        'screen_name'=>$matches[1][0],
        'desc'=>'',
        'url'=>$matches[5][0],
        'img'=>$matches[7][0],
        'gender'=>'',
        'email'=>'',
        'location'=>$matches[10][0],
      );
      return $user;
    }
    return FALSE;
  }

}
