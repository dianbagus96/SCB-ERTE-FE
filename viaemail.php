<?php
session_start();  
require_once("configurl.php");
if(in_array($_SESSION["priv_session"],array("0","3"))==true  || substr($_SESSION["AKSES"],3,1)!="1"){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}elseif(time()>$_SESSION['timeout_session']){	
	echo "<script> window.location.href='".base_url."err/7/".md5(7)."';</script>";exit;
}else{
	$_SESSION['timeout_session'] = TIMEOUT;
}
require_once("dbconndb.php");
$connDB->connect();
$id = strFilter($_POST['id']);
$fileupload = strFilter($_POST['fileupload']);	
$for = strFilter($_POST['modul']);

if($for == "pebplus"){
	$fileupload = ($fileupload==1)? "fileupload='email'" : "fileupload=NULL";		
	$sql = "update tbldmpeb set ".$fileupload." where idpeb='".$id."'";
	$connDB->execute($sql);		
}elseif($for == "rte"){
	$fileupload = ($fileupload==1)? "fileupload='email'" : "fileupload=NULL";		
	$sql = "update tblfcRTE set ".$fileupload.", kelengkapanDok='0' where idRTE='".$id."'";
	$connDB->execute($sql);		
}	
?>

