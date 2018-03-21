<?php
if(in_array($_SESSION["priv_session"],array("0"))==false){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}elseif(time()>$_SESSION['timeout_session']){	
	echo "<script> window.location.href='".base_url."err/7/".md5(7)."';</script>";exit;
}else{
	$_SESSION['timeout_session'] = TIMEOUT;
}
	
require_once("dbconn.php");
global $conn;
$conn->connect();
$pros = $_REQUEST['Submit'];
$cb = $_REQUEST['CB'];
if($div == "insert"){
	if(!is_array($cb)){ echo "<script> window.location.href='".base_url."modul/cabang/view';</script>";exit;}	
	foreach($cb as $a=>$b){ $CB[$a]=strFilter($b);}		
	$sql = "insert into TCABANG (KODE,NAMA) VALUES('".$CB['KODE']."','".cleanStr($CB['NAMA'])."')";
	$hasil = $conn->execute($sql);
	if($hasil){
		$_SESSION['respon'] = "Anda telah berhasil membuat Cabang baru";			
		
		$aktivitas = "Berhasil membuat cabang baru dengan nama : ".strFilter($CB['NAMA']);
		audit($conn,$aktivitas);				
	}
	echo "<script> window.location.href='".base_url."modul/cabang/view';</script>";exit;
}elseif($div == "update"){
	if(!is_array($cb)){ echo "<script> window.location.href='".base_url."modul/cabang/view';</script>";exit;}	
	foreach($cb as $a=>$b){ $CB[$a]=strFilter($b);}		
	if($CB['ID']){
		$sql = "update TCABANG set KODE='".$CB['KODE']."',NAMA='".strFilter($CB['NAMA'])."' where ID='".$CB['ID']."'";		
		$hasil = $conn->execute($sql);
	}	
	if($hasil){
		$_SESSION['respon'] = "Perubahan pada Cabang Berhasil";	
		
		$aktivitas = "Berhasil mengedit cabang dengan nama : ".strFilter($CB['NAMA']);
		audit($conn,$aktivitas);				
	}
	echo "<script> window.location.href='".base_url."modul/cabang/view';</script>";exit;
}
$conn->disconnect();
?>
<table cellpadding="0" cellspacing="0" width="85%">
	<tr>
		<td style="border-bottom:1px solid #D7D7D7;">
			<div style="padding-bottom:9px;">
			<span style="color:#1a68a4; font-size:16px; font-weight:bold;">
			<?php 
			if($div=="edit"){
				if(trim($data=$_REQUEST['radiopanel'])==""){ echo "<script> window.location.href='".base_url."modul/cabang/view';</script>";exit;}	
				$id = split(";",$data);
				$conn->connect();
				$sql = "select * from TCABANG where ID='".$id[0]."'";
				$data = $conn->query($sql); $data->next();
			}			
			echo ($div == "edit")? "Edit Cabang" : "Add New Cabang";
			?>                            
			</span><br />
		  </div>
		</td>
	</tr>
</table>			
<form method="post" id="formCabang" action='<?php echo base_url."modul/cabang/".($div=='edit'?'update':'insert')?>'>            
<input type="hidden" name="CB[ID]" value="<?php echo $data->get("ID");?>">						
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">Cabang  Information</td>
	</tr>
</table>				
<table cellpadding="0" cellspacing="0" width="100%" style="font-size:11px; font-family:arial; color:#333333;">
	<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Kode Cabang  <font color="#FF0000">*</font></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="CB[KODE]" type="text" id="kode" value="<?php echo $data->get('KODE')?>" style="width:200px; margin-left:2px;" maxlength="6"
        class="isi number nospace" label="Kode Cabang" fix="6">
        </span></td>
	</tr>
	<tr>
		<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Nama Cabang <font color="#FF0000">*</font></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="CB[NAMA]" type="text" id="nama" value="<?php echo $data->get('NAMA')?>" style="width:200px; margin-left:2px;"  maxlength="75"
        class="isi" label="Nama Cabang">
        </span></td>
	</tr>
	<tr>
		<td height="20"><span style="padding:6px 0px 6px 9px;"><strong>&nbsp;</strong></span></td>
		<td><span style="padding:6px 0px 6px 9px;">
        <button type="button" class="btn_1"  onclick="cekFormCabang('formCabang')" style="width:60px">Save</button> 
		 &nbsp;
		 <button type="button" onclick="history.back()" class="btn_2" style="width:75px">Cancel</button></span></td>
	</tr>
</table>
</form>													
