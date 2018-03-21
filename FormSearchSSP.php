<?php
session_start();
require_once("configurl.php");
if(trim($_SESSION['verified'])==""){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;	
}elseif(time()>$_SESSION['timeout_session']){	
	echo "<script> window.location.href='".base_url."err/7/".md5(7)."';</script>";exit;
}else{
	$_SESSION['timeout_session'] = TIMEOUT;
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Data Search</title>
<link type="text/css" href="<?php echo base_url?>css/style.css" rel="stylesheet" />	   
</head>

<body style="margin:0; padding:0;"><div>
  <table width="620" border="0" cellspacing="1" cellpadding="3">
    <tr>
      <td class="bodypage">	  
	  <?php
	require_once('conf.php');
	
	$order = strFilter($_GET['order']);
	$sort = strFilter($_GET['sort']);	
	$kat = strFilter($_GET['lstCari']);
	
	
	$myTask = strFilter($_REQUEST['task']);
	$form = strFilter($_REQUEST['frm']);
	$colm = strFilter($_REQUEST['colm']);
	$impid = strFilter($_REQUEST['impid']);
	$kantor = strFilter($_REQUEST['kantor']);
	$groupid = strFilter($_REQUEST['groupid']);
	$deptid = strFilter($_REQUEST['deptid']);
	$tipe = strFilter($_REQUEST['tipe']);
	
	$list = array();
	$SQL = "";
	switch($myTask){
		case "wp":
			$x1 = array("NPWP","NAMA");		
			
			$kat = (in_array(strtoupper($kat),$x1)==1)? $kat : $x1[0];
			$orderby = setSortir(array('sort'=>'NAMA','order'=>'desc'),$x1);	
			$list[] = array("NPWP","NPWP");
			$list[] = array("NAMA","Nama");
			$SQL = "Select NPWP, NAMA, ADDRESS,CITY,ZIPCODE
					From TCOMPANY 
					Where IDPAYEE like '%". trim($_SESSION["npwp_session"]) ."%'";
			break;
		case "user":
			$x1 = array("NPWP","NAMA");
			$kat = (in_array(strtoupper($kat),$x1))? $kat : $x1[0];
			$orderby = setSortir(array('sort'=>'NAMA','order'=>'desc'),$x1);	
			
			$list[] = array("NPWP","NPWP");
			$list[] = array("NAMA","Taxpayer Name");
			$SQL = "Select NPWP, NAMA as NAME, ADDRESS,CITY,ZIPCODE
					From TCOMPANY";
			break;
		case "userID":			
			$x1 = array("a.ID","a.BRANCHCODE","b.NPWP","b.NAMA","Address","City","ZIPCODE","PIC","PIC_Email","PIC_Phone","Fax_Number","a.NoRek");
			$x2 = array("Group_ID","Branch","NPWP","Name","Address","City","Postal_Code","PIC","PIC_Email","PIC_Phone","Fax_Number","No_Rek");
			
			$kat = (in_array($kat,$x1))? $kat : $x1[0];
			$orderby = setSortir(array('sort'=>'NAMA','order'=>'desc'),$x1);	
			
			$list[] = array("a.ID","Corp ID");
			$list[] = array("a.BRANCHCODE ","Branch");
			$list[] = array("b.NPWP","NPWP");			
			$list[] = array("b.NAMA","Taxpayer Name");
			$list[] = array("Address","Address");
			$list[] = array("City","City");
			$list[] = array("ZIPCODE","Postal Code");
			$list[] = array("PIC","PIC");
			$list[] = array("PIC_Email","PIC Email");			
			$list[] = array("PIC_Phone","PIC Phone");
			$list[] = array("Fax_Number","Fak Number");
			$list[] = array("a.NoRek","Rell ID");

			
			$SQL = "select b.ID as Corp_ID, b.NPWP, b.NAMA as Name, Address,City,ZIPCODE as Postal_Code, PIC, 
					PIC_Email,PIC_Phone, b.Fax_Number,a.NoRek as Rell_ID
					from TCOMPANY b left join TBLUSER a on a.NPWP=b.NPWP where b.PIC is not NULL
					Group By b.ID,a.BRANCHCODE,b.NPWP,b.NAMA, ADDRESS,CITY,ZIPCODE,PIC,PIC_EMAIL,PIC_PHONE,b.Fax_Number,a.NoRek";		
			#echo $SQL;
			
			break;
		case "map":
			$x1 = array("KDJNSBYR","KDMAP6","NMJNSBYR");
			$kat = (in_array(strtoupper($kat),$x1))? $kat : $x1[0];
			$orderby = setSortir(array('sort'=>'NMJNSBYR','order'=>'desc'),$x1);	
			
			$list[] = array("KDJNSBYR","Payment Type Code");
			$list[] = array("KDMAP6","MAP Code");
			$list[] = array("NMJNSBYR","Description");
			$SQL = "Select DISTINCT KDMAP6 as MAP_CODE, KDJNSBYR as PAYMENT_CODE, NMJNSBYR as DESCRIPTION
					From TJNSBYR ";
			break;
		case "datP":
			$x1 = array("A.NPWP","B.USERS","B.ACCOUNT");
			$kat = (in_array(strtoupper($kat),$x1))? $kat : $x1[0];
			$orderby = setSortir(array('sort'=>'USERS','order'=>'desc'),$x1);	
			
			$list[] = array("A.NPWP","NPWP");
			$list[] = array("B.USERS","Depositor Name");
			$list[] = array("B.ACCOUNT","No. Account");
			$list[] = array("B.TIPEACC","Account Type");
			
			$SQL = "Select A.NPWP, B.USERS as NAME, B.ACCOUNT, B.TIPEACC as 'Account Type'
					From TPENYETOR A INNER JOIN TACCOUNT B on A.NPWP=B.NPWP
					Where B.STSACC='SSP' and IDINI like '%". trim($_SESSION["ID"]) ."%' and B.FREEZE='N'
					AND ( 
						(B.GROUPID='".trim($_SESSION['groupid_session'])."' AND B.DEPTID='".trim($_SESSION['deptid_session'])."') or 
						(B.GROUPID='".trim($_SESSION['groupid_session'])."' AND (B.DEPTID='' or B.DEPTID is NULL)) or
						(
							(B.GROUPID='' or B.GROUPID is NULL)  AND (B.DEPTID='' or B.DEPTID is NULL) 
						) 
					)";
			break;
		case "datAcc":
			$x1 = array("NPWP","NAMA");
			$kat = (in_array(strtoupper($kat),$x1))? $kat : $x1[0];
			$orderby = setSortir(array('sort'=>'NAMA','order'=>'desc'),$x1);	
			
			$list[] = array("NPWP","NPWP");
			$list[] = array("NAMA","Nama Wajib Pajak");
			$SQL = "Select NPWP, NAMA as NAME From TPENYETOR Where IDINI like '%". trim($_SESSION["ID"]) ."%'";
			break;
		case "cabang":
			require('dbconndb.php');
			$conn = &$connDB;			
			$x1 = array("BRCHCD","NMBRCH");
			$kat = (in_array(strtoupper($kat),$x1))? $kat : $x1[0];
			$orderby = setSortir(array('sort'=>'NMBRCH','order'=>'desc'),$x1);	
			
			$list[] = array("BRCHCD","Code");
			$list[] = array("NMBRCH","Branch Name");
			$SQL = "select BRCHCD,NMBRCH from tblcabang ";
			break;
		case 'taxrekening':
			$x1 = array("tg.GROUPID","td.DEPTNAME","tg.Deskripsi");
			$kat = (in_array($kat,$x1))? $kat : $x1[0];
			$orderby = setSortir(array('sort'=>'GROUPID','order'=>'desc'),$x1);	
			
			$list[] = array("tg.GROUPID","Group ID");
			$list[] = array("td.DEPTNAME","Departement");
			$list[] = array("tg.Deskripsi","Deskripsi");
			
			$SQL = "Select tg.GROUPID AS 'Group ID',td.DEPTID as 'DEPT Code', td.DEPTNAME as Departement, tg.Deskripsi as Description 
					From TBLGROUP tg left join TBLDEPT td on tg.GROUPID=td.GROUPID AND CORPID='".trim($_SESSION['ID'])."' 
					where tg.npwp='".trim($_SESSION['npwp_session'])."'";	
			break;
		case 'groupid':
			$x1 = array("tg.GROUPID","tg.NAMA","td.DEPTNAME","tg.Deskripsi");
			$kat = (in_array($kat,$x1))? $kat : $x1[0];
			$orderby = setSortir(array('sort'=>'GROUPID','order'=>'desc'),$x1);	
			
			$list[] = array("tg.GROUPID","Group ID");
			$list[] = array("tg.NAMA","Group Name");
			$list[] = array("td.DEPTNAME","Departement");
			$list[] = array("tg.Deskripsi","Deskripsi");
			
			$SQL = "Select tg.GROUPID AS 'Group ID',tg.NAMA AS 'Group Name',td.DEPTID as 'DEPT Code', td.DEPTNAME as Departement, tg.Deskripsi as Description 
					From TBLGROUP tg left join TBLDEPT td on tg.GROUPID=td.GROUPID AND CORPID='".trim($_SESSION['ID'])."' 
					where tg.npwp='".trim($_SESSION['npwp_session'])."'";	
			break;
		case 'usergroup':			
			$x1 = array("usr.USERLOGIN","usr.FULLNAME","usr.EMAIL");
			$kat = (in_array($kat,$x1))? $kat : $x1[0];
			$orderby = setSortir(array('sort'=>'USERLOGIN','order'=>'desc'),$x1);	
			
			$list[] = array("usr.USERLOGIN","User ID");
			$list[] = array("usr.FULLNAME","Full Name");
			$list[] = array("usr.EMAIL","Email");
			
			$SQL = "select usr.USERLOGIN as User_ID,usr.FULLNAME as FULL_Name,usr.EMAIL as Email
					from TBLUSER usr where usr.ID='".trim($_SESSION['ID'])."' 
					AND (usr.USERPRIV NOT IN ('0','1','2','3','4','5','6') OR usr.USERPRIV is NULL)";
					
			break;			
		case 'userdept':			
			$x1 = array("usr.USERLOGIN","usr.FULLNAME","usr.EMAIL");
			$kat = (in_array($kat,$x1))? $kat : $x1[0];
			$orderby = setSortir(array('sort'=>'USERLOGIN','order'=>'desc'),$x1);	
			
			$list[] = array("usr.USERLOGIN","User ID");
			$list[] = array("usr.FULLNAME","Full Name");
			$list[] = array("usr.EMAIL","Email");
			
			$SQL = "select usr.USERLOGIN as User_ID,usr.FULLNAME as FULL_Name,usr.EMAIL as Email
					from TBLUSER usr where usr.ID='".trim($_SESSION['ID'])."' 
					AND (usr.USERPRIV NOT IN ('0','1','2','3','4','5','6') OR usr.USERPRIV is NULL)";
			
			break;
		case 'pic':			
			$x1 = array("usr.USERLOGIN","usr.FULLNAME","usr.EMAIL");
			$kat = (in_array($kat,$x1))? $kat : $x1[0];
			$orderby = setSortir(array('sort'=>'USERLOGIN','order'=>'desc'),$x1);	
			
			$list[] = array("usr.USERLOGIN","User ID");
			$list[] = array("usr.FULLNAME","Full Name");
			$list[] = array("usr.EMAIL","Email");
			
			$SQL = "select usr.USERLOGIN as User_ID,usr.FULLNAME as FULL_Name,usr.EMAIL as Email
					from TBLUSER usr where usr.ID='".trim($_SESSION['ID'])."' 
					AND usr.USERPRIV='6'";
			
			break;			
		case 'checker':			
			$x1 = array("usr.USERLOGIN","usr.FULLNAME","usr.EMAIL");
			$kat = (in_array($kat,$x1))? $kat : $x1[0];
			
			$checker = explode(",",substr($deptid,1,strlen($deptid)-1));
			$checkers = "";
			foreach($checker as $c){
				$checkers .= "'".$c."',";
			}
			$checkers = substr($checkers,0,strlen($checkers)-1);
			$orderby = setSortir(array('sort'=>'USERLOGIN','order'=>'desc'),$x1);	
			
			$list[] = array("usr.USERLOGIN","User ID");
			$list[] = array("usr.FULLNAME","Full Name");
			$list[] = array("usr.EMAIL","Email");
			
			$SQL = "select usr.USERLOGIN as User_ID,usr.FULLNAME as FULL_Name,usr.EMAIL as Email
					from TBLUSER usr 
					where usr.ID='".trim($_SESSION['ID'])."' and usr.USERLOGIN IN (".$checkers.")";
			
			break;	
		case 'kpbc':
			require('dbconndb.php');
			$conn = &$connDB;
			$x1 = array("KDKPBC","URKDKPBC","KOTA");			
			$kat = (in_array($kat,$x1))? $kat : $x1[0];
			$orderby = setSortir(array("sort"=>"kdkpbc","order"=>"desc"),$x1);		
			$list[] = array("KDKPBC","KODE");
			$list[] = array("URKDKPBC","KPBC");
			$list[] = array("KOTA","KOTA");			
			
			$SQL = "SELECT KDKPBC,URKDKPBC as KPBC,KOTA FROM TBLKPBC";	
			break;			
		default:
	}
	$_GET['lstCari'] = $_REQUEST['lstCari']= strFilter($kat);
	$_GET['page'] = $_REQUEST['page'] = (ereg('[^0-9]',$hal = $_REQUEST['page']))? 1 : $hal;
    	$_GET['txtpage0'] = $_REQUEST['txtpage0'] = (ereg('[^0-9]',$hal = $_REQUEST['txtpage0']))? 1 : $hal;
	$_GET['txtCari'] = $_REQUEST['txtCari'] = strFilter($_REQUEST['txtCari']);
	
	$conn->connect();
	$mySearch = new Search($conn,$form,$colm);
	$mySearch->set_param($myTask,$impid,$kantor,$groupid,$deptid,$tipe);
	$mySearch->SQL = $SQL;
	//echo $SQL;
	$mySearch->clear_List();
	$mySearch->add_categori($list);
	$mySearch->drawSearch();
	$conn->disconnect();
?>
	  
	  </td>
    </tr>
  </table>
</div>
</body>
</html>
