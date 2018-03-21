<?php
require_once("DBAccess.php");
require_once("ORAAccess.php");
require_once("ORA8Access.php");
require_once("PGAccess.php");
require_once("MySQLAccess.php");
require_once("MsSQLAccess.php");

/**
 * author gusto(watonist@telkom.net)
 */
class DBManager extends DBAccess{
  var $DB_UNKNOWN = 0;
  var $DB_ORACLE = 1;
  var $DB_ORACLE8 = 2;
  var $DB_POSTGRES = 3;
  var $DB_MYSQL = 4;

  var $_mode = 0;
  var $_tmpConn;

  function DBManager(){
    $nArgs = func_num_args();

    if($nArgs <= 0){
    }else if($nArgs == 1){
      $this->_mode = func_get_arg(0);
    }

    $this->init();
  }

  function setMode($mode){
    $this->_mode = func_get_arg(0);
  }

  function init(){
    if($this->_mode == $this->DB_UNKNOWN){
      $this->_tmpConn = new DBAccess();
    }else if($this->_mode == $this->DB_ORACLE){
      $this->_tmpConn = new ORAAccess();
    }else if($this->_mode == $this->DB_ORACLE8){
      $this->_tmpConn = new ORA8Access();
    }else if($this->_mode == $this->DB_POSTGRES){
      $this->_tmpConn = new PGAccess();
    }else if($this->_mode == $this->DB_MYSQL){
      $this->_tmpConn = new MySQLAccess();
    }
  }

  /**
   * ORACLE Connection only
   */
  function setSID($sid){
    if($this->_mode == $this->DB_ORACLE){
      return $this->_tmpConn->setSID($sid);
    }else if($this->_mode == $this->DB_ORACLE8){
      return $this->_tmpConn->setSID($sid);
    }
  }

  /**
   * PostgreSQL & MySQL connection only
   */
  function setDBName($name){
    if($this->_mode == $this->DB_POSTGRES){
      return $this->_tmpConn->setDBName($name);
    }else if($this->_mode == $this->DB_MYSQL){
      return $this->_tmpConn->setDBName($name);
    }
  }

  // common
  function parseURL($url){
    $this->_tmpConn->parseURL($url);
  }

  function connect(){
    $this->_tmpConn->connect();
    $this->isConnect = $this->_tmpConn->isConnect;
    return $this->isConnect;
  }

  function disconnect(){
    $this->_tmpConn->disconnect();
    $this->isConnect = $this->_tmpConn->isConnect;
  }

  function execute($SQLCmd){
    return $this->_tmpConn->execute($SQLCmd);
  }

  function query($SQLCmd){
    return $this->_tmpConn->query($SQLCmd);
  }

  function autoCommit(){
    return $this->_tmpConn->autoCommit();
  }

  function noAutoCommit(){
    return $this->_tmpConn->disconnect();
  }

  function commit(){
    return $this->_tmpConn->commit();
  }

  function rollback(){
    return $this->_tmpConn->rollback();
  }

}

$DB_MANAGER = new DBManager();

function strFilter($str){		
	$str = htmlspecialchars($str);
	$arr = array("=","--","&","*","(",")","+","!",'`','"','\\\\','\\','//','',':','>','<',"#","%","~","$");
	$str = str_replace($arr,"",$str);
	$str = (strpos($str,"'''")===false) ? str_replace("'","''",$str) : str_replace("'''","''",$str);	
	return trim($str);
}

function arrFilter($param){	
	$arr="";
	if(is_array($param)){
		$arr = array();
		foreach($param as $a=>$b){
			$arr[$a] = strFilter($b);
		}
	}
	return $arr;
}

function setSortir($default,$valid){
	$order = strFilter(strtolower($_GET['order']));
	$sort = strFilter(strtolower($_GET['sort']));
	$str; 
	if($sort!=""){
		$valid = array_map('strtolower', $valid);
		$_GET['sort'] = (!in_array($sort,$valid))? $default["sort"] : $sort;  	
	}
	if($order!=""){
		$_GET['order'] = (!in_array($order,array("asc","des")))? $default["order"] : $order;				
	}
	if($sort=="" && $order==""){
		$str = " Order by ".$default["sort"]." ".$default["order"];	
	}	
	return $str;
}



function insert($tbl,$arr,$tanpaKutip='',$conn,$string=0){
	$str = "insert into $tbl (";
	foreach($arr as $a=>$b){
		$str .= "$a,";
	}
	$str = substr($str,0,strlen($str)-1).") values (";
	foreach($arr as $a=>$val){
		if(is_array($tanpaKutip) && in_array($a,$tanpaKutip)){
			$str .= $val.",";
		}else{
			$str .= "'$val',";
		}
	}
	$str = substr($str,0,strlen($str)-1).")";
	return ($string==1)? $str : $conn->execute($str);
}

function update($tbl,$arrUpd,$arrWhere,$tanpaKutip='',$conn,$string=0){
	$str = "update $tbl set ";	
	foreach($arrUpd as $a=>$b){
		if(is_array($tanpaKutip) && in_array($a,$tanpaKutip)){
			$str .= "$a=$b,";
		}else{
			$str .= "$a='$b',";
		}	
	}
	$str  = substr($str,0,strlen($str)-1);
	if(is_array($arrWhere)){
		$str .=" where ";
		foreach($arrWhere as $a=>$b){
			if(is_array($tanpaKutip) && in_array($a,$tanpaKutip)){
				if(strpos(strtoupper($b),"NULL")!==false){
					$str .= $a." IS ".strtoupper(str_replace("is","",$b))." and";
				}else{
					$str .= " $a=$b and";
				}
			}else{
				$str .= " $a='$b' and";
			}
		}
		$str = substr($str,0,strlen($str)-3);
	}		
	return ($string==1)? $str : $conn->execute($str);
}

function delete($tbl,$arrWhere,$tanpaKutip='',$conn,$string=0){
	$str = "delete from $tbl where ";
	foreach($arrWhere as $a=>$b){
		if(is_array($tanpaKutip) && in_array($a,$tanpaKutip)){
			if(strpos(strtoupper($b),"NULL")!==false){
				$str .= $a." IS ".strtoupper(str_replace("is","",$b))." and";
			}else{
				$str .= " $a=$b and";
			}
		}else{
			$str .= " $a='$b' and";
		}	
	}
	$str = substr($str,0,strlen($str)-3);
	return ($string==1)? $str : $conn->execute($str);
}

function selectMax($tbl,$Field,$conn){
	$str = "select max(".$Field.") as MAX from $tbl";
	$data = $conn->query($str);
	$max = 0;
	if($data->next()){
		$max = $data->get("MAX");
	}
	return $max;
}


function get_where($tbl,$field,$where,$tanpaKutip="",$conn,$string=0){
	$str = "select ";
	foreach($field as $f){
		$str .= strtoupper($f).","; 	
	}
	$str  = substr($str,0,strlen($str)-1)." from $tbl where ";	
	foreach($where as $a=>$b){
		if(is_array($tanpaKutip) && in_array($a,$tanpaKutip)){				
			if(strpos(strtoupper($b),"NULL")!==false){
				$str .= $a." IS ".strtoupper(str_replace("is","",$b))." and ";
			}else{
				$str .= $a."=".$b." and ";
			}
		}else{
			$str .= $a."='".$b."' and ";		
		}		
	}
	$str = substr($str,0,strlen($str)-4);	
	$hasil = $conn->query($str);
	return ($string==1)? $str : $hasil;
}


function audit($conn,$aktivitas){			
	$pagevisit = $_SERVER["REQUEST_URI"];
	$ipview = $_SERVER['REMOTE_ADDR'];		
	$waktu = date("d/m/Y H:i:s");
	$userlogin = strFilter($_SESSION['uid_session']);
	$nama = strFilter($_SESSION['nmuser_session']);
	$id = trim($_SESSION["ID"]);
	
	$data = array("WAKTU"=>"SYSDATE","USERID"=>$userlogin,"NAMA"=>$nama,
					"AKTIVITAS"=>$aktivitas,"IPADDRESS"=>$ipview,"GROUPID"=>$id,"PAGEVISIT"=>$pagevisit);
	
	$result = insert("taudittrail",$data,array("WAKTU"),$conn);
	if(!$result){
		echo "<script>\n";		
		echo "$(document).ready(function(){\n";
			 echo "jAlert('Save to Audit Trail is Failed');\n";
		 echo "});\n";	
		echo "</script>\n";
	}else{
		return $result;	
	}
}

function logtrans($conn,$idtrans,$aktivitas){
	$tgl = date("d/m/Y H:i:s");
	$result = insert("tbllog",array("IDTRANS"=>$idtrans,"TGL"=>"CONVERT(DATETIME,'$tgl',105)",
					"USERNAME"=>$_SESSION['uid_session'],"ACTIVITY"=>$aktivitas),array("ID","TGL"),$conn);		
	if(!$result){
		echo "<script>\n";
			echo "$(document).ready(function(){\n";
				echo "jAlert('Save to Log Trail is Failed');\n";
			echo "});\n";
		echo "</script>\n";
	}else{
		return $result;	
	}
}	


function formatNPWP($str){
	return ($str)?substr($str,0,2).".".substr($str,2,3).".".substr($str,5,3).".".substr($str,8,1)."-".substr($str,9,3).".".substr($str,12,3) : "";	
}

?>