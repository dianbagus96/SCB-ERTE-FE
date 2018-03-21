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
if($_GET['seccode']!=md5($_GET['id'])){ echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;}

set_time_limit(900);
require_once("conf.php");
if($_GET['near']=='near'){
	$conn->connect();
	$sql = "select NILAI from tbldmMatching where upper(groupid) = '".strtoupper(trim($_SESSION['grpID']))."'";
	$data = $conn->query($sql);
	if($data->next()){
		$nilaiNearMatching = $data->get('NILAI');
	} else {
		$nilaiNearMatching = '0';
	}
	
}
//print_r($_GET);
require_once("dbconndb.php");
$cari = str_replace('\\','',$_POST["q"]);
$_POST["q"] = strFilter($_POST["q"]);
if ($div=="rtepilihpeb"){
	$connDB->connect();
	if (count($_POST["cbx"]) > 0){
		$sts =  implode(",",$_POST["cbx"]);
	$sql = " select status from tblfcrte where idrte = '$sts'";
	$exec = $connDB->query($sql);
	$exec->next();
	$status = $exec->get("status");
		if( $status == 5){
		echo "<script type='text/javascript'> 
						$(document).ready(function(){
							jAlert('RTE Uang Muka sudah dibatalkan','',function(r){
								if(r==true) window.location.href='".base_url."modul/rte/rteuangmuka';\n				
							});		
						})
					</script>";	
					exit;
		}
		
	}
	

}

?>
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
					case "rtepilihpeb" : $link = base_url."modul/rte/rteuangmuka";break;
					case "pilihpeb" : $link = base_url."modul/danamasuk/ekspor";break;
					case "pilihpebcampuran" : $link = base_url."modul/danamasuk/campuran";break;
				}
				echo "<a href='".$link."' style='text-decoration:none;color:inherit'><img src='".base_url."img/back.png' title='kembali' style='border:none'></a> ";
				echo ($div=="rtepilihpeb")?$bhs['RTE Uang Muka'][$kdbhs]:""; echo str_replace("-","",$bhs['Pilih PEB'][$kdbhs])."  <br />"; 
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
					$sql .= " from tbldmdanamasuk a where a.iddanamasuk='".strFilter($_GET['id'])."' ";																
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
						<td>Nominal Tujuan</td> <!-- Edit by yoanes: <?php echo $bhs['Nominal Transfer'][$kdbhs]?> => "Nominal Tujuan" -->
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
<div style="margin-top:18px; width: 100%; background:#F0F0F0; border: 1px solid #D7D7D7;">
	<div style="font-family:arial; font-weight:lighter; font-size:11px; padding-top:10px; padding-bottom:10px; padding-left:10px;">
		<table cellpadding="0" cellspacing="0" style="font-family:arial; font-weight:lighter; font-size:11px;">
			<tr>
				<td style="padding-left:20px; font-family:arial; font-size:11px; color:#333333; font-weight:bold;">
				<?php
				if($div=="pilihpeb"){
					$url = "modul/danamasuk/pilihpeb/";	
				}elseif($div=="rtepilihpeb"){
					$url = "modul/rte/rtepilihpeb/";	
				}			
				?>
				<form name="frmCari" id="frmCari" method="post" action="<?php echo base_url.$url.$_GET['id']."/".$_GET['seccode']?>">				
				<table>
					<tr>
						<td><?php echo $bhs['Kategori'][$kdbhs]?></td>
						<td><select name="field" id="field">
							<?php																
							$x1 = array("no_PEB");
							$x2 = array($bhs['No. PEB'][$kdbhs]);
							for($i=0;$i<count($x1);$i++){
								if($_POST['field']==$x1[$i]){
									echo("<option value=\"$x1[$i]\" selected>$x2[$i]</option>");
								} else {
									echo("<option value=\"$x1[$i]\">$x2[$i]</option>");
								}
							}
							?>
						  </select>
						</td>
					</tr>
					<tr>
						<td><?php echo $bhs['Cari'][$kdbhs]?></td>
						<td><input name="q" type="text" id="q" size="50" value="<?php echo($cari);?>"  placeholder="<?php echo $bhs['notif1'][$kdbhs]?> " >
							<button type="button" class="btn_2" name="search" onclick="javascript:$('#frmCari').submit()" style="width:73px;">Search</button>					
						   <?php if($cari){
									echo '<button type="button" onclick=location.href="'.base_url.$url.$_GET['id']."/".$_GET['seccode'].'" style="width:75px" class="btn_2">Cancel</button></td>';
						   		}
							?>
							<input type="hidden" name="Submit" value="submit"  />															
						</td>
					</tr>
				</table>
				</form>								
				</td>
			</tr>
		</table>
	</div>
</div>
<?php
	
if($div=="pilihpeb"){
echo "<form name='formtable' method='GET' enctype='multipart/form-data'>";
?>
<br />
<b style="font-size:13px; padding:5px;letter-spacing:2px;color:#333333">Data PEB</b>
<table width="100%" border="0" cellpadding="5" cellspacing="0" frameborder="0">
	<tr>
		<td>
		<select name="RDSelect">
		<option value="#" selected><?php echo $bhs['Pilih Proses'][$kdbhs]?></option>
		<option value="<?php echo base_url?>modul/danamasuk/eksporrte"><?php echo $bhs['Simpan Pemilihan PEB'][$kdbhs]?></option>
		</select>
		<button type="button" onClick="RDPanelClick(this)" name="btnRDPanel" class="btn_2" style="width:75px">Process</button>
		<button type="button" onClick="javascript:window.location.href='<?php echo base_url.$url.$_GET['id']."/".$_GET['seccode'].'/near'?>'" name="btnRDPanel" class="btn_4" style="width:105px">Near Macthing</button>
        <select name="statusDanamasuk" id="statusDanamasuk">
            <option value=""><?php echo $_SESSION['lang']['Pilih Status Dana Masuk'][$_SESSION['bahasa_sess']]?></option>
            <option value="1"> - Complete</option>
            <option value="3"> - Pending</option>
        </select>
		</td>
		<td align="right" style="font-family: arial; font-size: 11px; color:#333333;"></td>
	</tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" frameborder="0">
	<tr class=tbl_hdr >
		<th width="20" nowrap>		
		<input type="checkbox" name="cbxAll" onClick='check(this)'>
		</th>
		<th width="90" style="background:url(<?php echo base_url?>img/tab3.gif) repeat-x; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold; cursor: pointer;text-align:left" onclick="descasc('KPBC', '/modul/danamasuk/pilihpeb/<?php echo $id.'/'.md5($id)?>');">
		<input type="hidden" value="<?php echo $_GET['order']?>" id="descascKPBC">KPBC
		</th>
		<th width="110" style="background:url(<?php echo base_url?>img/tab3.gif) repeat-x; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold; cursor: pointer;text-align:left" onclick="descasc('INVOICE', '/modul/danamasuk/pilihpeb/<?php echo $id.'/'.md5($id)?>');">
		<input type="hidden" value="<?php echo $_GET['order']?>" id="descascNPWP">No Invoice
		</th>
		<th width="80" style="background:url(<?php echo base_url?>img/tab3.gif) repeat-x; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold; cursor: pointer;text-align:left" onclick="descasc('NOPEB', '/modul/danamasuk/pilihpeb/<?php echo $id.'/'.md5($id)?>');">
		<input type="hidden" value="<?php echo $_GET['order']?>" id="descascNOPEB"><?php echo $bhs['No. PEB'][$kdbhs]?>
		</th>
		<th width="110" style="background:url(<?php echo base_url?>img/tab3.gif) repeat-x; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold; cursor: pointer;text-align:left" onclick="descasc('TGLPEB', '/modul/danamasuk/pilihpeb/<?php echo $id.'/'.md5($id)?>');">
		<input type="hidden" value="<?php echo $_GET['order']?>" id="descascTGLPEB"><?php echo $bhs['Tanggal PEB'][$kdbhs]?>
		</th>
		<th width="110" style="background:url(<?php echo base_url?>img/tab3.gif) repeat-x; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold; cursor: pointer;text-align:left" onclick="descasc('VALUTA', '/modul/danamasuk/pilihpeb/<?php echo $id.'/'.md5($id)?>');">
		<input type="hidden" value="<?php echo $_GET['order']?>" id="descascVALUTA"><?php echo $bhs['Valuta'][$kdbhs]?>
		</th>
		<th width="110" style="background:url(<?php echo base_url?>img/tab3.gif) repeat-x; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold; cursor: pointer;text-align:left" onclick="descasc('FOBSORT', '/modul/danamasuk/pilihpeb/<?php echo $id.'/'.md5($id)?>');">
		<input type="hidden" value="<?php echo $_GET['order']?>" id="descascFOBSORT"><?php echo $bhs['Nilai PEB'][$kdbhs]?>
		</th>
		<th width="150" style="background:url(<?php echo base_url?>img/tab3.gif) repeat-x; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold; cursor: pointer;text-align:left" onclick="descasc('NILAIBARU', '/modul/danamasuk/pilihpeb/<?php echo $id.'/'.md5($id)?>');">
		<input type="hidden" value="<?php echo $_GET['order']?>" id="descascNILAIBARU"><?php echo $bhs['Nilai DHE'][$kdbhs]?>
		</th>
		<th width="" style="background:url(<?php echo base_url?>img/tab3.gif) repeat-x; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold; cursor: pointer;text-align:left" onclick="descasc('SANDIKETERANGAN', '/modul/danamasuk/pilihpeb/<?php echo $id.'/'.md5($id)?>');">
		<input type="hidden" value="<?php echo $_GET['order']?>" id="descascSANDIKETERANGAN"><?php echo $bhs['Sandi Keterangan'][$kdbhs]?>
		</th>
	</tr>
</table>
<div class="scroll-pane">
<?php				
}	
	$order = strFilter($_GET['order']);
	$sort = strFilter($_GET['sort']);
	$x1 = array("KPBC","NPWP","NAMAEKSPORTIR","NOPEB","TGLPEB","VALUTA","FOBSORT");								
	$orderby = setSortir(array('sort'=>'ID','order'=>'desc'),$x1);
	
	$div = (!in_array($div,array("pilihpebcampuran","pilihpeb","rtepilihpeb","danaterlaporkanpilihpeb")))? "pilihpeb" :$div;										
	$sql = "select idPEB as ID, KPBC,
			get_comma_separated_value(CAR) as INVOICE,		
			nama_eksportir as NAMAEKSPORTIR, no_PEB as NOPEB, TO_CHAR(tgl_PEB,'DD-MM-YYYY') AS TGLPEB, p.VALUTA , 
			TO_CHAR(FOB,'999,999,999,999.00') AS FOB,
			'' as NILAIBARU, '' as SANDIKETERANGAN, '".strFilter($_GET['id'])."' as IDDANAMASUK, KURS,'".$data->get("TGL_TRANSAKSI")."' as TGL_TRANSAKSI,
			(select (k.NOMINAL*p.FOB) from tbldmkurs k where k.valuta=p.valuta
			and DATEDIFF(TO_DATE('".$data->get("TGL_TRANSAKSI")."','DD-MM-YYYY'),k.tglawal) >=0 and 
			DATEDIFF(k.tglakhir,TO_DATE('".$data->get("TGL_TRANSAKSI")."','DD-MM-YYYY')) >=0 and ROWNUM = 1 ) as IDR,
			(select (k.NOMINAL) from tbldmkurs k where k.valuta='".$data->get('VALUTA_TRANSFER')."'
			and DATEDIFF(TO_DATE('".$data->get("TGL_TRANSAKSI")."','DD-MM-YYYY'),k.tglawal) >=0 and 
			DATEDIFF(k.tglakhir,TO_DATE('".$data->get("TGL_TRANSAKSI")."','DD-MM-YYYY')) >=0 and ROWNUM = 1  ) as KURS_IDR, '' as keterangan,
			'".str_replace("'","''",$data->get("IDLLD"))."' as idLLD,
			'".str_replace("'","''",$data->get("NAMA_PENGIRIM"))."' as nama_pengirim,
			'".$data->get("VALUTA_TRANSFER")."' as VALUTA_TRANSFER,
			FOB as FOBSORT,
			'".$data->get("JNS_PEMBAYARAN")."' as JNS_PEMBAYARAN";
	if($_GET['near']=='near'){
		$nilaitransfer = $data->get("NOMINAL_TRANSFER");												
		$sql .= " 	from tbldmpeb p left join tbldmkurs k on p.valuta=k.valuta
					where flag_used not in('1','4') and upper(p.groupid) in ('".strtoupper($_SESSION['grpID'])."') and
					DATEDIFF(TO_DATE('".$data->get("TGL_TRANSAKSI")."','DD-MM-YYYY'),k.tglawal)>=0 and
					DATEDIFF(k.tglakhir,TO_DATE('".$data->get("TGL_TRANSAKSI")."','DD-MM-YYYY'))>=0 and 
					abs(".$nilaitransfer."-(p.FOB * k.nominal))<=".$nilaiNearMatching." ";
	}else{
		$sql .= " ,( (select (k.NOMINAL*p.FOB) from tbldmkurs k where k.valuta=p.valuta
			and DATEDIFF(TO_DATE('".$data->get("TGL_TRANSAKSI")."','DD-MM-YYYY'),k.tglawal) >=0 and 
			DATEDIFF(k.tglakhir,TO_DATE('".$data->get("TGL_TRANSAKSI")."','DD-MM-YYYY')) >=0 and ROWNUM = 1) /
			(select (k.NOMINAL) from tbldmkurs k where k.valuta='".$data->get('VALUTA_TRANSFER')."'
			and DATEDIFF(TO_DATE('".$data->get("TGL_TRANSAKSI")."','DD-MM-YYYY'),k.tglawal) >=0 and 
			DATEDIFF(k.tglakhir,TO_DATE('".$data->get("TGL_TRANSAKSI")."','DD-MM-YYYY')) >=0 and ROWNUM = 1)
			) as FOB_KURS_DHE from tbldmpeb p where flag_used not in('1','4') and upper(p.groupid) = upper('".$_SESSION['grpID']."') ";	
	}
	
	if($_POST["Submit"]=="submit" ){						
		$q = explode(';',$_POST['q']);						
		if($q[0]!=""){
			$no_peb = "'".implode("','",$q)."'";
			$sql = $sql ." And no_PEB in (".$no_peb.")";	
		}else{
			$sql = $sql ." And no_PEB like '%%' ";	
		}
	} 				
	function getIDR($conn,$valuta,$tgl,$nilai){
		$sql = "select NOMINAL from tbldmkurs where valuta='".$valuta."' 
				and DATEDIFF(SYSDATE,tglawal)<=DATEDIFF(SYSDATE,TO_DATE('".$tgl."','DD-MM-YYYY'))and
				DATEDIFF(SYSDATE,tglakhir)>=DATEDIFF(SYSDATE,TO_DATE('".$tgl."','DD-MM-YYYY')) and ROWNUM = 1 order by tglawal desc";
		$d = $conn->query($sql); $d->next();
		return $nominal = $nilai*ceil($d->get('NOMINAL'));
	}	
	$connDB->connect();
	//sql gabungan
	$sql = $sql . $orderby;
	//echo $sql; 
	$table = new HTMLTable();
	$table->connection = $connDB;
	$table->width = "100%";
	$table->ajaxMod1 = 8;
	$table->ajaxMod2 = 9;
	$table->ajaxMod10 = 15;		
	
	if($div=="pilihpeb"){
		$table->modifForm1 = true;
	}elseif($div=="rtepilihpeb"){
		$table->ajaxMod18 = 7;			
	}
	
	$table->showBtnNew('Near Macthing',base_url."modul/rte/rtepilihpeb/".$_GET['id']."/".$_GET['seccode']."/near");
	$table->nominalDiterima = ($div=='pilihpebcampuran')? $data->get("DANAEKSPOR") : $data->get("NOMINAL_TRANSFER");
	$table->backlink = $div;
	$table->navRowSize = 10;
	$table->SQL = $sql;			
	if($data->get("JNS_UANGMUKA")==2){
		$table->opsiPlus3=true;		
	}
	// elemen data yang akan di passing
	$cols = array();
	$cols[0] = 0; #idpeb
	$cols[1] = 6; #valuta
	$cols[2] = 10; #iddanamasuk
	$cols[3] = 11; #kurs
	$cols[4] = 12; #tgl_Trans	
	$cols[5] = 16; #idlld
	$cols[6] = 7; #fob
	$cols[7] = 17; #NAMA PENGIRIM
	$cols[8] = 18; #VALUTA_TRANSFER
	$cols[9] = 20; #jns_pembayaran
	
	$data = array();
	$data[] = array("#",$bhs['Pilih Proses'][$kdbhs]);					
	$data[] = ($div=="rtepilihpeb")? array(base_url."modul/danamasuk/rtepilihpeb"," - Simpan Pemilihan PEB") : array(base_url."modul/danamasuk/eksporrte"," - Simpan Pemilihan PEB");
	$table->showCheckBox(true,$cols);	
	$table->showPager(false,$F_HANDLER->BOTTOM,500,10);
	$table->showRDPanel(true,$F_HANDLER->BOTH,$cols,$data);
	 #KPBC,Npwp, nama,no,tgl,valuta,nilai
	$x=0;
	$table->field[$x]->name = "ID";
	$table->field[$x]->headername = "ID";
	$table->field[$x]->align = $F_HANDLER->LEFT;
	$table->field[$x]->hidden = true;
	$x=1;					
	$table->field[$x]->name = "KPBC";
	$table->field[$x]->headername = "Kode KPBC";
	$table->field[$x]->align = $F_HANDLER->LEFT;
	$x=2;
	$table->field[$x]->name = "INVOICE";
	$table->field[$x]->headername = "INVOICE";
	$table->field[$x]->align = $F_HANDLER->LEFT;	
	$x=3;
	$table->field[$x]->name = "NAMAEKSPORTIR";
	$table->field[$x]->headername = "Eksportir";
	$table->field[$x]->hidden = true;
	$x=4;
	$table->field[$x]->name = "NOPEB";
	$table->field[$x]->headername = "No PEB";
	$table->field[$x]->align = $F_HANDLER->LEFT;
	$x=5;
	$table->field[$x]->name = "TGLPEB";
	$table->field[$x]->headername = "Tanggal PEB";
	$table->field[$x]->align = $F_HANDLER->LEFT;
	$x=6;
	$table->field[$x]->name = "VALUTA";
	$table->field[$x]->headername = "Valuta";
	$table->field[$x]->align = $F_HANDLER->LEFT;
	$x=7;
	$table->field[$x]->name = "FOBSORT";
	$table->field[$x]->headername = "Nilai PEB";
	$table->field[$x]->align = $F_HANDLER->RIGHT;
	$x=8;
	$table->field[$x]->name = "NILAIBARU";
	$table->field[$x]->headername = "Nilai DHE";
	$table->field[$x]->align = $F_HANDLER->LEFT;
	$x=9;
	$table->field[$x]->name = "SANDIKETERANGAN";
	$table->field[$x]->headername = "Sandi Keterangan";
	$table->field[$x]->align = $F_HANDLER->LEFT;
	$x=10;
	$table->field[$x]->name = "IDDANAMASUK";
	$table->field[$x]->headername = "iddanamasuk";
	$table->field[$x]->align = $F_HANDLER->LEFT;
	$table->field[$x]->hidden = true;
	$x=11;
	$table->field[$x]->name = "KURS";
	$table->field[$x]->headername = "kurs";
	$table->field[$x]->align = $F_HANDLER->LEFT;
	$table->field[$x]->hidden = true;
	$x=12;
	$table->field[$x]->name = "TGL_TRANSAKSI";
	$table->field[$x]->headername = "TGL_TRANSAKSI";
	$table->field[$x]->align = $F_HANDLER->LEFT;
	$table->field[$x]->hidden = true;
	$x=13;
	$table->field[$x]->name = "IDR";
	$table->field[$x]->headername = "IDR";
	$table->field[$x]->align = $F_HANDLER->LEFT;
	$table->field[$x]->hidden = true;
	$x=14;
	$table->field[$x]->name = "KURS_IDR";
	$table->field[$x]->headername = "KURS IDR";
	$table->field[$x]->align = $F_HANDLER->LEFT;
	$table->field[$x]->hidden = true;
	$x=15;
	$table->field[$x]->name = "keterangan";
	$table->field[$x]->headername = "Keterangan";
	$table->field[$x]->hidden = true;
	
	$x=16;
	$table->field[$x]->name = "idLLD";
	$table->field[$x]->hidden = true;
	$x=17;
	$table->field[$x]->hidden = true;
	$x=18;	
	$table->field[$x]->hidden = true;
	$x=19;		
	$table->field[$x]->hidden = true;
	$x=20;		
	$table->field[$x]->hidden = true;
	$x=21;		
	$table->field[$x]->hidden = true;
	
	
	$table->drawTable();
	$conn->disconnect();
}
$data = $connDB->query($sql);
$nextNo = $data->size()+1;
if($div=="pilihpeb"){
?>
</div>
<br />
<b style="font-size:13px; padding:5px;letter-spacing:2px;color:#333333;padding-bottom:10px;"><?php echo $bhs['Uang Muka'][$kdbhs]?></b>
<div style="font:tahoma 6px normal;margin-top:5px;" id="hasilBaca">
	<table cellpadding="0" cellspacing="0" border='0' style="font-size:12px; font-family:arial; color:#333333; width:100%">
	  <tr class="tbl_hdr" style="background:url(<?php echo base_url?>img/tab3.gif) repeat-x; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;"> 
		<th style="width:35px;">&nbsp;</th>
		<th style="text-align:left; ">No.</th>
		<th style="text-align:left;"><?php echo $bhs['Dana Masuk'][$kdbhs]?></th>
		<th style="text-align:left;"><?php echo $bhs['Nominal Transfer'][$kdbhs]?></th>		
		<th style="text-align:left;">Status</th>		
		<th style="text-align:left;"><?php echo $bhs['Pembayaran'][$kdbhs]?></th>		
	  </tr>
	  <tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
	  	<td style="border-bottom: 1px solid #D7D7D7">
		<input type="checkbox" id="cbx<?php echo $nextNo?>" onclick="javascript:showText(this,'<?php echo $nextNo?>')" class="cbxBiasa">
		</td>
		<td style="border-bottom: 1px solid #D7D7D7">1.</td>
		<td style="border-bottom: 1px solid #D7D7D7"><div align="LEFT" class="div_tbl" style="font-size:11px; font-family:Arial; padding:6px 0px 6px 9px;"><select name="jenisuang[]"><option value="1">Ekspor</option><option value="2">Non Ekspor</option><option value="3">Uang Muka</option></select></div></td>
		<td style="border-bottom: 1px solid #D7D7D7"><div align="LEFT" class="div_tbl" style="font-size:11px; font-family:Arial; padding:6px 0px 6px 9px;">
		<input type="text" id="ajaxMod1Text<?php echo $nextNo?>" style="text-align:right" name="uangmuka[]" class="sisadm" disabled="" onkeyup="javascript:numberFormat(this,',','','')">
		</div></td>
		<td style="border-bottom: 1px solid #D7D7D7;">
		<select name="statusUangMuka[]"><option value="">-</option><option value="1">Single PEB</option><option value="2">Multiple PEB</option></select>
		</td>
		<td style="border-bottom: 1px solid #D7D7D7;">
		<select name="jnsPembayaranUangMuka[]"><option value="">-</option><option value="01">Uang Muka Penuh</option><option value="02">Uang Muka Parsial</option></select>
		</td>
	  </tr>	
	  <tbody id="addRowArea">	  
	  </tbody>
</table>
<button type="button" class="btn_4" onclick="addRowUangMuka()">Tambah Row</button>&nbsp;<button type="button" class="btn_4" onclick="delRowUangMuka()">Hapus Row</button>
<span id="jumlahRow" jum="1"></span>
<span id="startId" jum="<?php echo $nextNo?>"></span>
</div>
</form>
<?php
}
?>

