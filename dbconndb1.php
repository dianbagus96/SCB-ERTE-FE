<?php
	PutEnv("TNS_ADMIN=/usr/lib/oracle/xe/app/oracle/product/10.2.0/server/network/admin"); 
//	PutEnv("TNS_ADMIN=/usr/lib/oracle/xe/app/oracle/product/10.2.0/server/lib");
//PutEnv("export ORACLE_BASE=/usr/lib/oracle/xe/app/oracle");
//PutEnv("export ORACLE_HOME=$ORACLE_BASE/product/10.2.0/server");
//PutEnv("export TNS_ADMIN=$ORACLE_BASE/product/10.2.0/server/network/admin");
//PutEnv("export PATH=$ORACLE_HOME/bin:$PATH");
//PutEnv("export LD_LIBRARY_PATH=$ORACLE_HOME/lib:/lib:/usr/lib:$LD_LIBRARY_PATH");
//PutEnv("export CLASSPATH=$ORACLE_HOME/JRE:$ORACLE_HOME/jlib:$ORACLE_HOME/rdbms/jlib");
//$dbstr1 ="(DESCRIPTION =(ADDRESS = (PROTOCOL = TCP)(HOST =10.1.2.4)(PORT = 1522))
//(CONNECT_DATA =
//(SERVER = DEDICATED)
//(SERVICE_NAME = idxdb2)
//(INSTANCE_NAME = idxdb2)))";
//PutEnv("TNS_ADMIN=/opt/lampp/etc");
	require_once("library/dbLite/DBManager.php");	
	$connDB = new ORA8Access();
	$connDB->parseURL("db.OCI8://rtescbprod:Rt3scbpr0d@IDXDB2");
	$connDB->connect();
?>
