<?php
    include('Net/SFTP.php');

    $ssh = new Net_SFTP('192.168.1.42');
    if (!$ssh->login('sarna', 'aimar')) {
        exit('Login Failed');
    }
    //echo nl2br($ssh->exec('ls'));
	//echo $ssh->get('SDS_eTax_Payment_V1.2.pdf','test_get3.pdf');
	$ssh->put('SDS_eTax_Payment_V1.2.pdf','SDS_eTax_Payment_V1.2.pdf',NET_SFTP_LOCAL_FILE);
	//echo $ssh->getSFTPLog();
?>