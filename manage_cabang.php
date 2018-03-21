<?php
if(in_array($_SESSION["priv_session"],array("0"))==false){
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
if($div=="delete"){	
	if(trim($data)==""){ echo "<script> window.location.href='".base_url."modul/cabang/view';</script>";exit;}	
	$d = split(";",$data);
	$s = "select NAMA FROM TCABANG WHERE ID = '".$d['0']."'";
	$dd = $conn->query($s); $dd->next();
	
	$aktivitas = "Berhasil menghapus cabang : ".strFilter($dd->get('NAMA'));
	audit($conn,$aktivitas);				
	
	$sql = "Delete TCABANG Where ID = '".$d[0]."'";	
	$data = $conn->execute($sql);
	if($data){
		$_SESSION['respon'] = "Penghapusan Cabang berhasil";
	}	
	echo "<script> window.location.href='".base_url."modul/cabang/view';</script>";exit;	
}
$conn->disconnect();
?>
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="border-bottom:1px solid #D7D7D7;">
			<div style="padding-bottom:9px;">
			<span style="color:#1a68a4; font-size:16px; font-weight:bold;">							
			<?php
			$messageBox =  "<div style='background:#E5EEF5;padding:5px;border:1px #CCC solid;'>
							<img src='".base_url."img/accept.png'> ".$_SESSION['respon']."</div>";
			if($_SESSION['respon']!=""){
				echo $messageBox;
				$_SESSION['respon'] = "";
			}else{
				echo "List Cabang <br />";
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
				<form name="frmCari" method="post" action="<?php echo base_url."modul/cabang/view"?>" onSubmit="return cekForm();">				
				<table>
					<tr>
						<td>Category</td>
						<td><select name="field" id="SearchBase">
							<?php 
							$x1 = array("KODE","NAMA");
							$x2 = array(" - Kode Cabang", " - Nama Cabang");
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
								echo '<button type="button" onclick=location.href="'.base_url.'modul/cabang/view" style="width:75px" class="btn_2">Cancel</button></td>';
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
	$orderby = setSortir(array('sort'=>'nama','order'=>'desc'),$x1);
	$conn->connect();
	$sql = "select ID, KODE,NAMA from TCABANG";
	
	if($_POST["q"]){		
		$sql = $sql ." WHERE LOWER(". $_POST["field"] .") Like '%". strtolower($_POST["q"]) ."%'";
	}
	//sql gabungan
	$sql = $sql . $orderby;	
	$table = new HTMLTable();
	$table->connection = $conn;
	$table->width = "100%";
	$table->navRowSize = 10;
	$table->SQL = $sql;
	// elemen data yang akan di passing
	$cols = array();
	$cols[0] = 0;
	$cols[1] = 5;
	//untuk ambil tipe dokumen BC 2.0 atau BC 2.3
	$data = array();
	//print_r($table);
	$data[] = array(base_url."modul/cabang/edit","Edit Cabang");
	$data[] = array(base_url."modul/cabang/delete","Delete Cabang");	
	$table->showCheckBox(false,$cols);	
	$table->showPager(true,$F_HANDLER->BOTTOM,50,20);
	$table->showRDPanel(true,$F_HANDLER->BOTH,$cols,$data);

	$table->field[0]->name = "ID";
	$table->field[0]->hidden= true;
	$table->field[0]->headername = "User ID";
	$table->field[0]->align = $F_HANDLER->LEFT;

	$table->field[1]->name = "KODE";
	$table->field[1]->headername = "Kode Cabang";
	$table->field[1]->align = $F_HANDLER->LEFT;
	
	
	$table->field[2]->name = "NAMA";
	$table->field[2]->headername = "Nama Cabang";
	$table->field[2]->align = $F_HANDLER->LEFT;
	
	$table->drawTable();	
	$conn->disconnect();

?>