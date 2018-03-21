<?php
if(trim($_SESSION["priv_session"])==""){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}else{
	
require_once("conf.php");
global $conn;
$conn->connect();
$cari = str_replace('\\','',$_POST["q"]);
$_POST["q"] = strFilter($_POST["q"]); 

?>

<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="border-bottom:1px solid #D7D7D7;">
			<div style="padding-bottom:9px;">
			<span style="color:#1a68a4; font-size:16px; font-weight:bold;">
			Browse Kurs
			</span><br />
			</div>
		</td>
	</tr>
</table>
<div style="margin-top:18px; width: 100%; background:#F0F0F0; border: 1px solid #D7D7D7;">
	<div style="font-family:arial; font-weight:lighter; font-size:11px; padding-top:10px; padding-bottom:10px; padding-left:10px;">
		<table cellpadding="0" cellspacing="0" width="100%" style="font-family:arial; font-weight:lighter; font-size:11px;">
			<tr>
				<td style="padding-left:20px; font-family:arial; font-size:11px; color:#333333; font-weight:bold;">
					<form name="frmCari" method="post" action="<?php echo base_url;?>modul/setting/kurs">					
					<table>
						<tr>
							<td>Category</td>
							<td><select name="field" id="field" onChange="javascript:cekField(this.value);">
								<?php 
								$x1 = array("VALUTA","WAKTU");
								$x2 = array("- Valuta","- Tanggal");
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
							<td>Search</td>										
							<td><input name="q" type="text" id="q" size="50" value="<?php echo($cari);?>"  >
								<input name="tg1" type="text" id="tg1" size="10" value="<?php echo($_POST['tg1']); ?>" readonly>
								<input type="button" name="popcal1" value="&#8225;" onClick="if(self.gfPop)gfPop.fPopCalendar(document.frmCari.tg1);return false;">
								<input name="space" type="text" style="border:0px;" value="s/d" size="3" readonly="true">
								<input name="tg2" type="text" id="tg2" value="<?php echo($_POST['tg2']); ?>" size="10" readonly>
								<input type="button" name="popcal2" value="&#8225;" onClick="if(self.gfPop)gfPop.fPopCalendar(document.frmCari.tg2);return false;">
								<button type="button" class="btn_2" onclick="cekForm(0);" name="search0" style="width:73px;">Search</button> 
								<button type="button" class="btn_2" onclick="cekForm(1);" name="search1"  style="width:73px;">Search</button>
								<?php if($cari || $_POST['tg1']){
										echo '<button type="button" onclick=location.href="'.base_url.'modul/setting/kurs" style="width:75px" class="btn_2">Cancel</button></td>';
									}
								?>
								<input type="hidden" name="Submit" value="submit"  />
							<?php					
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
<iframe width=174 height=189 name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="<?php echo base_url?>ipopeng.htm" scrolling="no" frameborder="0" 
style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;"></iframe>
<?php

	$orderby = setSortir(array('sort'=>'TGLAWAL','order'=>'desc'),$x1);			
	$tg1 = $_POST["tg1"];
	$tg2 = $_POST["tg2"];	

		$sql = "select VALUTA, TO_CHAR(TGLAWAL,'DD-MM-YYYY') as AWAL, TO_CHAR(TGLAKHIR,'DD-MM-YYYY') as AKHIR, NOMINAL  from tbldmkurs
				where  1=1 ";
		if (strFilter($_POST["q"]) == "" and $_SERVER['REQUEST_METHOD']!="POST"){
			$sql .= "and EXTRACT(YEAR FROM  TGLAWAL) = ".date('Y')." AND EXTRACT(MONTH FROM  TGLAWAL) = ".date('m');		
		}		
		if($_POST["field"] == "WAKTU"){
			$sql = $sql ." AND TGLAWAL BETWEEN TO_DATE('".$tg1 ."','DD-MM-YYYY') AND  TO_DATE('".$tg2 ."','DD-MM-YYYY') ";  
		}
		if($_POST["q"] && $_POST["field"] != "WAKTU"){
			$sql = $sql ." AND LOWER(". $_POST["field"] .") Like '%". strtolower($_POST["q"]) ."%'";
		}
	
	
	
	//sql gabungan
	$sql = $sql . $orderby;
	//echo $sql;
	$table = new HTMLTable();
	$table->connection = $conn;
	$table->width = "100%";
	$table->navRowSize = 15;
	$table->SQL = $sql;
	// elemen data yang akan di passing
	$cols = array();
	$cols[0] = 0;

	$data = array();
	$table->showCheckBox(false,$cols);	
	$table->showPager(true,$F_HANDLER->BOTTOM,45,20);
	$table->showRDPanel(false,$F_HANDLER->BOTH,$cols,$data);

	$x=0;
	$table->field[$x]->name = "VALUTA";
	$table->field[$x]->headername = "Valuta";
	$table->field[$x]->align = $F_HANDLER->LEFT;	
	
	$table->field[$x++]->name = "TGLAWAL";
	$table->field[$x]->headername = "Tanggal Awal";
	$table->field[$x]->align = $F_HANDLER->LEFT;

	$table->field[$x++]->name = "TGLAKHIR";
	$table->field[$x]->headername = "Tanggal Akhir";
	$table->field[$x]->align = $F_HANDLER->LEFT;
	
	$table->field[$x++]->name = "NOMINAL";
	$table->field[$x]->headername = "Nominal";
	$table->field[$x]->align = $F_HANDLER->LEFT;

	$table->drawTable();
	
}
?>