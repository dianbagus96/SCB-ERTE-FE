<?php
require_once("configurl.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
  	<head>
		<meta http-equiv="author" content="SCB e-RTE">
		<title>Standard Chartered Bank e-RTE</title>
		<link rel="Shortcut Icon" href="<?php echo base_url?>img/lg.ICO" type="image/x-icon" />
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
		<link type="text/css" href="<?php echo base_url?>css/style.css" rel="stylesheet" />	
		<link type="text/css" href="<?php echo base_url?>css/jquery.jscrollpane.css" rel="stylesheet" />	   
        <script type="text/javascript" src="<?php echo base_url?>js/jquery.js"></script> 
        <script type="text/javascript" src="<?php echo base_url?>js/jquery.tools.min.js"></script> 
    	<script type="text/javascript" src="<?php echo base_url?>js/jquery-ui-1.8.1.custom.min.js"></script> 
  	    <link type="text/css" href="<?php echo base_url?>js/jAlert/jquery.alerts.css" rel="stylesheet" />	   
    	<script type="text/javascript" src="<?php echo base_url?>js/jAlert/jquery.alerts.js"></script> 
    	<script type="text/javascript" src="<?php echo base_url?>js/gcm.js"></script> 
		<script type="text/javascript" src="<?php echo base_url?>js/encrypt.js"></script>         
		<script type="text/javascript" src="<?php echo base_url?>js/jquery.jscrollpane.min.js"></script>         
		<script type="text/javascript" src="<?php echo base_url?>js/jquery.mousewheel.js"></script>         
	</head>
	<body style="margin-left:18px; margin-right:18px;">
		<div style="width:100%;" >
			
			<div >
			<table width="100%" cellspacing="0" cellpadding="0" border="0">	<tr>
				<td width="20%" height="90" bgcolor="#0B9F47" align="center">
				<img width="200" height="70" src="<?php echo base_url?>img/scb-lg.png">
				</td>
				<td width="10%" bgcolor="#08833A"> </td>
				<td width="15%" bgcolor="#104E6B"> </td>
				<td width="5%" bgcolor="#1991CA"> </td>
				<td width="40%" bgcolor="#0071A7"><font style="font-size:200%; color:white;font-style:oblique;margin-left:2cm;">RINCIAN TRANSAKSI EKSPOR (e-RTE)</font> </td>
				</tr></table>
				</div>
				<?php							
				if($_SESSION["nmuser_session"]){
				?>
				<div style="width:100%;" align="center">
                    <div  style="width:100%; background:#F0F0F0; border-bottom: 2px solid #D7D7D7; height:26px; font-family:arial; font-size:11px; margin-top:-2px; position: 2px 2px 2px 2px;" >
                        <div  style="width:100%; color:#666666;">						
                            <table cellpadding="0" cellspacing="0" width="100%" >
                                <tr>
                                    <td align="left" style="padding-left:20px;">You are logged in as : <b><?php echo $_SESSION["nmuser_session"]; ?></b></td>
                                    <td align="right" style="padding-right:5px;">
                                        <table cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td>
                                                    <span id="formWaktu">
                                                        <script type="text/javascript" language="javascript">
                                                            set_datetimeclock('formWaktu');
                                                        </script>
                                                    </span>											
                                                </td>
                                                <td style="padding-right:5px; padding-left:20px;">
                                                    <a href="javascript: document.location='<?php echo base_url?>log/out'" class="htmlbutton">
                                                        <span style="margin-top:-2px;">Logout</span>
                                                    </a>																																
                                                </td>
                                            </tr>
                                        </table>
                                      </td>
                                  </tr>
                            </table>						
                        </div>
                    </div>
				</div>
				<?php
				}
				?>
			
