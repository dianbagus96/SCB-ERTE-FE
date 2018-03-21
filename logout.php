<?php
session_start();
require_once("configurl.php");
if($_SESSION['verified']==""){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}else{	
	require_once("dbconn.php");
	
	$aktivitas = "Telah Logout !";
	if(count($_SESSION)>0) audit($conn,$aktivitas);	
	
	function setNotAktif($conn){	
		$conn->connect();
		$user = trim($_SESSION['uid_session']);
		$group = trim($_SESSION['ID']);
		$q = "update TBLUSER set LOGIN='N' where USERLOGIN='$user' and ID='$group'";
		$conn->execute($q);	
		$conn->disconnect();
	}
	setNotAktif($conn);
	session_unregister("verified");
	session_unregister("npwp_session");
	session_unregister("uid_session");
	session_unregister("priv_session");
	session_unregister("nmcomp_session");
	session_unregister("email_session");
	session_unregister("nmuser_session");
	session_unregister("brachsCode");
	session_unregister("zipcode");
	session_unregister("NAMA_ses");
	session_unregister("ADDRESS_SES");
	session_unregister("CITY_ses");
	session_unregister("ID");
	session_unset();
	session_destroy();
	
	echo "<script> window.location.href='".base_url."';</script>";exit;
}
?>
