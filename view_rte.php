<?php

if(in_array($_SESSION["priv_session"],array("5"))==false  || substr($_SESSION["AKSES"],3,1)!="1"){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}else{
set_time_limit(900);
require_once("conf.php");
require_once("dbconndb.php");
$cbx = $_POST['cbx'];

if(in_array($div,array("upload","rteuangmukaupload"))){		
	
	if(!is_array($cbx)){ echo "<script> window.location.href='".base_url."modul/rte/baru';</script>";exit;}				
	$sandiKet = $_POST['sandiRTE'];
	$namafile = $_FILES['uploadDok']['name'];
	$lokasifile = $_FILES['uploadDok']['tmp_name'];
	//$typefile = $_FILES['uploadDok']['type'];	
	//echo $typefile;
	$sizefile = $_FILES['uploadDok']['size'];
	$x=0;
	$errQuery=0;
	$errFormat=0;
	$errSize =0;
	$connDB->connect();  
	$nomor = array(); 
	$nomorFile = array();
	foreach($cbx as $a=>$b){ $nomor[] = $a;}		
	if(is_array($namafile)){ foreach($namafile as $a=>$b){ $nomorFile[]=$a;}}	
	$noupload=0;	
	foreach($cbx as $d){
		if(in_array($nomor[$x],$nomorFile)==1){
			$arrfile=array();
			$arrfile = explode(".",$namafile[$nomor[$x]]);
			//print_r($arrfile);
			$typefile = $arrfile[max(array_keys($arrfile))];
			//echo strtolower($typefile);exit;								
			if(strtolower($typefile)=='pdf'){
				if($sizefile[$nomor[$x]]<1000000){				
					$sqlnopeb = "select p.NO_PEB from tblfcRTE r inner join tbldmpeb p on p.idPEB=r.idPEB where idRTE='".$d."'";
					$datanopeb = $connDB->query($sqlnopeb);$datanopeb->next();
					if (strlen($datanopeb->get('NO_PEB'))<5){
					$nopebdok = rand(100000,999999);
					}else{
					$nopebdok = $datanopeb->get('NO_PEB');
					}
					$nmfile = $nopebdok."_".rand(1000,9999).".pdf";						
					$hasil = move_uploaded_file($lokasifile[$nomor[$x]],"files/".$nmfile);
					if($hasil){												
						$sql = "update tblfcRTE set fileupload='".$nmfile."', kelengkapanDok='1', Sandi_Keterangan='".$sandiKet[$nomor[$x]]."' where idRTE='".$d."'";
						$connDB->execute($sql);
					}else{
						$errQuery++;	
					}
				}else{
					$errSize++;
				}
			}else{
				$errFormat++;	
			}
			$noupload++;				
		}
		$x++;
	}	
	$connDB->disconnect();   	
	$err = $errFormat+$errQuery+$errSize;
	$ok = $noupload-$err;
	$msg1;$msg2;
	require_once("dbconn.php");		
	$conn->connect();    
	
	
	if($err>0){
		if($errFormat>0){ $msg1= "<li style='font-size:12px;margin-left:15px;'>Kesalahan Format Dokumen sebanyak $errFormat buah file, dokumen yang diizinkan adalah  dokumen PDF</li>"; }
		if($errSize>0){ $msg2= "<li style='font-size:12px;margin-left:15px;'>Ukuran File melebihi 1MB sebanyak $errSize buah file</li>"; }
		if($ok>0){
			$_SESSION['statusRespon'] = 0;
			$_SESSION['respon'] = "Penyimpanan Dokumen Berhasil Sebanyak $ok file dan gagal sebanyak $err file karena : $msg1 $msg2";		
			$aktivitas = "Mengupload sejumlah ".$ok." Dokumen RTE";	
			audit($conn,$aktivitas); 
		}else{
			$_SESSION['statusRespon'] = 0;
			$_SESSION['respon'] = "Penyimpanan Dokumen Gagal karena : $msg1 $msg2";		
		}
	}elseif(is_array($_FILES['uploadDok']['name'])){
		$_SESSION['statusRespon'] = 1;
		$_SESSION['respon'] = $bhs['Dokumen Simpan'][$kdbhs];
		$aktivitas = "Mengupload sejumlah ".count($namafile)." Dokumen RTE";	
		audit($conn,$aktivitas); 
	}else{
		$_SESSION['statusRespon'] = 0;
		$_SESSION['respon'] = $bhs['Dokumen tidak diupload'][$kdbhs];
	}											
	$conn->disconnect();  	
	$link = ($div=="upload")?"baru":"rteuangmuka";
	echo "<script> window.location.href='".base_url."modul/rte/".$link."';</script>";exit;	
}elseif(in_array($div,array("tosend"))){
	if(!is_array($cbx)){ echo "<script> window.location.href='".base_url."modul/rte/baru';</script>";exit;}				
	$connDB->connect();   
	$idRte = "'".implode("','",$cbx)."'";
	//print_r($_POST);die();
	if($div=="tosend"){
		$sql = "select r.IDRTE, r.FILEUPLOAD, r.KELENGKAPANDOK, r.SANDI_KETERANGAN, case when (((r.nominal_IDR-r.nominal_DHE_IDR)>r.nominal_IDR*10/100) or ((r.nominal_IDR-r.nominal_DHE_IDR)>(select nilai from tbldmselisih)) or (r.nominal_DHE=0) or ((r.Sandi_Keterangan  = '0220' or r.Sandi_Keterangan  = '0230') and LENGTH(p.no_PEB) != 0 ) or r.Sandi_Keterangan  = '0300') then '1' else '' 
				end as SELISIH from tblfcRTE r left join tbldmpeb p on r.idpeb = p.idpeb where r.idRTE in (".$idRte.")";	
	
		$fileupload = $connDB->query($sql);
		while($fileupload->next()){
			if(($fileupload->get("KELENGKAPANDOK")=='0') && ($fileupload->get("SELISIH")=='1' ) && (trim($fileupload->get("FILEUPLOAD"))=='' ) && (trim($fileupload->get("SANDI_KETERANGAN"))<>'0220' ) && (trim($fileupload->get("SANDI_KETERANGAN"))<>'0230' ) && (trim($fileupload->get("SANDI_KETERANGAN"))<>'0240' )){
					echo "<script type='text/javascript'> 
						$(document).ready(function(){
							jAlert('Terdapat Dokumen RTE Yang Tidak Lengkap ','',function(r){
								if(r==true) window.location.href='".base_url."modul/rte/baru';\n				
							});		
						})
					</script>";	
					exit;
			}else{
				
				if (trim($fileupload->get("FILEUPLOAD"))=='email'){
					$sts= '3';
				}else{
					$sts= '1';
				}
				
				$sql = "update tblfcRTE set status='".$sts."', tglSend=SYSDATE where idRTE in (".$fileupload->get("IDRTE").")";					
				$hasil = $connDB->execute($sql);
			}	
		}
		
		if($hasil){
			$sql = "SELECT CASE WHEN LENGTH(p.NO_PEB) != 0 THEN p.NO_PEB ELSE 'NNNNNN' END as NO_PEB, CASE WHEN LENGTH(p.TGL_PEB) != 0 THEN TO_CHAR(p.TGL_PEB,'DD-MM-YYYY') ELSE 'NNNNNNNN' END as TGL_PEB,
 					d.REFERENCE_NUMBER, CASE WHEN r.nominal != 0 THEN r.nominal ELSE 0 END AS NOMINAL, r.NOMINAL_IDR, r.VALUTA, r.SANDI_KETERANGAN, r.KELENGKAPANDOK,
					r.FILEUPLOAD,TO_CHAR(r.TGLSEND,'DD-MM-YYYY') AS TGLSEND, 'RTE'||r.IDRTE AS IDRTE, '".$_SESSION['npwp_session']."' as NPWP,
					r.NOMINAL_DHE, r.NOMINAL_DHE_IDR, TO_CHAR(d.TGL_TRANSAKSI,'DD-MM-YYYY') as TGL_TRANSAKSI,
					CASE WHEN LENGTH(p.KPBC) != 0 THEN p.KPBC ELSE 'NNNNNN' END as KPBC, p.NAMA_EKSPORTIR, r.USERINPUT,r.STATUS, p.VALUTA as VALUTA_PEB
					FROM TBLFCRTE r LEFT JOIN TBLDMPEB p ON r.IDPEB = p.IDPEB
					LEFT JOIN TBLDMDANAMASUK d ON r.IDDANAMASUK = d.IDDANAMASUK
					WHERE r.IDRTE IN (".$idRte.")";					
			$hasilRTE = $connDB->query($sql);
			while($hasilRTE->next()){
				$rt_peb = $hasilRTE->get("NO_PEB");  //ok
				$rt_dm = $hasilRTE->get("REFERENCE_NUMBER"); //ok
				$rt_nom = $hasilRTE->get("NOMINAL");  //ok
				$rt_nomidr = $hasilRTE->get("NOMINAL_IDR");
				$rt_valuta = $hasilRTE->get("VALUTA");  //ok
				$rt_code = $hasilRTE->get("SANDI_KETERANGAN");  //ok
				$rt_dok = $hasilRTE->get("KELENGKAPANDOK");  //ok
				$rt_file = $hasilRTE->get("FILEUPLOAD");
				$rt_tgl = $hasilRTE->get("TGLSEND");
				$rt_id = $hasilRTE->get("IDRTE");
				$rt_npwp = $hasilRTE->get("NPWP");  //ok
				$rt_dhe = $hasilRTE->get("NOMINAL_DHE");  //ok
				$rt_dheidr = $hasilRTE->get("NOMINAL_DHE_IDR");
				$rt_tgpeb = $hasilRTE->get("TGL_PEB"); //ok
				$rt_tgdm = $hasilRTE->get("TGL_TRANSAKSI"); //ok
				$rt_kpbc = $hasilRTE->get("KPBC"); //ok
				$rt_eksportir = $hasilRTE->get("NAMA_EKSPORTIR"); //ok
				$rt_entryby = $hasilRTE->get("USERINPUT"); //ok
				$rt_status = $hasilRTE->get("STATUS"); //ok
				$rt_valuta_peb = $hasilRTE->get("VALUTA_PEB"); //ok
			//$write_txt .= "RTE|".$rt_peb."|".$rt_dm."|".$rt_npwp."|".$rt_nom."|".$rt_nomidr."|".$rt_valuta."|".$rt_code."|".$rt_dok."|".$rt_file."|".$rt_tgl."|".$rt_dhe."|".$rt_dheidr."|".$rt_id."|".$rt_tgpeb."|".$rt_tgdm."|".$rt_kpbc."|".$rt_eksportir."|".$rt_entryby."|".$rt_status."\r\n";
			$write_txt .= "RTE|".$rt_peb."|".$rt_dm."|".$rt_npwp."|".$rt_nom."|".$rt_nomidr."|".$rt_valuta."|".$rt_code."|".$rt_dok."|".$rt_file."|".$rt_tgl."|".$rt_dhe."|".$rt_dheidr."|".$rt_id."|".$rt_tgpeb."|".$rt_tgdm."|".$rt_kpbc."|".$rt_eksportir."|".$rt_entryby."|".$rt_status."|".$rt_valuta_peb."\r\n";
			if($rt_dok =='1' && strlen($rt_file)!=0){
				//copy('files/'.$rt_file, '/home/RTESCB1/BEBACKUP/'.$rt_file);
				copy('files/'.$rt_file, '/home/RTESCB1/TOBACKEND/'.$rt_file);
			}
			}
			$fl_name = "DOKRTE." ;
			writetxt($write_txt,$fl_name,$_SESSION['grpID']);
		}
	}	
	
	$connDB->disconnect();   
	$_SESSION['respon'] = 'Pengiriman RTE ke Bank Berhasil';
	$_SESSION['statusRespon'] = 1;	
	//echo "<script> window.location.href='".base_url."modul/rte/baru';</script>";exit;	
}elseif($div=="batal"){
	if(!is_array($cbx)){ echo "<script> window.location.href='".base_url."modul/rte/baru';</script>";exit;}				
	$connDB->connect();   
	$idRte = "'".implode("','",$cbx)."'";
	$sql = " SELECT BATCH FROM TBLFCRTE WHERE idrte IN (".$idRte.") group by BATCH ";
	$btc = $connDB->query($sql);
	while($btc->next()){
		$dtbc[] = $btc->get("BATCH");	
	}
	$batch = "'".implode("','",$dtbc)."'";
	$sql = "SELECT idrte, sandi_keterangan,idpeb,iddanamasuk FROM tblfcrte WHERE BATCH in ($batch) ";
	$dt = $connDB->query($sql);
	while($dt->next()){
	$sts = $dt->get("sandi_keterangan");
	if ($sts != '0300'){	
	$idRte = $dt->get('IDRTE');
	$sql = "select r.IDRTE,r.IDPEB, r.IDDANAMASUK, d.FLAG_USED as DFLAG_USED,p.FLAG_USED as PFLAG_USED, 
			r.VALUTA,d.jns_uangmuka as JNS_UANGMUKA, r.STATUS, d.REFERENCE_NUMBER, d.NOREK  
			from tblfcRTE r left join tbldmdanamasuk d on d.IDDANAMASUK=r.IDDANAMASUK
			left join tbldmpeb p on r.IDPEB=p.IDPEB where r.idRTE in (".$idRte.") ";	
	$data = $connDB->query($sql);
	//die($sql);
	$idpeb = array();
	$iddm = array();
	$dflag_used = array();	
	$pflag_used = array();	
	$arrvaluta = array();
	$arruangmuka = array();
	while($data->next()){
		$del = "delete from tbldmdanamasuk where reference_number = '".$data->get("REFERENCE_NUMBER")."' and norek = '".$data->get("NOREK")."' and dana_sisa = '1' ";
		$del_ok = $connDB->execute($del);
		if ($data->get("STATUS") == 1){
			$write_txt = "DELRTE|RTE".$data->get("IDRTE");
			$fl_name = "DOKFRONT." ;
			writetxt($write_txt,$fl_name,$_SESSION['grpID']);
		}
		$idRte = $data->get('IDRTE');
		$idPEB = $data->get('IDPEB');
		$idDanaMasuk = $data->get('IDDANAMASUK');
		$dflag_used = $data->get('DFLAG_USED');
		$pflag_used = $data->get('PFLAG_USED');
		$valuta = trim($data->get('VALUTA'));
		$uangmuka = $data->get('JNS_UANGMUKA');
		
		#$sql = "select idRTE from tblfcRTE WHERE iddanamasuk ='' and idPEB='".$idPEB."'";
		$sql = "select idRTE from tblfcRTE WHERE idPEB='".$idPEB."'";			
		$tanpaDHE = $connDB->query($sql);
		if($tanpaDHE->next() && trim($idDanaMasuk)!=""){		
			$sql = "select count(idRTE) as JUMPEB from tblfcRTE WHERE iddanamasuk !='' and idPEB='".$idPEB."'";			
			$jum = $connDB->query($sql);$jum->next();
			if($jum->get('JUMPEB')>1){
				$sql = "update tbldmpeb set flag_used='2' where idpeb ='".$idPEB."'";									
			}else{
				$sql = "update tbldmpeb set flag_used='0' where idpeb ='".$idPEB."'";
			}
		}else{
			if(trim($idDanaMasuk)==""){ #ketika batal RTE dari peb90+ maka kembali ke peb90+
				$sql = "update tbldmpeb set flag_used='".($pflag_used=='2'?'3':($pflag_used=='4'?'4':'0'))."' where idpeb ='".$idPEB."'";
			}else{ #ketika batal RTE dari RTE uangmuka pilih peb
				$sql = "update tbldmpeb set flag_used='".($pflag_used=='2'?'1':($pflag_used=='4'?'4':'0'))."' where idpeb ='".$idPEB."'";
			}
			
		}

		$connDB->execute($sql);	
		
		$sql = "select idRTE from tblfcRTE WHERE (nominal = '0' and iddanamasuk='".$idDanaMasuk."') or (nominal != '0' and iddanamasuk='".$idDanaMasuk."')";			
		
		$tanpaPEB = $connDB->query($sql);		
		$tanpa = $tanpaPEB->size();
		if($tanpa>1 && $valuta!=""){
			$sql = "select count(idRTE) as JUMDHE from tblfcRTE WHERE iddanamasuk='".$idDanaMasuk."'";				
			$jum = $connDB->query($sql);$jum->next();								
			if(($jum->get('JUMDHE')-1)>=1){
				$sql = "update tbldmdanamasuk set flag_used='1', jns_uangmuka='2' where iddanamasuk='".$idDanaMasuk."'";	
			}else{					
				$sql = "update tbldmdanamasuk set flag_used='1' where iddanamasuk='".$idDanaMasuk."'";	
			}										
		}else{	
			$sql = "select FLAG_UANGSISA from tbldmdanamasuk where iddanamasuk='".$idDanaMasuk."'";			
			$norek = $connDB->query($sql);$norek->next();
			if($norek->get('FLAG_UANGSISA')!="1"){	
				$sql = "update tbldmdanamasuk set flag_used='0' where iddanamasuk='".$idDanaMasuk."'";	
			}else{
				$sql = "delete from tbldmdanamasuk where iddanamasuk='".$idDanaMasuk."'";	
			}	
			
		}
		$connDB->execute($sql);	
		if($tanpa>0 && $valuta==""){
			$sql = "delete from tblfcRTE where iddanamasuk='".$idDanaMasuk."'";
		}else{
			$sql = "delete from tblfcRTE where idRTE in (".$idRte.")";	
		}
		$connDB->execute($sql);	
		}	
	}else{
		$sql1 = "update tblfcRTE set status = 1 where iddanamasuk='".$dt->get("iddanamasuk")."' and idpeb = '".$dt->get("idpeb")."'";
		$connDB->execute($sql1);	
		$sql2 = "delete from tblfcRTE where idrte='".$dt->get("idrte")."'";
		$connDB->execute($sql2);
		
	}
	}
	$_SESSION['statusRespon'] = 1;
	$_SESSION['respon'] = ($div=="batal")? $bhs['RTE Batal'][$kdbhs]:$bhs['RTE Batal Uang Muka'][$kdbhs];			
	$connDB->disconnect();   
	require_once("dbconn.php");
	$conn->connect();    
	
	$aktivitas = "Membatalkan sejumlah ".count($cbx)." Dokumen RTE";	
	audit($conn,$aktivitas); 	
	$conn->disconnect();    
	$link = ($div=="batal")? "baru":"rteuangmuka";
	
	echo "<script> window.location.href='".base_url."modul/rte/".$link."';</script>";exit;
}elseif($div=="viaemail"){
	if(!is_array($cbx)){ echo "<script> window.location.href='".base_url."modul/rte/baru';</script>";exit;}				
	$connDB->connect();   
	$idRte = "'".implode("','",$cbx)."'";
	$sql = "update tblfcRTE set fileupload='email', kelengkapanDok='0' where idRTE in (".$idRte.")";	
	$hasil = $connDB->execute($sql);				
	if($hasil){
		$_SESSION['respon'] = "Tandai kirim Dokumen via Email Berhasil";
		$_SESSION['statusRespon'] = 1;			
		require_once("dbconn.php");
		$conn->connect();    
		
		$aktivitas = "Menandai sejumlah ".count($cbx)." dokumen RTE dikirim via Email";						
		audit($conn,$aktivitas); 
		$conn->disconnect(); 		
	}
	echo "<script> window.location.href='".base_url."modul/rte/baru';</script>";exit;
}elseif($div=="update"){
	$sandiRTE = $_POST['sandiRTE'];
	if(!is_array($sandiRTE)){ echo "<script> window.location.href='".base_url."modul/rte/baru';</script>";exit;}				
	$connDB->connect();   	
	$nomor = array(); 
	foreach($sandiRTE as $a=>$b){ $nomor[] = $a;}		
	$x=0;
	foreach($sandiRTE as $srte){
		$sql = "update tblfcRTE set Sandi_Keterangan='".$srte."' where idRTE='".$cbx[$nomor[$x]]."'";
		$connDB->execute($sql);
		$x++;
	}	
	echo "<script> window.location.href='".base_url."modul/rte/baru';</script>";exit;
}elseif($div=="rteuangmukabatal"){
	if(!is_array($cbx)){ echo "<script> window.location.href='".base_url."modul/rte/rteuangmuka';</script>";exit;}				
	$connDB->connect();   
	$idRte = "'".implode("','",$cbx)."'";
	//$sql = "select batch from tblfcrte where idrte in (".$idRte.") and status = 0";
	$sql = "SELECT idrte,idPEB,iddanamasuk,nominal_DHE,nominal,valuta,kurs,Sandi_keterangan,KelengkapanDok,Keterangan,userInput,userEdit,Status,tglSend,nominal_IDR,nominal_DHE_IDR,no_identifikasi FROM tblfcrte WHERE  idrte IN (".$idRte.")";
	$dt = $connDB->query($sql);
	while($dt->next()){
		$status = $dt->get("Status");
		//die($status);
		if ($status != 5) {
			$batch = $_SESSION['grpID'].date('dmyHis');
			$sql = "INSERT INTO tblfcRTE (idPEB,iddanamasuk,nominal_DHE,nominal,valuta,kurs,Sandi_keterangan,KelengkapanDok,Keterangan,userInput,userEdit,Status,
				tglSend,nominal_IDR,nominal_DHE_IDR,no_identifikasi,batch) 
				VALUES ('".$dt->get("idPEB")."','".$dt->get("iddanamasuk")."',".$dt->get("nominal_DHE").",".$dt->get("nominal").",'".$dt->get("valuta")."',".$dt->get("kurs").",'0300','0','".$dt->get("Keterangan")."','".strFilter(trim($_SESSION['uid_session']))."',
				'','0',NULL,".$dt->get("nominal_IDR").",".$dt->get("nominal_DHE_IDR").",'".$dt->get("no_identifikasi")."','".$batch."')";
			
			$hasil = $connDB->execute($sql);
			$sql = "update tblfcrte set status = '5' where idrte = '".$dt->get("idrte")."'";
			$hasil = $connDB->execute($sql);
		} else {
			echo "<script type='text/javascript'> 
						$(document).ready(function(){
							jAlert('RTE uang muka sudah dibatalkan sebelumnya','',function(r){
								if(r==true) window.location.href='".base_url."modul/rte/rteuangmuka';\n				
							});		
						})
					</script>";	
					exit;
		}	
	}	
	$_SESSION['statusRespon'] = 1;
	$_SESSION['respon'] = ($div=="batal")? $bhs['RTE Batal'][$kdbhs]:$bhs['RTE Batal Uang Muka'][$kdbhs];			
	$connDB->disconnect();   
	require_once("dbconn.php");
	$conn->connect();    
	
	$aktivitas = "Membatalkan sejumlah ".count($cbx)." Dokumen RTE";	
	audit($conn,$aktivitas); 	
	$conn->disconnect();    
	$link = ($div=="batal")? "baru":"rteuangmuka";
	
	echo "<script> window.location.href='".base_url."modul/rte/".$link."';</script>";exit;
}


$cari = str_replace('\\','',$_POST["q"]);
$_POST["q"] = strFilter($_POST["q"]);

?>
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="border-bottom:1px solid #D7D7D7;">
			<div style="padding-bottom:9px;">
			<span style="color:#1a68a4; font-size:16px; font-weight:bold;">
			<?php			
			if(!in_array($div,array("baru","terkirim","pending","rteuangmuka"))){ 
			echo "<script> window.location.href='".base_url."modul/rte/baru';</script>";exit;}
			$judul = array("baru"=>$bhs['RTE Baru'][$kdbhs],"terkirim"=>$bhs['RTE Terkirim'][$kdbhs],"pending"=>"RTE Pending","rteuangmuka"=>$bhs['RTE Uang Muka'][$kdbhs]);							
			$messageBox =($_SESSION['statusRespon']==0)? 
						"<div style='background:#FDE9DF;padding:5px;border:1px #CCC solid;color:#633'>
						 <img src='".base_url."img/warninglogo.png' style='border:none'> ".$_SESSION['respon']."</div>" : 
						 "<div style='background:#E5EEF5;padding:5px;border:1px #CCC solid;'>
						 <img src='".base_url."img/accept.png' style='border:none'> ".$_SESSION['respon']."</div>";
			 
			if($_SESSION['respon']!=""){
				echo $messageBox;
				$_SESSION['respon']="";
			}else{
				echo $judul[$div]."<br />";
			}
			?>							
			</span>
			</div>
		</td>
	</tr>
</table>
<div style="margin-top:18px; width: 100%; background:#F0F0F0; border: 1px solid #D7D7D7;">
	<div style="font-family:arial; font-weight:lighter; font-size:11px; padding-top:10px; padding-bottom:10px; padding-left:10px;">
		<table cellpadding="0" cellspacing="0" style="font-family:arial; font-weight:lighter; font-size:11px;">
			<tr>
				<td style="padding-left:20px; font-family:arial; font-size:11px; color:#333333; font-weight:bold;">
				<form name="frmCari" method="post" action="<?php echo base_url."modul/rte/".$div?>">				
				<table>
					<tr>
						<td><?php echo $bhs['Kategori'][$kdbhs]?></td>
						<td><select name="field" id="field" onChange="javascript:cekField(this.value);">
							<?php																
							$x1 = array("NO_IDENTIFIKASI","NPWP","NAMA_EKSPORTIR","KPBC","NO_PEB","TGL_PEB","r.VALUTA","NOMINAL_DHE","NOMINAL","SANDI_KETERANGAN",
										"KELENGKAPANDOK","d.REFERENCE_NUMBER");
							$x2 = array($bhs['No. Identifikasi'][$kdbhs],'NPWP',$bhs['Nama Penerima DHE'][$kdbhs],$bhs['Sandi Kantor Pabean'][$kdbhs],$bhs['No. PEB'][$kdbhs],
									$bhs['Tanggal PEB'][$kdbhs],$bhs['Valuta'][$kdbhs],$bhs['Nilai DHE'][$kdbhs],$bhs['Nilai PEB'][$kdbhs],$bhs['Sandi Keterangan'][$kdbhs],
									$bhs['Kelengkapan Dokumen'][$kdbhs],$bhs['No. Ref'][$kdbhs]);
							
							if($div=="terkirim"||$div=="pending"){
								array_push($x1,"TGL_SEND");
								array_push($x2,$bhs['Tgl Pelaporan'][$kdbhs]);							
							}
							for($i=0;$i<count($x1);$i++){
								if($_POST['field']==$x1[$i]){
									echo("<option value=\"$x1[$i]\" selected>$x2[$i]</option>");
								} else {
									echo("<option value=\"$x1[$i]\">$x2[$i]</option>");
								}
							}
							?>
						  </select>
						</td>
					</tr>
					<tr>
						<td><?php echo $bhs['Cari'][$kdbhs]?></td>
						<td><input name="q" type="text" id="q" size="50" value="<?php echo($cari);?>"  >
							<input name="tg1" type="text" id="tg1" size="10" value="<?php echo($_POST['tg1']); ?>" readonly>
							<input type="button" name="popcal1" value="&#8225;" onClick="if(self.gfPop)gfPop.fPopCalendar(document.frmCari.tg1);return false;">
							<input name="space" type="text" style="border:0px;" value="s/d" size="3" readonly="true">
							<input name="tg2" type="text" id="tg2" value="<?php echo($_POST['tg2']); ?>" size="10" readonly>
							<input type="button" name="popcal2" value="&#8225;" onClick="if(self.gfPop)gfPop.fPopCalendar(document.frmCari.tg2);return false;">
							<button type="button" class="btn_2" onclick="cekForm(0);" name="search0" style="width:73px;"><?php echo $bhs['Cari'][$kdbhs]?></button> 
							 <button type="button" class="btn_2" onclick="cekForm(1);" name="search1"  style="width:73px;"><?php echo $bhs['Cari'][$kdbhs]?></button>
							<?php if($cari||$_POST['tg1']||$_POST['tg2']){
									echo '<button type="button" onclick=location.href="'.base_url."modul/rte/".$div.'" style="width:75px" class="btn_2">'.$bhs['Batal'][$kdbhs].'</button></td>';
								}
							?>
							<input type="hidden" name="Submit" value="submit"  />
							<?php
							//fungsi cek
							echo("<script language=\"javascript\">\n");
							echo("	cekField(\"". $_POST["field"] ."\");\n");
							echo("</script>");
							?>									
						 </td>
					</tr>
				</table>
				</form>								
				</td>
			</tr>
		</table>
	</div>
</div>
<iframe width=174 height=189 name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="<?php echo base_url?>ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">
</iframe>

<?php
if($div=="baru"){
echo "<fieldset style='border:none;padding:10px;font-size:11px;font-family:Tahoma;padding-bottom:-20px;border-bottom:1px #CCC solid'>
		<b>Catatan :</b>
		<ul style='margin-bottom:-5px;'>		
		<span style='background:#FAC7B8;width:10px;border:1px #999 solid;margin-left:-30px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> &nbsp;Rejected			
		</ul>
	</fieldset>";
}
	
	$order = $_GET['order'];;
	$sort = $_GET['sort'];		
	$rte = array("NO_IDENTIFIKASI","NPWP","NAMA_EKSPORTIR","KPBC","NO_PEB","TGL_PEB","VALUTA","NOMINALSORT","FOBSORT","SANDI_KETERANGAN","KELENGKAPANDOK","SELISIH","VIAEMAIL","FLAG_USED","REFNUMBER");			
	$orderby = setSortir(array('sort'=>'ID','order'=>'desc'),$rte);	
	
	$tg1 = $_POST["tg1"];
	$tg2 = $_POST["tg2"];	
	
	$where = array("baru"=>"'0','6'","terkirim"=>"'1','4','5'","draft"=>"'2'","pending"=>"'3'","rteuangmuka"=>"'1','5'");
	$sql = "select r.idRTE as ID, 
			d.reference_number as REFNUMBER,
			CASE WHEN p.NPWP != NULL THEN
				CASE LENGTH(p.NPWP) WHEN 15 THEN (SUBSTR(p.NPWP,1,2)||'.'||SUBSTR(p.NPWP,3,3)||'.'||SUBSTR(p.NPWP,6,3)||'.'||SUBSTR(p.NPWP,9,1)||
				'-'||SUBSTR(p.NPWP,10,3)||'.'||SUBSTR(p.NPWP,13,3)) ELSE p.NPWP END
			ELSE 
				CASE LENGTH('".$_SESSION['npwp_session']."') WHEN 15 THEN (SUBSTR('".$_SESSION['npwp_session']."',1,2)||'.'||SUBSTR('".$_SESSION['npwp_session']."',3,3)||'.'||SUBSTR('".$_SESSION['npwp_session']."',6,3)||'.'||SUBSTR('".$_SESSION['npwp_session']."',9,1)||
				'-'||SUBSTR('".$_SESSION['npwp_session']."',10,3)||'.'||SUBSTR('".$_SESSION['npwp_session']."',13,3)) ELSE '".$_SESSION['npwp_session']."' END
			END AS NPWP,										
			CASE WHEN LENGTH(p.nama_eksportir) != 0 THEN p.nama_eksportir ELSE '".$_SESSION['nmcomp_session']."' END as NAMAEKSPORTIR, 
			CASE WHEN LENGTH(p.KPBC) != 0 THEN p.KPBC ELSE 'NNNNNN' END as KPBC, 
			CASE WHEN LENGTH(p.no_PEB) != 0 THEN p.no_PEB ELSE 'NNNNNNNN' END as NOPEB, 	
			CASE WHEN LENGTH(p.tgl_PEB) != 0 THEN TO_CHAR(p.tgl_PEB,'DD-MM-YYYY') ELSE 'NNNNNNN' END as TGLPEB, 						
			r.VALUTA,
			SUBSTR(TO_CHAR(d.nominal_transfer ,'999,999,999,999.99'),0, LENGTH(TO_CHAR(d.nominal_transfer,'999,999,999,999.99'))-3) as DANAMASUK, 
					
			CASE WHEN r.nominal_DHE != 0 THEN TO_CHAR(r.nominal_DHE,'999,999,999,999.99') ELSE '0' END  AS FOB, 			
			CASE WHEN r.nominal != 0 THEN TO_CHAR(r.nominal,'999,999,999,999.99') ELSE '0' END AS NOMINAL, 	
			
			r.Sandi_Keterangan as SANDIKETERANGAN,
			r.KELENGKAPANDOK, 
			r.KETERANGAN, TO_CHAR(r.tglSend,'DD-MM-YYYY') AS TGLSEND";
	if($div=="baru"){#DHE < PEB sebesar 10% atau 10 jt rupiah
		$sql .=",case when (((r.nominal_IDR-r.nominal_DHE_IDR)>r.nominal_IDR*10/100) or ((r.nominal_IDR-r.nominal_DHE_IDR)>(select nilai from tbldmselisih)) or (r.nominal_DHE=0) or ((r.Sandi_Keterangan  = '0220' or r.Sandi_Keterangan  = '0230' ) and LENGTH(p.no_PEB) != 0 ) or r.Sandi_Keterangan  = '0300') then '1' else '' 
				end as SELISIH,r.fileupload as VIAEMAIL";
		$whereplus = " ";
	}elseif($div=="rteuangmuka"){
		$sql .=",case when (((r.nominal_IDR-r.nominal_DHE_IDR)>r.nominal_IDR*10/100) or ((r.nominal_IDR-r.nominal_DHE_IDR)>(select nilai from tbldmselisih)) or (r.nominal_DHE=0)) then '1' else '' 
				end as SELISIH,case when status = '5' then 'Dibatalkan' else '' end as sts_btl";
		$whereplus = "and nominal =0 and d.flag_used!='2' ";
	}elseif($div=="terkirim"){
		$sql .=",'' as Hidden1,'' as Hidden2 ";
		$whereplus = " ";#"and (nominal !=0 and d.flag_used='1') ";
	}else{
		$sql .= ",'' as SELISIH,'' as VIAEMAIL";
		$whereplus = " ";
	}
	
	$sql .=",r.FILEUPLOAD,r.nominal_DHE as FOBSORT, NOMINAL as NOMINALSORT, r.IDDANAMASUK, 
				D.FLAG_USED ,d.reference_number as REFNUMBER1, TO_CHAR(r.tglSend,'DD-MM-YYYY') as tglSent, r.status
				from tblfcRTE r left join tbldmdanamasuk d on r.iddanamasuk=d.iddanamasuk left join tbldmPEB p on r.idPEB=p.idPEB 
			 	where r.status in (".$where[$div].") ".$whereplus." and ( upper(p.groupid) in ('".strtoupper($_SESSION['grpID'])."') or d.noRek in(".trim($_SESSION['noRek']).") )";
	if($div=="rteuangmuka"){
	$sql .=" and sandi_keterangan <> '0300' ";
	}
	if($_POST["Submit"]=="submit" ){						
		if($_POST["field"] == "TGL_PEB"){
			$sql = $sql ." And p.TGL_PEB BETWEEN TO_DATE('".$tg1 ."','DD-MM-YYYY') AND  TO_DATE('".$tg2 ."','DD-MM-YYYY') "; 
		}elseif($_POST["field"] == "TGL_SEND"){
			$sql = $sql ." And r.TGLSEND BETWEEN TO_DATE('".$tg1 ."','DD-MM-YYYY') AND  TO_DATE('".$tg2 ."','DD-MM-YYYY') "; 
		} else {
			$sql = $sql ." And upper(". strFilter($_POST["field"]) .") Like '%". strtoupper($_POST["q"]) ."%'";							
		}
	} 
	
	//sql gabungan
	$sql = $sql . $orderby;
	//echo $sql;
	$table = new HTMLTableColorRTE();
	$table->connection = $connDB;
	$table->width = "100%";
	$table->navRowSize = 10;
	$table->SQL = $sql;	
	$table->cbxMod1 = true;			
	$table->color = 1;
	// elemen data yang akan di passing
	$cols = array();
	$cols[0] = 0;
	$data = array();
	$data[] = array("#",$bhs['Pilih Proses'][$kdbhs]);	
	if($div=="baru"){
		$table->ajaxMod5= 15; #selisih
		$table->ajaxMod6= 11; #sandi keterangan		
		$table->ajaxMod9= 17; #viaemail
		#$data[] = array(base_url."modul/rte/viaemail","- Tandai Pengiriman dokumen via Email");	
		$data[] = array(base_url."modul/rte/update",$bhs['Simpan Sandi Keterangan'][$kdbhs]);
		$data[] = array(base_url."modul/rte/upload",'- '.$bhs['Upload Dokumen'][$kdbhs]);
		$data[] = array(base_url."modul/rte/tosend",$bhs['Kirim ke Bank'][$kdbhs]);
		$data[] = array(base_url."modul/rte/batal",$bhs['Batal RTE'][$kdbhs]);		
	}elseif($div=="rteuangmuka"){
		$table->ajaxMod5= 15; #selisih
		$table->ajaxMod6= 11; #sandi keterangan		
		$table->ajaxMod9= 17; #viaemail
		$data[] = array(base_url."modul/rte/rtepilihpeb",$bhs['Pilih PEB'][$kdbhs]);
		$data[] = array(base_url."modul/rte/rteuangmukabatal",$bhs['Batal Uangmuka'][$kdbhs]);
	}
	$table->ajaxMod7= 12; #kelengkapandok
	$table->ajaxMod12= 1; #true
	$data[] = array(base_url."reportrte.php",$bhs['Cetak Report'][$kdbhs]);
	
	$table->showCheckBox(true,$cols);	
	$table->showPager(true,$F_HANDLER->BOTTOM,50,20);
	$table->showRDPanel(true,$F_HANDLER->BOTH,$cols,$data);	
	$x=0;
	$table->field[$x]->name = "ID";
	$table->field[$x]->headername = "ID";
	$table->field[$x]->align = $F_HANDLER->LEFT;
	$table->field[$x]->hidden = true;
	$x=1;	
	$table->field[$x]->name= "REFNUMBER";
	$table->field[$x]->headername= $bhs['No. Ref'][$kdbhs];
	$table->field[$x]->align = $F_HANDLER->LEFT;	
	$x=2;
	$table->field[$x]->name = "NPWP";
	$table->field[$x]->headername = "NPWP";
	$table->field[$x]->align = $F_HANDLER->LEFT;	
	$x=3;
	$table->field[$x]->name = "NAMA_EKSPORTIR";
	$table->field[$x]->headername = $bhs['Nama Penerima DHE'][$kdbhs];
	$table->field[$x]->align = $F_HANDLER->LEFT;	
	$x=4;
	$table->field[$x]->name = "KPBC";
	$table->field[$x]->headername = $bhs['Sandi Kantor Pabean'][$kdbhs];
	$table->field[$x]->align = $F_HANDLER->LEFT;
	$x=5;
	$table->field[$x]->name = "NO_PEB";
	$table->field[$x]->headername = $bhs['No. PEB'][$kdbhs];
	$table->field[$x]->align = $F_HANDLER->LEFT;
	$x=6;
	$table->field[$x]->name = "TGL_PEB";
	$table->field[$x]->headername = $bhs['Tanggal PEB'][$kdbhs];
	$table->field[$x]->align = $F_HANDLER->LEFT;					
	$x=7;
	$table->field[$x]->name = "VALUTA";
	$table->field[$x]->headername = $bhs['Valuta'][$kdbhs];
	$table->field[$x]->align = $F_HANDLER->LEFT;
	$x=8;
	$table->field[$x]->name = "DANA_MASUK";	
	$table->field[$x]->hidden= true;
	$x=9;
	$table->field[$x]->name = "FOBSORT";
	$table->field[$x]->headername = $bhs['Nilai DHE'][$kdbhs];
	$table->field[$x]->align = $F_HANDLER->RIGHT;
	$x=10;
	$table->field[$x]->name = "NOMINALSORT";
	$table->field[$x]->headername = $bhs['Nilai PEB'][$kdbhs];
	$table->field[$x]->align = $F_HANDLER->RIGHT;
	$x=11;
	$table->field[$x]->name = "SANDI_KETERANGAN";
	$table->field[$x]->headername = $bhs['Sandi Keterangan'][$kdbhs];
	$table->field[$x]->align = $F_HANDLER->LEFT;
	$x=12;
	$table->field[$x]->name = "KELENGKAPANDOK";
	$table->field[$x]->headername = $bhs['Kelengkapan Dokumen'][$kdbhs];
	$table->field[$x]->align = $F_HANDLER->LEFT;	
	$x=13;
	$table->field[$x]->name = "KETERANGAN";	
	$table->field[$x]->hidden = true;
	$x=14;
	$table->field[$x]->name = "TGLSEND";
	$table->field[$x]->hidden = true;
	
	if($div=="baru" || $div=="rteuangmuka"){
		
		if ($div=="rteuangmuka"){
		$x=15;
		$table->field[$x]->name = "SELISIH";
		$table->field[$x]->hidden = true;
	
		$x=16;
		$table->field[$x]->name = "sts_btl";
		$table->field[$x]->headername = $bhs['status uangmuka'][$kdbhs];
		$table->field[$x]->align = $F_HANDLER->Center;
		}else{
		$x=15;
		$table->field[$x]->name = "SELISIH";
		$table->field[$x]->headername = $bhs['Upload Dokumen'][$kdbhs];
		$table->field[$x]->align = $F_HANDLER->LEFT;	
			
		$x=16;
		$table->field[$x]->name = "sts_btl";
		$table->field[$x]->hidden = true;
$x=17;
		$table->field[$x]->name = "VIAEMAIL";
		$table->field[$x]->headername = $bhs['Via Email'][$kdbhs];
		$table->field[$x]->align = $F_HANDLER->LEFT;			
		}
	}else{
		$x=15;
		$table->field[$x]->name = "SELISIH";
		$table->field[$x]->hidden = true;
		$x=16;
		$table->field[$x]->name = "SELISIH";
		$table->field[$x]->hidden = true;
		
	}
	$x++;
	$table->field[$x]->name = "FILEUPLOAD";
	$table->field[$x]->headername = "FILEUPLOAD";
	$table->field[$x]->align = $F_HANDLER->LEFT;
	$table->field[$x]->hidden = true;
	
	$x++;
	$table->field[$x]->name= "FOBSORT";
	$table->field[$x]->hidden= true;
	
	$x++;
	$table->field[$x]->name= "NOMINALSORT";
	$table->field[$x]->hidden= true;
	
	$x++;
	$table->field[$x]->name= "IDDANAMASUK";
	$table->field[$x]->hidden= true;
	
	$x++;
	$table->field[$x]->name= "FLAG_USED";
	$table->field[$x]->hidden= true;
	
	$x++;
	$table->field[$x]->name= "REFNUMBER";
	$table->field[$x]->hidden= true;
	$x++;
	$table->field[$x]->name= "TGLSENT";
	$table->field[$x]->headername= $bhs['Tgl Pelaporan'][$kdbhs];
	$table->field[$x]->hidden= true;

	if($div=="terkirim"||$div=="pending"){
		$table->field[$x]->hidden= false;
	}
	$x++;
	$table->field[$x]->name= "STATUS";
	$table->field[$x]->hidden= true;
	$table->drawTable();
	$conn->disconnect();
}
?>
