<?php
if(in_array($_SESSION["priv_session"],array("0","3"))==true  || substr($_SESSION["AKSES"],3,1)!="1"){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}elseif(time()>$_SESSION['timeout_session']){	
	echo "<script> window.location.href='".base_url."err/7/".md5(7)."';</script>";exit;
}else{
	$_SESSION['timeout_session'] = TIMEOUT;
}
	?>
	<script type="text/javascript" src="<?php echo base_url?>js/jquery.js"></script> 
	<link type="text/css" href="<?php echo base_url?>js/jAlert/jquery.alerts.css" rel="stylesheet" />	   
	<script type="text/javascript" src="<?php echo base_url?>js/jAlert/jquery.alerts.js"></script> 
	<?php
	set_time_limit(900);
	$batch = $_SESSION['grpID'].date('dmyHis');
	if($div=='rtedanamasuk'){
		 $iddm = $_POST['cbx'];
		if(!is_array($iddm)){ echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;}
		function getIDR($conn,$valuta,$tgl,$nilai){
			 $sql = "select NOMINAL from tbldmkurs where valuta='".$valuta."' 
					AND DATEDIFF(TO_DATE('".$tgl."','DD-MM-YYYY'),tglawal) >=0 and 
				DATEDIFF(tglakhir,TO_DATE('".$tgl."','DD-MM-YYYY')) >=0 and ROWNUM = 1 order by tglawal desc";
			$d = $conn->query($sql); $d->next();
			return $nominal = $nilai*ceil($d->get('NOMINAL'));
		}
		
		$nominal = $_POST['nominal'];		
		$sandiRTE = $_POST['sandiRTE'];
		$keterangan = $_POST['keterangan'];
		$terakhir = $_POST['terakhir']; #pilihan terakhir multiple
		$idpeb = 0;
		
		require_once("dbconndb.php");
		$connDB->connect();
		$x=0;
		$hasil=0;
		/*$cols[0] = iddanamasuk
		  $cols[1] = tgltransaksi
		  $cols[2] = valuta
		  $cols[3] = idpeb
		  $cols[4] = kurs
		  $cols[5] = idlld
		  $cols[6] = jnsUangMuka
		  $cols[7] = nilaitransfer dhe
		  $cols[8] = kurs dhe / valuta transfer
		 */
		 //print_r($iddm);die();
		foreach($iddm as $row){
			$dm = explode(";",$row);
			$nominal_IDR = getIDR($connDB,$dm[2],$dm[1],str_replace(',','',strFilter($nominal[$x]))); 	
			$nominal_DHE_IDR = getIDR($connDB,$dm[8],$dm[1],str_replace(',','',strFilter($dm[7])));
			if($nominal_IDR==0){
				echo "<script type='text/javascript'> 
					$(document).ready(function(){
						jAlert('Kurs Tidak Ditemukan','',function(r){
							if(r==true) window.location.href='".base_url."modul/peb/pilihdanamasuk/".$dm[3]."/".md5($dm[3])."';\n				
						});		
					})
				</script>";	
				exit;
			}	
			$sql = "INSERT INTO tblfcRTE (idPEB,iddanamasuk,nominal_DHE,nominal,valuta,kurs,Sandi_keterangan,KelengkapanDok,Keterangan,userInput,userEdit,Status,tglSend,nominal_IDR,nominal_DHE_IDR,no_identifikasi,batch) 
			VALUES ('".$dm[3]."','".$dm[0]."',".str_replace(',','',strFilter($dm[7])).",".str_replace(',','',strFilter($nominal[$x])).",'".$dm[2]."',".(strlen(trim($dm[4]))==0?0:$dm[4]).",'".$sandiRTE[$x]."','0','".strFilter($keterangan[$x])."','".strFilter(trim($_SESSION['uid_session']))."','','0',NULL,".$nominal_IDR.",".$nominal_DHE_IDR.",'".$dm[5]."','".$batch."')";	
			$hasil = $connDB->execute($sql);	
			//echo $hasil;die();
			#$hasil =1;
			if($hasil){
				$sql = "select JNS_UANGMUKA from tbldmdanamasuk where iddanamasuk='".$dm[0]."' and kd_dana='04'";	
				$dtDana = $connDB->query($sql);
				if($dtDana->next()){					
					$flag_used = 1;
					if($dtDana->get('JNS_UANGMUKA')=='2'){							
						$jns_uangmuka = ($terakhir[$x]==1)? 1 : 2;
						$flag_used = ($terakhir[$x]==1)? 2 : 1;						
					}									
					$sql = "update tbldmdanamasuk set flag_used='".$flag_used."', jns_uangmuka='".$jns_uangmuka."' where iddanamasuk='".$dm[0]."'";								
				}else{
					$sql = "update tbldmdanamasuk set flag_used='1' where iddanamasuk='".$dm[0]."'";			
				}
				$connDB->execute($sql);	
			}
			$idpeb = $dm[3];
			$x++;
		}
		if($hasil){
			$sql = "select VALUTA,FOB from tbldmpeb where idPEB='".$dm[3]."'";
			$d= $connDB->query($sql); $d->next();
			$fob_idr = getIDR($connDB,$d->get('VALUTA'),$dm[1],$d->get('FOB'));		
			$sql = "update tbldmpeb set flag_used='".strFilter($_POST['statusPeb'])."',FOB_IDR=".strFilter($fob_idr)." where idPEB='".strFilter($idpeb)."'";
			$connDB->execute($sql);
			$_SESSION['respon'] = $bhs['RTE Simpan'][$kdbhs];
			
			require_once("dbconn.php");
			$conn->connect();    
			
			$aktivitas = "Membuat sejumlah ".count($iddm)." RTE";		
			audit($conn,$aktivitas); 
			$conn->disconnect();
		}
	}elseif($div=='rtetanpadanamasuk'){
		$peb 	= $_POST['PEB'];
		$tgl	= $_POST['tgl'];
		$jnsbyr = $_POST['jnsbyr'];
		
		if(!is_array($peb)){ echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;}
			$sql = "update tbldmpeb set fl_send='1', tgl_tempo= to_date('$tgl','DD-MM-YYYY'), JNS_BAYAR = '$jnsbyr' where idpeb='".($peb['idpeb'])."'";			
			$connDB->execute($sql);		
			$connDB->disconnect();	
			require_once("dbconn.php");
			$conn->connect();    
				$sql = "SELECT p.NPWP, p.CAR, p.NAMA_EKSPORTIR, p.ALAMAT_EKSPORTIR, p.KPBC, p.NO_PEB, TO_CHAR(p.TGL_PEB,'DD-MM-YYYY') as TGL, p.FOB, p.VALUTA,
							p.CREATEDBY, TO_CHAR(p.DTCREATED,'DD-MM-YYYY') as DTCREATED, p.UPDATEBY, TO_CHAR(p.DTUPDATE,'DD-MM-YYYY') as DTUPDATE, p.FLAG_USED
							FROM TBLDMPEB p 
							WHERE p.idpeb='".($peb['idpeb'])."'";	
				
				$dataPEB = $conn->query($sql);
				if ($dataPEB->next()){
					$write_txt = "PEB|".$dataPEB->get('CAR')."|".$dataPEB->get('NPWP')."|".$dataPEB->get('NAMA_EKSPORTIR')."|".$dataPEB->get('ALAMAT_EKSPORTIR')."|".$dataPEB->get('KPBC')."|".
							$dataPEB->get('NO_PEB')."|".$dataPEB->get('TGL')."|".$dataPEB->get('VALUTA')."|".$dataPEB->get('FOB')."|".$dataPEB->get('CREATEDBY')."|".$dataPEB->get('DTCREATED')."|".
							$dataPEB->get('UPDATEBY')."|".$dataPEB->get('DTUPDATE')."|".$dataPEB->get('FLAG_USED')."\r\n";		
					writetxt($write_txt,'DOKPEB.',$_SESSION['grpID']);
				}
			$aktivitas = "Mengirim sejumlah 1 PEB 90+ ke Bank";		
			audit($conn,$aktivitas); 
			$conn->disconnect();	
			$_SESSION['respon'] = $bhs['Kirim PEB'][$kdbhs];
					
	}else{
		echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
	}	
	$_SESSION['statusRespon'] = 1;
	echo "<script> window.location.href='".base_url."modul/rte/baru';</script>";exit;

?>