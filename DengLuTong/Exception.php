<?php
namespace DengLuTong;
class Exception extends \Exception
{
  function __construct($message='', $code='', $previous='')
  {
    echo '<div style="
    																	MARGIN-RIGHT: auto; 
    																	MARGIN-LEFT: auto;
    																	text-align:center;
    																	width:550px;
    																	height:100px;
    																	line-height:30px;
    																	padding:20px;
    																	border:1px solid #ff0000;">'.__NAMESPACE__.' : '.$message.'</div>';
    die;
  }
}

