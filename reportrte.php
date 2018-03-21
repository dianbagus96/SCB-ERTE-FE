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
	
	$aktivitas = "Mencetak sejumlah ".count($cbx)." RTE";		
	audit($conn,$aktivitas); 
	$conn->disconnect();
	
	require_once("library/dbLite/DBManager.php");
	require_once("dbconndb.php");
	$conn =& $connDB;
	$conn->connect();
	
	$idRTE = "'".implode("','",$cbx)."'";		
	$sql = "select r.NO_IDENTIFIKASI, 
			CASE WHEN p.NPWP != NULL THEN
			CASE LENGTH(p.NPWP) WHEN 15 THEN (SUBSTR(p.NPWP,1,2)||'.'||SUBSTR(p.NPWP,3,3)||'.'||SUBSTR(p.NPWP,6,3)||'.'||SUBSTR(p.NPWP,9,1)||
			'-'||SUBSTR(p.NPWP,10,3)||'.'||SUBSTR(p.NPWP,13,3)) ELSE p.NPWP END
			ELSE 
				CASE LENGTH('".$_SESSION['npwp_session']."') WHEN 15 THEN (SUBSTR('".$_SESSION['npwp_session']."',1,2)||'.'||SUBSTR('".$_SESSION['npwp_session']."',3,3)||'.'||SUBSTR('".$_SESSION['npwp_session']."',6,3)||'.'||SUBSTR('".$_SESSION['npwp_session']."',9,1)||
				'-'||SUBSTR('".$_SESSION['npwp_session']."',10,3)||'.'||SUBSTR('".$_SESSION['npwp_session']."',13,3)) ELSE '".$_SESSION['npwp_session']."' END
			END AS NPWP,	
			CASE WHEN LENGTH(p.nama_eksportir) != 0 THEN p.nama_eksportir ELSE '".$_SESSION['nmcomp_session']."' END as NAMA_EKSPORTIR, 
			CASE WHEN LENGTH(p.KPBC) != 0 THEN p.KPBC ELSE 'NNNNNN' END as KPBC, 
			CASE WHEN LENGTH(p.no_PEB) != 0 THEN p.no_PEB ELSE 'NNNNNNNN' END as NO_PEB, 
			CASE WHEN LENGTH(p.tgl_PEB) != 0 THEN TO_CHAR(p.tgl_PEB,'YYYYMMDD') ELSE '00000000' END as TGL_PEB, 				
			r.VALUTA, d.nominal_transfer as DANAMASUK,
			r.nominal_DHE as DHE, r.nominal as FOB,r.Sandi_Keterangan as SANDI, r.KELENGKAPANDOK, r.KETERANGAN,
			d.REFERENCE_NUMBER, TO_CHAR(d.TGL_TRANSAKSI,'DD-MM-YYYY') as TGL_TRANSAKSI, d.VALUTA_DITERIMA 
			from tblfcRTE r left join tbldmdanamasuk d on r.iddanamasuk=d.iddanamasuk left join tbldmPEB p on r.idPEB=p.idPEB where r.idRTE in (".$idRTE.") Order By r.IDRTE DESC";				
	//echo $sql;
	$data = $conn->query($sql);	
	$i=0;		
	while($data->next()){
		$dt['IDENTIFIKASI'][$i] = $data->get("NO_IDENTIFIKASI");
		$dt['NPWP'][$i] = $data->get("NPWP");
		$dt['NAMA_EKSPORTIR'][$i] = $data->get("NAMA_EKSPORTIR");
		$dt['KPBC'][$i] = $data->get("KPBC");
		$dt['NO_PEB'][$i] = $data->get("NO_PEB");
		$dt['TGL_PEB'][$i] = $data->get("TGL_PEB");	
		$dt['VALUTA'][$i] = strtoupper($data->get("VALUTA"));
		$dt['DANAMASUK'][$i] = $data->get("DANAMASUK");
		$dt['DHE'][$i] = $data->get("DHE");
		$dt['FOB'][$i] = $data->get("FOB");
		$dt['SANDI'][$i] = $data->get("SANDI");	
		$dt['KELENGKAPANDOK'][$i] = $data->get("KELENGKAPANDOK");	
		$dt['KETERANGAN'][$i] = $data->get("KETERANGAN");	
		$dt['REFERENCE_NUMBER'][$i] = $data->get("REFERENCE_NUMBER");	
		$dt['TGL_TRANSAKSI'][$i] = $data->get("TGL_TRANSAKSI");	
		$dt['VALUTA_DITERIMA'][$i] = $data->get("VALUTA_DITERIMA");	
		$i++;
	}
	$connDB->disconnect();
	
	set_time_limit(10);
	
	require_once "library/excel/class.writeexcel_workbook.inc.php";
	require_once "library/excel/class.writeexcel_worksheet.inc.php";
	
	$fname = tempnam("/tmp", "RTE.xls");
	//$fname = "library/excel/test.xls";
	$workbook = &new writeexcel_workbook($fname);
	
	$worksheet1 =& $workbook->addworksheet('RTE');
	
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
	$worksheet1->insert_bitmap(0, 0, 'img/scb_logoxls.bmp', 4, 2, 1.15, 1);
	
	$baris =6;					 
	$worksheet1->write($baris, 0, 'No.', $header);
	$worksheet1->write($baris, 1, $bhs['No. Identifikasi'][$kdbhs], $header);
	$worksheet1->write($baris, 2,'NPWP', $header);
	$worksheet1->write($baris, 3, $bhs['Nama Penerima DHE'][$kdbhs], $header);
	$worksheet1->write($baris, 4, $bhs['Sandi Kantor Pabean'][$kdbhs], $header);
	$worksheet1->write($baris, 5, $bhs['No. PEB'][$kdbhs], $header);
	$worksheet1->write($baris, 6, $bhs['Tanggal PEB'][$kdbhs], $header);
	$worksheet1->write($baris, 7, $bhs['Valuta'][$kdbhs].' PEB', $header);
	$worksheet1->write($baris, 8, $bhs['Nilai PEB'][$kdbhs], $header);
	$worksheet1->write($baris, 9, $bhs['Valuta'][$kdbhs].' DHE', $header);
	$worksheet1->write($baris, 10, $bhs['Nilai DHE'][$kdbhs], $header);
	$worksheet1->write($baris, 11, $bhs['Sandi Keterangan'][$kdbhs], $header);
	$worksheet1->write($baris, 12, $bhs['Kelengkapan Dokumen'][$kdbhs], $header);
	$worksheet1->write($baris, 13, $bhs['No. Ref'][$kdbhs], $header);
	$worksheet1->write($baris, 14, $bhs['Tanggal Transaksi DHE'] [$kdbhs], $header);
	$baris++;
	
	for($i=0;$i<count($cbx);$i++){
		$a = $i+$baris;			
		$worksheet1->write($a, 0, ($i+1), $center);
		$worksheet1->write_string($a, 1, $dt['IDENTIFIKASI'][$i], $left);
		$worksheet1->write_string($a, 2, $dt['NPWP'][$i], $left);
		$worksheet1->write_string($a, 3, $dt['NAMA_EKSPORTIR'][$i], $left);
		$worksheet1->write_string($a, 4, $dt['KPBC'][$i], $center); 		
		$worksheet1->write_string($a, 5, $dt['NO_PEB'][$i], $center);
		$worksheet1->write_string($a, 6, $dt['TGL_PEB'][$i], $center);
		$worksheet1->write($a, 7, $dt['VALUTA'][$i], $center);			
		$worksheet1->write($a, 8, $dt['FOB'][$i], $right);
		$worksheet1->write($a, 9, $dt['VALUTA_DITERIMA'][$i], $center);			
		$worksheet1->write($a, 10, $dt['DHE'][$i], $right);
		$worksheet1->write_string($a, 11, $dt['SANDI'][$i], $center);
		$worksheet1->write($a, 12, $dt['KELENGKAPANDOK'][$i], $center);
		$worksheet1->write_string($a, 13, $dt['REFERENCE_NUMBER'][$i], $center);	
		$worksheet1->write_string($a, 14, $dt['TGL_TRANSAKSI'][$i], $center);	
	}	
	$workbook->close();
	header("Content-Type: application/x-msexcel; name=\"RTE_".date("YmdHis").".xls\"");
	header("Content-Disposition: inline; filename=\"RTE_".date("YmdHis").".xls\"");
	$fh=fopen($fname, "rb");
	fpassthru($fh);
	unlink($fname);
}else{
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}

?>