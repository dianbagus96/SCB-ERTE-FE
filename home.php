<?php
if($_SESSION['verified']==""){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;	
}elseif(time()>$_SESSION['timeout_session']){	
	echo "<script> window.location.href='".base_url."err/7/".md5(7)."';</script>";exit;
}else{
	$_SESSION['timeout_session'] = TIMEOUT;
}
$priv = $_SESSION['priv_session'];
?>
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="border-bottom:1px solid #D7D7D7;" width="200px;">
			<div style="padding-bottom:9px;">
			<span style="color:#005D9A; font-size:21px; font-weight:bold;"><?php echo $bhs['Welcome'][$kdbhs]?></span><br />
			<span style="color:#666666; font-size:11px; font-weight:bold;"><?php echo $bhs['Login Terakhir'][$kdbhs]." "; echo date(d).'-'.date(m).'-'.date(Y).' ';?> 
			<?php echo date('h:m');?></span>						
			</div>
		</td>
	</tr>
</table>
<div style="height:18px;">&nbsp;</div>
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="background-image:url(<?php echo base_url?>img/tab2.png); width:529px; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;" colspan='2'>User Information</td>
	</tr>
</table>
<table cellpadding="0" cellspacing="0" width="100%" style="font-size:11px; font-family:arial; color:#333333;">
	<tr>
		<td height="20" width="200" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>User ID</strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">: <?php echo $_SESSION["uid_session"]; ?></span></td>
	</tr>
	<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Email</strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">: <?php echo $_SESSION["email_session"]; ?></span></td>
	</tr>
	<tr>
      <td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Role</strong></span></td>
	  <td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">:
	    <?php 						
		$privilege = array("0"=>"Super Administrator","1"=>"Administrator SCB","2"=>"Administrator SCB App","3"=>"User Administrator","4"=>"Operator","5"=>"Supervisor",
							"6"=>"PIC of ".$_SESSION['pic_of_session']);
		echo $privilege[$priv];
		function akses_word($akses){
			$str = "";
			$str .= (substr($akses,0,1)==1)? "SSPCP, ":"";
			$str .= (substr($akses,1,1)==1)? "SSP, ":"";
			$str .= (substr($akses,2,1)==1)? "Upload SSP, ":"";
			$str .= (substr($akses,3,1)==1)? "RTE, ":"";		 
			return substr($str,0,strlen($str)-2);
		}
		?>
      </span></td>
  </tr>
  	
</table>
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="background-image:url(<?php echo base_url?>img/tab2.png); width:529px; height:7px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;" colspan='2'></td>
	</tr>
</table>
<?php
if($priv!="0"){
?>
<div style="height:18px;">&nbsp;</div>
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="background-image:url(<?php echo base_url?>img/tab2.png); width:529px; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;" colspan='2'><?php echo $bhs['Profil Perusahaan'][$kdbhs]?></td>
	</tr>
</table>
<table cellpadding="0" cellspacing="0" width="100%" style="font-size:11px; font-family:arial; color:#333333;">
	<tr class="mix">
		<td height="20" width="200"><span style="padding:6px 0px 6px 9px;"><strong><?php echo $bhs['Nama Perusahaan'][$kdbhs]?></strong></span></td>
		<td>
		<span style="padding:6px 0px 6px 9px;"> : 
		<?php 					
		echo($_SESSION["nmcomp_session"]);		
		?>					
		</span>
	   </td>
	</tr>	
    <?php
	$x=0;
    $npwp_all = explode(",",$_SESSION['npwp_many_session']);
	foreach($npwp_all as $n){
		if($x==0){	
		?>
		<tr>
			<td height="20"><span style="padding:6px 0px 6px 9px;"><strong><em>NPWP</em></strong></span></td>
			<td><span style="padding:6px 0px 6px 9px;">: <?php echo str_replace("'","",$n);?></span></td>
		</tr>
    	<?php
		}else{?>
		<tr>
			<td height="20"><span style="padding:6px 0px 6px 9px;">&nbsp;</td>
			<td><span style="padding:6px 0px 6px 9px;">: <?php echo str_replace("'","",$n);?></span></td>
		</tr>	
		<?php
        }
		$x++;
	}
	?>
    <tr class="mix">
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong><?php echo $bhs['Alamat'][$kdbhs]?></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">: <?php echo($_SESSION['ADDRESS_SES']);?></span></td>
	</tr>
	<tr>
		<td height="20"><span style="padding:6px 0px 6px 9px;"><strong><?php echo $bhs['Kota'][$kdbhs]?></strong></span></td>
		<td><span style="padding:6px 0px 6px 9px;">: <?php echo($_SESSION['CITY_ses']);?></span></td>
	</tr>
	<tr class="mix">
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong><?php echo $bhs['Kode Pos'][$kdbhs]?></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">: <?php echo($_SESSION['zipcode']);?></span></td>
	</tr>
</table>

<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="background-image:url(<?php echo base_url?>img/tab2.png); width:529px; height:7px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;" colspan='2'></td>
	</tr>
</table>
<div style="height:18px;">&nbsp;</div>
	<?php
	if(substr($_SESSION['AKSES'],1,1)=='1' && ($priv=='2'||$priv=='4')){ #just for checker ssp & super
	?>
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td style="background-image:url(<?php echo base_url?>img/tab2.png); width:529px; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;" colspan='2'>Pending Task</td>
			</tr>
		</table>
		<table cellpadding="0" cellspacing="0" width="100%" style="font-size:11px; font-family:arial; color:#333333;">
			<tr>
				<td width="114" rowspan="5"><img src="<?php echo base_url."images/kertas.png"?>" style="width:100px;border:none" /></td>
				<td height="20" width="258" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>SSP New</strong></span></td>
				<td width="940" style="border-bottom: 1px solid #D7D7D7;">
				<span style="padding:6px 0px 6px 9px;">: <?php echo "<a href='".base_url."modul/ssp/new' style='color:#009015;font-weight:bold'>".$jml_new."</a>";?></a></span>	   </td>
			</tr>
			<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
				<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>SSP Validation</strong></span></td>
				<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">: <?php echo "<a href='".base_url."modul/ssp/valid' style='color:#009015;font-weight:bold'>".$jml_valid."</a>";?></a></span></td>
			</tr>
			<tr>
				<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>SSP Reception</strong></span></td>
				<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">: <?php echo "<a href='".base_url."modul/ssp/terima' style='color:#009015;font-weight:bold'>".$jum_reception."</a>";?></span></td>
			</tr>
			<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
				<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>SSPCP Status</strong></span></td>
				<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">: <?php echo "<a href='".base_url."modul/sspcp/status' style='color:#009015;font-weight:bold'>".$jum_sspcp."</a>";?></a></span></td>
			</tr>
			<tr>
				<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>PIB Status</strong></span></td>
				<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">: <?php echo "<a href='".base_url."modul/pib/status' style='color:#009015;font-weight:bold'>".$jum_sspcp."</a>";?></span></td>
			</tr>		
		</table>
		
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td style="background-image:url(<?php echo base_url?>img/tab2sx.png); width:529px; height:7px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;" colspan='2'></td>
			</tr>
		</table>
		<?php
	}elseif(substr($_SESSION['AKSES'],1,1)=='1' && ($priv=='1'||$priv=='4')){ #just for maker & super ssp ?> 
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td style="background-image:url(<?php echo base_url?>img/tab2.png); width:529px; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;" colspan='2'>Activity Summary</td>
			</tr>
		</table>
		<table cellpadding="0" cellspacing="0" width="100%" style="font-size:11px; font-family:arial; color:#333333;">
			<tr>
				<td width="114" rowspan="3"><img src="<?php echo base_url."images/jabat.png"?>" style="width:100px" /></td>
				<td height="20" width="258" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>SSP New</strong></span></td>
				<td width="940" style="border-bottom: 1px solid #D7D7D7;">
				<span style="padding:6px 0px 6px 9px;">: <?php echo "<a href='".base_url."modul/ssp/new' style='color:#009015;font-weight:bold'>".$jml_new."</a>";?></a></span>	   </td>
			</tr>
			<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
				<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>SSP Validation</strong></span></td>
				<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">: <?php echo "<a href='".base_url."modul/ssp/valid' style='color:#009015;font-weight:bold'>".$jml_valid."</a>";?></a></span></td>
			</tr>
			<tr>
				<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>SSP Reception</strong></span></td>
				<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">: <?php echo "<a href='".base_url."modul/ssp/terima' style='color:#009015;font-weight:bold'>".$jum_reception."</a>";?></span></td>
			</tr>	
		</table>
		
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td style="background-image:url(<?php echo base_url?>img/tab2sx.png); width:529px; height:7px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;" colspan='2'></td>
			</tr>
		</table>
	<?php
	}
}if(substr($_SESSION["AKSES"],3,1)=="1" && in_array($priv,array("0","3","1","2"))==false){
if ($priv == "5"){	
?>
<td style="padding-left:18px; width:200px; font-family:arial;" valign="top" width="20%">
<table cellpadding="0" cellspacing="0" width="400">
	<tr>
		<td style="border-bottom:1px solid #D7D7D7;" width="200px;">
			<div style="padding-bottom:9px;">
			<span style="color:#86C533; font-size:21px; font-weight:bold;"><?php echo $bhs['Ringkasan'][$kdbhs]?></span><br />
			<span style="color:#666666; font-size:11px; font-weight:bold;"><?php echo $bhs['Ringkasan Transaksi'][$kdbhs]?></span>						
			</div>
		</td>
	</tr>
</table>
<div style="height:18px;">&nbsp;</div>
<table cellpadding="0" cellspacing="0" width="400">
	<tr>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:20px; height:22px;">&nbsp;</td>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:90%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;"><?php echo $bhs['Ringkasan Dana Masuk'][$kdbhs]?></td>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:20px; height:22px;">&nbsp;</td>
	</tr>
</table>
<table cellpadding="0" cellspacing="0" width="400" style="font-size:11px; font-family:arial; color:#333333;">
	<tr>
		<td height="20" width="400" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong><a href="<?php echo base_url?>modul/danamasuk/baru" style="text-decoration:none;color:#005D9A;;">&#8250; <?php echo $bhs['Dana Masuk Baru'][$kdbhs]; echo " ( Item : $jml_neweks )"; ?></a></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">&nbsp;</span></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong><a href="<?php echo base_url?>modul/danamasuk/ekspor" style="text-decoration:none;color:#005D9A;">&#8250; <?php echo $bhs['Ekspor'][$kdbhs]; echo " ( Item : $jml_ekspor )"; ?></a></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">&nbsp;</span></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong><a href="<?php echo base_url?>modul/danamasuk/nonekspor" style="text-decoration:none;color:#005D9A;">&#8250; <?php echo $bhs['Non Ekspor'][$kdbhs]; echo " ( Item : $jml_nonekspor )"; ?></a></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">&nbsp;</span></td>
		<td>&nbsp;</td>
	</tr>
	
	<tr>
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong><a href="<?php echo base_url?>modul/danamasuk/uangmuka" style="text-decoration:none;color:#005D9A;">&#8250; <?php echo $bhs['Uang Muka'][$kdbhs]; echo " ( Item : $jml_uangmuka )"; ?></a></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">&nbsp;</span></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong><a href="<?php echo base_url?>modul/danamasuk/terlaporkan" style="text-decoration:none;color:#005D9A;">&#8250; <?php echo $bhs['Dana Terlaporkan'][$kdbhs]; echo " ( Item : $jml_terpakai )"; ?></a></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">&nbsp;</span></td>
		<td>&nbsp;</td>
	</tr>
</table>
<table cellpadding="0" cellspacing="0" width="400">
	<tr>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:7px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;"></td>
	</tr>
</table>			
<div style="height:18px;">&nbsp;</div>
<table cellpadding="0" cellspacing="0" width="400">
	<tr>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:20px; height:22px;">&nbsp;</td>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:90%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;"><?php echo $bhs['Ringkasan PEB'][$kdbhs]?></td>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:20px; height:22px;">&nbsp;</td>
	</tr>
</table>
<table cellpadding="0" cellspacing="0" width="400" style="font-size:11px; font-family:arial; color:#333333;">
	<tr>
		<td height="20" width="400" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong><a href="<?php echo base_url?>modul/peb/baru" style="text-decoration:none;color:#009015;">&#8250; <?php echo $bhs['PEB Baru'][$kdbhs]; echo " ( Item : $jml_newpeb )"; ?></a></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">&nbsp;</span></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong><a href="<?php echo base_url?>modul/peb/plus" style="text-decoration:none;color:#009015;">&#8250; PEB 90 +<?php echo "( Item : $jml_plus )"; ?></a></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">&nbsp;</span></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong><a href="<?php echo base_url?>modul/peb/terlaporkan" style="text-decoration:none;color:#009015;">&#8250; <?php echo $bhs['PEB Terlaporkan'][$kdbhs]; echo " ( Item : $jml_pakai )"; ?></a></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">&nbsp;</span></td>
		<td>&nbsp;</td>
	</tr>					
</table>                
<table cellpadding="0" cellspacing="0" width="400">
	<tr>
		<td style="background:url(<?php echo base_url?>img/tab1.png); width:100%; height:7px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;"></td>
	</tr>
</table>
<div style="height:18px;">&nbsp;</div>
<table cellpadding="0" cellspacing="0" width="400">
	<tr>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:20px; height:22px;">&nbsp;</td>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:90%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;"><?php echo $bhs['Ringkasan RTE'][$kdbhs]?></td>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:20px; height:22px;">&nbsp;</td>
	</tr>
</table>
<table cellpadding="0" cellspacing="0" width="400" style="font-size:11px; font-family:arial; color:#333333;">
	<tr>
		<td height="20" width="400" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong><a href="<?php echo base_url?>modul/rte/baru" style="text-decoration:none;color:#005D9A;;">&#8250; <?php echo $bhs['RTE Baru'][$kdbhs]; echo " ( Item : $jml_newrte )"; ?></a></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">&nbsp;</span></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong><a href="<?php echo base_url?>modul/rte/terkirim" style="text-decoration:none;color:#005D9A;">&#8250; <?php echo $bhs['RTE Terkirim'][$kdbhs]; echo " ( Item : $jml_sent )"; ?></a></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">&nbsp;</span></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong><a href="<?php echo base_url?>modul/rte/pending" style="text-decoration:none;color:#005D9A;">&#8250; RTE Pending <?php echo "( Item : $jml_pending )"; ?></a></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">&nbsp;</span></td>
		<td>&nbsp;</td>
	</tr>
	
</table>
<table cellpadding="0" cellspacing="0" width="400">
	<tr>
	  <td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:7px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;"></td>
	</tr>
</table>
<?php
} elseif( $priv == "4"){
?>

<td style="padding-left:18px; width:200px; font-family:arial;" valign="top" width="20%">
<table cellpadding="0" cellspacing="0" width="400">
	<tr>
		<td style="border-bottom:1px solid #D7D7D7;" width="200px;">
			<div style="padding-bottom:9px;">
			<span style="color:#86C533; font-size:21px; font-weight:bold;"><?php echo $bhs['Ringkasan'][$kdbhs]?></span><br />
			<span style="color:#666666; font-size:11px; font-weight:bold;"><?php echo $bhs['Ringkasan Transaksi'][$kdbhs]?></span>						
			</div>
		</td>
	</tr>
</table>
<div style="height:18px;">&nbsp;</div>
<table cellpadding="0" cellspacing="0" width="400">
	<tr>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:20px; height:22px;">&nbsp;</td>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:90%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;"><?php echo $bhs['Ringkasan PEB'][$kdbhs]?></td>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:20px; height:22px;">&nbsp;</td>
	</tr>
</table>
<table cellpadding="0" cellspacing="0" width="400" style="font-size:11px; font-family:arial; color:#333333;">
	<tr>
		<td height="20" width="400" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong><a href="<?php echo base_url?>modul/peb/baru" style="text-decoration:none;color:#009015;">&#8250; <?php echo $bhs['PEB Baru'][$kdbhs]; echo " ( Item : $jml_newpeb )"; ?></a></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">&nbsp;</span></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong><a href="<?php echo base_url?>modul/peb/plus" style="text-decoration:none;color:#009015;">&#8250; PEB 90 +<?php echo "( Item : $jml_plus )"; ?></a></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">&nbsp;</span></td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong><a href="<?php echo base_url?>modul/peb/terlaporkan" style="text-decoration:none;color:#009015;">&#8250; <?php echo $bhs['PEB Terlaporkan'][$kdbhs]; echo " ( Item : $jml_pakai )"; ?></a></strong></span></td>
		<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">&nbsp;</span></td>
		<td>&nbsp;</td>
	</tr>					
</table> 
</td>
<?php
}					
}
?>
</td>

		