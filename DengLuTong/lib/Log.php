<?php
namespace DengLuTong\lib;
class Log
{
  private static $instance = NULL;
 
  private function __construct(){}
  
  private function __clone(){}
  
  static function getInstance()
  {
    if (! self::$instance)
    {
      self::$instance = new Log();
    }
    return self::$instance;
  }
  
  
  function tofile($content,$filename='dltlog.txt')
  {
    $fp=fopen($filename, 'a');
    if($fp)
    {
      $result=fwrite($fp, "\r\n".date('Y-m-d H:i:s')." ".$content);
      fclose($fp);
      return $result;
    }
    return FALSE;
  }
  
  function clear($filename='dltlog.txt')
  {
    $fp=fopen($filename, 'w');
    if($fp)
    {
      fclose($fp);
      return TRUE;
    }
    return FALSE;
    
  }
  
  
}