<?php
//ini_set( 'error_reporting', E_ALL ^ E_NOTICE );
ini_set( 'display_errors', '1' ); 

	//koneksi database
	PutEnv("TNS_ADMIN=/opt/lampp/etc"); 
	require_once("../sendEmail.php");
	require_once("../library/dbLite/DBManager.php");
	$conn = new ORA8Access();
	$conn->parseURL("db.OCI8://rtescbprod:Rt3scbpr0d@IDXDB2");
	
//$mail_cs = "ruslan.nasution@edi-indonesia.co.id";
function writetxt($data,$name,$group){
	$Dokname 	= $name.$group.".".date('ymdHis').".FLT";	 
	$file 		= '/home/RTESCB1/TOBACKEND/'.$Dokname;
	$filebac 	= '/home/RTESCB1/BEBACKUP/'.$Dokname;
	
	//$file 		= '/opt/lampp/htdocs/TaxSCB/RTE/scheduller/TOBACKEND/'.$Dokname;
	//$filebac 	= '/opt/lampp/htdocs/TaxSCB/RTE/scheduller/BEBACKUP/'.$Dokname;
	
	$filename 	= fopen($file, 'w');
	fputs($filename, $data);
	fclose($filename);
	
	$filenamebac 	= fopen($filebac, 'w');
	fputs($filenamebac, $data);
	fclose($filenamebac);
	
	echo date('Ymd').'-Berhasil-'.$Dokname;
	
	return true;
}

$mail_cs = "sarna@edi-indonesia.co.id";
$conn->connect();
$hari = array(3,7,14);
//   CEK DANA MASUK LEBIH DARI 3,7,14 HARI
//
//for($z=0;$z<3;$z++){
//	
//	 $sql2= "select c.npwp from taccount c inner join tbldmdanamasuk d on c.account = d.norek 
//			where  FLOOR(datediff(sysdate,d.dtcreated)) = ".$hari[$z]." and d.kd_dana = '00'  and d.flag_email = ".$z." and freeze = '1' group by c.npwp";
//	$data2 = $conn->query($sql2);
//	while($data2->next()){
//		$npwp  = $data2->get("npwp");
//		 $sql = "SELECT  dna.iddanamasuk,dna.iddanamasuk, dna.reference_number, 
//				dna.nama_pengirim, 
//				TO_CHAR(dna.tgl_transaksi,'DD-MM-YYYY') as tgl_transaksi, 
//				dna.valuta_transfer ||' '|| dna.nominal_transfer as Nominal_transfer , 
//				dna.valuta_diterima ||' '|| dna.nominal_diterima as Nominal_diterima , 
//				dna.nama_bank_pengirim, 
//				dna.berita 
//			from tbldmdanamasuk dna inner join taccount act on act.account = dna.norek 
//			where dna.flag_email = ".$z." and FLOOR(datediff(sysdate,dna.dtcreated)) = ".$hari[$z]." 
//				and act.npwp = '".$npwp."' and dna.kd_dana = '00'";
//		
//		$data_dana = $conn->query($sql);
//		while($data_dana->next()){
//			$data_alert ['nama'][]  = $data_dana->get("nama_pengirim");
//			$data_alert ['tgl'][]   = $data_dana->get("tgl_transaksi");
//			$data_alert ['kirim'][]   = $data_dana->get("Nominal_transfer");
//			$data_alert ['terima'][]   = $data_dana->get("Nominal_diterima");
//			$data_alert ['bank'][]   = $data_dana->get("nama_bank_pengirim");
//			$data_alert ['berita'][]   = $data_dana->get("berita");
//			$data_alert ['id'][]   = $data_dana->get("reference_number");
//			$data_alert ['key'][]   = $data_dana->get("iddanamasuk");
//		}
//		
//		if($data_dana->size()>0){	
//			$table = '
//					<html>
//						<head>
//						<style>
//						table
//						{
//						border-collapse:collapse;
//						}
//						table, td, th
//						{
//						border:1px solid black;
//						}
//						</style>
//					</head>
//						<table style = "width = 100%;border: 1px ;">
//				<tr align="center" bgcolor ="#AFAFB0">
//					<td width = "10%">No Referensi</td>
//					<td width = "20%">Nama Pengirim</td>
//					<td width = "10%">Tanggal Transaksi</td>
//					<td width = "15%">Nominal Transfer</td>
//					<td width = "15%">Nominal Diterima</td>
//					<td width = "20%">Bank Pengirim</td>
//					<td width = "20%">Berita</td>
//				</tr>';
//			for($i=0;$i<count( $data_alert ['nama']);$i++){
//				
//				if ( $i % 2 == 0 ){
//					$color = "#FFFFFF";
//				} else {
//					$color = "#D3D1FB";
//				}
//				$table .= '
//				<tr bgcolor="'.$color.'">
//					<td width = "10%">'.$data_alert ['id'][$i].'</td>
//					<td width = "20%">'.$data_alert ['nama'][$i].'</td>
//					<td width = "10%">'.$data_alert ['tgl'][$i].'</td>
//					<td width = "15%">'.$data_alert ['kirim'][$i].'</td>
//					<td width = "15%">'.$data_alert ['terima'][$i].'</td>
//					<td width = "20%">'.$data_alert ['bank'][$i].'</td>
//					<td width = "20%">'.$data_alert ['berita'][$i].'</td>
//				</tr>';
//				
//			}
//			$table .= ' </table>';
//			$sql= "select email,fullname from tbluser where npwp = '$npwp' and notif = 1";
//			$data = $conn->query($sql);
//			while($data->next()){
//				$email[] = $data->get("email");
//				$name [] = $data->get("fullname");
//			}
//			$id_flag = "'".implode("','",$data_alert ['key'])."'"; 
//			$sts_flag = $z+1;
//			$sql_flag = "update tbldmdanamasuk set flag_email = ".$sts_flag." where iddanamasuk in (".$id_flag.")";
//			$flag = $conn->execute($sql_flag);
//			if($flag){
//				$to = implode(";",$email);
//				$kepada = implode(";",$name);
//				$subject = "Reminder on DHE is not yet Received";								
//				$body = "	<br><br>Dear Valued Customers,
//							<i><br>Kepada nasabah yang terhormat,<br></i>
//							
//					
//					<br>Referring to your RTE report with the mentioned on attachment,  
//					<i><br>Merujuk pada laporan RTE seperti disebutkan pada lampiran.</i>
//					<br>Please be informed that your DHE is not received yet while the cut of date of DHE received has reached the overdue.
//					<i><br>Sebagai informasi bahwa DHE belum diterima sedangkan batas waktu penerimaan sudah melampaui batas tanggal penerimaan.</i>
//					<br> ".$table."<br>
//					<br>Kindly need your cooperation to complete the supporting documentation as an additional data that required by Regulatory (Central Bank).
//					<i><br>Mohon kerjasamanya yang baik untuk melengkapi dokumen pendukung sebagai tambahan data yang diperlukan sesuai dengan ketentuan pada peraturan Bank Indonesia.</i>
//					<br>We would like to remind you for completing the RTE report to avoid any penalty from the Regulatory (Central Bank).
//					<i><br>Kami ingin mengingatkan kembali untuk melengkapi pelaporan RTE untuk menghindari denda dari Bank Indonesia.</i>
//					<br>Really appreciate your good cooperation and assistance.
//					<i><br>Kami sangat menghargai kerjasamanya yang baik dan bantuannya. </i>
//
//					<br><br>Thank you and Best regards,
//					<i><br>Terimakasih dan hormat kami,	</i>
//					<br>Business Support Unit 
//					<br>Standard Chartered Bank Indonesia ";
//				//send_Email(array('to'=>$to,'subject'=>$subject,'isi'=>$body,'kepada'=>$kepada));	
//			}
//		}	
//		$data_alert=array();
//		$email = array();
//		$name  = array();	
//	}
//}
////   CEK RTE PENDING LEBIH DARI 3,7,14 HARI
//for($z=0;$z<3;$z++){
//	$sql2= "select p.npwp from tblfcrte r inner join tbldmpeb p  on r.idpeb = p.idpeb
//	where FLOOR(datediff(sysdate, r.dtcreated)) = ".$hari[$z]." and r.fl_email = ".$z." group by p.npwp";
//	$data2 = $conn->query($sql2);
//	while($data2->next()){
//		$npwp  	= $data2->get("npwp");
//		$sql 	= "SELECT p.NO_PEB, d.REFERENCE_NUMBER, r.NOMINAL, r.NOMINAL_IDR, r.VALUTA, r.SANDI_KETERANGAN, r.KELENGKAPANDOK,
//					r.FILEUPLOAD,TO_CHAR(r.TGLSEND,'DD-MM-YYYY') AS TGLSEND, r.IDRTE AS IDRTE, 
//					r.NOMINAL_DHE, r.NOMINAL_DHE_IDR, p.NAMA_EKSPORTIR, p.KPBC, TO_CHAR(p.TGL_PEB,'DD-MM-YYYY') AS TGL_PEB, p.FOB
//					FROM TBLFCRTE r LEFT JOIN TBLDMPEB p ON r.IDPEB = p.IDPEB
//					LEFT JOIN TBLDMDANAMASUK d ON r.IDDANAMASUK = d.IDDANAMASUK
//		WHERE p.NPWP = '".$npwp."' AND FLOOR(datediff(sysdate, r.dtcreated)) = ".$hari[$z]." and r.fl_email = ".$z." and r.status = '3' ";
//		
//		$hasilRTE = $conn->query($sql);
//		while($hasilRTE->next()){
//
//			$data_alert ['NO_PEB'][] 		= $hasilRTE->get("NO_PEB");
//			$data_alert ['REFERENCE_NUMBER'][] 	= $hasilRTE->get("REFERENCE_NUMBER");
//			$data_alert ['NOMINAL'][] 		= $hasilRTE->get("NOMINAL");
//			$data_alert ['NOMINAL_IDR'][] 		= $hasilRTE->get("NOMINAL_IDR");
//			$data_alert ['VALUTA'][] 		= $hasilRTE->get("VALUTA");
//			$data_alert ['SANDI_KETERANGAN'][] 	= $hasilRTE->get("SANDI_KETERANGAN");
//			$data_alert ['KELENGKAPANDOK'][] 	= $hasilRTE->get("KELENGKAPANDOK");
//			$data_alert ['FILEUPLOAD'][] 		= $hasilRTE->get("FILEUPLOAD");
//			$data_alert ['TGLSEND'][] 		= $hasilRTE->get("TGLSEND");
//			$data_alert ['IDRTE'][] 		= $hasilRTE->get("IDRTE");
//			$data_alert ['NPWP'][] 			= $npwp;
//			$data_alert ['NOMINAL_DHE'][] 		= $hasilRTE->get("NOMINAL_DHE");
//			$data_alert ['NOMINAL_DHE_IDR'][] 	= $hasilRTE->get("NOMINAL_DHE_IDR");
//			$data_alert ['NAMA_EKSPORTIR'][] 	= $hasilRTE->get("NAMA_EKSPORTIR");
//			$data_alert ['KPBC'][] 			= $hasilRTE->get("KPBC");
//			$data_alert ['TGL_PEB'][] 		= $hasilRTE->get("TGL_PEB");
//			$data_alert ['FOB'][] 			= $hasilRTE->get("FOB");
//			
//		}
//		
//		if($hasilRTE->size()>0){	
//			$table = '
//					<html>
//						<head>
//						<style>
//						table
//						{
//						border-collapse:collapse;
//						}
//						table, td, th
//						{
//						border:1px solid black;
//						}
//						</style>
//					</head>
//						<table style = "width = 100%;border: 1px ;">
//				<tr align="center" bgcolor ="#AFAFB0">
//						<td width = "10%">No. Referensi</td>
//						<td width = "10%">NPWP Transaksi</td>
//						<td width = "15%">Nama</td>
//						<td width = "10%">KPBC</td>
//						<td width = "10%">No. PEB</td>
//						<td width = "10%">Tgl. PEB</td>
//						<td width = "5%">Valuta</td>
//						<td width = "10%">Nilai DHE</td>
//						<td width = "10%">Nilai PEB</td>
//						<td width = "5%">Sandi</td>
//						<td width = "5%">Kelengkapan Dok.</td>
//				</tr>';
//			for($i=0;$i<count( $data_alert ['IDRTE']);$i++){
//				if ( $i % 2 == 0 ){
//					$color = "#FFFFFF";
//				} else {
//					$color = "#D3D1FB";
//				}
//				$table .= '
//				<tr bgcolor="'.$color.'">
//						<td width = "10%">'.$data_alert ['REFERENCE_NUMBER'][$i].'</td>
//						<td width = "10%">'.$data_alert ['NPWP'][$i].'</td>
//						<td width = "15%">'.$data_alert ['NAMA_EKSPORTIR'][$i].'</td>
//						<td width = "10%">'.$data_alert ['KPBC'][$i].'</td>
//						<td width = "15%">'.$data_alert ['NO_PEB'][$i].'</td>
//						<td width = "10%">'.$data_alert ['TGL_PEB'][$i].'</td>
//						<td width = "5%">'.$data_alert ['VALUTA'][$i].'</td>
//						<td width = "10%">'.$data_alert ['NOMINAL_DHE'][$i].'</td>
//						<td width = "10%">'.$data_alert ['FOB'][$i].'</td>
//						<td width = "5%">'.$data_alert ['SANDI_KETERANGAN'][$i].'</td>
//						<td width = "5%">'.$data_alert ['KELENGKAPANDOK'][$i].'</td>
//					
//					
//				</tr>';
//				
//			}
//			$table .= ' </table>';
//			$sql= "select email,fullname from tbluser where npwp = '$npwp' and notif = 1 ";
//			$data = $conn->query($sql);
//			while($data->next()){
//				$email[] = $data->get("email");
//				$name [] = $data->get("fullname");
//			}
//			$id_flag = "'".implode("','",$data_alert ['IDRTE'])."'"; 
//			$sts_rte = $z+1;
//			$sql_flag = "update tblfcrte set fl_email = ".$sts_rte." where idrte in (".$id_flag.")";
//			$flag = $conn->execute($sql_flag);
//			if($flag){
//				$to = implode(";",$email);
//				$kepada = implode(";",$name);
//				$subject = "Reminder on Supporting Docs not yet received";								
//				$body = "
//					<br>Dear Valued Customers,
//					<i><br>Kepada Nasabah terhormat,<br></i>
//				
//				<br>Referring to your RTE report with the mentioned on attachment,  
//				<i><br>Merujuk pada laporan RTE yang tersebut pada lampiran  </i>
//				<br>Please be informed that the supporting document is not submitted yet while the cut of date of submission has reached the overdue.
//				<i><br>Sebagai informasi bahwa dokumen pendukung belum di serahkan sedangkan tanggal penyerahan sudah melampaui masanya.  </i>
//				<br>Kindly need your cooperation to complete the supporting documentation as an additional data that required by Regulatory (Central Bank).
//				<i><br>Mohon kerjasamanya yang baik untuk melengkapi dokumen pendukung sebagai tamabahan data yang diperlukan oleh Bank Indonesia. </i>
//				<br> ".$table."<br>
//				<br>We would like to remind you for completing the RTE report to avoid any penalty from the Regulatory (Central Bank).
//				<i><br>Kami ingin mengingatkan nasabah eksportir untuk melengkapi laporan RTE untuk menghindari denda dari Bank Indonesia. </i>
//				<br>Really appreciate your good cooperation and assistance.
//				<i><br>Kami sangat menghargai kerjasamanya yang baik dan bantuannya.</i>
//
//				<br><br>Thank you and Best regards,
//				<i><br>Terimakasih dan hormat kami,	</i>
//				<br>Business Support Unit 
//				<br>Standard Chartered Bank Indonesia
//				";	
//				//send_Email(array('to'=>$to,'subject'=>$subject,'isi'=>$body,'kepada'=>$kepada));	
//			}
//		}	
//		$data_alert=array();
//		$email = array();
//		$name  = array();	
//	}
//}
//
////   CEK PEB LEBIH DARI 3,7,14 HARI
//for($z=0;$z<3;$z++){
//	$sql2= "select p.npwp from tbldmpeb p  
//			where FLOOR(datediff(sysdate, p.dtcreated)) > ".$hari[$z]." and p.fl_email = ".$z." and p.flag_used = 0";
//	$data2 = $conn->query($sql2);
//	while($data2->next()){
//		$npwp  	= $data2->get("npwp");
//		$sql 	= "SELECT p.IDPEB, p.NO_PEB, p.NPWP, p.NAMA_EKSPORTIR, p.KPBC, TO_CHAR(p.TGL_PEB,'DD-MM-YYYY') AS TGL_PEB, p.FOB
//					FROM tbldmpeb p 
//					where FLOOR(datediff(sysdate, p.dtcreated)) > ".$hari[$z]." and p.fl_email = ".$z." and p.flag_used = 0";
//		
//		$hasilRTE = $conn->query($sql);
//		while($hasilRTE->next()){
//			$data_alert ['NO_PEB'][] 				= $hasilRTE->get("NO_PEB");
//			$data_alert ['NPWP'][] 					= $hasilRTE->get("NPWP");
//			$data_alert ['NAMA_EKSPORTIR'][] 		= $hasilRTE->get("NAMA_EKSPORTIR");
//			$data_alert ['KPBC'][] 					= $hasilRTE->get("KPBC");
//			$data_alert ['TGL_PEB'][] 				= $hasilRTE->get("TGL_PEB");
//			$data_alert ['FOB'][] 					= $hasilRTE->get("FOB");
//			$data_alert ['IDPEB'][] 				= $hasilRTE->get("IDPEB");			
//		}
//		
//		if($hasilRTE->size()>0){	
//			$table = '
//					<html>
//						<head>
//						<style>
//						table
//						{
//						border-collapse:collapse;
//						}
//						table, td, th
//						{
//						border:1px solid black;
//						}
//						</style>
//					</head>
//						<table style = "width = 100%;border: 1px ;">
//				<tr align="center" bgcolor ="#AFAFB0">
//						<td width = "15%">No. PEB</td>
//						<td width = "20%">NPWP</td>
//						<td width = "20%">Nama Eksportir</td>
//						<td width = "10%">KPBC</td>
//						<td width = "15%">Tgl. PEB</td>
//						<td width = "20%">FOB</td>
//				</tr>';
//			for($i=0;$i<count( $data_alert ['IDPEB']);$i++){
//				if ( $i % 2 == 0 ){
//					$color = "#FFFFFF";
//				} else {
//					$color = "#D3D1FB";
//				}
//				$table .= '
//				<tr bgcolor="'.$color.'">
//						<td width = "10%">'.$data_alert ['NO_PEB'][$i].'</td>
//						<td width = "10%">'.$data_alert ['NPWP'][$i].'</td>
//						<td width = "15%">'.$data_alert ['NAMA_EKSPORTIR'][$i].'</td>
//						<td width = "10%">'.$data_alert ['KPBC'][$i].'</td>
//						<td width = "15%">'.$data_alert ['TGL_PEB'][$i].'</td>
//						<td width = "10%">'.$data_alert ['FOB'][$i].'</td>			
//				</tr>';
//				
//			}
//			$table .= ' </table>';
//			$sql= "select email,fullname from tbluser where npwp = '$npwp' and notif = 1";
//			$data = $conn->query($sql);
//			while($data->next()){
//				$email[] = $data->get("email");
//				$name [] = $data->get("fullname");
//			}
//			$id_flag = "'".implode("','",$data_alert ['IDPEB'])."'"; 
//			$sts_peb = $z+1;
//			$sql_flag = "update tbldmpeb set fl_email = ".$sts_peb." where IDPEB in (".$id_flag.")";
//			$flag = $conn->execute($sql_flag);
//			if($flag){
//				$to = implode(";",$email);
//				$kepada = implode(";",$name);
//				$subject = "REMINDER EMAIL FOR DETAIL OF PEB NOT COMPLETED";								
//				$body = "
//				<br><br>Dear Valued Customers,
//				<i><br>Kepada nasabah yang terhormat,<br></i>
//				
//				<br>Referring to your RTE report with the mentioned on attachment,  
//				<i><br>Merujuk pada laporan RTE seperti disebutkan pada lampiran.</i>
//				<br>Please be informed that the RTE report as per attachment is not completed yet on the details of PEB (e.g. nominal amount of PEB (FOB), dated of PEB and number of PEB).
//				<i><br>Sebagai informasi bahwa laporan RTE seperti terlampir belum dilengkapi dengan rincian informasi dari PEB (sepert: nilai PEB (FOB), tanggal PEB dan nomor PEB)</i>
//				<br> ".$table." <br>
//				<br>We would like to remind you for completing the RTE report to avoid any penalty from the Regulatory (Central Bank).
//				<i><br>Kami ingin mengingatkan kembali untuk melengkapi pelaporan RTE untuk menghindari denda dari Bank Indonesia.</i>
//				<br>Really appreciate your good cooperation and assistance.
//				<i><br>Kami sangat menghargai kerjasamanya yang baik dan bantuannya</i>
//				<br><br>Thank you and Best regards,
//				<i><br>Terimakasih dan salam hormat,	</i>
//				<br>Business Support Unit 
//				<br>Standard Chartered Bank Indonesia";	
//				//send_Email(array('to'=>$to,'subject'=>$subject,'isi'=>$body,'kepada'=>$kepada));	
//			}
//		}	
//		$data_alert=array();
//		$email = array();
//		$name  = array();	
//	}
//}
//
//
//// cek rekenging tidak memiliki account web 
//$write_txt = "";
//$sql3= "select a.NOREK,(SELECT NAMA_PEMILIK FROM tbldmdanamasuk WHERE NOREK = A.NOREK and ROWNUM = 1) AS NAMA  
//		from tbldmdanamasuk a left  join taccount b on a.norek = b.account 
//		where b.account is null group by a.norek";
//$hasilAcc = $conn->query($sql3);
//while ($hasilAcc->next()){
//	$rell = $hasilAcc->get("NOREK");
//	$nama = $hasilAcc->get("NAMA");
//	$write_txt .= "DOKRELL|".$rell."|".$nama."|0\r\n";
//}
//$sql3= "select a.NOREK,(SELECT NAMA_PEMILIK FROM tbldmdanamasuk WHERE NOREK = A.NOREK and ROWNUM = 1) AS NAMA  
//		from tbldmdanamasuk a inner  join taccount b on a.norek = b.account 
//		group by a.norek";
//$hasilAcc = $conn->query($sql3);
//while ($hasilAcc->next()){
//	$rell = $hasilAcc->get("NOREK");
//	$nama = $hasilAcc->get("NAMA");
//	$write_txt .= "DOKRELL|".$rell."|".$nama."|1\r\n";
//}	
//if (strlen($write_txt) > 0 ){	
//	writetxt($write_txt,'DOKRELL.','GW');
//	$write_txt = "";
//}
//
//Setiap tannggal 5 untuk 1 bulan sebelumnya'

if (date("d") == '05'){
    
	$write_txt = "";
	$bln = date('m')-1;
	$tahun = date('Y');
	if($bln==0){
		$bln = '12';
		$tahun = $tahun-1;
	}
	//$bln = '12';
	$bulan = str_pad($bln,2,"0",STR_PAD_LEFT);

	//$tahun = '2015';
	$sql   = "select reference_number, flag_used, kd_dana, jns_pembayaran, jns_uangmuka, flag_uangsisa, dana_sisa, kode_nonekspor, nominal_transfer 
				FROM tbldmdanamasuk where to_char(tgl_transaksi,'MM-YYYY') = '".$bulan."-".$tahun."' ";
				
				
	$datadm = $conn->query($sql);
   
	while($datadm->next()){
		$reff 		= trim($datadm->get('reference_number'));
		$used 		= trim($datadm->get('flag_used'));
		$dana 		= trim($datadm->get('kd_dana'));
		$pembayaran = trim($datadm->get('jns_pembayaran'));
		$uangmuka 	= trim($datadm->get('jns_uangmuka'));
		$uangsisa 	= trim($datadm->get('flag_uangsisa'));
		$danasisa 	= trim($datadm->get('dana_sisa'));
		$sandi 		= trim($datadm->get('kode_nonekspor'));
		$nonimal 		= trim($datadm->get('nominal_transfer'));
		$write_txt .= "DOKREPORT|".$reff."|".$used."|".$dana."|".$pembayaran."|".$uangmuka."|".$uangsisa."|".$danasisa."|".$sandi."|".$nonimal."\r\n";
	}
 	if (strlen($write_txt) > 0 ){	
		writetxt($write_txt,'DOKREPORT.','GW');
		$write_txt = "";
	}
	//	Nama File DOKREPORT_
	//	DOKREPORT|reffrnece number|flag used|kode dana|jenis pembayaran|jenis uang muka|flag uang sisa|dana sisa|kode nonekspor|nominal 
	
}	
$conn->disconnect();	
		
?>