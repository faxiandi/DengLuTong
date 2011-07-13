<?php
namespace DengLuTong\lib\Db;
use DengLuTong\Exception;

class DbMysql extends Db
{
  private $config,$link;
  
  function __construct($config)
  {
    $this->config=$config;
    $this->connect();
  }
  
  function connect()
  {
    $this->link=mysql_connect( $this->config['dbhost'], $this->config['dbuser'], $this->config['dbpass']);
    if($this->link)
    {
      if(!mysql_select_db($this->config['dbname'], $this->link))
      {
        throw new Exception('DataBase not found');
      }
      mysql_query("SET NAMES '".$this->config['charset']."'",$this->link);
    }else {
      throw new Exception('DB connect error');
    }
  }
  
  function select($sql)
  {
    $this->queryID = mysql_query($sql, $this->link);
    $this->numRows = mysql_num_rows($this->queryID);
    $result = array();
    if($this->numRows >0) {
      while($row = mysql_fetch_assoc($this->queryID)){
        $result[]   =   $row;
      }
      mysql_data_seek($this->queryID,0);
    }
    return $result;    
  }
  
  function insert($table,$data)
  {
    $keys='';
    $values='';
    foreach ($data as $k=>$v) {
      $keys.='`'.$k.'`,';
      $values.="'".$v."',";
    }
    $keys=rtrim($keys,',');
    $values=rtrim($values,',');
    $sql="insert into {$table} ({$keys}) values ({$values})";
    $result =   mysql_query($sql, $this->link) ;
    return mysql_insert_id($this->link);
  }
  
  function execute($sql)
  {
    $result =   mysql_query($sql, $this->link) ;
    if($result)
    {
      return  mysql_affected_rows($this->link);
    }
    return FALSE;
  } 
  
  
  
}