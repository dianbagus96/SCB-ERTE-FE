<?php
if($_SESSION['verified']==""){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}else{

require_once("dbconn.php");
require_once("conf.php");
require_once("sendEmail.php");
global $conn;

//print_r($_SESSION);
function timeStamp(){ //buat id - 
	$xx = date("YmdHis");
	return $xx;
}
$readonly = "readonly style='background:#E0E0E0;border:1px #999 solid;padding:2px'";
if($_POST["radiopanel"]){
  $radio = explode(';',$_POST["radiopanel"]);
  $id = $radio[1];
  $npwp = strFilter($_POST["npwp"]);
}
$conn->connect();
$div = $_GET['div'];
if($div=="edittaxpayer"){
	if(trim($_POST["radiopanel"])==""){ echo "<script> window.location.href='".base_url."modul/manage/taxpayer';</script>";exit;}
	$npwp = $radio[0];
	$sql = "Select *
			From TCOMPANY
			Where NPWP = '$npwp' And IDPAYEE = '". $_SESSION["npwp_session"] ."'";
	$data = $conn->query($sql);
	if($data->next()){
		$nama = $data->get("NAMA");
		$npwp = $data->get("NPWP");
		$address = $data->get("ADDRESS");
		$city = $data->get("CITY");
		$zipcode = $data->get("ZIPCODE");
		$id = $data->get("ID");		
	}
}elseif($div=="editdepositor"){	
	if(trim($_POST["radiopanel"])==""){ echo "<script> window.location.href='".base_url."modul/manage/depositor';</script>";exit;}
	$npwp = $radio[0];
	$sql = "Select a.NPWP, a.NAMA, b.ACCOUNT, b.TIPEACC
			From TPENYETOR a LEFT JOIN TACCOUNT b ON a.NPWP=b.NPWP
			Where a.NPWP = '". $npwp ."' And a.IDINI = '". trim($_SESSION["ID"]) ."'";	
	$data = $conn->query($sql);
	if($data->next()){
		$nama = $data->get("NAMA");
		$npwp = $data->get("NPWP");
		$account = $data->get("ACCOUNT");
		$tipeacc = $data->get('TIPEACC');
	}
}elseif($div=="updatetaxpayer"){
	if(trim($npwp)==""){ echo "<script> window.location.href='".base_url."modul/manage/taxpayer';</script>";exit;}
	$id = strFilter($_POST["id"]);	
	$nama = strtoupper(strFilter($_POST["nama"]));
	$address = strtoupper(strFilter($_POST["address"]));
	$city = strtoupper(strFilter($_POST["city"]));
	$zipcode = strFilter($_POST["zipcode"]);
	$sql = "Update TCOMPANY Set NAMA = '$nama',
								ADDRESS = '$address',
								CITY = '$city',
								ZIPCODE = '$zipcode'
	Where NPWP = '". $npwp ."' And IDPAYEE = '".trim( $_SESSION["npwp_session"]) ."' And ID ='$id'";	
	$data = $conn->execute($sql);
	if($data){
		$message = "Update success.";		
		
		$aktivitas = "Berhasil mengedit Taxpayer dengan NPWP : $npwp";
		audit($conn,$aktivitas);		
		$_SESSION['statusRespon']=1;	
		$_SESSION['respon']='Perubahan pada Taxpayer berhasil';		
	}
	echo "<script> window.location.href='".base_url."modul/manage/taxpayer';</script>";exit;
}elseif($div=="updatedepositor"){
	if(trim($npwp)==""){ echo "<script> window.location.href='".base_url."modul/manage/depositor';</script>";exit;}
	$id = strFilter($_POST["id"]);
	$idacc = strFilter($_POST["idacc"]);
	$npwp = strFilter($_POST["npwp"]);
	$nama = strtoupper(strFilter($_POST["nama"]));
	$account = strFilter($_POST["account"]);
	$tipeacc = $_POST['tipeacc'];
	
	$sql = "Update TPENYETOR Set NAMA = '$nama' Where NPWP = '". $npwp ."' And IDINI = '". $_SESSION["ID"] ."'";		
	$data = $conn->execute($sql);
	
	$sql2 = "Update TACCOUNT set ACCOUNT ='$account', TIPEACC='$tipeacc' Where NPWP = '". $npwp ."' AND ID ='$idacc' AND STSACC='SSP'";
	$data = $conn->execute($sql2);
	
	if($data){
		
		$aktivitas = "Berhasil mengedit Depositor dengan NPWP : $npwp";
		audit($conn,$aktivitas);	
		$_SESSION['statusRespon']=1;
		$_SESSION['respon']='Perubahan pada Depositor berhasil';	
	}
	echo "<script> window.location.href='".base_url."modul/manage/depositor';</script>";exit;
}elseif($div=="deletetaxpayer"){	
	if(trim($_POST['radiopanel'])==""){ echo "<script> window.location.href='".base_url."modul/manage/taxpayer';</script>";exit;}
	$npwp = $radio[0];
	$sql = "Delete TCOMPANY
			Where NPWP = '". $npwp ."' And IDPAYEE = '". trim($_SESSION["npwp_session"]) ."'";
	$data=$conn->execute($sql);
	if($data){
		$data = $conn->execute($sql);
		
		$aktivitas = "Berhasil menghapus Taxpayer dengan NPWP : $npwp";
		audit($conn,$aktivitas);	
		
		$npwp="";
		$_SESSION['statusRespon']=1;
		$_SESSION['respon']='Penghapusan Taxpayer berhasil';		
	}
	echo "<script> window.location.href='".base_url."modul/manage/taxpayer';</script>";exit;
}elseif($div=="deletedepositor"){	
	$npwp = $radio[0];	
	if(trim($npwp)==""){ echo "<script> window.location.href='".base_url."modul/manage/depositor';</script>";exit;}
	
	$sql = "Delete TPENYETOR Where NPWP = '". $npwp ."' And IDINI = '". trim($_SESSION["ID"]) ."'";
	
	$data=$conn->execute($sql);	
	$sql2 = "Delete TACCOUNT Where NPWP = '". $npwp ."' And ID ='$id'";
	$data=$conn->execute($sql2);
	
	$data = $conn->execute($sql);
	
	$aktivitas = "Berhasil menghapus Depositor dengan NPWP : $npwp";
	audit($conn,$aktivitas);		
	$npwp = "";
	$_SESSION['respon']='Penghapusan Depositor berhasil';
	$_SESSION['statusRespon']=1;
	echo "<script> window.location.href='".base_url."modul/manage/depositor';</script>";exit;
}elseif($div=="inserttaxpayer"){
	if(trim($npwp=$_POST['npwp'])==""){ echo "<script> window.location.href='".base_url."modul/manage/taxpayer';</script>";exit;}
	$nama = strtoupper(strFilter($_POST["nama"]));
	$address = strtoupper(strFilter($_POST["address"]));
	$city = strtoupper(strFilter($_POST["city"]));
	$zipcode = strFilter($_POST["zipcode"]);
	$sql = "select count(NPWP) as TOTAL from TCOMPANY where NPWP='$npwp'";
	$data  = $conn->query($sql);$data->next();
	$total =$data->get('TOTAL'); 
	if($total>0){ 
		$_SESSION['respon']= $bhs['NPWP sudah Terdaftar'][$kdbhs];
		$_SESSION['statusRespon']=0;
		echo "<script> window.location.href='".base_url."modul/manage/taxpayer';</script>";	
		exit();
	}
	$sql = "Insert Into TCOMPANY (ID, NPWP, NAMA, ADDRESS, CITY, ZIPCODE, IDPAYEE)
			Values ('". timeStamp() ."', '$npwp', '$nama', '$address', '$city', '$zipcode', '". trim($_SESSION["npwp_session"]) ."')";
	$data = $conn->execute($sql);
	
	$aktivitas = "Berhasil membuat Taxpayer baru dengan NPWP : $npwp";
	audit($conn,$aktivitas);	
	$_SESSION['respon']='Anda telah Berhasil membuat Taxpayer baru';
	$_SESSION['statusRespon']=1;	
	echo "<script> window.location.href='".base_url."modul/manage/taxpayer';</script>";exit;
}elseif($div=="insertdepositor"){	
	if(trim($npwp)==""){ echo "<script> window.location.href='".base_url."modul/manage/depositor';</script>";exit;}
	$nama = strtoupper(strFilter($_POST["nama"]));
	$account = strFilter($_POST["account"]);
	$tipeacc = $_POST['tipeacc'];
	$sql = "select count(NPWP) as TOTAL from TPENYETOR where NPWP='$npwp'";
	$d = $conn->query($sql); $d->next();	
	if($d->get('TOTAL')>0){ 
		$_SESSION['respon']= $bhs['NPWP sudah Terdaftar'][$kdbhs];
		$_SESSION['statusRespon']=0;
		echo "<script> window.location.href='".base_url."modul/manage/depositor';</script>";exit;
	}
	$sql = "Insert Into TPENYETOR (ID, NPWP, NAMA, IDINI)
			Values ('". timeStamp() ."', '$npwp', '$nama',  '". trim($_SESSION["ID"]) ."')";
	$data = $conn->execute($sql);	
	$sql2 = "Insert Into TACCOUNT (ID, NPWP, ACCOUNT,STSACC,TIPEACC,USERS,REGDATE)
			values(". timeStamp() .", '$npwp', '$account','SSP','$tipeacc','',CONVERT(DATETIME,SYSDATE,105))";
	$data = $conn->execute($sql2);
	$message = "Save success.";	
	
	$aktivitas = "Berhasil membuat Depositor baru dengan NPWP : $npwp";
	audit($conn,$aktivitas);
	$_SESSION['respon']='Anda telah Berhasil membuat Depositor baru';	
	$_SESSION['statusRespon']=1;
	echo "<script> window.location.href='".base_url."modul/manage/depositor';</script>";exit;
}elseif($div=="updateprofile"){
	if(trim($fullname=$_POST["fullname"])==""){ echo "<script> window.location.href='".base_url."modul/manage/profile';</script>";exit;}
	$fullname = strtoupper(strFilter($fullname));
	$email = strFilter($_POST["email"]);
	
	$sql = "Update TBLUSER Set FULLNAME = '$fullname', EMAIL = '$email'
			Where USERLOGIN = '".trim($_SESSION["uid_session"]) ."' And NPWP = '". trim($_SESSION["npwp_session"]) ."'";
	
	$data = $conn->execute($sql);
	$_SESSION['email_session'] = trim($email);
	$_SESSION['nmuser_session'] = trim($fullname);
	
	$aktivitas = "Berhasil melakukan perubahan Profil";
	audit($conn,$aktivitas);
	echo "<script type='text/javascript' src='".base_url."js/jquery.tools.min.js'></script>\n"; 
		echo "<link type='text/css' href='".base_url."js/jAlert/jquery.alerts.css' rel='stylesheet' />\n";
        echo "<script type='text/javascript' src='".base_url."js/jAlert/jquery.alerts.js'></script>\n"; 
	echo "<script type=\"text/javascript\">\n";
	echo " $(document).ready(function(){\n";
	echo "     jAlert('Perubahan pada Profile berhasil!','',function(r){
					if(r==true) window.location.href='".base_url."modul/manage/profile';	
				});\n";	
	echo "});\n";
	echo "</script>\n";
	exit;

}
function cekPassword($pwd,$minPass,$minNum){
	// password minimal 8 maksimal 12
	$jmlNum=0;
	if(strlen($pwd) < $minPass){
		$valid = 0;
	} else {
		for($i=0;$i<strlen($pwd);$i++){
			if(is_numeric($pwd[$i])){
			$jmlNum++;
			}
		}
		if($jmlNum<$minNum){
			$valid = 0;
		}elseif (!preg_match("#[a-z]+#", $pwd)) {
			$valid = 0;
		}elseif (!preg_match("#[A-Z]+#", $pwd)) {
			$valid = 0;
		}else{
			$valid = 1;
		}
			}
	return $valid;
}
function historyPassword($conn,$pwd){

	$sql = "select *  from tblhistorypass where ROWNUM <=(select usage_set from tblmgtpassword)  and password = '".md5($pwd)."' AND USERNAME='".trim($_SESSION["uid_session"])."' AND GID='".trim($_SESSION["grpID"])."' order by waktu asc";
#die($sql);
	$cek = $conn->query($sql);
	$count = $cek->size();
	if($count>0){
		$return= 0;
	}else{
		$return = 1;
	}
	return $return;
}
$conn->disconnect();
//echo $zipcode;
?>
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="border-bottom:1px solid #D7D7D7;">
			<div style="padding-bottom:9px;">
			<span style="color:#1a68a4; font-size:16px; font-weight:bold;">
			<?php
			$messageBox =($_SESSION['statusRespon']=="0")? 
                            "<div style='background:#FDE9DF;padding:5px;border:1px #CCC solid;color:#633'>
							 <img src='".base_url."img/warninglogo.png' style='border:none'> ".$_SESSION['respon']."</div>" : 
							 "<div style='background:#E5EEF5;padding:5px;border:1px #CCC solid;'>
							 <img src='".base_url."img/accept.png' style='border:none'> ".$_SESSION['respon']."</div>";
							 
							
			if($_SESSION['respon']!=""){
				echo $messageBox;
				$_SESSION['respon']= "";	
			}else{				
				$judul = array(	"taxpayer"=>"Taxpayer  Management",
								"edittaxpayer"=>"Taxpayer  Management",
								"depositor"=>"Depositor  Management",
								"editdepositor"=>"Depositor  Management",
								"profile"=>"Profile  Management",
								"activation"=>"Activation Token");
				echo $judul[$div]."<br />";
			}
			?>									
			</span>
			</div>
		</td>
	</tr>
</table>
<?php
echo ($err == "npwp_available")? "<font style='font-size:11px;color:#FF0000;font-style:italic'>* ada duplikasi NPWP</font>" : "";
if(in_array($div,array("taxpayer","edittaxpayer"))){
	if(in_array($_SESSION['priv_session'],array('0','3'))){
		echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
	}
$zipcode = ($div=='taxpayer')?'':$zipcode;
?>
<strong  style="font-size:11px; font-family:arial; color:#333333;">
<label style="cursor:pointer"><input type="radio" name="type" checked />Single Input</label>
<label style="cursor:pointer"><input type="radio" name="type" onclick="location.href='<?php echo base_url?>modul/upload/taxpayer'" />Multiple Input</label>
</strong>
<form name="frmWP" method="post" action="<?php echo base_url."modul/manage/".($div=='taxpayer'?'inserttaxpayer':'updatetaxpayer')?>" id="frmWP" >
<input type="hidden" name="div" value="<?php echo($div);?>">
<input type="hidden" name="id" value="<?php echo($id);?>">
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">Accounts</td>
	</tr>
</table>
<table cellpadding="0" cellspacing="0" width="100%" style="font-size:11px; font-family:arial; color:#333333;">
	<tr>
		<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong><em>NPWP</em> <font color="#FF0000">*</font></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="npwp" type="text" id="npwp" value="<?php echo($npwp); ?>" maxlength="15" <?php if($div=="edittaxpayer"){ echo $readonly; }else{ echo "onkeyup=javascript:cekNPWP(this,'wp')"; }?> class="isi number nospace" label="NPWP" fix="15">
		<button type="button" class="btn_1" style="width:60px" onclick="inquiry(1)">Inquiry</button>&nbsp;&nbsp;
		<font id='warningNPWP'></font>
		</span>		
		</td>
	</tr>
	<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Name</strong> <font color="#FF0000">*</font></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="nama" type="text" id="nama" value="<?php echo($nama); ?>" size="40" class="isi" label="Name">
        </span></td>
	</tr>
	<tr>
		<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Address</strong> <font color="#FF0000">*</font></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="address" type="text" id="address" value="<?php echo($address); ?>" size="120" class="isi" label="Address">
        </span></td>
	</tr>
	<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>City</strong> <font color="#FF0000">*</font></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="city" type="text" id="city" value="<?php echo($city); ?>" class="isi" label="City">
        </span></td>
	</tr>
	<tr>
		<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Zip Code</strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="zipcode" type="text" id="zipcode" value="<?php echo($zipcode); ?>" size="10" class="number" fix="5" label="Zip Code" maxlength="5">
        </span></td>
	</tr>
	<tr>
		<td height="20"><span style="padding:6px 0px 6px 9px;"><strong>&nbsp;</strong></span></td>
		<td><span style="padding:6px 0px 6px 9px;">
        <button type="button" class="btn_1" onClick="cekFormWP('frmWP')" style="width:60px">Save</button> 
		<?php if($div=="taxpayer"){?>
			<button type="reset" class="btn_1" style="width:60px">Reset</button>
		<?php
		}elseif($div=="edittaxpayer"){
		?>
			<button type="button" onclick="history.back()" class="btn_1" style="width:60px">Cancel</button>
		<?php
		}
		?>
		</span></td>
	</tr>
</table>			
</form>
<?php

$headers = array('NAMA','NPWP','ADDRESS','CITY','ZIPCODE');
$orderby = setSortir(array('sort'=>'NAMA','order'=>'desc'),$headers);	
//browse semua wajib pajak
$sql = "Select NAMA, NPWP, ADDRESS, CITY, ZIPCODE , IDPAYEE, ID 
		From TCOMPANY
		Where IDPAYEE = '". trim($_SESSION["npwp_session"]) ."' ";
$sql = $sql . $orderby;
$conn->connect();
$table = new HTMLTable();
$table->connection = $conn;
$table->width = "100%";
$table->navRowSize = 10;
$table->SQL = $sql;

// elemen data yang akan di passing
$cols = array();
$cols[0] = 1;

//untuk ambil tipe dokumen BC 2.0 atau BC 2.3
$data = array();
$data[] = array("#","Pilih Proses");
$data[] = array(base_url."modul/manage/edittaxpayer"," - Edit Data");
$data[] = array(base_url."modul/manage/deletetaxpayer"," - Delete Data");

$table->showCheckBox(false,$cols);	
$table->showPager(true,$F_HANDLER->BOTTOM,50,20);
$table->showRDPanel(true,$F_HANDLER->BOTH,$cols,$data);

$table->field[0]->name = "NAMA";
$table->field[0]->headername = "Nama Wajib Pajak";
$table->field[0]->align = $F_HANDLER->LEFT;

$table->field[1]->name = "NPWP";
$table->field[1]->headername = "NPWP";
$table->field[1]->align = $F_HANDLER->LEFT;


$table->field[2]->name = "ADDRESS";
$table->field[2]->headername = "Alamat";
$table->field[2]->align = $F_HANDLER->LEFT;

$table->field[3]->name = "CITY";
$table->field[3]->headername = "Kota";
$table->field[3]->align = $F_HANDLER->LEFT;


$table->field[4]->name = "ZIPCODE";
$table->field[4]->headername = "Kode Pos";
$table->field[4]->align = $F_HANDLER->LEFT;

$table->field[5]->name = "IDPAYEE";
$table->field[5]->headername = "Kode Pos";
$table->field[5]->align = $F_HANDLER->LEFT;
$table->field[5]->hidden = true;

$table->field[6]->name = "ID";
$table->field[6]->headername = "ID";
$table->field[6]->align = $F_HANDLER->LEFT;
$table->field[6]->hidden = true;

$table->drawTable();

}elseif(in_array($div,array("depositor","editdepositor"))){	
?>
<form name="frmWP" method="post" action="<?php echo base_url."modul/manage/".($div=='depositor'?'insertdepositor':'updatedepositor')?>">
<input type="hidden" name="act" value="<?php echo($act);?>">
<input type="hidden" name="idacc" value="<?php echo($id);?>">
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">Accounts</td>
	</tr>
</table>
<table cellpadding="0" cellspacing="0" width="100%" style="font-size:11px; font-family:arial; color:#333333;">
	<tr>
		<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong><em>NPWP</em> <font color="#FF0000">*</font></strong><span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="npwp" type="text" id="npwp" value="<?php echo($npwp); ?>" maxlength="15" <?php if($div=='editdepositor'){ echo $readonly; }else{ echo "onkeyup=javascript:cekNPWP(this,'dp')";}?> class="isi number nospace" label="NPWP" fix="15">
		<button type="button" class="btn_1" style="width:60px" onclick="inquiry(1)">Inquiry</button>&nbsp;&nbsp;
		<font id='warningNPWP'></font>
		</span></td>
	</tr>
	<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Name</strong> <font color="#FF0000">*</font></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><input name="nama" type="text" id="nama" value="<?php echo($nama);?>" size="40" maxlength="50" <?php #echo $readonly?> /></span></td>
	</tr>
	<tr>
		<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Account No. <font color="#FF0000">*</font></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;">
			<span style="padding:6px 0px 6px 9px;">
			<input name="account" type="text" id="account" value="<?php echo($account); ?>" size="30" maxlength="13" <?php echo ($div!="editdepositor")? /*$readonly*/"":"";?>>
			<strong>Account type : </strong>
					<select name="tipeacc">
					<?php
					$arrtipeacc = array("1"=>"- Saving Account","2"=>"- Current Account");
					foreach($arrtipeacc as $a=>$b){
						echo ($tipeacc==$a)? "<option value='$a' selected>$b</option>" : "<option value='$a'>$b</option>";						
					}
					?>					
					</select>
			</span>
		</td>
	</tr>
	<tr>
		<td height="20"><span style="padding:6px 0px 6px 9px;"><strong>&nbsp;</strong></span></td>
		<td><span style="padding:6px 0px 6px 9px;"><input type="hidden" name="SubmitDP" id="SubmitDP" value="<?php echo($type_form);?>"/><button type="button" class="btn_1"  onClick="cekFormPe();" style="width:60px">Save</button> <button type="button" onclick="history.back()" class="btn_2" style="width:75px">Cancel</button></span></td>
	</tr>
</table>	
</form>
<?php
				
//print_r($_GET);
$headers = array('NAMA','NPWP','ACCOUNT');
$orderby = setSortir(array('sort'=>'NAMA','order'=>'asc'),$headers);	
			
//browse semua wajib pajak
$sql = "Select a.NAMA, a.NPWP, 
		case when b.TIPEACC=1 then b.ACCOUNT+' - Saving Account'
		when b.TIPEACC=2 then b.ACCOUNT+' - Current Account'
		else b.ACCOUNT end as ACCOUNT,		
		a.ID, b.ID as IDA From TPENYETOR a LEFT JOIN TACCOUNT b ON a.NPWP=b.NPWP
		Where IDINI = '". ($_SESSION["ID"]) ."' ";
$sql = $sql . $orderby;
//print_r($_GET);
$table = new HTMLTable();
$table->connection = $conn;
$table->width = "100%";
$table->navRowSize = 10;
$table->SQL = $sql;

// elemen data yang akan di passing
$cols = array();
$cols[0] = 1;
$cols[1] = 4;
//untuk ambil tipe dokumen BC 2.0 atau BC 2.3
$data = array();
$data[] = array("#","Pilih Proses");
$data[] = array(base_url."modul/manage/editdepositor"," - Edit Data"); 
$data[] = array(base_url."modul/manage/deletedepositor"," - Delete Data");

$table->showCheckBox(false,$cols);	
$table->showPager(true,$F_HANDLER->BOTTOM,50,20);
$table->showRDPanel(true,$F_HANDLER->BOTH,$cols,$data);

$table->field[0]->name = "NAMA";
$table->field[0]->headername = "Nama Wajib Pajak";
$table->field[0]->align = $F_HANDLER->LEFT;

$table->field[1]->name = "NPWP";
$table->field[1]->headername = "NPWP";
$table->field[1]->align = $F_HANDLER->LEFT;

$table->field[2]->name = "ACCOUNT";
$table->field[2]->headername = "ACCOUNT";
$table->field[2]->align = $F_HANDLER->LEFT;

$table->field[3]->name = "ID";
$table->field[3]->headername = "ID";
$table->field[3]->align = $F_HANDLER->LEFT;
$table->field[3]->hidden = true;

$table->field[4]->name = "IDA";
$table->field[4]->headername = "IDA";
$table->field[4]->align = $F_HANDLER->LEFT;
$table->field[4]->hidden = true;

$table->drawTable();

} else if($div=="profile"){
?>
<form method="post" action="<?php echo base_url?>modul/manage/updatepassword" id="formPass">	
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">Edit Password</td>
	</tr>
</table>
<table cellpadding="0" cellspacing="0" width="100%" style="font-size:11px; font-family:arial; color:#333333;">
	<tr>
		<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Old Password <font color="#FF0000">*</font></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="pwdlama" type="password" id="pwdlama" class="isi nospace" label="Old Password">
        </span></td>
	</tr>
	<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>New Password <font color="#FF0000">*</font></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="pwdbaru1" type="password" id="pwdbaru1" class="isi nospace" label="New Password" >
     	</span></td>
	</tr>
	<tr>
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <strong>Re-type New Password <font color="#FF0000">*</font></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="pwdbaru2" type="password" id="pwdbaru2" class="isi nospace" label="Re-type New Password"></span></td>
	</tr>
	<tr>
		<td height="20"><span style="padding:6px 0px 6px 9px;"><strong>&nbsp;</strong></span></td>
		<td><span style="padding:6px 0px 6px 9px;">
        <button type="button" class="btn_1" onclick="cekPassword('formPass')" style="width:60px">Save</button>
        <button type="button" onclick="history.back()" class="btn_2" style="width:75px">Cancel</button></span></td>
	</tr>
</table>
</form>		
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">Edit Profile</td>
	</tr>
</table>					
<form method="post" action="<?php echo base_url?>modul/manage/updateprofile" id="formEmail">
<table cellpadding="0" cellspacing="0" width="100%" style="font-size:11px; font-family:arial; color:#333333;">
	<tr>
		<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Fullname <font color="#FF0000">*</font></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="fullname" type="text" id="nama" value="<?php echo($_SESSION["nmuser_session"]); ?>" size="40" class="isi" label="Fullname">
        </span></td>
	</tr>
	<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Email <font color="#FF0000">*</font></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="email" type="text" id="email" size="50" value="<?php echo($_SESSION["email_session"]);?>" class="isi email nospace" label="Email">
       	</span></td>
	</tr>
	<tr>
		<td height="20"><span style="padding:6px 0px 6px 9px;"><strong>&nbsp;</strong></span></td>
		<td><span style="padding:6px 0px 6px 9px;">
        <button type="button" class="btn_1" onClick="cekFormEmail('formEmail')" style="width:60px">Save</button> <button type="button" onclick="history.back()" class="btn_2" style="width:75px">Cancel</button></span></td>
	</tr>
</table>
</form>
<?php
}elseif($div=="activation"){
?>
<form method="post" action="<?php echo base_url?>security/activation" id="formActivation">
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; 
	font-size:11px; font-family:arial; font-weight:bold;">Activation Token</td>
	</tr>
</table> 

<table cellpadding="0" cellspacing="0" width="100%" style="font-size:11px; font-family:arial; color:#333333;">
	<tr>
		<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Credential ID <font 
		color="#FF0000">*</font></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
	        <input name="credentialid" type="text" fix="12" maxlength="12" size="30" class="isi nospace" label="Credential ID">
        </span></td>
	</tr>
	<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
      		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Security Code 1 <font color="#FF0000">*</font></strong></span></td>
  		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="otp1" type="text" maxlength="6" fix="6" size="30" class="isi number nospace" label="Security Code 1" />
      </span></td>
    </tr>
<tr>
<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span 
style="padding:6px 0px 6px 9px;"><strong>Security Code 2 <font 
color="#FF0000">*</font></strong></span></td>
<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 
6px 9px;">
        <input name="otp2" type="text" maxlength="6" fix="6" size="30" 
class="isi number nospace" label="Security Code 2">
        </span></td>
</tr>
<tr>
<td height="20"><span style="padding:6px 0px 6px 
9px;"><strong>&nbsp;</strong></span></td>
<td><span style="padding:6px 0px 6px 9px;">
        <button type="button" class="btn_1" 
onClick="cekActivation('formActivation')" 
style="width:60px">Active</button> <button type="button" 
onclick="history.back()" class="btn_2" 
style="width:75px">Cancel</button></span></td>
</tr>
</table>
</form>		
<?php
}
	$conn->connect();
	if($div=="updatepassword"){		
		if(trim($pwdlama = $_POST["pwdlama"])==""){ echo "<script> window.location.href='".base_url."modul/manage/profile';</script>";exit;}
		$pwdbaru1 = $_POST["pwdbaru1"];
		$pwdbaru2 = $_POST["pwdbaru2"];
		if($pwdbaru1==$pwdbaru2){	
			
			$sql = "SELECT PASS_SET,ALPHA_SET,USAGE_SET FROM TBLMGTPASSWORD";
			$data = $conn->query($sql);
			if($data->next()){
				$usage_set = intval($data->get("USAGE_SET"));
				$minPass = $data->get("PASS_SET");
				$minNum = $data->get("ALPHA_SET");
			}
			
			$sq = "SELECT PASSWORD FROM TBLHISTORYPASS WHERE USERNAME = '". $_SESSION["uid_session"] ."' AND  GID = '". $_SESSION["ID"] ."' order by waktu asc";
			#die($sqls);
			$dat = $conn->query($sq);
			if($dat->size()==$usage_set){
				$i=0;
				while($dat->next()){
					$pass = $dat->get("PASSWORD");
					if($i!=0){
						if($pass == md5($pwdbaru1)){
							$newpas = $pass;
							break;
						}else{
							$newpas = "x";
						}
					}else{
						$newpas = "x";						
						$passLama = $pass;
					}
					$i++;
				}	
			}else{
				while($dat->next()){
					$pass = $dat->get("PASSWORD");				
					if($pass == md5($pwdbaru1)){
						$newpas = $pass;
						break;
					}else{
						$newpas = "x";
					}
				}					
			}
			//echo $newpas."-".$passLama;//die();
			//if(preg_match_all("/[\W]/",$pwdbaru1,$match)){
			//	echo("<script language=\"javascript\">\n");
			//	echo "   jAlert('Invalid Character in password!','',function(r){
			//		if(r==true) window.location.href='".base_url."modul/manage/profile';		
			//	});\n";								
			//	echo("</script>");
			//	exit;	
			//}else
			if(!preg_match("/[0-9]/",$pwdbaru1)){
				echo("<script language=\"javascript\">\n");
				echo "   jAlert('Password must containt numeric!','',function(r){
					if(r==true) window.location.href='".base_url."modul/manage/profile';		
				});\n";								
				echo("</script>");
				exit;	
			}elseif(strlen(ereg_replace("[^0-9]", "",$pwdbaru1)) < $alpha_set){
				echo("<script language=\"javascript\">\n");
				echo "   jAlert('Password at least ".$alpha_set." digit numeric!','',function(r){
					if(r==true) window.location.href='".base_url."modul/manage/profile';		
				});\n";								
				echo("</script>");
				exit;	
			}else{
			#echo $newpas; exit;
			if (historyPassword($conn,$pwdbaru1)==1){
			if(cekPassword($pwdbaru1,$minPass,$minNum)==1){
				if (($newpas=="x") or ($newpas=="")){
					//print_r($_SESSION);die();
					#echo $usage_set.'-2'.$usage_tot; exit;
					$sql = "Select * 
							From TBLUSER 
							Where lower(USERLOGIN) = '". strtolower($_SESSION["uid_session"]) ."' And PASSWORD = '". md5($pwdlama) ."' And lower(ID) = '". strtolower(trim($_SESSION["grpID"])) ."'";		
							#die($sql);
					//print_r($conn);die();
					$data = $conn->query($sql);			
					if($data->next()){
						$sql = "Update TBLUSER Set PASSWORD = '". md5($pwdbaru1) ."',EMAILSTATUS = '0', LAST_CHANGEPASS = SYSDATE 
								Where lower(USERLOGIN) = '". strtolower($_SESSION["uid_session"]) ."' And PASSWORD = '". md5($pwdlama) ."' And NPWP = '". $_SESSION["npwp_session"] ."'";
						//die($sql);
						$datar = $conn->execute($sql);
						if($datar){
							$_SESSION['EMAILSTATUS'] = "0"; 
							$conn->execute("insert into tblhistorypass (username, gid, password, waktu ) values('". $_SESSION["uid_session"] ."','". $_SESSION["grpID"] ."','". md5($pwdbaru1) ."',SYSDATE)");
						}
						
						
						
						$aktivitas = "Berhasil melakukan perubahan Password";
						audit($conn,$aktivitas);
						$email = $data->get("EMAIL");
						
						$subject = "Perubahan Password e-RTE";
						$body = "Yth. ".ucfirst($username).",<br>Anda telah berhasil memperbaharui password Anda pada E-RTE SCB.<br>Berikut informasinya :<br> 
								<li>Group Id : ".strtoupper($_SESSION['grpID'])." </li>
								<li>User Id : ".$_SESSION['uid_session']."</li>
								<li>Password Baru : $pwdbaru1</li><br>Silakan gunakan password baru diatas untuk mengakses account Anda.
								<br><br>Terimakasih<br><br><b>Admin E-RTE SCB</b><br><br>";
								
						sendEmail(array('to'=>$email,'subject'=>$subject,'isi'=>$body));				
						echo("<script language=\"javascript\">\n");
						echo "  jAlert('Perubahan Password Berhasil, Silahkan Login Kembali!','',function(r){
							if(r==true) window.location.href='".base_url."log/out';	
						});\n";					
						echo("</script>");
						exit;
					} else {
						echo("<script language=\"javascript\">\n");
						echo "   jAlert('Old Password Not Match!','',function(r){
							if(r==true) window.location.href='".base_url."modul/manage/profile';		
						});\n";								
						echo("</script>");
						exit;
					}
				}
			} else {				
				echo("<script language=\"javascript\">\n");
				echo "     jAlert('Password must consist of at least ".$minPass." characters (lowercase and uppercase) and a at least contain of ".$minNum." numerics!','',function(r){
						if(r==true) window.location.href='".base_url."modul/manage/profile';	
					});\n";							
				echo("</script>");
				exit;
			}
			}else{
				echo("<script language=\"javascript\">\n");
						echo "   jAlert('Password have been used. Please try again!','',function(r){
							if(r==true) window.location.href='".base_url."modul/manage/profile';		
						});\n";								
						echo("</script>");
						exit;
			}
		}
		}else{
			echo("<script language=\"javascript\">\n");
			echo("     jAlert('Re-Type Password Not Match!','',function(r){
						if(r==true) window.location.href='".base_url."modul/manage/profile';	
					});\n");			
			echo("</script>");
			exit;
		}
	}
}
?>
