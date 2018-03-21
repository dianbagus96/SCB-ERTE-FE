<?php
require_once("ResultSet.php");

/**
 * this class is an abstact class that define the common
 * database handling method, it doesn't implements any
 * specific database connection handling.
 *
 * author : gusto(watonist@telkom.net)
 *
 * db connection URL : db:dbtype://user:password@serverInstance
 */

class DBAccess{
  var $_host = "";
  var $_isSetHost = false;
  var $_port = 0;
  var $_isSetPort = false;
  var $_user = "";
  var $_isSetUser = false;
  var $_passwd = "";
  var $_isSetPassword = false;

  var $dbType = "";
  var $serverInstance = "";

  var $isConnect = false;
  var $connection;

  function DBAccess(){
  }

  function setConnectionString($url){
    $this->parseURL($url);
  }

  function parseURL($url){
    $_a = parse_url($url);
    list($Scheme, $this->dbType) = explode(".", $_a["scheme"]);

    if(strtolower($Scheme) != "db") return false;

    $this->setHost($_a["host"]);
    $this->setUser($_a["user"]);
    $this->setPassword($_a["pass"]);
    @ $this->serverInstance = $_a["path"];
  }

  function setHost($host){
    $sVal = explode(":", $host);
    $sCount = count($sVal);

    if($sCount >= 1) $this->_host = $sVal[0];
    if($sCount >= 2) $this->_port = $sVal[1];
  }

  function getHost(){
    return $this->_host;
  }

  function setPort($port){
    $this->_port = $port;
  }

  function getPort(){
    return $this->_port;
  }

  function setUser($user){
    $this->_user = $user;
  }

  function getUser(){
    return $this->_user;
  }

  function setPassword($password){
    $this->_passwd = $password;
  }

  function getPassword(){
    return $this->_passwd;
  }

  function connect(){
  }

  function disconnect(){
  }

  function execute($SQLCmd){
  }

  function query($SQLCmd){
  }

  function autoCommit(){
  }

  function noAutoCommit(){
  }

  function commit(){
  }

  function rollback(){
  }
}
?>