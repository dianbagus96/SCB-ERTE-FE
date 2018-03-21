<?php
if(in_array($_SESSION["priv_session"],array("3"))==false){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}else{

require_once("dbconn.php");
require_once("conf.php");
global $conn;
$conn->connect();
$cari = str_replace('\\','',$_POST["q"]);
$_POST["q"] = strFilter($_POST["q"]);
$data = $_POST["radiopanel"];
if($div=="delete" && trim($data)!=""){
	$d = split(";",$data);
	$s = "select NPWP,ACCOUNT FROM TACCOUNT WHERE ID = '".$d[0]."'";
	$dd = $conn->query($s); $dd->next();
	
	$aktivitas = "Berhasil menghapus Rekening dengan NPWP : ".$dd->get('NPWP');
	audit($conn,$aktivitas);				
	
	$sql = "Delete TACCOUNT Where ID = '".$d[0]."'";	
	$data = $conn->execute($sql);
	$sql = "select NPWP from TACCOUNT where NPWP='".$d[3]."' AND STSACC='SSP'";
	$hasil = $conn->query($sql);		
	if(!$hasil->next()){
		$sql = "delete from TPENYETOR where NPWP='".$d[3]."' AND IDINI='".trim($_SESSION['ID'])."'";				
		$hasil = $conn->execute($sql);		
	}	
	if($data){
		$_SESSION['respon'] = "Penghapusan Rekening berhasil";
		$_SESSION['statusRespon']=1;
	}
	echo "<script> window.location.href='".base_url."modul/rekeningtax/view';</script>";exit;
}elseif($div=="freeze" && trim($data)!=""){
	$d = split(";",$data);
	$s = "select NPWP,ACCOUNT FROM TACCOUNT WHERE ID = '".$d['0']."'";
	$dd = $conn->query($s); $dd->next();
	
	$sqlrelasi = "select ta.ID from TACCOUNT ta inner join TPENYETOR p on ta.NPWP=p.NPWP and p.IDINI='".trim($_SESSION['ID'])."' 
			where ta.ACCOUNT='".trim($dd->get('ACCOUNT'))."'";
	$drelasi = $conn->query($sqlrelasi); 
	
	
	$aktivitas = "Berhasil Freeze Rekening dengan NPWP : ".$dd->get('NPWP');
	audit($conn,$aktivitas);				
	
	if($drelasi->next()){
		$sql = "UPDATE TACCOUNT set freeze='Y' Where ID = '".$d[0]."' or ID = '".trim($drelasi->get('ID'))."'";	
	}else{
		$sql = "UPDATE TACCOUNT set freeze='Y' Where ID = '".$d[0]."'";	
	}
	$data = $conn->execute($sql);
	if($data){
		$_SESSION['respon'] = "Freeze Rekening berhasil";
		$_SESSION['statusRespon']=1;
	}
	echo "<script> window.location.href='".base_url."modul/rekeningtax/view';</script>";exit;
}elseif($div=="unfreeze" && trim($data)!=""){
	$d = split(";",$data);
	$s = "select NPWP,ACCOUNT FROM TACCOUNT WHERE ID = '".$d['0']."'";
	$dd = $conn->query($s); $dd->next();
	
	$sqlrelasi = "select ta.ID from TACCOUNT ta inner join TPENYETOR p on ta.NPWP=p.NPWP and p.IDINI='".trim($_SESSION['ID'])."' 
			where ta.ACCOUNT='".trim($dd->get('ACCOUNT'))."'";
	$drelasi = $conn->query($sqlrelasi); 
	
	
	$aktivitas = "Membatalkan Freeze Rekening dengan NPWP : ".$dd->get('NPWP');
	audit($conn,$aktivitas);						
	
	if($drelasi->next()){
		$sql = "UPDATE TACCOUNT set freeze='N' Where ID = '".$d[0]."' or ID = '".trim($drelasi->get('ID'))."'";	
	}else{
		$sql = "UPDATE TACCOUNT set freeze='N' Where ID = '".$d[0]."'";	
	}
	$data = $conn->execute($sql);
	if($data){
		$_SESSION['respon'] = "Unfreeze Rekening berhasil";
		$_SESSION['statusRespon']=1;
	}
	echo "<script> window.location.href='".base_url."modul/rekeningtax/view';</script>";exit;
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
				echo "Account Setting<br />";
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
				<form name="frmCari" method="post" action="<?php echo base_url?>modul/rekeningtax/view" onSubmit="return cekForm();">				
				<table>
					<tr>
						<td>Category</td>
						<td><select name="field" id="SearchBase">
							<?php 
							$x1 = array("ACCOUNT","B.USERS","A.NPWP","GROUPID","FREEZE");
							$x2 = array(" - Account No"," - Account Name"," - NPWP"," - Group"," - Freeze");
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
							<?php 
							if($cari){
								echo '<button type="button" onclick=location.href="'.base_url.'modul/rekeningtax/view" style="width:75px" class="btn_2">Cancel</button>';
							}
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
<?php		
	$orderby = setSortir(array('sort'=>'id','order'=>'desc'),$x1);
	
	$conn->connect();
	$sql = "select B.ID,ACCOUNT,B.USERS,A.NPWP,
			case when DEPTID='' OR DEPTID IS NULL THEN GROUPID
			ELSE GROUPID+'-'+DEPTID END AS GROUPS,FREEZE,GROUPID,DEPTID
			From TPENYETOR A INNER JOIN TACCOUNT B on A.NPWP=B.NPWP 
			Where IDINI like '%". trim($_SESSION["ID"]) ."%' and STSACC='SSP'";
	
	if($_POST["q"]){		
		if($_POST["field"]=='B.USERS'){
			$sql = $sql ." AND (". $_POST["field"] .") Like '%". $_POST["q"] ."%'";
		}else{
			$sql = $sql ." AND LOWER(". $_POST["field"] .") Like '%". strtolower($_POST["q"]) ."%'";
		}
	}
	
	require_once("library/tbLite/develop/htmltable.classcolorssp.php");
	//sql gabungan	
	$sql = $sql . $orderby;
	
	$table = new HTMLTableColorSSP();
	$table->warna = "rekeningtax";
	$table->connection = $conn;
	$table->width = "100%";
	$table->navRowSize = 10;
	$table->SQL = $sql;
	// elemen data yang akan di passing
	$cols = array();
	$cols[] = 0;	
	$cols[] = 1;	
	$cols[] = 2;	
	$cols[] = 3;	
	$cols[] = 5;	
	$cols[] = 6;	
	$cols[] = 7;	
	//untuk ambil tipe dokumen BC 2.0 atau BC 2.3
	$data = array();
	//print_r($table);
	$data[] = array("#","Pilih Proses");
	$data[] = array(base_url."modul/rekeningtax/edit"," - Edit Rekening");
	$data[] = array(base_url."modul/rekeningtax/delete"," - Hapus Rekening");
	$data[] = array(base_url."modul/rekeningtax/freeze"," - Freeze");	
	$data[] = array(base_url."modul/rekeningtax/unfreeze"," - Unfreeze");	
	$table->showCheckBox(false,$cols);	
	$table->showPager(true,$F_HANDLER->BOTTOM,50,20);
	$table->showRDPanel(true,$F_HANDLER->BOTH,$cols,$data);
	
	$table->field[0]->name = "ID";
	$table->field[0]->hidden= true;	

	$table->field[1]->name = "ACCOUNT";
	$table->field[1]->headername = "Account Number";
	$table->field[1]->align = $F_HANDLER->LEFT;
	
	$table->field[2]->name = "USERS";
	$table->field[2]->headername = "Account Name";
	$table->field[2]->align = $F_HANDLER->LEFT;
	
	$table->field[3]->name = "NPWP";
	$table->field[3]->headername = "NPWP";
	$table->field[3]->hidden = true;
	
	$table->field[4]->name = "GROUPID";
	$table->field[4]->headername = "Group";
	$table->field[4]->align = $F_HANDLER->LEFT;
	
	$table->field[5]->name = "FREEZE";
	$table->field[5]->headername = "Freeze";
	$table->field[5]->align = $F_HANDLER->LEFT;
	
	$table->field[6]->name = "GROUPID";
	$table->field[6]->hidden = true;
	$table->field[7]->name = "DEPTID";
	$table->field[7]->hidden = true;
		
	$table->drawTable();	
	$conn->disconnect();
}
?>