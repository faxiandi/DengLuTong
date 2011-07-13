<?php
namespace DengLuTong\lib;
use DengLuTong\Exception;

class Vendor
{
  static $site,$name,$logo;
  //static $theme="<a class=\"DLT_{vendor}\" href=\"javascript:DLTWin=DLTWinOpen('{url}{vendor}','DLTWin','width=650,height=450,menubar=0,scrollbars=1, resizable=1,status=1,titlebar=0,toolbar=0,location=0')\"><img  id=\"{vendor}\" src=\"{logo}\"></img></a>";
  static $minitheme="<a class=\"DLT_Mini_Bars DLT_Mini_{vendor}\" href=\"javascript:DLTWin=DLTWinOpen('{url}{vendor}','DLTWin','900','550')\" title=\"用{name}帐号登录\"></a>";
  static $theme="<a class=\"DLT_{vendor}\" href=\"javascript:DLTWin=DLTWinOpen('{url}{vendor}','DLTWin','900','550')\"> </a>";
  
  protected  $config,$log;
  public $appid,$secid,$callback,$error;
  
  function __construct($vender)
  {
    $this->config=Config::getInstance();
    //get KEYID from config
    $this->appid=Config::getKeys('KEYID', $vender);
    if(!$this->appid)
    {
      throw new Exception($vender.' KEYID NOT EXISTS');
    }
    //get SECID from config
    $this->secid=Config::getKeys('SECID', $vender);
    if(!$this->secid)
    {
      throw new Exception($vender.' SECID NOT EXISTS');
    }
    //get CallBack from config
    $this->callback=($this->config->CallBack.$vender);
    if(!$this->appid || !$this->secid)
    {
      throw new Exception($vender.' KEYID OR SECID NOT EXISTS');
    }
    
  }
  
  /**
   * 获取登录图片
   * @param string $vender	服务商名称
   */
  static function getLogo($vender)
  {
    $logo=Config::getKeys('LOGO', $vender);
    return $logo;
  }
  
  /**
   * 显示登录条
   * @param string $url	登录地址
   * @param string $vendor	服务商
   * @param string $theme	模板
   */
  static function getBar($url='',$vendor='',$name='',$theme='theme')
  {
    empty($theme)?$theme='theme':'';
    $str=self::$$theme;
    if(empty($url))$url=Config::getInstance()->LoginUrl;
    $logo=self::getLogo($vendor);
    
    return str_replace(array('{url}','{vendor}','{logo}','{name}'),array($url,$vendor,$logo,$name),$str);
  }
  
  /**
   * oauth登录,包含获取token和获取用户信息2步
   * @param array $args
   */
  function login($args=array())
  {
    $key=$this->getAccessToken($args);
    return $this->showUser($key);
  }  
  
  /**
   * 先获取request token，然后跳转至服务商登录页面
   */
  function getRequestToken(){}
  
  function getAccessToken($args=array()){}

  /**
   * 显示登录页面
   */
  function gotoLoginPage(){}
  function logout(){}
  
  /**
   * OPENID登录跳转，返回时直接前往callback页面
   */
  static function openidredirect()
  {
    $query=str_replace('dltact=login', 'dltact=callback', $_SERVER['QUERY_STRING']);
    header('Location: ?'.$query);    
  }

  
}
