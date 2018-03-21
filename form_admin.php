<?php

if(in_array($_SESSION["priv_session"],array("0","1","2","3"))==false){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}elseif(time()>$_SESSION['timeout_session']){	
	echo "<script> window.location.href='".base_url."err/7/".md5(7)."';</script>";exit;
}else{
	$_SESSION['timeout_session'] = TIMEOUT;
}
require_once("dbconn.php");
require_once("sendEmail.php");
global $conn;
$conn->connect();

function timeStamp(){ 
	$xx = date("YmdHis");
	return $xx;
}

function akses_word($akses){
		$str = "";
		$str .= (substr($akses,0,1)==1)? "SSPCP,":"";
		$str .= (substr($akses,1,1)==1)? "SSP,":"";
		$str .= (substr($akses,2,1)==1)? "Upload SSP,":"";
		$str .= (substr($akses,3,1)==1)? "RTE,":"";
		return substr($str,0,strlen($str)-1);		 
}		


$uid = strFilter($_POST["uid"]);
$data = $_REQUEST["radiopanel"];
$noReks="";
if($div=="edit"){
	if(trim($data)==""){echo "<script> window.location.href='".base_url."modul/user/view';</script>";exit;}	
	$cbx = split(";",$data);		
	$uid = $cbx[0];
	$uid = str_replace("\'","''",$uid); #-> kutip dua
	$npwp = str_replace('-','',str_replace('.','',$cbx[1]));
	if(!$uid) echo "<script> window.location.href='".base_url."modul/home/profile';</script>";	
	$sql = "select usr.USERLOGIN, usr.ID, usr.EMAIL , usr.PHONE_NUMBER, usr.FAX_NUMBER, usr.FULLNAME, usr.USERPRIV, usr.NPWP, com.NAMA, com.ADDRESS, com.CITY, 
			com.ZIPCODE, com.PIC, com.PIC_EMAIL, com.PIC_PHONE, com.FAX_NUMBER AS PIC_FAX, usr.BRANCHCODE,usr.AKSES, usr.NOREK, com.GROUP_ACCOUNT, usr.CSMEMAIL,tg.GROUPID,
			td.DEPTID,tg.NAMA AS GROUPNAME, usr.FILEUPLOAD, usr.NOTIF
			from TBLUSER usr left join TCOMPANY com
			on usr.NPWP = com.NPWP
			LEFT JOIN TBLGROUP tg on usr.NPWP=tg.NPWP and tg.USERS like '%,'+usr.USERLOGIN+',%' 
			left join TBLDEPT td on tg.GROUPID=td.GROUPID AND td.CORPID='".$_SESSION['ID']."' and td.USERS like '%,'+usr.USERLOGIN+',%' 
			where com.NPWP = com.IDPAYEE
			And usr.USERLOGIN = '$uid' And usr.NPWP = '$npwp'";	
	$data = $conn->query($sql);	
	if($data->next()){
		$fullname = $data->get("FULLNAME");
		$email = $data->get("EMAIL");
		$phone = $data->get('PHONE_NUMBER');
		$fax = $data->get('FAX_NUMBER');
		$priv = $data->get("USERPRIV");
		$wpnpwp = $data->get("NPWP");
		$idpayee = $data->get("NPWP"); 
		$id = trim($data->get("ID"));
		$wpnama = $data->get("NAMA");
		$wpalamat = $data->get("ADDRESS");
		$wpkota = $data->get("CITY");
		$wpzipcode = $data->get("ZIPCODE");
		$pic_ = $data->get("PIC");
		$picEmail_ = $data->get("PIC_EMAIL");
		$picPhone_ = $data->get("PIC_PHONE");
		$picFax_ = $data->get("PIC_FAX");
		$branchCD = $data->get("BRANCHCODE");
		$akses = $data->get("AKSES");
		$uid = $data->get("USERLOGIN");
		$noReks = trim($data->get('NOREK'));
		$grouping = trim($data->get('GROUP_ACCOUNT'));
		$CSMEmail_ = trim($data->get("CSMEMAIL"));
		$GROUPID = trim($data->get("GROUPID"));
		$GROUPNAME = trim($data->get("GROUPNAME"));
		$DEPTID = trim($data->get("DEPTID"));
		$FILEUPLOAD = trim($data->get("FILEUPLOAD"));
		$NOTIF = trim($data->get("NOTIF"));
	}
	$readonly = ($priv!=3)?"readonly style='background:#E0E0E0;border:1px #999 solid;padding:2px'":"";
	$readonlys = "readonly style='background:#E0E0E0;border:1px #999 solid;padding:2px'";
	
	$disabled = "disabled";
}elseif($div=="update"){	
	if(trim($uid)==""){ echo "<script> window.location.href='".base_url."modul/user/view';</script>";exit;}	
	$fullname = strFilter(strtoupper($_POST["fullname"]));
	$email = strFilter($_POST["email"]);
	$phone = strFilter($_POST["phone"]);
	$fax = strFilter($_POST["fax"]);
	$notif = $_POST["notif"];
	$ROLE = strFilter($_POST['role']);
	$ROLE_OLD = strFilter($_POST['role_old']);
	$GROUPID = strFilter($_POST['groupid']);
	$DEPTID = strFilter($_POST['deptid']);
	$GROUPID_OLD = strFilter($_POST['groupid_old']);
	$DEPTID_OLD = strFilter($_POST['deptid_old']);
	$GROUPNAME = strFilter($_POST['groupname']);
	$AKSES_OLD = $_POST["akses_old"];
	
	$priv = strFilter($_POST["priv"]);
	#akses admin
	$akses = "";
	$akses .= $_POST["ceksspcp"]==""? "0" : "1";
	$akses .= $_POST["cekssp"]==""? "0" : "1";
	$akses .= $_POST["upload"]==""? "0" : "1";
	$akses .= $_POST["cekrte"]==""? "1" : "1";
	
	$fileupload = "1";
	$fileupload .= $_POST["file_niaga"]==""? "0" : "1";
	$fileupload .= $_POST["file_citi"]==""? "0" : "1";
	
	if($_SESSION["priv_session"] == "0" || $_SESSION["priv_session"] == "1"){
		$id=strFilter(trim($_POST["id"]));
		$idpayee = strFilter(trim($_POST["idpayee"])); 
		$branch= strFilter(trim($_POST["branch"]));
		$nama = strFilter(strtoupper($_POST["wpnama"]));
		$npwp = strFilter(trim($_POST["wpnpwp"]));
		$npwps = strFilter(trim($_POST["wpnpwps"]));
		$address = strFilter(strtoupper($_POST["wpalamat"]));
		$city = strFilter(strtoupper($_POST["wpkota"]));
		$zipcode = strFilter($_POST["wpzipcode"]);
		$pic_ = strFilter($_POST['pic']);
		$picEmail_ = strFilter($_POST['picEmail']);
		$picPhone_ = strFilter($_POST['picPhone']);
		$picFax_= strFilter($_POST['picFax']);		
		$noReks = strFilter($_POST["noReks"]);
		$noReksAwal = strFilter($_POST["noReksAwal"]);
		$CSMEmail_ = trim(strFilter($_POST["CSMEmail"]));
		$grouping = $_POST["grouping"];
		if($npwp!=$npwps){
			$sql = "select NPWP as JUMNPWP FROM TCOMPANY WHERE NPWP='$npwp'";
			$jum= $conn->query($sql);			
			$jumNPWP = $jum->size(); 						
		}
		if($jumNPWP>0){
			$npwp = $npwps;			
		}else{
			$sql = "UPDATE TACCOUNT SET NPWP='".$npwp."' where NPWP='".$npwps."'";
			$conn->execute($sql);						
		}				
		$sql = "UPDATE TCOMPANY SET NAMA='".$nama."', ADDRESS='".$address."', CITY='".$city."', zipcode='".$zipcode."', PIC='$pic_', PIC_EMAIL='$picEmail_', PIC_PHONE='$picPhone_', FAX_NUMBER ='$picFax_', GROUP_ACCOUNT='".$grouping."',NPWP='".$npwp."',IDPAYEE='".$npwp."' WHERE NPWP='".$npwps."'";
		$conn->execute($sql);
		$write_txt = "COM|".$npwp."|".$nama."|".$address."|".$city."|".$zipcode."|".$id."|".$pic_."|".$picEmail_."|".$picPhone_."|".$picFax_." \r\n";
		writetxt($write_txt,"DOKACC.",$_SESSION['grpID']);
		$sqlAcc = "UPDATE TACCOUNT SET ACCOUNT='".$noReks."' where NPWP='".$npwps."' and ACCOUNT='".$noReksAwal."'";
	}else{
		$id= trim($_SESSION["ID"]);
		$branch=trim($_SESSION["brachsCode"]);
		$nama=$_SESSION["nmcomp_session"];
		$npwps= strFilter($_POST["idpayee"]);
		$npwp= strFilter($_POST["idpayee"]);
		$address=$_SESSION["ADDRESS_SES"];
		$city=$_SESSION["CITY_ses"];
		$zipcode=$_SESSION["zipcode"];
		$pic_=$_SESSION["pic"];
		$picEmail_=$_SESSION["pic_email"];
		$picPhone_=$_SESSION["pic_phone"];
		$picFax_=$_SESSION["pic_fax"];
		$noReks = str_replace("'","",$_SESSION["noRek"]);	
		$account_group = $_POST['account_group'];
		$conn->connect();
		if(is_array($account_group)){
			$sql = "select USERS,ACCOUNT,NPWP,ID from TACCOUNT WHERE NPWP='".$npwp."' and STSACC='RTE'";					
			$norek = $conn->query($sql);
			while($norek->next()){
				$users = $norek->get('USERS');
				$users = str_replace($uid.",","",$users);
				$sql = "update TACCOUNT set USERS='".strFilter($users)."' WHERE NPWP='".$norek->get('NPWP')."' AND ACCOUNT='".$norek->get('ACCOUNT')."' AND STSACC='RTE'";										
				$conn->execute($sql);
				$write_txt = "ACC|".$norek->get('NPWP')."|".$norek->get('ACCOUNT')."|".$norek->get('ID')." \r\n";
				writetxt($write_txt,"DOKACC.",$_SESSION['grpID']);
			}
			foreach($account_group as $norek){
				$sql = "select USERS from TACCOUNT where NPWP='".$npwp."' AND ACCOUNT='".$norek."' and STSACC='RTE'";				
				$rek = $conn->query($sql);
				if($rek->next()){
					$users = $rek->get('USERS');
					$users = (strpos($users,",".$uid.",")>-1)? str_replace($uid.",","",$users) : $users."".$uid.",";
					$sql = "update TACCOUNT set USERS='".strFilter($users)."' WHERE NPWP='".$npwp."' AND ACCOUNT='".$norek."'";					
					$conn->execute($sql);
					
				}						
			}
			
		}
	}
	if($_SESSION["priv_session"] == "0"){
		$sqlBranch = "Select BRANCHCODE from TBLUSER Where USERLOGIN = '$uid' And NPWP = '$npwps'";	
		$dtBranch = $conn->query($sqlBranch); $dtBranch->next();
	}
	if($ROLE=='3'){ #user admin
		$sql = "Update TBLUSER set FULLNAME = '".$fullname."', EMAIL = '".$email."',NOTIF = '".$notif."', PHONE_NUMBER='".$phone."', FAX_NUMBER='".$fax."',
				BRANCHCODE = '".$branch."', NOREK='".$noReks."', CSMEMAIL='".$CSMEmail_."', NPWP='".$npwp."', 
				USERPRIV='3',CHECKER=NULL, AKSES='".$akses."',FILEUPLOAD='".$fileupload."' Where USERLOGIN = '".$uid."' And ID = '".$id."' ";						
		
		$sqlreleaser = "Update TBLUSER set AKSES='".$akses."' Where USERPRIV='4' and ID = '".$id."' ";						
		$conn->execute($sqlreleaser);
	}elseif($ROLE=='4'){ #OPERATOR
		$sql = "Update TBLUSER set FULLNAME = '".$fullname."', EMAIL = '".$email."',NOTIF = '".$notif."', PHONE_NUMBER='".$phone."', FAX_NUMBER='".$fax."', 
				USERPRIV='4', CHECKER=NULL, AKSES='0001'
				Where USERLOGIN = '".$uid."' And ID = '".$id."' ";
				
	}elseif($ROLE=='5'){ #user rte
		$sql = "Update TBLUSER set FULLNAME = '".$fullname."', EMAIL = '".$email."',NOTIF = '".$notif."', PHONE_NUMBER='".$phone."', FAX_NUMBER='".$fax."',
				USERPRIV='5', CHECKER=NULL, AKSES='0001'
				Where USERLOGIN = '".$uid."' And ID = '".$id."' ";
	}elseif($ROLE=='2'){ #pic
		$sql = "Update TBLUSER set FULLNAME = '".$fullname."', EMAIL = '".$email."',NOTIF = '".$notif."', PHONE_NUMBER='".$phone."', FAX_NUMBER='".$fax."', 
				USERPRIV='2', CHECKER=NULL, AKSES=''
				Where USERLOGIN = '".$uid."' And ID = '".$id."' ";
	}elseif($ROLE=='1'){ #pic
		$sql = "Update TBLUSER set FULLNAME = '".$fullname."', EMAIL = '".$email."',NOTIF = '".$notif."', PHONE_NUMBER='".$phone."', FAX_NUMBER='".$fax."', 
				USERPRIV='1', CHECKER=NULL, AKSES=''
				Where USERLOGIN = '".$uid."' And ID = '".$id."' ";
	}else{
		if($_SESSION["priv_session"] == "3"){ #admin perusahaan
			$ROLE = ($ROLE_OLD!=1 && $ROLE_OLD!=2)? "NULL" : "'".$ROLE."'";
			$sql = "Update TBLUSER set FULLNAME = '".$fullname."', EMAIL = '".$email."',NOTIF = '".$notif."', PHONE_NUMBER='".$phone."', FAX_NUMBER='".$fax."',USERPRIV='".$ROLE."' 
					Where USERLOGIN = '".$uid."' And ID = '".$id."' ";			
		}else{ #maker / checker
			$AKSES = ($ROLE_OLD!=1 && $ROLE_OLD!=2)? ",AKSES=NULL" : ",AKSES='".$AKSES_OLD."'";
			$ROLE = ($ROLE_OLD!=1 && $ROLE_OLD!=2)? "NULL" : "'".$ROLE."'";
			$sql = "Update TBLUSER set FULLNAME = '".$fullname."', EMAIL = '".$email."', NOTIF = '".$notif."', PHONE_NUMBER='".$phone."', FAX_NUMBER='".$fax."',USERPRIV='".$ROLE."'
					".$AKSES." 
					Where USERLOGIN = '".$uid."' And ID = '".$id."' ";
		}
	}
	#echo $sql;exit;
	$conn->execute($sql);	
	
	if($ROLE=='3' || $ROLE=='4' || $ROLE=='5'){
		#hapus uid pada dept
		$sqldept = "Select USERS from TBLDEPT where USERS like '%,".$uid.",%' AND CORPID='".trim($_SESSION['ID'])."'";
		$dtdept = $conn->query($sqldept);
		if($dtdept->next()){
			$users = str_replace(",".trim($uid).",",",",$dtdept->get('USERS'));
			$sqlgroup = "update TBLDEPT set USERS='".$users."' where USERS like '%,".$uid.",%' AND CORPID='".trim($_SESSION['ID'])."'";
			$conn->execute($sqlgroup);
		}
		# hapus uid pada group
		$sqlgroup = "Select USERS from TBLGROUP where USERS like '%,".$uid.",%'  AND npwp='".trim($_SESSION['npwp_session'])."'";		
		$dtgroup = $conn->query($sqlgroup);
		if($dtgroup->next()){
			$users = str_replace(",".$uid.",",",",$dtgroup->get('USERS'));
			$sqlgroup = "update TBLGROUP set USERS='".$users."' where USERS like '%,".$uid.",%'  AND npwp='".trim($_SESSION['npwp_session'])."'";
			$conn->execute($sqlgroup);
		}	
	}
	
	
	$aktivitas = "Berhasil melakukan pengeditan user dengan UserID : ".$uid." pada Group ID : ".$id;
	
	audit($conn,$aktivitas);
	if($_SESSION["priv_session"] == "0" && $ROLE=='3'){	
		$sql = "Update TBLUSER Set NOREK='$noReks', NPWP='$npwp', CSMEMAIL='$CSMEmail_' , BRANCHCODE = '$branch', FILEUPLOAD='".$fileupload."' where ID='$id' and 
				BRANCHCODE='".$dtBranch->get('BRANCHCODE')."'";		
				$conn->execute($sql);
		$conn->execute($sqlAcc);
		require_once("dbconndb.php");
		$connDB->connect();
		$connDB->execute($sqlAcc);
		$connDB->disconnect();
	}				
	if($jumNPWP>0){
		$_SESSION['statusRespon'] = 0;
		$_SESSION['respon'] = $bhs['NPWP sudah Terdaftar'][$kdbhs];
		
	}else{
		$_SESSION['statusRespon'] = 1;
		$_SESSION['respon'] = "Perubahan pada User Berhasil!";	
	}
	echo "<script> window.location.href='".base_url."modul/user/view';</script>";exit;
}elseif($div=='insert'){	
	$sqlPass = "select PASS_SET,ALPHA_SET from tblMgtPassword";
	$dataPass = $conn->query($sqlPass);
	if($dataPass->next()){
		$minPass = $dataPass->get("pass_set");
		$minNum = $dataPass->get("ALPHA_SET");
	}else{
		$minPass = 6;
		$minNum = 2;
	}
	function createPassword($minPass,$minNum){	
		$char = "QWERTYUIOPASDFGHJKLZXCVBNMmnbvcxzasdfghjklpoiuytrewq";
		$num = array();
		for($i=0;$i<$minNum;$i++){
			$nilNum = rand(0,$minPass-1);
			while(in_array($nilNum,$num)){
				$nilNum = rand(0,$minPass-1);
			}
			$num[] = $nilNum;
		}
		for($i=0;$i<$minPass;$i++){	
			if(!in_array($i,$num)){
				$nil = rand(0,51);
				$xx = substr($char,$nil,1);
			}else{
				$xx = rand(0,9);
			}
			$pwd = $pwd . $xx;
		}
		return $pwd;
	}
	if(trim($uid)==""){ echo "<script> window.location.href='".base_url."modul/user/view';</script>";exit;}	
	$role = strFilter($_POST["role"]);
	$groupid = strFilter($_POST["groupid"]);
	$deptid = strFilter($_POST["deptid"]);
	$groupname = strFilter($_POST["groupname"]);
	$uid = str_replace(" ","",strFilter($_POST["uid"]));
	$fullname = strFilter(strtoupper($_POST["fullname"]));
	$email = strFilter($_POST["email"]);
	$phone = strFilter($_POST["phone"]);
	$fax = strFilter($_POST["fax"]);
	$priv = strFilter($_POST["priv"]);
	
	$akses = "";
	$akses .= $_POST["ceksspcp"]==""? "0" : "1";
	$akses .= $_POST["cekssp"]==""? "0" : "1";
	$akses .= $_POST["upload"]==""? "0" : "1";
	$akses .= $_POST["cekrte"]==""? "1" : "1";
	
	$fileupload = "1";
	$fileupload .= $_POST["file_niaga"]==""? "0" : "1";
	$fileupload .= $_POST["file_citi"]==""? "0" : "1";
	
	$password = createPassword($minPass,$minNum);
	if($_SESSION["priv_session"] == "0"||$_SESSION["priv_session"] == "1"||$_SESSION["priv_session"] == "2"){
		$id=strFilter(trim($_POST["id"]));
		$branch= strFilter(trim($_POST["branch"]));
		$nama = strFilter($_POST["wpnama"]);
		$npwp = strFilter(trim($_POST["wpnpwp"]));
		$address = strtoupper(strFilter($_POST["wpalamat"]));
		$city = strtoupper(strFilter($_POST["wpkota"]));
		$zipcode = strFilter($_POST["wpzipcode"]);
		$pic_ = strFilter(strtoupper($_POST['pic']));
		$picEmail_ = strFilter($_POST['picEmail']);
		$picPhone_ = strFilter($_POST['picPhone']);
		$picFax_= strFilter($_POST['picFax']);
		$noReks = strFilter($_POST["noReks"]);
		$grouping = $_POST["grouping"];
		$CSMEmail_ = trim(strFilter($_POST["CSMEmail"]));
	}else{
		$id= trim($_SESSION["ID"]);
		$branch=trim($_SESSION["brachsCode"]);
		$nama=strFilter($_SESSION["nmcomp_session"]);
		$npwp=$_SESSION["npwp_session"];
		$address=strFilter($_SESSION["ADDRESS_SES"]);
		$city=strFilter($_SESSION["CITY_ses"]);
		$zipcode= $_SESSION["zipcode"];
		$pic_= $_SESSION["pic"];
		$picEmail_= $_SESSION["picEmail"];
		$picPhone_= $_SESSION["picPhone"];
		$picFax_= $_SESSION["picFax"];
		$noReks = str_replace("'","",$_SESSION["noRek"]);	
		$grouping = $_SESSION["group_account"];
		$account_group = $_POST['account_group'];
	}

	$cekID = "select count(*) jml from TBLUSER where USERPRIV = '3' and UPPER(ID) = '".strtoupper(trim($id))."'";	
	$dataC = $conn->query($cekID);
	$dataC->next();
	if(($dataC->get("jml") > 1) and ($priv=='3')){
		//$message = "ID Key ".$id." telah terdaftar!, gunakan yang lain !";
		$message = '
					<div style="width: 750px; background:#F0F0F0; border: 1px solid #D7D7D7;">
					<div style="font-family:arial; font-weight:lighter; font-size:11px; padding-top:10px; padding-bottom:10px; padding-left:10px;">
						<table cellpadding="0" cellspacing="0" style="font-family:arial; font-weight:lighter; font-size:11px;">
							<tr>
								<td><img src="'.base_url.'img/warning.png" style="border:none"/></td>
								<td style="padding-left:20px; font-family:arial; font-size:11px; color:#808080;">
								Group ID not available! Please try again.
								</td>
							</tr>
						</table>
					</div>
				</div>
	';
	} else {
		//cek apakah uid sudah terdaftar	
		$sql = "select * From TBLUSER Where USERLOGIN = '$uid' And  NPWP = '$npwp' and UPPER(ID) = '".strtoupper(trim($id))."'";
		$data = $conn->query($sql);
		if($data->next()){
			$message = '				
				<div style="width: 750px; background:#F0F0F0; border: 1px solid #D7D7D7;">
					<div style="font-family:arial; font-weight:lighter; font-size:11px; padding-top:10px; padding-bottom:10px; padding-left:10px;">
						<table cellpadding="0" cellspacing="0" style="font-family:arial; font-weight:lighter; font-size:11px;">
							<tr>
								<td><img src="'.base_url.'img/warning.png" style="border:none"/></td>
								<td style="padding-left:20px; font-family:arial; font-size:11px; color:#808080;">
								user ID already exist!
								</td>
							</tr>
						</table>
					</div>
				</div>';

		} else {
			
			if($role=='3'){ #user admin
				$sql = "Insert Into TBLUSER (USERLOGIN, PASSWORD, EMAIL, PHONE_NUMBER, FAX_NUMBER, FULLNAME, NPWP, ID,BRANCHCODE,NOREK,CSMEMAIL,USERPRIV,AKSES,LOGIN) 
						Values ('$uid', '". md5($password) ."', '$email','$phone','$fax','$fullname', '$npwp', '$id', '$branch', '".trim($noReks)."','$CSMEmail_','3','".$akses."','N')";		
						
				#$que = "Insert into tblHistoryPass (USERNAME,GID,PASSWORD) values ('$uid','". md5($password) ."','$id')";
				
			}elseif($role=='4'){ #operator
				$sql = "Insert Into TBLUSER (USERLOGIN, PASSWORD, EMAIL, PHONE_NUMBER, FAX_NUMBER, FULLNAME, NPWP, ID,BRANCHCODE,NOREK,CSMEMAIL,USERPRIV,AKSES,LOGIN) 
						Values ('$uid', '". md5($password) ."', '$email','$phone','$fax','$fullname', '$npwp', '$id', '$branch', '".trim($noReks)."','$CSMEmail_','4','0001','N')";
				$akses ='0001';
			}elseif($role=='5'){ #user rte
				$sql = "Insert Into TBLUSER (USERLOGIN, PASSWORD, EMAIL, PHONE_NUMBER, FAX_NUMBER, FULLNAME, NPWP, ID,BRANCHCODE,NOREK,CSMEMAIL,USERPRIV,AKSES,LOGIN) 
						Values ('$uid', '". md5($password) ."', '$email','$phone','$fax','$fullname', '$npwp', '$id', '$branch', '".trim($noReks)."','$CSMEmail_','5','0001','N')";
				
				#$que = "Insert into tblHistoryPass (USERNAME,GID,PASSWORD) values ('$uid','". md5($password) ."','$id')";
				
				$akses = '0001';		
			}elseif($role=='2'){ #Admin Bank
				$sql = "Insert Into TBLUSER (USERLOGIN, PASSWORD, EMAIL, PHONE_NUMBER, FAX_NUMBER, FULLNAME, NPWP, ID,BRANCHCODE,NOREK,CSMEMAIL,USERPRIV,AKSES,LOGIN) 
						Values ('$uid', '". md5($password) ."', '$email','$phone','$fax','$fullname', '$npwp', '$id', '$branch', '".trim($noReks)."','$CSMEmail_','1','','N')";
				$akses = '';		
			
			}elseif($role=='1'){ #Admin Bank App
				$sql = "Insert Into TBLUSER (USERLOGIN, PASSWORD, EMAIL, PHONE_NUMBER, FAX_NUMBER, FULLNAME, NPWP, ID,BRANCHCODE,NOREK,CSMEMAIL,USERPRIV,AKSES,LOGIN) 
						Values ('$uid', '". md5($password) ."', '$email','$phone','$fax','$fullname', '$npwp', '$id', '$branch', '".trim($noReks)."','$CSMEmail_','1','','N')";
				$akses = '';		
			}else{ #default
				$sql = "Insert Into TBLUSER (USERLOGIN, PASSWORD, EMAIL, PHONE_NUMBER, FAX_NUMBER, FULLNAME, NPWP, ID,BRANCHCODE,NOREK,CSMEMAIL,LOGIN) 
						Values ('$uid', '". md5($password) ."', '$email','$phone','$fax','$fullname', '$npwp', '$id', '$branch', '".trim($noReks)."','$CSMEmail_','N')";
				$akses = '0000';
			}
		
			$hasil = $conn->execute($sql);
			//$hasil = $conn->execute($que);
			if($hasil){
				$to = $email;				
				$subject = "SCB E-RTE";								
				$body = "Yth. ".ucfirst($fullname).",<br>Account Anda telah berhasil terdaftar pada SCB E-RTE.<br>Berikut informasinya :<br> 
						<li>Group Id : $id </li>
						<li>User Id : $uid </li>
						<li>Password : $password</li>
						<br>Silakan gunakan account diatas untuk mengakses account Anda pada SCB E-RTE di link <a href='http://standardchartered.ebank-services.com/RTE/'> SCB E-RTE </a>.<br>Untuk keamanan silakan lakukan perubahan pada password Anda secara berkala.
						<br><br>Terimakasih<br><br><b>Admin SCB E-RTE</b><br><br>";	
				$d = $conn->query("select * FROM TBLUSER WHERE USERLOGIN='$uid' and ID='$id'"); $d->next();				
				
				sendEmail(array('to'=>$to,'subject'=>$subject,'isi'=>$body));				
				$body = "Yth. Administator E-RTE,<br>Anda telah berhasil mendaftarkan account baru pada SCB E-RTE di link <a href='http://standardchartered.ebank-services.com/RTE/'> SCB E-RTE </a>.<br>Berikut informasinya :<br> 
						<li>Group Id : $id </li>
						<li>User Id : $uid </li>						
						<br><br>Terimakasih<br><br><b>Admin SCB E-RTE</b><br><br>";
				//email admin rte	
				//$to = "etax@bankmandiri.co.id";
				//sendEmail(array('to'=>$to,'subject'=>$subject,'isi'=>$body));
						
				
				$aktivitas = " Berhasil membuat user baru dengan UserID : ".$uid." pada Group ID : ".$id;
				audit($conn,$aktivitas);	
				$arrRole = array("2"=>"Administrator SCB","2"=>"Administrator SCB Aplication","3"=>"User Administrator","4"=>"Operator","5"=>"Supervisor");
				$message = '
				<span style="color:#1a68a4; font-size:14px; font-weight:bold;">
					<div style="background:#E5EEF5;padding:5px;border:1px #CCC solid;width:740px;">
						<img src="'.base_url.'img/accept.png" style="border:none"> Anda telah Berhasil membuat User Baru 
					</div>					
				</span>
				<div style="height:18px;">&nbsp;</div>				
				<table cellpadding="0" cellspacing="0" width="750">
					<tr>
						<td style="background:url('.base_url.'img/tab1.png); width:20px; height:22px;">&nbsp;</td>
						<td style="background:url('.base_url.'img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">User Information</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" width="750" style="font-size:11px; font-family:arial; color:#333333;">
					<tr>
						<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>User ID</strong></span></td>
						<td style="border-bottom: 1px solid #D7D7D7; width:1px;">:</td>
						<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">'.$d->get('USERLOGIN').'</span></td>
					</tr>
					<tr>
						<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Password</strong></span></td>
						<td style="border-bottom: 1px solid #D7D7D7; width:1px;">:</td>
						<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">'.$password.'</span></td>
					</tr>
					<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
						<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Fullname</strong></span></td>
						<td style="border-bottom: 1px solid #D7D7D7;">:</td>
						<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">'.$d->get('FULLNAME').'</span></td>
					</tr>
					<tr>
						<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Email</strong></span></td>
						<td style="border-bottom: 1px solid #D7D7D7;">:</td>
						<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">'.$email.'</span></td>
					</tr>
					<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
						<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Phone Number</strong></span></td>
						<td style="border-bottom: 1px solid #D7D7D7;">:</td>
						<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">'.$phone.'</span></td>
					</tr>
					<tr>
						<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Fax Number</strong></span></td>
						<td style="border-bottom: 1px solid #D7D7D7;">:</td>
						<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">'.$fax.'</span></td>
					</tr>
					
													
				</table>				
				<div style="height:18px;">&nbsp;</div>				
				<table cellpadding="0" cellspacing="0" width="750">
					<tr>
						<td style="background:url('.base_url.'img/tab1.png); width:20px; height:22px;">&nbsp;</td>
						<td style="background:url('.base_url.'img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">Company Profile</td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" width="750" style="font-size:11px; font-family:arial; color:#333333;">
					<tr>
						<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Group ID</strong></span></td>
						<td style="border-bottom: 1px solid #D7D7D7;">:</td>
						<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">'.$id.'</span></td>
					</tr>
					<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
						<td height="20" width="170" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong><em>NPWP</em></strong></span></td>
						<td style="border-bottom: 1px solid #D7D7D7;width:1px">:</td>
						<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">'.$npwp.'</span></td>
					</tr>
					<tr>
						<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Company Name</strong></span></td>
						<td style="border-bottom: 1px solid #D7D7D7;">:</td>
						<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">'.$nama.'</td>
					</tr>
					<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
						<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Address</strong></span></td>
						<td style="border-bottom: 1px solid #D7D7D7;">:</td>
						<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">'.$address.'</span></td>
					</tr>
					<tr>
						<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>City</strong></span></td>
						<td style="border-bottom: 1px solid #D7D7D7;">:</td>
						<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">'.$city.'</span></td>
					</tr>
					<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
						<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Postal Code</strong></span></td>
						<td style="border-bottom: 1px solid #D7D7D7;">:</td>
						<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">'.$zipcode.'</span></td>
					</tr>
					<tr>
						<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>PIC</strong></span></td>
						<td style="border-bottom: 1px solid #D7D7D7;">:</td>
						<td style="border-bottom:1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">'.$pic_.'</span></td>
					</tr>
					<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
						<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>PIC Email</strong></span></td>
						<td style="border-bottom: 1px solid #D7D7D7;">:</td>
						<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">'.$picEmail_.'</span></td>
					</tr>
					<tr>
						<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>PIC Phone</strong></span></td>
						<td style="border-bottom: 1px solid #D7D7D7;">:</td>
						<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">'.$picPhone_.'</span></td>
					</tr>
					<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
						<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Fax Number</strong></span></td>
						<td style="border-bottom: 1px solid #D7D7D7;">:</td>
						<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">'.$picFax_.'</span></td>
					</tr>'.(trim($noReks)?
						'<tr>
							<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Bank Account</strong></span></td>
							<td style="border-bottom: 1px solid #D7D7D7;">:</td>
							<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">'.$noReks.'</span></td>
						</tr>'
						:''
					
					).'
				</table>
				<div style="margin-top:18px; width: 750px; background:#F0F0F0; border: 1px solid #D7D7D7;">
					<div style="font-family:arial; font-weight:lighter; font-size:11px; padding-top:10px; padding-bottom:10px; padding-left:10px;">
						<table cellpadding="0" cellspacing="0" style="font-family:arial; font-weight:lighter; font-size:11px;">
							<tr>
								<td><img src="'.base_url.'img/warning.png" style="border:none"/></td>
								<td style="padding-left:20px; font-family:arial; font-size:11px; color:#808080;">
								- Please change your password after successful login for first time to the application<br /><br />
								- Change your password periodically for your security
								</td>
							</tr>
						</table>
					</div>
				</div>';
				$sql = "Select * From TCOMPANY Where NPWP = '$npwp' And IDPAYEE ='$npwp'";
				$data = $conn->query($sql);
				if(!$data->next()){
					$sql = "Insert Into TCOMPANY (NPWP, NAMA, ADDRESS, CITY, ZIPCODE, IDPAYEE,PIC,PIC_EMAIL,PIC_PHONE,FAX_NUMBER,ID,GROUP_ACCOUNT)
					Values ('$npwp', '$nama', '$address', '$city', '$zipcode', '$npwp','$pic_','$picEmail_','$picPhone_','$picFax_','$id','$grouping')";
					$conn->execute($sql);
					$write_txt = "COM|".$npwp."|".$nama."|".$address."|".$city."|".$zipcode."|".$id."|".$pic_."|".$picEmail_."|".$picPhone_."|".$picFax_." \r\n";
				}
				
				if($_SESSION['priv_session']=='0'||$_SESSION['priv_session']=='1'||$_SESSION['priv_session']=='2'){
					if(trim($noReks)!=""){
						$sql = "SELECT ID FROM TACCOUNT where NPWP='$npwp' and ACCOUNT='$noReks'";	
						#die($sql);	
						$data = $conn->query($sql);
						if(!$data->next() and $data->size()==0){
							$stamp = timeStamp();
						 	$sql = "Insert Into TACCOUNT(ID,NPWP,ACCOUNT,STSACC,GROUPID) values(".$stamp.",'$npwp','$noReks','RTE','$id')";
							$conn->execute($sql);
							$write_txt .= "ACC|".$npwp."|".$noReks."|".$stamp." \r\n";
						}
					}
				}/* else{
					foreach($account_group as $norek){
						$sql = "select USERS from TACCOUNT where NPWP='".$npwp."' AND ACCOUNT='".$norek."' and STSACC='RTE'";				
						$rek = $conn->query($sql);
						if($rek->next()){
							$users = $rek->get('USERS');
							$users = (strpos($users,",".$uid.",")>-1)? str_replace($uid.",","",$users) : $users."".$uid.",";
							$sql = "update TACCOUNT set USERS='".strFilter($users)."' WHERE NPWP='".$npwp."' AND ACCOUNT='".$norek."'";					
							$conn->execute($sql);
						}						
					}				
				} */
				if (strlen($write_txt)>0){ 
				writetxt($write_txt,"DOKACC.",$_SESSION['grpID']);
				}
			}
		}		
		
	}
	$_SESSION['respon'] = $message;
//	echo "<script> window.location.href='".base_url."modul/user/add';</script>";exit;
}
$conn->disconnect();
?>

<?php
if($_SESSION['respon']!=""){
	echo $_SESSION['respon'];
	$_SESSION['respon']="";
}else{	
	if(!$_POST["Submit"]){
	?>	
	<table cellpadding="0" cellspacing="0" width="100%" id="cekadmin">
		<tr>
			<td style="border-bottom:1px solid #D7D7D7;">
				<div style="padding-bottom:9px;">
				<span style="color:#1a68a4; font-size:16px; font-weight:bold;">
				<?php
				$judul = array("add"=>"Create New User","edit"=>"Edit User");				
				echo $judul[$div];					
				?>							
				</span><br />
				</div>
			</td>
		</tr>
	</table>				
	<?php
	if($_SESSION["priv_session"]=="3"){
		$readonly = "readonly style='background:#E0E0E0;border:1px #999 solid;padding:2px'";
	?>				
	<div style="height:18px;">&nbsp;</div>				
	<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
			<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">User Information</td>
		</tr>
	</table>
	<form name="frmPengguna" method="post" action="<?php echo base_url."modul/user/".($div=='add'?'insert':'update');?>" id="frmPengguna" >	         		
	 <input type="hidden" name="idpayee" value="<?php echo($idpayee);?>">
	 <input type="hidden" name="role_old" value="<?php echo($priv);?>">
	 <?php
	 if(substr($_SESSION['AKSES'],1,1)!='1'){
		echo "<input name='priv' id='priv' type='hidden' value='4'>";
	 }
	 ?>	
	<table cellpadding="0" cellspacing="0" width="100%" style="font-size:11px; font-family:arial; color:#333333;">
		<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
          <td  width="19%"  height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Role <font color="#FF0000">*</font></strong></span></td>
		  <td  width="81%"  style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <select name="role" id="role" style="width:176px;margin-left:1px;" onchange="isReleaser(this.value)">
			<?php						
			if($priv!=""){				
				if ($priv=='4'||$priv=='5'){
					if($priv=='4'){
						$selec1 = "selected";
					}else{
						$selec2 = "selected";
					}
					echo "<option value ='4' ".$selec1.">- Operator</option>";
					echo "<option value ='5' ".$selec2.">- Supervisor</option>";
			}	
			}else{				
				echo (substr($_SESSION['AKSES'],3,1)==1)?'<option value="5" '.($priv==5?'selected':'').' >- Supervisor</option>':'';
				echo (substr($_SESSION['AKSES'],3,1)==1)?'<option value="4" '.($priv==5?'selected':'').' >- Operator</option>':'';
				if(substr($_SESSION['AKSES'],0,1)==1 || substr($_SESSION['AKSES'],1,1)==1 || substr($_SESSION['AKSES'],2,1)==1){
					echo 	'<option value="4">- Releaser</option>
							 <option value="6">- PIC</option>
							 <option value="" selected>- eTax Operator</option>';
				}
			}
			?>
            </select>
          </span> </td>
	  </tr>		
		<tr>
			<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>User ID  <font color="#FF0000">*</font></strong></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <input name="uid" type="text" id="uid" maxlength="30" value="<?php echo $uid;?>" <?php echo($div != 'edit')? "" :"ket=edit ".$readonly;?>  
            onkeyup="javascript:$('#warningUserid').html('')" class="nospace isi" label="User ID">
			<?php echo $div!="edit"? '<button type="button" onClick="javascript:cekUserIdAsAdmin();" style="font-size:11px;" class="btn_4">Check User ID</button><font id="warningUserid"></font>' : "";?></span></td>
		</tr>
		<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
			<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Fullname  <font color="#FF0000">*</font></strong></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <input name="fullname" type="text" id="fullname" value="<?php echo($fullname); ?>" size="40" maxlength="50" class="isi" label="Fullname">
            </span></td>
		</tr>
		<tr>
			<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Email  <font color="#FF0000">*</font></strong></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <input name="email" type="text" id="email" value="<?php echo($email); ?>" size="40" maxlength="255" class="isi email nospace" label="Email">
            </span></td>
		</tr>
	  <tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
			<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Phone Number  <font color="#FF0000">*</font></strong></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <input name="phone" type="text" id="phone" value="<?php echo($phone); ?>" size="20" maxlength="14" class="isi phone nospace" label="Phone Number">
            </span></td>
		</tr>
		<tr>
			<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <strong>Fax Number  <font color="#FF0000">*</font></strong></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <input name="fax" type="text" id="fax" value="<?php echo($fax); ?>" size="20" maxlength="14" class="isi phone nospace" label="Fax Number" ></span></td>
		</tr>
		<?php	
		if($_SESSION['group_account']=='1'){			
		?>
		<tr id="listRekening" <?php echo $div!='edit'?'style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;"':'';?>>
			<td height="20" style="border-bottom: 1px solid #D7D7D7;vertical-align:top"><span style="padding:6px 0px 6px 9px;">
			<strong>No. Rekening <font color="#FF0000">*</font></strong></span>			
            </td>
			<td style="border-bottom: 1px solid #D7D7D7;padding:4px;">
			<span style="padding:6px 0px 6px 9px;">
            <select multiple="multiple" style="width:200px;height:100px;" name="account_group[]" id="account_group" <?php echo ($div=="add")? 'class="isi"' : 'class="pass"'?> label="No. Rekening">
                <?php
                    $conn->connect();
                    $sql = "select ACCOUNT,USERS from TACCOUNT where npwp='".$_SESSION['npwp_session']."' and STSACC='RTE'";
                    $rek = $conn->query($sql);
                    
                    while($rek->next()){
                        echo (strpos($rek->get('USERS'),",".$uid.",")>-1)? "<option value='".$rek->get('ACCOUNT')."' selected>".$rek->get('ACCOUNT')."</option>" : "<option value='".$rek->get('ACCOUNT')."'>".$rek->get('ACCOUNT')."</option>";
                    }		
                ?>
            </select>                      
			</span>
            </td>
		</tr>
		<?php
		}
		?>
		<tr>
			<td height="20"><span style="padding:6px 0px 6px 9px;"><strong>&nbsp;</strong></span></td>
			<td><span style="padding:6px 0px 6px 9px;"><input type="hidden" name="Submit" id="Submit"  value="<?php echo($div); ?>"/>
			<button type="button" class="btn_1" onClick="cekFormPengguna1('frmPengguna');" style="width:65px">Save</button>                        
			<button type="reset" class="btn_1" style="width:65px">Reset</button>
			<?php
			if($div=="edit"){?><button type="button" onclick="history.back()" class="btn_1" style="width:75px">Cancel</button><?php }?>
			</span></td>
		</tr>
	</table>
	</form>
	<?php
	}elseif($_SESSION["priv_session"]=="0"||$_SESSION["priv_session"]=="2"||$_SESSION["priv_session"]=="1"){
	?>
	<div style="height:18px;">&nbsp;</div>				
	<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
			<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">User Information</td>
		</tr>
	</table>
	<form name="frmPengguna" id="frmPengguna" method="post" action="<?php echo base_url."modul/user/".($div=='edit'?'update':'insert')?>">	
	
	<table cellpadding="0" cellspacing="0" width="100%" style="font-size:11px; font-family:arial; color:#333333;">
		<tr>
			<td height="20" width="259" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>User ID  <font color="#FF0000">*</font></strong></span></td>
			<td width="1053" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
			  <input name="uid" type="text" id="uid" maxlength="30" <?php echo("value =\"$uid\" $readonlys"); ?> ket="<?php echo($div != 'edit')? "" :"edit" ?>"  
              onkeyup="javascript:$('#warningUserid').html('')" class="nospace isi" label="User ID" />			  
		  <?php echo $div!="edit"? '<button type="button" onClick="javascript:cekUserIdAsAdmin();" style="font-size:11px;" class="btn_4">Check User ID</button><font id="warningUserid"></font>' : "";?></span></td>
		</tr>
		<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
			<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Fullname  <font color="#FF0000">*</font></strong></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <input name="fullname" type="text" id="fullname" value="<?php echo($fullname); ?>" size="40" maxlength="50" class="isi" label="Fullname"></span></td>
		</tr>
		<tr>
			<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Email  <font color="#FF0000">*</font></strong></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <input name="email" type="text" id="email" value="<?php echo($email); ?>" size="40" maxlength="255" class="isi email nospace" label="Email"></span></td>
		</tr>
		<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
			<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Phone Number  <font color="#FF0000">*</font></strong></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <input name="phone" type="text" id="phone" value="<?php echo($phone); ?>" size="20" maxlength="14" class="isi phone nospace" label="Phone Number" ></span></td>
		</tr>
		<tr>
			<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Fax Number  <font color="#FF0000">*</font></strong></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <input name="fax" type="text" id="fax" value="<?php echo($fax); ?>" size="20" maxlength="14" class="isi phone nospace" label="Fax number" ></span></td>
		</tr>
		<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
			<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Roles  <font color="#FF0000">*</font></strong></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;">
			<span style="padding:6px 0px 6px 9px;">
			<input type="hidden" name="role_old" value="<?php echo $priv?>" />
			
			<select name="role" id="priv" onchange="cekPrivilege(this,<?php echo ($div=='edit'?'1':'0')?>,1)">
				<?php 
				#5 Supervisor  3 Admin Customer rte  0 super admin 1 admin scb 2 admin scb app 4 Operator
				$label = array("1"=>"Admin SCB","2"=>"Admin SCB App","3"=>"Admin Customer","4"=>"Operator","5"=>"Supervisor");
				if($priv=='3' && $div=='edit'){#user administrator
					
					echo "<option value ='".$priv."' selected>". $label[3] ."</option>";
				}
				elseif(($priv=='4'||$priv=='5') && $div=='edit'){#user administrator
					if ($priv=='4'){
						$selec1 = "selected";
					}else{
						$selec2 = "selected";
					}
					echo "<option value ='4' ".$selec1.">". $label[4] ."</option>";
					echo "<option value ='5' ".$selec2.">". $label[5] ."</option>";	
				}elseif(($priv=='1'||$priv=='2') && $div=='edit'){#rte user
					if ($priv=='1'){
						$selec1 = "selected";
					}else{
						$selec2 = "selected";
					}
					echo "<option value ='1' ".$selec1.">". $label[1] ."</option>";
				//	echo "<option value ='2' ".$selec2.">". $label[2] ."</option>";	
				}elseif($div=='add'){#new user
					if($_SESSION["priv_session"]==0){
						//echo "<option value ='' selected>-</option>";
						echo "<option value ='1'>". $label[1] ."</option>";
						//echo "<option value ='2' >". $label[2] ."</option>";
					}elseif($_SESSION["priv_session"]==1){
					//	echo "<option value ='' selected>-</option>";
						echo "<option value ='3' >". $label[3] ."</option>";
						
					}elseif($_SESSION["priv_session"]==2){
					//	echo "<option value ='' selected>-</option>";
						echo "<option value ='2' >". $label[2] ."</option>";
						
					}
					
				}
				?>
			</select></span></td>
		</tr>
		<tr >
			<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Notification Email  <font color="#FF0000">*</font></strong></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;">
			<span style="padding:6px 0px 6px 9px;">
			
			<select name="notif" >
			
					<option value ='0' <?=$NOTIF!='1'?'selected="selected"':''?>>NO</option>
					<option value ='1' <?=$NOTIF=='1'?'selected="selected"':''?>>YES</option>	
			
			</select></span></td>
		</tr>
		
	</table>				
	<div style="height:18px;">&nbsp;</div>
	<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
			<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">Company Profile</td>
		</tr>
	</table>
	<table cellpadding="0" cellspacing="0" width="100%" style="font-size:11px; font-family:arial; color:#333333;">
		<tbody id="detailCompanyProfile">
		<tr>
			<td height="20" width="269" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <strong>Corp ID <font color="#FF0000">*</font></strong></span></td>
			<td width="1043" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
			<input name="id" type="text" id="id" maxlength="8" <?php echo("value = \"$id\" $readonlys"); ?>  class="nospace isi" label="Fullname">
		  	<input type="button" value="..." onClick="javascript:popIDPengguna();" <?php echo $disabled;?> ></span></td>
		</tr>		
		<!--<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
			<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Branch <font color="#FF0000">*</font></strong></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <select name="branch" id="branch" <?php echo $readonly?> class="isi" label="Branch">
				<?php
				$conn->connect();
				$sql = "select KODE, NAMA from TCABANG";
				$data = $conn->query($sql);
				$pilCabang= ($branchCD) ? "" : "<option value='' selected disabled>-- PILIH CABANG --</option>";
				while($data->next()){						
					if($branchCD == $data->get('kode')){
						$pilCabang .="<option value = '". $data->get("KODE")."' selected>".$data->get("KODE")." - ".$data->get("NAMA")."</option>";
					}else{						
						if($priv!=""){
							$pilCabang .=($priv==3)? "<option value = '". $data->get("KODE")."' >".$data->get("KODE")." - ".$data->get("NAMA")."</option>" : "";
						}else{
							$pilCabang .="<option value = '". $data->get("KODE")."' >".$data->get("KODE")." - ".$data->get("NAMA")."</option>";
						}
					}
				}
				echo $pilCabang;
				$conn->disconnect();
				?>
				</select></span><div id="pilCabang" value="<?php echo $pilCabang?>"></div></td>
		</tr>-->
		<tr>
			<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong><em>NPWP <font color="#FF0000">*</font></em></strong></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
			<input name="wpnpwp" type="text" id="wpnpwp" <?php echo("value = \"$wpnpwp\" $readonly"); ?> maxlength="15"  
            onkeyup="getComProf(this,1)" class="isi number nospace" label="NPWP" fix="15">
			<input name="wpnpwps" type="hidden" id="wpnpwp" <?php echo("value = \"$wpnpwp\""); ?>>
			</span></td>
		</tr>
		<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
			<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Company Name <font color="#FF0000">*</font></strong></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <input name="wpnama" type="text" id="wpnama" <?php echo("value = \"$wpnama\" $readonly"); ?> size="40" maxlength="30" class="isi" label="Company Name">
            </span></td>
		</tr>
		<tr>
			<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Address <font color="#FF0000">*</font></strong></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <input name="wpalamat" type="text" id="wpalamat" <?php echo("value = \"$wpalamat\" $readonly"); ?> size="60" maxlength="50" class="isi" label="Address" >
            </span></td>
		</tr>
		<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
			<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>City <font color="#FF0000">*</font></strong></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <input name="wpkota" type="text" id="wpkota" <?php echo("value = \"$wpkota\" $readonly"); ?> maxlength="30" class="isi" label="City" >
            </span></td>
		</tr>
		<tr>
			<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Postal Code <font color="#FF0000">*</font></strong></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <input name="wpzipcode" type="text" id="wpzipcode" <?php echo("value = \"$wpzipcode\" $readonly"); ?> maxlength="5" class="isi number nospace" fix='5' label="Postal Code">
            </span></td>
		</tr>
		<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
			<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>PIC <font color="#FF0000">*</font></strong></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <input name="pic" type="text" id="pic" <?php echo("value = \"$pic_\" $readonly"); ?> size="40" maxlength="50" class="isi" label="PIC" >
            </span></td>
		</tr>
		<tr>
			<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>PIC Email <font color="#FF0000">*</font></strong></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <input name="picEmail" type="text" id="picEmail" <?php echo("value = \"$picEmail_\" $readonly"); ?> size="60" maxlength="255" class="isi email nospace" label="PIC Email" >
            </span></td>
		</tr>
		<tr style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
			<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <strong>PIC Phone Number <font color="#FF0000">*</font></strong></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <input name="picPhone" type="text" id="picPhone" <?php echo("value = \"$picPhone_\" $readonly"); ?> maxlength="14" class="isi phone nospace" label="PIC Phone Number">
            </span></td>
		</tr>
		 <tr>
			<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;"><strong>Fax Number <font color="#FF0000">*</font></strong></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <input name="picFax" type="text" id="picFax" <?php echo("value = \"$picFax_\" $readonly"); ?> maxlength="14" class="isi phone nospace" label="Fax Number" >
            </span></td>
		</tr>		
		<tr  style="border-bottom: 1px solid #D7D7D7; background:#E5EEF5;">
			<td height="20" style="border-bottom: 1px solid #D7D7D7;"><span style="padding:6px 0px 6px 9px;">
            <strong>Rell ID <font color="#FF0000">*</font></strong></span></td>
			<td style="border-bottom: 1px solid #D7D7D7;">
			<span style="padding:6px 0px 6px 9px;">
				<input name="noReks" type="text" id="noRek" <?php echo("value = \"$noReks\" $readonly "); ?> 
                class="isi number nospace" label="Rell ID" >
				<input name="noReksAwal" type="hidden" value="<?php echo $noReks?>" class="noReksAwal">						
			</span></td>
		</tr>		
		</tbody>
		<tr>
			<td height="20"><span style="padding:6px 0px 6px 9px;"><strong>&nbsp;</strong></span></td>
			<td><span style="padding:6px 0px 6px 9px;">
			<input type="hidden" value="<?php echo($div); ?>" name="Submit" id="Submit"/>
			<button type="button" class="btn_1" onClick="cekFormPengguna2('frmPengguna')" style="width:60px">Save</button>&nbsp;
			<?php
			if($div=='edit'){
				echo '<button type="reset" class="btn_2" style="width:75px" >Reset</button>';
			}else{
				echo '<button type="reset" class="btn_2" style="width:75px" onclick=location.href="'.base_url.'modul/user/add" >Reset</button>';
			}
			?>           
			<button type="button" onclick="history.back()" style="width:75px" class="btn_2">Cancel</button>
			</span></td>
		</tr>
	</table>
	</form>
	<?php
		}
	}
}?>
</div>
			