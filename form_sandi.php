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
$TC = arrFilter($_REQUEST['TC']);
$data = $_REQUEST['radiopanel'];
if($div=='add' || $div=='edit'){
	//if(trim($data)==""){ echo "<script> window.location.href='".base_url."modul/npwp/view';</script>";exit;}	
	$radio = split(";",$data);
	$id = $radio[0];
	$sandi = $radio[1];
	$nama  = $radio[2];
	$keterangan = $radio[3];
	if($div=='add'){ $npwp = '';}
}elseif($div == "insert"){
	if(!is_array($TC)){ echo "<script> window.location.href='".base_url."modul/sandi/view';</script>";;exit;}				
	unset($TC['sandi_old']);		
	//$TC["PIC"] = $TC["ID"];
	$hasil  = get_where("TBLDMSANDIRTE",array("SANDI"),array("SANDI"=>$TC['sandi']),"",$conn);	
	if(!$hasil->next()){
		$hasil  = insert("TBLDMSANDIRTE",$TC,"",$conn);
		if($hasil){
			$_SESSION['respon'] = "Anda telah Berhasil menambahkan Sandi Baru";
			$_SESSION['statusRespon']=1;
			
			$aktivitas = "Berhasil menambahkan sandi baru : ".$TC['sandi'];
			audit($conn,$aktivitas);		
		}
	}else{
		$_SESSION['respon'] = "Sandi Sudah Terdaftar";
		$_SESSION['statusRespon']=0;		
	}		
	echo "<script> window.location.href='".base_url."modul/sandi/view';</script>";exit;	
}elseif($div == "update"){
	if(!is_array($TC)){ echo "<script> window.location.href='".base_url."modul/sandi/view';</script>";;exit;}	
	
			
	if($TC['sandi']){
		$sandi_old = $TC['sandi_old'];
		unset($TC['sandi_old']);		
		//$TC['IDPAYEE'] = $TC['NPWP'];
		$hasil  = get_where("TBLDMSANDIRTE",array("ID"),array("ID"=>$TC['id']),"",$conn);
		if($hasil->next()){						
			$hasil = update("TBLDMSANDIRTE",$TC,array("ID"=>$TC["id"]),"",$conn);
			if($hasil){
				$_SESSION['respon'] = "Perubahan pada Sandi Berhasil";	
				$_SESSION['statusRespon']=1;
				
				$aktivitas = "Berhasil mengedit Sandi : ".$TC['sandi'];
				audit($conn,$aktivitas);				
			}
		}else{
			$_SESSION['respon'] = "Tidak ada perubahan";
			$_SESSION['statusRespon']=0;
		}				
	}		
	echo "<script> window.location.href='".base_url."modul/sandi/view';</script>";exit;	
}
$conn->disconnect();
?>
<table cellpadding="0" cellspacing="0" width="85%">
	<tr>
		<td style="border-bottom:1px solid #D7D7D7;">
			<div style="padding-bottom:9px;">
			<span style="color:#1a68a4; font-size:16px; font-weight:bold;">
			<?php echo ($div == "edit")? "Edit Sandi" : "Add New Sandi";
			?>                            
			</span><br />
		  </div>
		</td>
	</tr>
</table>			
<form method="post" id="formNPWP" action='<?php echo base_url."modul/sandi/".($div=='edit'?'update':'insert');?>'>  
<input type="hidden" name="TC[sandi_old]" value="<?php echo trim($sandi)?>" />
<table style="padding-top:18px;" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">Sandi Information</td>
	</tr>
</table>				
<table cellpadding="0" cellspacing="0" width="100%" style="font-size:11px; font-family:arial; color:#333333;">
	<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Sandi</strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="TC[id]" type="hidden" value="<?php echo $id?>"  style='width:200px; margin-left:2px;'>
	 <input name="TC[sandi]" type="text" value="<?php echo $sandi?>"  style='width:100px; margin-left:2px;' fix="4"  maxlength="4">	</span>
        </td>
	</tr>
	<tr>
		<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Nama Sandi</strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <textarea name="TC[nama_sandi]"  rows="3"  style="width:300px; margin-left:2px;"  
        label="Nama Sandi" ><?php echo $nama?></textarea>
        </span></td>
	</tr>
	<tr>
		<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Keterangan Sandi</strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <textarea name="TC[keterangan]"  rows="3" style="width:300px; margin-left:2px;"  
         label="Keterang Sandi" ><?php echo $keterangan?></textarea>
        </span></td>
	</tr>
	<tr>
		<td height="20"><span style="padding:6px 0px 6px 9px;"><strong>&nbsp;</strong></span></td>
		<td><span style="padding:6px 0px 6px 9px;">
        <button type="button" class="btn_1"  onclick="cekFormNPWP('formNPWP')" style="width:60px">Save</button>&nbsp;
		 <button type="button" onclick="history.back()" class="btn_2" style="width:75px">Cancel</button></span></td>
	</tr>
</table>	
</form>	
