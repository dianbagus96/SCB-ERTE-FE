<?php
if(in_array($_SESSION["priv_session"],array("0"))==false){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}elseif(time()>$_SESSION['timeout_session']){	
	echo "<script> window.location.href='".base_url."err/7/".md5(7)."';</script>";exit;
}else{
	$_SESSION['timeout_session'] = TIMEOUT;
}
	
$kat = $_REQUEST["kat"];
require_once("conf.php");
require_once("dbconn.php");
global $conn;
$conn->connect();

#proses


if($div=="setbank" && trim($_FILES["gambarbank"]["name"])!=""){
	$type = $_FILES["gambarbank"]["type"];	
	$bank = $_FILES["gambarbank"]["name"];
	if($bank && ($type == "image/jpg" || $type == "image/jpeg" || $type == "image/pjpeg")){
		$sql = "Update TBLLOGO set NAMALOGO='$bank' where ID='1'";
		$data = $conn->execute($sql);
		move_uploaded_file($_FILES['gambarbank']['tmp_name'],"logo/".$bank);   
		
		$aktivitas = "Berhasil mengubah logo bank";
		audit($conn,$aktivitas);
		$_SESSION['respon'] = "Perubahan pada Logo Bank berhasil";		
	}else{
		$_SESSION['respon'] = "Logo Bank harus berformat jpg!";	
	}	
}elseif($div=="setttd" && trim($_FILES["gambarttd"]["name"])!=""){
	$type = $_FILES["gambarttd"]["type"];	
	$ttd = $_FILES["gambarttd"]["name"];
	if($ttd && ( $type == "image/jpg" || $type == "image/jpeg" || $type == "image/pjpeg")){
		$sql = "Update TBLLOGO set NAMALOGO='$ttd' where ID='2'";
		$data = $conn->execute($sql);
		move_uploaded_file($_FILES['gambarttd']['tmp_name'],"logo/".$ttd);   
		
		$aktivitas = "Berhasil mengubah logo tanda tangan";
		audit($conn,$aktivitas);
		$_SESSION['respon'] = "Perubahan pada Logo Tanda Tangan berhasil";	
	}else{
		$_SESSION['respon'] = "Logo Tanda Tangan harus berformat jpg!";	
	}	
}elseif($div=="setcap" && trim($_FILES["gambarcap"]["name"])!=""){
	$type = $_FILES["gambarcap"]["type"];	
	$cap = $_FILES["gambarcap"]["name"];
	if($cap && ( $type == "image/jpg" || $type == "image/jpeg" || $type == "image/pjpeg")){
		$sql = "Update TBLLOGO set NAMALOGO='$cap' where ID='3'";
		$data = $conn->execute($sql);
		move_uploaded_file($_FILES['gambarcap']['tmp_name'],"logo/".$cap);   
		
		$aktivitas = "Berhasil mengubah logo cap";
		audit($conn,$aktivitas);
		$_SESSION['respon'] = "Perubahan pada Logo Cap Bank berhasil";		
	}else{
		$_SESSION['respon'] = "Logo Cap Bank harus berformat jpg!";	
	}	
}

$sql = "SELECT NAMALOGO FROM TBLLOGO WHERE ID='1'";
$data = $conn->query($sql);
if($data->next()){
	$bank = $data->get("NAMALOGO");
}

$sql = "SELECT NAMALOGO FROM TBLLOGO WHERE ID='2'";
$data = $conn->query($sql);
if($data->next()){
	$ttd = $data->get("NAMALOGO");
}

$sql = "SELECT NAMALOGO FROM TBLLOGO WHERE ID='3'";
$data = $conn->query($sql);
if($data->next()){
	$cap = $data->get("NAMALOGO");
}
?>

<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="border-bottom:1px solid #D7D7D7;">
			<div style="padding-bottom:9px;">
			<span style="color:#1a68a4; font-size:16px; font-weight:bold;">
			<?php
			$messageBox =(substr($_SESSION['respon'],strlen($_SESSION['respon'])-1,1)=="!")? 
			"<div style='background:#FDE9DF;padding:5px;border:1px #CCC solid;color:#633'>
			 <img src='".base_url."img/warninglogo.png' style='border:none'> ".$_SESSION['respon']."</div>" : 
			 "<div style='background:#E5EEF5;padding:5px;border:1px #CCC solid;'>
			 <img src='".base_url."img/accept.png' style='border:none'> ".$_SESSION['respon']."</div>";
			 
			if(trim($_SESSION['respon'])!=""){
				echo $messageBox;
				$_SESSION['respon'] = "";
			}else{
				echo "Logo Edit<br />";
			}
			?>
			</span>
			</div>
		</td>
	</tr>
</table>					
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">Edit Logo Bank </td>
	</tr>
</table>
<form method="post" action="<?php echo base_url?>modul/logo/setbank" enctype="multipart/form-data">
<table cellpadding="0" cellspacing="0" width="100%" style="font-size:11px; font-family:arial; color:#333333;">
	<tr>
		<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;" valign="top"><span style="padding:6px 0px 6px 9px;"><strong>Logo Bank </strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
		<div><img src="<?php echo base_url?>logo/<?php echo $bank;?>" style="width:100px;border:none"/><?php //echo $bank?></div>
		</span></td>
	</tr>
	<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Browse Logo Bank </strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><input name="gambarbank" type="file" id="gambarbank"></span></td>
	</tr>
	<tr>
		<td height="20"><span style="padding:6px 0px 6px 9px;"><strong>&nbsp;</strong></span></td>
		<td><span style="padding:6px 0px 6px 9px;"><input type="hidden" name="Submit" id="Submit" value="bank"/>
		  <button type="submit" class="btn_1" style="width:63px">Update</button>&nbsp; 
		  <button type="button" onclick="history.back()" class="btn_2" style="width:75px">Cancel</button>
		  </span></td>
	</tr>
</table>
</form>		
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">Edit Cap & Tanda Tangan </td>
	</tr>
</table>					
<form method="post" action="<?php echo base_url?>modul/logo/setcap" enctype="multipart/form-data">
<table cellpadding="0" cellspacing="0" width="100%" style="font-size:11px; font-family:arial; color:#333333;">
	<tr>
		<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;" valign="top"><span style="padding:6px 0px 6px 9px;"><strong>Cap & Tanda Tangan </strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
		<div><img src="<?php echo base_url?>logo/<?php echo $cap;?>" style="width:130px;border:none"/><?php //echo $cap?></div>
		</span></td>
	</tr>
	<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Browse Cap & Tanda Tangan </strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><input name="gambarcap" type="file" id="email"></span></td>
	</tr>
	<tr>
		<td height="20"><span style="padding:6px 0px 6px 9px;"><strong>&nbsp;</strong></span></td>
		<td><span style="padding:6px 0px 6px 9px;"><input type="hidden" name="Submit" id="Submit" value="cap"/>
		<button type="submit" class="btn_1" style="width:63px">Update</button>&nbsp; 
		<button type="button" onclick="history.back()" class="btn_2" style="width:75px">Cancel</button>
		</span></td>
	</tr>
</table>
</form>
