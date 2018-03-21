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
	if(trim($data)==""){ echo "<script> window.location.href='".base_url."modul/npwp/view';</script>";exit;}	
	$radio = split(";",$data);
	$id = $radio[0];
	if ($div=='add'){
		$npwp = "";	
		$nama = "";	
		$alamat = "";
	}else{
		$npwp = $radio[1];	
		$nama = $radio[2];	
		$alamat = $radio[3];
	}		
	if($div=='add'){ $npwp = '';}
}elseif($div == "insert"){
	if(!is_array($TC)){ echo "<script> window.location.href='".base_url."modul/npwp/view';</script>";;exit;}				
	unset($TC['NPWP_OLD']);		
	$TC["PIC"] = $TC["ID"];
	$hasil  = get_where("tcompany",array("NPWP"),array("ID"=>$TC['ID'],"NPWP"=>$TC['NPWP']),"",$conn);	
	if(!$hasil->next()){
		$hasil  = insert("tcompany",$TC,"",$conn);
		if($hasil){
			$_SESSION['respon'] = "Anda telah Berhasil menambahkan NPWP Baru";
			$_SESSION['statusRespon']=1;
			
			$aktivitas = "Berhasil menambahkan NPWP baru pada Corp ID: ".$TC['ID'];
			audit($conn,$aktivitas);		
		}
	}else{
		$_SESSION['respon'] = "NPWP Sudah Terdaftar";
		$_SESSION['statusRespon']=0;		
	}		
	echo "<script> window.location.href='".base_url."modul/npwp/view';</script>";exit;	
}elseif($div == "update"){
	if(!is_array($TC)){ echo "<script> window.location.href='".base_url."modul/npwp/view';</script>";;exit;}	
	
			
	if($TC['ID']){
		$NPWP_OLD = $TC['NPWP_OLD'];
		unset($TC['NPWP_OLD']);		
		$TC['IDPAYEE'] = $TC['NPWP'];
		$hasil  = get_where("tcompany",array("NPWP","ADDRESS","NAMA"),array("ID"=>$TC['ID'],"NPWP"=>$TC['NPWP'],"NAMA"=>$TC['NAMA'],"ADDRESS"=>$TC['ADDRESS']),"",$conn);
		if(!$hasil->next() || $hasil->get('NPWP')!=$TC['NPWP']){						
			$hasil = update("tcompany",$TC,array("NPWP"=>$NPWP_OLD,"ID"=>$TC["ID"]),"",$conn);
			$cek = "select ID from tcompany where TRIM(PIC) <> TRIM(ID) AND  NPWP = '".$TC['NPWP']."' and ID = '".$TC['ID']."' ";
			$cek_hasil = $conn->query($cek);
			if ($cek_hasil->size() == 1){
				$sql = "update tbluser set NPWP ='".$TC['NPWP']."' where upper(ID) ='".strtoupper($TC["ID"])."' ";
				$conn->execute($sql);
				$sql = "update taccount set NPWP ='".$TC['NPWP']."' where upper(GROUPID) ='".strtoupper($TC["ID"])."' ";
				$conn->execute($sql); 
			}
			if($hasil){
				$_SESSION['respon'] = "Perubahan pada NPWP Berhasil";	
				$_SESSION['statusRespon']=1;
				
				$aktivitas = "Berhasil mengedit NPWP pada Corp ID : ".$TC['ID'];
				audit($conn,$aktivitas);				
			}
		}else{
			if($hasil->get('NPWP')==$TC['NPWP']){
				$_SESSION['respon'] = "Tidak ada perubahan";
			}else{
				$_SESSION['respon'] = "NPWP Sudah Terdaftar";	
			}
			$_SESSION['statusRespon']=0;
		}				
	}		
	//echo "<script> window.location.href='".base_url."modul/npwp/view';</script>";exit;	
}
$conn->disconnect();
?>
<table cellpadding="0" cellspacing="0" width="85%">
	<tr>
		<td style="border-bottom:1px solid #D7D7D7;">
			<div style="padding-bottom:9px;">
			<span style="color:#1a68a4; font-size:16px; font-weight:bold;">
			<?php echo ($div == "edit")? "Edit NPWP" : "Add New NPWP";
			?>                            
			</span><br />
		  </div>
		</td>
	</tr>
</table>			
<form method="post" id="formNPWP" action='<?php echo base_url."modul/npwp/".($div=='edit'?'update':'insert');?>'>  
<input type="hidden" name="TC[NPWP_OLD]" value="<?php echo trim($npwp)?>" />
<table style="padding-top:18px;" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">NPWP Information</td>
	</tr>
</table>				
<table cellpadding="0" cellspacing="0" width="100%" style="font-size:11px; font-family:arial; color:#333333;">
	<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Corp ID</strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="TC[ID]" type="text" value="<?php echo $id?>" readonly style='background:#E0E0E0;border:1px #999 solid;padding:2px;width:200px; margin-left:2px;'></span>
        </td>
	</tr>
	<tr>
		<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>NPWP</strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="TC[NPWP]" type="text" value="<?php echo trim($npwp)?>" style="width:200px; margin-left:2px;" maxlength="15" 
        class="isi number nospace" label="NPWP" fix="15">
        </span></td>
	</tr>	
	<tr>
		<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Nama </strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="TC[NAMA]" type="text" value="<?php echo trim($nama)?>" style="width:200px; margin-left:2px;" maxlength="100" 
        class="isi" label="NAMA" >
        </span></td>
	</tr>	
	<tr>
		<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Alamat</strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="TC[ADDRESS]" type="text" value="<?php echo trim($alamat)?>" style="width:200px; margin-left:2px;" maxlength="100" 
        class="isi" label="ALAMAT">
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
