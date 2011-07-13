<?php
namespace DengLuTong\lib\Db;
use DengLuTong\Exception;

use DengLuTong\lib\Config;

class  Db
{
  private static $instance = NULL;
  private function __construct(){}
  private function __clone(){}
  static function getInstance()
  {
    if (! self::$instance)
    {
      self::$instance = self::factory();
    }
    
    return self::$instance;
  }
  
  static function factory()
  {
    $config=Config::getInstance()->DB;
    $class='DengLuTong\lib\Db\\Db'.$config['type'];
    if(class_exists($class)){
      $db=new $class($config);
      return $db;
    }else {
      throw new Exception($class.' not exists');
    }    
  }

  function connect(){}
  function select($sql){}
  function insert($table,$data){}
  function execute(){}
}