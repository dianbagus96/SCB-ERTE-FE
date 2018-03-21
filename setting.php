<?php
if(in_array($_SESSION["priv_session"],array("0"))==false){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}elseif(time()>$_SESSION['timeout_session']){	
	echo "<script> window.location.href='".base_url."err/7/".md5(7)."';</script>";exit;
}else{
	$_SESSION['timeout_session'] = TIMEOUT;
}
$kat = $_REQUEST["kat"];
$set_user = $_REQUEST["set_user"];
$set_pwd = $_REQUEST["set_pwd"];
$set_alp = $_REQUEST["set_alp"];
$set_age = $_REQUEST["set_age"];
$set_fail = $_REQUEST["set_fail"];
$set_use = $_REQUEST["set_use"];
require_once("conf.php");
global $conn;
$conn->connect();

$news = htmlspecialchars(strFilter($_POST['set_user']));
if($div=="update"){
	if(trim($news)==""){echo "<script> window.location.href='".base_url."modul/setting/setting';</script>";exit;}
	$from = $_POST['tglfrom'];
	$to = $_POST['tglto'];
	$cek_sql = 'select count(*) as id from tblmgtpassword';
	$find = $conn->query($cek_sql);
	$find->next();
	if ($find->get('id')>0){
	$sql = " update tblmgtpassword set user_set=$set_user,pass_set=$set_pwd,alpha_set=$set_alp,age_set=$set_age,failed_set=$set_fail,usage_set=$set_use";	
	}else{
	$sql = " insert into tblmgtpassword(user_set,pass_set,alpha_set,age_set,failed_set,usage_set) values($set_user,$set_pwd,$set_alp,$set_age,$set_fail,$set_use)";	
	}
	//$sql = "update tblmgtpassword set user_set='".$set_user."',pass_set='".$set_pwd."',alpha_set='".$set_alp."',age_set='".$set_age."',failed_set='".$set_fail."',usage_set='".$set_use."'";
	$conn->execute($sql);
	$_SESSION['respon'] = "Berhasil mengupdate Management Password";
	echo "<script> window.location.href='".base_url."modul/setting/setting';</script>";exit;
}
$sql = "select USER_SET,PASS_SET,ALPHA_SET,AGE_SET,FAILED_SET,USAGE_SET from tblMgtPassword";
$datanews = $conn->query($sql);
if($datanews->next()){
	$user = $datanews->get('USER_SET');
	$pass = $datanews->get('PASS_SET');
	$alpha = $datanews->get('ALPHA_SET');	
	$age = $datanews->get('AGE_SET');
	$failed = $datanews->get('FAILED_SET');
	$usage = $datanews->get('USAGE_SET');	
}
?>

<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="border-bottom:1px solid #D7D7D7;">
			<div style="padding-bottom:9px;">
			<span style="color:#1a68a4; font-size:16px; font-weight:bold;">
			<?php
			$messageBox = "<div style='background:#E5EEF5;padding:5px;border:1px #CCC solid;'>
			 <img src='".base_url."img/accept.png' style='border:none'> ".$_SESSION['respon']."</div>";
			 
			if(trim($_SESSION['respon'])!=""){
				echo $messageBox;
				$_SESSION['respon'] = "";
			}else{
				echo "Management Password<br />";
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
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">Management Password</td>
	</tr>
</table>					
<form method="post" action="<?php echo base_url?>modul/setting/update" id="formsetting">
<table cellpadding="0" cellspacing="0" width="100%" style="font-size:11px; font-family:arial; color:#333333;">
	<tr>
		<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Minimum user id length <font color="#FF0000">*</font></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="set_user" type="text" id="set_user" class="isi number tujuh nospace" label="Minimum user id length" value="<?php echo $user; ?>" size="5" style="text-align:right"> 
        </span>
        <span style="padding:6px 0px 6px 9px;"><strong>Char</strong></span></td>
	</tr>
	<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Minimum pwd length <font color="#FF0000">*</font></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="set_pwd" type="text" id="set_pwd" class="isi number enam nospace" label="Minimum pwd length" value="<?php echo $pass; ?>" size="5" style="text-align:right">
     	</span>
         <span style="padding:6px 0px 6px 9px;"><strong>Char</strong></span></td>
	</tr>
	<tr>
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
      <strong>Numeric On Password <font color="#FF0000">*</font></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
      <input name="set_alp" type="text" id="set_alp" class="isi number dua nospace" label="Numeric On Password" value="<?php echo $alpha; ?>" size="5" style="text-align:right"></span>
       <span style="padding:6px 0px 6px 9px;"><strong>Numeric</strong></span></td>
	</tr>
   <tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
		<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Maximum age <font color="#FF0000">*</font></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="set_age" type="text" id="set_age" class="isi number sebulan nospace" label="Maximum age" value="<?php echo $age; ?>" size="5" style="text-align:right">
        </span>
         <span style="padding:6px 0px 6px 9px;"><strong>Day</strong></span></td>
	</tr>
	<tr >
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Max. failed login <font color="#FF0000">*</font></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="set_fail" type="text" id="set_fail" class="isi number tiga nospace" label="Max. failed login" value="<?php echo $failed; ?>" size="5" style="text-align:right">
     	</span>
        <span style="padding:6px 0px 6px 9px;"><strong>X</strong></span></td>
	</tr>
	<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
      <strong>Min. historical usage <font color="#FF0000">*</font></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
      <input name="set_use" type="text" id="set_use" class="isi number enam nospace" label="Min. historical usage" value="<?php echo $usage; ?>" size="5" style="text-align:right"></span>
      <span style="padding:6px 0px 6px 9px;"><strong>X</strong></span></td>
	</tr>
	<tr>
		<td height="20"><span style="padding:6px 0px 6px 9px;"><strong>&nbsp;</strong></span></td>
		<td><span style="padding:6px 0px 6px 9px;">
       	<input type="hidden" name="Submit" id="Submit"/>
		<button type="button" class="btn_1" onClick="cekSetPass('formsetting')" style="width:63px">Update</button>&nbsp; 
        <button type="button" onclick="history.back()" class="btn_2" style="width:75px">Cancel</button></span></td>
	</tr>
    
</table>
</form>
<iframe width=174 height=189 name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="<?php echo base_url?>ipopeng.htm" scrolling="no" 
		frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;"></iframe>
