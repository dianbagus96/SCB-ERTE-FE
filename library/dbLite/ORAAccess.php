<?php
require_once("DBAccess.php");

/**
 * author gusto(watonist@telkom.net)
 */
class ORAAccess extends DBAccess{
  var $_sid = "";
  var $_autoCommit = false;

  function ORAAccess(){
  }

  function parseURL($url){
    parent::parseURL($url);
    $this->_sid = parent::getHost();
  }

  function setSID($sid){
    $this->_sid = $sid;
  }

  function getSID(){
    return $this->_sid;
  }

  function connect(){
    if(strtoupper($this->dbType) != "ORACLE") return false;
    if($this->isConnect) return true;
    //echo "connect string = ".parent::getUser()."/".parent::getPassword()."@".$this->_sid."<br>";
    //$this->connection = ora_logon(parent::getUser()."@".$this->_sid, parent::getPassword());
	$this->connection = oci_connect(parent::getUser()."@".$this->_sid, parent::getPassword());
	
    if(!$this->connection){
      $this->isConnect = false;
    }else{
      $this->isConnect = true;
    }

    return $this->isConnect;
  }

  function disconnect(){
    if(!$this->isConnect) return true;
    ora_logoff($this->connection);
    $this->isConnect = false;
    return true;
  }

  function execute($SQLCmd){
    if(!$this->isConnect) return false;

    $cursor = ora_open($this->connection);
    $stmt = ora_parse ($cursor, $SQLCmd);
    $stat = ora_exec($cursor);

    if($this->_autoCommit){
      $this->commit();
    }

    ora_close($cursor);
    return $stat;
  }

  function query($SQLCmd){
    if(!$this->isConnect) return;

    $cursor = ora_open($this->connection);
    $stmt = ora_parse ($cursor, $SQLCmd);
    $stat = ora_exec($cursor);

    $results = array();
    $resultName = array();

    for($i=0;$i<ora_numcols($cursor);$i++){
      $resultName[$i] = ora_columnname($cursor, $i);
    }

    $cnt=0;
    while(ora_fetch($cursor)){
      for($i=0;$i<ora_numcols($cursor);$i++){
        $results[$resultName[$i]][$cnt] = ora_getcolumn($cursor, $i);
      }
      $cnt++;
    }

    $rs = new ResultSet();
    $rs->setHolder($results);

    ora_close($cursor);
    return $rs;
  }

  function autoCommit(){
    if(!$this->isConnect) return false;
    return ora_commiton($this->connection);
  }

  function noAutoCommit(){
    if(!$this->isConnect) return false;
    return ora_commitoff($this->connection);
  }

  function commit(){
    if(!$this->isConnect) return false;
    return ora_commit($this->connection);
  }

  function rollback(){
    if(!$this->isConnect) return false;
    return ora_rollback($this->connection);
  }
}

?>