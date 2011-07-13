<?php
use DengLuTong\Exception;
use DengLuTong\lib\Config;
use DengLuTong\DengLuTong;
require_once 'DengLuTong/DengLuTong.php';
//require_once 'DengLuTong/DLTConfig.php';

$act = empty( $_GET['dltact'] ) ? 'showBars' : $_GET['dltact'];

$vendor = empty( $_GET['vendor'] ) ? '' : 'Vendor_' .$_GET['vendor'];

$dlt = DengLuTong::getInstance( $vendor );
if(!method_exists($dlt, $act))
{
  throw new Exception('Method not exists.');
}
$return=$dlt->$act($vendor);
switch ($act)
{
  case 'callback':
    echo '<script>window.opener.location.href="'.Config::getInstance()->BaseUrl .'?dltact=bind";window.close();</script>';
    die;
  case 'unbind':
    $url=empty($_GET['url'])?'./':$_GET['url'];
    header('Location: '.$url);
    die;
}
