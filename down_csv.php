<?php
session_start();
require_once("configurl.php");
if($_SESSION['verified']==""){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}elseif(time()>$_SESSION['timeout_session']){	
	echo "<script> window.location.href='".base_url."err/7/".md5(7)."';</script>";exit;
}else{
	$_SESSION['timeout_session'] = TIMEOUT;
}

require_once("dbconn.php");
if($_POST["cari"]){	
	$namaFile = "report.xls";
	function xlsBOF() {
		echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
		return;
	}
	function xlsEOF() {
		echo pack("ss", 0x0A, 0x00);
		return;
	}
	function xlsWriteNumber($Row, $Col, $Value) {
		echo pack("sssss", 0x203, 14, $Row, $Col, 0x0);
		echo pack("d", $Value);
		return;
	}
	function xlsWriteLabel($Row, $Col, $Value ) {
		$L = strlen($Value);
		echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
		echo $Value;
		return;
	}
	
	function createHeader($arr){
		$x=0;
		foreach($arr as $ar){
			xlsWriteLabel(0,$x,$ar);               		
			$x++;
		}
	}
	function simple_decrypt($text)
	{
	$salt ='e-RTE SCB by EDI INDONESI @ ';
	return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
	}	
	$conn->connect();
	$sql = str_replace("\\","",$_POST['cari']);
	$sql = simple_decrypt($sql);
	$data = $conn->query($sql);	
	
	if($_POST['for']=='user'){
		$berkas="User";
		$filename = "DATA_CUST". date("MY") .".xls";
		$hasil ="NPWP;NAMA GROUP;GROUP ID;USER LOGIN;EMAIL;NAMA LOGIN;PRIVILAGE";
		$field = explode(",","NPWP,NAMA,ID,USERLOGIN,EMAIL,FULLNAME,USERPRIV");				
	}elseif($_POST['for']=='auditTrail'){
		$berkas="Audit Trail";
		$filename = "AUDIT_TRAIL". date("MY") .".xls";
		$hasil ="USER ID;NAMA;PAGE VISIT;WAKTU;AKTIVITAS;IP ADDRESS";
		$field = explode(",","USERID,NAMA,PAGEVISIT,WAKTU,AKTIVITAS,IPADDRESS");
	}elseif($_POST['for']=='auditLogTrail'){
		$berkas="Audit Log Trail";
		$filename = "AUDIT_LOG_TRAIL". date("MY") .".xls";
		$hasil ="TRANS;TANGGAL;USERNAME;AKTIVITAS";
		$field = explode(",","IDTRANS,TANGGAL,USERNAME,ACTIVITY");
	}elseif($_POST['for']=='rekening'){
		$berkas="Data NPWP dan Rekening";
		$filename = "Data_Rekening". date("MY") .".xls";
		$hasil ="NPWP;REKENING";
		$field = explode(",","NPWP,ACCOUNT");
	}
	
	
	$aktivitas = "Melakukan download daftar ".$berkas."";
	audit($conn,$aktivitas);
	
		
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");
	header("Content-Disposition: attachment; filename=".$filename."");
	header("Content-Transfer-Encoding: binary ");
	xlsBOF();
	$hasil = explode(";",$hasil);
	createHeader($hasil);	
	$noBarisCell = 1;	
	while ($data->next())
	{
	   $x=0;
	   foreach($field as $f){
			xlsWriteLabel($noBarisCell,$x,$data->get($f));	   
			$x++;
	   }
	   $noBarisCell++;	   
	}		
	xlsEOF();
	exit();
}
?>