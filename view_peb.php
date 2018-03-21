<?php
if(in_array($_SESSION["priv_session"],array("5","4"))==false  || substr($_SESSION["AKSES"],3,1)!="1"){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}else{

set_time_limit(900);
require_once("conf.php");
require_once("dbconndb.php");

$cari = str_replace('\\','',$_POST["q"]);
$_POST["q"] = strFilter($_POST["q"]);
$cbx = $_POST['cbx'];
if($div=='toplus'){	
	if(!is_array($cbx)){ echo "<script> window.location.href='".base_url."modul/peb/baru';</script>";}
	$idpeb = "'".implode("','",$cbx)."'";	
	$sql = "update tbldmpeb set flag_used='3' where idPEB in (".$idpeb.")";	
	$connDB->connect();
	$hasil = $connDB->execute($sql);
	if($hasil){
		$_SESSION['respon'] = $bhs['Alokasi PEB90+'][$kdbhs];
		$_SESSION['statusRespon'] = 1;			
		require_once("dbconn.php");
		$conn->connect();    
		
		$aktivitas = "Mengalokasikan PEB sebagai PEB 90+";						
		audit($conn,$aktivitas); 
		$conn->disconnect(); 				
	}
	echo "<script> window.location.href='".base_url."modul/peb/baru';</script>";exit;
}elseif($div=="plusupload"){
	if(!is_array($cbx)){ echo "<script> window.location.href='".base_url."modul/peb/plus';</script>";exit;}				
	//print_r($_FILES);die();
	$namafile = $_FILES['uploadDok']['name'];
	$lokasifile = $_FILES['uploadDok']['tmp_name'];
	//$typefile = $_FILES['uploadDok']['type'];
	$sizefile = $_FILES['uploadDok']['size'];	
	$x=0;
	$errQuery=0;
	$errFormat=0;
	$errSize =0;
	$connDB->connect();  
	foreach($cbx as $d){
		$arrfile=array();
		$arrfile = explode(".",$namafile[$x]);
		//print_r($arrfile);
		$typefile = $arrfile[max(array_keys($arrfile))];
		//echo strtolower($typefile);exit;								
		if(strtolower($typefile)=='pdf'){
		//if(strpos(strtolower($typefile[$x]),'pdf')!==false){
			if($sizefile[$x]<1000000){					
				$sqlnoPeb = "select NO_PEB,fileupload from tbldmpeb where idPEB='".$d."'";
				$noPeb = $connDB->query($sqlnoPeb);$noPeb->next();	
				$nmfile = $noPeb->get('NO_PEB')."_".rand(100000,999999).".pdf";						
				$hasil = move_uploaded_file($lokasifile[$x],"files/".$nmfile);					
				if($hasil){
					$sql = "update tbldmpeb set fileupload='".$nmfile."' where idPEB='".$d."'";
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
		$x++;
	}
	$connDB->disconnect();   	
	$err = $errFormat+$errQuery+$errSize;
	$ok = ($x-1)-$err;
	$msg1;$msg2;
	require_once("dbconn.php");		
	$conn->connect();    
	
	
	if($err>0){
		if($errFormat>0){ $msg1= "<li style='font-size:12px;margin-left:15px;'>Kesalahan Format Dokumen sebanyak $errFormat buah file, dokumen yang diizinkan adalah  dokumen PDF</li>"; }
		if($errSize>0){ $msg2= "<li style='font-size:12px;margin-left:15px;'>Ukuran File melebihi 1 MB sebanyak $errSize buah file</li>"; }
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
		$aktivitas = "Mengupload sejumlah ".count($namafile)." Dokumen Pembaritahuan";	
		audit($conn,$aktivitas); 
	}else{
		$_SESSION['statusRespon'] = 0;
		$_SESSION['respon'] = $bhs['Dokumen tidak diupload'][$kdbhs];
	}											
	$conn->disconnect();  	
	//echo "<script> window.location.href='".base_url."modul/peb/plus';</script>";exit;	
}elseif(in_array($div,array('delete','deleteterlaporkan'))){
	if(!is_array($cbx)){ echo "<script> window.location.href='".base_url."modul/peb/baru';</script>";}
	$connDB->connect();
	foreach($cbx as $id){
		$sqlcar = "select CAR from tbldmpeb where idpeb=".$id."";	
		$dtcar = $connDB->query($sqlcar);$dtcar->next();	
		$sql = "delete from tbldmpeb where idpeb=".$id."";	
		$connDB->execute($sql);
		$write_txt .= "DELPEB|".$dtcar->get('CAR')."\r\n";
		$sql = "delete from tblpebdok where CAR='".$dtcar->get('CAR')."'";	
		$connDB->execute($sql);
		$write_txt .= "DELINV|".$dtcar->get('CAR')."\r\n";
		
	}	
	if (strlen($write_txt)>0 ){
		writetxt($write_txt,'DOKPEB.',$_SESSION['ID']);
		$write_txt='';
	}
	$_SESSION['respon'] = $bhs['PEB Hapus'][$kdbhs];
	$_SESSION['statusRespon'] = 1;			
	require_once("dbconn.php");
	$conn->connect();    
	
	$aktivitas = "Menghapus sejumlah ".count($cbx)." dokumen PEB";						
	audit($conn,$aktivitas); 
	$conn->disconnect(); 				

	$link = ($div=="delete")?"baru":"terlaporkan";
	echo "<script> window.location.href='".base_url."modul/peb/".$link."';</script>";exit;
}elseif($div=='update'){
	if(!is_array($cbx)){ echo "<script> window.location.href='".base_url."modul/peb/baru';</script>";}
	$connDB->connect();
	$nopeb = $_POST['nopeb'];
	$tglpeb = $_POST['tglpeb'];	
	$x=0;
	if(is_array($nopeb)){
		foreach($nopeb as $Nopeb){
			$tglpebs = trim($tglpeb[$x]);
			$tglpebs = (strlen($tglpebs)<4)? "NULL" : "TO_DATE('".$tglpebs."','DD-MM-YYY')";
			$sql = "update tbldmpeb set no_PEB='".$Nopeb."', tgl_PEB=$tglpebs where idPEB ='".$cbx[$x]."'";
			
			$hasil = $connDB->execute($sql);	
			$x++;
		}
	}else{
		$_SESSION['respon'] = $bhs['Tidak Ada Perubahan'][$kdbhs];
		$_SESSION['statusRespon'] = 0;
	}
	if($x>0){
		$_SESSION['respon'] = $bhs['Perubahan Tersimpan'][$kdbhs];
		$_SESSION['statusRespon'] = 1;			
		require_once("dbconn.php");
		$conn->connect();    
		
		$aktivitas = "Mengisi No.PEB dan tanggal PEB sejumlah ".count($cbx)." dokumen PEB";						
		audit($conn,$aktivitas); 
		$conn->disconnect(); 				
	}
	
	echo "<script> window.location.href='".base_url."modul/peb/baru';</script>";exit;
}elseif($div=='batal'){
	if(!is_array($cbx)){ echo "<script> window.location.href='".base_url."modul/peb/baru';</script>";}
	$idpeb = "'".implode("','",$cbx)."'";	
	$sql = "update tbldmpeb set flag_used='0', fileupload=NULL where idPEB in (".$idpeb.")";	
	$connDB->connect();
	$hasil = $connDB->execute($sql);
	if($hasil){
		$_SESSION['respon'] = $bhs['PEB90+ Batal'][$kdbhs];
		$_SESSION['statusRespon'] = 1;			
		require_once("dbconn.php");
		$conn->connect();    
		
		$aktivitas = "Pembatalan PEB 90+ sejumlah ".count($cbx)." PEB";						
		audit($conn,$aktivitas); 
		$conn->disconnect(); 				
	}
	echo "<script> window.location.href='".base_url."modul/peb/plus';</script>";exit;
}
?>
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="border-bottom:1px solid #D7D7D7;">
			<div style="padding-bottom:9px;">
			<span style="color:#1a68a4; font-size:16px; font-weight:bold;">
			<?php
			$judul = array("baru"=>$bhs['PEB Baru'][$kdbhs],"terlaporkan"=>$bhs['PEB Terlaporkan'][$kdbhs],"plus"=>"PEB 90 +");
			$messageBox =($_SESSION['statusRespon']==0)? 
						"<div style='background:#FDE9DF;padding:5px;border:1px #CCC solid;color:#633'>
						 <img src='".base_url."img/warninglogo.png' style='border:none'> ".$_SESSION['respon']."</div>" : 
						 "<div style='background:#E5EEF5;padding:5px;border:1px #CCC solid;'>
						 <img src='".base_url."img/accept.png' style='border:none'> ".$_SESSION['respon']."</div>";
			if($_SESSION['respon']){
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
				<form name="frmCari" method="post" action="<?php echo base_url."modul/peb/".$div;?>">				
				<table>
					<tr>
						<td><?php echo $bhs['Kategori'][$kdbhs]?></td>
						<td><select name="field" id="field" onChange="javascript:cekField(this.value);">
					<?php 									
					$x1 = array("noDok","KPBC","NPWP","Nama_Eksportir","no_PEB","tgl_PEB","valuta","FOB");
					$x2 = array("Invoice","Kode KPBC","NPWP",$bhs['Eksportir'][$kdbhs],$bhs['No. PEB'][$kdbhs],$bhs['Tanggal PEB'][$kdbhs],$bhs['Valuta'][$kdbhs],$bhs['Nilai PEB'][$kdbhs]);
					if($div=="terlaporkan"){
						array_push($x1,"flag_used");
						array_push($x2,"Status PEB");						
					}
					for($i=0;$i<count($x1);$i++){
						if($_POST['field']==$x1[$i]){
							echo("<option value=\"$x1[$i]\" selected>$x2[$i]</option>");
						} else {
							echo("<option value=\"$x1[$i]\">$x2[$i]</option>");
						}
					}
					?>
				  </select></td>
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
									echo '<button type="button" onclick=location.href="'.base_url."modul/peb/".$div.'" style="width:75px" class="btn_2">'.$bhs['Batal'][$kdbhs].'</button></td>';
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
			<span style='background:#FFF;width:10px;border:1px #999 solid;margin-left:-30px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> &nbsp;".$bhs['PEB<70'][$kdbhs]."
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;			
			<span style='background:#F4F99B;width:10px;border:1px #999 solid;margin-left:-30px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> &nbsp;".$bhs['70<PEB<90'][$kdbhs]."
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<span style='background:#FAC7B8;width:10px;border:1px #999 solid;margin-left:-30px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> &nbsp;".$bhs['PEB>90'][$kdbhs]."			
			</ul>
		</fieldset>";
	}
	
	$order = $_GET['order'];;
	$sort = $_GET['sort'];
	
	$peb = array("nodok","KPBC","NPWP","Nama_Eksportir","no_PEB","tgl_PEB","valuta","FOBSORT","flag_used");										
	$orderby = setSortir(array('sort'=>'ID','order'=>'desc'),$peb);
	$tg1 = $_POST["tg1"];
	$tg2 = $_POST["tg2"];   
	$div = (!in_array($div,array("baru","terlaporkan","plus")))? "baru" :$div;					
	$where = array("baru"=>"'0'","terlaporkan"=>"'1','2'","plus"=>"'3'");
	$sql = "select idPEB as ID, p.CAR AS noDok, KPBC,
			CASE LENGTH(p.NPWP) WHEN 15  THEN (SUBSTR(p.NPWP,1,2)||'.'||SUBSTR(p.NPWP,3,3)||'.'||SUBSTR(p.NPWP,6,3)||'.'||SUBSTR(p.NPWP,9,1)||'-'||SUBSTR(p.NPWP,10,3)||'.'||SUBSTR(p.NPWP,13,3)) 
			ELSE p.NPWP END AS NPWP,
			nama_eksportir, no_PEB, TO_DATE(tgl_PEB,'DD-MM-YYYY') AS tgl_PEB, valuta , 
			TO_CHAR(FOB,'999,999,999,999,999.00') AS FOB";
	if($div=="baru"){
		require_once("library/tbLite/develop/htmltable.classcolor.php");		
	}elseif($div=="terlaporkan"){
		$sql .= ", flag_used ";
	}elseif($div=="plus"){
		$sql .= ", '1' as upload , case when LENGTH(p.FILEUPLOAD) > 4 then '1' else '0' end as status,to_char(tgl_tempo,'DD-MM-YYYY') as tgl_tempo,(select s.definisi from tblkodestatus s where s.kode = p.jns_bayar and s.id = '1' ) as jns_bayar,case FL_SEND when 1 then 'SEND' else '' end as FL_SEND ,'' as d,'' as e,'' as f,
		p.FILEUPLOAD,'' as g";
	}
	$sql .= ", FOB as FOBSORT, DATEDIFF(SYSDATE,to_date(TGL_PEB,'DD-MM-YY'))  as COLOR, case source when '2' then 'Input' when '1' then 'Upload' else '' end as desc_source from tbldmpeb p 
			where p.flag_used in (".$where[$div].") and upper(p.groupid) = '".strtoupper($_SESSION['grpID'])."'";
										
	if($_POST["Submit"]=="submit" ){						
		if($_POST["field"] == "tgl_PEB"){
			$sql = $sql ." And tgl_PEB BETWEEN TO_DATE('".$tg1 ."','DD-MM-YYYY') AND  TO_DATE('".$tg2 ."','DD-MM-YYYY') "; 
		}elseif($_POST["field"] == "flag_used"){
			$cari ="";
			$str = strtoupper($_POST["q"]);
			$str = ($str!="")?$str:"xx";
			if(strpos("COMPLETED",$str)>-1){
				$cari ="1";
			}elseif(strpos("PENDING",$str)>-1){
				$cari ="2";
			}
			$sql = $sql ." And upper(". strFilter($_POST["field"]) .") like '%$cari%'";	
		}elseif($_POST["field"] == "noDok"){
			$sql = $sql ." And upper(SUBSTR(p.CAR,2,LENGTH(p.CAR))) like '%$cari%'";							
		} else {
			$sql = $sql ." And upper(". strFilter($_POST["field"]) .") Like '%". strtoupper($_POST["q"]) ."%'";							
		}
	} 
	
	$connDB->connect();	
	$sql = $sql . $orderby;					
	$table = ($div=="baru")? new HTMLTableColor(): new HTMLTable();	
	$table->connection = $connDB;
	$table->width = "100%";
	$table->navRowSize = 10;
	$table->SQL = $sql;		
	//echo $sql;			
	$cols = array();
	$cols[0] = 0;
	$data = array();					
	$data[] = array("#",$bhs['Pilih Proses'][$kdbhs]);
	if($div=="baru"){
		//$data[] = array(base_url."modul/peb/update",$bhs['Simpan Perubahan'][$kdbhs]);
		if(in_array($_SESSION["priv_session"],array("5"))==true ){		
			$data[] = array(base_url."modul/peb/pilihdanamasuk",$bhs['Pilih Dana Masuk'][$kdbhs]);
			$data[] = array(base_url."modul/peb/toplus","- PEB 90+");	
		}				
		#$data[] = array(base_url."modul/peb/pilihtanpadanamasuk","- Pilih Tanpa Dana Masuk");					
							
		$data[] = array(base_url."modul/peb/edit","- Edit PEB");		
		$data[] = array(base_url."modul/peb/delete",$bhs['Hapus PEB'][$kdbhs]);				
		$table->ajaxMod13 = 10; #color
		$table->ajaxMod15 = 5; #field no_peb
		$table->ajaxMod16 = 6; #field tgl_peb
		
	}elseif($div=="terlaporkan"){
		if(in_array($_SESSION["priv_session"],array("5"))==true ){
			$data[] = array(base_url."modul/peb/pilihdanamasukpendings",$bhs['Pilih Dana Masuk'][$kdbhs]);	
			#$data[] = array(base_url."modul/peb/deleteterlaporkan",$bhs['Hapus PEB'][$kdbhs]);					
			$table->ajaxMod8 = 9;
		}else{
			$table->ajaxMod8 = 9;
		}
	}elseif($div=="plus"){		
		$data[] = array(base_url."modul/peb/plusupload",$bhs['Upload Pemberitahuan'][$kdbhs]);			
		#$data[] = array(base_url."modul/peb/pilihdanamasukpending","- Pilih Dana Masuk");	
		#$data[] = array(base_url."modul/peb/pilihtanpadanamasuk","- Pilih Tanpa Dana Masuk");
		if(in_array($_SESSION["priv_session"],array("5"))==true ){	
			$data[] = array(base_url."modul/peb/pilihtanpadanamasuk",$bhs['Bentuk PEB'][$kdbhs]);
			$data[] = array(base_url."modul/peb/batal",$bhs['Batalkan PEB90+'][$kdbhs]);	
		}
								
		$table->ajaxMod11 = 9;
		$table->ajaxMod7 = 10;
		$table->ajaxMod12 = true;
		//$table->ajaxMod14 = 11;		
	}
	
	$table->showCheckBox(true,$cols);	
	$table->showPager(true,$F_HANDLER->BOTTOM,50,20);
	$table->showRDPanel(true,$F_HANDLER->BOTH,$cols,$data);
	$x=0;
	$table->field[$x]->name = "ID";
	$table->field[$x]->headername = "ID";
	$table->field[$x]->align = $F_HANDLER->LEFT;
	$table->field[$x]->hidden = true;
	$x=1;					
	$table->field[$x]->name = "nodok";
	$table->field[$x]->headername = "No Pengajuan (CAR)";
	$table->field[$x]->align = $F_HANDLER->LEFT;	
	$x=2;					
	$table->field[$x]->name = "KPBC";
	$table->field[$x]->headername = "Kode KPBC";
	$table->field[$x]->align = $F_HANDLER->LEFT;
	$x=3;
	$table->field[$x]->name = "NPWP";
	$table->field[$x]->headername = "NPWP";
	$table->field[$x]->align = $F_HANDLER->LEFT;
	$x=4;
	$table->field[$x]->name = "Nama_Eksportir";
	$table->field[$x]->headername = $bhs['Eksportir'][$kdbhs];
	$table->field[$x]->align = $F_HANDLER->LEFT;	$x=5;
	$table->field[$x]->name = "no_PEB";
	$table->field[$x]->headername = $bhs['No. PEB'][$kdbhs];
	$table->field[$x]->align = $F_HANDLER->LEFT;
	$x=6;
	$table->field[$x]->name = "tgl_PEB";
	$table->field[$x]->headername = $bhs['Tanggal PEB'][$kdbhs];
	$table->field[$x]->align = $F_HANDLER->LEFT;
	$x=7;
	$table->field[$x]->name = "valuta";
	$table->field[$x]->headername = $bhs['Valuta'][$kdbhs];
	$table->field[$x]->align = $F_HANDLER->LEFT;
	$x=8;
	$table->field[$x]->name = "FOBSORT";
	$table->field[$x]->headername = $bhs['Nilai PEB'][$kdbhs];
	$table->field[$x]->align = $F_HANDLER->RIGHT;
	if($div=="terlaporkan"){
		$x=9;
		$table->field[$x]->name = "flag_used";
		$table->field[$x]->headername = $bhs['Status PEB'][$kdbhs]; 
		$table->field[$x]->align = $F_HANDLER->LEFT;

		//hidden NPWP : Edit by yoanes 20141011
		$table->field[3]->hidden = true;

	}elseif($div=="plus"){
		$x=9;
		$table->field[$x]->name = "upload";
		$table->field[$x]->headername = $bhs['Upload Dokumen'][$kdbhs]; 
		$table->field[$x]->align = $F_HANDLER->LEFT;
		$x=10;
		$table->field[$x]->name = "status";
		$table->field[$x]->headername = $bhs['Dokumen'][$kdbhs]; 
		$table->field[$x]->align = $F_HANDLER->LEFT;
		$x=11;
		$table->field[$x]->name = "tgl_tempo";
		$table->field[$x]->headername = $bhs['Tanggal Jatuhtempo'][$kdbhs]; 
		$table->field[$x]->align = $F_HANDLER->LEFT;	
		$x=12;
		$table->field[$x]->name = "jns_bayar";
		$table->field[$x]->headername = $bhs['Jenis Pembayaran'][$kdbhs]; 
		$table->field[$x]->align = $F_HANDLER->LEFT;	
		$x=13;
		$table->field[$x]->name = "fl_send";
		$table->field[$x]->headername = $bhs['Status PEB'][$kdbhs]; 
		$table->field[$x]->align = $F_HANDLER->LEFT;	
		$x=14;
		$table->field[$x]->hidden = true;
		$x=15;
		$table->field[$x]->hidden = true;
		$x=16;
		$table->field[$x]->hidden = true;
		$x=17;
		$table->field[$x]->hidden = true;#fileupload
		$x=18;
		$table->field[$x]->hidden = true;
	}			
	$x++;
	$table->field[$x]->name = "FOBSORT";
	$table->field[$x]->hidden = true;
	
	$x++;
	$table->field[$x]->name = "COLOR";
	$table->field[$x]->hidden= true;
	
	$x++;
	$table->field[$x]->name = "desc_source";
	$table->field[$x]->headername = "Source"; 
	$table->field[$x]->align = $F_HANDLER->CENTER;
	
	$table->drawTable();
	$conn->disconnect();			
}
?>