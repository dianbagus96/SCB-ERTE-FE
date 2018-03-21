<?php
if(in_array($_SESSION["priv_session"],array("0","1"))==false){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}elseif(time()>$_SESSION['timeout_session']){	
	echo "<script> window.location.href='".base_url."err/7/".md5(7)."';</script>";exit;
}else{
	$_SESSION['timeout_session'] = TIMEOUT;
}
require_once("dbconn.php");
global $conn;
$conn->connect();
$r = $_REQUEST['R'];
$data = $_REQUEST['radiopanel'];
if($div=='add' || $div=='edit'){
	if(trim($data)==""){ echo "<script> window.location.href='".base_url."modul/rekening/view';</script>";exit;}	
	$radio = split(";",$data);
	$id = $radio[0];
	$npwp = $radio[1];
	$rekening = $radio[2];
	$notif = $radio[3];
	if($div=='add'){ $rekening = '';}
}elseif($div == "insert"){
	if(!is_array($r)){ echo "<script> window.location.href='".base_url."modul/rekening/view';</script>";;exit;}	
	foreach($r as $a=>$b){ $R[$a]=strFilter($b);}		
	function timeStamp(){
		$xx = date("YmdHis");
		return $xx;
	}
	$sql = "SELECT ID FROM TACCOUNT where ACCOUNT='".$R['ACCOUNT']."'";
	$data = $conn->query($sql);
	if(!$data->next()){
		$stamp = timeStamp();
		$sql = "insert into TACCOUNT (ID,NPWP,ACCOUNT,STSACC,GROUPID) VALUES(".$stamp.",'".$R['NPWP']."','".$R['ACCOUNT']."','RTE','".$R['ID']."')";	
		$hasil = $conn->execute($sql);
		if($hasil){
			$_SESSION['respon'] = "Anda telah Berhasil membuat Rell ID Baru";
			$_SESSION['statusRespon']=1;
			
			$aktivitas = "Berhasil membuat Rell ID baru pada NPWP : ".$R['NPWP'];
			audit($conn,$aktivitas);		
			$write_txt = "ACC|".$R['NPWP']."|".$R['ACCOUNT']."|".$stamp." \r\n";
			writetxt($write_txt,"DOKACC.",$_SESSION['grpID']);	
		}
	}else{
		$_SESSION['respon'] = "Rell ID Sudah Terdaftar";
		$_SESSION['statusRespon']=0;		
	}		
	echo "<script> window.location.href='".base_url."modul/rekening/view';</script>";exit;	
}elseif($div == "update"){
	if(!is_array($r)){ echo "<script> window.location.href='".base_url."modul/rekening/view';</script>";;exit;}	
	
	foreach($r as $a=>$b){ $R[$a]=strFilter($b);}		
	if($R['ID']){
		$sql = "select ACCOUNT from TACCOUNT where ACCOUNT='".$R['ACCOUNT']."' and NPWP='".$R['NPWP']."' ";	
		$hasil = $conn->query($sql);
		if(!$hasil->next()){
			$sql = "update TACCOUNT set ACCOUNT='".$R['ACCOUNT']."' where ID=".$R['ID']."";				
			$hasil = $conn->execute($sql);		
			if($hasil){
				$_SESSION['respon'] = "Perubahan pada Rell ID Berhasil";	
				$_SESSION['statusRespon']=1;
				
				$aktivitas = "Berhasil mengedit Rekeing pada NPWP : ".$R['NPWP'];
				audit($conn,$aktivitas);
				$write_txt = "ACC|".$R['NPWP']."|".$R['ACCOUNT']."|".$R['ID']." \r\n";
				writetxt($write_txt,"DOKACC.",$_SESSION['grpID']);	
			}
		}else{
			$_SESSION['respon'] = "Rell ID Sudah Terdaftar";
			$_SESSION['statusRespon']=0;
		}				
	}		
	echo "<script> window.location.href='".base_url."modul/rekening/view';</script>";exit;	
}
$conn->disconnect();
?>
<table cellpadding="0" cellspacing="0" width="85%">
	<tr>
		<td style="border-bottom:1px solid #D7D7D7;">
			<div style="padding-bottom:9px;">
			<span style="color:#1a68a4; font-size:16px; font-weight:bold;">
			<?php echo ($div == "edit")? "Edit Rekening" : "Add New Rekening";
			?>                            
			</span><br />
		  </div>
		</td>
	</tr>
</table>			
<form method="post" id="formRekening" action='<?php echo base_url."modul/rekening/".($div=='edit'?'update':'insert');?>'>  
<input type="hidden" name="R[ID]" value="<?php echo $id;?>">						
<table style="padding-top:18px;" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">Rell ID Information</td>
	</tr>
</table>				
<table cellpadding="0" cellspacing="0" width="100%" style="font-size:11px; font-family:arial; color:#333333;">
	<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>NPWP <font color="#FF0000">*</font></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
      <input name="R[NPWP]" type="text" id="npwp" value="<?php echo $npwp?>" readonly style='background:#E0E0E0;border:1px #999 solid;padding:2px;width:200px; margin-left:2px;'></span></td>
	</tr>
	<tr>
		<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Rell ID <font color="#FF0000">*</font></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="R[ACCOUNT]" type="text" id="account" value="<?php echo trim($rekening)?>" style="width:200px; margin-left:2px;" 
        class="isi" label="Rell ID" >
      </span></td>
	</tr>	
	<tr>
		<td height="20"><span style="padding:6px 0px 6px 9px;"><strong>&nbsp;</strong></span></td>
		<td><span style="padding:6px 0px 6px 9px;">
        <button type="button" class="btn_1"  onclick="cekFormRekening('formRekening')" style="width:60px">Save</button> 
		 &nbsp;
		 <button type="button" onclick="history.back()" class="btn_2" style="width:75px">Cancel</button></span></td>
	</tr>
</table>	
</form>	
