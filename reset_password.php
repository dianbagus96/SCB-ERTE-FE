<?php
session_start();
require_once("configurl.php");
if(in_array($_SESSION["priv_session"],array("0","3","2","1"))==false){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}elseif(time()>$_SESSION['timeout_session']){	
	echo "<script> window.location.href='".base_url."err/7/".md5(7)."';</script>";exit;
}else{
	$_SESSION['timeout_session'] = TIMEOUT;
}
$data = $_REQUEST["radiopanel"];

if(trim($data)!=""){	
	require_once("dbconn.php");
	require_once("sendEmail.php");
	global $conn;
	$conn->connect();

	$cbx = split(";",$data);
	$uid = $cbx[0];		
	$npwp = str_replace('-','',str_replace('.','',$cbx[1]));
	$sql = "select * from TBLUSER WHERE USERLOGIN='$uid'";
	$data = $conn->query($sql);
	$data->next();
	$fullname = $data->get('FULLNAME');
	$email = $data->get("EMAIL");
	$groupid = $data->get('ID');
		
	function ResetPassword($min_pass,$min_num){
		$char = "QWERTYUPLKJHGFDSAZXCVBNM";
		$char_lt = "qwertyupasdfghjkzxcvbnm";
		$num = "23456789";
			$pwd = '';
			for ($i = 0; $i < $min_pass; $i++) {
			 //   $randomString .= $characters[rand(0, strlen($characters) - 1)];
			    
			    if( $i % 2 == 0){
				$randomString = $char[rand(0, strlen($char) - 1)];
				}else{
				$randomString = $char_lt[rand(0, strlen($char_lt) - 1)];
				}
			    if(($min_pass-$i) <= $min_num){
				$randomString = $num[rand(0, strlen($num) - 1)];
				}
			
				$pwd  = $pwd .	$randomString;
			}
		return $pwd;
	}
	
	$sql = "SELECT PASS_SET,ALPHA_SET FROM TBLMGTPASSWORD";
	$data = $conn->query($sql);
	$data->next();
	$min_pass = intval($data->get('PASS_SET'));
	$min_num = intval($data->get('ALPHA_SET'));
	
	$PASS=ResetPassword($min_pass,$min_num);
	
	$PASSWORD=md5($PASS);
	$parsing=explode(';',$_POST['radiopanel']);
	$uid=$parsing[0];
	$wpnpwp=str_replace('-','',str_replace('.','',$parsing[1]));
	$sql = "Update TBLUSER set PASSWORD = '$PASSWORD', EMAILSTATUS='2', LOGIN='N' Where USERLOGIN = '$uid' And NPWP = '$wpnpwp' ";	
	$hasil = $conn->execute($sql);
	$sql2 = "insert into tblhistorypass (username,gid,password,waktu) values ('$uid','$groupid','$PASSWORD',SYSDATE)";	
	$hasil2 = $conn->execute($sql2);
	if($hasil){
		
		$aktivitas = " Berhasil mereset password dari UserID : $uid";
		audit($conn,$aktivitas);
		
		$conn->disconnect();
		
		$to .= $email;				
		$subject = "Reset Password SCB E-RTE";								
		$body = "Yth. ".ucfirst($fullname).",<br>Password Anda telah Kami Reset.<br>Berikut informasinya :<br> 
				<li>Group Id : $groupid </li>
				<li>User Id : $uid </li>
				<li>Password Baru : $PASS</li><br>Silakan gunakan password baru diatas untuk mengakses account Anda pada SCB E-RTE.<br>
				Untuk keamanan silakan lakukan perubahan pada password Anda secara berkala.
				<br><br>Terimakasih<br><br><b>Admin SCB E-RTE </b><br><br>";	
		sendEmail(array('to'=>$to,'subject'=>$subject,'isi'=>$body));			
		$_SESSION['respon'] = '<table cellpadding="0" cellspacing="0" style="width:100%">
						<tr>
							<td style="border-bottom:1px solid #D7D7D7;">
								<div style="padding-bottom:9px;">
								<span style="color:#1a68a4; font-size:14px;font-weight:bold;">
								<div style="background:#E5EEF5;padding:5px;border:1px #CCC solid;">
								<img src="'.base_url.'img/accept.png" style="border:none"> Reset Password telah berhasil</div>							
								</span>
								</div>
							</td>
						</tr>
					</table>
					<table style="padding-top:18px;width:100%" cellpadding="0" cellspacing="0">
						<tr>
							<td style="background:url('.base_url.'img/tab1.png); width:20px; height:22px;">&nbsp;</td>
							<td style="background:url('.base_url.'img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">User Information</td>
						</tr>
					</table>
					<table cellpadding="0" cellspacing="0" style="font-size:11px;width:100%; font-family:arial; color:#333333;">
						<tr>
							<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>User ID</strong></span></td>
							<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">'.$uid.'</span></td>
						</tr>
						<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
							<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Password</strong></span></td>
							<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">'.$PASS.'</span></td>
						</tr>
					</table>';
	}
	echo "<script> window.location.href='".base_url."modul/user/reset';</script>";exit;
}	
if($_SESSION['respon']){
	echo $_SESSION['respon'];	
	$_SESSION['respon']="";	
}else{
	echo "<script> window.location.href='".base_url."modul/user/view';</script>";exit;
}
			?>
				<div style="margin-top:18px; width:100%; background:#F0F0F0; border: 1px solid #D7D7D7;">
					<div style="font-family:arial; font-weight:lighter; font-size:11px; padding-top:10px; padding-bottom:10px; padding-left:10px;">
						<table cellpadding="0" cellspacing="0" style="font-family:arial; font-weight:lighter; font-size:11px;">
							<tr>
								<td><img src="<?php echo base_url?>img/warning.png" style='border:none'/></td>
								<td style="padding-left:20px; font-family:arial; font-size:11px; color:#808080;">
								- Please change your password after successful login for first time to the application<br /><br />
								- Change your password periodically for your security
								</td>
							</tr>
						</table>
					</div>
				</div>
			</td>
		</tr>
	</table>
</div>
