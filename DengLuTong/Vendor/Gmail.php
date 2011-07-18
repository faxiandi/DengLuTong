<?php
namespace DengLuTong;
use DengLuTong\lib\OAuth\OpenID\LightOpenID;

class Vendor_Gmail extends lib\Vendor
{
  static $site='Gmail';
  static $name='Gmail';
  static $logo='';
  //static $theme='';
  protected $oauth,$client;
  function __construct()
  {
    parent::__construct(self::$site);
    $this->openid = new LightOpenID();
    $this->identity='https://www.google.com.hk/accounts/o8/id';
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
   * OPENID登录无须获取token，直接跳转
   */
  function gotoLoginPage()
  {
    parent::openidredirect();
  }
  

  /**
   * (non-PHPdoc)
   * @see DengLuTong\lib.Vendor::login()
   */
  function login($args=array())
  {
    if(!$this->openid->validate())
    {
      $this->openid->identity = $this->identity;
      $this->openid->required = array('namePerson/friendly', 'contact/email' , 'contact/country/home', 'namePerson/first', 'pref/language', 'namePerson/last');
      if(!$this->openid->mode)
      {
        header('Location: ' . $this->openid->authUrl());
        die;
      }    
    }
    $key=$this->getAccessToken($args);
    return $this->showUser();
  }
  
  function getAccessToken($args=array())
  {
    if($this->openid->mode == 'cancel')
    {
      throw new Exception('User has canceled.');
    }else{
      if($this->openid->validate())
      {
        return $this->openid->validate();
      }else{
        throw new Exception('User has not logged in.');
      }
    }
  }
  
  function showUser()
  {
    $info=$this->openid->getAttributes();
    preg_match_all('/id=(.*?)$/is', $this->openid->identity,$match);
    if(!$match)return FALSE;
    if(is_array($info))
    {
      $user=array(
        'id'=>$match[1][0],
        'name'=>$info['namePerson/first'].'.'.$info['namePerson/last'],
        'screen_name'=>$info['namePerson/first'].' '.$info['namePerson/last'],
        'email'=>$info['contact/email'],
        'desc'=>'',
        'url'=>'',
        'img'=>'',
        'gender'=>'',
        'location'=>$info['contact/country/home'],
      );
      return $user;
    }
    return FALSE;
    
  }
  
}
