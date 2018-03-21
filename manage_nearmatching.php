<?php
if(in_array($_SESSION["priv_session"],array("3"))==false){
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
$near_matching = strFilter($_POST['near_matching']);
if($div=="update" && $near_matching!=""){		
	$sql = "select NILAI from tbldmMatching where upper(GROUPID)='".strtoupper(trim($_SESSION['grpID']))."'";
	$data = $conn->query($sql);
	if($data->next()){
		$sql = "update tbldmMatching set nilai='".str_replace(',','',$near_matching)."' where GROUPID='".strtoupper(trim($_SESSION['grpID']))."'";			
	}else{
		$sql = "insert into tbldmMatching (GROUPID,NILAI) VALUES ('".strtoupper(trim($_SESSION['grpID']))."','".str_replace(',','',$near_matching)."')";			
	}
	//echo $sql;
	$hasil = $conn->execute($sql);
	if($hasil){
		$_SESSION['respon'] = $bhs['Perubahan Near Matching'][$kdbhs];
		$_SESSION['near_matching']= $near_matching;		
	}
	echo "<script> window.location.href='".base_url."modul/nearmatching/setting';</script>";exit;
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
				echo "Near Matching Management";
			}		
			$conn->connect();
			$sql = "select NILAI from tbldmMatching where upper(GROUPID)='".strtoupper(trim($_SESSION['grpID']))."'";
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
<form method="post" action="<?php echo base_url?>modul/nearmatching/update" id="formNearmatching">
<input type="hidden" name="act" value="<?php echo($act);?>">
<input name="npwp" type="hidden" value="<?php echo(strtoupper($_SESSION["grpID"])); ?>">
<table cellpadding="0" cellspacing="0" width="100%" style="font-size:11px; font-family:arial; color:#333333;">
	<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Near Matching</strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
        <input name="near_matching" type="text" id="near_matching" size="50" value="<?php echo number_format($NILAI,0,'',',');?>" 
        onkeyup="javascript:numberFormat(this,',','','')" class="money" label="Near Matching" maxlength="15"></span></td>
	</tr>
	<tr>
		<td height="20"><span style="padding:6px 0px 6px 9px;"><strong>&nbsp;</strong></span></td>
		<td><span style="padding:6px 0px 6px 9px;">
        <button type="button" onclick="javascript:$('#formNearmatching').submit()" class="btn_1" style="width:60px">Save</button> 
        <button type="button" onclick="history.back()" class="btn_2" style="width:75px">Cancel</button></span></td>
	</tr>
</table>
</form>			
