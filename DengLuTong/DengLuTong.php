<?php
namespace DengLuTong;

use DengLuTong\lib\Db\Db;
use DengLuTong\lib\Config;

session_start();
class DengLuTong
{
  static $config;
  private static $instance = NULL;
  private $vendor,$local;
  
  /**
   * 自动加载
   * @param string $class	类名
   * @throws Exception
   */
  static function autoload($class)
  {
    $dir=dirname(__FILE__).DIRECTORY_SEPARATOR.'..';
    $file=$dir.DIRECTORY_SEPARATOR.str_replace('_',DIRECTORY_SEPARATOR,$class).'.php';
    $file=str_replace('\\',DIRECTORY_SEPARATOR,$file);
    //echo $class.':'.$file."\r\n<br>";
    if(is_file($file))
    {
      require_once($file);
    }else{
      //debug_print_backtrace();
      throw new Exception("$class does not exist"); 
    }
  }
  
  private function __construct($vendor='',$local='Local')
  {
    self::getConfig();
    $this->setVendor($vendor);
    $local==''?$local=self::$config->Local:'';
    if($local)
    {
      $classname='DengLuTong\lib\Local\\'.$local;
      $this->local=new $classname();
    }
  }

  private function __clone()
  {
  }
  
  static function getInstance($vendor='',$local='')
  {
    if (! self::$instance)
    {
      self::$instance = new DengLuTong($vendor,$local);
    }
    return self::$instance;
  }  
  
  function setLocal($local='Local')
  {
      $classname='DengLuTong\lib\Local\\'.$local;
      $this->local=new $classname();
  }
  
  
  
  
  /**
   * 获取设置
   */
  static function getConfig()
  {
    self::$config=Config::getInstance();
  }
  
  /**
   * 获取所有登录条
   */
  static function getBars($theme='')
  {
    self::getConfig();
    $bars=array();
    if(is_array(self::$config->KEYID)){
      foreach (self::$config->KEYID as $site=>$app)
      {
        if($app)
        {
          $class='DengLuTong\Vendor_'.$site;
          $bars[]=$class::getBar($theme);
        }
      }
    }
    return $bars;
  }
  
  /**
   * 显示所有登录条
   * @param string $file	模板文件
   */
  static function _showBars()
  {
    self::getConfig();
    $file=self::$config->themefile;
    $theme=self::$config->theme;
    $bars=self::getBars($theme);
    self::showTpl($file,array('bars'=>$bars));
  }
  
  /**
   * 显示模板
   * @param string $file	模板文件
   * @param array $data	模板数据
   */
  static function showTpl($file,$data=array())
  {
    foreach ($data as $k=>$v)
    {
      $$k=$v;    //注意变量污染!!
    }
    return include_once(self::getTpl($file));
  }
  
  /**
   * 获取模板文件
   * @param string $file
   */
  static function getTpl($file)
  {
    $file=self::getTplPath().$file.'.php';
    if(is_file($file))
    {
      return $file;
    }else{
      throw new Exception('File < '.$file.' > not found.');
    }
    
  }
  
  /**
   * 获取模板目录
   */
  static function getTplPath()
  {
    return 'DengLuTong/tpl/';
    //$dir=dirname(__FILE__);
    //return $dir.DIRECTORY_SEPARATOR.'DengLuTong/tpl/';
  }
  
  /**
   * 设置服务商
   * @param string $vendor	服务商
   */
  function setVendor($vendor='')
  {
    if($vendor)
    {
      $class='DengLuTong\\'.$vendor;
      $this->vendor=new $class();
    }
  }
  
  function getRequestToken()
  {
    return $this->vendor->getRequestToken();
  }

  /**
   * 登录过程
   * @param string $vendor	服务商
   * @param array $args
   */
  function callback($vendor,$args=array())
  {
    $keys=DengLuTong::getKeys();
    //各服务商返回的参数各不相同，故统一处理。
    $args=array ( 
          'oauth_token' => isset( $_GET['oauth_token'] ) ? $_GET['oauth_token'] : '' , 
          'oauth_token_secret' => !empty($keys['oauth_token_secret'])?$keys['oauth_token_secret']:'', 
          'oauth_verifier' => isset( $_GET['oauth_verifier'] ) ? $_GET['oauth_verifier'] : '',
          'openid' => isset( $_GET['openid'] ) ? $_GET['openid'] : '' ,
      		'oauth_vericode' => isset( $_GET['oauth_vericode'] ) ? $_GET['oauth_vericode'] : '' ,
      		'code' => isset( $_GET['code'] ) ? $_GET['code'] : '' ,
      );
    $user=$this->vendor->login($args);
    if($user)
    {
      $user['vendor']=$vendor;
      $this->setUser($user);
    }
    return $user;    
  }
  
  /**
   * 获取用户信息
   * @param mix $uid 用户ID
   * @param array $keys
   */
  function showUser($uid,$keys=array())
  {
    return $this->vendor->showUser($uid,$keys);
  }
  
  /**
   * 前往登录页面
   */
  function gotoLoginPage()
  {
    self::clearSession();
    return $this->vendor->gotoLoginPage();
  }
  function login($vendor='')
  {
    return $this->gotoLoginPage();
  }
  
  /**
   * 暂存登录后用户信息
   * @param mix $user
   */
  static function setUser($user)
  {
    $_SESSION['DLTUSER']= $user ;
  }
  /**
   * 获取暂存的用户信息
   */
  static function getUser()
  {
    return !empty($_SESSION['DLTUSER'])?$_SESSION['DLTUSER']:FALSE;
  }
  /**
   * 暂存key
   * @param array $keys
   */
  static function setKeys($keys)
  {
    $_SESSION['DLTKEYS']= $keys ;
  }
  /**
   * 获取暂存的key
   */
  static function getKeys()
  {
    return !empty($_SESSION['DLTKEYS'])?$_SESSION['DLTKEYS']:FALSE;
  }
  /**
   * 清除暂存
   */
  static function clearSession()
  {
    $_SESSION['DLTUSER']=NULL;
    $_SESSION['DLTKEYS']=NULL;
    unset($_SESSION['DLTUSER'],$_SESSION['DLTKEYS']);
    setcookie("kx_connect_session_key", '', time()-3600*6);
  }
  
  /**
   * 绑定用户
   */
  static function bind($local='Local')
  {
    !$local?$local='Local':1;
    if(!self::getUser())
    {
      throw new Exception('Login Failed');
    }
    if($local)
    {
      $classname='DengLuTong\lib\Local\\'.$local;
      $class=new $classname();
      $class->bind();
    }
    
  }
  
  /**
   * 保存用户
   */
  static function save()
  {
      $this->local->save();
  }
  
  
  
  /**
   * 登出
   */
  function logout()
  {
    self::clearSession();
    header('Location: ./');
  }
  
  /**
   * 显示所有登录条
   */
  function showBars()
  {
    self::_showBars();    
  }



  function getBinded($uid)
  {
    $binded=$this->local->getBinded($uid);
    $sites=self::$config->KEYID;
    $i=0;
    foreach ($sites as $name=>$site)
    {
      $class='DengLuTong\Vendor_'.$name;
      $result[$i]=array('site'=>$class::$site,'name'=>$class::$name,'vendor'=>'Vendor_'.$name,'dlt_user'=>'');
      if($binded)
      {
        foreach ($binded as $bind)
        {
          if($bind['vendor']=='Vendor_'.$name)
          {
            $result[$i]['dlt_user']=$bind['screen_name'];
            break;
          }
        }
      }
      $i++;
    }
    return $result;
  }
  
  function unbind($vendor='',$uid='')
  {
    return $this->local->unbind($uid,$vendor);
  }
  
  function localLogin($uid)
  {
    return $this->local->localLogin($uid);
  }
  
  
  
}



spl_autoload_register('\DengLuTong\DengLuTong::autoload');
require_once 'DengLuTong/DLTConfig.php';
