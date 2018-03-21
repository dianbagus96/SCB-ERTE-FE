<?php
if(in_array($_SESSION["priv_session"],array("0","1"))==false){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}elseif(time()>$_SESSION['timeout_session']){	
	echo "<script> window.location.href='".base_url."err/7/".md5(7)."';</script>";exit;
}else{
	$_SESSION['timeout_session'] = TIMEOUT;
}

require_once("dbconn.php");
require_once("conf.php");
global $conn;
$conn->connect();

$cari = str_replace('\\','',$_POST["q"]);
$_POST["q"] = strFilter($_POST["q"]);
$data = $_POST["radiopanel"];
if($div=="delete" && trim($data)!=""){
	$d = split(";",$data);
	
	$hasil = delete("TBLDMSANDIRTE",array("ID"=>$d[0]),"",$conn);
	if($hasil){
		$_SESSION['respon'] = "Penghapusan Sandi berhasil";
		$_SESSION['statusRespon']=1;
	}
	
	$aktivitas = "Berhasil menghapus Sandi : ".$d[1];
	audit($conn,$aktivitas);					
	echo "<script> window.location.href='".base_url."modul/sandi/view';</script>";exit;
}
$conn->disconnect();
?>
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="border-bottom:1px solid #D7D7D7;">
			<div style="padding-bottom:9px;">
			<span style="color:#1a68a4; font-size:16px; font-weight:bold;">							
			<?php
			$messageBox =($_SESSION['statusRespon']==0)? 
						"<div style='background:#FDE9DF;padding:5px;border:1px #CCC solid;color:#633'>
						 <img src='".base_url."img/warninglogo.png' style='border:none'> ".$_SESSION['respon']."</div>" : 
						 "<div style='background:#E5EEF5;padding:5px;border:1px #CCC solid;'>
						 <img src='".base_url."img/accept.png' style='border:none'> ".$_SESSION['respon']."</div>";
						 
			if($_SESSION['respon']!=""){
				echo $messageBox;
				$_SESSION['respon'] = "";
			}else{
				echo "List Sandi Keteragan<br />";
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
				<form name="frmCari" method="post" action="<?php echo base_url?>modul/sandi/view" onSubmit="return cekForm();">				
				<table>
					<tr>
						<td>Category</td>
						<td><select name="field" id="SearchBase">
							<?php 
							$x1 = array("SANDI","NAMA_SANDI");
							$x2 = array(" - Sandi", " - Nama Sandi");
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
						<td>Search</td>
						<td><b id="SearchInput"><input name="q" type="text" id="q" size="50" value="<?php echo($cari);?>" ></b> <button type="submit" class="btn_2" style="width:73px">Search</button> 
						<?php if($cari){
								echo '<button type="button" onclick=location.href="'.base_url.'modul/sandi/view" style="width:75px" class="btn_2">Cancel</button></td>';
								}
							?>
					</tr>
				</table>								
				</form>								
				</td>
			</tr>
		</table>
	</div>
</div>
<?php		
	$orderby = setSortir(array('sort'=>'SANDI','order'=>'asc'),$x1);
	$conn->connect();
	$sql = "select ID, SANDI, NAMA_SANDI, KETERANGAN from TBLDMSANDIRTE WHERE 1 = 1 ";
		
	if($_POST["q"]){		
		$field = $_POST["field"];
			$sql = $sql ." AND LOWER(". $field .") Like '%". strtolower($_POST["q"]) ."%'";		
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
	$cols[1] = 1;
	$cols[2] = 2;
	$cols[3] = 3;
	$data = array();
	$data[] = array("#","Pilih Proses");
	$data[] = array(base_url."modul/sandi/edit"," - Edit Sandi");
	$data[] = array(base_url."modul/sandi/delete"," - Delete Sandi");	
	$table->showCheckBox(false,$cols);	
	$table->showPager(true,$F_HANDLER->BOTTOM,50,20);
	$table->showRDPanel(true,$F_HANDLER->BOTH,$cols,$data);

	$table->field[0]->name = "ID";	
	$table->field[0]->headername = "Corp ID";
	$table->field[0]->align = $F_HANDLER->LEFT;
	$table->field[0]->hidden = true;

	$table->field[1]->name = "SANDI";
	$table->field[1]->headername = "Sandi";
	$table->field[1]->align = $F_HANDLER->LEFT;
	
	$table->field[2]->name = "NAMA_SANDI";
	$table->field[2]->headername = "Nama Sandi";
	$table->field[2]->align = $F_HANDLER->LEFT;
	
	$table->field[3]->name = "KETERANGAN";
	$table->field[3]->headername = "Keterangan";
	$table->field[3]->align = $F_HANDLER->LEFT;

	$table->drawTable();	
	$conn->disconnect();
?>