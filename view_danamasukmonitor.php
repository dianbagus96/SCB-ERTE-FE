<?php
if(in_array($_SESSION["priv_session"],array("0"))==false){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}else{
$jumRek = count(explode(",",$_SESSION['noRek']));

set_time_limit(900);
require_once("conf.php");
require_once("dbconndb.php");
$connDB->connect();

$cari = str_replace('\\','',$_POST["q"]);
$_POST["q"] = strFilter($_POST["q"]);
?>
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="border-bottom:1px solid #D7D7D7;">
			<div style="padding-bottom:9px;">
			<span style="color:#1a68a4; font-size:16px; font-weight:bold;" id="view">
			<?php
			$messageBox = "<div style='background:#E5EEF5;padding:5px;border:1px #CCC solid;' class='msgRespon'>
							<img src='".base_url."img/accept.png' style='border:none'> ".$_SESSION['respon']."
							</div>";							
			if($_SESSION['respon']!=""){
				echo $messageBox;
				$_SESSION['respon']="";
			}else{
				echo "Dana Masuk <br />"; 								
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
				<form name="frmCari" method="post" action="<?php echo base_url."modul/danamasuk/monitor";?>">				
				<table>
					<tr>
						<td><?php echo $bhs['Kategori'][$kdbhs]?></td>
						<td><select name="field" id="field" onChange="javascript:cekField(this.value);">
						<?php 									
						$x1 = array("nama_pengirim","valuta_transfer","nominal_transfer","tgl_transaksi","nama_bank_pengirim","berita");						
						$x2 = array($bhs['Nama Pengirim'][$kdbhs],$bhs['Valuta Transfer'][$kdbhs],$bhs['Nominal Transfer'][$kdbhs],$bhs['Valuta Diterima'][$kdbhs],$bhs['Nominal Diterima'][$kdbhs],$bhs['Tanggal Transaksi'][$kdbhs],$bhs['Nama Bank'][$kdbhs],$bhs['Berita'][$kdbhs]);										
						array_push($x1, "norek");
						array_push($x2, $bhs['No. Rek'][$kdbhs]);
						array_push($x1, "reference_number");
						array_push($x2, $bhs['No. Ref'][$kdbhs]);
						//array_push($x1, "tipe_dana");
						//array_push($x2, "Tipe Dana");
						
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
									echo '<button type="button" onclick=location.href="'.base_url.'modul/danamasuk/monitor" style="width:75px" class="btn_2">'.$bhs['Batal'][$kdbhs].'</button></td>';
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
	echo "<fieldset style='border:none;padding:10px;font-size:11px;font-family:Tahoma;padding-bottom:-20px;border-bottom:1px #CCC solid'>
			<b>Catatan tipe dana :</b>
			<ul style='margin-bottom:-5px;'>
			<span style='background:#F4F99B;width:10px;border:1px #999 solid;margin-left:-30px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> &nbsp; 1. ".$bhs['Dana Masuk Baru'][$kdbhs]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<span style='background:#FFF;width:10px;border:1px #999 solid;margin-left:-30px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> &nbsp;2. ".$bhs['Ekspor'][$kdbhs]."	
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<span style='background:#E5EEF5;width:10px;border:1px #999 solid;margin-left:-30px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> &nbsp;3. ".$bhs['Non Ekspor'][$kdbhs]."	
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<span style='background:#BDE6D2;width:10px;border:1px #999 solid;margin-left:-30px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> &nbsp;4. ".$bhs['Uang Muka'][$kdbhs]."	
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<span style='background:#FAC7B8;width:10px;border:1px #999 solid;margin-left:-30px;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> &nbsp;5. ".$bhs['Dana Terlaporkan'][$kdbhs]."	
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			</ul>
		</fieldset>";
	
	$orderby = setSortir(array('sort'=>'id','order'=>'desc'),$x1);	
	$tg1 = $_POST["tg1"];
	$tg2 = $_POST["tg2"];
	$sql = "select a.iddanamasuk as ID, TO_CHAR(a.tgl_transaksi,'DD-MM-YYYY') AS tgl_transaksi, a.valuta_transfer,
			TO_CHAR(a.nominal_transfer,'999,999,999,999,999') AS nominal_transfer,
			a.valuta_diterima,
			TO_CHAR(a.nominal_diterima,'999,999,999,999,999')AS nominal_diterima,			
			a.nama_pengirim, a.nama_bank_pengirim,a.berita,
			a.nominal_transfer as nominal_transfersort, a.nominal_diterima as nominal_diterimasort,
			a.norek,
			a.reference_number,
			a.kd_dana, a.flag_used 
			from tbldmdanamasuk a";	#kecuali campuran tidak ada
			
	$where = array("1"=>"kd_dana ='00' and flag_used='0'","2"=>"kd_dana ='01' and flag_used='0'",
					"3"=>"kd_dana ='02' and flag_used='0'",
					"4"=>"kd_dana ='04' and flag_used='0'","5"=>"flag_used in('1','2')");
	$semua = " where a.kd_dana in ('00','01','02','04') and flag_used in ('0','1','2')";				
	if($_POST["Submit"]=="submit" ){						
		if($_POST["field"] == "tgl_transaksi"){
			$sql .= $semua." and tgl_transaksi BETWEEN to_date('".$tg1 ."','DD-MM-YYYY') AND  to_date('".$tg2 ."','DD-MM-YYYY') "; 
		}else if($_POST["field"] == "tipe_dana"){
			if($_POST["q"]<=5){
				$sql .= " where ".$where[$_POST["q"]];
			}else{
				$sql .= " where 1=2";
			}			
		} else {
			$sql .= $semua." and upper(". strFilter($_POST["field"]) .") Like '%". strtoupper($_POST["q"]) ."%'";							
		}
	}else{
		$sql .= $semua;
	} 
	
	$connDB->connect();
	//sql gabungan
	$sql = $sql . $orderby;
	//echo $sql;
	$table = new HTMLTableColorRTE();	
	$table->connection = $connDB;
	$table->width = "100%";
	$table->navRowSize = 10;	
	$table->SQL = $sql;
	$table->color = 2;
	// elemen data yang akan di passing
	$cols = array();	
	$data = array();
	$table->showRDPanel(false,$F_HANDLER->BOTH,$cols,$data);
	
	$table->showCheckBox(false,$cols);	
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
	$table->field[3]->headername = $bhs['Nominal Transfer'][$kdbhs];
	$table->field[3]->align = $F_HANDLER->RIGHT;
	
	$table->field[4]->name = "valuta_diterima";
	$table->field[4]->headername = $bhs['Valuta Diterima'][$kdbhs];
	$table->field[4]->align = $F_HANDLER->LEFT;
	$table->field[4]->hidden = true;
	
	$table->field[5]->name = "nominal_diterimasort";
	$table->field[5]->headername = $bhs['Nominal Diterima'][$kdbhs];
	$table->field[5]->align = $F_HANDLER->RIGHT;
	$table->field[5]->hidden = true;
	
	$table->field[6]->name = "nama_pengirim";
	$table->field[6]->headername = $bhs['Nama Pengirim'][$kdbhs];
	$table->field[6]->align = $F_HANDLER->LEFT;				
	
	$table->field[7]->name = "nama_bank_pengirim";
	$table->field[7]->headername = $bhs['Nama Bank'][$kdbhs];
	$table->field[7]->align = $F_HANDLER->LEFT;				
	
	$table->field[8]->name = "berita";
	$table->field[8]->headername = $bhs['Berita'][$kdbhs];
	$table->field[8]->align = $F_HANDLER->LEFT;	
	
	$x=9;
	$table->field[$x]->name = "nominal_transfersort";
	$table->field[$x]->hidden = true;
	$x++;
	$table->field[$x]->name = "nominal_diterimasort";
	$table->field[$x]->hidden = true;
	$x++;
	$table->field[$x]->name = "norek";
	$table->field[$x]->headername = $bhs['No. Rek'][$kdbhs];
	$x++;
	$table->field[$x]->name = "reference_number";
	$table->field[$x]->headername = $bhs['No. Ref'][$kdbhs];
	$x++;
	$table->field[$x]->name = "kd_dana";
	$table->field[$x]->hidden = true;
	$x++;
	$table->field[$x]->name = "flag_used";
	$table->field[$x]->hidden = true;
	
	$table->drawTable();
	$conn->disconnect();
				
}
?>