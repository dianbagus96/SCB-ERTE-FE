<?php
session_start();
require_once('header.php');
?>
<div id="content" style="margin-top:24px;">
	<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td style="width:174px;" valign="top" width="10%">
			<?php			
			require_once('menu.php');
			?>
			</td>			
			<td style="padding-left:18px; font-family:arial;" valign="top">
			<div style="margin-left:20px;margin-right:10px">
            <?php
				function getView($div,$menu,$else){
					foreach($menu as $a=>$b){
						$arrdiv[] = $a;
					}											 										
					return (in_array($div,$arrdiv))?$menu[$div] :$else;	
				}				
				$mod = $_GET['mod'];
				$div = $_GET['div'];
				$seccode = $_GET['seccode'];
				//print_r ($_GET);

				switch($mod){
					default 		:	$view = 'home';break;
					case 'user'		:	$menu = array(
											'add' => 'form_admin',
											'edit' => 'form_admin',
											'lock' => 'view_user',
											'unlock' => 'view_user',
											'view' => 'view_user',
											'delete' => 'view_user',
											'insert' => 'form_admin',
											'update' => 'form_admin',
											'unlog'=>'view_user',
											'reset'=>'reset_password'
											);																				
										$view = getView($div,$menu,'home');
										break;
					case 'email'	:	$menu = array(
											'add' => 'form_email',
											'edit' => 'form_email',
											'view' => 'manage_email',
											'delete' => 'manage_email',
											'insert' => 'form_email',
											'update' => 'form_email'
											);										
										$view = getView($div,$menu,'home');
										break;
					/*case 'emailtest'	:	$menu = array(
											'add' => 'form_emailtest',
											'edit' => 'form_emailtest',
											'view' => 'manage_emailtest',
											'delete' => 'manage_emailtest',
											'insert' => 'form_emailtest',
											'update' => 'form_emailtest'
											);										
										$view = getView($div,$menu,'home');
										break;*/
					case 'data'	:	$menu = array(
											'add' => 'form_data',
											'edit' => 'form_data',
											'view' => 'manage_data',
											'delete' => 'manage_data',
											'insert' => 'form_data',
											'update' => 'form_data'
											);										
										$view = getView($div,$menu,'home');
										break;
					case 'cabang'	:	$menu = array(
											'add' => 'form_cabang',													
											'edit' => 'form_cabang',
											'view' => 'manage_cabang',
											'delete' => 'manage_cabang',
											'insert' => 'form_cabang',
											'update' => 'form_cabang'
											);										
										$view = getView($div,$menu,'home');
										break;
					case 'rekening'	:	$menu = array(
											'add' => 'form_rekening',													
											'edit' => 'form_rekening',
											'view' => 'manage_rekening',
											'delete' => 'manage_rekening',
											'insert' => 'form_rekening',
											'update' => 'form_rekening'
											);										
										$view = getView($div,$menu,'home');
										break;	
					case 'groupid'	:	$menu = array(											
											'view' => 'manage_groupid',
											'delete' => 'manage_groupid'											
											);										
										$view = getView($div,$menu,'home');	
										break;	
					/*case 'credid'	:	$menu = array(	
											'add' => 'manage_credential',													
											'edit' => 'manage_credential',
											'view' => 'manage_credential',
											'delete' => 'manage_credential',
											'insert' => 'manage_credential',
											'update' => 'manage_credential',
											'new' => 'view_credential'
										    );										
										$view = getView($div,$menu,'home');	
									//echo ($view);
										break;		*/
					case 'npwp'	:	$menu = array(	
											'add' => 'form_npwp',													
											'edit' => 'form_npwp',
											'view' => 'manage_npwp',
											'delete' => 'manage_npwp',
											'insert' => 'form_npwp',
											'update' => 'form_npwp'			
											);										
										$view = getView($div,$menu,'home');	
										break;		
					/*case 'depositor'	:	$menu = array( #account depositor											
												'acc' => 'manage_rekdepositor',
												'add' => 'form_rekdepositor',
												'insert' => 'form_rekdepositor',
												'edit' => 'form_rekdepositor',
												'update' => 'form_rekdepositor',
												'delete' => 'form_rekdepositor',											
												'detail' => 'form_rekdepositor'											
											);										
										$view = getView($div,$menu,'home');
										break;	*/						
					case 'audit'	:	$menu = array(
											'trail' => 'view_trail',
											'logtrail' => 'view_logtrail'
											);
										$view = getView($div,$menu,'home');
										break;
					case 'selisih'	:	$menu = array(
											'view' => 'manage_selisih',
											'update' => 'manage_selisih'
											);
										$view = getView($div,$menu,'home');
										break;				
					case 'sandi'	:	$menu = array(
											'view' => 'manage_sandi',
											'add' => 'form_sandi',
											'edit' => 'form_sandi',
											'delete' => 'manage_sandi',
											'insert' => 'form_sandi',
											'update' => 'form_sandi'	
											);
										$view = getView($div,$menu,'home');
										break;					
					case 'manage'	:	$menu = array(	'profile','taxpayer',
														'edittaxpayer','editdepositor',
														'updateprofile','updatetaxpayer',
														'updatedepositor','updatepassword',
														'deletetaxpayer','deletedepositor',
														'inserttaxpayer','insertdepositor',
														'activation'																												
													);
										$view = (in_array($div,$menu))?'usr_mgt' :'home';							
										break;
					case 'upload'	:	$menu = array(	'taxpayer','bacataxpayer','douploadtaxpayer'
													);
										$view = (in_array($div,$menu))?'upload_taxpayer' :'home';							
										break;
					case 'nearmatching'	: $view = 'manage_nearmatching';break;
					case 'setting'		: $menu = array(
													'setting' => 'setting',
													'kurs' => 'view_kurs'
														);
										  $view = getView($div,$menu,'home');
										  break;
					case 'news'			: $view = 'newsedit';break;
					case 'pib'			:	$view = 'view_pib';break;
					case 'peb'			:	$menu = array(
														'upload' => 'upload_peb',
														'baca' => 'upload_peb',
														'doupload' => 'upload_peb',
														'baru' => 'view_peb',
														'plus' => 'view_peb',
														'toplus' => 'view_peb',
														'plusupload' => 'view_peb',														
														'terlaporkan' => 'view_peb',
														'update' => 'view_peb',
														'delete' => 'view_peb',
														'deleteterlaporkan' => 'view_peb',
														'batal' => 'view_peb',
														'pilihdanamasuk' => 'combinedanamasuk',
														'pilihtanpadanamasuk' => 'combinedanamasuk',
														'pilihdanamasukpending' => 'combinedanamasuk',#from peb 90+
														'pilihdanamasukpendings' => 'combinedanamasuk',#from peb terlaporkan
														'rtedanamasuk'=>'combinedanamasukexe',
														'rtetanpadanamasuk'=>'combinedanamasukexe',
														'create'=>'form_peb',
														'insert'=>'form_peb',
														'edit'=>'form_peb',						
														'editexe'=>'form_peb',														
													);
											$view = getView($div,$menu,'home');
											break;
					case 'danamasuk'	: 	$menu = array(
														'pilihpeb' => 'combinepeb',														
														'pilihpebcampuran' => 'combinepeb',
														'pilihpebuangmuka' => 'combinepeb',
														'danaterlaporkanpilihpeb' => 'combinepeb',					
														'uangmukarte' => 'combinepebexe',
														'eksporrte' => 'combinepebexe',
														'rtepilihpeb' => 'combinepebexe',
														'monitor' => 'view_danamasukmonitor',
														'add' => 'form_danamasuk',
														'splitdana' => 'splitdana',
														'insert' => 'form_danamasuk'   
													);
											$view = getView($div,$menu,'view_danamasuk');																		
											break; 
					case 'rte'			:	switch($div){
												case "rtepilihpeb" 	:	$view = "combinepeb"; break;
												#case "upload" 		:	$view = "view_rte"; break;
												#case "baca" 		:	$view = "upload_rte"; break;
												#case "doupload" 	:	$view = "upload_rte"; break;
												default				:	$view = "view_rte"; break;
											}
											break;
					case 'rtelc'		:	$menu = array("view"=>"view_rtelc");
											$view = getView($div,$menu,'view_rtelc');																
											break;
					case 'vip'			: $menu = array(
												'activation'=>'vip/activation'					
											);
											$view = getView($div,$menu,'home');
											break;				
					
				}
				//echo $view;
				include($view.".php");
			?>
			</div>
            </td>
		</tr>
	</table>
</div>
<?php
require_once('footer.php');
?>