<?php
	PutEnv("TNS_ADMIN=/usr/lib/oracle/xe/app/oracle/product/10.2.0/server/network/admin"); 
	require_once("library/tbLite/develop/htmltable.class.php");
	require_once("library/tbLite/develop/htmltable.classcolorrte.php");
	require_once("library/search/search.class.php");
	require_once("library/search/searchtbl.class.php");
	require_once("library/dbLite/DBManager.php");	
	$conn = new ORA8Access();
	$conn->parseURL("db.OCI8://rtescbprod:Rt3scbpr0d@IDXDB2");
	$conn->connect();	
?>
