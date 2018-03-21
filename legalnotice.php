<?php
session_start();		  
require_once('header.php');
?>
<div id="content" style="margin-top:24px; width: 942px; font-family:arial; font-size:11px; padding-left:14px;">
	<span style="color:#005D9A; font-weight:bold; cursor:pointer;" onclick="ntu();">Notice to Users</span><b style="color:#86c533"> &nbsp; | &nbsp; </b><span style="color:#005D9A; font-weight:bold; cursor:pointer;" onclick="iln();">Important Legal Notice</span>
	<div id="ntu" style="margin-top: 18px; display:none;">
		<span style="color:#1a68a4; font-size:14px; font-weight:bold;">
			Notice to Users
		</span>
		<br /><br />
		<div style="color:#333333; font-size:11px; font-family:arial;">
		The use of this service provided by Mandiri or any other Mandiri group company to you is subject to the terms and conditions stipulated in the agreements entered into between Mandiri or any other Mandiri group company and you. You acknowledge that the users of the service, being your representatives are advised to always check with your company administrators, information security department, and/or any other relevant department, on all security matters in relation to the use of this service including the agreements that govern the terms of this service.		
		</div>
	</div>
	<div id="iln" style="margin-top: 18px; display:none;">
		<span style="color:#1a68a4; font-size:14px; font-weight:bold;">
			Important Legal Notice
		</span>
		<br /><br />
		<div style="color:#333333; font-size:11px; font-family:arial;">

		</div>
	</div>
</div>
<script>
	function ntu(){
		$("#iln").css('display', 'none');
		$("#ntu").css('display', 'run-in');
	}
	function iln(){
		$("#ntu").css('display', 'none');
		$("#iln").css('display', 'run-in');
	}
</script>