<?php 
$dir=dirname(__FILE__).DIRECTORY_SEPARATOR;
echo file_get_contents($dir.'dltjs.js');
echo file_get_contents($dir.'dltcomm.css');
include_once($dir.'dltdefault.php');

?>

<div class="clearfloat DLT">
<div class="DLT_TITLE">合作网站登录(Powered by 登录通)</div>
<ul id="DLT_BARS"  class='clearfloat'>

<?php
foreach ($bars as $bar)
{
  echo '<li>';
  echo $bar;
  echo '</li>';
}
?>
</ul>
</div>

