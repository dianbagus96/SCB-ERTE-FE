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
global $conn;
$conn->connect();

$news = htmlspecialchars(strFilter($_POST['news']));
if($div=="update"){
	if(trim($news)==""){echo "<script> window.location.href='".base_url."modul/news/setting';</script>";exit;}
	$from = $_POST['tglfrom'];
	$to = $_POST['tglto'];
	$sql = "update tnews set news='".$news."' , datefrom=TO_DATE('".$from."','DD-MM-YYYY'), dateto = TO_DATE('".$to."','DD-MM-YYYY')";
	$conn->execute($sql);
	$_SESSION['respon'] = "Berhasil mengupdate Pengumuman";
	echo "<script> window.location.href='".base_url."modul/news/setting';</script>";exit;
}
$sql = "select  NEWS,TO_CHAR(DATEFROM,'DD-MM-YYY') AS DATEFROM,TO_CHAR(DATETO,'DD-MM-YYYY') AS DATETO from tnews WHERE ROWNUM =1 ORDER BY dateto DESC ";
$datanews = $conn->query($sql);
if($datanews->next()){
	$news = $datanews->get('NEWS');
	$tglfrom = $datanews->get('DATEFROM');
	$tglto = $datanews->get('DATETO');	
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
				echo "Announcement Setting<br />";
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
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">Announcement</td>
	</tr>
</table>					
<form method="post" action="<?php echo base_url?>modul/news/update" name="newsform">
<table cellpadding="0" cellspacing="0" width="100%" style="font-size:11px; font-family:arial; color:#333333;">
	<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
	  <td width="52" height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>From</strong></span></td>
	  <td width="354" style="border-bottom: 1px solid #D7D7D7;">
	  <span style="padding:6px 0px 6px 9px;">
		<input name="tglfrom" type="text" id="tglfrom" value="<?php echo($tglfrom);?>" size="10"  readonly="readonly" />
	  </span>
	  <a href="javascript:void(0)" onclick="if(self.gfPop)gfPop.fPopCalendar(document.newsform.tglfrom); return false;">
	  	<img name="popcal" align="absmiddle" src="<?php echo base_url?>img/calbtn.gif" alt="Pilih tanggal" width="34" height="22" border="0" id="gbrClndr" />
	  </a>
	  </td>
	  <td width="30" height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>To</strong></span></td>
	  <td width="893" style="border-bottom: 1px solid #D7D7D7;">
	  <span style="padding:6px 0px 6px 9px;">
		<input name="tglto" type="text" id="tglto" value="<?php echo($tglto);?>" size="10"  readonly="readonly" />
	  </span>
	  <a href="javascript:void(0)" onclick="if(self.gfPop)gfPop.fPopCalendar(document.newsform.tglto); return false;">
	  	<img name="popcal" align="absmiddle" src="<?php echo base_url?>img/calbtn.gif" alt="Pilih tanggal" width="34" height="22" border="0" id="gbrClndr" />
	  </a>
	  </td>
    </tr>		 
	<tr>
		<td height="20" colspan="4"><br><span style="padding:6px 0px 6px 9px;">			
			<strong>Announcement Text : </strong></span></td>		
	</tr>
	<tr>
		<td style="border-bottom: 1px solid #D7D7D7;" colspan="4">
		<span style="padding:6px 0px 6px 9px;">
		<div>
		<textarea style="width:500px; padding:5px" rows="5" name="news"><?php echo $news?></textarea>
		</div>
		</span></td>
	</tr>
	<tr>
		<td colspan="4"><span style="padding:6px 0px 6px 9px;"><input type="hidden" name="Submit" id="Submit"/>
		<button type="submit" class="btn_1" style="width:63px">Update</button>&nbsp; 
		<button type="button" onclick="history.back()" class="btn_2" style="width:75px">Cancel</button>
		</span></td>
	</tr>
</table>
</form>
<iframe width=174 height=189 name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="<?php echo base_url?>ipopeng.htm" scrolling="no" 
		frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;"></iframe>
