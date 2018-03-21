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
if($div=="delete" && trim($data)!=""){
	$d = split(";",$data);
	$id = trim($d[0]);
	$npwp = trim($d[1]);
	
	
	$aktivitas = "Berhasil menghapus Corp ID : ".$id;
	audit($conn,$aktivitas);				
	
	$sql = "Delete from TCOMPANY Where ID = '".$id."' and NPWP='".$npwp."'";	
	$data = $conn->execute($sql);
	if($data){
		$sql = "Delete from TACCOUNT Where NPWP='".$npwp."'";	
		$data = $conn->execute($sql);
		$sql = "Delete from TBLUSER Where NPWP='".$npwp."' and ID = '".$id."'";	
		$data = $conn->execute($sql);
		$sql = "Delete from TBLGROUP Where NPWP='".$npwp."'";	
		$data = $conn->execute($sql);
		$sql = "Delete from TBLDEPT Where CORPID='".$id."'";	
		$data = $conn->execute($sql);		
		$_SESSION['respon'] = "Penghapusan Corp ID berhasil";
		$_SESSION['statusRespon']=1;
	}
	echo "<script> window.location.href='".base_url."modul/groupid/view';</script>";exit;
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
				echo "List Corp ID <br />";
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
				<form name="frmCari" method="post" action="<?php echo base_url?>modul/groupid/view" onSubmit="return cekForm();">				
				<table>
					<tr>
						<td>Category</td>
						<td><select name="field" id="SearchBase">
							<?php 
							$x1 = array("ID","NPWP","NAMA","ADDRESS","CITY","ZIPCODE","PIC","PIC_EMAIL","PIC_PHONE","FAX_NUMBER","GROUP_ACCOUNT");
							$x2 = array("- Corp ID"," - NPWP", " - Nama", " - Alamat", " - Kota", " - Kode POS", " - PIC", " - Email PIC", " - Phone PIC", " - Number Fax", " - Grouping");
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
								echo '<button type="button" onclick=location.href="'.base_url.'modul/groupid/view" style="width:75px" class="btn_2">Cancel</button></td>';
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
	$orderby = setSortir(array('sort'=>'npwp','order'=>'desc'),$x1);
	$conn->connect();
	$sql = "select ID, NPWP, NAMA, ADDRESS, CITY, ZIPCODE, PIC, PIC_EMAIL, PIC_PHONE, FAX_NUMBER, GROUP_ACCOUNT from TCOMPANY where PIC is not NULL and PIC_EMAIL is not NULL ";
	
	
	if($_POST["q"]){		
		$sql = $sql ." and LOWER(". $_POST["field"] .") Like '%". strtolower($_POST["q"]) ."%'";
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
	$cols[1] = 1;	
	//untuk ambil tipe dokumen BC 2.0 atau BC 2.3
	$data = array();
	//print_r($table);
	$data[] = array("#","Pilih Proses");	
	$data[] = array(base_url."modul/groupid/delete"," - Delete Corp ID");	
	$table->showCheckBox(false,$cols);	
	$table->showPager(true,$F_HANDLER->BOTTOM,50,20);
	$table->showRDPanel(true,$F_HANDLER->BOTH,$cols,$data);

	$table->field[0]->name = "ID";
	$table->field[0]->headername = "Corp ID";
	$table->field[0]->align = $F_HANDLER->LEFT;
	
	$table->field[1]->name = "NPWP";
	$table->field[1]->headername = "NPWP";
	$table->field[1]->align = $F_HANDLER->LEFT;

	$table->field[2]->name = "NAMA";
	$table->field[2]->headername = "Nama";
	$table->field[2]->align = $F_HANDLER->LEFT;
	
	$table->field[3]->name = "ADDRESS";
	$table->field[3]->headername = "Alamat";
	$table->field[3]->align = $F_HANDLER->LEFT;
	
	$table->field[4]->name = "CITY";
	$table->field[4]->headername = "Kota";
	$table->field[4]->align = $F_HANDLER->LEFT;
	
	$table->field[5]->name = "ZIPCODE";
	$table->field[5]->headername = "Kode POS";
	$table->field[5]->align = $F_HANDLER->LEFT;
	
	$table->field[6]->name = "PIC";
	$table->field[6]->headername = "PIC";
	$table->field[6]->align = $F_HANDLER->LEFT;
	
	$table->field[7]->name = "PIC_EMAIL";
	$table->field[7]->headername = "Email PIC";
	$table->field[7]->align = $F_HANDLER->LEFT;
	
	$table->field[8]->name = "PIC_PHONE";
	$table->field[8]->headername = "Phone PIC";
	$table->field[8]->align = $F_HANDLER->LEFT;
	
	$table->field[9]->name = "FAX_NUMBER";
	$table->field[9]->headername = "Number Fax";
	$table->field[9]->align = $F_HANDLER->LEFT;
	
	$table->field[10]->name = "GROUP_ACCOUNT";
	$table->field[10]->headername = "Grouping";
	$table->field[10]->align = $F_HANDLER->LEFT;
	
	$table->drawTable();	
	$conn->disconnect();

?>