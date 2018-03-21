<?php
if(in_array($_SESSION["priv_session"],array("0","3"))==true  || substr($_SESSION["AKSES"],3,1)!="1"){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}else{
if($_GET['seccode']!=md5($_GET['id'])){ echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;}
set_time_limit(900);
require_once("conf.php");
require_once("dbconndb.php");
require_once("library/tbLite/develop/htmltable.classcolor.php");
//echo $div;

$cari = str_replace('\\','',$_POST["q"]);
$_POST["q"] = strFilter($_POST["q"]);
?>
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="border-bottom:1px solid #D7D7D7;">
			<div style="padding-bottom:9px;">
			<span style="color:#1a68a4; font-size:16px; font-weight:bold;">
			<?php			
			$judul = array("pilihdanamasuk"=>str_replace("-","",$bhs['Pilih Dana Masuk'][$kdbhs]),"pilihtanpadanamasuk"=>str_replace("-","",$bhs['Bentuk PEB'][$kdbhs]),"pilihdanamasukpending"=>$bhs['Pilih Pending'][$kdbhs],"pilihdanamasukpendings"=>$bhs['Pilih Pending'][$kdbhs]);				
			$messageBox = "<div style='background:#E5EEF5;padding:5px;border:1px #CCC solid;'>
							<img src='".base_url."img/accept.png' style='border:none'> ".$_SESSION['respon']."
							</div>";
			if($_SESSION['respon']!=""){
				echo $messageBox;
				$_SESSION['respon']="";
			}else{
				switch($div){
					case "pilihdanamasuk" : $link = base_url."modul/peb/baru";break;
					case "pilihdanamasukpending" : $link = base_url."modul/peb/plus";break;
					case "pilihtanpadanamasuk" : $link = base_url."modul/peb/plus";break;
					case "pilihdanamasukpendings" : $link = base_url."modul/peb/terlaporkan";break;
				}
				echo "<a href='".$link."' style='text-decoration:none;color:inherit'><img src='".base_url."img/back.png' title='kembali' style='border:none'></a> ".$judul[$div]." <br />"; 
			}
			?>							
			</span>
			</div>
		</td>
	</tr>
</table>
<?php if($div=="pilihtanpadanamasuk"){
	$connDB->connect();
	$sts =  implode(",",$_POST["cbx"]);
	$sql = " select fl_send, case when LENGTH(FILEUPLOAD) > 4 then '1' else '0' end as status  from tbldmpeb where idpeb = '$sts'";
	$exec = $connDB->query($sql);
	$exec->next();
	$status = $exec->get("fl_send");
	$upload = $exec->get("status");
	if( $status == 1){
		echo "<script type='text/javascript'> 
						$(document).ready(function(){
							jAlert('PEB sudah dikirimkan sebelumnya','',function(r){
								if(r==true) window.location.href='".base_url."modul/peb/plus';\n				
							});		
						})
					</script>";	
					exit;
	}
	if( $upload == 0){
		echo "<script type='text/javascript'> 
						$(document).ready(function(){
							jAlert('Dokumen PEB belum dilangkapi','',function(r){
								if(r==true) window.location.href='".base_url."modul/peb/plus';\n				
							});		
						})
					</script>";	
					exit;
	}
	?>

<form id="frmCari" nama="frmCari"  method="post" action="<?php echo base_url?>modul/peb/rtetanpadanamasuk">
<?php }?>
<div style="margin-top:18px; width: 100%; background:#F0F0F0; border: 1px solid #D7D7D7;">
	<div style="font-family:arial; font-size:11px; padding-top:10px; padding-bottom:10px; padding-left:10px;">
		<table cellpadding="0" cellspacing="0" style="font-family:arial; font-size:11px;">
			<tr>
				<td width="1223" style="padding-left:20px; font-family:arial; font-size:11px; color:#333333; font-weight:bold;">                              
				<?php	
				$connDB->connect();							                               
				$sql = "select idPEB as ID, KPBC,
				CASE LENGTH(p.NPWP) WHEN 15 THEN (SUBSTR(p.NPWP,1,2)||'.'||SUBSTR(p.NPWP,3,3)||'.'||SUBSTR(p.NPWP,6,3)||'.'||SUBSTR(p.NPWP,9,1)||
				'-'||SUBSTR(p.NPWP,10,3)||'.'||SUBSTR(p.NPWP,13,3)) ELSE p.NPWP END AS NPWP,	
				NAMA_EKSPORTIR, NO_PEB, TO_CHAR(tgl_PEB,'DD-MM-YYYY') AS TGL_PEB, VALUTA , 
				TO_CHAR(FOB,'999,999,999,999,999.99') AS FOB_FORMAT,
				FOB, KURS
				from tbldmpeb p where idPEB =".strFilter($_GET['id']);	
				//echo $sql;//die();						
				
				$data = $connDB->query($sql); $data->next();
				$NILAI_PEB = $data->get('FOB');
				?>
				<table width="1032">
					<tr>
						<td width="170">KPBC</td>
						<td width="12">:</td>
						<td width="421"><?php echo $data->get("KPBC")?></td>
						<td width="133"><?php echo $bhs['Tanggal PEB'][$kdbhs]?></td>
						<td width="10">:</td>
						<td width="325"><?php echo $data->get("TGL_PEB")?></td>
					</tr>									
					<tr>
						<td><?php echo $bhs['Eksportir'][$kdbhs]?></td><td>:</td><td><?php echo $data->get("NAMA_EKSPORTIR")?></td>
						 <td><?php echo $bhs['Nilai PEB'][$kdbhs]?></td>
						<td width="10">:</td>
						 <td><?php echo $data->get("FOB_FORMAT")." (".$data->get("VALUTA").")"?></td>
				  </tr>
					<tr>
					  <td>NPWP</td>
					  <td>:</td>
					  <td><?php echo $data->get("NPWP")?></td>
					  <?php
					  if($div=="pilihtanpadanamasuk"){?>
					  <td><?php echo $bhs['Tanggal Jatuhtempo'][$kdbhs]?></td>
					  <td>:</td>                                      
					  <td><input name="tgl" type="text" id="tgl"  size="10" >
					  <a href="javascript:void(0)" onClick="if(self.gfPop)gfPop.fPopCalendar(document.getElementById('tgl')); return false;"><img name="popcal" align="absmiddle" src="<?php echo base_url?>img/calbtn.gif" alt="Pilih tanggal" width="34" height="22" border="0" id="gbrClndr"></a>
					  </td>
					  
					   <?php
					   }else{?>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						<?php
						}
						?>
					</tr>
					<tr valign="top">  <?php
					  if($div=="pilihtanpadanamasuk"){?>
						<td><?php echo $bhs['No. PEB'][$kdbhs]?></td>
						<td>:</td>
						<td><?php echo $data->get("NO_PEB")?></td>
						 <td><?php echo $bhs['Jenis Pembayaran'][$kdbhs]?></td>
					  <td>:</td>
						<td>	<select name="jnsbyr" id="jnsbyr" style="width:120px" >
								<option value="011">Usance L/C</option>
								<option value="012">Konsinyasi</option>
								<option value="013">Kemudian</option>
								<option value="014">Collection</option>
							</select>
					  </td>
						 <?php
					   }else{?>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						<?php
						}
						?>
					</tr>                                 
				</table>                                														
			  </td>
			</tr>
		</table>                        
	</div>
</div>
	
<?php
//exit;
if($div=="pilihdanamasuk"||$div=="pilihdanamasukpending" ||$div=="pilihdanamasukpendings"){
echo "<fieldset style='border:none;padding:10px;font-size:11px;font-family:Tahoma;padding-bottom:-20px;border-bottom:1px #CCC solid'>
	<b>Catatan :</b>
	<ul style='margin-bottom:-5px;'>
	<span style='background:#F4F99B;width:10px;border:1px #999 solid;margin-left:-30px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> &nbsp;".$bhs['Uang Muka Dilaporkan'][$kdbhs]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<span style='background:#E5EEF5;width:10px;border:1px #999 solid;margin-left:-30px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> &nbsp;".$bhs['Ekspor'][$kdbhs]."
	</ul>
</fieldset>";
}elseif($div=="pilihtanpadanamasuk"){?>
<div style="text-align:right">
<input type="hidden" name="PEB[idpeb]" value="<?php echo $data->get("ID")?>" />
<input type="hidden" name="PEB[valuta]" value="<?php echo $data->get("VALUTA")?>" />
<input type="hidden" name="PEB[kurs]" value="<?php echo $data->get("KURS")?>" /> 
<input type="hidden" name="PEB[nominal]" value="<?php echo $NILAI_PEB?>" />    
<button type="submit" onclick="javascript:$('#frmCari').submit();" class="btn_2" style="width:75px">Process</button>                
</div>
</form>
<iframe width=174 height=189 name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="<?php echo base_url?>ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">
</iframe>
<?php
}
	if($div=='pilihdanamasuk' || $div=='pilihdanamasukpending' || $div=='pilihdanamasukpendings'){
		
		$order = strFilter($_GET['order']);
		$sort = strFilter($_GET['sort']);
		$x1 = array("nama_pengirim","tgl_transaksi","nominal_transfer","nominal_diterima","berita");					
		$orderby = setSortir(array('sort'=>'ID','order'=>'desc'),$x1);
		$tg1 = $_POST["tg1"];
		$tg2 = $_POST["tg2"];
	   
		$sql = "select a.iddanamasuk as ID,a.nama_pengirim,a.nama_bank_pengirim, TO_CHAR(a.tgl_transaksi,'DD-MM-YYYY') AS tgl_transaksi, 
				TO_CHAR(a.nominal_transfer,'999,999,999,999,999.99') ||' ('||a.valuta_transfer||')' AS nominal_transfer,
				TO_CHAR(a.nominal_diterima,'999,999,999,999,999.99') ||' ('||a.valuta_diterima||')' AS nominal_diterima,
				a.berita,'' as nilaipembagian, r.sandi_keterangan as sandiKeterangan,
				'".$data->get("VALUTA")."' as valuta,
				'".$data->get("ID")."' as idPeb,
				'".$data->get("KURS")."' as kurs,
				'' as keterangan,
				a.nominal_transfer as nilai_dhe,
				kd_dana,
				idLLD,
				(select (k.NOMINAL) from tbldmkurs k where k.valuta=a.valuta_transfer
				and DATEDIFF(a.tgl_transaksi,k.tglawal)>=0 and
				DATEDIFF(a.tgl_transaksi,k.tglakhir)<=0 and ROWNUM = 1  ) as KURS_IDR,
				(select (k.NOMINAL*a.nominal_transfer) from tbldmkurs k where k.valuta=a.valuta_transfer
				and DATEDIFF(a.tgl_transaksi,k.tglawal)>=0 and
				DATEDIFF(a.tgl_transaksi,k.tglakhir)<=0 and ROWNUM = 1 ) as IDR	,
				a.jns_uangmuka as JENIS_UANG_MUKA, '' as IS_LAST_CHOISE,
				a.nominal_transfer AS NILAI_DHE,
				
				(select (k.NOMINAL) from tbldmkurs k where k.valuta='".trim($data->get("VALUTA"))."'
				and DATEDIFF(a.tgl_transaksi,k.tglawal)>=0 and
				DATEDIFF(a.tgl_transaksi,k.tglakhir)<=0 and ROWNUM = 1 ) as IDR_PEB,
				a.valuta_transfer as kurs_dhe
				
				from tbldmdanamasuk a left join tblfcrte r on a.iddanamasuk = r.iddanamasuk
				where ((a.kd_dana in('01') and a.flag_used='0') or (a.flag_used='1' and a.kd_dana='04' and r.status = '1' and r.sandi_keterangan != '0300'))  and a.NoRek in (".trim($_SESSION['noRek']).")";
			
		if($_POST["Submit"]=="submit" ){						
			if($_POST["field"] == "tgl_transaksi"){
				$sql = $sql ." And tgl_transaksi BETWEEN TO_DATE('".$tg1 ."','DD-MM-YYYY') AND  TO_DATE('".$tg2 ."','DD-MM-YYYY') "; 
			} else {
				$sql = $sql ." And upper(". strFilter($_POST["field"]) .") Like '%". strtoupper($_POST["q"]) ."%'";							
			}
		} 
		
		$connDB->connect();
		//sql gabungan
		$sql = $sql . $orderby;
		//echo $sql;
		$table = new HTMLTableColor();
		$table->opsiPlus1=true;
		$table->connection = $connDB;
		$table->width = "100%";					
		$table->backlink = $div;
		$table->navRowSize = 10;
		$table->ajaxMod1 = 7;
		$table->ajaxMod2 = 8;
		$table->ajaxMod10 = 12;
		$table->ajaxMod13 = false;	
		$table->ajaxMod17 = 18;	
		$table->ajaxMod18 = 19;		
		$table->ajaxMod19 = 4;		
		
		$table->SQL = $sql;		
		//echo $sql;				
		// elemen data yang akan di passing
		$cols = array();
		$cols[0] = 0; #iddanamasuk
		$cols[1] = 3; #tgltransaksi
		$cols[2] = 9; #valuta
		$cols[3] = 10; #idpeb
		$cols[4] = 11; #kurs
		$cols[5] = 15; #idlld
		$cols[6] = 18; #jnsUangMuka
		$cols[7] = 13; #nilaitransfer dhe
		$cols[8] = 21; #kurs dhe
		
		$data = array();
		$data[] = array("#",$bhs['Pilih Proses'][$kdbhs]);
		$data[] = array(base_url."modul/peb/rtedanamasuk",$bhs['Simpan Pemilihan Dana Masuk'][$kdbhs]);
		$table->showCheckBox(true,$cols);	
		$table->showPager(false,$F_HANDLER->BOTTOM,500,10);
		$table->showRDPanel(true,$F_HANDLER->BOTH,$cols,$data);
		$table->nominalDiterima = $NILAI_PEB;
		
		$table->field[0]->name = "ID";
		$table->field[0]->headername = "ID";
		$table->field[0]->align = $F_HANDLER->LEFT;
		$table->field[0]->hidden = true;
				
		$table->field[1]->name = "nama_pengirim";
		$table->field[1]->headername = $bhs['Nama Pengirim'][$kdbhs]; 
		$table->field[1]->align = $F_HANDLER->LEFT;	
		
		$table->field[2]->name = "nama_bank_pengirim";
		$table->field[2]->headername = $bhs['Nama Bank'][$kdbhs];
		$table->field[2]->align = $F_HANDLER->LEFT;	
		
		$table->field[3]->name = "tgl_transaksi";
		$table->field[3]->headername = $bhs['Tanggal Transaksi'][$kdbhs];
		$table->field[3]->align = $F_HANDLER->LEFT;
		
		$table->field[4]->name = "nominal_transfer";
		$table->field[4]->headername = $bhs['Nominal Transfer'][$kdbhs];
		$table->field[4]->align = $F_HANDLER->LEFT;
		
		$table->field[5]->name = "nominal_diterima";
		$table->field[5]->headername = $bhs['Nominal Diterima'][$kdbhs];
		$table->field[5]->align = $F_HANDLER->LEFT;
		
		$table->field[6]->name = "berita";
		$table->field[6]->headername = $bhs['Berita'][$kdbhs];
		$table->field[6]->align = $F_HANDLER->LEFT;			
		
		$table->field[7]->name = "nilaipembagian";
		$table->field[7]->headername = $bhs['Nilai PEB'][$kdbhs];
		$table->field[7]->align = $F_HANDLER->LEFT;
		
		$table->field[8]->name = "sandiKeterangan";
		$table->field[8]->headername = $bhs['Sandi Keterangan'][$kdbhs];
		$table->field[8]->align = $F_HANDLER->LEFT;					
		
		$table->field[9]->name = "valuta";
		$table->field[9]->headername = "valuta";
		$table->field[9]->align = $F_HANDLER->LEFT;											
		$table->field[9]->hidden = true;
		
		$table->field[10]->name = "idPeb";
		$table->field[10]->headername = "idPeb";
		$table->field[10]->align = $F_HANDLER->LEFT;											
		$table->field[10]->hidden = true;
		
		$table->field[11]->name = "kurs";
		$table->field[11]->headername = "kurs";
		$table->field[11]->align = $F_HANDLER->LEFT;											
		$table->field[11]->hidden = true;
		
		$table->field[12]->name = "keterangan";
		$table->field[12]->headername = "Keterangan";
		$table->field[12]->align = $F_HANDLER->LEFT;											
		$table->field[12]->hidden = true;
		
		$table->field[13]->name = "nilai_dhe";
		$table->field[13]->hidden =  true;

		$table->field[14]->name = "kd_dana";
		$table->field[14]->hidden =  true;
		
		$table->field[15]->name = "idlld";
		$table->field[15]->hidden =  true;
		
		$table->field[16]->name = "KURS_IDR";
		$table->field[16]->hidden =  true;
		
		$table->field[17]->name = "IDR";
		$table->field[17]->hidden =  true;
		
		$table->field[18]->name = "JENIS_UANG_MUKA";
		$table->field[18]->headername = $bhs['Ket'][$kdbhs];
		
		$table->field[19]->name = "IS_LAST_CHOISE";
		$table->field[19]->headername = $bhs['Terakhir'][$kdbhs];
		
		$table->field[20]->hidden= true;
		$table->field[21]->hidden= true;
		$table->field[22]->hidden= true;
		
		$table->drawTable();
		$conn->disconnect();
	}
}
?>