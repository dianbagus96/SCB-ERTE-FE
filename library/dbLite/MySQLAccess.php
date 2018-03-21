<?php
require_once("DBAccess.php");

/**
 * author gusto(watonist@telkom.net)
 */
class MySQLAccess extends DBAccess{
  var $_dbName = "";

  function MySQLAccess(){
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
    if(strtoupper($this->dbType) != "MYSQL") return false;
    if($this->isConnect) return true;

    $hostStr = "";
    if(parent::getHost() != ""){
      $hostStr.= parent::getHost();
    }else{
      return false;
    }

    if(parent::getPort() != 0){
      $hostStr.= ":".parent::getPort();
    }

    $this->connection = mysql_connect($hostStr, parent::getUser(), parent::getPassword(), true);

    if(!$this->connection){
      $this->isConnect = false;
    }else{
      $db_selected = mysql_select_db($this->_dbName, $this->connection);
      if (!$db_selected) {
        $this->isConnect = false;
        $this->disconnect();
      }else{
        $this->isConnect = true;
      }
    }

    return $this->isConnect;
  }

  function disconnect(){
    if(!$this->isConnect) return true;

    mysql_close($this->connection);
    $this->isConnect = false;
    return true;
  }

  function execute($SQLCmd){
    if(!$this->isConnect) return false;

    $stat = mysql_query($SQLCmd);
    if (!$stat) {
      return false;
    }

    if($this->_autoCommit){
      $this->commit();
    }

    return true;
  }

  function query($SQLCmd){
    if(!$this->isConnect) return;

    $cursor = mysql_query($SQLCmd, $this->connection);
    if (!$cursor) return;

    $results = array();
    $resultName = array();

    for($i=0;$i<mysql_num_fields($cursor);$i++){
      $resultName[$i] = mysql_field_name($cursor, $i);
    }

    $cnt=0;
    while($row = mysql_fetch_assoc($cursor)){
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
    return mysql_query("commit", $this->connection);
  }

  function rollback(){
    if(!$this->isConnect) return false;
    return mysql_query("rollback", $this->connection);
  }
}
?>