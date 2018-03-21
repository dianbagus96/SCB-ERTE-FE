<?php
if(in_array($_SESSION["priv_session"],array("5","4"))==false || substr($_SESSION["AKSES"],3,1)!="1"){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}else{
	set_time_limit(100);
	ini_set ('max_execution_time', '10000' );
	ini_set ("memory_limit","1000M");	
	require_once("dbconndb.php");
	$jumField = 18;
	$npwp_all = $_SESSION['npwp_many_arr_session'];
	if ($div == 'baca'){
		if(!is_array($_FILES['userfile'])){ echo "<script> window.location.href='".base_url."modul/peb/upload';</script>";exit;}	
		echo '<script type="text/javascript" src="js/jquery.js"></script> 
				<link type="text/css" href="js/jAlert/jquery.alerts.css" rel="stylesheet" />	   
				<script type="text/javascript" src="js/jAlert/jquery.alerts.js"></script>';
		$name = $_FILES['userfile']['name'];
		$mime = $_FILES['userfile']['type'];

		$format = explode('.',$name); $format = $format[1];		
		if(strtolower($format)=='txt'){
			if($name!="FT30H.TXT" && $name!="FT30DK.TXT" && $mime!="text/plain"){			  
				echo "<script type='text/javascript'>"; 
				echo "	$(document).ready(function(){\n";
				echo "		jAlert('PEB Document is incorrect!','',function(r){\n";
				echo "			if(r==true){ window.location.href='".base_url."modul/peb/upload'; }\n";				
				echo "		});\n";
				echo "	});\n";
				echo "</script>";	
				exit;
			}else{
				$filename = "files/".$name;
				move_uploaded_file($_FILES['userfile']['tmp_name'], $filename);			
				$handle = fopen($filename, "r");	
				$handlecek = fopen($filename, "r");	
				$tbdata="";			
				$style = 'style="border-bottom: 1px solid #D7D7D7; border-right: 1px solid #D7D7D7;padding:5px"';				
				$cekdata = fgets($handlecek,  6000000);				
				$cekjm = substr($cekdata,75,100);
				if(strlen(trim($cekjm))>0){						
					$errnpwp=0;
					while (!feof($handle)) {
						$data = fgets($handle,6000000);				
						if(substr($data,0,1)!=""){
							$car 	= substr($data,0,26);
							$npwp 	= substr($data,45,15);
							
							if(!in_array($npwp,$npwp_all)){
								$npwp  = "<font color='#FF0000'>$npwp *</font>";
								$errnpwp++;
							}
							$nama 	= substr($data,60,50);
							$kpbc 	= substr($data,690,6);
							$valuta = substr($data,809,3);
							$fob	= substr($data,846,19);
							$nopeb	= substr($data,1121,6);
							$tgl	= substr($data,1127,8);																					
							$tglpeb = substr($tgl,6,2)."-".substr($tgl,4,2)."-".substr($tgl,0,4);
							
							$tbdata.= ($row%2==0)? "<tr style='border-bottom: 1px solid #D7D7D7; background:#E5EEF5'>" : "<tr style='border-bottom: 1px solid #D7D7D7;'>";	
							$tbdata.='<td '.$style.' >'.(++$row).'.</td>';
							$tbdata.='<td '.$style.' >'.$car.'</td>';
							$tbdata.='<td '.$style.' >'.$npwp.'</td>';
							$tbdata.='<td '.$style.' >'.$nama.'</td>';
							$tbdata.='<td '.$style.' >'.$kpbc.'</td>';
							$tbdata.='<td '.$style.' >'.$valuta.'</td>';
							$tbdata.='<td '.$style.' align="right" >'.$fob.'</td>';					
							$tbdata.='<td '.$style.' >'.$nopeb.'</td>';
							$tbdata.='<td '.$style.' >'.$tglpeb.'</td>';
							$tbdata.='</tr>';							
						}														
					}#while   
					 
				}else{
					$errnpwp=0;
					while (!feof($handle)) {
						$data = fgets($handle, 6000000);							
						$kdDok	= substr($data,31,3);
						if(substr($data,0,1)!="" && ($kdDok=='380')){
							$car 	= substr($data,0,26);
							$id 	= substr($data,30,1);								
							$noDok 	= substr($data,34,30);
							$tgl 	= substr($data,64,8);
							$tglDok = substr($tgl,6,2)."-".substr($tgl,4,2)."-".substr($tgl,0,4);
							
							$tbdata.= ($row%2==0)? "<tr style='border-bottom: 1px solid #D7D7D7; background:#E5EEF5'>" : "<tr style='border-bottom: 1px solid #D7D7D7;'>";	
							$tbdata.='<td '.$style.' >'.(++$row).'.</td>';
							$tbdata.='<td '.$style.' >'.$car.'</td>';								
							$tbdata.='<td '.$style.' >'.$id.'</td>';
							$tbdata.='<td '.$style.' >'.$kdDok.'</td>';
							$tbdata.='<td '.$style.' >'.$noDok.'</td>';
							$tbdata.='<td '.$style.' >'.$tglDok.'</td>';
							$tbdata.='</tr>';							
						}
																
					}#while   
				}           
			}#endif				
		}else{
			echo "<script type='text/javascript'>"; 
			echo "	$(document).ready(function(){\n";
			echo "		jAlert('PEB Document is incorrect!','',function(r){\n";
			echo "			if(r==true){ window.location.href='".base_url."modul/peb/upload'; }\n";			
			echo "		});\n";
			echo "	});\n";
			echo "</script>";	
			exit;
		}
	}elseif($div == 'doupload'){		
		if(trim($_POST['namaFile'])==""){ echo "<script> window.location.href='".base_url."modul/peb/upload';</script>";exit;}	
		function timeStamp(){
			$xx = date("YmdHis");
			return $xx;
		}		
		
		if($_POST['format']=='txt'){              
			$filename = "files/".$_POST['namaFile'];
			$handle = fopen($filename, "r");
			$handlecek = fopen($filename, "r");		
			$errnpwp=0;
			$cekdata = fgets($handlecek,6000000);				
			$cekjm = substr($cekdata,75,100);			
			if(strlen(trim($cekjm))>0){					
				$error=0;	
				$row=0;	
				$connDB->connect();
				$line = 0;
				$akhir = 0;
				while (!feof($handle)) {
					  $data = fgets($handle,  6000000);						
					  if(substr($data,0,1)!=""){
					  	  $row++;
						  $car = strFilter(substr($data,0,26));	
						  $npwp = strFilter(substr($data,45,15));
						  if(in_array($npwp,$npwp_all)){					  		   						  
							  $sql = "select CAR from tbldmpeb where CAR='$car'";					  
							  $d = $connDB->query($sql);						 
							  if(!$d->next()){								 
									$nama 	= strFilter(substr($data,60,50));
									$alamat 	= strFilter(substr($data,110,50));
									$kpbc 	= strFilter(substr($data,690,6));
									$valuta 	= strFilter(substr($data,809,3));
									$fob		= strFilter(substr($data,846,19));
									$nopeb	= strFilter(substr($data,1121,6));
									$tgl		= strFilter(substr($data,1127,8));																					
									$tglpeb 	= strFilter(substr($tgl,6,2)."-".substr($tgl,4,2)."-".substr($tgl,0,4));	
																  
									$sql = "insert into tbldmpeb(CAR,NPWP,Nama_Eksportir,KPBC,NO_PEB,TGL_PEB,FOB,Alamat_Eksportir,flag_used,valuta,Source,DTCREATED,GROUPID,CREATEDBY) 
									values('$car','$npwp','$nama','$kpbc','$nopeb',to_date('$tglpeb','DD-MM-YYYY'),$fob,'$alamat','0','$valuta','1',SYSDATE,'".strtoupper($_SESSION['grpID'])."','".strtoupper($_SESSION['uid'])."')";			 
									$write_txt .= "PEB|".$car."|".$npwp."|".$nama."|".$alamat."|".$kpbc."|".$nopeb."|".$tglpeb."|".$valuta."|".$fob."|".strtoupper($_SESSION['uid'])."|".date('d-m-Y')."|||0\r\n";
									  if ($line == 20 ){
										writetxt($write_txt,'DOKPEB.',$_SESSION['grpID']);
										$cekpoint = $akhir;
										$line = 0;
										$write_txt='';
									  }
									$connDB->execute($sql);
									$line++;
									$akhir++;
							  }else{
								  ++$error;  
							  }
						  }else{
						  	  $errnpwp++;							  
						  }
					  }														
				
				  }#while
				   if ($akhir - $cekpoint <20 && strlen($write_txt)>0 ){
					writetxt($write_txt,'DOKPEB.',$_SESSION['grpID']);
					$write_txt='';
				   }
				 
				  $error +=$errnpwp;
				
			}else{
				$error=0;
				$errorcar=0;	
				$row=0;	
				$connDB->connect();
				$line = 0;
				$akhir = 0;
				while (!feof($handle)) {
					  $data = fgets($handle,  6000000);						 
						$kdDok	= substr($data,31,3);							
						if(substr($data,0,1)!="" && ($kdDok=='380'|| $kdDok=='111')){
							$row++;
							$car 	= substr($data,0,26);
							$sql = "select CAR from tbldmPEB where CAR = '$car'";
							$dtpeb = $connDB->query($sql);
							if($dtpeb->next()){								
								$id 	= substr($data,30,1);								
								$noDok 	= trim(substr($data,34,30));
								$tgl 	= substr($data,64,8);
								$tglDok = substr($tgl,6,2)."-".substr($tgl,4,2)."-".substr($tgl,0,4);
								
								$sql = "select CAR from tblPebDok where CAR = '$car' and id='$id' and kdDok='$kdDok'";
								$d = $connDB->query($sql);								
								if(!$d->next()){
									$sql = "insert into tblPebDok (car,id,kdDok,noDok,tglDok) values('$car',$id,'$kdDok','$noDok',to_date('$tglDok','DD-MM-YYYY'))";
									$hasil = $connDB->execute($sql);
									$write_txt .= "INV|".$car."|".$inv."|".trim($_POST['tglInv_'.$indexTanggal])."|".$kdDok."\r\n";
									  if ($line == 20 ){
										writetxt($write_txt,'DOKPEB.',$_SESSION['grpID']);
										$cekpoint = $akhir;
										$line = 0;
										$write_txt='';
									  }
								}else{
									$errorcar++;  
								}
							}else{
								$error++; 
							}
						}							
				}#while
				 if ($akhir - $cekpoint <20 && strlen($write_txt)>0 ){
					writetxt($write_txt,'DOKPEB.',$_SESSION['grpID']);
					$write_txt='';
				   }
				   
				$error +=$errorcar;
			}
			
			
			$connDB->disconnect();
			require_once("dbconn.php");
			$conn->connect();    
			
			$aktivitas = "Mengupload sejumlah $x PEB, ket: ".($row-$error)." berhasil ,".$error." gagal";
			audit($conn,$aktivitas); 
			$conn->connect(); 
		}
	}   
	
	?>
	<table cellpadding="0" cellspacing="0" style="width:100%">
		<tr>
			<td style="border-bottom:1px solid #D7D7D7;">
				<div style="padding-bottom:9px;">
				<span style="color:#1a68a4; font-size:16px; font-weight:bold;">
				Upload PEB
				</span><br />                            
				</div>                           
			</td>
		</tr>
	</table>				
	<div style="height:18px;">&nbsp;</div>				
	<table cellpadding="0" cellspacing="0" style="width:100%">
		<tr>
			<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
			<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">File PEB</td>
		</tr>
	</table>				
	<form name="frmUpload" id="frmUpload" method="post" enctype="multipart/form-data" action="<?php echo base_url?>modul/peb/baca">
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
							<span style="margin-top:-2px;"><?php echo $bhs['Baca'][$kdbhs]?></span>
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
	<form action="<?php echo base_url?>modul/peb/doupload" method="post" id="upload">           
	<?php
	if($div == 'baca'){		
		if(strtolower($format)=='txt'){	
			if($noDok){
				?>
				<br />
				<fieldset style="border:1px solid #CCC; font-size:12px;color:#036;font-family:'Trebuchet MS', Arial, Helvetica, sans-serif">
				<legend><b><?php echo $bhs['note6'][$kdbhs]?></b></legend>
					<li style='margin-left:15px;'><?php echo $bhs['note4'][$kdbhs]?><b> <?php echo $_FILES['userfile']['name'];?></b></li>
					<li style='margin-left:15px;'><?php echo $bhs['note5'][$kdbhs]?><b> <?php echo $row;?></b><br/></li>
					<?php
						echo ($errnpwp>0)?"<li style='margin-left:15px;'>".$bhs['errpeb1'][$kdbhs]."</li><br />":"";
					?>
					<input type="hidden" name="namaFile" value="<?php echo $_FILES['userfile']['name']?>" />
					<input type="hidden" name="format" value="txt" />
					<input type="hidden" name="btnUpload" value="Upload" />
					<table><tr><td>                
					<a href="#" class="htmlbutton" onclick="javascript:$('#upload').submit()">
						<span style="margin-top:-2px;">Upload</span>
					</a></td><td>																	
					<a href="javascript:location.href='<?php echo base_url?>modul/peb/upload'" class="htmlbutton">
						<span style="margin-top:-2px;"> <?php echo $bhs['Batal'][$kdbhs]?></span>
					</a></td></tr></table>	
				</fieldset>
				<br />
				<div style="font:tahoma 7px normal;" id="hasilBaca">            						   
				<table cellpadding="0" cellspacing="0" border='0' style="font-size:10px; font-family:arial; color:#333333; width:100%">
				 <tr class="tbl_hdr" style="background:url(<?php echo base_url?>img/tab3.gif) repeat-x; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;"> 
					<th >No.</th>
					<th>CAR</th>
					<th>ID</th>
					<th>KODE DOKUMEN</th>
					<th>NOMOR DOKUMEN</th>
					<th>TGL DOKUMEN</th>
				  </tr>
				   <?php
						echo $tbdata;
					?>
				</table>	
				</div>
			<?php
			}else{?>
				<br />
				<fieldset style="border:1px solid #CCC; font-size:12px;color:#036;font-family:'Trebuchet MS', Arial, Helvetica, sans-serif">
				<legend><b><?php echo $bhs['note6'][$kdbhs]?></b></legend>
					<li style='margin-left:15px;'><?php echo $bhs['note4'][$kdbhs]?><b> <?php echo $_FILES['userfile']['name'];?></b></li>
					<li style='margin-left:15px;'><?php echo $bhs['note5'][$kdbhs]?><b> <?php echo $row;?></b><br/></li>
					<?php
						echo ($errnpwp>0)?"<li style='margin-left:15px;'>".$bhs['errpeb1'][$kdbhs]."</li><br />":"";
					?>
					<input type="hidden" name="namaFile" value="<?php echo $_FILES['userfile']['name']?>" />
					<input type="hidden" name="format" value="txt" />
					<input type="hidden" name="btnUpload" value="Upload" />
					<table><tr><td>                
					<a href='#' class='htmlbutton' onclick='javascript:$("#upload").submit()'><span style='margin-top:-2px;'>Upload</span></a>
					</td><td>																	
					<a href="javascript:location.href='<?php echo base_url?>modul/peb/upload'" class="htmlbutton">
						<span style="margin-top:-2px;"> <?php echo $bhs['Batal'][$kdbhs]?></span>
					</a></td></tr></table>	
				</fieldset>
				<br />
				<div style="font:tahoma 7px normal;" id="hasilBaca">            						   
				<table cellpadding="0" cellspacing="0" border='0' style="font-size:10px; font-family:arial; color:#333333; width:100%">
				 <tr class="tbl_hdr" style="background:url(<?php echo base_url?>img/tab3.gif) repeat-x; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;"> 
					<th>No.</th>
					<th>CAR</th>
					<th>NPWP</th>
					<th><?php echo $bhs['Eksportir'][$kdbhs]?></th>
					<th>KPBC</th>
					<th><?php echo $bhs['Valuta'][$kdbhs]?></th>    
					<th>FOB</th>
					<th><?php echo $bhs['No. PEB'][$kdbhs]?></th>
					<th><?php echo $bhs['Tanggal PEB'][$kdbhs]?></th>
				  </tr>
				   <?php
						echo $tbdata;
					?>
				</table>	
				</div>			
			<?php
			}
		}
		echo "</form>";
	}elseif($div == 'doupload'){
		?>
		 <br />
		<fieldset style="border:1px solid #CCC; font-size:12px;color:#036;font-family:'Trebuchet MS', Arial, Helvetica, sans-serif">
		<legend><b><?php echo $bhs['note7'][$kdbhs]?></b></legend>
			<li style='margin-left:15px;'><?php echo $bhs['note8'][$kdbhs]?><b> <?php echo $row;?></b><br/></li>
			<li style='margin-left:15px;'><?php echo $bhs['note9'][$kdbhs]?><b> <?php echo ($row-$error)?></b><br/></li>	           
		<?php 
		if($error){ 			
			if(strlen(trim($cekjm))>0){
				if($error-$errnpwp>0){
					echo " <li style='margin-left:15px;'>".$bhs['errpeb3'][$kdbhs]."<b style='color:#FF0000'> ".($error-$errnpwp)."</b><br/></li>";
				}
				if($errnpwp){ 
					echo " <li style='margin-left:15px;'>".$bhs['errpeb4'][$kdbhs]."<b style='color:#FF0000'> ".($errnpwp)."</b><br/></li>";
				}				
			}else{
				if($error-$errorcar>0){
					echo " <li style='margin-left:15px;'>".$bhs['errpeb4'][$kdbhs]."<b style='color:#FF0000'> ".($error-$errorcar)."</b><br/></li>";
				}
				if($errorcar){ 
					echo " <li style='margin-left:15px;'>".$bhs['errpeb5'][$kdbhs]."<b style='color:#FF0000'> ".($errorcar)."</b><br/></li>";	
				}	
			}
		}
		?>
		</fieldset>
		<br />		
		<?php	
	}else{                  	
		?>
		<br />
		<fieldset style="border:1px solid #CCC; font-size:12px;color:#036;font-family:'Trebuchet MS', Arial, Helvetica, sans-serif">
			<b><?php echo $bhs['informasi'][$kdbhs];?></b>
			<li style='margin-left:15px;'><?php echo $bhs['note2'][$kdbhs]?></li>
			<li style='margin-left:15px;'><?php echo $bhs['note3'][$kdbhs]?></li>		
			<br />	                    
		   <button type="button" class="btn_6" onclick="window.open('<?php echo base_url."download.php"?>','_parent')" style="width:260px;">
			 Download Tutorial
		   </button>
		</fieldset>
		
	<?php
	}
	?>
	</form>   
<?php
}
?>
