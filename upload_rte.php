<?php
if(in_array($_SESSION['priv_session'],array('0','3'))){
		echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}else{
	
	set_time_limit(100);
	ini_set ('max_execution_time', '10000' );
	ini_set ("memory_limit","1000M");
		
	require_once("dbconn.php");
	require_once("sendEmail.php");
	include_once("library/excel/read_excel.php");
	include_once("library/excel/simplexlsx.class.php");	
	
	function timeStamp(){ 
		$xx = date("YmdHis");
		return $xx;
	}
	
	global $conn;
	$conn->connect();
	$jumErr =0;
	$wajibIsi = array(2,3,4,5,6,7,8,9,10);
	$jumField = 11;

	if ($div == "baca" && is_array($_FILES['userfile'])){
	  if (strlen($userfile) > 0){
		  $upload_dir = "files/";	
		  
		  $filename = "";	
		  $filename = $upload_dir.$_FILES['userfile']['name'];
		  $nama  = $_FILES['userfile']['name'];
		  $ext = substr($nama,strlen($nama)-4,4);
		  move_uploaded_file($_FILES['userfile']['tmp_name'], $filename);
		  if(strpos($ext,'.')>-1){		 
			  $_SESSION['typeFile'] = 'xls';
			  $data = new Spreadsheet_Excel_Reader();
			  $data->setOutputEncoding('CP1251');
			  $data->read($filename);	  
			  $row =0;		 
			  for ($i = 2; $i <= ($data->sheets[0]['numRows']); $i++) {
				  $str = trim($data->sheets[0]['cells'][$i][1]);				  
				  if(!empty($str)){
					  $tbdata .= ($row%2==0)? "<tr style='border-bottom: 1px solid #D7D7D7; background:#E5EEF5'>" : "<tr style='border-bottom: 1px solid #D7D7D7;'>";			  
					  $tbdata .= '<td valign="top" style="border-bottom: 1px solid #D7D7D7;border-right:1px solid #D7D7D7;padding:1px 1px 1px 5px" >'.($i-1).'</td>';
					  for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {						 
						 if($j<=$jumField){
							 $str = trim($data->sheets[0]['cells'][$i][$j]);
							 if(!empty($str)){																
								if($j==2 && strlen($str)!=15){
									$str = "<font style='color:#F00'>$str (harus 15 digit)</font>";
									$jumErr++;
								}else{
									if($_SESSION['npwp_session']!=$str && $j==2){
										$str = "<font style='color:#F00'>$str (bukan npwp perusahaan)</font>";
										$jumErr++;
									}
								}																		
								if( ($j==5 || $j==4 ) && (ereg('[^0-9]',$str)==true || strlen($str)!=6) ){ #kpbc, nopeb
									$str = "<font style='color:#F00;'>$str (harus bertipe numerik 6 digit)</font>";
									$jumErr++;
								}elseif($j==6){ #tglpeb
									$str = str_replace('/','-',$str);
									$jumErr +=(!ereg("^(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-([0-9]{4})$",$str))? 1 : 0;  	
									$str = (!ereg("^(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-([0-9]{4})$",$str))? 
									"<font style='color:#F00'>".$str." (format tgl harus dd-mm-yyyy)</font>" : $str; 
								}elseif($j==7 && strlen($str)!=3){ #valuta
									$str = "<font style='color:#F00;'>$str (harus berisi karakter 3 digit)</font>";
									$jumErr++;
								}elseif( $j==8 || $j==9){
									if(ereg('[^0-9.,]',$str)==true){ #dhe,fob
										$str = "<font style='color:#F00;'>$str (harus bertipe numerik)</font>";
										$jumErr++;
									}
								}elseif( $j==10 && (ereg('[^0-9]',$str)==true || strlen($str)!=4)){ #sandiketerangan
									$str = "<font style='color:#F00;'>$str (harus bertipe numerik 4 digit)</font>";
									$jumErr++;
								}												
								
								$tbdata .= '<td valign="top" style="border-bottom: 1px solid #D7D7D7; border-right: 1px solid #D7D7D7;padding:1px 1px 1px 5px" >' .$str. '</td>';
							  }else{
									if(in_array($j,$wajibIsi)){
										$str = "<font style='color:#F00;text-transform:italic'>wajib diisi</font>";
										$jumErr++;
									}									
									$tbdata .= '<td valign="top" style="border-bottom: 1px solid #D7D7D7; border-right: 1px solid #D7D7D7;padding:1px 1px 1px 5px" >' .$str. '</td>';
							  }
						  }
						
					  }
					  $tbdata .= "</tr>";
					  $row++;
				  }
			  }
			  
		  }else{
			  $_SESSION['typeFile'] = 'xlsx';
			  $data = new SimpleXLSX($filename);  
			  list($cols,) = $data->dimension(1);
			  $row =0;
			  $loop=0;
			  foreach( $data->rows(1) as $k => $r) {
				if($loop>0){
					$str  = $r[0].$r[2].$r[3].$r[4].$r[5];
					if(!empty($str)){
						  $tbdata .= ($row%2==0)? "<tr style='border-bottom: 1px solid #D7D7D7; background:#E5EEF5'>" : "<tr style='border-bottom: 1px solid #D7D7D7;'>";			  
						  $tbdata .= '<td valign="top" style="border-bottom: 1px solid #D7D7D7;border-right:1px solid #D7D7D7;padding:1px 1px 1px 5px" >'.($row+1).'.</td>';
						  for( $j = 1; $j <= $cols; $j++){
							  if($j<=$jumField){
							  	 $str  = strFilter($r[$j-1]);
								 if(!empty($str)){																
									if($j==2 && strlen($str)!=15){
										$str = "<font style='color:#F00'>$str (harus 15 digit)</font>";
										$jumErr++;
									}else{
										if($_SESSION['npwp_session']!=$str && $j==2){
											$str = "<font style='color:#F00'>$str (bukan npwp perusahaan)</font>";
											$jumErr++;
										}
									}																		
									if( ($j==5 || $j==4 ) && (ereg('[^0-9]',$str)==true || strlen($str)!=6) ){ #kpbc, nopeb
										$str = "<font style='color:#F00;'>$str (harus bertipe numerik 6 digit)</font>";
										$jumErr++;
									}elseif($j==6){ #tglpeb
										$str = str_replace('/','-',$str);
										$jumErr +=(!ereg("^(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-([0-9]{4})$",$str))? 1 : 0;  	
										$str = (!ereg("^(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-([0-9]{4})$",$str))? 
										"<font style='color:#F00'>".$str." (format tgl harus dd-mm-yyyy)</font>" : $str; 
									}elseif($j==7 && strlen($str)!=3){ #valuta
										$str = "<font style='color:#F00;'>$str (harus berisi karakter 3 digit)</font>";
										$jumErr++;
									}elseif( $j==8 || $j==9){
										if(ereg('[^0-9.,]',$str)==true){ #dhe,fob
											$str = "<font style='color:#F00;'>$str (harus bertipe numerik)</font>";
											$jumErr++;
										}
									}elseif( $j==10 && (ereg('[^0-9]',$str)==true || strlen($str)!=4)){ #sandiketerangan
										$str = "<font style='color:#F00;'>$str (harus bertipe numerik 4 digit)</font>";
										$jumErr++;
									}												
									
									$tbdata .= '<td valign="top" style="border-bottom: 1px solid #D7D7D7; border-right: 1px solid #D7D7D7;padding:1px 1px 1px 5px" >' .$str. '</td>';
								  }else{
										if(in_array($j,$wajibIsi)){
											$str = "<font style='color:#F00;text-transform:italic'>wajib diisi</font>";
											$jumErr++;
										}									
										$tbdata .= '<td valign="top" style="border-bottom: 1px solid #D7D7D7; border-right: 1px solid #D7D7D7;padding:1px 1px 1px 5px" >' .$str. '</td>';
								  }
							  }
						  }
						  $tbdata .= "</tr>";
						  $row++;
					  }			
				}
				 $loop++;
			  }	
		}	
	  }else{
		  $row = 1;
		  echo "<script language=\"javascript\">";
		  echo "$(document).ready(function(){";
			echo "  jAlert('Pilih Data anda Terlebih dahulu.');";  
		  echo "})";	  
		  echo "</script>";
	  }
	}
	
	if($div == "doupload"){	
		$success=0;
		$failed =0;
		$total=0;
		$npwp=  strFilter($_POST['npwp']);
		$norek =  strFilter($_POST['norek']); 
		
		if($_SESSION['typeFile']=='xls'){
			if(file_exists($filename)){			
				$data = new Spreadsheet_Excel_Reader();
				$data->setOutputEncoding('CP1251');
				$data->read($filename);
				$x =0;
				$noBarisDataSalah = array();
				
				for ($i = 2; $i <= ($data->sheets[0]['numRows']); $i++) {
					$str = trim($data->sheets[0]['cells'][$i][1]);
					if(!empty($str)){
						$y = 0;
						$xxx = 0;
						for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {						 
							 if($j<=$jumField){
								 $str = trim($data->sheets[0]['cells'][$i][$j]);
								 if(!empty($str)){																
									if($j==2 && strlen($str)!=15){
										$xxx++;
									}else{
										if($_SESSION['npwp_session']!=$str && $j==2){
											$xxx++;
										}
									}																		
									if( ($j==5 || $j==4 ) && (ereg('[^0-9]',$str)==true || strlen($str)!=6) ){ #kpbc, nopeb
										$xxx++;
									}elseif($j==6){ #tglpeb
										$str = str_replace('/','-',$str);
										$xxx +=(!ereg("^(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-([0-9]{4})$",$str))? 1 : 0;  											
									}elseif($j==7 && strlen($str)!=3){ #valuta
										$xxx++;
									}elseif( $j==8 || $j==9){
										if(ereg('[^0-9.,]',$str)==true){ #dhe,fob
											$xxx++;
										}
									}elseif( $j==10 && (ereg('[^0-9]',$str)==true || strlen($str)!=4)){ #sandiketerangan
										$xxx++;
									}										
									$dataUpload[$x][] = $str;										
								  }else{
										if(in_array($j,$wajibIsi)){
											$xxx++;
										}																		
								  }
							  }
							
						  }						
						if($xxx>0) $noBarisDataSalah[] = $x;				
						$x++;							
					}					
				}						
				$numField = count($dataUpload[0]);
				$err=0;
				
				for($z=0;$z<$x;$z++){
					if(in_array($z,$noBarisDataSalah)==false){
						$no_identifikasi = $dataUpload[$z][0]; #rte
						$npwp = $dataUpload[$z][1]; #peb
						$penerima_dhe = $dataUpload[$z][2]; #peb
						$kpbc = $dataUpload[$z][3]; #peb
						$nopeb = $dataUpload[$z][4]; #peb
						$tglpeb = $dataUpload[$z][5]; #peb
						$valuta = $dataUpload[$z][6]; #rte
						$dhe = str_replace(",","",$dataUpload[$z][7]); #rte
						$nilai_peb = str_replace(",","",$dataUpload[$z][8]); #rte
						$sandi_ket = $dataUpload[$z][9]; #rte
						$noref = $dataUpload[$z][10]; #danamasuk						
						
						$sql = "Insert Into tblfcRTE (no_identifikasi,valuta,nominal_dhe,nominal,Sandi_Keterangan,userInput,status)";
						#$data = $conn->execute($sql);
						$sql = "Insert Into tbldmpeb (NPWP,NAMA_EKSPORTIR,KPBC,NO_PEB,TGL_PEB,FOB,FLAG_USED)";
						#$data = $conn->execute($sql);
						$sql = "SELECT IDDANAMASUK FROM TBLDMDANAMASUK WHERE REFERENCE_NUMBER=''";
					}
				}
				$total = $x; 
				$failed =  count($noBarisDataSalah);
				$success = $total-$failed;
				unlink($filename);
			}
		}elseif($_SESSION['typeFile']=='xlsx'){
			$data = new SimpleXLSX($filename);  
			list($cols,) = $data->dimension(1);
			$loop=0;
			$noBarisDataSalah = array();
			$x=0;
			foreach( $data->rows(1) as $k => $r) {
			  if($loop>0){		  
				  $str  = $r[1];			
			  	  if(!empty($str)){																
						if($j==2 && strlen($str)!=15){
							$xxx++;
						}else{
							if($_SESSION['npwp_session']!=$str && $j==2){
								$xxx++;
							}
						}																		
						if( ($j==5 || $j==4 ) && (ereg('[^0-9]',$str)==true || strlen($str)!=6) ){ #kpbc, nopeb
							$xxx++;
						}elseif($j==6){ #tglpeb
							$str = str_replace('/','-',$str);
							$xxx +=(!ereg("^(0[1-9]|1[0-9]|2[0-9]|3[01])-(0[1-9]|1[012])-([0-9]{4})$",$str))? 1 : 0;  											
						}elseif($j==7 && strlen($str)!=3){ #valuta
							$xxx++;
						}elseif( $j==8 || $j==9){
							if(ereg('[^0-9.,]',$str)==true){ #dhe,fob
								$xxx++;
							}
						}elseif( $j==10 && (ereg('[^0-9]',$str)==true || strlen($str)!=4)){ #sandiketerangan
							$xxx++;
						}										
						$dataUpload[$x][] = $str;										
				  }else{
						if(in_array($j,$wajibIsi)){
							$xxx++;
						}																		
			      }					
			   }		 
			  $loop++;
			}				
			$numField = count($dataUpload[0]);
				$err=0;
				for($z=0;$z<$x;$z++){
					if(in_array($z,$noBarisDataSalah)==false){
						$no_identifikasi = $dataUpload[$z][0]; #rte
						$npwp = $dataUpload[$z][1]; #peb
						$penerima_dhe = $dataUpload[$z][2]; #peb
						$kpbc = $dataUpload[$z][3]; #peb
						$nopeb = $dataUpload[$z][4]; #peb
						$tglpeb = $dataUpload[$z][5]; #peb
						$valuta = $dataUpload[$z][6]; #rte
						$dhe = str_replace(",","",$dataUpload[$z][7]); #rte
						$nilai_peb = str_replace(",","",$dataUpload[$z][8]); #rte
						$sandi_ket = $dataUpload[$z][9]; #rte
						$noref = $dataUpload[$z][10]; #danamasuk
						
						print_r($dataUpload[$z]);exit;
						$sql = "Insert Into tblfcRTE (no_identifikasi,valuta,nominal_dhe,nominal,Sandi_Keterangan,userInput,status)";
						#$data = $conn->execute($sql);
						$sql = "Insert Into tbldmpeb (NPWP,NAMA_EKSPORTIR,KPBC,NO_PEB,TGL_PEB,FOB,FLAG_USED)";
						#$data = $conn->execute($sql);
						$sql = "SELECT IDDANAMASUK FROM TBLDMDANAMASUK WHERE REFERENCE_NUMBER=''";
					}
				}
				$total = $x; 
				$failed =  count($noBarisDataSalah);
				$success = $total-$failed;
				unlink($filename);
		}
		$message = "<fieldset style='width:97.8%;border:1px #CCC solid'>
						<legend><b>Informasi</b></legend> 
						<ul style='margin-left:0px'>
						<li>Total record : <b>".($total)."</b></li>
						<li>Berhasil upload : <b> ".($success)." record </b></li>";
		$message .= 	($failed>0)? "<li>Gagal upload : <b style='color:#F00'>".($failed)." record</b></li>" : "";
		$message .=		"</ul>						
					</fieldset>";	
	
		
		$aktivitas = "Mengupload sejumlah $x RTE, ket: ".($x-$err)." berhasil ,$err gagal";
		audit($conn,$aktivitas);		
	}
	$conn->disconnect();
	?>
	<table cellpadding="0" cellspacing="0" style="width:100%">
		<tr>
			<td style="border-bottom:1px solid #D7D7D7;">
				<div style="padding-bottom:9px;">
				<span style="color:#1a68a4; font-size:16px; font-weight:bold;">
				Upload RTE</span><br />                            
			  </div>                           
			</td>
		</tr>
	</table>				
<strong  style="font-size:11px; font-family:arial; color:#333333;">
<label style="cursor:pointer"></label>
	</strong>
	<div style="height:18px;">&nbsp;</div>				
	
	<table cellpadding="0" cellspacing="0" style="width:100%">
		<tr>
			<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
			<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">File xls / xlsx SSP (Format)</td>
		</tr>
	</table>
	
	<form name="frmUpload" id="frmUpload" method="post" enctype="multipart/form-data" action="<?php echo base_url?>modul/rte/baca">
	<table cellpadding="0" cellspacing="0" style="font-size:11px; font-family:arial; color:#333333; width:100%">
		<tr>
			<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>File</strong></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;">
				<table cellpadding="0" cellspacing="0">
				<tr>
					<td style="padding-left:7px;">
						<span style="padding:6px 0px 6px 9px;"><input name="userfile" type="file" size="60" id="userfile"></span>
					</td>
					<td>									
						<a href="#" class="htmlbutton" onclick="cekUpload()">
							<span style="margin-top:-2px;"> Baca</span>
						</a>
					</td>
				</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="2"><font style="font-size:12px; color:#039"> <?php echo $message;?></font></td>
		</tr>
	</table>
	</form>
	
	<form name="frmUploadz" id="frmUploadz" method="post" action="<?php echo base_url?>modul/rte/doupload">
	<input type="hidden" name="filename" value="<?php echo $filename;?>" />
	<input name="content" type="hidden" value="<?php echo $content; ?>" />
	<br />
	
	<?php
	if ($div == "baca" && is_array($_FILES['userfile']) && $row>0){
	?>	 
	<table cellpadding="0" cellspacing="0" style="width:100%" >
		<tr>
			<td style="padding-left:7px; width:10px;">
				<a href="javascript:cekBacaUpload('taxpayer')" class="htmlbutton">
					<span style="margin-top:-2px;">Upload</span>
				</a>																	
			</td>
			<td>
			<a href="javascript:history.back();" class="htmlbutton">
				<span style="margin-top:-2px;"> Cancel</span>
			</a>																	
			</td>                       
		</tr>                    
	</table>
	<br />
	
	</form>	
	<div style="font:tahoma 6px normal;" id="hasilBaca">
	<table cellpadding="0" cellspacing="0" border='0' style="font-size:10px; font-family:arial; color:#333333; width:100%">
	  <tr><td colspan="36" bordercolor="#FFFFFF" style="font-weight:bold; font-size:11px;">Jumlah Data Upload : <?php echo $row;?><hr /></td></tr>
	  <tr class="tbl_hdr" style="background:url(<?php echo base_url?>img/tab3.gif) repeat-x; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;"> 
		<th>No.</th>
		<th><?php echo $bhs['No. Identifikasi'][$kdbhs]?></th>
		<th>NPWP</th>
		<th><?php echo $bhs['Nama Penerima DHE'][$kdbhs]?></th>
		<th><?php echo $bhs['Sandi Kantor Pabean'][$kdbhs]?></th>
		<th><?php echo $bhs['No. PEB'][$kdbhs]?></th>
		<th><?php echo $bhs['Tanggal PEB'][$kdbhs]?></th>
		<th><?php echo $bhs['Valuta'][$kdbhs]?></th>
		<th><?php echo $bhs['Nilai DHE'][$kdbhs]?></th>
		<th><?php echo $bhs['Nilai PEB'][$kdbhs]?></th>
		<th><?php echo $bhs['Sandi Keterangan'][$kdbhs]?></th>
		<th><?php echo $bhs['No. Ref'][$kdbhs]?></th>
	  </tr>
	   <?php
			echo $tbdata;
			if($jumErr>0){							
		?>
			 <tfoot>
				<tr>
					<td colspan="18" style="color:#F00;font-size:11px; font-style:italic "><br>Catatan : <br>Perbaiki Kesalahan Diatas!</td>
				</tr>
			</tfoot>
		<?php
			}
		?>
	</table>
	</div>
	<?php
	}
}
?>
