<?php
session_start();
require_once("configurl.php");
if($_SESSION['verified']==""){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;	
}elseif(time()>$_SESSION['timeout_session']){	
	echo "<script> window.location.href='".base_url."err/7/".md5(7)."';</script>";exit;
}else{
	$_SESSION['timeout_session'] = TIMEOUT;
}
	
require_once("dbconn.php");
$conn->connect();
$npwp = strFilter($_POST['npwp']);
$userid = strFilter($_POST['userid']);

$for = strFilter($_GET['modul']);

if($for == "npwp"){ #tbluser
	$sql = " Select a.ID as GROUP_ID, a.BRANCHCODE as BRANCH, b.NPWP, b.NAMA as NAME, ADDRESS,CITY,ZIPCODE as POSTAL_CODE, PIC, PIC_EMAIL, 
					PIC_PHONE, b.FAX_NUMBER as FAX,a.NOREK, a.CSMEMAIL,b.GROUP_ACCOUNT 
					From TBLUSER a left join TCOMPANY b on a.NPWP=b.NPWP where  b.NPWP='$npwp'";
	$d = $conn->query($sql);
	$d->next();
	$arr = array('group'=>$d->get('GROUP_ID'),'nama'=>$d->get('NAME'),'kodeCabang'=>$d->get('BRANCH'),'cabang'=>$d->get('CABANG'),'kota'=>$d->get('CITY'),'alamat'=>$d->get('ADDRESS'),'kodePos'=>$d->get('POSTAL_CODE'),'pic'=>$d->get('PIC'),'picEmail'=>$d->get('PIC_EMAIL'),'picPhone'=>$d->get('PIC_PHONE'),'picFax'=>$d->get('FAX'),'noRek'=>$d->get('NOREK'),'CSMEmail'=>$d->get('CSMEMAIL'),'group_account'=>$d->get('GROUP_ACCOUNT'));
	$arr = json_encode($arr);
	exit($arr);
}elseif($for == "userid"){#tbluser
	$group = ($_POST['id'])? $_POST['id'] : $_SESSION['ID'];		
	$d = $conn->query("Select PASSWORD FROM TBLUSER WHERE USERLOGIN='$userid' and ID='$group' and USERPRIV not in ('0')");
	$d->next();	
	$e = $conn->query("Select USER_SET FROM TBLMGTPASSWORD");
	$e->next();	
	$arr = array('jumlah'=>$d->size(),'user_set'=>$e->get("USER_SET"));
	$arr = json_encode($arr);
	exit($arr);	
}elseif($for=='dp'){ 
	$d = $conn->query("select * from TPENYETOR where NPWP='$npwp' and IDINI='".trim($_SESSION["ID"])."'");
	$d->next();
	$arr = array('jumlah'=>$d->size());
	$arr = json_encode($arr);
	exit($arr);		
}elseif($for=='wp'){ 
	$d = $conn->query("select * from TCOMPANY where NPWP='$npwp' and IDPAYEE like '%". trim($_SESSION["npwp_session"]) ."%'");
	$d->next();
	$arr = array('jumlah'=>$d->size());
	$arr = json_encode($arr);
	exit($arr);		
}elseif($for=='taxpay'){ 
	$d = $conn->query("Select NPWP, NAMA as NAME, ADDRESS,CITY,ZIPCODE From TCOMPANY Where IDPAYEE like '%". trim($_SESSION["npwp_session"]) ."%' and NPWP='".$npwp."'");
	$d->next();
	$arr = array('nama'=>$d->get('NAME'),'kota'=>$d->get('CITY'),'alamat'=>$d->get('ADDRESS'),'kodePos'=>$d->get('ZIPCODE'));
	$arr = json_encode($arr);
	exit($arr);	
}elseif($for=='frmdp'){
	$d = $conn->query("Select A.NPWP, A.NAMA as NAME, B.ACCOUNT From TPENYETOR A INNER JOIN TACCOUNT B on A.NPWP=B.NPWP Where IDINI like '%". trim($_SESSION["ID"]) ."%' and A.NPWP='".$npwp."'");
	$d->next();
	$arr = array('nama'=>$d->get('NAME'),'account'=>$d->get('ACCOUNT'));
	$arr = json_encode($arr);
	exit($arr);	
}
#ket : penyetor -> depositor, taxpayer -> tcompany
?>