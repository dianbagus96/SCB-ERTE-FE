<?php
session_start(); 

require_once('header.php');
if($_SESSION["verified"]){
	echo '<script>
				document.location = \''.base_url.'modul/home/profile\';
		  </script>';
}
?>
<script>
$(document).ready(function(){
	$('#grpID').focus();
	$('input:text, input:password').each(function(){
		$(this).keypress(function(e){
			if ( e.which == 13 ){			
				$('#form_login').submit()
			} 
		});												 
	})
});

</script>
<div id="content" style="margin-top:18px; width: 100%;" align="center">
	<table cellpadding="0" cellspacing="0">
		<tr>
			<td id="login_form" style="background:url(<?php echo base_url?>img/silver_login.png) no-repeat; width:522px; height:243px;" valign="top">
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td width="224">											
							<form id="form_login" method="post" action="<?php echo base_url?>log/in" onsubmit="return val_login();">			
							<table width="245" cellpadding="0" cellspacing="0">
								<tr>
									<td width="243" style="font-family:Trebuchet MS; color:#1a68a4; font-size:18px; padding-left:30px; padding-top:27px;">Secure Login</td>
								</tr>
								<tr>
									<td style="font-family:Trebuchet MS; color:#1a68a4; font-size:12px; padding-left:30px; padding-top:20px;">Corporate ID</td>
								</tr>
								<tr>
									<td style="padding-left:30px; padding-top:8px;"><input style="width:190px;" name="grpID" id="grpID" maxlength="8"  /></td>
								</tr>
								<tr>
									<td style="font-family:Trebuchet MS; color:#1a68a4; font-size:12px; padding-left:30px; padding-top:8px;">User ID</td>
								</tr>
								<tr>
									<td style="padding-left:30px; padding-top:8px;"><input style="width:190px;" name="uid" id="uid"  maxlength="30" /></td>
								</tr>
								<tr>
									<td style="font-family:Trebuchet MS; color:#1a68a4; font-size:12px; padding-left:30px; padding-top:8px;">Password</td>
								</tr>
								<tr>
									<td style="padding-left:30px; padding-top:8px;">
										<input type="hidden" name="pwdhash" id="pwdhash"/>
										<input type="password" style="width:190px;" id="pwd"  maxlength="50" onkeyup="javascript:$('#pwdhash').val(calcMD5(this.value))" />
									</td>
								</tr>
								<tr>
									<td style="padding-left:30px; padding-top:8px;">
										<table width="100%">
											<tr>
												<td>
													<a href="#" onclick="javascript:$('#form_login').submit();" class="htmlbutton">
														<span style="margin-top:-2px;"> Login</span>
													</a>
												</td>
												<td style="font-family:Trebuchet MS; color:#1a68a4; font-size:12px;">
												<?php			
												echo "Lang : ";
												$arrBahasa = array("0"=>"Indonesia","1"=>"English");
												echo "<select id='settingBahasa' name='bahasa'>";						
												foreach($arrBahasa as $kdbhs=>$bhs){
													echo ($_SESSION['bahasa_sess']==$kdbhs) ? "<option value='".$kdbhs."' selected>".$bhs."</option>":"<option value='".$kdbhs."'>".$bhs.
														"</option>";				
												}
												echo "</select>";
												?>			
												</td>
											</tr>
										</table>
									</td>
								</tr>
						  </table>							
							<script>
							function val_login(){
								if($("#grpID").val().length<1){
									jAlert('Please Enter Your Group ID'); 																		
								}else if($("#uid").val().length<1){
									jAlert('Please Enter Your User ID'); 
								}else if($("#pwd").val().length<1){
									jAlert('Please Enter Your Password'); 																	
								}else{
									return true;
								}
								return false;								
							}
							</script>	
							</form>					
					  </td>
						<td width="242">
							<table cellpadding="0" cellspacing="0">
								<tr valign="top">
									<td width="224">&nbsp;</td>
								</tr>
								<tr valign="top">
									<td height="72">&nbsp;</td>
								</tr>
								<tr valign="top">
									<td height="36" style="padding-left:29px;"><img src="<?php echo base_url?>img/help.png" style='border:none' /></td>
								</tr>
								<tr valign="top">
									<td height="42" style="padding-left:64px; font-size:11px; font-family:Trebuchet MS; color:#666;">
										<br />																		
								</tr>
								<tr valign="top">
									<td height="36" style="padding-left:29px; "><img src="<?php echo base_url?>img/security.png"/></td>
								</tr>
								<tr valign="top">
									<td style="padding-left:64px; padding-top:8px; font-size:12px; font-family:arial; color:#fff;">	
										<table width="135" border="0" cellpadding="2" cellspacing="0" title="Click to Verify - This site chose Symantec SSL for secure e-commerce and confidential communications.">
											<tr>
												<td width="135" align="center" valign="top">
													
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</table>										
					  </td>
					</tr>
				</table>
			</td>			
		</tr>
		<tr>
		  <td valign="top" style="padding-top:18px; background:url(<?php echo base_url?>img/bg_globe.png) no-repeat bottom right;">
			<span style="font-family:arial; color:#02507c; font-weight:bold; font-size:14px;margin-left:30px"></span>
			<div style="height:18px;">&nbsp;</div>
			<table style="color:#808080; font-family:arial; font-size:12px;margin-left:30px" cellpadding="0" cellspacing="0">
				<tr>
					<td width="10" valign="middle" height="20" align="center"></td>
					<td valign="middle"></td>
				</tr>
				<tr>
					<td width="10" valign="middle" height="20" align="center"></td>
					<td valign="middle"</td>
				</tr>
			</table>          
		  </td>
	  </tr>	 
	 </table> 	
</div>
<?php
require_once("dbconn.php");
$conn->connect();
$sql = "SELECT NEWS FROM TNEWS WHERE DATEDIFF(SYSDATE,DATEFROM)>=0 AND DATEDIFF(DATETO,SYSDATE)>=0 AND ROWNUM =1 ORDER BY dateto DESC ";		
$datanews = $conn->query($sql);
$conn->disconnect();
if($datanews->next()){
	$news = '<div style="margin:0 auto; width:45%;margin-top:10px; font-family:Trebuchet MS;color:#003366;text-shadow: 0 1px 0 rgb(240, 240, 240);font-size:13px;">';
	$news .= "<marquee scrollamount='4' onmouseover='this.stop()' onmouseout='this.start()'>".$datanews->get('NEWS')."</marquee></div>";	
	echo $news;
}
require_once('footer.php');
?>

