<?php
require_once('library/phpmailer/class.phpmailer.php');
//require_once("dbconn.php");
function broadcast(){
	global $error;
	global $conn;
	$to = $email;				
				$subject = "SCB E-RTE";								
				$d = $conn->query("select * FROM TBLUSER"); 
				while($d->next()){
					$email 		=  $d->get('EMAIL');
					$fullname 	= $d->get('FULLNAME');
					$bodi 		= "Yth. ".ucfirst($fullname).",
						<br><br>Account Anda telah berhasil terdaftar pada SCB E-RTE.
						<br>Silahkan mengakses aplikasi SCB E-RTE 
						di link <a href='http://standardchartered.ebank-services.com/RTE/'> SCB E-RTE </a>.<br>
						Untuk keamanan silahkan lakukan perubahan pada password Anda secara berkala.
						<br><br>Terimakasih<br><br><b>Admin SCB E-RTE</b><br><br>";	
				};				
				
				 $conn->disconnect();
		
		$to=trim($email);
		$body = $bodi;	
		$subject = 'Reminder SCB E-RTE';
		$mail = new PHPMailer();  // create a new object
		$mail->IsSMTP(); // enable SMTP
		$mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
		$mail->SMTPAuth = false;  // authentication enabled
		$mail->Host = '10.1.12.1';
		$mail->Port = 25; 	
		$mail->SetFrom("e-rte@sc.com", "Admin E-RTE SCB");
		$mail->Subject = $subject;		
		$mail->ContentType = "text/html"; 
		$mail->Body = $body;
		$mail->AddAddress($to);	
		$mail->Send();			
				
	
	}
function sendEmail($data) { 
		global $error;
		
		$to=trim($data['to']);
		$body = $data['isi'];
		$subject = $data['subject'];
		$mail = new PHPMailer();  // create a new object
		$mail->IsSMTP(); // enable SMTP
		$mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
		$mail->SMTPAuth = false;  // authentication enabled
		$mail->Host = '10.1.12.1';
		$mail->Port = 25; 	
		$mail->SetFrom("e-rte@sc.com", "Admin E-RTE SCB");
		$mail->Subject = $subject;		
		$mail->ContentType = "text/html"; 
		$mail->Body = $body;
		$mail->AddAddress($to);	
		$mail->Send();
	}
function send_Email($data) { 
		global $error;
		$to	= explode(";",$data['to']);
		$kepada = explode(";",$data['kepada']);
			for($i=0;$i<count($kepada);$i++){
				
				$body    = "Yth. ".ucfirst($kepada[$i]).",";
				$body 	.= $data['isi'];
				$subject = $data['subject'];
				$mail = new PHPMailer();  // create a new object
				$mail->IsSMTP(); // enable SMTP
				$mail->SMTPDebug = 0;  // debugging: 1 = errors and messages, 2 = messages only
				$mail->SMTPAuth = false;  // authentication enabled
				$mail->Host = '10.1.12.1';
				$mail->Port = 25; 		
				#$mail->AddBCC("sarna@edi-indonesia.co.id");	
				$mail->SetFrom("e-rte@sc.com", "Admin E-RTE SCB");
				$mail->Subject = $subject;		
				$mail->ContentType = "text/html"; 
				$mail->Body = $body;
				$mail->AddAddress($to[$i]);
				$mail->Send();
			}
		return true;	
	}
?>