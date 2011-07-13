<?php
require_once 'DengLuTong/DengLuTong.php';

use DengLuTong\lib\Db\Db;
use DengLuTong\DengLuTong;
$DLTUser=DengLuTong::getUser();
$dlt=DengLuTong::getInstance();
if($_SERVER['REQUEST_METHOD']=='POST')
{
  $name=addslashes($_POST['name']);
  $pass=addslashes($_POST['pass']);
  $email=addslashes($_POST['email']);
  $db=Db::getInstance();
  $sql="select * from user where user_name='{$name}'";
  $result=$db->select($sql);
  if(!$result)
  {
    $data['user_name']=$name;
    $data['pass']=$pass;
    $data['email']=$email;
    if($DLTUser)
    {
      $data['pass']=rand(1,100);
    }
    $uid=$db->insert('user',$data);
    if($uid)
    {
      echo '注册成功,请登录';
      //如果第三方用户已登录，先进行本地登录，然后绑定
      if($DLTUser)
      {
        if($dlt->localLogin($uid))
        {
          $dlt->bind();
        }
      }
    }else{
      echo '注册失败';
    }
  }else{
    echo '已存在';
  }
}
?>
<form method="post" action="">
name:<input type='text' name='name' value='<?php echo $DLTUser?$DLTUser['screen_name']:''; ?>'>
email:<input type='text' name='email' value='<?php echo $DLTUser?$DLTUser['email']:''; ?>'>
<?php 
if(!$DLTUser){
?>
pass:<input type='text' name='pass'>
<?php 
}

?>
<p></p>
<input type="submit" value="submit">
</form>
<a href='login.php'>login</a> &nbsp;<a href='logout.php'>logout</a>