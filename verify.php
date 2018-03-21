<?php
session_start();
require_once("configurl.php");
if($_POST["uid"]){		
	require_once("dbconn.php");	
	function cekStr($str) {
		$search = array ('@<script[^>]*?>.*?</script>@si', // Strip out javascript
						'@<[\/\!]*?[^<>]*?>@si',          // Strip out HTML tags
						'@([\r\n])[\s]+@',                // Strip out white space
						'@&(quot|#34);@i',                // Replace HTML entities
						'@&(amp|#38);@i',
						'@&(lt|#60);@i',
						'@&(gt|#62);@i',
						'@&(nbsp|#160);@i',
						'@&(iexcl|#161);@i',
						'@&(cent|#162);@i',
						'@&(pound|#163);@i',
						'@&(copy|#169);@i',
						'@&#(\d+);@e'
						);                    // evaluate as php
		
		$replace = array ('',
						 '',
						 '\1',
						 '"',
						 '&',
						 '<',
						 '>',
						 ' ',
						 chr(161),
						 chr(162),
						 chr(163),
						 chr(169),
						 'chr(\1)');		
		return preg_replace($search, $replace, $str);		
	}
		
	function setAktif($conn){
	
		$user = trim($_SESSION['uid_session']);
		$group = trim($_SESSION['ID']);	
		$q = "update TBLUSER set LOGIN='Y', LAST_LOGIN=SYSDATE where USERLOGIN='$user' and ID='$group'";
		$conn->execute($q);	
	}
		
	$uid = strFilter(cekStr($_POST["uid"]));
	$grpID = strtoupper(strFilter(cekStr($_POST["grpID"])));
	$pwd = $_POST["pwdhash"];
	$sequel = "select usr.ID, usr.NPWP, usr.USERLOGIN, usr.USERPRIV, com.ADDRESS, com.CITY, com.NAMA, usr.EMAIL, usr.FULLNAME, com.ZIPCODE, com.PIC, com.PIC_EMAIL,
				com.PIC_PHONE, com.FAX_NUMBER, usr.BRANCHCODE, usr.AKSES, usr.PASSWORD, usr.EMAILSTATUS, usr.NOREK, usr.CSMEMAIL, com.GROUP_ACCOUNT, usr.CHECKER,
				tg.GROUPID, td.DEPTID, usr.FILEUPLOAD,tg.PIC as PIC_GROUP,td.PIC as PIC_DEPT,usr.TOKENID, usr.TOKENSTATUS, TO_DATE(usr.LAST_CHANGEPASS,'dd-MM-yy')  -  TRUNC(SYSDATE) as CHANGE_PASS
				from TBLUSER usr LEFT JOIN TCOMPANY com 
			on usr.NPWP = com.IDPAYEE AND usr.NPWP=com.NPWP LEFT JOIN TBLGROUP tg on usr.NPWP=tg.NPWP and tg.USERS like '%,'+usr.USERLOGIN+',%' 
			left join TBLDEPT td on tg.GROUPID=td.GROUPID AND lower(td.CORPID)='".strtolower($grpID)."' and td.USERS like '%,'+usr.USERLOGIN+',%' 
			where (lower(usr.USERLOGIN) = '".strtolower($uid)."')  And lower(usr.ID) = '".strtolower($grpID)."'";		
	#die($sequel);
	$conn->connect();	
	$data = $conn->query($sequel);
	//exit;
	if($data->next()){     
		//berarti ketemu		
		$verified = "True";
		$npwp_session = $data->get("NPWP");
		$uid_session = $data->get("USERLOGIN");
		$priv_session = $data->get("USERPRIV");
		$nmcomp_session = $data->get("NAMA");
		$email_session = $data->get("EMAIL");
		$nmuser_session = $data->get("FULLNAME");
		$brachsCode = $data->get("BRANCHCODE");
		$checker_session = $data->get("CHECKER").$data->get("PIC_GROUP").",".$data->get("PIC_DEPT").",";
		$groupid_session = $data->get("GROUPID");
		$deptid_session =  $data->get("DEPTID");
		$fileupload_session=  $data->get("FILEUPLOAD");
		$last_changepass =  intval($data->get("CHANGE_PASS"));
		
		//New Add
		$zipcode=$data->get("ZIPCODE");
		$pic = $data->get("PIC");
		$picEmail = $data->get("PIC_EMAIL");
		$picPhone = $data->get("PIC_PHONE");
		$picFax = $data->get("FAX_NUMBER");
		
		$NAMA_ses=$data->get("NAMA");
		$ADDRESS_SES=$data->get("ADDRESS");
		$CITY_ses=$data->get("CITY");
		$ID=$data->get("ID");
		$AKSES=$data->get("AKSES");
		$pass=$data->get("PASSWORD");
		$emailstatus=$data->get("EMAILSTATUS");
		$CSMEmail = $data->get('CSMEMAIL');
		$group_account = $data->get('GROUP_ACCOUNT');	

		$tokenid = $data->get('TOKENID');
		$tokenstatus = $data->get('TOKENSTATUS');	
		//echo $group_account;exit;
		if($group_account=='0' or trim($group_account)==''){			
			//echo "masuk sini";exit;
			$noRek = "";
			if(in_array($priv_session,array('0','3'))){
				$noRek = "'".$data->get("NOREK")."'";
			}else{
				$sql = "select ACCOUNT from TACCOUNT WHERE upper(GROUPID)='".strtoupper(trim($grpID))."'";		
				//echo $sql;exit;
				$rek = $conn->query($sql); 
				while($rek->next()){ $noRek .= "'".$rek->get('ACCOUNT')."',"; }						
				$noRek = substr($noRek,0,strlen($noRek)-1);
				$noRek = (strlen($noRek)>0)?$noRek : "''";
				//echo $noRek;exit;
			}//echo $noRek;exit;		
		}else{			
			$noRek = "";
			if(in_array($priv_session,array('0','3'))){
				$noRek = "'".$data->get("NOREK")."'";
			}else{
				$sql = "select ACCOUNT from TACCOUNT WHERE upper(GROUPID)='".strtoupper(trim($grpID))."'";					
				$rek = $conn->query($sql); 
				while($rek->next()){ $noRek .= "'".$rek->get('ACCOUNT')."',"; }						
				$noRek = substr($noRek,0,strlen($noRek)-1);
				$noRek = (strlen($noRek)>0)?$noRek : "''";
			}	
		}
		
		$sqlnpwp = "select NPWP from tcompany where ID='$ID' and PIC is not null";
		$hasil = $conn->query($sqlnpwp);		
		$npwp_many_session = array();
		while($hasil->next()){
			$npwp_many_session[] = $hasil->get("NPWP");			
		}
		$npwp_many_arr_session = $npwp_many_session;	
		$npwp_many_session = "'".implode("','",$npwp_many_session)."'";
			
		if(cekAktif($uid,$ID,$conn)==1 && ($priv_session!=0 && $priv_session!=1)){	
			//aktifitas
					$_SESSION['uid_session'] = $uid;
					$_SESSION["ID"] = $grpID;
					$aktivitas = " Mencoba login di lebih dari 1 PC";
					audit($conn,$aktivitas);
					unset($_SESSION['uid_session']);
					unset($_SESSION['ID']);		
			echo "<script> window.location.href='".base_url."err/4/".md5(4)."';</script>";exit;
		}
		
		if($priv_session==""){
			echo "<script> window.location.href='".base_url."err/6/".md5(6)."';</script>";exit;
		}
		//echo $pass." | ".$pwd;exit;
		if(trim($pass)==$pwd){
				
				$sql = "SELECT AGE_SET FROM TBLMGTPASSWORD";
				$data = $conn->query($sql);
				if($data->next()){
					$age_set = intval($data->get(0));
				}
			//echo $last_changepass.$age_set; die();
			if(trim($emailstatus)=='1' && ($priv_session !=0 && $priv_session !=1)){
				//aktifitas
					$_SESSION['uid_session'] = $uid;
					$_SESSION["ID"] = $grpID;
					$aktivitas = " Account diblok ";
					audit($conn,$aktivitas);
					unset($_SESSION['uid_session']);
					unset($_SESSION['ID']);	
				echo "<script> window.location.href='".base_url."err/5/".md5(5)."';</script>";exit;
			}if(trim($emailstatus)=='2' && $priv_session !=0 && $priv_session !=1){
				$_SESSION['verified'] = "True";			//aktifitas
				$_SESSION['timeout_session'] = TIMEOUT;
				$_SESSION['grpID'] = $grpID;
				$_SESSION['npwp_session'] = $npwp_session;
				$_SESSION['uid_session'] = $uid;
				$_SESSION['priv_session']  = $priv_session;
				$_SESSION['nmuser_session']  = $nmuser_session;
				$_SESSION['EMAILSTATUS'] = "2"; 
				$_SESSION['email_session'] = $email_session;
				echo "<script>alert('Please Change Your New Password!')\n";
				echo "window.location.href='".base_url."modul/manage/profile';</script>";exit;
			}elseif($last_changepass >= $age_set  && ($priv_session !=0 && $priv_session !=1) ){
					$sql = "update TBLUSER set EMAILSTATUS='2' where (lower(USERLOGIN) = '".strtolower($uid)."')  And lower(ID) = '".strtolower($grpID)."' and userpriv!='0'";
					//die($sql);
					$result = $conn->execute($sql);	
					$_SESSION['verified'] = "True";			//aktifitas
					$_SESSION['timeout_session'] = TIMEOUT;
					$_SESSION['grpID'] = $grpID;
					$_SESSION['npwp_session'] = $npwp_session;
					$_SESSION['uid_session'] = $uid;
					$_SESSION['priv_session']  = $priv_session;
					$_SESSION['nmuser_session']  = $nmuser_session;
					$_SESSION['EMAILSTATUS'] = "2"; 
					$_SESSION['email_session'] = $email_session;
					//die($_SESSION['verified']);
					echo "<script>alert('Your Password is Expired, Please Change Your Password!')\n";
					echo "window.location.href='".base_url."modul/manage/profile';</script>";exit;													
			}else{	
				
				$warChPwd = $age_set - $last_changepass;
				#echo $warChPwd.'masuk'; exit;
				if($warChPwd <= 5){
					echo "<script type='text/javascript'> 
						alert('Your Password will Expire in $warChPwd day.Please Change your password!');
					</script>";	
				}
				
				if($priv_session==6){
					$pic_of_session = "";
					$sql = "select USERS,GROUPID FROM TBLGROUP WHERE PIC='".$uid_session."' AND NPWP='".$npwp_session."'";
					$dataUserGroup = $conn->query($sql);
					$member_session = "";
					if($dataUserGroup->next()){						
						$pic_of_session = $dataUserGroup->get('GROUPID');
						$member= explode(",",$dataUserGroup->get('USERS'));
						foreach($member as $m){
							$member_session .= "'".$m."',";
						}
						$member_session = substr($member_session,0,strlen($member_session)-1);
					}else{
						$sql = "select USERS, GROUPID+'-'+DEPTID AS PIC_OF FROM TBLDEPT WHERE PIC='".$uid_session."' AND CORPID='".$ID."'";
						$dataUserDept = $conn->query($sql);						
						if($dataUserDept->next()){
							$pic_of_session = $dataUserGroup->get('PIC_OF');
							$member = explode(",",$dataUserDept->get('USERS'));
							foreach($member as $m){
								$member_session .= "'".$m."',";
							}
						}
						
						$member_session = substr($member_session,0,strlen($member_session)-1);
					}								
					session_register("pic_of_session");
					session_register("member_session");
				}
				$_SESSION['bahasa_sess'] = $_POST['bahasa'];
				$_SESSION['login_failed'] = 0;
				$_SESSION['NAMA_ses']= $data->get("NAMA");
				$_SESSION['ADDRESS_SES']= $data->get("ADDRESS");
				$_SESSION['CITY_ses']= $data->get("CITY");
				//$_SESSION['ID']= $data->get("ID");
				$_SESSION['grpID'] = $grpID;
				$_SESSION['noRek'] = $noRek;
				//echo $_SESSION['noRek'];exit;
				$_SESSION['timeout_session'] = TIMEOUT;
				$_SESSION['npwp_many_session'] = $npwp_many_session;
				$_SESSION['npwp_many_arr_session'] =$npwp_many_arr_session;

				$_SESSION['tokenid_sess'] = $tokenid;
				$_SESSION['tokenstatus_sess'] = $tokenstatus;
														
				session_register("verified");
				session_register("npwp_session");
				session_register("uid_session");
				session_register("priv_session");
				session_register("nmcomp_session");
				session_register("email_session");
				session_register("nmuser_session");
				session_register("brachsCode");
				session_register("checker_session");
				session_register("groupid_session");
				session_register("deptid_session");
				session_register("fileupload_session");
				
				session_register("zipcode");
				session_register("pic");
				session_register("picEmail");
				session_register("picPhone");
				session_register("picFax");
				
				session_register("NAMA_ses");
				session_register("ADDRESS_SES");
				session_register("CITY_ses");
				session_register("ID");
				session_register("AKSES");
				session_register("grpID");
				session_register("noRek");
				session_register("CSMEmail");	
				session_register("group_account");						
				
				require("configbahasa.php");
				
				$aktivitas = " Berhasil login";
				audit($conn,$aktivitas);						
				setAktif($conn);
				echo "<script> window.location.href='".base_url."modul/home/profile';</script>";exit;
			}
		} else {
				if(strtoupper($_SESSION['grpID_failed'])==strtoupper($grpID)&&strtoupper($_SESSION['uid_failed'])==strtoupper($uid)){
					$_SESSION['login_failed'] = $_SESSION['login_failed']+1;
				}
				$_SESSION['grpID_failed'] = $grpID;
				$_SESSION['uid_failed'] = $uid;	
				
				$sql = "SELECT FAILED_SET FROM TBLMGTPASSWORD";
				$data = $conn->query($sql);
				if($data->next()){
					$fail_set = intval($data->get(0));
				}
								
				if($_SESSION['login_failed']==($fail_set-1)){
					$sql = "select USERPRIV from tbluser where (lower(USERLOGIN) = '".strtolower($uid)."')  And lower(ID) = '".strtolower($grpID)."'";
					$datauser = $conn->query($sql);$datauser->next();
					if($datauser->get('USERPRIV')!=0){					
						$sql = "update TBLUSER set EMAILSTATUS='1' where (lower(USERLOGIN) = '".strtolower($uid)."')  And lower(ID) = '".strtolower($grpID)."' and userpriv!='0'";
						$result = $conn->execute($sql);
						//aktifitas
						
						$aktivitas = " Account diblok ";
						audit($conn,$aktivitas);
						session_destroy(); 
						echo "<script> window.location.href='".base_url."err/5/".md5(5)."';</script>";exit;
					}else{
						$_SESSION['uid_session'] = $uid;
						$_SESSION["ID"] = $grpID;
						$aktivitas = " Salah memasukkan userid dan groupid";
						audit($conn,$aktivitas);
						unset($_SESSION['uid_session']);
						unset($_SESSION['ID']);
						echo "<script> window.location.href='".base_url."err/1/".md5(1)."';</script>";exit;
					}
				}else{
					$_SESSION['uid_session'] = $uid;
					$_SESSION["ID"] = $grpID;
					$aktivitas = " Salah memasukkan userid dan groupid";
					audit($conn,$aktivitas);
					unset($_SESSION['uid_session']);
					unset($_SESSION['ID']);
					echo "<script> window.location.href='".base_url."err/1/".md5(1)."';</script>";exit;
				}
		}
	}else{
		echo "<script> window.location.href='".base_url."err/2/".md5(2)."';</script>";exit;
	}
}else{
	echo "<script> window.location.href='".base_url."';</script>";exit;
}
	
?>