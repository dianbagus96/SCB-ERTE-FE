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

$cbx = $_REQUEST['cbx'];
if(is_array($cbx)){
	require_once("dbconn.php");
	$conn->connect();    
	
	$aktivitas = "Mencetak sejumlah ".count($cbx)." Dana Masuk";		
	audit($conn,$aktivitas); 
	$conn->disconnect();
	
	require_once("library/dbLite/DBManager.php");
	require_once("dbconndb.php");
	$conn =& $connDB;
	$conn->connect();
	$div = $_GET['div'];

	if($div=='terlaporkan'){
		$idDm = implode(",",$cbx);
		$sql = "select TO_CHAR(a.tgl_transaksi,'DD-MM-YYYY HH24:MI:SS') AS TGLTRANSAKSI, a.VALUTA_TRANSFER,
			TO_CHAR(a.nominal_transfer,'999,999,999,999') AS NOMINAL_TRANSFER,
			a.VALUTA_DITERIMA,
			TO_CHAR(a.nominal_diterima,'999,999,999,999') AS NOMINAL_DITERIMA,			
			a.NAMA_PENGIRIM, a.NAMA_BANK_PENGIRIM,a.BERITA, a.NOREK, a.REFERENCE_NUMBER,
			b.URAIAN_DANA
			from tbldmdanamasuk a inner join tbldmdana b on a.kd_dana=b.kd_dana
			where idDanamasuk in (".$idDm.")";			
	}else{
		for($i=0;$i<count($cbx);$i++){
		$dt = explode(';',$cbx[$i]);
		$idDma[] = "'".$dt[0]."'";
		}	
		$idDm = implode(",",$idDma);
		
		$sql = "select TO_CHAR(a.tgl_transaksi,'DD-MM-YYYY HH24:MI:SS') AS TGLTRANSAKSI, a.VALUTA_TRANSFER,
			TO_CHAR(a.nominal_transfer,'999,999,999,999') AS NOMINAL_TRANSFER,
			a.VALUTA_DITERIMA,
			TO_CHAR(a.nominal_diterima,'999,999,999,999') AS NOMINAL_DITERIMA,			
			a.NAMA_PENGIRIM, a.NAMA_BANK_PENGIRIM,a.BERITA, a.NOREK, a.REFERENCE_NUMBER
			from tbldmdanamasuk a where idDanamasuk in (".$idDm.")";			
	}
	$dt = $conn->query($sql);	
	$no=0;			
	$str = "";
	//echo $sql;die();
	while($dt->next()){			
		$data = array();
		$data[] = $dt->get("TGLTRANSAKSI");
		$data[] = $dt->get("VALUTA_TRANSFER");
		$data[] = $dt->get("NOMINAL_TRANSFER");
		$data[] = $dt->get("VALUTA_DITERIMA");
		$data[] = $dt->get("NOMINAL_DITERIMA");
		$data[] = $dt->get("NAMA_PENGIRIM");	
		$data[] = strtoupper($dt->get("NAMA_BANK_PENGIRIM"));
		$data[] = $dt->get("BERITA");			
		if($div=="terlaporkan"){
			$data[] = $dt->get("URAIAN_DANA");						
		}
		$data[] = $dt->get("NOREK");
		$data[] = $dt->get("REFERENCE_NUMBER");						
		$no++;
		
		$str .=implode(";",$data).chr(10);
	}
	$connDB->disconnect();
	$filename = "DANAMASUK_".date("YmdHis").".txt";
	//$hd = fopen("files/".$filename,"a");
	//fwrite($hd,$head.$data);
	//fclose($hd);		
	//$path = "E:/Apache/htdocs/Mandiritaxes/files/".$filename;	
	#$path = "D:/Apache/htdocs/Mandiritaxes/files/".$filename;	#SERVER MANDIRI
	
	header("Content-Type: text");
	header('Content-Disposition: attachment; filename="'.$filename.'"');
	header("Pragma: no-cache");
	echo $str;
	//unlink($path);
	
}else{
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}

?>