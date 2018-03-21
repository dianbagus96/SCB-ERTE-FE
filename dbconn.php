<?php
	PutEnv("TNS_ADMIN=/usr/lib/oracle/xe/app/oracle/product/10.2.0/server/network/admin"); 
	require_once("library/dbLite/DBManager.php");	
	$conn = new ORA8Access();
	$conn->parseURL("db.OCI8://rtescbprod:Rt3scbpr0d@IDXDB2");
	$conn->connect();	
	function cleanStr($value){
		$value = stripslashes(strip_tags($value));
		$value = str_replace(array('delete','DELETE','rm -','!','|','?','=','`','"','\\\\','\\','//',';',':','*','>','<'),'', $value);	 
		  if(strpos($value,"''")===false){
			  $value = str_replace("'","''",$value); 
		  }else{
			  $value = str_replace("''","''",$value); 
		  }
		return trim($value);	
	}
	function writetxt($data,$name,$group){
			$Dokname 	= $name.$group.".".date('ymdHis').".FLT";	
			$file 		= '/home/RTESCB1/TOBACKEND/'.$Dokname;
			$filebac 	= '/home/RTESCB1/BEBACKUP/'.$Dokname;
			
			$filename 	= fopen($file, 'w');
			fputs($filename, $data);
			fclose($filename);
			
			$filenamebac 	= fopen($filebac, 'w');
			fputs($filenamebac, $data);
			fclose($filenamebac);
			
			return true;
	}
	function cekAktif($user,$group,$conn){
		$conn->connect();
		$user = trim($user);
		$group = trim($group);
		$q = "select LOGIN from TBLUSER where lower(USERLOGIN)='".strtolower($user)."'  and lower(ID)='".strtolower($group)."'";
		$d = $conn->query($q);
		$d->next();
		$nilai = $d->get('LOGIN')=='N'?0:1;
		return $nilai;
	}
	if(cekAktif(isset($_SESSION['uid_session'])!='0' && isset($_SESSION['uid_session']),isset($_SESSION['ID']),$conn)==0){ echo "<script> window.location.href='".base_url."log/out';</script>";exit;}
	
	$plus =1;
?>
