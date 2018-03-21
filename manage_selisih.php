<?php
if(in_array($_SESSION["priv_session"],array("0","1"))==false){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}elseif(time()>$_SESSION['timeout_session']){	
	echo "<script> window.location.href='".base_url."err/7/".md5(7)."';</script>";exit;
}else{
	$_SESSION['timeout_session'] = TIMEOUT;
}

require_once("dbconn.php");
require_once("conf.php");
global $conn;

$conn->connect();
$nilai_selisih = strFilter($_POST['nilai']);
if($div=="update" && $nilai_selisih!=""){		
	$sql = "select NILAI from tbldmselisih ";
	$data = $conn->query($sql);
	if($data->next()){
		$sql = "update tbldmselisih set nilai='".str_replace(',','',$nilai_selisih)."'";			
	}else{
		$sql = "insert into tbldmselisih (LAST_UPDATE,NILAI) VALUES (SYSDATE,'".str_replace(',','',$nilai_selisih)."')";			
	}
	$hasil = $conn->execute($sql);
	if($hasil){
		$_SESSION['respon'] = $bhs['Perubahan Selisih'][$kdbhs];
		$_SESSION['near_matching']= $nilai_selisih;		
	}
	echo "<script> window.location.href='".base_url."modul/selisih/view';</script>";exit;
}
$conn->disconnect();
$readonly = "readonly style='background:#E0E0E0;border:1px #999 solid;padding:2px'";
?>

<table cellpadding="0" cellspacing="0" width="85%">
	<tr>
		<td style="border-bottom:1px solid #D7D7D7;">
			<div style="padding-bottom:9px;width:750px">
			<span style="color:#1a68a4; font-size:16px; font-weight:bold;">
			<?php
			$messageBox = "<div style='background:#E5EEF5;padding:5px;border:1px #CCC solid;'>
							<img src='".base_url."img/accept.png' style='border:none'> ".$_SESSION['respon']."
							</div>";
							
			if($_SESSION['respon']){
				echo $messageBox;	
				$_SESSION['respon']="";
			}else{
				echo "Difference Management";
			}		
			$conn->connect();
			$sql = "select NILAI from tbldmselisih ";
			$data = $conn->query($sql); $data->next();
			$NILAI = $data->get('NILAI');					
			?>									
			</span>
			</div>
		</td>
	</tr>
</table>


<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">Edit Near Matching</td>
	</tr>
</table>					
<form method="post" action="<?php echo base_url?>modul/selisih/update" id="formSelisih">
<input type="hidden" name="act" value="<?php echo($act);?>">
<table cellpadding="0" cellspacing="0" width="100%" style="font-size:11px; font-family:arial; color:#333333;">
	<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Difference (IDR)</strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="nilai" type="text" id="nilai" size="50" value="<?php echo number_format($NILAI,0,'',',');?>" 
        onkeyup="javascript:numberFormat(this,',','','')" class="money" label="Nilai Selisih" maxlength="15"></span></td>
	</tr>
	<tr>
		<td height="20"><span style="padding:6px 0px 6px 9px;"><strong>&nbsp;</strong></span></td>
		<td><span style="padding:6px 0px 6px 9px;">
        <button type="button" onclick="javascript:$('#formSelisih').submit()" class="btn_1" style="width:60px">Save</button> 
        <button type="button" onclick="history.back()" class="btn_2" style="width:75px">Cancel</button></span></td>
	</tr>
</table>
</form>			
