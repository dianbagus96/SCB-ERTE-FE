<?php
session_start();
if($_POST['div']=='npwp'){
	require_once("dbconn.php");
	$conn->connect();
	$npwp = $_REQUEST['npwp'];
	$sql = "select count(NPWP) as TOTAL from TCOMPANY where NPWP = '$npwp'";
	$data = $conn->query($sql);
	$data->next();	
	echo ($data->get('TOTAL')>0)? 1: 0;
}elseif($_POST['div']=='car'){
	require_once("dbconndb.php");
	$connDB->connect();
	$car = $_POST['aju'];
	$sql = "select count(CAR) as TOTAL from tbldmpeb where CAR='".trim($car)."'";
	$data = $connDB->query($sql);
	$data->next();	
	echo ($data->get('TOTAL')>0)? 1: 0;
}elseif($_POST['div']=='groupid'){
	require_once("dbconn.php");
	$conn->connect();
	$groupid = trim($_POST['groupid']);
	$sql = "select NAMA from TBLGROUP where GROUPID = '$groupid' AND npwp='".trim($_SESSION['npwp_session'])."'";	
	$data = $conn->query($sql);
	$data->next();	
	$arr = array('TOTAL'=>$data->size(),'NAMA'=>$data->get('NAMA'));
	exit(json_encode($arr));
}elseif($_POST['div']=='deptid'){
	require_once("dbconn.php");
	$conn->connect();
	$deptid = trim($_POST['deptid']);
	$deptid_old = trim($_POST['deptid_old']);
	$groupid = trim($_POST['groupid']);
	
	$sql = "select DEPTNAME from TBLDEPT where GROUPID = '$groupid' AND deptid = '$deptid' and corpid='".trim($_SESSION['ID'])."'";	
	$data = $conn->query($sql);
	$data->next();	
	$arr = array('TOTAL'=>$data->size(),'NAMA'=>$data->get('DEPTNAME'));
	exit(json_encode($arr));
}elseif($_POST['div']=='cancelMaker'){
	require_once("dbconn.php");
	$conn->connect();
	$maker = trim($_POST['maker']);
	$checker = trim($_POST['checker']);
	
	$sql = "UPDATE TBLUSER SET USERPRIV=NULL,AKSES='0000',CHECKER=NULL where ID='".trim($_SESSION['ID'])."' and USERLOGIN='".$maker."' and USERPRIV='1'";							
	$hasil = $conn->execute($sql);				
	if($hasil){
		$_SESSION['respon'] = "Pembatalan Maker Berhasil";
		$_SESSION['statusRespon']=1;
		
		$aktivitas = "Berhasil membatalkan Maker pada Checker : $checker, Corp ID : ".trim($_SESSION['ID']);	
		audit($conn,$aktivitas);				
	}	
}elseif($_POST['div']=='npwp_dp'){
	require_once("dbconn.php");
	$conn->connect();
	$npwp = $_REQUEST['npwp'];
	$sql = "select count(NPWP) as TOTAL from TPENYETOR where NPWP = '$npwp'";
	$data = $conn->query($sql);
	$data->next();	
	echo ($data->get('TOTAL')>0)? 1: 0;
}elseif($_POST['div']=='npwp_peb'){
	require_once("dbconn.php");
	$conn->connect();
	$npwp = $_REQUEST['npwp'];
	$sql = "select NAMA, ADDRESS from TCOMPANY where NPWP = '$npwp' AND ID = '".trim($_SESSION['ID'])."'";
	$data = $conn->query($sql);
	$data->next();	
	$arr = array('ADDRESS'=>$data->get('ADDRESS'),'NAMA'=>$data->get('NAMA'));
	exit(json_encode($arr));
}
?>