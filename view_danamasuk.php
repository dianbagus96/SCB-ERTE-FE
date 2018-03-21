<?php
if(in_array($_SESSION["priv_session"],array("5"))==false || substr($_SESSION["AKSES"],3,1)!="1" ){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}else{
$jumRek = count(explode(",",$_SESSION['noRek']));

set_time_limit(900);
require_once("conf.php");
require_once("dbconn.php");
$conn->connect();
$cbx = $_POST['cbx'];
$code = $_POST['code'];
if(is_array($cbx)){
	if(in_array($div,array("toekspor","tononekspor","tocampuran","tobaru","batalcampuran","batalekspor","batalnonekspor","bataluangmuka","uangmukafull","uangmukaparsial","update"))){
		if(in_array($div,array("toekspor","tononekspor","tocampuran","tobaru","batalcampuran","batalekspor","batalnonekspor","bataluangmuka"))){
			if($div=='bataluangmuka'){			
				$arriddanamasuk = array();
				foreach($cbx as $a){
					$b = explode(";",$a);
					$arriddanamasuk[] = $b[0];
				}
				$iddanamasuk = "'".implode("','",$arriddanamasuk)."'";
			}else{
				$iddanamasuk = "'".implode("','",$cbx)."'";
			}		
			$kd_dana = array("toekspor"=>"01","tononekspor"=>"02","tocampuran"=>"03","batalekspor"=>"00","batalcampuran"=>"00","batalnonekspor"=>"00","bataluangmuka"=>"01");
			if (in_array($div,array("batalekspor","batalnonekspor"))){
				$cek = "select DANA_SISA, IDDANAMASUK, REFERENCE_NUMBER FROM TBLDMDANAMASUK where iddanamasuk in (".$iddanamasuk.")";
				$cek_ok = $conn->query($cek);
				while($cek_ok->next()){
					if ($cek_ok->get('DANA_SISA' )== '2'){
						$del = "delete from tbldmdanamasuk where REFERENCE_NUMBER='".$cek_ok->get('REFERENCE_NUMBER')."' and DANA_SISA = '2'";
						$conn->execute($del);
						$sql = "update tbldmdanamasuk set kd_dana='".$kd_dana[$div]."' where REFERENCE_NUMBER='".$cek_ok->get('REFERENCE_NUMBER')."' and DANA_SISA = '0' and kd_dana = '05'";	
						$hasil = $conn->execute($sql);
					}elseif ($cek_ok->get('DANA_SISA' )== '1'){
					echo "<script type='text/javascript'> 
							$(document).ready(function(){
								jAlert('Dana Split RTE tidak bisa dibatalkan ','',function(r){
									if(r==true) window.location.href='".base_url."modul/danamasuk/".$div."';\n				
								});		
							})
						</script>";exit;	
					}else{
						$sql = "update tbldmdanamasuk set kd_dana='".$kd_dana[$div]."', kode_nonekspor = null where iddanamasuk in ('".$cek_ok->get('IDDANAMASUK')."')";	
						$hasil = $conn->execute($sql);		
					}
				}
				
			}else{
				$sql = "update tbldmdanamasuk set kd_dana='".$kd_dana[$div]."', kode_nonekspor ='".$code."'  where iddanamasuk in (".$iddanamasuk.")";
				$hasil = $conn->execute($sql);			
			}
			
			if($hasil){
				if($div=='tocampuran'){
					foreach($cbx as $d){
						$sql = "insert into tblfcdanapartial(iddanamasuk,DanaEkspor,DanaNonEkspor) values('".$d."',0,0)";				
						$hasil = $conn->execute($sql);
					}
				}elseif($div=='batalcampuran'){
					foreach($cbx as $d){
						$sql = "delete from tblfcdanapartial where iddanamasuk='".$d."'";				
						$hasil = $conn->execute($sql);
					}
				}				
				$message = 	array(	"toekspor"=>"Ekspor","tononekspor"=>"Non Ekspor","tocampuran"=>"dana Campuran",
									"batalcampuran"=>"Campuran","batalekspor"=>"Ekspor","batalnonekspor"=>"Non Ekspor","bataluangmuka"=>"Uang Muka");								
				$_SESSION['respon'] = in_array($div,array('batalcampuran','batalekspor','batalnonekspor','bataluangmuka'))? 
										"Pembatalan dana masuk ".$message[$div]." Berhasil":
										"Alokasi sebagai ".$message[$div]." Berhasil";												
				$aktivitas = "Mengalokasikan sejumlah ".count($cbx)." Dana masuk sebagai Dana ".$message[$div]."";				
			}		
			$link = array("batalcampuran"=>"campuran","batalekspor"=>"ekspor","batalnonekspor"=>"nonekspor","bataluangmuka"=>"ekspor");		
			$go = in_array($div,array('batalcampuran','batalekspor','batalnonekspor','bataluangmuka'))? $link[$div] :'baru';
			
		}elseif(in_array($div,array('uangmukafull','uangmukaparsial'))){
			$iddanamasuk = "'".implode("','",$cbx)."'";
			if($div=='uangmukafull'){
				$jnsbayar = '01';
				$uangmuka = "Penuh";			
			}else{	
				$jnsbayar = '02';
				$uangmuka = "Parsial";			
			}
			$sql = "update tbldmdanamasuk set kd_dana='04',jns_pembayaran='".$jnsbayar."' where iddanamasuk in (".$iddanamasuk.")";				
			$hasil = $conn->execute($sql);
			if($hasil){
				$_SESSION['respon'] = "Alokasi uang muka ".$uangmuka." Berhasil";			
				$aktivitas = "Mengalokasikan Dana Uang Muka sejumlah ".count($cbx)."";				
			}
			$go = 'ekspor';
		}else{
			$danaEkspor = $_POST['danaEkspor'];
			$danaNonEkspor = $_POST['danaNonEkspor'];	
			$x=0;
			foreach($cbx as $d){
				$sql = "update tblfcdanapartial set DanaEkspor='".str_replace(',','',$danaEkspor[$x])."', DanaNonEkspor='".str_replace(',','',$danaNonEkspor[$x])."' where iddanamasuk='".$d."'";	
				$hasil = $conn->execute($sql);
				$x++;
			}
			$_SESSION['respon'] = $bhs['Perubahan Tersimpan'][$kdbhs];
			$aktivitas = "Mengalokasikan Dana Campuran sejumlah ".count($cbx)."";				
			$go = 'campuran';
		}	
		require_once("dbconn.php");
		$conn->connect();    
		
		audit($conn,$aktivitas); 
		$conn->disconnect(); 		
		echo "<script> window.location.href='".base_url."modul/danamasuk/".$go."';</script>";exit;
	}
}
$conn->disconnect();
$cari = str_replace('\\','',$_POST["q"]);
$_POST["q"] = strFilter($_POST["q"]);
?>
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="border-bottom:1px solid #D7D7D7;">
			<div style="padding-bottom:9px;">
			<span style="color:#1a68a4; font-size:16px; font-weight:bold;" id="view">
			<?php
			if(!in_array($div,array("baru","ekspor","nonekspor","uangmuka","terlaporkan"))){ 
			echo "<script> window.location.href='".base_url."modul/danamasuk/baru';</script>";exit;}
			$judul = array("baru"=>$bhs['Dana Masuk Baru'][$kdbhs],"ekspor"=>$bhs['Ekspor'][$kdbhs],"nonekspor"=>$bhs['Non Ekspor'][$kdbhs],
			"uangmuka"=>$bhs['Uang Muka'][$kdbhs],"terlaporkan"=>$bhs['Dana Terlaporkan'][$kdbhs]);
			
			$messageBox = "<div style='background:#E5EEF5;padding:5px;border:1px #CCC solid;' class='msgRespon'>
							<img src='".base_url."img/accept.png' style='border:none'> ".$_SESSION['respon']."
							</div>";							
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
				<form name="frmCari" method="post" action="<?php echo base_url."modul/danamasuk/".$div?>">				
				<table>
					<tr>
						<td><?php echo $bhs['Kategori'][$kdbhs]?></td>
						<td><select name="field" id="field" onChange="javascript:cekField(this.value);">
						<?php 									
						$x1 = array("nama_pengirim","valuta_transfer","nominal_transfer","valuta_diterima","nominal_diterima","tgl_transaksi","nama_bank_pengirim","berita");						
						$x2 = array($bhs['Nama Pengirim'][$kdbhs],$bhs['Valuta Transfer'][$kdbhs],$bhs['Nominal Transfer'][$kdbhs],$bhs['Valuta Diterima'][$kdbhs],$bhs['Nominal Diterima'][$kdbhs],$bhs['Tanggal Transaksi'][$kdbhs],$bhs['Nama Bank'][$kdbhs],$bhs['Berita'][$kdbhs]);
						if($div=="uangmuka"){
							array_push($x1,"uraian_pembayaran");
							array_push($x2,$bhs['Pembayaran'][$kdbhs]);							
						}
						if($jumRek>1){
							array_push($x1, "norek");
							array_push($x2, $bhs['No. Rek'][$kdbhs]);
						}
						array_push($x1, "reference_number");
						array_push($x2, $bhs['No. Ref'][$kdbhs]);
						
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
									echo '<button type="button" onclick=location.href="'.base_url.'modul/danamasuk/'.$div.'" style="width:75px" class="btn_2">'.$bhs['Batal'][$kdbhs].'</button></td>';
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
	if($div=="baru"||$div=="ekspor"){
	echo "<fieldset style='border:none;padding:10px;font-size:11px;font-family:Tahoma;padding-bottom:-20px;border-bottom:1px #CCC solid'>
			<b>Catatan :</b>
			<ul style='margin-bottom:-5px;'>
			<span style='background:#F4F99B;width:10px;border:1px #999 solid;margin-left:-30px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> &nbsp;".$bhs['Dana>3'][$kdbhs]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<span style='background:#E5EEF5;width:10px;border:1px #999 solid;margin-left:-30px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> &nbsp;".$bhs['Dana<3'][$kdbhs]."	
			</ul>
		</fieldset>";
	}
	
	$order = $_GET['order'];;
	$sort = $_GET['sort'];
	$danamasuk = array("tgl_transaksi","valuta_transfer","nominal_transfersort","valuta_diterima","nominal_diterimasort","nama_pengirim","nama_bank_pengirim","berita","norek","reference_number");									
	$orderby = setSortir(array('sort'=>'ID','order'=>'desc'),$danamasuk);	
	$tg1 = $_POST["tg1"];
	$tg2 = $_POST["tg2"];
	$where = array("baru"=>"kd_dana ='00' and flag_used='0'","ekspor"=>"kd_dana ='01' and flag_used='0'",
					"nonekspor"=>"kd_dana ='02' and flag_used='0'",
					"uangmuka"=>"kd_dana ='04' and flag_used='0'","terlaporkan"=>"flag_used in('1','2','3')");
	$rekening = ($jumRek>1)?",norek":"";
	
	if($div=="terlaporkan"){
		//Edit by yoanes: a.nominal_diterima => a.nominal_transfer
		$sql = "select a.iddanamasuk as ID,TO_CHAR(a.tgl_transaksi,'DD-MM-YYYY') AS tgl_transaksi, a.valuta_transfer,
			TO_CHAR(a.nominal_transfer,'999,999,999,999,999.00') AS nominal_transfer, 
			a.valuta_diterima,
			TO_CHAR(a.nominal_diterima,'999,999,999,999,999.00') AS nominal_diterima,			
			a.nama_pengirim, a.nama_bank_pengirim,a.berita ";
	}else{
		$sql = "select a.iddanamasuk as ID,TO_CHAR(a.tgl_transaksi,'DD-MM-YYYY') AS tgl_transaksi, a.valuta_transfer,
			TO_CHAR(a.nominal_transfer,'999,999,999,999,999.00') AS nominal_transfer,
			a.valuta_diterima,
			TO_CHAR(a.nominal_diterima,'999,999,999,999,999.00') AS nominal_diterima,			
			a.nama_pengirim, a.nama_bank_pengirim,a.berita ";
	}

	if($div=="baru"){
		require_once("library/tbLite/develop/htmltable.classcolor.php");
		$sql .=",	DATEDIFF(SYSDATE,a.tgl_transaksi) as COLOR
					,a.nominal_transfer as nominal_transfersort, a.nominal_diterima as nominal_diterimasort
					".$rekening.",a.reference_number, case when a.dana_sisa='1' then 'Dana Split RTE' when a.dana_sisa='2' then 'Dana Split' else '' end as Status_dana from tbldmdanamasuk a where ".$where[$div]." and a.NoRek in(".trim($_SESSION['noRek']).")";
	}elseif($div=="uangmuka"){
		$sql .=" ,b.uraian_pembayaran, 
				 case when a.jns_pembayaran ='01' then '0220' else '0230' end as sandiKeterangan , '' as keterangan, idLLd
				 ,a.nominal_transfer as nominal_transfersort, a.nominal_diterima as nominal_diterimasort				 
				".$rekening.",a.reference_number from tbldmdanamasuk a inner join tbldmpembayaran b on a.jns_pembayaran=b.jns_pembayaran where ".$where[$div]." and a.NoRek in(".trim($_SESSION['noRek']).")";
	}elseif($div=="terlaporkan"){
		$sql .=" , b.uraian_dana as uraian_dana,a.nominal_diterima as nominal_transfersort, a.nominal_diterima as nominal_diterimasort ".$rekening.",a.reference_number,
				case when a.flag_used='1' then 'Completed' 
				when a.flag_used='3' then 'Pending' end as Status_dana  
				from tbldmdanamasuk a inner join tbldmdana b on a.kd_dana=b.kd_dana where ".$where[$div]." and a.NoRek in(".trim($_SESSION['noRek']).")";
	}elseif($div=="ekspor"){
		require_once("library/tbLite/develop/htmltable.classcolor.php");
		$sql .=",	DATEDIFF(SYSDATE,a.tgl_transaksi) as COLOR
					,a.nominal_transfer as nominal_transfersort, a.nominal_diterima as nominal_diterimasort
					".$rekening.",a.reference_number, case when a.dana_sisa='1' then 'Dana Split RTE' when a.dana_sisa='2' then 'Dana Split' else '' end as Status_dana from tbldmdanamasuk a where ".$where[$div]." and a.NoRek in(".trim($_SESSION['noRek']).")";
	}else{
		$sql .=" ,a.nominal_transfer as nominal_transfersort, a.nominal_diterima as nominal_diterimasort ".$rekening.",a.reference_number, case when a.dana_sisa='1' then 'Dana Split RTE' when a.dana_sisa='2' then 'Dana Split' else '' end as Status_dana, kode_nonekspor from tbldmdanamasuk a where ".$where[$div]." and a.NoRek in(".trim($_SESSION['noRek']).")";
	}							
	
										
	if($_POST["Submit"]=="submit" ){						
		if($_POST["field"] == "tgl_transaksi"){
			$sql = $sql ." And tgl_transaksi BETWEEN TO_DATE('".$tg1 ."','DD-MM-YYYY') AND  TO_DATE('".$tg2 ."','DD-MM-YYYY') "; 
		} else {
			$sql = $sql ." And upper(". strFilter($_POST["field"]) .") Like '%". strtoupper($_POST["q"]) ."%'";							
		}
	} 
	
	$conn->connect();

	//sql gabungan
	$sql = $sql . $orderby;		
	//echo $sql;				
	$table = ($div=="baru"||$div=="ekspor")? new HTMLTableColor(): new HTMLTable();	
	$table->connection = $conn;
	$table->width = "100%";
	$table->navRowSize = 10;
	if($div=="baru"||$div=="ekspor"){
		$table->ajaxMod13 = 9;#color
	}elseif($div=="campuran"){
		$table->ajaxMod3 = 9;
		$table->ajaxMod4 = 10;
	}elseif($div=="uangmuka"){		
		$table->opsiPlus2=true;
	}
	$table->SQL = $sql;

	#echo $sql;
	//print_r($_SESSION);
	// elemen data yang akan di passing
	$cols = array();
	if($div=="uangmuka"){
		$cols[0] = 0;#ID
		$cols[1] = 3;#nominal_transfer
		$cols[2] = 12;#idlld
		$cols[3] = 10;#sandiketerangan
		$cols[4] = 2;#valuta_transfer
		$table->ajaxMod10 = 11;
	}else{
		$cols[0] = 0;
	}
	
	$proses = array("baru"=>array(
								array("#",$bhs['Pilih Proses'][$kdbhs]),
								array(base_url."modul/danamasuk/toekspor",$bhs['Alokasi Ekspor'][$kdbhs]),
								array(base_url."modul/danamasuk/tononekspor",$bhs['Alokasi Non Ekspor'][$kdbhs]),
								array(base_url."modul/danamasuk/splitdana",$bhs['Split Dana'][$kdbhs]),								
								array(base_url."reportdanamasuk.php?div=baru",$bhs['Cetak Report'][$kdbhs]),
								array(base_url."csvdanamasuk.php?div=baru","- CSV Report"),		
								array(base_url."txtdanamasuk.php?div=baru","- TXT Report")						
								
							),
					"ekspor"=>array(
								array("#",$bhs['Pilih Proses'][$kdbhs]),	
								array(base_url."modul/danamasuk/pilihpeb",$bhs['Pilih PEB'][$kdbhs]),
								array(base_url."modul/danamasuk/uangmukafull",$bhs['Alokasi Penuh'][$kdbhs]),
								array(base_url."modul/danamasuk/uangmukaparsial",$bhs['Alokasi Parsial'][$kdbhs]),
								array(base_url."modul/danamasuk/batalekspor",$bhs['Batal Ekspor'][$kdbhs]),
								array(base_url."reportdanamasuk.php?div=ekspor",$bhs['Cetak Report'][$kdbhs]),
								array(base_url."csvdanamasuk.php?div=baru","- CSV Report"),
								array(base_url."txtdanamasuk.php?div=baru","- TXT Report")						
							),
					"nonekspor"=>array(
								array("#",$bhs['Pilih Proses'][$kdbhs]),
								array(base_url."modul/danamasuk/batalnonekspor",$bhs['Batal Non Ekspor'][$kdbhs]),
								array(base_url."csvdanamasuk.php?div=baru","- CSV Report"),
								array(base_url."txtdanamasuk.php?div=baru","- TXT Report")
							),
					"uangmuka"=>array(
								array("#",$bhs['Pilih Proses'][$kdbhs]),
								array(base_url."modul/danamasuk/uangmukarte",$bhs['Bentuk RTE'][$kdbhs]),												
								array(base_url."modul/danamasuk/bataluangmuka",$bhs['Batalkan Uang Muka'][$kdbhs]),
								array(base_url."csvdanamasuk.php?div=baru","- CSV Report"),
								array(base_url."txtdanamasuk.php?div=baru","- TXT Report")
							),
					"terlaporkan"=>array(
								array("#",$bhs['Pilih Proses'][$kdbhs]),
								array(base_url."reportdanamasuk.php?div=terlaporkan",$bhs['Cetak Report'][$kdbhs]),
								array(base_url."csvdanamasuk.php?div=terlaporkan","- CSV Report"),
								array(base_url."txtdanamasuk.php?div=terlaporkan","- TXT Report"),
								//array(base_url."modul/danamasuk/danaterlaporkanpilihpeb",$bhs['Pilih PEB'][$kdbhs]),						
							)
				);
	$data = $proses[$div];
	$table->showRDPanel(true,$F_HANDLER->BOTH,$cols,$data);
	
	if(in_array($div,array('baru','campuran'))){
		$table->showCheckBox(true,$cols);		
	}elseif($div=="terlaporkan"){
		#$table->showCheckBox(false,$cols);	
		#$table->showRDPanel(false,$F_HANDLER->BOTH,$cols,$data);	
		$table->cbxMod2 = true;			
	}else{
		$table->showCheckBox(true,$cols);	
	}	
	$table->showPager(true,$F_HANDLER->BOTTOM,50,20);				

		
	$table->field[0]->name = "ID";
	$table->field[0]->headername = "ID";
	$table->field[0]->align = $F_HANDLER->LEFT;
	$table->field[0]->hidden = true;

	$table->field[1]->name = "tgl_transaksi";
	$table->field[1]->headername = $bhs['Tanggal Transaksi'][$kdbhs];
	$table->field[1]->align = $F_HANDLER->LEFT;
	
	$table->field[2]->name = "valuta_transfer";
	$table->field[2]->headername = $bhs['Valuta Transfer'][$kdbhs];
	$table->field[2]->align = $F_HANDLER->LEFT;
	
	$table->field[3]->name = "nominal_transfersort";
	$table->field[3]->headername = "Nominal Tujuan";// edit by yoanes: $bhs['Nominal Transfer'][$kdbhs];
	$table->field[3]->align = $F_HANDLER->RIGHT;
	
	$table->field[4]->name = "valuta_diterima";
	$table->field[4]->headername = $bhs['Valuta Diterima'][$kdbhs];
	$table->field[4]->align = $F_HANDLER->LEFT;
	
	$table->field[5]->name = "nominal_diterimasort";
	$table->field[5]->headername = $bhs['Nominal Diterima'][$kdbhs];
	$table->field[5]->align = $F_HANDLER->RIGHT;
	
	$table->field[6]->name = "nama_pengirim";
	$table->field[6]->headername = $bhs['Nama Pengirim'][$kdbhs];
	$table->field[6]->align = $F_HANDLER->LEFT;				
	
	$table->field[7]->name = "nama_bank_pengirim";
	$table->field[7]->headername = $bhs['Nama Bank'][$kdbhs];
	$table->field[7]->align = $F_HANDLER->LEFT;				
	
	$table->field[8]->name = "berita";
	$table->field[8]->headername = $bhs['Berita'][$kdbhs];
	$table->field[8]->align = $F_HANDLER->LEFT;	
	$x=8;
	if($div=="baru"||$div=="ekspor"){
		$table->field[9]->name = "COLOR";	
		$table->field[9]->hidden = true;	
		$x=9;		
	}elseif($div=="uangmuka"){
		$table->field[9]->name = "uraian_pembayran";
		$table->field[9]->headername = $bhs['Pembayaran'][$kdbhs];
		$table->field[9]->align = $F_HANDLER->LEFT;		
		
		$table->field[10]->name = "sandiKeterangan";
		$table->field[10]->headername = $bhs['Sandi Keterangan'][$kdbhs];
		$table->field[10]->align = $F_HANDLER->LEFT;		
		
		$table->field[11]->name = "keterangan";	
		$table->field[11]->hidden = true;
		
		$table->field[12]->name = "idlld";
		$table->field[12]->hidden = true;
		$x=12;	
	}elseif($div=="terlaporkan"){						
		$table->field[9]->name = "uraian_dana";
		$table->field[9]->headername = $bhs['Tipe Dana'][$kdbhs];;
		$table->field[9]->align = $F_HANDLER->LEFT;			
		$x=9;		
	}
	
	$x++;
	$table->field[$x]->name = "nominal_transfersort";
	$table->field[$x]->hidden = true;
	$x++;
	$table->field[$x]->name = "nominal_diterimasort";
	$table->field[$x]->hidden = true;
	if($jumRek>1){
		$x++;
		$table->field[$x]->name = "norek";
		$table->field[$x]->headername = $bhs['No. Rek'][$kdbhs];
	}
	$x++;
	$table->field[$x]->name = "reference_number";
	$table->field[$x]->headername = $bhs['No. Ref'][$kdbhs];
	$x++;
	$table->field[$x]->name = "Status_dana";
	$table->field[$x]->headername = $bhs['Status Dana'][$kdbhs];;
	$table->field[$x]->align = $F_HANDLER->LEFT;			
	$x++;
	$table->field[$x]->name = "kode_nonekspor";
	$table->field[$x]->headername = $bhs['Sandi Keterangan'][$kdbhs];;
	$table->field[$x]->align = $F_HANDLER->CENTER;	
	$table->drawTable();
	$conn->disconnect();
				
}
?>