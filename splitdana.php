<script type="text/javascript">
$(function()
{
	$('.scroll-pane').jScrollPane();
});
</script>
<?php
if(in_array($_SESSION["priv_session"],array("0","3"))==true || substr($_SESSION["AKSES"],3,1)!="1" ){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}else{

	set_time_limit(900);
	require_once("conf.php");
	require_once("dbconndb.php");
	$cbx = $_POST['cbx'];
	$act = strFilter($_POST['act']);
	$connDB->connect();
	if ($act == "save"){
		$jenisuang 	= $_POST['jenisuang'];
		$uangmuka  	= $_POST['uangmuka'];
		$dana  		= explode(";",$_POST['dana']);
		$idstt  	= $_POST['idstt'];
		$total 		= 0;
		$x=0;
		$sql = array();
		foreach($uangmuka as $uang){
			$cek = str_replace(",","",$uang);
			$total += $cek;
			$sql[] = "insert into tbldmdanamasuk(TGL_TRANSAKSI,IDLLD,NAMA_PEMILIK, NOREK, VALUTA_TRANSFER, VALUTA_DITERIMA,
								NOMINAL_DITERIMA, NAMA_PENGIRIM, NAMA_BANK_PENGIRIM,BERITA,REFERENCE_NUMBER,
								nominal_transfer,jns_uangmuka,flag_used,kd_dana,jns_pembayaran,dana_sisa,KODE_NONEKSPOR) 
						select   d.TGL_TRANSAKSI, d.IDLLD, d.NAMA_PEMILIK, d.NOREK, d.VALUTA_TRANSFER, d.VALUTA_DITERIMA,
								d.NOMINAL_DITERIMA, d.NAMA_PENGIRIM, d.NAMA_BANK_PENGIRIM, d.BERITA, d.REFERENCE_NUMBER,
								".$cek." as nominal_transfer,
								'0' as jns_uangmuka ,'0' as flag_used,'0".$jenisuang[$x]."' as kd_dana,'0' as jns_pembayaran, '2' as dana_sisa,'".$idstt[$x]."' as KODE_NONEKSPOR  from tbldmdanamasuk d  where  d. iddanamasuk = '".$dana[1]."' ";
		$x++;
		}
		
		//Kondisi Baru Deby 7 Juni 2016
		$total2 = str_replace(".","",$total);
		$dana2 = str_replace(".","",$dana[0]);		
		if ($total2 != $dana2){
			echo "<script type='text/javascript'>  jAlert('Total Dana Alokasi tidak sesuai dengan Nominal Transfer') </script>";
		}else{
		foreach($sql as $query){
			$connDB->execute($query);
		}
		$updt = "update tbldmdanamasuk set kd_dana = '05' where iddanamasuk = '".$dana[1]."'";
		$connDB->execute($updt);
		$_SESSION['respon'] = "Split Dana berhasil";			
		$aktivitas = "Split Dana sejumlah ".count($cbx)."";	
		audit($connDB,$aktivitas); 
		echo "<script> window.location.href='".base_url."modul/danamasuk/baru';</script>";exit;
		}

	}
	?>
	<form name="formtable" method="POST" enctype="multipart/form-data" >
	<input type="hidden" value="save" name="act" id="act">
	<input type="hidden" value="<?php echo $cbx[0] ?>" name="cbx[]" id="cbx[]">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
	<td style="border-bottom:1px solid #D7D7D7;">
	<div style="padding-bottom:9px;">
	<span style="color:#1a68a4; font-size:16px; font-weight:bold;">
	<?php							
	$messageBox = "<div style='background:#E5EEF5;padding:5px;border:1px #CCC solid;'>
							<img src='img/accept.png' style='border:none'> ".$_SESSION['respon']."
							</div>";
	if($_SESSION['respon']!=""){
		echo $messageBox;
		$_SESSION['respon']="";
	}else{
		switch($div){
		case "splitdana" : $link = base_url."modul/danamasuk/baru";break;
		}
		echo "<a href='".$link."' style='text-decoration:none;color:inherit'><img src='".base_url."img/back.png' title='kembali' style='border:none'></a> ";
		echo $bhs['Split'][$kdbhs]."  <br />"; 
	}
	?>							
	</span>
	</div>
	</td>
	</tr>
	</table>
	<div style="margin-top:18px; width: 100%; background:#F0F0F0; border: 1px solid #D7D7D7;">
	<div style="font-family:arial; font-size:11px; padding-top:10px; padding-bottom:10px; padding-left:10px;">
	<table cellpadding="0" cellspacing="0" style="font-family:arial; font-size:11px;">
	<tr>
	<td style="padding-left:20px; font-family:arial; font-size:11px; color:#333333; font-weight:bold;">						
	<?php	
	$connDB->connect();											
	$sql = "select a.iddanamasuk as ID,a.nama_pengirim as NAMA_PENGIRIM,a.NAMA_BANK_PENGIRIM, TO_CHAR(a.tgl_transaksi,'DD-MM-YYYY') AS TGL_TRANSAKSI, 
						TO_CHAR(a.nominal_transfer,'999,999,999,999.99') as NOMINAL_TRANSFER_FORMAT,
						TO_CHAR(a.nominal_diterima,'999,999,999,999.99') as NOMINAL_DITERIMA_FORMAT,						
						a.nominal_transfer AS NOMINAL_TRANSFER,a.VALUTA_TRANSFER,
						a.nominal_diterima AS NOMINAL_DITERIMA,a.VALUTA_DITERIMA,
						a.idLLD as IDLLD,
						a.JNS_UANGMUKA,
						a.FLAG_USED";
	if($div=='pilihpebcampuran'){
		$sql .= ",  TO_CHAR(b.DanaEkspor,'DD-MM-YYYY') as DANAEKSPOR_FORMAT,								
								b.DanaEkspor as DANAEKSPOR 
								from tbldmdanamasuk a inner join tblfcdanapartial b on a.iddanamasuk=b.iddanamasuk where a.iddanamasuk='".strFilter($_GET['id'])."' ";								
	}elseif($div=="rtepilihpeb"){
		$sql .= " ,b.URAIAN_PEMBAYARAN,a.JNS_PEMBAYARAN from tbldmdanamasuk a inner join tbldmpembayaran b on a.jns_pembayaran=b.jns_pembayaran where a.iddanamasuk='".strFilter($_GET['id'])."' ";														
	}else{
		$sql .= " from tbldmdanamasuk a where a.iddanamasuk='".strFilter($cbx[0])."' ";																
	}
	$data = $connDB->query($sql); $data->next();
	?>
	<table width="894">
	<tr>
	<td width="167"><?php echo $bhs['Nama Pengirim'][$kdbhs]?></td>
	<td width="9">:</td>
	<td width="443"><?php echo ucfirst($data->get("NAMA_PENGIRIM"))?></td>
	<td width="129"><?php echo $bhs['Tanggal Transaksi'][$kdbhs]?></td>
	<td width="9">:</td>
	<td width="109"><?php echo $data->get("TGL_TRANSAKSI")?></td>
	</tr>									
	<tr>
	<td><?php echo $bhs['Nama Bank'][$kdbhs]?></td>
	<td>:</td>
	<td><?php echo $data->get("NAMA_BANK_PENGIRIM")?></td>                                      
	<?php
	if($div=='pilihpebcampuran'){	
		echo "<td>Nilai DHE</td><td>:</td><td>".$data->get("DANAEKSPOR_FORMAT")." (".$data->get('VALUTA_TRANSFER').")"."</td>";
	}elseif($div=='rtepilihpeb'){
		$jns_uangmuka[1] = "Single";
		$jns_uangmuka[2] = "Multiple";
		echo "<td>Jenis Uang Muka</td><td>:</td><td>".$jns_uangmuka[$data->get("JNS_UANGMUKA")]."</td>";
	}else{
		echo "<td></td><td></td><td></td>";
	}
	?>
	</tr>
	<tr>
	<td><?php echo $bhs['Nominal Transfer'][$kdbhs]?></td>
	<td>:</td>
	<td><?php echo $data->get("NOMINAL_TRANSFER_FORMAT")." (".$data->get('VALUTA_TRANSFER').")"?></td>
	<?php
	if($div=="rtepilihpeb"){
		echo "<td>Jenis Pembayaran</td><td>:</td><td>".$data->get('URAIAN_PEMBAYARAN')."</td>";
	}else{
		echo "<td></td><td></td><td></td>";
	}
	?>
	</tr>
	<tr>
	<td><?php echo $bhs['Nominal Diterima'][$kdbhs]?></td>
	<td>:</td>
	<td><?php echo ($data->get("NOMINAL_DITERIMA_FORMAT"))?$data->get("NOMINAL_DITERIMA_FORMAT")." (".$data->get('VALUTA_DITERIMA').")":"";?></td>
	<td></td>
	<td></td>
	<td></td>
	</tr>                                    
	</table>									
	</td>
	</tr>
	</table>
	</div>
	</div>
	<br />
	<input type="hidden" value="<?php echo  $data->get("NOMINAL_TRANSFER").";".$data->get("ID")  ?>" name="dana" id="dana">
	<button type="submit" class="btn_2" >Proses</button>&nbsp;
	<div style="font:tahoma 6px normal;margin-top:5px;" id="hasilBaca">
	<table cellpadding="0" cellspacing="0" border='0' style="font-size:12px; font-family:arial; color:#333333; width:100%">
	<tr class="tbl_hdr" style="background:url(<?php echo base_url?>img/tab3.gif) repeat-x; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;"> 
	<th style="width:35px;">&nbsp;</th>
	<th style="text-align:left; ">No.</th>
	<th style="text-align:left;"><?php echo $bhs['Dana Masuk'][$kdbhs]?></th>
	<th style="text-align:left;"><?php echo $bhs['Nominal Transfer'][$kdbhs]?></th>			
	<th>&nbsp;</th>	
	</tr>
	<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
	<td style="border-bottom: 1px solid #D7D7D7">
	</td>
	<td style="border-bottom: 1px solid #D7D7D7">1.</td>
	<td style="border-bottom: 1px solid #D7D7D7"><div align="LEFT" class="div_tbl" style="font-size:11px; font-family:Arial; padding:6px 0px 6px 9px;"><select idstt="<?php echo $nextNo?>" name="jenisuang[]" onchange="cekSTT(this,idstt<?php echo $nextNo?>)"><option value="1">Ekspor</option><option value="2">Non Ekspor</option></select></div></td>
	<td style="border-bottom: 1px solid #D7D7D7"><div align="LEFT" class="div_tbl" style="font-size:11px; font-family:Arial; padding:6px 0px 6px 9px;">
	<input type="text" id="ajaxMod1Text<?php echo $nextNo?>" style="text-align:right" name="uangmuka[]" class="sisadm"  onkeyup="javascript:numberFormatKoma(this,',','','')">
	</div></td>
	<td  ><input type="text" style="display:none" name="idstt[]" size="5" id="idstt<?php echo $nextNo?>" onclick="javascript:showSTT('idstt'<?php echo $nextNo?>)"/></td>
	</tr>	
	<tbody id="addRowArea">	  
	</tbody>
	</table>
	<button type="button" class="btn_4" onclick="addRowSplit()">Tambah Row</button>&nbsp;<button type="button" class="btn_4" onclick="delRowSplit()">Hapus Row</button>
	<span id="jumlahRow" jum="1"></span>
	<span id="startId" jum="<?php echo $nextNo?>"></span>
	</div>
	</form>
	<?php
}
?>

