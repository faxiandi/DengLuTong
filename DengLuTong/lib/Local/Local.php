<?php
namespace DengLuTong\lib\Local;
use DengLuTong\DengLuTong;

use DengLuTong\lib\Db\Db;
use DengLuTong\lib\Config;

class Local
{
  private $dlt_user,$db,$dbconfig;
  function __construct()
  {
    $this->dbconfig=Config::getInstance()->DB;
    $this->initDB();
  }
  function checkRegistered($uid,$vendor)
  {
    $sql="select user_id from ".$this->dbconfig['tablename']." where dlt_user_id='{$uid}' and from='{$vendor}'";
    echo $sql;
  } 
  function initDB()
  {
    $this->db=Db::getInstance();
    //$this->db->connect();
  }  
  
  /**
   * 本地注册流程
   */
  function localRegister()
  {
    return rand(1,10);
  }
  
  function localLogout(){}
  
  /**
   * 本地登录处理
   * @param mix $uid 本地用户ID
   */
  function localLogin($uid)
  {
    $sql="select * from user where id='{$uid}'";
    $result=$this->db->select($sql);
    if($result)
    {
      $_SESSION['user']=array('id'=>$result[0]['id'],'name'=>$result[0]['user_name']);
      return TRUE;
    }else{
      return FALSE;
    }
    
  }
  
  /**
   * 判断本地用户是否已登录
   */
  function checkLogin()
  {
    return empty($_SESSION['user'])?FALSE:$_SESSION['user']['id'];
  }
  
  /**
   * 检测是否已绑定
   */
  function checkBinded()
  {
    $user=DengLuTong::getUser();
    $sql="select user_id from ".$this->dbconfig['tablename']." where dlt_user_id='{$user['id']}' and vendor='{$user['vendor']}' limit 1";
    $result=$this->db->select($sql);
    return $result?$result[0]['user_id']:FALSE;
  }
  
  /**
   * 绑定本地用户
   */
  function bind()
  {
    $user=$this->checkLogin();
    $uid=$this->checkBinded();
    //本地用户已绑定
    if($uid)
    {
      if($this->localLogin($uid))
      {
        $this->success();
      }else{
        $this->error();
      }
    }else{
      //本地用户未绑定
      if($user)
      {
        //本地用户已登录，进行绑定处理
        $this->processBind($user);
      }else{
        //未登录，前往登录页面，登录完成后再次转向绑定页面
        $this->gotoLogin();
      }
    }
  }
  
  function processBind($uid)
  {
    if(!$uid)return FALSE;
    $user=DengLuTong::getUser();
    $user['dlt_user_id']=$user['id'];    
    unset($user['id']);
    $user['user_id']=$uid;
    $user['keys']=serialize(DengLuTong::getKeys());
    
    $user['name'] = addslashes(trim($user['name']));
    $user['screen_name'] = addslashes(trim($user['screen_name']));
    $user['desc'] = addslashes(trim($user['desc']));
    $lastid=$this->db->insert($this->dbconfig['tablename'],$user);
    if($lastid)
    {
      DengLuTong::clearSession();
      $this->success();
    }else{
      $this->error();
    }
  }
  
  
  function updateBind()
  {
    
  }
  
  function save()
  {
    //本地注册流程，返回用户ID
    $uid=$this->localRegister();
    $this->processBind($uid);
  }
  
  function gotoLogin()
  {
    header('Location: login.php');
  }
  
  
  
  function gotoRegister()
  {
    //header('Location: ');
    die;
  }
  
  function success()
  {
    header('Location: ./');die;
  }
  
  function error()
  {
    echo 'error';
  }
  
  function getBinded($uid,$vendor='')
  {
    $sql="select * from ".$this->dbconfig['tablename']." where user_id='{$uid}'".($vendor?" and vendor='{$vendor}'":'');
    $result=$this->db->select($sql);
    return $result;
  }
  
  function unbind($uid='',$vendor='')
  {
    empty($uid)?$uid=$_SESSION['user']['id']:'';
    if(empty($uid))return FALSE;
    $sql="delete from ".$this->dbconfig['tablename']." where user_id='{$uid}'".($vendor?" and vendor='{$vendor}'":'');
    $result=$this->db->execute($sql);
    return $result;    
  }
  

  
  
}