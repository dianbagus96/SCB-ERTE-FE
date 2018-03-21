<?php
if(in_array($_SESSION["priv_session"],array("0","1","2","3"))==false){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}else{

require_once('dbconn.php');
require_once("conf.php");
global $conn;
$conn->connect();

$cari = str_replace('\\','',$_POST["q"]);
$_POST["q"] = strFilter($_POST["q"]);
$data = $_POST["radiopanel"];
if($div=="delete"){
	if(trim($data)==""){ echo "<script> window.location.href='".base_url."modul/user/view';</script>";exit;}	
	$cbx = split(";",$data);
	$uid = $cbx[0];
	$npwp = str_replace('-','',str_replace('.','',$cbx[1]));
	$id = trim($cbx[2]);
	$uid = str_replace("\'","''",$uid); #-> kutip dua
	$sql = "Delete TBLUSER Where USERLOGIN = '$uid' And NPWP = '$npwp'";
	$data = $conn->execute($sql);
	
	
	$aktivitas = "Berhasil menghapus user dengan UserID : ".$uid." pada Corp ID : ".$id;
	audit($conn,$aktivitas);
	$_SESSION['statusRespon']=1;
	$_SESSION['respon'] = "Penghapusan User berhasil";	
	echo "<script> window.location.href='".base_url."modul/user/view';</script>";exit;
}elseif($div=="lock"){
	if(trim($data)==""){ echo "<script> window.location.href='".base_url."modul/user/view';</script>";exit;}	
	$cbx = split(";",$data);
	$uid = $cbx[0];
	$npwp = str_replace('-','',str_replace('.','',$cbx[1]));
	$id = trim($cbx[2]);	
	$uid = str_replace("\'","''",$uid); #-> kutip dua
	$sql = "Update TBLUSER set EMAILSTATUS ='1' Where USERLOGIN = '$uid' And NPWP = '$npwp'";
	$data = $conn->execute($sql);
	
	
	$aktivitas = "Berhasil me-lock user dengan UserID : ".$uid." pada Corp ID : ".$id;
	audit($conn,$aktivitas);
	$_SESSION['statusRespon']=1;
	$_SESSION['respon'] = "Lock pada User berhasil";	
	echo "<script> window.location.href='".base_url."modul/user/view';</script>";exit;
}elseif($div=="unlock"){
	if(trim($data)==""){ echo "<script> window.location.href='".base_url."modul/user/view';</script>";exit;}	
	$cbx = split(";",$data);
	$uid = $cbx[0];
	$npwp = str_replace('-','',str_replace('.','',$cbx[1]));
	$id = trim($cbx[2]);
	$uid = str_replace("\'","''",$uid); #-> kutip dua
	$sql = "Update TBLUSER set EMAILSTATUS ='0' Where USERLOGIN = '$uid' And NPWP = '$npwp'";
	$data = $conn->execute($sql);
	
	
	$aktivitas = "Berhasil me-unlock user dengan UserID : ".$uid." pada Corp ID : ".$id;
	audit($conn,$aktivitas);
	$_SESSION['statusRespon']=1;
	$_SESSION['respon'] = "Unlock pada User berhasil";	
	echo "<script> window.location.href='".base_url."modul/user/view';</script>";exit;
}elseif($div=="unlog"){
	if(trim($data)==""){ echo "<script> window.location.href='".base_url."modul/user/view';</script>";exit;}	
	$cbx = split(";",$data);		
	$uid = $cbx[0];	
	$uid = strFilter($uid);
	$id = str_replace('-','',str_replace('.','',$cbx[1]));
	$sqlSelect = "select LOGIN from tbluser where userlogin='$uid' and NPWP='$id' and LOGIN='Y'";
	//echo $sqlSelect;die();
	$data = $conn->query($sqlSelect);
	if($data->next()){
		$sqlUpdate = "update tbluser set LOGIN='N' where userlogin='$uid' and NPWP='$id'";
		//echo $sqlUpdate;die();
		$conn->execute($sqlUpdate);	
		$aktivitas = "melakukan logout user dengan UserID : ".$uid." pada NPWP: ".$id;
		audit($conn,$aktivitas);
		$_SESSION['statusRespon']=1;
		$_SESSION['respon'] = "Logout pada User berhasil";	
	}else{
		$_SESSION['statusRespon']=0;
		$_SESSION['respon'] = "Proses tidak bisa dilakukan";
	}
	echo "<script> window.location.href='".base_url."modul/user/view';</script>";exit;
}
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
				echo "Browse User <br />";
			}?>
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
				<form name="frmCari" method="POST" action="<?php echo base_url?>modul/user/view">			
				<table>
					<tr>
						<td>Category</td>
						<td><select name="field" id="field">
							<?php 
							if($_SESSION['priv_session']=='0'||$_SESSION['priv_session']=='1'||$_SESSION['priv_session']=='2'){
								$x1 = array("usr.USERLOGIN","usr.EMAIL","usr.FULLNAME","usr.USERPRIV","usr.NPWP","com.NAMA","usr.ID","usr.EMAILSTATUS","usr.LOGIN");
								$x2 = array("- User ID"," - Email", "- Fullname","- Priv","- NPWP","- Taxpayer","- Corp ID","- Status Lock","- Status Login");
							}elseif($_SESSION['priv_session']=='3'){
								$x1 = array("usr.USERLOGIN","usr.EMAIL","usr.FULLNAME","usr.USERPRIV","usr.NPWP","com.NAMA","usr.EMAILSTATUS","usr.LOGIN");
								$x2 = array("- User ID"," - Email", "- Fullname","- Priv","- NPWP","- Taxpayer","- Status Lock","- Status Login");
							}
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
						<td><input name="q" type="text" id="q" size="50" value="<?php echo($cari);?>" > <button type="button" class="btn_2" onClick="cekForm(0);" style="width:73px">Search</button> 
							<?php if($cari){
								echo '<button type="button" onclick=location.href="'.base_url.'modul/user/view" style="width:75px" class="btn_2">Cancel</button></td>';
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
	$valid = array("userlogin","email","fullname","npwp","nama","id","status","login");
	$orderby = setSortir(array('sort'=>'npwp','order'=>'desc'),$valid);

	$tg1 = $_POST["tg1"];
	$tg2 = $_POST["tg2"];
	
	if($_SESSION["priv_session"] == 0 ){
		$sql = "select usr.USERLOGIN, usr.EMAIL, usr.FULLNAME, case
					when usr.USERPRIV = 1 then '1 - Admin SCB' 
					when usr.USERPRIV = 2 then '2 - Admin SCB Aplication' 
					when usr.USERPRIV = 3 then '3 - User Administrator' 
					when usr.USERPRIV = 4 then '4 - Operator' 
					when usr.USERPRIV = 5 then '5 - Supervisor' end as USERPRIV
			,CASE LENGTH(usr.NPWP) WHEN 15  THEN (SUBSTR(usr.NPWP,1,2)||'.'||SUBSTR(usr.NPWP,3,3)||'.'||SUBSTR(usr.NPWP,6,3)||'.'||SUBSTR(usr.NPWP,9,1)||'-'||SUBSTR(usr.NPWP,10,3)||'.'||SUBSTR(usr.NPWP,13,3)) 
			ELSE usr.NPWP END AS NPWP, com.NAMA, usr.ID, usr.EMAILSTATUS as STATUS, usr.LOGIN, case usr.NOTIF when 1 then 'YES' else 'NO' end as NOTIFS
			from TBLUSER usr INNER JOIN TCOMPANY com 
			on usr.NPWP = com.IDPAYEE and com.ID = usr.ID
			where usr.USERPRIV NOT IN ('0') ";			
	}elseif( $_SESSION["priv_session"] == 1 || $_SESSION["priv_session"] == 2){
		$sql = "select usr.USERLOGIN, usr.EMAIL, usr.FULLNAME, case
					when usr.USERPRIV = 1 then '1 - Admin SCB' 
					when usr.USERPRIV = 2 then '2 - Admin SCB Aplication' 
					when usr.USERPRIV = 3 then '3 - User Administrator' 
					when usr.USERPRIV = 4 then '4 - Operator' 
					when usr.USERPRIV = 5 then '5 - Supervisor' end as USERPRIV
			,CASE LENGTH(usr.NPWP) WHEN 15  THEN (SUBSTR(usr.NPWP,1,2)||'.'||SUBSTR(usr.NPWP,3,3)||'.'||SUBSTR(usr.NPWP,6,3)||'.'||SUBSTR(usr.NPWP,9,1)||'-'||SUBSTR(usr.NPWP,10,3)||'.'||SUBSTR(usr.NPWP,13,3)) 
			ELSE usr.NPWP END AS NPWP, com.NAMA, usr.ID, usr.EMAILSTATUS as STATUS, usr.LOGIN, case usr.NOTIF when 1 then 'YES' else 'NO' end as NOTIFS
			from TBLUSER usr INNER JOIN TCOMPANY com 
			on usr.NPWP = com.IDPAYEE and com.ID = usr.ID
			where usr.USERPRIV NOT IN ('0','1','2') ";		
	
	}else{
		$GID = $_SESSION['ID'];
		$sql = "select usr.USERLOGIN, usr.EMAIL, usr.FULLNAME, case
					when usr.USERPRIV = 1 then '1 - Admin SCB' 
					when usr.USERPRIV = 2 then '2 - Admin SCB Aplication' 
					when usr.USERPRIV = 3 then '3 - User Administrator' 
					when usr.USERPRIV = 4 then '4 - Operator' 
					when usr.USERPRIV = 5 then '5 - Supervisor' end as USERPRIV
		, CASE LENGTH(usr.NPWP) WHEN 15  THEN (SUBSTR(usr.NPWP,1,2)||'.'||SUBSTR(usr.NPWP,3,3)||'.'||SUBSTR(usr.NPWP,6,3)||'.'||SUBSTR(usr.NPWP,9,1)||'-'||SUBSTR(usr.NPWP,10,3)||'.'||SUBSTR(usr.NPWP,13,3)) 
			ELSE usr.NPWP END AS NPWP, com.NAMA, usr.ID, 
		usr.EMAILSTATUS as STATUS , usr.LOGIN, case usr.NOTIF when 1 then 'YES' else 'NO' end as NOTIFS
		from TBLUSER usr INNER JOIN TCOMPANY com on usr.NPWP = com.IDPAYEE and com.ID = usr.ID
		LEFT JOIN TBLGROUP tg on usr.NPWP=tg.NPWP and (tg.USERS like '%,'||usr.USERLOGIN||',%' or tg.PIC = usr.USERLOGIN)  
		left join TBLDEPT td on td.CORPID='".$_SESSION['ID']."' and (td.USERS like '%,'||usr.USERLOGIN||',%'  or td.PIC = usr.USERLOGIN)
		where usr.NPWP = com.NPWP AND usr.ID='$GID' AND usr.USERPRIV IN ('4','5','',NULL) ";
	}
	
	if($_POST["q"]!=''){
		if($_POST['field']=="usr.EMAILSTATUS"){
			$sql = $sql ." And LOWER(". $_POST["field"] .") = '". strtolower($_POST["q"]) ."'";
		}else{
			$sql = $sql ." And LOWER(". $_POST["field"] .") Like '%". strtolower($_POST["q"]) ."%'";
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
	$cols[1] = 4;
	$cols[2] = 6;
	$data = array();
	$data[] = array("#","Pilih Proses");
	$data[] = array(base_url."modul/user/edit"," - Edit Account");
	$data[] = array(base_url."modul/user/delete"," - Delete Account");
	$data[] = array(base_url."modul/user/lock"," - Lock Account");
	$data[] = array(base_url."modul/user/unlock"," - Unlock Account");
	$data[] = array(base_url."modul/user/unlog"," - Logout User");
	$data[] = array(base_url."reset_password.php"," - Reset Password");
	$data[] = array(base_url."udd.php"," - User Matrix");
	
	$table->showCheckBox(false,$cols);	
	$table->showPager(true,$F_HANDLER->BOTTOM,50,20);
	$table->showRDPanel(true,$F_HANDLER->BOTH,$cols,$data);
	
	$table->field[0]->name = "USERLOGIN";
	$table->field[0]->headername = "User ID";
	$table->field[0]->align = $F_HANDLER->LEFT;
	
	$table->field[1]->name = "EMAIL";
	$table->field[1]->headername = "Email";
	$table->field[1]->align = $F_HANDLER->LEFT;	
	
	$table->field[2]->name = "FULLNAME";
	$table->field[2]->headername = "Fullname";
	$table->field[2]->align = $F_HANDLER->LEFT;
	
	$table->field[3]->name = "USERPRIV";
	$table->field[3]->headername = "Priv";
	$table->field[3]->align = $F_HANDLER->LEFT;
	
	
	$table->field[4]->name = "NPWP";
	$table->field[4]->headername = "<em>NPWP</em>";
	$table->field[4]->align = $F_HANDLER->LEFT;
	
	$table->field[5]->name = "NAMA";
	$table->field[5]->headername = "Taxpayer";
	$table->field[5]->align = $F_HANDLER->LEFT;
	
	$table->field[6]->name = "ID";
	if($_SESSION["priv_session"] == 0){
		$table->field[6]->headername = "Corp ID";
	}else{
		$table->field[6]->headername = "Group ID";
	}
	$table->field[6]->align = $F_HANDLER->LEFT;	
	
	$table->field[7]->name = "STATUS";
	$table->field[7]->headername = "Status Lock";
	$table->field[7]->align = $F_HANDLER->LEFT;
	
	$table->field[8]->name = "LOGIN";
	$table->field[8]->headername = "Status Login";
	$table->field[8]->align = $F_HANDLER->LEFT;
	
	$table->field[9]->name = "NOTIFS";
	$table->field[9]->headername = "Notif";
	$table->field[9]->align = $F_HANDLER->CENTER;
	$table->drawTable();
	
	if(in_array($_SESSION["priv_session"],array("0","3","2","1"))){
		$for = "user";
		include ("form_csv.php");
	}
}
?>
