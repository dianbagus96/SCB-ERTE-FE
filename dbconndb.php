<?php
	PutEnv("TNS_ADMIN=/usr/lib/oracle/xe/app/oracle/product/10.2.0/server/network/admin"); 
	require_once("library/dbLite/DBManager.php");	
	$connDB = new ORA8Access();
	$connDB->parseURL("db.OCI8://rtescbprod:Rt3scbpr0d@IDXDB2");
	$connDB->connect();
?>
