<?php
/*******************************************************************************
* Software: FTP Class                                                          *
* Version	: 1.00                                                               *
* Date		: 15-09-2005                                                         *
* Author	: Martinus J Wahyudi                                                 *
* License	: Freeware                                                           *
* Desc		: Class for collecting ftp function, try to make simpler the coding  * 
*					  process. This Class use the built in function of PHP itself. so it *
*					  just exposure the function.                                        *  
*                                                                              *
* You may use, modify and redistribute this software as you wish.              *
*******************************************************************************/
class Ftp {
		
		/* var for handle the ftp host */
		var $ftp_server;
		
		/* var for handle the ftp user login */
		var $ftp_user;
		
		/* var for handle the ftp password login */
		var $ftp_pass;
		
		/* var for handle the ftp connection id, so we can use it later for many purpose */
		var $con_id;
		
		var $mode;
		var $login_result;
		
		function ftp(){
			$this->ftp_server = "192.168.23.193";
			$this->ftp_user = "ftprte";
			$this->ftp_pass = "Mandiri123";
			$this->mode = FTP_BINARY;
		}
		
		function connect(){
			$this->con_id = ftp_connect($this->ftp_server); 
  			$this->login_result = ftp_login($this->con_id, $this->ftp_user, $this->ftp_pass); 
		}
		
		function disConnect(){
			ftp_close($this->con_id);
		}
		
		function getFile($source,$destination){
		}
		
		function putFile($source,$destination){
		  	$upload = ftp_put($this->con_id, $destination,$source, $this->mode); 
		}
		
		function setAsBinary(){
			$this->mode = FTP_BINARY;
		}
		
		function setAsASCII(){
			$this->mode = FTP_ASCII;
		}
}	
?> 