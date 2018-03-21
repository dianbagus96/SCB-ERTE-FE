<?php
if(in_array($_SESSION["priv_session"],array("0","3"))==false){
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
$te = $_REQUEST['TE'];
if($div == "insert"){
	if(!is_array($te)){ echo "<script> window.location.href='".base_url."modul/email/view';</script>";exit;}	
	foreach($te as $a=>$b){ $TE[$a]=strFilter($b);}	
	$sql = "insert into TEMAIL(ID,NAMA,EMAIL,ROLE,CABANG,AKTIF) VALUES(CONVERT(VARCHAR,GETDATE(),120),'".cleanStr($TE['NAMA'])."','".$TE['EMAIL']."','".$TE['ROLE']."','".$TE['CABANG']."','".$TE['AKTIF']."')";
	$hasil = $conn->execute($sql);	
	
	$aktivitas = "Berhasil membuat email baru dengan alamat : ".$TE['EMAIL'];	
	audit($conn,$aktivitas);				
	$_SESSION['respon'] = "Anda telah Berhasil membuat Email baru";	
	echo "<script> window.location.href='".base_url."modul/email/view';</script>";exit;
}elseif($div == "update"){
	if(!is_array($te)){ echo "<script> window.location.href='".base_url."modul/email/view';</script>";exit;}
	foreach($te as $a=>$b){ $TE[$a]=strFilter($b);}		
	$TE['ID'] = $te['ID'];
	if($TE['ID']){	
		$sql = "update TEMAIL set NAMA='".cleanStr($TE['NAMA'])."',EMAIL='".$TE['EMAIL']."',ROLE ='".$TE['ROLE']."',CABANG='".$TE['CABANG']."',AKTIF='".$TE['AKTIF']."' where ID='".$TE['ID']."'";
		$hasil = $conn->execute($sql);
		if($hasil){
			
			$aktivitas = "Berhasil mengedit email dengan alamat : ".$TE['EMAIL'];
			
			audit($conn,$aktivitas);
			$_SESSION['respon'] = "Perubahan pada Email Berhasil";	
		}
	}	
	echo "<script> window.location.href='".base_url."modul/email/view';</script>";
	exit;
}elseif($div=='edit'){	
	if(trim($data = $_REQUEST['radiopanel'])==""){ echo "<script> window.location.href='".base_url."modul/email/view';</script>";exit;}	
	$id = split(";",$data);
	$conn->connect();
	$sql = "select * from TEMAIL where ID='".$id[0]."'";
	$data = $conn->query($sql); $data->next();
}
$conn->disconnect();
?>
<table cellpadding="0" cellspacing="0" width="85%">
	<tr>
		<td style="border-bottom:1px solid #D7D7D7;">
			<div style="padding-bottom:9px;">
			<span style="color:#1a68a4; font-size:16px; font-weight:bold;">
			<?php 			
			echo ($div == "edit")? "Edit Email" : "Add New Email";
			?>                            
			</span><br />
		  </div>
		</td>
	</tr>
</table>			
<form method="post" id="formEmail" action='<?php echo base_url."modul/email/".($div=='edit'?'update':'insert')?>'>            
<input type="hidden" name="div" value="<?php echo($div);?>">			
<input type="hidden" name="TE[ID]" value="<?php echo $data->get("ID");?>">						
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">Email Information</td>
	</tr>
</table>				
<table cellpadding="0" cellspacing="0" width="100%" style="font-size:11px; font-family:arial; color:#333333;">
	<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Nama <font color="#FF0000">*</font></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="TE[NAMA]" type="text" id="nama" value="<?php echo $data->get('NAMA')?>" style="width:200px; margin-left:2px;" maxlength="50" class="isi" label="Nama">
      </span></td>
	</tr>
	<tr>
		<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Email <font color="#FF0000">*</font></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="TE[EMAIL]" type="text" id="email" value="<?php echo $data->get('EMAIL')?>" style="width:200px; margin-left:2px;" maxlength="50" class="isi email nospace" label="Email">
      </span></td>
	</tr>
	<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Role <font color="#FF0000">*</font></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;">
		<span style="padding:6px 0px 6px 9px;">
			<select name="TE[ROLE]" id="priv">
			<?php
			$x1 = array('SSP','SSPCP','RTE');							
			for($i=0;$i<count($x1);$i++){
				if($data->get('ROLE') == $x1[$i])
					echo("<option value = \"". $x1[$i] ."\" selected >". $x1[$i] ."</option>");									
				else
					echo("<option value = \"". $x1[$i] ."\" >". $x1[$i] ."</option>");																		
			}								
			?>
			</select>
		 </span>
		</td>
	</tr>
	<tr>
		<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Cabang <font color="#FF0000">*</font></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">     					
		<?php
		if($_SESSION['priv_session']=='0'){
			$conn->connect();
			$sql = "select * from TCABANG";
			$dt = $conn->query($sql);	
		?>
			<select name="TE[CABANG]" id="cabang">
				<?php
				while($dt->next()){
					if($dt->get('KODE') == $data->get('CABANG'))
						echo("<option value = \"". $dt->get('KODE') ."\" selected>". $dt->get('NAMA') ."</option>");									
					else
						echo("<option value = \"". $dt->get('KODE') ."\">". $dt->get('NAMA') ."</option>");									
				}								
				?>
				</select>
		 <?php
		}elseif($_SESSION['priv_session']=='3'){							
			$conn->connect();
			$sql = "select * from TCABANG where KODE='".trim($_SESSION['brachsCode'])."'";
			$dt = $conn->query($sql);	
			$dt->next();	
			echo '<input type="text" value="'.$dt->get('NAMA').'" readonly style="background:#E0E0E0;border:1px #999 solid;padding:2px"><input type="hidden" value="'.$dt->get('KODE').'" name="TE[CABANG]" value="'.$dt->get('KODE').'">';
		}
		 ?>
		</span></td>
	</tr>
	<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Status <font color="#FF0000">*</font></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
		<input name="TE[AKTIF]" type="radio" value="1" <?php echo ($data->get('AKTIF')==1)? "checked" : "";?>>Aktif 
		<input name="TE[AKTIF]" type="radio" value="0" <?php echo ($data->get('AKTIF')==0)? "checked" : "";?>>Tidak Aktif</span></td>
	</tr>
	<tr>
		<td height="20"><span style="padding:6px 0px 6px 9px;"><strong>&nbsp;</strong></span></td>
		<td><span style="padding:6px 0px 6px 9px;">
        <button type="button" class="btn_1"  onclick="cekFormEmail('formEmail')" style="width:60px">Save</button> 
		 &nbsp;
		 <button type="button" onclick="history.back()" class="btn_2" style="width:75px">Cancel</button></span></td>
	</tr>
</table>
</form>														
<?php
$conn->disconnect();
?>
