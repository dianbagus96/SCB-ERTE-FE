<?php
session_start();
require_once('header.php');
require_once("dbconn.php");
$err=$_GET['err'];
if(md5($err)!=$_GET['seccode']){ 
	echo "<script> window.location.href='".base_url."';</script>";exit;
}else{
	if($err == "1"){
		$message = "Invalid Username and Password ";
		$button = '<button class="btn_1" onclick="document.location=\''.base_url.'\'" type="button">Back</button>';				
	}else if($err == "2"){
		$message = "Sorry, you have entered an invalid Group ID or User ID. Please try again. ";
		$button = '<button class="btn_1" onclick="document.location=\''.base_url.'\'" type="button">Home</button>';		
	}else if($err == "3"){		
		if($_SESSION['verified']==""){			
			$message = "Session timeout. ";	
		}else{
			$message = "You have no access! ";
		}
		$button = '<button class="btn_1" onclick="document.location=\''.base_url.'\'" type="button">Back</button>';		
	}else if($err == "4"){
		$message = "User is still Login ";
		$button = '<button class="btn_1" onclick="document.location=\''.base_url.'\'" type="button">Back</button>';	
	}else if($err == "5"){
		session_destroy();
		$message = "Your Account has ben blocked... please contact administrator ";		
		$button = '<button class="btn_1" onclick="document.location=\''.base_url.'\'" type="button">Home</button>';		
	}else if($err == "6"){
		$message = "You have no priviledge... please contact administrator ";
		$button = '<button class="btn_1" onclick="document.location=\''.base_url.'\'" type="button">Back</button>';		
	}else if($err == "7"){	
		$conn->connect();
		$user = trim($_SESSION['uid_session']);
		$group = trim($_SESSION['ID']);
		$q = "update TBLUSER set LOGIN='N' where USERLOGIN='$user' and ID='$group'";
		$conn->execute($q);	
		$conn->disconnect();
		session_destroy();
		$message = "Session timeout. ";			
		$button = '<button class="btn_1" onclick="document.location=\''.base_url.'\'" type="button">Back</button>';		
	}
	?>
	<div id="content" style="margin-top:18px; width: 100%; background:#F0F0F0; border: 1px solid #D7D7D7;">
		<div style="font-family:arial; font-weight:lighter; font-size:11px; padding-top:10px; padding-bottom:10px; padding-left:10px;">
			<table cellpadding="0" cellspacing="0" style="font-family:arial; font-weight:lighter; font-size:11px;">
				<tr>
					<td><img src="<?php echo base_url?>img/warning.png" style='border:none'/></td>
					<td style="padding-left:20px; font-family:arial; font-size:11px; color:red; font-weight:bold;"><?php echo $message; ?> &nbsp;<?php echo $button; ?></td>
				</tr>
			</table>
		</div>
	</div>
	<?php
	require_once('footer.php');
}
?>