<?php
namespace DengLuTong\lib;


class Config
{
  private static $instance = NULL;
  private $prop = array ();
  
  private function __construct()
  {
  }
  
  private function __clone()
  {
  }
  
  static function getInstance()
  {
    if (! self::$instance)
    {
      self::$instance = new Config();
    }
    return self::$instance;
  }
  
  function __set($name, $value)
  {
    $this->prop[$name] = $value;
  }
  function __get($name)
  {
    return ! empty( $this->prop[$name] ) ? $this->prop[$name] : NULL;
  }
  function getAll()
  {
    return $this->prop;
  }
  static function getKeys($type,$name,$default=FALSE)
  {
    $values=Config::getInstance()->$type;
    if($values && !empty($values[$name]))
    {
      return $values[$name];
    }
    return $default;
  }
}
