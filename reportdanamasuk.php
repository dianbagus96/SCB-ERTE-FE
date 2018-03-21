<?php
session_start();
$bhs = $_SESSION['lang'];
$kdbhs = $_SESSION['bahasa_sess'];
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
	$idDm = "'".implode("','",$cbx)."'";	
	if($div=='terlaporkan'){
		$sql = "select TO_CHAR(a.tgl_transaksi,'DD-MM-YYYY HH24:MI:SS') AS TGLTRANSAKSI, a.VALUTA_TRANSFER,
			TO_CHAR(a.nominal_transfer,'999,999,999,999') AS NOMINAL_TRANSFER,
			a.VALUTA_DITERIMA,
			TO_CHAR(a.nominal_diterima,'999,999,999,999') AS NOMINAL_DITERIMA,			
			a.NAMA_PENGIRIM, a.NAMA_BANK_PENGIRIM,a.BERITA, a.NOREK, a.REFERENCE_NUMBER,
			b.URAIAN_DANA
			from tbldmdanamasuk a inner join tbldmdana b on a.kd_dana=b.kd_dana
			where idDanamasuk in (".$idDm.")";			
	}else{
		$sql = "select TO_CHAR(a.tgl_transaksi,'DD-MM-YYYY Hh24:MI:SS') AS TGLTRANSAKSI, a.VALUTA_TRANSFER,
			TO_CHAR(a.nominal_transfer,'999,999,999,999')  AS NOMINAL_TRANSFER,
			a.VALUTA_DITERIMA,
			TO_CHAR(a.nominal_diterima,'999,999,999,999') AS NOMINAL_DITERIMA,			
			a.NAMA_PENGIRIM, a.NAMA_BANK_PENGIRIM,a.BERITA, a.NOREK, a.REFERENCE_NUMBER
			from tbldmdanamasuk a where idDanamasuk in (".$idDm.")";			
	}
	$data = $conn->query($sql);	
	$i=0;		
	while($data->next()){
		$dt['TGLTRANSAKSI'][$i] = $data->get("TGLTRANSAKSI");
		$dt['VALUTA_TRANSFER'][$i] = $data->get("VALUTA_TRANSFER");
		$dt['NOMINAL_TRANSFER'][$i] = $data->get("NOMINAL_TRANSFER");
		$dt['VALUTA_DITERIMA'][$i] = $data->get("VALUTA_DITERIMA");
		$dt['NOMINAL_DITERIMA'][$i] = $data->get("NOMINAL_DITERIMA");
		$dt['NAMA_PENGIRIM'][$i] = $data->get("NAMA_PENGIRIM");	
		$dt['NAMA_BANK_PENGIRIM'][$i] = strtoupper($data->get("NAMA_BANK_PENGIRIM"));
		$dt['BERITA'][$i] = $data->get("BERITA");			
		$dt['NOREK'][$i] = $data->get("NOREK");
		$dt['REFERENCE_NUMBER'][$i] = $data->get("REFERENCE_NUMBER");			
		$dt['URAIAN_DANA'][$i] = $data->get("URAIAN_DANA");						
		$i++;
	}
	$connDB->disconnect();
	
	set_time_limit(10);
	
	require_once "library/excel/class.writeexcel_workbook.inc.php";
	require_once "library/excel/class.writeexcel_worksheet.inc.php";
	
	$fname = tempnam("/tmp", "DANAMASUK.xls");	
	$workbook = &new writeexcel_workbook($fname);
	
	$worksheet1 =& $workbook->addworksheet('DANAMASUK');
	
	$header =& $workbook->addformat();
	$header->set_color('white');
	$header->set_align('center');
	$header->set_align('vcenter');
	$header->set_pattern();
	$header->set_fg_color('grey');
	$header->set_text_wrap();
	
	$center =& $workbook->addformat();
	$center->set_align('center');
	
	$left =& $workbook->addformat();
	$left->set_align('left');
	$left->set_num_format('0');
	
	$right =& $workbook->addformat();
	$right->set_align('right');
	$right->set_num_format('0');
	
	$unlock =& $workbook->addformat();
	$unlock->set_align('left');
	$unlock->set_num_format('0');
	$unlock->set_locked(0);
	
	$lampirhdr=& $workbook->addformat();
	$lampirhdr->set_color('white');
	$lampirhdr->set_align('center');
	$lampirhdr->set_align('vcenter');
	$lampirhdr->set_pattern();
	$lampirhdr->set_fg_color('grey');
	$lampirhdr->set_text_wrap();
	$lampirhdr->setBorder('2');
	
	$lampirdtl=& $workbook->addformat();
	$lampirdtl->set_align('left');
	$lampirdtl->setBorder('1');
	
	$hiddenfield=& $workbook->addformat();
	$hiddenfield->set_hidden();
	
	$worksheet1->set_column('B:O', 20);
	$worksheet1->set_column('P:Q', 5,$left,1);
	$worksheet1->set_row(0, 30);
	$worksheet1->set_selection('C3');
	$worksheet1->insert_bitmap(0, 0, 'img/scb_logoxls.bmp', 4, 2, 1.15, 1);
	
	$baris =6;					 
	$worksheet1->write($baris, 0, 'No', $header);
	$worksheet1->write($baris, 1, $bhs['Tanggal Transaksi'][$kdbhs], $header);
	$worksheet1->write($baris, 2, $bhs['Valuta Transfer'][$kdbhs], $header);
	$worksheet1->write($baris, 3, $bhs['Nominal Transfer'][$kdbhs], $header);
	$worksheet1->write($baris, 4, $bhs['Valuta Diterima'][$kdbhs], $header);
	$worksheet1->write($baris, 5, $bhs['Nominal Diterima'][$kdbhs], $header);
	$worksheet1->write($baris, 6, $bhs['Nama Pengirim'][$kdbhs], $header);
	$worksheet1->write($baris, 7, $bhs['Nama Bank'][$kdbhs], $header);
	$worksheet1->write($baris, 8, $bhs['Berita'][$kdbhs], $header);
	$x=9;
	if($div=='terlaporkan'){			
		$worksheet1->write($baris, $x, $bhs['Status Dana'][$kdbhs], $header);			
		$x++;
	}		
	$jumRek = count(explode(",",$_SESSION['noRek']));		
	if($jumRek>1){			
		$worksheet1->write($baris, $x, $bhs['No. Rek'][$kdbhs], $header);
		$x++;
	}		
	$worksheet1->write($baris, $x, $bhs['No. Ref'][$kdbhs], $header);
	
	$baris++;
	for($i=0;$i<count($cbx);$i++){
		$a = $i+$baris;		
		$worksheet1->write($a, 0, ($i+1), $center);
		$worksheet1->write_string($a, 1, $dt['TGLTRANSAKSI'][$i], $left);
		$worksheet1->write_string($a, 2, $dt['VALUTA_TRANSFER'][$i], $left);
		$worksheet1->write_string($a, 3, $dt['NOMINAL_TRANSFER'][$i], $right);
		$worksheet1->write_string($a, 4, $dt['VALUTA_DITERIMA'][$i], $left); 		
		$worksheet1->write_string($a, 5, $dt['NOMINAL_DITERIMA'][$i], $right);
		$worksheet1->write_string($a, 6, $dt['NAMA_PENGIRIM'][$i], $left);
		$worksheet1->write($a, 7, $dt['NAMA_BANK_PENGIRIM'][$i], $left);
		$worksheet1->write($a, 8, $dt['BERITA'][$i], $left);
		$x=9;
		if($div=='terlaporkan'){							
			$worksheet1->write($a, $x, $dt['URAIAN_DANA'][$i], $left);			
			$x++;
		}				
		if($jumRek>1){				
			$worksheet1->write_string($a, $x, $dt['NOREK'][$i], $left);
			$x++;
		}
		$worksheet1->write_string($a, $x, $dt['REFERENCE_NUMBER'][$i], $left);
	}	
	$workbook->close();
	header("Pragma: ");
	header("Content-Type: application/x-msexcel; name=\"DANAMASUK_".date("YmdHis").".xls\"");
	header("Content-Disposition: inline; filename=\"DANAMASUK_".date("YmdHis").".xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	unlink($fname);
}else{
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}

?>