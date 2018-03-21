<?php
if(in_array($_SESSION["priv_session"],array("5","4"))==false || substr($_SESSION["AKSES"],3,1)!="1"){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}elseif(time()>$_SESSION['timeout_session']){	
	echo "<script> window.location.href='".base_url."err/7/".md5(7)."';</script>";exit;
}else{
	$_SESSION['timeout_session'] = TIMEOUT;
}
require_once("dbconn.php");
$conn->connect();
$type_form = "Save";
if($div=="insert"){
	if(trim($_POST["car"])==""){ echo "<script> window.location.href='".base_url."modul/peb/baru';</script>";exit;}	
		
	foreach($_POST as $a=>$b){
		$_POST[$a] = str_replace("\\","",$b);	
	}
	$car 	= strFilter($_POST['car']);
	$nama 	= strFilter($_POST['eksportir']);
	$alamat = strFilter($_POST['alamat']);
	$kpbc 	= strFilter($_POST['kpbc']);
	$valuta = strFilter(strtoupper($_POST['valuta']));
	$fob	= str_replace(",","",strFilter($_POST['fob']));
	$nopeb	= strFilter($_POST['nopeb']);
	$tglpebin = trim($_POST['tglpeb']);
	$npwp = trim($_POST['npwp']);
	$tglpeb = ($tglpebin=="")? "" : "TO_DATE('$tglpebin','DD-MM-YYYY')";
								  
	$sql = "select CAR from tbldmpeb where CAR='".trim($car)."'";
	$dtcar = $conn->query($sql);
	
	if(!$dtcar->next()){
		$sql = "insert into tbldmpeb(CAR,NPWP,Nama_Eksportir,KPBC,NO_PEB,TGL_PEB,FOB,Alamat_Eksportir,flag_used,valuta,Source,DTCREATED,GROUPID,CREATEDBY) 
		values('$car','$npwp','$nama','$kpbc','$nopeb',$tglpeb,'$fob','$alamat','0','$valuta','2',SYSDATE,'".strtoupper($_SESSION['grpID'])."','".$_SESSION['uid_session']."')";
		$data = $conn->execute($sql);	
		$invoice = $_POST['Inv'];
		$write_txt .= "PEB|".$car."|".$npwp."|".$nama."|".$alamat."|".$kpbc."|".$nopeb."|".$tglpebin."|".$valuta."|".$fob."|".$_SESSION['uid_session']."|".date('d-m-Y')."|||0\r\n";
		$inv = "";
		$no=1;
		$indexTanggal = 1;
		foreach($invoice as $row){
			$inv = strFilter($row);
			if($inv!=""){			
				$tglpebdok = (trim($_POST['tglInv_'.$indexTanggal])=="")? "NULL":"TO_DATE('".trim($_POST['tglInv_'.$indexTanggal])."','DD-MM-YYYY')";
				$sql = "insert into tblPebDok(car,kddok,nodok,id,tglDok) values('$car','380','$inv',$no,$tglpebdok)";
				$data = $conn->execute($sql);
				$write_txt .= "INV|".$car."|".$inv."|".trim($_POST['tglInv_'.$indexTanggal])."|380|".$_SESSION['uid_session']."|".date('d-m-Y')."||\r\n";
				$no++;
			}
			$indexTanggal++;
		}	  		
		writetxt($write_txt,'DOKPEB.',$_SESSION['grpID']);
		require_once("dbconn.php");
		$conn->connect();
		
		$aktivitas = "Berhasil membuat PEB baru dengan CAR : $car";
		audit($conn,$aktivitas);		
		$_SESSION['respon'] = 'Anda telah Berhasil membuat PEB baru';	
		$_SESSION['statusRespon'] = 1;
	}else{
		$_SESSION['respon'] = 'Input PEB Gagal! CAR PEB sudah tersedia sebelumnya';	
		$_SESSION['statusRespon'] = 0;
	}	
	
	echo "<script> window.location.href='".base_url."modul/peb/baru';</script>";exit;
} else if($div=="editexe"){
	if(trim($_POST["car"])==""){ echo "<script> window.location.href='".base_url."modul/peb/baru';</script>";exit;}	
	foreach($_POST as $a=>$b){
		$_POST[$a] = str_replace("\\","",$b);	
	}
	$idpeb 	= strFilter($_POST['idpeb']);
	$car 	= strFilter($_POST['car']);
	$nama 	= strFilter($_POST['eksportir']);
	$alamat = strFilter($_POST['alamat']);
	$kpbc 	= strFilter($_POST['kpbc']);
	$valuta = strFilter($_POST['valuta']);
	$valuta = strFilter($_POST['valuta']);
	$fob	= str_replace(",","",strFilter($_POST['fob']));
	$nopeb	= strFilter($_POST['nopeb']);
	$tglpebin = trim($_POST['tglpeb']);
	$npwp = trim($_POST['npwp']);
	$tglpeb = ($tglpebin=="")? "NULL" : "TO_DATE('$tglpebin','DD-MM-YYYY')";
	
	$sql = "update tbldmpeb set CAR='$car',NPWP='$npwp',NAMA_EKSPORTIR='$nama',KPBC='$kpbc',NO_PEB='$nopeb',TGL_PEB=$tglpeb,
			FOB=$fob,ALAMAT_EKSPORTIR='$alamat',VALUTA='$valuta', UPDATEBY = '".$_SESSION['uid_session']."', DTUPDATE = SYSDATE WHERE idPEB='$idpeb'";			 
	$data = $conn->execute($sql);
	$write_txt .= "PEB|".$car."|".$npwp."|".$nama."|".$alamat."|".$kpbc."|".$nopeb."|".$tglpebin."|".$valuta."|".$fob."|||".$_SESSION['uid_session']."|".date('d-m-Y')."|0\r\n";
	$sql = "delete from tblPebDok where CAR='$car' and kddok='380'";
	$conn->execute($sql);
	$invoice = $_POST['Inv'];
	$inv = "";
	$no=1;
	$indexTanggal = 1;
	//$write_txt .= "DELINV|".$car."\r\n";
	foreach($invoice as $row){
		$inv = strFilter($row);
		if($inv!=""){			
			$sql = "insert into tblPebDok(car,kddok,nodok,id,tgldok) values('$car','380','$inv',$no,TO_DATE('".$_POST['tglInv_'.$indexTanggal]."','DD-MM-YYYY'))";
			$data = $conn->execute($sql);	
			$write_txt .= "INV|".$car."|".$inv."|".trim($_POST['tglInv_'.$indexTanggal])."|380|||".$_SESSION['uid_session']."|".date('d-m-Y')."\r\n";
			$no++;
		}
		$indexTanggal++;
	}	 
	
	require_once("dbconn.php");
	$conn->connect();
	if (strlen($write_txt)>0 ){
		writetxt($write_txt,'DOKPEB.',$_SESSION['grpID']);
		$write_txt='';
	}
	$aktivitas = "Berhasil melakukan pengeditan PEB dengan CAR : $car";
	audit($conn,$aktivitas);
	$_SESSION['respon'] = "Perubahan pada PEB Berhasil";	
	$_SESSION['statusRespon'] = 1;
	
	echo "<script> window.location.href='".base_url."modul/peb/baru';</script>";exit;
}
if($div=="edit"){
	if(!is_array($_POST['cbx'])){ echo "<script> window.location.href='".base_url."modul/peb/baru';</script>";exit; }
	$sql = "select IDPEB,NPWP,CAR,NAMA_EKSPORTIR,ALAMAT_EKSPORTIR,KPBC,NO_PEB,TO_CHAR(TGL_PEB,'DD-MM-YYYY') AS TGL_PEB,VALUTA,FOB from tbldmpeb WHERE idPEB='".$_POST['cbx'][0]."' and source != '1'";
	$datapeb = $conn->query($sql);$datapeb->next();
	if ($datapeb->size() != 0){
		$idpeb = $datapeb->get('IDPEB');
		$car = $datapeb->get('CAR');
		$npwp_eks = $datapeb->get('NPWP');
		$eksportir = $datapeb->get('NAMA_EKSPORTIR');
		$alamat = $datapeb->get('ALAMAT_EKSPORTIR');
		$kpbc = $datapeb->get('KPBC');
		$nopeb = trim($datapeb->get('NO_PEB'));
		$tglpeb = $datapeb->get('TGL_PEB');
		$valuta = $datapeb->get('VALUTA');
		$fob = $datapeb->get('FOB');
		$sql = "select NODOK,TO_CHAR(TGLDOK,'DD-MM-YYYY') AS TGLDOK from tblPEBDok where CAR='$car'";
		$datapebdok = $conn->query($sql);		
	}else{
		$_SESSION['respon'] = 'Edit PEB Gagal! Source PEB berasal dari upload';	
		$_SESSION['statusRespon'] = 0;
		echo "<script>window.location.href='".base_url."modul/peb/baru';</script>";exit;       
	}
	 
		
	
}
$readonly=" readonly style='background:#E0E0E0;border:1px #999 solid;padding:2px'";
?>
	<form name="frmPEB" id="frmPEB" method="post" action="<?php echo base_url."modul/peb/".($div=='create'?'insert':'editexe');?>"> 
	<input type="hidden" name="idpeb" value="<?php echo $idpeb?>" />
	<table cellpadding="0" cellspacing="0" style="width:100%" id="cekssp">
		<tr>
			<td style="border-bottom:1px solid #D7D7D7;">
				<div style="padding-bottom:9px;">
				<span style="color:#1a68a4; font-size:16px; font-weight:bold;">
				<?php 	
					$messageBox = "<div style='background:#E5EEF5;padding:5px;border:1px #CCC solid;'>
								<img src='".base_url."img/accept.png' style='border:none'> ".$_SESSION['respon']."
								</div>";
					if($_SESSION['respon']!=""){
						echo $messageBox;
						$_SESSION['respon']="";
					}else{
						echo ($div=='edit')? "Edit PEB<br />" : $bhs['Input PEB'][$kdbhs]."<br />"; 
					}
				?>
				</span>
				</div>
			</td>
		</tr>
	</table>
	<?php
   if($_SESSION['respon']!=""){
	   echo "<button type='button' class='btn_1' onClick=javascript:location.href='".base_url."modul/ssp/new'>Back</button>";
	   $_SESSION['respon']="";
   }else{
	?>
	<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
			<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">Data PEB</td>
		</tr>
	</table>
	<table cellpadding="0" cellspacing="0" style="font-size:11px; font-family:arial; color:#333333;width:100%">
		<tr>
			<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>CAR / No Aju <font color="#FF0000">*</font></strong></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;">
			<span style="padding:6px 0px 6px 9px;">
				<input name="car" type="text" id="car" maxlength="26" size="30px;" <?php echo ($div=="edit")? $readonly : "";?> 
                value="<?php echo($car);?>" onkeyup="$(this).removeAttr('style');$('font#warningCAR').html('')" class="isi number nospace" label="CAR / No Aju" fix="26">
			</span><font id='warningCAR'></font>			</td>
		</tr>
		<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
			<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>NPWP</strong> <font color="#FF0000">*</font></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;">
            <span style="padding:6px 0px 6px 9px;">            
            <select name="npwp" label="NPWP" id="npwp" onchange="cekDataPEB(this);">
			<option value="" selected>-</option>
            <?php
            $npwp_all = explode(",",$_SESSION['npwp_many_session']);
			foreach($npwp_all as $n){	
				$n = str_replace("'","",$n);
				if ($npwp_eks == $n ){
					echo "<option value='$n' selected>$n</option>";
				}else{
					echo "<option value='$n'>$n</option>";
				}
			}
			?>
            </select>
            </span>
            </td>
		</tr>		
		<tr>
			<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Nama Eksportir</strong> <font color="#FF0000">*</font></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <input name="eksportir" type="text" id="eksportir" size="60"   value ="<?php echo $eksportir?>"
            maxlength="50" class="isi" label="Nama Eksportir">
            </span></td>
		</tr>
		<tr valign="top" style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
			<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Alamat Eksportir</strong> <font color="#FF0000">*</font></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <textarea name="alamat" id="alamat" cols="61" rows="5" class="isi" label="Alamat Eksportir"><?php echo $alamat ?></textarea></span></td>
		</tr>
		<tr>
          <td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Valuta</strong> <font color="#FF0000">*</font></span></td>
		  <td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
             <?php
				
				$sql = "select VALUTA from tbldmkurs group by valuta";
				$dtval = $conn->query($sql);			
				$adaval=0;
				$checked ="";
				while($dtval->next()){
					if($valuta==$dtval->get('VALUTA')){
						$checked .="<option value='".$dtval->get('VALUTA')."' selected> - ".$dtval->get('VALUTA')."</option>";
						$adaval++;
					}else{
						$checked .="<option value='".$dtval->get('VALUTA')."'> - ".$dtval->get('VALUTA')."</option>";
					}
				}
			if($valuta!=""){
			?>			
			  <label style="cursor:pointer">
			  <input type="radio" name="rval" value="1" <?php echo ($adaval>0)? "checked" : ""?> onclick="$('.valselect').removeAttr('disabled');$('.valinput').attr('disabled','disabled')"/>
			  <strong>Pilih dari Daftar : </strong>
			  </label>
              <select name="valuta"  class="valselect isi" id="valuta" <?php echo ($adaval>0)? "" : "disabled"?> label="Valuta">
              		<option value="">Pilih Valuta</option>
					<?php echo $checked;?>
              </select>			  			  
              <label style="cursor:pointer">
			  <input type="radio" name="rval" value="2" <?php echo ($adaval>0)? "" : "checked"?> onclick="$('.valinput').removeAttr('disabled');$('.valselect').attr('disabled','disabled')" />
			  <strong>Input Manual : </strong>
			  </label>
		    <input type="text" name="valuta" id="valuta" class="valinput isi" maxlength="3" size="5" <?php echo ($adaval>0)? "disabled" : "value='".trim($valuta)."'"?> 
            style="text-transform:uppercase; text-align:center" label="Valuta"/>          			
			<?php
			}else{?>
				<label style="cursor:pointer">
				<input type="radio" name="rval" value="1" checked onclick="$('.valselect').removeAttr('disabled');$('.valinput').attr('disabled','disabled')"/>
				<strong>Pilih dari Daftar : </strong>
				</label>
				<select name="valuta"  class="valselect isi" id="valuta" label="Valuta">
					<option value="">Pilih Valuta</option>
					<?php echo $checked;?>
				</select>				
				<label style="cursor:pointer">			  
				<input type="radio" name="rval" value="2" onclick="$('.valinput').removeAttr('disabled');$('.valselect').attr('disabled','disabled')" />
				<strong>Input Manual : </strong>
				</label>
				<input type="text" name="valuta" id="valuta" class="valinput isi" maxlength="3" size="5" disabled 
                style="text-transform:uppercase; text-align:center" label="Valuta"/>
			<?php
			}
			?>
			</td>
	  </tr>
		<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
          <td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>FOB</strong> <font color="#FF0000">*</font></span></td>
		  <td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <input name="fob" type="text" id="fob" value="<?php echo number_format($fob,2);?>" size="20" onkeyup="javascript:numberFormatKoma(this,',','','')" maxlength="18"
            class="isi nospace" label="FOB">
          </span> </td>
	  </tr>
		<tr>
			<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>KPBC</strong> <font color="#FF0000">*</font></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <input name="kpbc" type="text" id="kpbc" value="<?php echo($kpbc);?>" maxlength="6" size="10" class="isi number nospace" label="KPBC" fix="6"></span></td>
		</tr>
		<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
			<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>NO PEB</strong> <font color="#FF0000">*</font></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <input name="nopeb" type="text" id="nopeb" value="<?php echo($nopeb);?>" size="10" maxlength="6" class="isi number nospace" label="NO PEB"></span></td>
		</tr>
		<tr>
			<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Tanggal PEB</strong> <font color="#FF0000">*</font></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <input name="tglpeb" type="text" id="tglpeb" value="<?php echo($tglpeb);?>" size="10"  readonly class="isi number nospace" label="Tanggal PEB" ></span><a href="javascript:void(0)" onClick="if(self.gfPop)gfPop.fPopCalendar(document.frmPEB.tglpeb); return false;"><img name="popcal" align="absmiddle" src="<?php echo base_url?>img/calbtn.gif" alt="Pilih tanggal" width="34" height="22" border="0" id="gbrClndr"></a></td>
		</tr>
		<?php
		if($div=="create"){
		?>
			<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
				<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Invoice</strong></span></td>
				<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">			
				<input type="text" name="Inv[]" maxlength="30" size="30" id="inv1" /> <strong>Tgl : </strong> &nbsp;<input type="text" name="tglInv_1" id="tglInv" readonly size="10" /><a href="javascript:void(0)" onClick="if(self.gfPop)gfPop.fPopCalendar(document.frmPEB.tglInv_1); return false;"><img name="popcal" align="absmiddle" src="<?php echo base_url?>img/calbtn.gif" alt="Pilih tanggal" width="34" height="22" border="0" id="gbrClndr"></a> <input type="button" value="Tambah" onclick="addInvoice()"/>
				<input type="hidden" id="jumInvoice" value="1" />
				</span></td>
			</tr>
			<tbody id="areaInvoice">		
			</tbody>
		<?php
		}elseif($div=="edit"){
			$no=1;
			while($datapebdok->next()){
				if($no==1){
				?>							
				<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
					<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Invoice</strong></span></td>
					<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">			
					<input type="text" name="Inv[]" maxlength="30" size="30" id="inv1" value="<?php echo $datapebdok->get('NODOK')?>"/> <strong>Tgl : </strong> &nbsp;<input type="text" name="tglInv_1" id="tglInv" readonly size="10" value="<?php echo $datapebdok->get('TGLDOK')?>" /><a href="javascript:void(0)" onClick="if(self.gfPop)gfPop.fPopCalendar(document.frmPEB.tglInv_1); return false;"><img name="popcal" align="absmiddle" src="<?php echo base_url?>img/calbtn.gif" alt="Pilih tanggal" width="34" height="22" border="0" id="gbrClndr"></a> <input type="button" value="Tambah" onclick="addInvoice()"/>
					<input type="hidden" id="jumInvoice" value="<?php echo $datapebdok->size();?>" />
					</span></td>
				</tr>
				<?php
				}else{?>					
					<tr id="rowinv<?php echo $no?>">
						<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"></td>
						<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">			
						<input type="text" name="Inv[]" maxlength="30" size="30" id="inv1" value="<?php echo $datapebdok->get('NODOK')?>"/> <strong>Tgl : </strong> &nbsp;<input type="text" name="tglInv_<?php echo $no?>" id="tglInv" readonly size="10" value="<?php echo $datapebdok->get('TGLDOK')?>" /><a href="javascript:void(0)" onClick="if(self.gfPop)gfPop.fPopCalendar(document.frmPEB.tglInv_<?php echo $no?>); return false;"><img name="popcal" align="absmiddle" src="<?php echo base_url?>img/calbtn.gif" alt="Pilih tanggal" width="34" height="22" border="0" id="gbrClndr"></a> <input type="button" value="Hapus" onclick="removeInvoice(<?php echo $no?>)"/>
						</span></td>
					</tr>
				<?php
				}	
				$no++;			
			}
			if($datapebdok->size()==0){?>
				<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
					<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Invoice</strong></span></td>
					<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">			
					<input type="text" name="Inv[]" maxlength="30" size="30" id="inv1" /> <strong>Tgl : </strong> &nbsp;<input type="text" name="tglInv_1" id="tglInv" readonly size="10" /><a href="javascript:void(0)" onClick="if(self.gfPop)gfPop.fPopCalendar(document.frmPEB.tglInv_1); return false;"><img name="popcal" align="absmiddle" src="<?php echo base_url?>img/calbtn.gif" alt="Pilih tanggal" width="34" height="22" border="0" id="gbrClndr"></a> <input type="button" value="Tambah" onclick="addInvoice()"/>
					<input type="hidden" id="jumInvoice" value="1" />
					</span></td>
				</tr>				
			<?php
			}
			?>
			<tbody id="areaInvoice"></tbody>	
		<?php
		}
		?>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td height="20"><span style="padding:6px 0px 6px 9px;"><strong>&nbsp;</strong></span></td>
			<td><span style="padding:6px 0px 6px 9px;">
			<button type="button" class="btn_1" onClick="cekPEB(<?php echo ($div=="create")?1:0;?>,'frmPEB');" style="width:60px">Save</button>
			<button type="reset" style="margin-left:0px; margin-right:0px;width:60px" class="btn_1" onclick="$('#valselect').removeAttr('disabled');$('#valinput').attr('disabled','disabled')">Reset</button>			                   
			<?php if($div=="edit"){?>
				<button type="button" onclick="location.href='<?php echo base_url?>modul/peb/baru'" style="margin-left:0px;width:60px" class="btn_1">Cancel</button></span>
			<?php } ?>
			</td>
			
		</tr>
	</table>				
	<iframe width=174 height=189 name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="<?php echo base_url?>ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">
</iframe>
	<?php
	}
?>
