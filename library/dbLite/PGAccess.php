<?php
require_once("DBAccess.php");

/**
 * author gusto(watonist@telkom.net)
 */
class PGAccess extends DBAccess{
  var $_dbName = "";

  function PGAccess(){
  }

  function setUser($user){
    parent::setUser($user);
    if($this->_dbName == ""){
      $this->setDBName($this->_user);
    }
  }

  function setDBName($name){
    $this->_dbName = $name;
  }

  function getDBName(){
    return $this->_dbName;
  }

  function parseURL($url){
    parent::parseURL($url);
    $this->_dbName = $this->serverInstance;
  }

  function connect(){
    if(strtoupper($this->dbType) != "POSTGRES") return false;
    if($this->isConnect) return true;

    $connectStr = "";
    if(parent::getHost() != ""){
      $connectStr.= "host=".parent::getHost()." ";
    }else{
      return false;
    }

    if(parent::getPort() != 0){
      $connectStr.= "port=".parent::getPort()." ";
    }

    if($this->_dbName != ""){
      $connectStr.= "dbname=".$this->_dbName." ";
    }else{
      return false;
    }

    if(parent::getUser() != ""){
      $connectStr.= "user=".parent::getUser()." ";
    }else{
      return false;
    }

    if(parent::getPassword() != ""){
      $connectStr.= "password=".parent::getPassword()." ";
    }

    $this->connection = pg_connect($connectStr);

    if(!$this->connection){
      $this->isConnect = false;
    }else{
      $this->isConnect = true;
    }

    return $this->isConnect;
  }

  function disconnect(){
    if(!$this->isConnect) return true;

    pg_close($this->connection);
    $this->isConnect = false;
    return true;
  }

  function execute($SQLCmd){
    if(!$this->isConnect) return false;

    $stat = pg_query ($this->connection, $SQLCmd);

    if($this->_autoCommit){
      $this->commit();
    }

    return $stat;
  }

  function query($SQLCmd){
    if(!$this->isConnect) return;

    $cursor = pg_query ($this->connection, $SQLCmd);
    $results = array();
    $resultName = array();

    for($i=0;$i<pg_num_fields($cursor);$i++){
      $resultName[$i] = pg_field_name($cursor, $i);
    }

    $cnt=0;
    while($row = pg_fetch_assoc($cursor)){
      for($i=0;$i<count($resultName);$i++){
        $results[$resultName[$i]][$cnt] = $row[$resultName[$i]];
      }
      $cnt++;
    }

    $rs = new ResultSet();
    $rs->setHolder($results);

    return $rs;
  }

  function autoCommit(){
    if(!$this->isConnect) return false;
    $this->_autoCommit = true;
    return true;
  }

  function noAutoCommit(){
    if(!$this->isConnect) return false;
    $this->_autoCommit = false;
    return true;
  }

  function commit(){
    if(!$this->isConnect) return false;
    return pg_query ($this->connection, "commit");
  }

  function rollback(){
    if(!$this->isConnect) return false;
    return pg_query ($this->connection, "rollback");
  }

}

?>