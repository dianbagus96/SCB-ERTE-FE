<?php
ini_set( 'error_reporting', E_ALL ^ E_NOTICE );
ini_set( 'display_errors', '-1' ); 
require_once("../dbconndb1.php");
require_once("../sendEmail.php");

$email_operator 	= 'sarna@edi-indonesia.co.id';
$fileLog 		= 'logGW.txt';
$folderlokalinbound 	= '/home/RTESCB/TOFRONTEND/';
$folderlokaloutbound = '/home/RTESCB/TOBACKEND/';
$folderlokalbackup 	= '/home/RTESCB/BEBACKUP/';
$folderlokalbackupfe = '/home/RTESCB/FEBACKUP/';
$folderlokalbackuppr	= '/home/RTESCB/FRONTENDPROSES/';
$log_txt = "";
$write_txt = '';
//mereset status login
	if (date('H:i') == "03:00"){
		$sql = "update tbluser set login = 'N'";
		$connDB->execute($sql);
	}
	function writetxt($data,$name,$group){
			$Dokname 	= $name.$group.".".date('ymdHis').".FLT";	
			$file 		= '/home/RTESCB1/TOBACKEND/'.$Dokname;
			$filebac 	= '/home/RTESCB1/BEBACKUP/'.$Dokname;
			
			$filename 	= fopen($file, 'w');
			fputs($filename, $data);
			fclose($filename);
			
			$filenamebac 	= fopen($filebac, 'w');
			fputs($filenamebac, $data);
			fclose($filenamebac);
			
			return true;
	}
//Membaca file inbound
$dirinbound=opendir($folderlokalinbound);
while($files=readdir($dirinbound)){
	$ext = pathinfo($files, PATHINFO_EXTENSION);
	if ($files=="." or $files=="..")continue;
	if (copy($folderlokalinbound.$files, $folderlokalbackuppr.$files) and strtoupper($ext) == 'FLT' ){
		$place = $folderlokalinbound.$files;
		$baris = file($place);
		$namefile = explode('.',$files);
		$log_txt .= date('d-m-y H:i:s')." Proses file ".$files." \r\n";
		foreach ($baris as $juml_baris => $isinya) {
			
			//baca respon PEB
			if (strtoupper($namefile[0])=='RESPEB'){ 
				// 00 sukses 01 error
				//PEB|NO AJU|STATUS|ERROR REPORT
				$filecreate = "DOKPEB.";
				$status = explode("|",$isinya);
				if ($status[2] == '01' && $status[0] == 'PEB' ){
					$sql = "SELECT p.NPWP, p.NAMA_EKSPORTIR, p.ALAMAT_EKSPORTIR, p.KPBC, p.NO_PEB, TO_CHAR(p.TGL_PEB,'DD-MM-YYYY') as TGL, p.FOB, p.VALUTA,
							p.CREATEDBY, TO_CHAR(p.DTCREATED,'DD-MM-YYYY') as DTCREATED, p.UPDATEBY, TO_CHAR(p.DTUPDATE,'DD-MM-YYYY') as DTUPDATE, p.FLAG_USED, r.interval, r.file_flat 
							FROM TBLDMPEB p 
								LEFT JOIN TBLLOGERROR r ON p.CAR = r.key AND r.TYPE = 'PEB'
							WHERE p.CAR ='".$status[1]."'";							
					$dataPEB = $connDB->query($sql);
					if($dataPEB->next() && $dataPEB->get('interval') < 3){
						$write_txt .= "PEB|".$status[1]."|".$dataPEB->get('NPWP')."|".$dataPEB->get('NAMA_EKSPORTIR')."|".$dataPEB->get('ALAMAT_EKSPORTIR')."|".$dataPEB->get('KPBC')."|".
						$dataPEB->get('NO_PEB')."|".$dataPEB->get('TGL')."|".$dataPEB->get('VALUTA')."|".$dataPEB->get('FOB')."|".$dataPEB->get('CREATEDBY')."|".$dataPEB->get('DTCREATED')."|".
						$dataPEB->get('UPDATEBY')."|".$dataPEB->get('DTUPDATE')."|".$dataPEB->get('FLAG_USED')."\r\n";
						$NOERR = $dataPEB->get('interval')+1;
						if ($NOERR==1){
							$sqllog = "INSERT INTO TBLLOGERROR (KEY, INTERVAL, KETERANGAN, DATE_LOG, TYPE, FILE_FLAT) VALUES ('".$status[1]."',".$NOERR.",'".$status[3]."',SYSDATE,'PEB','".$files."')";
						}else{
							$fl_file = $dataPEB->get('file_flat');	
							$sqllog = "UPDATE TBLLOGERROR SET INTERVAL = ".$NOERR.", KETERANGAN = '".$status[3]."', DATE_LOG = SYSDATE, FILE_FLAT = '".$fl_file." ".$files."'  WHERE KEY = '".$status[1]."' AND TYPE = 'PEB'";
						}
						
					$connDB->execute($sqllog);	
					}
				}
				if ($status[2] == '01' && $status[0] == 'INV' ){
					$sql = "SELECT p.NODOK, TO_CHAR(p.TGLDOK,'DD-MM-YYYY'), r.interval, r.file_flat FROM TBLPEBDOK p 
						LEFT JOIN TBLLOGERROR r ON p.CAR = r.key AND r.TYPE = 'INV'
						WHERE p.CAR ='".$status[1]."'";							
					$dataPEB = $connDB->query($sql);
					if($dataPEB->next() && $dataPEB->get(2) < 3){
						$write_txt .= "INV|".$status[1]."|".$dataPEB->get(0)."|".$dataPEB->get(1)."\r\n";
						$NOERR = $dataPEB->get(2)+1;
						if ($NOERR==1){
							$sqllog = "INSERT INTO TBLLOGERROR (KEY, INTERVAL, KETERANGAN, DATE_LOG, TYPE, FILE_FLAT) VALUES ('".$status[1]."',".$NOERR.",'".$status[3]."',SYSDATE, 'INV','".$files."')";
						}else{
							$sqllog = "UPDATE TBLLOGERROR SET INTERVAL = ".$NOERR.", KETERANGAN = '".$status[3]."', DATE_LOG = SYSDATE, FILE_FLAT = '".$dataPEB->get(3)." ".$files."' WHERE KEY = '".$status[1]."' and TYPE = 'INV'";
						}
					$connDB->execute($sqllog);	
					}
				}
				if ($status[2] == '01' && $status[0] == 'DELPEB' ){
					$sql = "SELECT INTERVAL, FILE_FLAT FROM TBLLOGERROR 
						WHERE KEY ='".$status[1]."'  AND TYPE = 'DLPEB'";							
					$dataPEB = $connDB->query($sql);
					if($dataPEB->next() && $dataPEB->get('INTERVAL') < 3){
						$write_txt .= "DELPEB|".$status[1]."||||||||\r\n";
						$NOERR = $dataPEB->get('INTERVAL')+1;
						if ($NOERR==1){
							$sqllog = "INSERT INTO TBLLOGERROR (KEY, INTERVAL, KETERANGAN, DATE_LOG, TYPE, FILE_FLAT) VALUES ('".$status[1]."',".$NOERR.",'".$status[3]."',SYSDATE,'DLPEB','".$files."')";
						}else{
							$sqllog = "UPDATE TBLLOGERROR SET INTERVAL = ".$NOERR.", KETERANGAN = '".$status[3]."', DATE_LOG = SYSDATE, FILE_FLAT = '".$dataPEB->get(1)." ".$files."'  WHERE KEY = '".$status[1]."'  AND TYPE = 'DLPEB'";
						}	
					$connDB->execute($sqllog);	
					}
				}
				if ($status[2] == '01' && $status[0] == 'DELINV' ){
					$sql = "SELECT INTERVAL, FILE_FLAT FROM TBLLOGERROR 
						WHERE KEY ='".$status[1]."'  AND TYPE = 'DLINV'";							
					$dataPEB = $connDB->query($sql);
					if($dataPEB->next() && $dataPEB->get('INTERVAL') < 3){
						$write_txt .= "DELINV|".$status[1]."|||\r\n";
						$NOERR = $dataPEB->get('INTERVAL')+1;
						if ($NOERR==1){
							$sqllog = "INSERT INTO TBLLOGERROR (KEY, INTERVAL, KETERANGAN, DATE_LOG, TYPE, FILE_FLAT) VALUES ('".$status[1]."',".$NOERR.",'".$status[3]."',SYSDATE, 'DLINV','".$files."')";
						}else{
							$sqllog = "UPDATE TBLLOGERROR SET INTERVAL = ".$NOERR.", KETERANGAN = '".$status[3]."', DATE_LOG = SYSDATE, FILE_FLAT = '".$dataPEB->get(1)." ".$files."'  WHERE KEY = '".$status[1]."' AND TYPE = 'DLINV'";
						}
					$connDB->execute($sqllog);	
					}
				}
				
			}
			
			if (strtoupper($namefile[0])=='RESRTE'){ 
				// 00 sukses 01 error
				//KODE RTE|STATUS|ERROR REPORT
				$filecreate = "DOKRTE.";
				$status = explode("|",$isinya);
				if(strtoupper($status[1]) == '01'){
					$id = substr($status[0], 3, strlen($status[0]));   						
					$sql = "SELECT CASE WHEN LENGTH(p.NO_PEB) != 0 THEN p.NO_PEB ELSE 'NNNNNN' END as NO_PEB, CASE WHEN LENGTH(p.TGL_PEB) != 0 THEN TO_CHAR(p.TGL_PEB,'DD-MM-YYYY') ELSE 'NNNNNNNN' END as TGL_PEB,
							       d.REFERENCE_NUMBER, CASE WHEN r.nominal != 0 THEN r.nominal ELSE 0 END AS NOMINAL, r.NOMINAL_IDR, r.VALUTA, r.SANDI_KETERANGAN, r.KELENGKAPANDOK,
								r.FILEUPLOAD,TO_CHAR(r.TGLSEND,'DD-MM-YYYY') AS TGLSEND, 'RTE'||r.IDRTE AS IDRTE, p.NPWP as NPWP, l.INTERVAL,
								r.NOMINAL_DHE, r.NOMINAL_DHE_IDR, l.FILE_FLAT,
								TO_CHAR(d.TGL_TRANSAKSI,'DD-MM-YYYY') as TGL_TRANSAKSI,CASE WHEN LENGTH(p.KPBC) != 0 THEN p.KPBC ELSE 'NNNNNN' END as KPBC, 
								p.NAMA_EKSPORTIR
								FROM TBLFCRTE r LEFT JOIN TBLDMPEB p ON r.IDPEB = p.IDPEB
								LEFT JOIN TBLDMDANAMASUK d ON r.IDDANAMASUK = d.IDDANAMASUK
								LEFT JOIN TBLLOGERROR l ON 'RTE'||r.IDRTE = l.KEY AND l.TYPE = 'RTE'
								WHERE r.IDRTE IN (".$id.")";				
					$hasilRTE = $connDB->query($sql);
					while($hasilRTE->next()){
						$rt_peb = $hasilRTE->get("NO_PEB");
						$rt_dm = $hasilRTE->get("REFERENCE_NUMBER");
						$rt_nom = $hasilRTE->get("NOMINAL");
						$rt_nomidr = $hasilRTE->get("NOMINAL_IDR");
						$rt_valuta = $hasilRTE->get("VALUTA");
						$rt_code = $hasilRTE->get("SANDI_KETERANGAN");
						$rt_dok = $hasilRTE->get("KELENGKAPANDOK");
						$rt_file = $hasilRTE->get("FILEUPLOAD");
						$rt_tgl = $hasilRTE->get("TGLSEND");
						$rt_id = $hasilRTE->get("IDRTE");
						$rt_npwp = $hasilRTE->get("NPWP");
						$rt_error = $hasilRTE->get("INTERVAL");
						$rt_dhe = $hasilRTE->get("NOMINAL_DHE");
						$rt_dheidr = $hasilRTE->get("NOMINAL_DHE_IDR");
						$rt_flat = $hasilRTE->get("FILE_FLAT");
							$rt_tgpeb = $hasilRTE->get("TGL_PEB"); //ok
							$rt_tgdm = $hasilRTE->get("TGL_TRANSAKSI"); //ok
							$rt_kpbc = $hasilRTE->get("KPBC"); //ok
							$rt_eksportir = $hasilRTE->get("NAMA_EKSPORTIR"); //ok
							
						if($rt_error < 3){     
							$write_txt .= "RTE|".$rt_peb."|".$rt_dm."|".$rt_npwp."|".$rt_nom."|".$rt_nomidr."|".$rt_valuta."|".$rt_code."|".$rt_dok."|".$rt_file."|".$rt_tgl."|".$rt_dhe."|".$rt_dheidr."|".$rt_id."|".$rt_tgpeb."|".$rt_tgdm."|".$rt_kpbc."|".$rt_eksportir."\r\n";
							$NOERR = $rt_error+1;
							if ($NOERR==1){
								$sqllog = "INSERT INTO TBLLOGERROR (KEY, INTERVAL, KETERANGAN, DATE_LOG, TYPE, FILE_FLAT) VALUES ('".$rt_id."',".$NOERR.",'".$status[2]."',SYSDATE,'RTE','".$files."')";
							}else{
								$sqllog = "UPDATE TBLLOGERROR SET INTERVAL = ".$NOERR.", KETERANGAN = '".$status[2]."', FILE_FLAT = '".$rt_flat." ".$files."' ,DATE_LOG = SYSDATE WHERE KEY = '".$rt_id."' AND TYPE = 'RTE'";
							}
						$connDB->execute($sqllog);	
						}
					}
					
				}
			}
			if (strtoupper($namefile[0])=='DOKSTA'){ 
				// 00 accept 01 reject
				//KODE RTE|STATUS|KETERANGAN|timstamp
				$filecreate = "RESSTA.";
				$status = explode("|",$isinya);
				if(strtoupper($status[1]) == '2'){
					$id = substr($status[0], 3, strlen($status[0]));    
					$sql = "UPDATE TBLFCRTE SET STATUS = '6', KETERANGAN = '".$status[2]."' 
							WHERE IDRTE IN (".$id.")";				
					$hasilRTE = $connDB->execute($sql);
					if(!$hasilRTE){
						$sqler = "SELECT INTERVAL, FILE_FLAT FROM TBLLOGERROR WHERE KEY = '".$status[0]."' AND TYPE = 'STS'";
						$hasilLOG = $connDB->query($sqler);$hasilLOG->next();
						$NOERR = $hasilLOG->get("INTERVAL")+1;
						$fl_flat = $hasilLOG->get("FILE_FLAT");
						if ($NOERR==1){
							$sqllog = "INSERT INTO TBLLOGERROR (KEY, INTERVAL, KETERANGAN, DATE_LOG, TYPE, FILE_FLAT) VALUES ('".$status[0]."',".$NOERR.",'GAGAL UPDATE STATUS RTE',SYSDATE,'STS','".$files."')";
						}else{
							$sqllog = "UPDATE TBLLOGERROR SET INTERVAL = ".$NOERR.", KETERANGAN = 'GAGAL UPDATE STATUS RTE', DATE_LOG = SYSDATE, FILE_FLAT = '".$fl_flat." ".$files."' WHERE KEY = '".$status[0]."'  AND TYPE = 'STS'";
						}
						$connDB->execute($sqllog);
						$write_txt .= $status[0]."|01|".$files." GAGAL UPDATE STATUS RTE";
					}else{
						$write_txt .= $status[0]."|00|";
						$sql_rte = "select p.npwp, p.nama_eksportir, p.no_peb, d.reference_number, r.tglsend, u.email,
							    u.fullname, r.sandi_keterangan from tblfcrte r 
								left join tbldmpeb p on r.idpeb = p.idpeb
								left join tbldmdanamasuk d on r.iddanamasuk = d.iddanamasuk
								inner join tbluser u on (p.npwp = u.npwp) or (d.norek = u.norek) 
								where r.idrte = '$id'";
						$hasil_email = $connDB->query($sql_rte);
						while($hasil_email->next()){
						    $data_nama[]  = $hasil_email->get('fullname');
						    $data_email[] = $hasil_email->get('email');
						    $dt_npwp			= $hasil_email->get('npwp');
						    $dt_nama_eksportir		= $hasil_email->get('nama_eksportir');
						    $dt_no_peb			= $hasil_email->get('no_peb');
						    $dt_reference_number	= $hasil_email->get('reference_number');
						    $dt_tglsend			= $hasil_email->get('tglsend');
						    $dt_sandi_keterangan	= $hasil_email->get('sandi_keterangan');
						}
					$body = "
						<br>Terdapat transaksi RTE yang di reject oleh pihak bank.
						<br>Berikut informasinya :<br>
						<table style ='width:80%;'>
							<tr>
								<td style ='width:30%;' >NPWP</td>
								<td style ='width:10%;' >:</td>
								<td style ='width:60%;' >$dt_npwp</td>
							</tr>
							<tr>
								<td style ='width:30%;' >Nama Eksportir</td>
								<td style ='width:10%;' >:</td>
								<td style ='width:60%;' >$dt_nama_eksportir</td>
							</tr>
							<tr>
								<td style ='width:30%;' >No PEB</td>
								<td style ='width:10%;' >:</td>
								<td style ='width:60%;' >$dt_no_peb</td>
							</tr>
							<tr>
								<td style ='width:30%;' >No Referensi Dana</td>
								<td style ='width:10%;' >:</td>
								<td style ='width:60%;' >$dt_reference_number</td>
							</tr>
							<tr>
								<td style ='width:30%;' >Tgl Pelaporan</td>
								<td style ='width:10%;' >:</td>
								<td style ='width:60%;' >$dt_tglsend</td>
							</tr>
							<tr>
								<td style ='width:30%;' >Sandi Keterangan</td>
								<td style ='width:10%;' >:</td>
								<td style ='width:60%;' >$dt_sandi_keterangan</td>
							</tr>
							<tr>
								<td style ='width:30%;' >Keterangan Reject</td>
								<td style ='width:10%;' >:</td>
								<td style ='width:60%;' >$status[2]</td>
							</tr>
						</table>
						<br>Silakan cek kembali data RTE pada aplikasi.
						<br><br>Terimakasih<br><br><b>Admin E-RTE SCB </b><br><br>";	    
					$subject = "Reminder Reject RTE SCB";
					$to = implode(";",$data_email);
					$kepada = implode(";", $data_nama);
					send_Email(array('to'=>$to,'subject'=>$subject,'isi'=>$body,'kepada'=>$kepada));			
					}
				}
				
			}
			if (strtoupper($namefile[0])=='DOKDM'){ 
				// 00 sukses 01 error
				//REFERENCE_NUMBER|TGL_TRANSAKSI|NAMA_PEMILIK|NO_REK|VALUTA_TRANSFER|NOMINAL_TRANSFER|VALUTA_DITERIMA|NOMINAL_DITERIMA|NAMA_PENGIRIM|NAMA_BANK_PENGIRIM|BERITA|ENTRY_BY|ENTRY_DATE|UPDATE_BY|UPDATE_DATE|FL_STATUS|ERR_MSG
				//901151423222|24-08-2013|Juni Fajarwati|30607648789|IDR |50000|IDR |50000|-|-|-|100000051|23-08-2013|100000051|23-08-2013||
				
				$filecreate = "RESDM.";
				$data = explode("|",$isinya);
				$sql = "SELECT * FROM TBLDMDANAMASUK WHERE REFERENCE_NUMBER = '".$data[0]."'";
				$cekDM = $connDB->query($sql);
				if($cekDM->size()>0){
					$sql = "UPDATE TBLDMDANAMASUK SET TGL_TRANSAKSI = to_date('$data[1]','DD-MM-YYYY'), IDLLD = '$data[0]', NAMA_PEMILIK = '$data[2]',
						NOREK = '$data[3]', VALUTA_TRANSFER = '".trim($data[4])."', NOMINAL_TRANSFER = '$data[5]', VALUTA_DITERIMA = '".trim($data[4])."',
						NOMINAL_DITERIMA = '$data[5]', NAMA_PENGIRIM = '$data[8]', NAMA_BANK_PENGIRIM = '$data[9]', BERITA = '$data[10]'
						WHERE REFERENCE_NUMBER = '$data[0]'";
				}else{
					$sql = "INSERT INTO TBLDMDANAMASUK (REFERENCE_NUMBER, TGL_TRANSAKSI, IDLLD, NAMA_PEMILIK, NOREK, VALUTA_TRANSFER, NOMINAL_TRANSFER,
						VALUTA_DITERIMA, NOMINAL_DITERIMA, NAMA_PENGIRIM, NAMA_BANK_PENGIRIM, BERITA, FLAG_USED, KD_DANA, JNS_PEMBAYARAN, JNS_UANGMUKA) VALUES ('$data[0]',to_date('$data[1]','DD-MM-YYYY'),'$data[0]','$data[2]',
						'$data[3]', '$data[4]', '$data[5]', '$data[4]', '$data[5]', '$data[8]', '$data[9]', '$data[10]','0','00','0','0')";
				}
				$hasilDM = $connDB->execute($sql);
				if ($hasilDM){
				   $write_txt .= "RESDM|".$data[0]."|00|\r\n";		    
				}else{
				    $sql = "SELECT INTERVAL, FILE_FLAT FROM TBLLOGERROR WHERE KEY= '$data[0]' AND TYPE = 'DM' ";	    
				    $dterror = $connDB->query($sql);$dterror->next();
				    $NOERR = $dterror->get('INTERVAL')+1;
				    if($NOERR <= 3){
					$write_txt .= "RESDM|".$data[0]."|01|".$files." ERROR INSERT OR UPDATE DANA MASUK \r\n";
					$fl_flat = $dterror->get('FILE_FLAT');
					if ($NOERR==1){
						$sqllog = "INSERT INTO TBLLOGERROR (KEY, INTERVAL, KETERANGAN, DATE_LOG, TYPE, FILE_FLAT) VALUES ('".$data[0]."',".$NOERR.",'".$status[2]."',SYSDATE,'DM','".$files."')";
					}else{
						$sqllog = "UPDATE TBLLOGERROR SET INTERVAL = ".$NOERR.", KETERANGAN = '".$status[2]."', DATE_LOG = SYSDATE, FILE_FLAT = '".$fl_flat." ".$files."' WHERE KEY = '".$data[0]."' AND TYPE = 'DM' ";
					}
					$connDB->execute($sqllog);
				    }else{
					$body = "Yth. Administrator Bank,
					<br>Terjadi kegagalan dalam pengiriman Dana Masuk ke Front End RTE SCB .
					<br>Berikut informasinya :<br> 
						<table style ='width:80%;'>
							<tr>
								<td style ='width:30%;' >No Referensi Dana</td>
								<td style ='width:10%;' >:</td>
								<td style ='width:60%;' >$data[0]</td>
							</tr>
							<tr>
								<td style ='width:30%;' >REL ID</td>
								<td style ='width:10%;' >:</td>
								<td style ='width:60%;' >$data[3]</td>
							</tr>
							<tr>
								<td style ='width:30%;' >Nama Pemilik</td>
								<td style ='width:10%;' >:</td>
								<td style ='width:60%;' >$data[2]</td>
							</tr>
						</table>	
						<br>Silakan cek kembali pengiriman dana masuk pada Back End.
						<br><br>Terimakasih<br><br><b>Admin E-RTE SCB </b><br><br>";	    
					$subject = "Gagal Dana Masuk ke Front End RTE SCB";
					sendEmail(array('to'=>$email_operator,'subject'=>$subject,'isi'=>$body));	
				    }
					
				    
				}
				
				
			}
			if (strtoupper($namefile[0])=='RESACC'){ 
			
				$filecreate = "DOKACC.";
				$status = explode("|",$isinya);
				// 00 sukses 01 error
				//COM|NPWP|STATUS|ERROR REPORT
				if ($status[2] == '01' && $status[0] == 'COM' ){
					$sql = "SELECT p.NPWP, p.NAMA, p.ADDRESS, p.CITY, p.ZIPCODE, p.ID, p.PIC, p.PIC_EMAIL, p.PIC_PHONE, p.FAX_NUMBER,
						    r.interval, r.file_flat
						    FROM TCOMPANY p 
						    LEFT JOIN TBLLOGERROR r ON p.NPWP = r.key AND r.TYPE = 'COM'
						    WHERE p.NPWP ='".$status[1]."'";							
					$dataCom = $connDB->query($sql);
					if($dataCom->next() && $dataCom->get('interval') < 3){
					    $npwp = $dataCom->get('NPWP');
					    $nama = $dataCom->get('NAMA');
					    $address = $dataCom->get('ADDRESS');
					    $city = $dataCom->get('CITY');
					    $zipcode = $dataCom->get('ZIPCODE'); 
					    $id = $dataCom->get('ID');
					    $pic_ = $dataCom->get('PIC');
					    $picEmail_ = $dataCom->get('PIC_EMAIL');
					    $picPhone_ = $dataCom->get('PIC_PHONE');
					    $picFax_ = $dataCom->get('FAX_NUMBER');
						$write_txt .= "COM|".$npwp."|".$nama."|".$address."|".$city."|".$zipcode."|".$id."|".$pic_."|".$picEmail_."|".$picPhone_."|".$picFax_." \r\n";
						$NOERR = $dataCom->get('interval')+1;
						if ($NOERR==1){
							$sqllog = "INSERT INTO TBLLOGERROR (KEY, INTERVAL, KETERANGAN, DATE_LOG, TYPE, FILE_FLAT) VALUES ('".$status[1]."',".$NOERR.",'".$status[3]."',SYSDATE,'COM','".$files."')";
						}else{
							$fl_file = $dataCom->get('file_flat');	
							$sqllog = "UPDATE TBLLOGERROR SET INTERVAL = ".$NOERR.", KETERANGAN = '".$status[3]."', DATE_LOG = SYSDATE, FILE_FLAT = '".$fl_file." ".$files."'  WHERE KEY = '".$status[1]."' and TYPE = 'COM'";
						}
					$connDB->execute($sqllog);			
					}
				}
				// 00 sukses 01 error
				//ACC|ID|STATUS|ERROR REPORT
				if ($status[2] == '01' && $status[0] == 'ACC' ){
				   	$sql = "SELECT p.ID, p.NPWP, p.ACCOUNT,
						    r.interval, r.file_flat
						    FROM TACCOUNT p 
						    LEFT JOIN TBLLOGERROR r ON p.ID = r.key AND r.TYPE = 'ACC'
						    WHERE p.ID ='".$status[1]."'";							
					$dataCom = $connDB->query($sql);
					if($dataCom->next() && $dataCom->get('interval') < 3){
					    $npwp = $dataCom->get('NPWP');
					    $id = $dataCom->get('ID');
					    $account = $dataCom->get('ACCOUNT');
						$write_txt .= "ACC|".$npwp."|".$account."|".$id." \r\n";
						$NOERR = $dataCom->get('interval')+1;
						if ($NOERR==1){
							$sqllog = "INSERT INTO TBLLOGERROR (KEY, INTERVAL, KETERANGAN, DATE_LOG, TYPE, FILE_FLAT) VALUES ('".$status[1]."',".$NOERR.",'".$status[3]."',SYSDATE,'ACC','".$files."')";
						}else{
							$fl_file = $dataCom->get('file_flat');	
							$sqllog = "UPDATE TBLLOGERROR SET INTERVAL = ".$NOERR.", KETERANGAN = '".$status[3]."', DATE_LOG = SYSDATE, FILE_FLAT = '".$fl_file." ".$files."'  WHERE KEY = '".$status[1]."' AND TYPE = 'ACC'";
						}
					$connDB->execute($sqllog);		
					}
				}
			
			}
			if (strtoupper($namefile[0])=='DOKKURS'){
			    $filecreate = "RESKURS.";
				$status = explode("|",$isinya);
				$sql = "SELECT * FROM TBLDMKURS WHERE KODE = '$status[0]'";
				$cekkurs = $connDB->query($sql);
				if($cekkurs->size()>0){
					$sql = "UPDATE TBLDMKURS SET VALUTA = '$status[1]', TGLAWAL = TO_DATE('$status[2]','DD-MM-YYYY'), NOMINAL='$status[3]', TGLAKHIR = TO_DATE('$status[4]','DD-MM-YYYY'), LASTUPDATE=SYSDATE
						WHERE KODE = '$status[0]'";
				}else{
					$sql = "INSERT INTO TBLDMKURS (VALUTA, TGLAWAL, NOMINAL, TGLAKHIR, LASTUPDATE, KODE) VALUES ('$status[1]',TO_DATE('$status[2]','DD-MM-YYYY'),'$status[3]',
						TO_DATE('$status[4]','DD-MM-YYYY'),SYSDATE,'$status[0]')";
				}
				$hasilDM = $connDB->execute($sql);
				if ($hasilDM){
				    $write_txt .= "RESKURS|".$status[0]."|00|\r\n";		    
				}else{
				    $sql = "SELECT INTERVAL, FILE_FLAT FROM TBLLOGERROR WHERE KEY= '$status[0]' AND TYPE = 'KURS' ";	    
				    $dterror = $connDB->query($sql); $dterror->next();
				    $NOERR = $dterror->get('INTERVAL')+1;
				    if($NOERR <= 3){
					$write_txt .= "RESKURS|".$status[0]."|01|".$files." ERROR INSERT OR UPDATE KURS \r\n";
					$fl_flat = $dterror->get('FILE_FLAT');
					if ($NOERR==1){
						$sqllog = "INSERT INTO TBLLOGERROR (KEY, INTERVAL, KETERANGAN, DATE_LOG, TYPE, FILE_FLAT) VALUES ('".$status[0]."',".$NOERR.",'GAGAL INSERT OR UPDATE',SYSDATE,'KURS','".$files."')";
					}else{
						$sqllog = "UPDATE TBLLOGERROR SET INTERVAL = ".$NOERR.", KETERANGAN = 'GAGAL INSERT OR UPDATE', DATE_LOG = SYSDATE, FILE_FLAT = '".$fl_flat." ".$files."' WHERE KEY = '".$status[0]."'  AND TYPE = 'KURS'";
					}
					$connDB->execute($sqllog);  
				    }
					
				    
				}
 
			  
			}
			
			if (strtoupper($namefile[0])=='RESFRONT'){
			    $filecreate = "DOKFRONT.";
				$status = explode("|",$isinya);
				// 00 sukses 01 error
				//COM|NPWP|STATUS|ERROR REPORT
				if ($status[1] == '01' ){
						$write_txt .= "DELRTE|".$status[0]." \r\n";
						$sql = "select interval, file_flat from TBLLOGERROR where key = '$status[0]' and type = 'DELRTE'";
						$dataCom = $connDB->query($sql);$dataCom->next();
						$NOERR = $dataCom->get('interval')+1;
						if ($NOERR==1){
							$sqllog = "INSERT INTO TBLLOGERROR (KEY, INTERVAL, KETERANGAN, DATE_LOG, TYPE, FILE_FLAT) VALUES ('".$status[0]."',".$NOERR.",'".$status[2]."',SYSDATE,'DELRTE','".$files."')";
						}else{
							$fl_file = $dataCom->get('file_flat');	
							$sqllog = "UPDATE TBLLOGERROR SET INTERVAL = ".$NOERR.", KETERANGAN = '".$status[2]."', DATE_LOG = SYSDATE, FILE_FLAT = '".$fl_file." ".$files."'  WHERE KEY = '".$status[0]."' and TYPE = 'DELRTE'";
						}
						$connDB->execute($sqllog);    	
				}
			}
			if (strtoupper($namefile[0])=='DOKFLAG'){
			    $filecreate = "RESFLAG.";
				$status = explode("|",$isinya);
				$id = substr($status[0], 3, strlen($status[0]));    
				$sql = "UPDATE TBLFCRTE SET STATUS = '".$status[1]."', KELENGKAPANDOK = '1', FILEUPLOAD = '".$status[2]."' 
						WHERE IDRTE IN (".$id.")";
				$hasilFlag = $connDB->execute($sql);			
				if ($hasilFlag){
				    $write_txt .= $status[0]."|00|\r\n";		    
				}else{
				    $sql = "SELECT INTERVAL, FILE_FLAT FROM TBLLOGERROR WHERE KEY= '$status[0]' AND TYPE = 'PENDING' ";	    
				    $dterror = $connDB->query($sql);$dterror->next();
				    $NOERR = $dterror->get('INTERVAL')+1;
				    if($NOERR <= 3){
					$write_txt .= $status[0]."|01|".$files." ERROR INSERT OR UPDATE RTE PENDING \r\n";
					$fl_flat = $dterror->get('FILE_FLAT');
					if ($NOERR==1){
						$sqllog = "INSERT INTO TBLLOGERROR (KEY, INTERVAL, KETERANGAN, DATE_LOG, TYPE, FILE_FLAT) VALUES ('".$status[0]."',".$NOERR.",'GAGAL INSERT OR UPDATE',SYSDATE,'PENDING','".$files."')";
					}else{
						$sqllog = "UPDATE TBLLOGERROR SET INTERVAL = ".$NOERR.", KETERANGAN = 'GAGAL INSERT OR UPDATE', DATE_LOG = SYSDATE, FILE_FLAT = '".$fl_flat." ".$files."' WHERE KEY = '".$status[0]."'  AND TYPE = 'PENDING'";
					}
					$connDB->execute($sqllog);  
				    }
					
				    
				}
			}
			if (strtoupper($namefile[0])=='RESRELL'){
			    $filecreate = "DOKRELL.";
				$status = explode("|",$isinya);
				// 00 sukses 01 error
				if ($status[2] == '01' ){
						$sql = "select * from taccount where account ='".$status[1]."' ";
						$datack = $connDB->query($sql);
						if ($datack->size()>0){
							$write_txt .= "DOKRELL|".$status[1]."||1 \r\n";
						}else{
							$write_txt .= "DOKRELL|".$status[1]."||0 \r\n";
						}
						$sql = "select interval, file_flat from TBLLOGERROR where key = '".$status[1]."' and type = 'RELLID'";
						$dataCom = $connDB->query($sql);
						$dataCom->next();
						$NOERR = $dataCom->get('interval')+1;
						if ($NOERR==1){
							$sqllog = "INSERT INTO TBLLOGERROR (KEY, INTERVAL, KETERANGAN, DATE_LOG, TYPE, FILE_FLAT) VALUES ('".$status[1]."',".$NOERR.",'".$status[3]."',SYSDATE,'RELLID','".$files."')";
						}else{
							$fl_file = $dataCom->get('file_flat');	
							$sqllog = "UPDATE TBLLOGERROR SET INTERVAL = ".$NOERR.", KETERANGAN = '".$status[3]."', DATE_LOG = SYSDATE, FILE_FLAT = '".$fl_file." ".$files."'  WHERE KEY = '".$status[1]."' and TYPE = 'RELLID'";
						}
						$connDB->execute($sqllog);    	
				}
			}
			
			if (strtoupper($namefile[0])=='RESREPORT'){
			    $filecreate = "DOKREPORT.";
				$status = explode("|",$isinya);
				// 00 sukses 01 error
				if ($status[2] == '01' ){
						$sql = "select reference_number, flag_used, kd_dana, jns_pembayaran, jns_uangmuka, flag_uangsisa, dana_sisa, kode_nonekspor 
								FROM tbldmdanamasuk where reference_number = '".$status[1]."'";
						$datadm = $connDB->query($sql);
						if ($datadm->next()){
							$reff 		= trim($datadm->get('reference_number'));
							$used 		= trim($datadm->get('flag_used'));
							$dana 		= trim($datadm->get('kd_dana'));
							$pembayaran = trim($datadm->get('jns_pembayaran'));
							$uangmuka 	= trim($datadm->get('jns_uangmuka'));
							$uangsisa 	= trim($datadm->get('flag_uangsisa'));
							$danasisa 	= trim($datadm->get('dana_sisa'));
							$sandi 		= trim($datadm->get('kode_nonekspor'));
							$write_txt .= "DOKREPORT|".$reff."|".$used."|".$dana."|".$pembayaran."|".$uangmuka."|".$uangsisa."|".$danasisa."|".$sandi."\r\n";
						}
						$sql = "select interval, file_flat from TBLLOGERROR where key = '".$status[1]."' and type = 'DOKREPORT'";
						$dataCom = $connDB->query($sql);
						$dataCom->next();
						$NOERR = $dataCom->get('interval')+1;
						if ($NOERR==1){
							$sqllog = "INSERT INTO TBLLOGERROR (KEY, INTERVAL, KETERANGAN, DATE_LOG, TYPE, FILE_FLAT) VALUES ('".$status[1]."',".$NOERR.",'".$status[3]."',SYSDATE,'DOKREPORT','".$files."')";
						}else{
							$fl_file = $dataCom->get('file_flat');	
							$sqllog = "UPDATE TBLLOGERROR SET INTERVAL = ".$NOERR.", KETERANGAN = '".$status[3]."', DATE_LOG = SYSDATE, FILE_FLAT = '".$fl_file." ".$files."'  WHERE KEY = '".$status[1]."' and TYPE = 'DOKREPORT'";
						}
						$connDB->execute($sqllog);    	
				}
			}
			
			
		}
		if (strlen($write_txt) > 0 ){	
			writetxt($write_txt,$filecreate,'GW');
			$write_txt = '';
			$place = $folderlokalinbound.$files;
			unlink($place);
		}else{
			$place = $folderlokalinbound.$files;
			unlink($place);
		}
	}
}
closedir($dirinbound);


$connDB->disconnect();

//Tulis log GW
$filename 	= fopen($fileLog, 'a');
fputs($filename, $log_txt);
fclose($filename);


?>
