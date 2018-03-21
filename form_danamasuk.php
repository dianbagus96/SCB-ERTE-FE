<?php
if(in_array($_SESSION["priv_session"],array("0"))==false){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}elseif(time()>$_SESSION['timeout_session']){	
	echo "<script> window.location.href='".base_url."err/7/".md5(7)."';</script>";exit;
}else{
	$_SESSION['timeout_session'] = TIMEOUT;
}
require_once("dbconn.php");
require_once("dbconndb.php");
$connDB->connect();
$DM = arrFilter($_POST['DM']);
if($div=='insert'){
	if(!is_array($DM)){ echo "<script> window.location.href='".base_url."modul/danamasuk/add';</script>";exit;}	
	$tgltran = $_POST['tgltran'];
	$DM['TGL_TRANSAKSI'] = "CONVERT(DATETIME,'$tgltran',105)";
	$DM['NOMINAL_TRANSFER'] = str_replace(",","",$DM['NOMINAL_TRANSFER']);
	$DM['NOMINAL_DITERIMA'] = str_replace(",","",$DM['NOMINAL_DITERIMA']);
	$DM['FLAG_USED'] = "0";
	$DM['KD_DANA'] = "00";
	$DM['JNS_PEMBAYARAN'] = "00";
		
	insert("tbldmdanamasuk",$DM,array("TGL_TRANSAKSI","NOMINAL_TRANSFER","NOMINAL_DITERIMA"),$connDB);
	$iddm = selectMax("tbldmdanamasuk","iddanamasuk",$connDB);	
	$aktivitas = "Berhasil menginput Dana Masuk dengan ID : ".$iddm;
	$connDB->disconnect();
	$conn->connect();
	audit($conn,$aktivitas);		
	$conn->disconnect();
	$_SESSION['respon'] = 'Anda telah Berhasil menginput Dana Masuk';		
	echo "<script> window.location.href='".base_url."modul/danamasuk/monitor';</script>";exit;	
}

?>
<table cellpadding="0" cellspacing="0" width="85%">
	<tr>
		<td style="border-bottom:1px solid #D7D7D7;">
			<div style="padding-bottom:9px;">
			<span style="color:#1a68a4; font-size:16px; font-weight:bold;">
			<?php 			
			echo ($div == "edit")? "Edit Danamasuk" : "Add New Dana Masuk";
			?>                            
			</span><br />
		  </div>
		</td>
	</tr>
</table>			
<form method="post" id="formDanamasuk" action='<?php echo base_url."modul/danamasuk/".($div=='edit'?'update':'insert')?>' name="formDanamasuk">            
<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
		<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">Dana Masuk Information</td>
	</tr>
</table>				
<table cellpadding="0" cellspacing="0" width="100%" style="font-size:11px; font-family:arial; color:#333333;">
	<tr>
	  <td height="20" ><span style="padding:6px 0px 6px 9px;"><strong>Tanggal Transaksi <font color="#FF0000">*</font></strong></span></td>
		<td>
        <span style="padding:6px 0px 6px 9px;">
        <input name="tgltran" type="text" id="tgltran" value="<?php echo($tgltran);?>" size="10"  readonly class="isi" label="Tanggal Transaksi">
        </span><a href="javascript:void(0)" onClick="if(self.gfPop)gfPop.fPopCalendar(document.formDanamasuk.tgltran); return false;"><img name="popcal" align="absmiddle" src="<?php echo base_url?>images/calbtn.gif" alt="Pilih tanggal" width="34" height="22" border="0" id="gbrClndr"></a>
        </td>
	</tr>
	<tr class="mix">
	  <td height="20" ><span style="padding:6px 0px 6px 9px;"><strong>Nomor Referensi<font color="#FF0000">*</font></strong></span></td>
	  <td ><span style="padding:6px 0px 6px 9px;">
	    <input name="DM[REFERENCE_NUMBER]" type="text" value="<?php echo $data->get('REFERENCE_NUMBER')?>" style="width:200px; margin-left:2px;" maxlength="20" 
        class="isi" label="Nomor Referensi" />
	    </span></td>
    </tr>
	<tr>
		<td height="20" width="170" ><span style="padding:6px 0px 6px 9px;"><strong>ID LLD <font color="#FF0000">*</font></strong></span></td>
		<td ><span style="padding:6px 0px 6px 9px;">
        <input name="DM[IDLLD]" type="text" value="<?php echo $data->get('IDLLD')?>" style="width:200px; margin-left:2px;" maxlength="50" class="isi" label="ID LLD"></span></td>
	</tr>
	<tr class="mix">
		<td height="20" ><span style="padding:6px 0px 6px 9px;"><strong>Nama Pemilik <font color="#FF0000">*</font></strong></span></td>
		<td ><span style="padding:6px 0px 6px 9px;">
		  <input name="DM[NAMA_PEMILIK]" type="text" value="<?php echo $data->get('NAMA_PEMILIK')?>" style="width:200px; margin-left:2px;" maxlength="20"  class="isi" label="Nama Pemilik"/>
		</span></td>
	</tr>
	<tr>
		<td height="20" width="170" ><span style="padding:6px 0px 6px 9px;"><strong>No. Rekening <font color="#FF0000">*</font></strong></span></td>
		<td ><span style="padding:6px 0px 6px 9px;">
		  <input name="DM[NOREK]" type="text" value="<?php echo $data->get('NOREK')?>" style="width:200px; margin-left:2px;" maxlength="13" class="isi number nospace" label="No. Rekening" fix="13"/>
		</span></td>
	</tr>
	<tr class="mix">
		<td height="20" ><span style="padding:6px 0px 6px 9px;"><strong>Valuta Transfer <font color="#FF0000">*</font></strong></span></td>
		<td ><span style="padding:6px 0px 6px 9px;">
        <?php
				
				$sql = "select VALUTA from tbldmkurs group by valuta";
				$dtval = $connDB->query($sql);			
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
          <input type="radio" name="rval1" value="1" <?php echo ($adaval>0)? "checked" : ""?> onclick="$('.valselect1').removeAttr('disabled');$('.valinput1').attr('disabled','disabled')"/>
          <strong>Pilih dari Daftar : </strong> 
        </label>
        <select name="DM[VALUTA_TRANSFER]"  class="valselect1 isi" label="Valuta Transfer" id="valuta" <?php echo ($adaval>0)? "" : "disabled"?>>
          <option value="">Pilih Valuta</option>
          <?php echo $checked;?>
          </select>
        <label style="cursor:pointer">
          <input type="radio" name="rval1" value="2" <?php echo ($adaval>0)? "" : "checked"?> onclick="$('.valinput1').removeAttr('disabled');$('.valselect1').attr('disabled','disabled')" />
          <strong>Input Manual : </strong> </label>
        <input type="text" name="DM[VALUTA_TRANSFER]" id="valuta" class="valinput1 isi" maxlength="3" size="5" label="Valuta Transfer"
		<?php echo ($adaval>0)? "disabled" : "value='".trim($valuta)."'"?> style="text-transform:uppercase; text-align:center" />
        <?php
			}else{?>
        <label style="cursor:pointer">
          <input type="radio" name="rval2" value="1" checked="checked" onclick="$('.valselect2').removeAttr('disabled');$('.valinput2').attr('disabled','disabled')"/>
          <strong>Pilih dari Daftar : </strong> </label>
        <select name="DM[VALUTA_TRANSFER]"  class="valselect2 isi" id="valuta" label="Valuta Transfer">
          <option value="">Pilih Valuta</option>
          <?php echo $checked;?>
        </select>
        <label style="cursor:pointer">
          <input type="radio" name="rval2" value="2" onclick="$('.valinput2').removeAttr('disabled');$('.valselect2').attr('disabled','disabled')" />
          <strong>Input Manual : </strong> </label>
        <input type="text" name="DM[VALUTA_TRANSFER]" id="valuta" class="valinput2 isi" maxlength="3" size="5" disabled="disabled" style="text-transform:uppercase; text-align:center" label="Valuta Transfer"/>
        <?php
			}
			?>
        </span></td>
	</tr>
	<tr>
	  <td height="20" ><span style="padding:6px 0px 6px 9px;"><strong>Nominal Transfer <font color="#FF0000">*</font></strong></span></td>
	  <td ><span style="padding:6px 0px 6px 9px;">
	    <input name="DM[NOMINAL_TRANSFER]" type="text" value="<?php echo $data->get('NOMINAL_TRANSFER')?>" style="width:200px; margin-left:2px; text-align:right" 
        maxlength="50" onkeyup="javascript:numberFormatKoma(this,',','','')" class="isi money" label="Nominal Transfer"/>
	    </span></td>
    </tr>
	<tr class="mix">
	  <td height="20" ><span style="padding:6px 0px 6px 9px;"><strong>Valuta Diterima <font color="#FF0000">*</font></strong></span></td>
	  <td ><span style="padding:6px 0px 6px 9px;">
      <?php
				
				$sql = "select VALUTA from tbldmkurs group by valuta";
				$dtval = $connDB->query($sql);			
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
        <strong>Pilih dari Daftar : </strong> </label>
      <select name="DM[VALUTA_DITERIMA]"  class="valselect isi" id="valuta2" <?php echo ($adaval>0)? "" : "disabled"?> label="Valuta Terima">
        <option value="">Pilih Valuta</option>
        <?php echo $checked;?>
      </select>
      <label style="cursor:pointer">
        <input type="radio" name="rval" value="2" <?php echo ($adaval>0)? "" : "checked"?> onclick="$('.valinput').removeAttr('disabled');$('.valselect').attr('disabled','disabled')" />
        <strong>Input Manual : </strong> </label>
      <input type="text" name="DM[VALUTA_DITERIMA]" id="valuta2" class="valinput isi" maxlength="3" size="5" <?php echo ($adaval>0)? "disabled" : "value='".trim($valuta)."'"?> style="text-transform:uppercase; text-align:center" label="Valuta Terima" />
      <?php
			}else{?>
      <label style="cursor:pointer">
        <input type="radio" name="rval" value="1" checked="checked" onclick="$('.valselect').removeAttr('disabled');$('.valinput').attr('disabled','disabled')"/>
        <strong>Pilih dari Daftar : </strong> </label>
      <select name="DM[VALUTA_DITERIMA]" class="valselect isi" id="valuta2" label="Valuta Terima" >
        <option value="">Pilih Valuta</option>
        <?php echo $checked;?>
      </select>
      <label style="cursor:pointer">
        <input type="radio" name="rval" value="2" onclick="$('.valinput').removeAttr('disabled');$('.valselect').attr('disabled','disabled')" />
        <strong>Input Manual : </strong> </label>
      <input type="text" name="DM[VALUTA_DITERIMA]" id="valuta2" class="valinput isi" maxlength="3" size="5" disabled="disabled" style="text-transform:uppercase; text-align:center"
      label="Valuta Terima" />
      <?php
			}
			?>
      </span></td>
    </tr>
	<tr>
	  <td height="20" ><span style="padding:6px 0px 6px 9px;"><strong>Nominal Diterima <font color="#FF0000">*</font></strong></span></td>
	  <td ><span style="padding:6px 0px 6px 9px;">
	    <input name="DM[NOMINAL_DITERIMA]" type="text" id="email4" value="<?php echo $data->get('NOMINAL_DITERIMA')?>" style="width:200px; margin-left:2px; text-align:right" 
        onkeyup="javascript:numberFormatKoma(this,',','','')" maxlength="50" class="isi" label="Nominal Terima" />
	    </span></td>
    </tr>
	<tr class="mix">
	  <td height="20" ><span style="padding:6px 0px 6px 9px;"><strong>Nama Pengirim <font color="#FF0000">*</font></strong></span></td>
	  <td ><span style="padding:6px 0px 6px 9px;">
	    <input name="DM[NAMA_PENGIRIM]" type="text" id="email5" value="<?php echo $data->get('NAMA_PENGIRIM')?>" style="width:200px; margin-left:2px;" maxlength="50" class="isi" label="Nama Pengirim"/>
	    </span></td>
    </tr>
	<tr>
	  <td height="20" ><span style="padding:6px 0px 6px 9px;"><strong>Nama Bank Pengirim <font color="#FF0000">*</font></strong></span></td>
	  <td ><span style="padding:6px 0px 6px 9px;">
	    <input name="DM[NAMA_BANK_PENGIRIM]" type="text" id="email6" value="<?php echo $data->get('NAMA_BANK_PENGIRIM')?>" style="width:200px; margin-left:2px;" maxlength="50"
        class="isi" label="Nama Bank Pengirim" />
	    </span></td>
    </tr>
	<tr class="mix">
	  <td height="20" valign="top" ><span style="padding:6px 0px 6px 9px;"><strong>Berita</strong></span></td>
	  <td ><span style="padding:6px 0px 6px 9px;">
	    <textarea name="DM[BERITA]" cols="50" rows="5" style="font-family:Tahoma;font-size:11px;" class="" label="Berita"><?php echo $remark?></textarea>
	  </span></td>
    </tr>
	<tr>
		<td height="20"><span style="padding:6px 0px 6px 9px;"><strong>&nbsp;</strong></span></td>
		<td><span style="padding:6px 0px 6px 9px;">
        <button type="button" class="btn_1"  onclick="cekFormDanaMasuk('formDanamasuk')" style="width:60px">Save</button> 
		 &nbsp;
		 <button type="button" onclick="history.back()" class="btn_2" style="width:75px">Cancel</button></span></td>
	</tr>
</table>
</form>														
<iframe width=174 height=189 name="gToday:normal:agenda.js" id="gToday:normal:agenda.js" src="<?php echo base_url?>ipopeng.htm" scrolling="no" frameborder="0" style="visibility:visible; z-index:999; position:absolute; left:-500px; top:0px;">
</iframe>
<?php
$conn->disconnect();
?>
