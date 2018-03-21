<?php
session_start();
require("configurl.php");
if(in_array($_SESSION["priv_session"],array("0","3"))==true  || substr($_SESSION["AKSES"],0,1)!="1"){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}
require_once("conf.php");
require('dbconndb.php');
$conn = &$connDB;
$conn->connect();
$mod = $_POST['mod'];
if($mod== "kpbc"){
	$kode = $_POST['kode'];	
	$sql = "select KDKPBC, URKDKPBC FROM TBLKPBC where kdkpbc ='$kode'";
	$data = $conn->query($sql);
	$str = "";
	if($data->next()){
		$str = $data->get('URKDKPBC');
	}	
	echo $str;
	
}elseif($mod== "jpn"){
	$kode = $_POST['kode'];	
	$where  = array("01"=>"IMPOR=1","02"=>"EKSPOR=1","03"=>"CUKAI=1","04"=>"TERTENTU=1");
	$sql = "select KDDOK, NAMADOK FROM KODE_PEMBAYARAN where ".$where[$kode];
	$data = $conn->query($sql);
	$str = "<option value='' disabled selected>Pilih Dokumen Dasar Pembayaran</option>";
	while($data->next()){
		$str .= "<option value='".$data->get('KDDOK')."'>".$data->get('KDDOK')." - ".$data->get('NAMADOK')."</option>";
	}	
	echo $str;
	
}elseif($mod == "nomor"){
	$kode = $_POST['kode'];
	switch($kode) {
		#spkbm
		case (6)  :
		case (11) :
		case (12) :
		case (22) :
		case (24) :
		case (25) :
		case (26) :
		case (28) :
		case (29) :
		case (30) :
		case (33) :
		case (34) :
		case (35) :
		case (37) :
		case (38) :
		case (39) :
		case (42) :
		case (43) :
		case (10) :
		case (45) :
		case (46) :
		case (7) :
		case (47) :
		case (48) :
		case (49) :
		case (51) :
		case (52) :
		case (53) :
		case (54) :
		case (55) :
		case (56) :
		case (57) :
		case (58) : echo "spkbm";break;
		#spkbm cicilan
		case (9) :
		case (23) :
		case (27) :
		case (32) :
		case (36) :
		case (40) :
		case (44) :
		case (50) :  echo "cicilan";break;	
		#car
		case (13) :
		case (14) :
		case (1) :
		case (15) :
		case (4) :
		case (2) :
		case (16) :
		case (8) :
		case (3) :
		case (5) :
		case (17) :
		case (18) :
		case (21) :
		case (31) :
		case (41) :
		case (19) :
		case (20) :
		case (6) :
		case (50) :  echo "car";break;	
	}

}elseif($mod== "npwp"){
	$npwp = $_POST['kode'];		
	$sql = "select NAMA, ADDRESS, CITY,ZIPCODE FROM TCOMPANY where ID='".$_SESSION['ID']."' AND NPWP='".$npwp."'";
	$data = $conn->query($sql);
	$arr = array("nama"=>"","address"=>"","city"=>"","zipcode"=>"");
	if($data->next()){
		$arr = array("nama"=>$data->get("ADDRESS"),"address"=>$data->get("ADDRESS"),"city"=>$data->get("CITY"),"zipcode"=>$data->get("ZIPCODE"));
	}	
	echo json_encode($arr);

}
?>