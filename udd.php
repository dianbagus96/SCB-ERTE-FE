<?php
session_start();
require_once("configurl.php");
if(in_array($_SESSION["priv_session"],array("0","3","1","2"))==false){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}elseif(time()>$_SESSION['timeout_session']){	
	echo "<script> window.location.href='".base_url."err/7/".md5(7)."';</script>";exit;
}else{
	$_SESSION['timeout_session'] = TIMEOUT;
}
$fileName = "User Matrix "."-".date("dmY").".xls";

header('Pragma: no-cache');
header('Expires: 0');
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="'.$fileName.'"');


set_time_limit(36000);
require_once("dbconn.php");
require_once("conf.php");
global $conn;
$conn->connect();
?>
<body>
<table border="1" align="center" width="2600px">
<tr>
	<td colspan="6" align="left"><h2>Daftar User </h2></td>
</tr>
<tr>
	<td bgcolor="#CCCCCC" width="100px"><div align="center"><strong>Nomor</strong></div></td>
	<td bgcolor="#CCCCCC" width="500px"><div align="center"><strong>User Name</strong></div></td>
	<td bgcolor="#CCCCCC" width="500px"><div align="center"><strong>Fullname</strong></div></td>
	<td bgcolor="#CCCCCC" width="500px"><div align="center"><strong>Email</strong></div></td>
	<td bgcolor="#CCCCCC" width="500px"><div align="center"><strong>Group ID</strong></div></td>
	<td bgcolor="#CCCCCC" width="500px"><div align="center"><strong>Role</strong></div></td>
</tr>
<?php
if(in_array($_SESSION['priv_session'],array("0"))==true){
	$SQL = "select USERLOGIN,FULLNAME,EMAIL,NPWP,ID,case
					when USERPRIV = 0 then '0 - Super Admin' 
					when USERPRIV = 1 then '1 - Admin SCB' 
					when USERPRIV = 2 then '2 - Admin SCB Aplication' 
					when USERPRIV = 3 then '3 - User Administrator' 
					when USERPRIV = 4 then '4 - Operator' 
					when USERPRIV = 5 then '5 - Supervisor' end as USERPRIV
					from TBLUSER order by ID, USERPRIV ";
}elseif(in_array($_SESSION['priv_session'],array("1","2"))==true){
		$SQL = "select usr.USERLOGIN, usr.FULLNAME, usr.EMAIL,usr.ID,case
					when usr.USERPRIV = 1 then '1 - Admin SCB' 
					when usr.USERPRIV = 2 then '2 - Admin SCB Aplication' 
					when usr.USERPRIV = 3 then '3 - User Administrator' 
					when usr.USERPRIV = 4 then '4 - Operator' 
					when usr.USERPRIV = 5 then '5 - Supervisor' end as USERPRIV,usr.ID
from TBLUSER usr 
where usr.USERPRIV IN ('1','2','3','5','4') order by usr.ID,usr.USERPRIV";
	
}elseif($_SESSION['priv_session']=='3'){
	$SQL = "select usr.USERLOGIN, usr.FULLNAME, usr.EMAIL,case
					when usr.USERPRIV = 1 then '1 - Admin SCB' 
					when usr.USERPRIV = 2 then '2 - Admin SCB Aplication' 
					when usr.USERPRIV = 3 then '3 - User Administrator' 
					when usr.USERPRIV = 4 then '4 - Operator' 
					when usr.USERPRIV = 5 then '5 - Supervisor' end as USERPRIV,usr.ID
from TBLUSER usr INNER JOIN TCOMPANY com on usr.NPWP = com.IDPAYEE 
where usr.NPWP = com.NPWP AND usr.ID='".trim($_SESSION['ID'])."' AND usr.USERPRIV IN ('3','5','4') order by usr.USERPRIV";
}
//echo $SQL;
$hasil = $conn->query($SQL);
$no = 1;	
while($hasil->next()){
	?>
		<tr>
			<td align="center"><?php echo $no;?></td>
			<td align="left"><?php echo ($hasil->get("USERLOGIN"));?></td>
			<td align="left"><?php echo $hasil->get("FULLNAME");?></td>
			<td align="left"><?php echo $hasil->get("EMAIL");?></td>
			<td align="left"><?php echo ($hasil->get("ID"));?></td>
			<td align="left"><?php echo $hasil->get("USERPRIV");?></td>
		</tr>
	<?php
	$no++;
}

$aktivitas = "Berhasil mendownload user matrix";
audit($conn,$aktivitas);				
$conn->disconnect();
?>
</table>