<?php
if(in_array($_SESSION["priv_session"],array("0","3"))==false){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}else{

require_once("dbconn.php");
require_once("conf.php");
global $conn;
$conn->connect();
$cari = str_replace('\\','',$_POST["q"]);
$_POST["q"] = strFilter($_POST["q"]);
$data = $_POST["radiopanel"];
if($div=="delete"){
	if(trim($data)==""){ echo "<script> window.location.href='".base_url."modul/email/view';</script>";exit;}	
	$d = split(";",$data);
	$s = "select EMAIL from TEMAIL where ID = '".$d[0]."'";
	$dt = $conn->query($s); $dt->next();
	$sql = "Delete TEMAIL Where ID = '".$d[0]."'";	
	$data = $conn->execute($sql);
	
	
	$aktivitas = "Berhasil menghapus email dengan alamat : ".$dt->get('EMAIL');
	
	audit($conn,$aktivitas);
	$_SESSION['respon'] = "Penghapusan Email berhasil";	
	echo "<script> window.location.href='".base_url."modul/email/view';</script>";exit;
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
								<img src='".base_url."img/accept.png' style='border:none'> ".$_SESSION['respon']."</div>";
				if($_SESSION['respon']!=""){
					echo $messageBox;
					$_SESSION['respon']="";
				}else{
					echo "List Email<br />";
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
						<form name="frmCari" method="post" action="<?php echo base_url?>modul/email/view" onSubmit="return cekForm();">						
						<table>
							<tr>
								<td>Category</td>
								<td><select name="field" id="SearchBase">
								<?php 
								$x1 = array("a.NAMA","a.EMAIL","a.ROLE","b.NAMA","a.AKTIF");
								$x2 = array(" - Nama", " - Email", "- Role ","- Cabang","- Status");
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
								<td><b id="SearchInput"><input name="q" type="text" id="q" size="50" value="<?php echo($cari);?>" ></b> 
									<button type="submit" class="btn_2" style="width:73px">Search</button> 
								<?php if($cari){
										echo '<button type="button" onclick=location.href="'.base_url.'modul/email/view" style="width:75px" class="btn_2">Cancel</button></td>';
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
	$valid = array("namaorg","email","role","nama","aktif");
	$orderby = setSortir(array('sort'=>'nama','order'=>'desc'),$valid);
	
	$conn->connect();
	if($_SESSION['priv_session']=="0"){
		$sql = "select a.ID as ID, a.NAMA as NAMAORG, a.EMAIL, a.ROLE, b.NAMA, a.AKTIF AS AKTIF from TEMAIL a, TCABANG b WHERE a.CABANG=b.KODE ";
	}elseif($_SESSION['priv_session']=="3"){
		$sql = "select a.ID as ID, a.NAMA as NAMAORG, a.EMAIL, a.ROLE, b.NAMA, a.AKTIF AS AKTIF from TEMAIL a, TCABANG b WHERE a.CABANG=b.KODE and a.CABANG='".trim($_SESSION['brachsCode'])."'";
	}
	if($_POST["q"]){		
		$sql = $sql ." and LOWER(". strFilter($_POST["field"]) .") Like '%". strtolower($_POST["q"]) ."%'";
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
	//untuk ambil tipe dokumen BC 2.0 atau BC 2.3
	$data = array();
	
	$data[] = array(base_url."modul/email/edit","Edit Account");
	$data[] = array(base_url."modul/email/delete","Delete Account");	
	$table->showCheckBox(false,$cols);	
	$table->showPager(true,$F_HANDLER->BOTTOM,50,20);
	$table->showRDPanel(true,$F_HANDLER->BOTH,$cols,$data);

	$table->field[0]->name = "ID";
	$table->field[0]->hidden= true;
	$table->field[0]->headername = "User ID";
	$table->field[0]->align = $F_HANDLER->LEFT;

	$table->field[1]->name = "NAMAORG";
	$table->field[1]->headername = "Nama";
	$table->field[1]->align = $F_HANDLER->LEFT;
	
	
	$table->field[2]->name = "EMAIL";
	$table->field[2]->headername = "Email";
	$table->field[2]->align = $F_HANDLER->LEFT;
	
	$table->field[3]->name = "ROLE";
	$table->field[3]->headername = "Role";
	$table->field[3]->align = $F_HANDLER->LEFT;
	
	
	$table->field[4]->name = "NAMA";
	$table->field[4]->headername = "Cabang";
	$table->field[4]->align = $F_HANDLER->LEFT;
	
	$table->field[5]->name = "AKTIF";
	$table->field[5]->headername = "Status";
	$table->field[5]->align = $F_HANDLER->LEFT;
	
	$table->drawTable();	
	$conn->disconnect();
}
?>