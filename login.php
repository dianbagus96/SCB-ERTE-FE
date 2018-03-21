<?php
session_start();
require_once('header.php');

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
						'@&#(\d+);@e');                    // evaluate as php
		
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

if($_POST['uid']){
	$_SESSION['grpID']=cekStr(trim($_POST['grpID']));
	$_SESSION['uid']=cekStr(trim($_POST['uid']));
	$uid = $_SESSION['uid'];
	$grpID = $_SESSION['grpID'];
}else{
	$uid = $_SESSION['uid'];
	$grpID = $_SESSION['grpID'];
}
?>
<script>
$(document).ready(function(){
	$('#pwd').focus();
});
</script>
<div id="content" style="margin-top:18px; width: 942px; background:url(img/bg_pwd.png) no-repeat; height:110px;">
	<div style="font-family:arial; font-weight:lighter; font-size:11px; padding-left:10px;">
		<form id="form_login" method="post" action="verify.php">
		<input type="hidden" name="grpID" id="grpID" value="<?php echo trim($_SESSION['grpID'])?>"/>
		<input type="hidden" name="uid" id="uid" value="<?php echo trim($_SESSION['uid'])?>"/>
		<div style="height:30px;">&nbsp;</div>
		Please insert your password<br /><br />
		<table cellpadding="0" cellspacing="0">
			<tr>
				<td width="270">
					<b style="font-weight:bold;">Enter Password : </b> &nbsp;&nbsp;&nbsp;<input type="password" name="pwd" id="pwd"/>
				</td>
				<td>
					<a href="javascript: $('#form_login').submit();" class="htmlbutton2">
						<span style="margin-top:-2px;"> Proceed</span>
					</a>													
				</td>
				<td>
					<a href="javascript: document.location='index.php'" class="htmlbutton2">
						<span style="margin-top:-2px;"> Cancel</span>
					</a>													
				</td>
			</tr>
		</table>
		</form>
	</div>
</div>
<?php
require_once('footer.php');
?>