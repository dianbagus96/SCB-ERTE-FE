<?php
require_once("DBAccess.php");

/**
 * author gusto(watonist@telkom.net)
 */
class MsSQLAccess extends DBAccess{
  var $_database = "";
  var $_host = "";
  var $_autoCommit = false;

  function MsSQLAccess(){
  }

  function parseURL($url){
    parent::parseURL($url);
	$this->_host = parent::getHost();
  }

  function setDB($database){
    $this->_database = $database;
  }

  function getDB(){
    return $this->_database;
  }

  function connect(){
    if(strtoupper($this->dbType) != "MSSQL") return false;
    if($this->isConnect) return true;
	
    $this->connection = mssql_connect($this->_host, parent::getUser(), parent::getPassword());
		mssql_select_db($this->_database, $this->connection);

    if(!$this->connection){
      $this->isConnect = false;
    }else{
      $this->isConnect = true;
    }

    return $this->isConnect;
  }

  function disconnect(){
    if(!$this->isConnect) return true;
		mssql_close($this->connection);
    $this->isConnect = false;
    return true;
  }

  function execute($SQLCmd){
    if(!$this->isConnect) return false;
		$stat = mssql_query($SQLCmd,$this->connection);
    if($this->_autoCommit){
      $this->commit();
    }
    return true;
  }

  function query($SQLCmd){
    if(!$this->isConnect) return;
    $results = array();
    $resultName = array();

		$recordset = mssql_query($SQLCmd,$this->connection);

		$count = 0;
		while($field = mssql_fetch_field($recordset)){
			$resultName[$count] = $field->name;
			$count++;
		}
		
    $cnt=0;
		while ($row = mssql_fetch_array($recordset)) {
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
    return false;
  }

  function commit(){
    if(!$this->isConnect) return false;
    return mssql_query("commit", $this->connection);
  }

  function rollback(){
    if(!$this->isConnect) return false;
    return mssql_query("rollback", $this->connection);
  }
}

?>