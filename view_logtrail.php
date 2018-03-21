<?php
if(in_array($_SESSION["priv_session"],array("0","3"))==false){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}else{
	
set_time_limit(0);
ini_set ('max_execution_time', '3600' );
ini_set ("memory_limit","512M");
ini_set("post_max_size","512M");
ini_set("mssql.textlimit","2147483647");
ini_set("mssql.textsize ","2147483647");

require_once("conf.php");
global $conn;
$conn->connect();

$cari = str_replace('\\','',$_POST["q"]);
$_POST["q"] = strFilter($_POST["q"]); 

if($_POST["delete"]=="yes"){
	$cbx = split(";",$_POST["radiopanel"]);
	$id = $cbx[0];	
	$sql = "Delete TBLLOG Where ID = '$id'";
	$data = $conn->execute($sql);
}
?>
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="border-bottom:1px solid #D7D7D7;">
			<div style="padding-bottom:9px;">
			<span style="color:#1a68a4; font-size:16px; font-weight:bold;">
			Browse Audit Log Trail
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
				<form name="frmCari" method="post" action="<?php echo base_url;?>modul/audit/logtrail">			
				<table>
					<tr>
						<td>Category</td>
						<td><select name="field" id="field" onChange="javascript:cekField(this.value);">
							<?php 
							$x1 = array("IDTRANS","TGL","USERNAME","ACTIVITY"/*,"STATUS"*/);
							$x2 = array("- TRANS"," - TANGGAL","- USERNAME","- AKTIVITAS"/*,"- STATUS"*/);
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
									echo '<button type="button" onclick=location.href="'.base_url.'modul/audit/logtrail" style="width:75px" class="btn_2">Cancel</button></td>';
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
<iframe width=174 height=189 name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="<?php echo base_url?>ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;"></iframe>
<?php
	$orderby = setSortir(array('sort'=>'tgl','order'=>'desc'),$x1);
	
	$tg1 = $_POST["tg1"];
	$tg2 = $_POST["tg2"];
	
	if($_SESSION['priv_session']=='0'){
		$sql = "select ID, IDTRANS, TO_CHAR(TGL,'DD-MM-YYYY') as TANGGAL, USERNAME, ACTIVITY, STATUS from TBLLOG";
		if($_POST["field"] == "TGL"){
			$sql = $sql ." WHERE TGL BETWEEN TO_DATE('".$tg1 ."','YYYY-MM-DD') AND  TO_DATE('".$tg2 ."','YYYY-MM-DD') "; 
		}
		if($_POST["q"] && $_POST["field"] != "TGL"){
			$sql = $sql ." where LOWER(". $_POST["field"] .") Like '%". strtolower($_POST["q"]) ."%'";				
		}
	}elseif($_SESSION['priv_session']=='3'){
		$sql = "select a.ID, a.IDTRANS, TO_CHAR(TGL,'DD-MM-YYYY') as TANGGAL, a.USERNAME, a.ACTIVITY, a.STATUS from TBLLOG a, TGCMSSP b 
				where a.IDTRANS=b.ID and b.IDINI='".$_SESSION['ID']."' ";
		if($_POST["field"] == "TGL"){
			$sql = $sql ." WHERE TGL BETWEEN TO_DATE('".$tg1 ."','YYYY-MM-DD') AND  TO_DATE('".$tg2 ."','YYYY-MM-DD') ";  
		}
		if($_POST["q"] && $_POST["field"] != "TGL"){
			$sql = $sql ." AND LOWER(". $_POST["field"] .") Like '%". strtolower($_POST["q"]) ."%'";				
		}

	}

//sql gabungan
$sql = $sql . $orderby;
//echo $sql;


$table = new HTMLTable();
$table->connection = $conn;
$table->width = "100%";
$table->navRowSize = 10;
$table->SQL = $sql;
// elemen data yang akan di passing
$cols = array();
$cols[0] = 0;
//$cols[1] = 4;

//untuk ambil tipe dokumen BC 2.0 atau BC 2.3
$data = array();
$table->showCheckBox(false,$cols);	
$table->showPager(true,$F_HANDLER->BOTTOM,50,20);
$table->showRDPanel(false,$F_HANDLER->BOTH,$cols,$data);

$table->field[0]->name = "ID";
$table->field[0]->headername = "ID";
$table->field[0]->align = $F_HANDLER->LEFT;
$table->field[0]->hidden = true;

$table->field[1]->name = "IDTRANS";
$table->field[1]->headername = "TRANS";
$table->field[1]->align = $F_HANDLER->LEFT;

$table->field[2]->name = "TGL";
$table->field[2]->headername = "TANGGAL";
$table->field[2]->align = $F_HANDLER->LEFT;


$table->field[3]->name = "USERNAME";
$table->field[3]->headername = "USERNAME";
$table->field[3]->align = $F_HANDLER->LEFT;

$table->field[4]->name = "ACTIVITY";
$table->field[4]->headername = "AKTIVITAS";
$table->field[4]->align = $F_HANDLER->LEFT;

$table->field[5]->name = "STATUS";
$table->field[5]->headername = "STATUS";
$table->field[5]->align = $F_HANDLER->LEFT;
$table->field[5]->hidden=true;

$table->drawTable();

$for = "auditLogTrail";
include ("form_csv.php");
}
?>