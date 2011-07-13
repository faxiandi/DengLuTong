<?php
namespace DengLuTong\lib\Local;
use DengLuTong\DengLuTong;

class DiscuzX2 extends Local
{
  
  function __construct()
  {
    //require_once  './source/class/class_core.php';
  }
  
  function save($uid)
  {
    if(!$uid)return FALSE;
    $user=DengLuTong::getUser();
    $user['dlt_user_id']=$user['id'];    
    $user['from']=$user['vendor'];
    unset($user['id'],$user['vendor']);
    $user['user_id']=$uid;
    $user['keys']=serialize(DengLuTong::getKeys());
    
    $user['name'] = addslashes(trim(dstripslashes($user['name'])));
    $user['desc'] = addslashes(trim(dstripslashes($user['desc'])));
    \DB::insert('denglutong_user',$user);
    DengLuTong::clearSession();
  }
  
  function gotoRegister()
  {
    header('Location: member.php?mod=register');die;
  }

}
 