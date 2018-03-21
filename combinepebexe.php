<?php
if(in_array($_SESSION["priv_session"],array("0","3"))==true || substr($_SESSION["AKSES"],3,1)!="1" ){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}elseif(time()>$_SESSION['timeout_session']){	
	echo "<script> window.location.href='".base_url."err/7/".md5(7)."';</script>";exit;
}else{
	$_SESSION['timeout_session'] = TIMEOUT;
}
	
	$sandiRTE = $_POST['sandiRTE'];
	$batch = $_SESSION['grpID'].date('dmyHis');
	if(is_array($_POST['cbx']) || $div=="eksporrte"){
		?>
		<script type="text/javascript" src="<?php echo base_url?>js/jquery.js"></script> 
		<link type="text/css" href="<?php echo base_url?>js/jAlert/jquery.alerts.css" rel="stylesheet" />	   
		<script type="text/javascript" src="<?php echo base_url?>js/jAlert/jquery.alerts.js"></script> 
		<?php
		session_start();
		set_time_limit(900);
		function getIDR($conn,$valuta,$tgl,$nilai){
			$sql = "select NOMINAL from tbldmkurs where valuta='".$valuta."' 
				AND DATEDIFF(TO_DATE('".$tgl."','DD-MM-YYYY'),tglawal) >=0 and 
				DATEDIFF(tglakhir,TO_DATE('".$tgl."','DD-MM-YYYY')) >=0 and ROWNUM = 1 order by tglawal desc";
			$d = $conn->query($sql); $d->next();
			return $nominal = $nilai*ceil($d->get('NOMINAL'));
		}
		
		$idPeb = $_POST['cbx'];
		
		$nominal = $_POST['nominal'];
		$keterangan = $_POST['keterangan'];			
		$iddanamasuk = 0;
		$tgltrans_="";
		$idlld_ = 0;
		$sandiRTE_="";	
		$nama_pengirim_="";
		$valuta_transfer_="";	
		require_once("dbconndb.php");
		$connDB->connect();	
		if($div=="eksporrte"){	
			$x=0;
			$hasil=0;
			/*
			$cols[0] = idpeb
			$cols[1] = valuta
			$cols[2] = iddanamasuk
			$cols[3] = kurs
			$cols[4] = tgl_Trans
			$cols[5] = idlld
			$cols[6] = nilai dhe
			$cols[7] = 17; #NAMA PENGIRIM
			$cols[8] = 18; #VALUTA_TRANSFER			
			*/			
			if(is_array($idPeb)){
				$dmasuk = explode(";",$idPeb[0]);	
				$sql = "select TO_CHAR(TGL_TRANSAKSI,'DD-MM-YYYY') as TGL_TRANSAKSI,IDLLD,NAMA_PEMILIK, NOREK, VALUTA_TRANSFER, NOMINAL_TRANSFER, VALUTA_DITERIMA,
						NOMINAL_DITERIMA, NAMA_PENGIRIM, NAMA_BANK_PENGIRIM,BERITA,REFERENCE_NUMBER from tbldmdanamasuk where iddanamasuk = '".$dmasuk[2]."'";
				$iddanamasuk = $dmasuk[2];
				$dtdm = $connDB->query($sql); $dtdm->next();
				
				$tgl_transaksi_ = $dtdm->get('TGL_TRANSAKSI');
				$idlld_ = $dtdm->get('IDLLD');
				$nama_pemilik_ = $dtdm->get('NAMA_PEMILIK');
				$norek_ = $dtdm->get('NOREK');
				$valuta_transfer_ = $dtdm->get('VALUTA_TRANSFER');
				$nominal_transfer_ = $dtdm->get('NOMINAL_TRANSFER');
				$valuta_diterima_ = $dtdm->get('VALUTA_DITERIMA');
				$nominal_diterima_ = $dtdm->get('NOMINAL_DITERIMA');
				$nama_pengirim_ = $dtdm->get('NAMA_PENGIRIM');
				$nama_bank_pengirim_ = $dtdm->get('NAMA_BANK_PENGIRIM');
				$berita_ = $dtdm->get('BERITA');
				$reference_number_ = $dtdm->get('REFERENCE_NUMBER');				
				
				foreach($idPeb as $row){
					
					$peb = explode(";",$row);
					#print_r($peb);exit;
					$nominal_IDR = getIDR($connDB,$peb[1],$peb[4],str_replace(',','',$peb[6])); 	
					$nominal_DHE_IDR = getIDR($connDB,$peb[8],$peb[4],str_replace(',','',$nominal[$x])); 
							
					if($nominal_IDR==0){
						echo "<script type='text/javascript'> 
							$(document).ready(function(){
								jAlert('Kurs Tidak Ditemukan ','',function(r){
									if(r==true) window.location.href='".base_url."modul/danamasuk/pilihpeb/".$peb[2]."/".md5($peb[2])."';\n				
								});		
							})
						</script>";	
						exit;
					}
					
					$sql = "INSERT INTO tblfcRTE (idPEB,iddanamasuk,nominal_DHE,nominal,valuta,kurs,Sandi_keterangan,KelengkapanDok,Keterangan,userInput,userEdit,Status,tglSend,nominal_IDR,nominal_DHE_IDR,no_identifikasi,batch) 
					VALUES ('".strFilter($peb[0])."','".strFilter($peb[2])."',".str_replace(',','',$nominal[$x]).",".str_replace(',','',$peb[6]).",'".strFilter($peb[8])."',".(strlen(trim($peb[3]))==0?0:$peb[3]).",'".strFilter($sandiRTE[$x])."','0','".strFilter($keterangan[$x])."','".strFilter(trim($_SESSION['uid_session']))."','','0',NULL,".strFilter($nominal_IDR).",".strFilter($nominal_DHE_IDR).",'".$peb[5]."','".$batch."')";
					
					$hasil = $connDB->execute($sql);
					if($hasil){
						$sql = "select VALUTA,FOB from tbldmpeb where idPEB='".strFilter($peb[0])."'";
						$d= $connDB->query($sql); $d->next();
						$fob_idr = getIDR($connDB,$d->get('VALUTA'),$peb[4],$d->get('FOB'));
						$sql = "update tbldmpeb set flag_used='1',FOB_IDR=".strFilter($fob_idr)." where idPEB='".strFilter($peb[0])."'";
						$connDB->execute($sql);	
					}				
					$x++;
				}
			}
			$uangmuka = $_POST['uangmuka'];	
			$statusUangMuka = $_POST['statusUangMuka'];			
			$jnsPembayaranUangMuka = $_POST['jnsPembayaranUangMuka'];			
			$jenisuang = $_POST['jenisuang'];
			if(is_array($uangmuka)){
				$sql = "select IDPEB, NPWP FROM tbldmpeb where GROUPID='".trim($_SESSION['grpID'])."' and NAMA_EKSPORTIR='".strFilter(trim($_SESSION['nmcomp_session']))."'
						and CAR=NULL";				
				$peb = $connDB->query($sql);
				
				if(!$peb->next()){
					$sql = "insert into tbldmpeb (NPWP,NAMA_EKSPORTIR,FLAG_USED,DTCREATED,GROUPID) values 
							('".trim($_SESSION['npwp_session'])."','".strFilter(trim($_SESSION['nmcomp_session']))."','4',SYSDATE,'".strtoupper($_SESSION['grpID'])."')";
					$connDB->execute($sql);
					$sql = "select max(IDPEB) as IDPEB from tbldmpeb";
					$dataPeb = $connDB->query($sql); $dataPeb->next();
					$idPEB = $dataPeb->get('IDPEB');
				}else{
					$idPEB = $peb->get('IDPEB');
				}				
				$x=0;
				foreach($uangmuka as $um){		
					if ($jenisuang[$x]=="3"){				
						$sandiRTE_ = ($jnsPembayaranUangMuka[$x]=='01') ? '0220' : '0230'; #01 uangmuka penuh 02 parsial
						$sql = "insert into tbldmdanamasuk(TGL_TRANSAKSI,IDLLD,NAMA_PEMILIK, NOREK, VALUTA_TRANSFER, VALUTA_DITERIMA,
								NOMINAL_DITERIMA, NAMA_PENGIRIM, NAMA_BANK_PENGIRIM,BERITA,REFERENCE_NUMBER,
								nominal_transfer,jns_uangmuka,flag_used,kd_dana,jns_pembayaran) 
								values(TO_DATE('".$tgl_transaksi_."','DD-MM-YYYY'),'".$idlld_."','".$nama_pemilik_."','".$norek_."','".$valuta_transfer_."',
								'".$valuta_diterima_."','".$nominal_diterima_."','".$nama_pengirim_."','".$nama_bank_pengirim_."','".$berita_."','".$reference_number_."',
								".str_replace(',','',$um).",
								'".$statusUangMuka[$x]."','1','04','".$jnsPembayaranUangMuka[$x]."')";						
						$connDB->execute($sql);
						
						$sql = "select max(iddanamasuk) as ID from tbldmdanamasuk";
						$data  = $connDB->query($sql);$data->next();
						$iddanamasuk_ = $data->get('ID');
						$sql = "INSERT INTO tblfcRTE (idPEB,iddanamasuk,nominal,nominal_DHE,nominal_DHE_IDR,valuta,kurs,Sandi_keterangan,KelengkapanDok,Keterangan,userInput,userEdit,Status,tglSend,nominal_IDR,no_identifikasi) 
						VALUES ('".$idPEB."','".$iddanamasuk_."',0,".str_replace(',','',$um).",0,'".$valuta_transfer_."',0,'".$sandiRTE_."','0','','".strFilter(trim($_SESSION['uid_session']))."','','0',SYSDATE,0,'".$idlld_."')";																
						$hasil = $connDB->execute($sql);									
					}elseif($jenisuang[$x]=="2"){
						$sql = "insert into tbldmdanamasuk(TGL_TRANSAKSI,IDLLD,NAMA_PEMILIK, NOREK, VALUTA_TRANSFER, VALUTA_DITERIMA,
								NOMINAL_DITERIMA, NAMA_PENGIRIM, NAMA_BANK_PENGIRIM,BERITA,REFERENCE_NUMBER,
								nominal_transfer,jns_uangmuka,flag_used,kd_dana,jns_pembayaran,dana_sisa) 
								values(TO_DATE('".$tgl_transaksi_."','DD-MM-YYYY'),'".$idlld_."','".$nama_pemilik_."','".$norek_."','".$valuta_transfer_."',
								'".$valuta_diterima_."','".$nominal_diterima_."','".$nama_pengirim_."','".$nama_bank_pengirim_."','".$berita_."','".$reference_number_."',
								".str_replace(',','',$um).",
								'0','0','02','0','1')";						
						$connDB->execute($sql);	
					
					}elseif($jenisuang[$x]=="1"){
						$sql = "insert into tbldmdanamasuk(TGL_TRANSAKSI,IDLLD,NAMA_PEMILIK, NOREK, VALUTA_TRANSFER, VALUTA_DITERIMA,
								NOMINAL_DITERIMA, NAMA_PENGIRIM, NAMA_BANK_PENGIRIM,BERITA,REFERENCE_NUMBER,
								nominal_transfer,jns_uangmuka,flag_used,kd_dana,jns_pembayaran,dana_sisa) 
								values(TO_DATE('".$tgl_transaksi_."','DD-MM-YYYY'),'".$idlld_."','".$nama_pemilik_."','".$norek_."','".$valuta_transfer_."',
								'".$valuta_diterima_."','".$nominal_diterima_."','".$nama_pengirim_."','".$nama_bank_pengirim_."','".$berita_."','".$reference_number_."',
								".str_replace(',','',$um).",
								'0','0','01','0','1')";	
						$connDB->execute($sql);
					}									
					$x++;
				}
			}
			if($hasil){
				$sql = "update tbldmdanamasuk set flag_used='".$_POST['statusDanamasuk']."' where iddanamasuk='".strFilter($iddanamasuk)."'";
				$connDB->execute($sql);
				$_SESSION['respon'] = $bhs['RTE Simpan'][$kdbhs];
				
				require_once("dbconn.php");
				$conn->connect();    
				
				$aktivitas = "Membuat sejumlah ".count($idPeb)." RTE";		
				audit($conn,$aktivitas); 
				$conn->disconnect();  
			}
		}elseif($div=="uangmukarte"){
			/*
			$cols[0] = 0;#ID
			$cols[1] = 3;#nominal_transfer
			$cols[2] = 12;#idlld
			$cols[3] = 10;#sandiketerangan
			$cols[4] = 2;#valuta_transfer
			*/
			$arr  = $_POST['cbx'];
			//print_r ($arr); die(); 
			$keterangan = $_POST['keterangan'];	
			$jnsUangMuka = $_POST['jnsUangMuka'];
			$sql = "select IDPEB, NPWP FROM tbldmpeb where GROUPID = '".trim($_SESSION['grpID'])."' and NAMA_EKSPORTIR='".strFilter(trim($_SESSION['nmcomp_session']))."' 
					and CAR=NULL";			
			$peb = $connDB->query($sql);
			
			if(!$peb->next()){
				$sql = "insert into tbldmpeb (NPWP,NAMA_EKSPORTIR,FLAG_USED,DTCREATED,GROUPID) values 
						('".trim($_SESSION['npwp_session'])."','".strFilter(trim($_SESSION['nmcomp_session']))."','4',SYSDATE,'".strtoupper($_SESSION['grpID'])."')";
				$connDB->execute($sql);
				$sql = "select max(IDPEB) as IDPEB from tbldmpeb";
				$dataPeb = $connDB->query($sql); $dataPeb->next();
				$idPEB = $dataPeb->get('IDPEB');
				
			}else{
				$idPEB = $peb->get('IDPEB');
			}
			$x=0;
			foreach($arr as $row){
				$dm = explode(';',$row);
				$batch = $_SESSION['grpID'].date('dmyHis').$x;
				$sql = "INSERT INTO tblfcRTE (idPEB,iddanamasuk,nominal,nominal_DHE,nominal_DHE_IDR,valuta,kurs,Sandi_keterangan,KelengkapanDok,Keterangan,userInput,userEdit,Status,tglSend,nominal_IDR,no_identifikasi,batch) 
				VALUES ('".$idPEB."','".strFilter($dm[0])."',0,'".str_replace(',','',$dm[1])."','0','".$dm[4]."',0,'".$dm[3]."','0','','".strFilter(trim($_SESSION['uid_session']))."','','0',SYSDATE,0,'".$dm[2]."','".$batch."')";											
				$hasil = $connDB->execute($sql);
				if($hasil){
					$sql = "update tbldmdanamasuk set flag_used='1', jns_uangmuka='".$jnsUangMuka."' where iddanamasuk='".strFilter($dm[0])."'";
					$connDB->execute($sql);	
				}
				$x++;
			}
			$_SESSION['respon'] = $bhs['RTE Uang Muka Simpan'][$kdbhs];
			
			require_once("dbconn.php");
			$conn->connect();    
			
			$aktivitas = "Membuat sejumlah ".count($arr)." RTE";		
			audit($conn,$aktivitas); 
			$conn->disconnect();  
		}elseif($div=="rtepilihpeb"){
			$x=0;
			$hasil=0;
			$nilai_peb = $_POST['nilai_peb'];
			
			/*
			$cols[0] = idpeb
			$cols[1] = valuta
			$cols[2] = iddanamasuk
			$cols[3] = kurs
			$cols[4] = tgl_Trans
			$cols[5] = idlld
			$cols[6] = nilai dhe
			$cols[7] = 17; #NAMA PENGIRIM
			$cols[8] = 18; #VALUTA_TRANSFER
			$cols[9] = 20; #jns_pembayaran
			
			*/
			foreach($idPeb as $row){
				$peb = explode(";",$row);
				$nominal_IDR = getIDR($connDB,$peb[1],$peb[4],str_replace(',','',$nilai_peb[$x])); 
				$nominal_DHE_IDR = getIDR($connDB,$peb[1],$peb[4],str_replace(',','',$nominal[$x])); 		
				if($nominal_IDR==0){
					echo "<script type='text/javascript'> 
						$(document).ready(function(){
							jAlert('Kurs Tidak Ditemukan','',function(r){
								if(r==true) window.location.href='".base_url."modul/rte/rtepilihpeb/".$peb[2]."/".md5($peb[2])."';\n				
							});		
						})
					</script>";	
					exit;
				}	
				$sql = "INSERT INTO tblfcRTE (idPEB,iddanamasuk,nominal_DHE,nominal,valuta,kurs,Sandi_keterangan,KelengkapanDok,Keterangan,userInput,userEdit,Status,tglSend,nominal_IDR,nominal_DHE_IDR,no_identifikasi,batch) 
				VALUES ('".strFilter($peb[0])."','".strFilter($peb[2])."',".str_replace(',','',$nominal[$x]).",".str_replace(',','',$nilai_peb[$x]).",'".strFilter($peb[8])."',".(strlen(trim($peb[3]))==0?0:$peb[3]).",'".strFilter($sandiRTE[$x])."','0','".strFilter($keterangan[$x])."','".strFilter(trim($_SESSION['uid_session']))."','','0',SYSDATE,".strFilter($nominal_IDR).",".strFilter($nominal_DHE_IDR).",'".$peb[5]."','".$batch."')";
				$hasil = $connDB->execute($sql);
				if($hasil){
					$sql = "select VALUTA,FOB from tbldmpeb where idPEB='".strFilter($peb[0])."'";
					$d= $connDB->query($sql); $d->next();
					$fob_idr = getIDR($connDB,$d->get('VALUTA'),$peb[4],$d->get('FOB'));
					if(trim($peb[9])=='02'){
						$flag = "2";
					}else{
						$flag = "1";
					}
					$sql = "update tbldmpeb set flag_used='".$flag."',FOB_IDR=".strFilter($fob_idr)." where idPEB='".strFilter($peb[0])."'";
					$connDB->execute($sql);	
				}
				$iddanamasuk = $peb[2];
				$x++;
			}
			if($hasil){
				$terakhir = $_POST['terakhir'];
				$sql = "select JNS_UANGMUKA from tbldmdanamasuk where iddanamasuk='".strFilter($iddanamasuk)."' and kd_dana='04'";					
				$dtDana = $connDB->query($sql);
				if($dtDana->next()){					
					$flag_used = 2;
					if($dtDana->get('JNS_UANGMUKA')=='2'){							
						$jns_uangmuka = ($terakhir==2)? 1 : 2;
						$flag_used = ($terakhir==2)? 2 : 1;						
					}				
					$sql = "update tbldmdanamasuk set flag_used='".$flag_used."', jns_uangmuka='".$jns_uangmuka."' where iddanamasuk='".strFilter($iddanamasuk)."'";								
				}else{
					$sql = "update tbldmdanamasuk set flag_used='1' where iddanamasuk='".strFilter($iddanamasuk)."'";			
				}
				$connDB->execute($sql);	
				$_SESSION['respon'] = $bhs['RTE Simpan'][$kdbhs];
				
				require_once("dbconn.php");
				$conn->connect();    
				
				$aktivitas = "Membuat sejumlah ".count($idPeb)." RTE";		
				audit($conn,$aktivitas); 
				$conn->disconnect();  
			}	
		
		}
		$_SESSION['statusRespon'] = 1;
		//echo $sql;
		echo "<script> window.location.href='".base_url."modul/rte/baru';</script>";exit;
	
	}else{
		echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;		
	}

?>