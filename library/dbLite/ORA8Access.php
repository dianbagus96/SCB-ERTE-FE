<?php
require_once("DBAccess.php");

/**
 *
 * db connection URL : db://user:password@OCI8/
 
 
if(!defined("SQL_LAYER"))
{

define("SQL_LAYER","oracle");
*/
class ORA8Access extends DBAccess{
  var $_sid = "";
  var $_autoCommit = false;

  function ORA8Access(){
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
    if(strtoupper($this->dbType) != "OCI8") return false;
    if($this->isConnect) return true;
    if($this->_sid == ""){
      $this->connection = OCILogon(parent::getUser(), parent::getPassword());
    }else{
      // $this->connection = OCILogon(parent::getUser(), parent::getPassword(), $this->getSID());
	  $this->connection = oci_connect(parent::getUser(), parent::getPassword(), $this->getSID());
    }

    if(!$this->connection){
      $this->isConnect = false;
    }else{
      $this->isConnect = true;
    }

    return $this->isConnect;
  }

  function disconnect(){
    if(!$this->isConnect) return true;

    OCILogoff($this->connection);
    $this->isConnect = false;
    return true;
  }

  function execute($SQLCmd){
    if(!$this->isConnect) return false;
    $stmt = OCIParse($this->connection, $SQLCmd);
    $stat = OCIExecute($stmt, OCI_DEFAULT);
    
    if($this->_autoCommit){
      $this->commit();
    }
      $this->commit();

    return $stat;
  }

  function query($SQLCmd){
    if(!$this->isConnect) return;

    $stmt = OCIParse($this->connection, $SQLCmd);
    $stat = OCIExecute($stmt, OCI_DEFAULT);

    $results = array();
    $nrows = OCIFetchStatement($stmt, $results, 0, -1, OCI_FETCHSTATEMENT_BY_COLUMN);
    
    $rs = new ResultSet();
    $rs->setHolder($results);

    OCIFreeStatement($stmt);

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
    return OCICommit($this->connection);
  }

  function rollback(){
    if(!$this->isConnect) return false;
    return OCIRollback($this->connection);
  }

}
//} // if ... define

?>