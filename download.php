<?php
session_start();
require_once("configurl.php");
if(in_array($_SESSION["priv_session"],array("0","3"))==true || substr($_SESSION["AKSES"],3,1)!="1"){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}elseif(time()>$_SESSION['timeout_session']){	
	echo "<script> window.location.href='".base_url."err/7/".md5(7)."';</script>";exit;
}else{
	$_SESSION['timeout_session'] = TIMEOUT;
}
	chdir("files");
	$file = "CARA_MENDAPATKAN_FILE_FT30H.pdf";
	header("content-type:application/force-download");
	header("content-length:".filesize($file));
	header("content-disposition:Attachment;filename=$file");
	readfile($file);

?>