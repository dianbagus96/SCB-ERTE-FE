<?php
if($_SESSION['verified']==""){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;	
}else{
	

 
function simple_encrypt($text)
{
	$salt ='e-RTE SCB by EDI INDONESI @ ';		
    return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
}
 

?>
<form name="frm_down_csv" action="<?php echo base_url?>down_csv.php" method="post">
<input type="hidden" name="cari" value="<?php echo simple_encrypt($sql); ?>">
<input type="hidden" name="for" value="<?php echo $for; ?>">
<input type="hidden" name="download" value="Download">
<!--<input type="submit" value="Download" name="download">
-->
<button class="btn_down" type="submit" name="submit" style="width:260px;">&nbsp;&nbsp;Download</button>
</form>
<?php
}
?>
