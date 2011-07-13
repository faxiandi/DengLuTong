<?php
require_once 'DengLuTong/DengLuTong.php';
use DengLuTong\DengLuTong;
use DengLuTong\lib\Config;
if(empty($_SESSION['user']))
{
  header('Location: login.php');die;
}

DengLuTong::clearSession();
$user=$_SESSION['user'];
echo 'Hi, '.$user['name'].' Welcome.';
$dlt=DengLuTong::getInstance('','Local');
$binded=$dlt->getBinded($user['id']);
echo '<div>';
foreach ($binded as $bind)
{
  echo '<p>'.$bind['name'].':'.($bind['dlt_user']?($bind['dlt_user'].' <a href="DLTClient.php?dltact=unbind&vendor='.$bind['site'].'">解除绑定</a>'):'<a href="javascript:DLTWinOpen(\''.Config::getInstance()->LoginUrl.$bind['site'].'\')">绑定</a>').'</p>';
}
echo '</div>';
?>
<a href='logout.php'>logout</a>
<script>
function DLTWinOpen(url,id,iWidth,iHeight)
{
	var iTop = (screen.height-30-iHeight)/2; //获得窗口的垂直位置;
	var iLeft = (screen.width-10-iWidth)/2; //获得窗口的水平位置;
	//DLTWin=window.open(url,id,'height='+iHeight+',innerHeight='+iHeight+',width='+iWidth+',innerWidth='+iWidth+',top='+iTop+',left='+iLeft+',toolbar=no,menubar=no,scrollbars=auto,resizeable=no,location=no,status=no');
	//chrome不支持showModalDialog弹出模态窗口
	DLTWin=window.showModalDialog(url,null,"dialogWidth="+iWidth+"px;dialogHeight="+iHeight+"px;dialogTop="+iTop+"px;dialogLeft="+iLeft+"px");
	
}
function $(id)
{
	return document.getElementById(id);
}
function iFrameHeight(frame) {   
	var ifm= $(frame);
	var subWeb = document.frames ? document.frames[frame].document : ifm.contentDocument;   
	if(ifm != null && subWeb != null) {
	   ifm.height = subWeb.body.scrollHeight;
	}   
}   
</script>