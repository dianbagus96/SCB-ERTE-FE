<?php
session_start();
require_once("configurl.php");
if(in_array($_SESSION["priv_session"],array("0","3"))==true  || substr($_SESSION["AKSES"],3,1)!="1"){
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
		require_once("dbconndb.php");	
		
		$order = strFilter($_GET['order']);
		$sort = strFilter($_GET['sort']);	
		$kat = strFilter($_GET['lstcari']);
		
		$x1 = array("SANDI","NAMA_SANDI","KETERANGAN");
		$kat = (in_array(strtoupper($kat),$x1))? $kat : $x1[0];
		
		$orderby = setSortir(array('sort'=>'NAMA_SANDI','order'=>'desc'),$x1);
			
		$form = strFilter($_REQUEST['frm']);
		$colm = strFilter($_REQUEST['colm']);
		$impid = strFilter($_REQUEST['impid']);
		$kantor = strFilter($_REQUEST['kantor']);
		
		$list[] = array("Sandi","Sandi");
		$list[] = array("Nama_Sandi","Nama Sandi");
		$list[] = array("Keterangan","Keterangan");
		$SQL = "Select Sandi,Nama_Sandi,Keterangan from tbldmSandiRTE";
		
		$_GET['lstCari'] = $_REQUEST['lstCari']= strFilter($kat);
		$_GET['page'] = $_REQUEST['page'] = (ereg('[^0-9]',$hal = $_REQUEST['page']))? 1 : $hal;
    	$_GET['txtpage0'] = $_REQUEST['txtpage0'] = (ereg('[^0-9]',$hal = $_REQUEST['txtpage0']))? 1 : $hal;
		$_GET['txtCari'] = $_REQUEST['txtCari'] = strFilter($_REQUEST['txtCari']);
		
		$conn->connect();
		$mySearch = new Search($connDB,$form,$colm);
		$mySearch->set_param($myTask,$impid,$kantor);
		$mySearch->SQL = $SQL;
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
