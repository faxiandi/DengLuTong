<?php
require_once 'DengLuTong/DengLuTong.php';
use DengLuTong\DengLuTong;
use DengLuTong\lib\Db\Db;

if($_SERVER['REQUEST_METHOD']=='POST')
{
  $name=addslashes($_POST['name']);
  $pass=addslashes($_POST['pass']);
  $db=Db::getInstance();
  $sql="select * from user where user_name='{$name}' and pass='{$pass}'";
  $result=$db->select($sql);
  if(!$result)
  {
    echo '登录失败';
  }else{
    echo '已登录';
    $_SESSION['user']=array('id'=>$result[0]['id'],'name'=>$result[0]['user_name']);
    //如果第三方网站已登录，则进行绑定
    if(DengLuTong::getUser())
    {
      DengLuTong::bind();
    }
    header('Location: index.php');die();
  }
}
?>
<form method="post" action="">
name:<input type='text' name='name'>
pass:<input type='text' name='pass'>

<p></p>
<input type="submit" value="submit">
</form>
<?php 
if(!DengLuTong::getUser())
{
  DengLuTong::_showBars();
}else{
  $user=DengLuTong::getUser();
  echo 'Hi, '.$user['screen_name'].' ,如果已有本站帐号请登录，或注册。';
}
?>
<a href='reg.php'>reg</a> &nbsp;<a href='logout.php'>logout</a>