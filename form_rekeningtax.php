<?php
if(in_array($_SESSION["priv_session"],array("3"))==false){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}else{

require_once("dbconn.php");
global $conn;
$conn->connect();
$r = $_REQUEST['R'];
$data = $_REQUEST['radiopanel'];
$readonly ="readonly style='background:#E0E0E0;border:1px #999 solid;padding:2px'";
if($div=='edit'){
	if(trim($data)==""){ echo "<script> window.location.href='".base_url."modul/rekeningtax/view';</script>";exit;}	
	$radio = split(";",$data);
	$id = $radio[0];
	$rekening = $radio[1];
	$name = $radio[2];
	$npwp = $radio[3];
	$groupid = $radio[5];
	$deptid = $radio[6];
	
	
}elseif($div == "insert"){
	if(!is_array($r)){ echo "<script> window.location.href='".base_url."modul/rekeningtax/view';</script>";;exit;}	
	foreach($r as $a=>$b){ $R[$a]=strFilter($b);}		
	function timeStamp(){
		$xx = date("YmdHis");
		return $xx;
	}
	$sql = "SELECT ID FROM TACCOUNT where ACCOUNT='".$R['ACCOUNT']."' AND NPWP='".$R['NPWP']."' AND GROUPID='".$R['GROUPID']."' AND DEPTID='".$R['DEPTID']."'";
	$data = $conn->query($sql);
	if(!$data->next()){
		$sql = "insert into TACCOUNT (ID,NPWP,ACCOUNT,GROUPID,DEPTID,STSACC,FREEZE) 
				VALUES(".timeStamp().",'".$R['NPWP']."','".$R['ACCOUNT']."','".$R['GROUPID']."','".$R['DEPTID']."','SSP','N')";	
		$hasil = $conn->execute($sql);
		$sql = "insert into TPENYETOR (ID,NPWP,NAMA,IDINI) VALUES(".timeStamp().",'".$R['NPWP']."','".$R['NAME']."','".trim($_SESSION['ID'])."')";	
		$hasil = $conn->execute($sql);
		
		if($hasil){
			$_SESSION['respon'] = "Anda telah Berhasil membuat Rekening Baru";
			$_SESSION['statusRespon']=1;
			
			$aktivitas = "Berhasil membuat Rekening baru pada NPWP : ".$R['NPWP'];
			audit($conn,$aktivitas);		
			require_once("dbconndb.php");
			$connDB->connect();
			$connDB->execute($sql);
			$connDB->disconnect();
		}
	}else{
		$_SESSION['respon'] = "Rekening Sudah Terdaftar";
		$_SESSION['statusRespon']=0;		
	}		
	echo "<script> window.location.href='".base_url."modul/rekeningtax/view';</script>";exit;	
}elseif($div == "update"){
	if(!is_array($r)){ echo "<script> window.location.href='".base_url."modul/rekeningtax/view';</script>";;exit;}	
	
	foreach($r as $a=>$b){ $R[$a]=strFilter($b);}		
	if($R['ID']){		
		$sql = "update TACCOUNT set USERS='".$R['NAME']."', GROUPID='".$R['GROUPID']."',DEPTID='".$R['DEPTID']."', ACCOUNT='".$R['ACCOUNT']."' 
				where ID=".$R['ID']."";						
		$hasil = $conn->execute($sql);		
		if($hasil){
			$_SESSION['respon'] = "Perubahan pada Rekening Berhasil";	
			$_SESSION['statusRespon']=1;
			
			$aktivitas = "Berhasil mengedit Rekeing pada NPWP : ".$R['NPWP'];
			audit($conn,$aktivitas);				
		}
				
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
			<?php echo ($div == "edit")? "Edit Account" : "Add Account";?>                            
			</span><br />
		  </div>
		</td>
	</tr>
</table>			
<form method="post" id="formRekening" action='<?php echo base_url."modul/rekeningtax/".($div=='edit'?'update':'insert');?>'>  
<input type="hidden" name="R[ID]" value="<?php echo $id;?>">
<input type="hidden" name="R[ACCOUNT_OLD]" value="<?php echo $npwp;?>">
<input type="hidden" name="R[NPWP]" value="<?php echo $npwp?>" />
<table style="padding-top:18px;" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">Account Information</td>				</tr>
	</tr>
</table>				
<table cellpadding="0" cellspacing="0" width="100%" style="font-size:11px; font-family:arial; color:#333333;"> 
	<tr>
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Account No. <font color="#FF0000">*</font></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="R[ACCOUNT]" type="text" id="account" value="<?php echo $rekening?>" maxlength="13" class="isi number nospace" label="No. Rekening" fix="13"></span></td>
	</tr>
	<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
		<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Name   <font color="#FF0000">*</font></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="R[NAME]" type="text" id="name" value="<?php echo $name?>" style="width:300px; margin-left:2px;" maxlength="50" class="isi" label="Name"></span></td>
	</tr>
	<tr>
		<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Group</strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
		<input name="R[GROUPID]" type="text" id="groupid" value="<?php echo $groupid?>"  maxlength="13" size="10"> - 
		<input name="R[DEPTID]" type="text" id="deptid" value="<?php echo $deptid?>"  maxlength="13" size="10">			
		<button type="button" onClick="javascript:popAccount();" style="font-size:11px;" class="btn_8">Browse Group</button><font id="warningGroupid"></font>
		</span>		</td>
	</tr>
	<tr>
		<td height="20"><span style="padding:6px 0px 6px 9px;"><strong>&nbsp;</strong></span></td>
		<td><span style="padding:6px 0px 6px 9px;">
        <button type="button" class="btn_1"  onclick="cekFormRekening('formRekening')" style="width:60px">Save</button> 		  
		 <button type="button" onclick="history.back()" class="btn_2" style="width:75px">Cancel</button></span></td>
	</tr>
</table>	
</form>	
<?php
}
?>
