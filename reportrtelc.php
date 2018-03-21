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
	
	$aktivitas = "Mencetak sejumlah ".count($cbx)." RTELC";		
	audit($conn,$aktivitas); 
	$conn->disconnect();
	
	require_once("library/dbLite/DBManager.php");
	require_once("dbconndb.php");
	$conn =& $connDB;
	$conn->connect();
	
	$idRTELC = "'".implode("','",$cbx)."'";
	$sql = "select ID_LLD,NPWP,NAMA,PABEAN,NOPEB,TGLPEB,VALUTA,NILAIEKSPOR1,NILAIEKSPOR,KET,DOKU from tbldmrtelc where Id_LLD in (".$idRTELC.")";
	$data = $conn->query($sql);	
	$i=0;		
	while($data->next()){
		$dt['ID_LLD'][$i] = $data->get("ID_LLD");
		$dt['NPWP'][$i] = $data->get("NPWP");
		$dt['NAMA'][$i] = $data->get("NAMA");
		$dt['PABEAN'][$i] = $data->get("PABEAN");
		$dt['NOPEB'][$i] = $data->get("NOPEB");
		$dt['TGLPEB'][$i] = $data->get("TGLPEB");	
		$dt['VALUTA'][$i] = strtoupper($data->get("VALUTA"));
		$dt['NILAIEKSPOR1'][$i] = $data->get("NILAIEKSPOR1");
		$dt['NILAIEKSPOR'][$i] = $data->get("NILAIEKSPOR");
		$dt['KET'][$i] = $data->get("KET");
		$dt['DOKU'][$i] = $data->get("DOKU");		
		$i++;
	}
	$connDB->disconnect();
	
	set_time_limit(10);
	
	require_once "library/excel/class.writeexcel_workbook.inc.php";
	require_once "library/excel/class.writeexcel_worksheet.inc.php";
	
	$fname = tempnam("/tmp", "RTELC.xls");
	//$fname = "library/excel/test.xls";
	$workbook = &new writeexcel_workbook($fname);
	
	$worksheet1 =& $workbook->addworksheet('RTELC');
	
	# Frozen panes
	#$worksheet1->freeze_panes(1, 0); # 1 row
	#$worksheet1->protect("protectcan"); 
	
	#######################################################################
	#
	# Set up some formatting and text to highlight the panes
	#
	
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
	$worksheet1->insert_bitmap(0, 0, 'images/logo-edii.bmp', 4, 2, 1.15, 1);

	$baris =4;	
	$worksheet1->write($baris, 0, 'No', $header);
	$worksheet1->write($baris, 1, 'Nomor Identifikasi', $header);
	$worksheet1->write($baris, 2, 'NPWP', $header);
	$worksheet1->write($baris, 3, 'Nama Penerima DHE', $header);
	$worksheet1->write($baris, 4, 'Sandi Kantor Pabean', $header);
	$worksheet1->write($baris, 5, 'Nomor Pendaftaran PEB', $header);
	$worksheet1->write($baris, 6, 'Tanggal PEB', $header);
	$worksheet1->write($baris, 7, 'Jenis Valuta', $header);
	$worksheet1->write($baris, 8, 'Nilai DHE', $header);
	$worksheet1->write($baris, 9, 'Nilai PEB', $header);
	$worksheet1->write($baris, 10, 'Sandi Keterangan', $header);
	$worksheet1->write($baris, 11, 'Kelengkapan Dokumen', $header);
	$baris++;
	
	for($i=0;$i<count($cbx);$i++){
		$a = $i+$baris;
				
		$worksheet1->write($a, 0, ($i+1), $center);
		$worksheet1->write_string($a, 1, $dt['ID_LLD'][$i], $left);
		$worksheet1->write_string($a, 2, $dt['NPWP'][$i], $left);
		$worksheet1->write_string($a, 3, $dt['NAMA'][$i], $left);
		$worksheet1->write_string($a, 4, $dt['PABEAN'][$i], $center); 		
		$worksheet1->write_string($a, 5, $dt['NOPEB'][$i], $center);
		$worksheet1->write($a, 6, $dt['TGLPEB'][$i], $left);
		$worksheet1->write($a, 7, $dt['VALUTA'][$i], $center);
		$worksheet1->write($a, 8, $dt['NILAIEKSPOR1'][$i], $right);
		$worksheet1->write($a, 9, $dt['NILAIEKSPOR'][$i], $right);
		$worksheet1->write_string($a, 10, $dt['KET'][$i], $center);
		$worksheet1->write($a, 11, $dt['DOKU'][$i], $center);			
	}	
	$workbook->close();
	header("Content-Type: application/x-msexcel; name=\"RTELC_".date("YmdHis").".xls\"");
	header("Content-Disposition: inline; filename=\"RTELC_".date("YmdHis").".xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	unlink($fname);
}else{
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}

?>