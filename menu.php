<?php
session_start();

if ($_SESSION['EMAILSTATUS']!="2"){
$kdbhs = $_SESSION['bahasa_sess'];
$bhs = $_SESSION['lang'];
//die($_SESSION['verified']);
//die($_SESSION['timeout_session']);
if($_SESSION['verified']==""){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}elseif(time()>$_SESSION['timeout_session']){	
	echo "<script> window.location.href='".base_url."err/7/".md5(7)."';</script>";exit;
}else{
	$_SESSION['timeout_session'] = TIMEOUT;
}
	require_once("dbconn.php");
	require_once("dbconndb.php");	
	$connRec = $conn;
	$conn->connect();	
	if($_SESSION['session_priv']!=0){	
		if(cekAktif($_SESSION['uid_session'],$_SESSION['ID'],$conn)==0) echo "<script> window.location.href='".base_url."log/out';</script>";
	}
	
	$user = "";
	if($_SESSION['priv_session']=='2'){ #checker
		$user = " and CHECKERS like '%,".trim($_SESSION['uid_session']).",%'";
	}elseif($_SESSION['priv_session']=='1'){#maker
		$user = " and MAKER = '".trim($_SESSION['uid_session'])."'";
	}elseif($_SESSION['priv_session']=='6'){#pic
		$user = " and MAKER in (".$_SESSION['member_session'].")";
	}
	

	if($_SESSION["priv_session"]=="0" || $_SESSION["priv_session"]=="3" || $_SESSION["priv_session"]=="1" || $_SESSION["priv_session"]=="2"){
?>
	<table cellpadding="0" cellspacing="0" width="174">
    
		<?php
		if(strpos($_SERVER['REQUEST_URI'],'home/view')){
		?>
		<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/home/view'" onmouseover="$(this).removeClass().addClass('tr_menus')" onmouseout="$(this).removeClass().addClass('tr_menu')">
			<td height="20" id="arr_personal_home"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_green.gif"/></td>
			<td style="padding-left:6px; color:#86C533;" id="mn_personal_home">Home &nbsp;</td>
		</tr>
		<?php
		}else{
		?>
		<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/home/view'" onmouseover="$(this).removeClass().addClass('tr_menus')" onmouseout="$(this).removeClass().addClass('tr_menu')">
			<td height="20" id="arr_personal_home"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
			<td style="padding-left:6px;" id="mn_personal_home">Home &nbsp;</td>
		</tr>
		<?php
		}
		if(strpos($_SERVER['REQUEST_URI'],'setting/kurs')){ 
			?>
			<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/setting/kurs'">
				<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_green.gif"/></td>
				<td style="padding-left:6px; color:#86C533;">Manage Kurs&nbsp;</td>
			</tr>
			<?php
			}else{
			?>
			<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/setting/kurs'">
				<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
				<td style="padding-left:6px;">Manage Kurs&nbsp;</td>
			</tr>
			<?php
			}
		?>
		
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
			<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">Corporate Setting</td>
		</tr>	
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>			
		<?php
		if(strpos($_SERVER['REQUEST_URI'],'user/add')){
			?>
			<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/user/add'" onmouseover="$(this).removeClass().addClass('tr_menus')" onmouseout="$(this).removeClass().addClass('tr_menu')">
				<td height="20" id="arr_create_new_user"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_green.gif"/></td>
				<td style="padding-left:6px; color:#86C533;" id="mn_create_new_user">Create New User &nbsp;</td>
			</tr>       	
		<?php
		}else{
			?>
			<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/user/add'" onmouseover="$(this).removeClass().addClass('tr_menus')" onmouseout="$(this).removeClass().addClass('tr_menu')">
				<td height="20" id="arr_create_new_user"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
				<td style="padding-left:6px;" id="mn_create_new_user">Create New User &nbsp;</td>
			</tr>
			<?php
		}
		if(strpos($_SERVER['REQUEST_URI'],'user/view')){
		?>
		<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/user/view'" onmouseover="$(this).removeClass().addClass('tr_menus')" onmouseout="$(this).removeClass().addClass('tr_menu')">
			<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_green.gif"/></td>
			<td style="padding-left:6px;color:#86C533;">Browse User &nbsp;</td>
		</tr>
		<?php
		}else{
		?>
		<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/user/view'" onmouseover="$(this).removeClass().addClass('tr_menus')" onmouseout="$(this).removeClass().addClass('tr_menu')">
			<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
			<td style="padding-left:6px;">Browse User &nbsp;</td>
		</tr>
		<?php
		}
		$url = split("/",$_SERVER['REQUEST_URI']);		
		
		if($_SESSION['priv_session']=='0' || $_SESSION['priv_session']=='1'){
		?>
       <!-- <tr class="tr_menu">
			<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
			<td style="padding-left:6px;">
			<form name="frmSelect" style="margin-bottom: 0px; margin-left:-4px;">
				<select name="pil" onchange="javascript:location.href=(this.value);" style="font-size:11px;width:150px">
				<option value="#">Setup Cabang</option>
				<?php				
				$x1 = array(base_url."modul/cabang/view",base_url."modul/cabang/add");
				$x2 = array("- List Cabang","- Add new Cabang");
				for($i=0;$i<count($x1);$i++){
					if(strpos($_SERVER['REQUEST_URI'],str_replace(base_url,'',$x1[$i]))){
						echo("<option value=\"". $x1[$i] ."\" selected>". $x2[$i] ."</option>");
					} else {
						echo("<option value=\"". $x1[$i] ."\">". $x2[$i] ."</option>");
					}
				}
				?>
				</select>	
			</form>		
			</td>
		</tr>-->
        <tr class="tr_menu">
			<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
			<td style="padding-left:6px;">
			<form name="frmSelect" style="margin-bottom: 0px; margin-left:-4px;">
				<select name="pil" onchange="javascript:location.href=(this.value);" style="font-size:11px;width:150px">
				<option value="#">Setup Rell ID</option>
				<?php				
				$x1 = array(base_url."modul/rekening/view");
				$x2 = array("- List Rell ID");
				for($i=0;$i<count($x1);$i++){
					if(strpos($_SERVER['REQUEST_URI'],str_replace(base_url,'',$x1[$i]))){
						echo("<option value=\"". $x1[$i] ."\" selected>". $x2[$i] ."</option>");
					} else {
						echo("<option value=\"". $x1[$i] ."\">". $x2[$i] ."</option>");
					}
				}
				?>
				</select>	
			</form>		
			</td>
		</tr>
        <tr class="tr_menu">
			<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
			<td style="padding-left:6px;">
			<form name="frmSelect" style="margin-bottom: 0px; margin-left:-4px;">
				<select name="pil" onchange="javascript:location.href=(this.value);" style="font-size:11px;width:150px">
				<option value="#">Setup NPWP</option>
				<?php				
				$x1 = array(base_url."modul/npwp/view");
				$x2 = array("- List NPWP");
				for($i=0;$i<count($x1);$i++){
					if(strpos($_SERVER['REQUEST_URI'],str_replace(base_url,'',$x1[$i]))){
						echo("<option value=\"". $x1[$i] ."\" selected>". $x2[$i] ."</option>");
					} else {
						echo("<option value=\"". $x1[$i] ."\">". $x2[$i] ."</option>");
					}
				}
				?>
				</select>	
			</form>		
			</td>
		</tr>
		 <tr class="tr_menu">
			<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
			<td style="padding-left:6px;">
			<form name="frmSelect" style="margin-bottom: 0px; margin-left:-4px;">
				<select name="pil" onchange="javascript:location.href=(this.value);" style="font-size:11px;width:150px">
				<option value="#">Setup Sandi</option>
				<?php				
				$x1 = array(base_url."modul/sandi/add",base_url."modul/sandi/view");
				$x2 = array("- Create New Sandi","- List Sandi");
				for($i=0;$i<count($x1);$i++){
					if(strpos($_SERVER['REQUEST_URI'],str_replace(base_url,'',$x1[$i]))){
						echo("<option value=\"". $x1[$i] ."\" selected>". $x2[$i] ."</option>");
					} else {
						echo("<option value=\"". $x1[$i] ."\">". $x2[$i] ."</option>");
					}
				}
				?>
				</select>	
			</form>		
			</td>
		</tr>
		<?php
		
		if(strpos($_SERVER['REQUEST_URI'],'selisih/view')){
		?>
		<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/selisih/view'" onmouseover="$(this).removeClass().addClass('tr_menus')" onmouseout="$(this).removeClass().addClass('tr_menu')">
			<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_green.gif"/></td>
			<td style="padding-left:6px;color:#86C533;">Manage Difference &nbsp;</td>
		</tr>
		<?php
		}else{
		?>
		<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/selisih/view'" onmouseover="$(this).removeClass().addClass('tr_menus')" onmouseout="$(this).removeClass().addClass('tr_menu')">
			<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
			<td style="padding-left:6px;">Manage Difference &nbsp;</td>
		</tr> 
		<?php
		}
		}
		if($_SESSION['priv_session']=='0'){?>
		<!--<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
			<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">Corporate Monitor</td>
		</tr>-->
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>			
			<?php
			if(strpos($_SERVER['REQUEST_URI'],'danamasuk/monitor')){
		?>
			<!--<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/danamasuk/monitor'" onmouseover="$(this).removeClass().addClass('tr_menus')" onmouseout="$(this).removeClass().addClass('tr_menu')">
				<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_green.gif"/></td>
				<td style="padding-left:6px;color:#86C533;">Dana Masuk&nbsp;</td>
			</tr>-->
			<?php
			}else{
			?>
			<!--<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/danamasuk/monitor'" onmouseover="$(this).removeClass().addClass('tr_menus')" onmouseout="$(this).removeClass().addClass('tr_menu')">
				<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
				<td style="padding-left:6px;">Dana Masuk &nbsp;</td>
			</tr>-->
			<?php
			}
		}
		if($_SESSION['priv_session']=='3'){
			if(strpos($_SERVER['REQUEST_URI'],'rekeningtax')){
			?>
			<!--<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/rekeningtax/view'" onmouseover="$(this).removeClass().addClass('tr_menus')" onmouseout="$(this).removeClass().addClass('tr_menu')">
				<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_green.gif"/></td>
				<td style="padding-left:6px;color:#86C533;">Account Setting&nbsp;</td>
			</tr>-->
			<?php
			}else{
			?>
		<!--	<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/rekeningtax/view'" onmouseover="$(this).removeClass().addClass('tr_menus')" onmouseout="$(this).removeClass().addClass('tr_menu')">
				<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
				<td style="padding-left:6px;">Account Setting&nbsp;</td>
			</tr>-->
			<?php
			}
			if(substr($_SESSION["AKSES"],3,1)=="1"){
				if(strpos($_SERVER['REQUEST_URI'],'modul/nearmatching')){
				?>
				<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/nearmatching/setting'" onmouseover="$(this).removeClass().addClass('tr_menus')" onmouseout="$(this).removeClass().addClass('tr_menu')">
					<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_green.gif"/></td>
					<td style="padding-left:6px;color:#86C533;">Manage Near Matching &nbsp;</td>
				</tr>
			<?php
				}else{?>
					<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/nearmatching/setting'" onmouseover="$(this).removeClass().addClass('tr_menus')" onmouseout="$(this).removeClass().addClass('tr_menu')">
						<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
						<td style="padding-left:6px;">Manage Near Matching &nbsp;</td>
					</tr>
				<?php	
				}
			}
		}
		
		?>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
			<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">Corporate Activity</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		</tr>
		<?php
		if(strpos($_SERVER['REQUEST_URI'],'audit/trail')){
		?>
		<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/audit/trail'" onmouseover="$(this).removeClass().addClass('tr_menus')" onmouseout="$(this).removeClass().addClass('tr_menu')">
			<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_green.gif"/></td>
			<td style="padding-left:6px; color:#86C533;">Audit Trail &nbsp;</td>
		</tr>
		<?php
		}else{
		?>
		<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/audit/trail'" onmouseover="$(this).removeClass().addClass('tr_menus')" onmouseout="$(this).removeClass().addClass('tr_menu')">
			<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
			<td style="padding-left:6px;">Audit Trail &nbsp;</td>
		</tr>
		<?php 
		}
		
		if(substr($_SESSION["AKSES"],1,1)=="1"||$_SESSION["priv_session"]=='0'){
			if(strpos($_SERVER['REQUEST_URI'],'audit/logtrail')){
			?>
			<!--<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/audit/logtrail'" onmouseover="$(this).removeClass().addClass('tr_menus')" onmouseout="$(this).removeClass().addClass('tr_menu')">-->
				<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_green.gif"/></td>
				<td style="padding-left:6px; color:#86C533;">Audit Log Trail &nbsp;</td>
			</tr>
			<?php
			}else{
			?>
			<!--<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/audit/logtrail'" onmouseover="$(this).removeClass().addClass('tr_menus')" onmouseout="$(this).removeClass().addClass('tr_menu')">-->
			<!--	<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>-->
			<!--	<td style="padding-left:6px;">Audit Log Trail &nbsp;</td>-->
			<!--</tr>-->
			<?php
			}
		}
		if(strpos($_SERVER['REQUEST_URI'],'modul/manage/profile')){
		?>
		<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/profile/edit'" onmouseover="$(this).removeClass().addClass('tr_menus')" onmouseout="$(this).removeClass().addClass('tr_menu')">
			<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_green.gif"/></td>
			<td style="padding-left:6px; color:#86C533;">Edit Password / Profile &nbsp;</td>
		</tr>
		<?php
		}else{
		?>
		<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/manage/profile'" onmouseover="$(this).removeClass().addClass('tr_menus')" onmouseout="$(this).removeClass().addClass('tr_menu')">
			<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
			<td style="padding-left:6px;">Edit Password / Profile &nbsp;</td>
		</tr>
		<?php
		}
		if($_SESSION["priv_session"]=="0"){
			if(strpos($_SERVER['REQUEST_URI'],'modul/setting/setting')){
			?>
			<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/setting/setting'" onmouseover="$(this).removeClass().addClass('tr_menus')" onmouseout="$(this).removeClass().addClass('tr_menu')">
				<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_green.gif"/></td>
				<td style="padding-left:6px; color:#86C533;">Management Password &nbsp;</td>
			</tr>
			<?php
			}else{
			?>
			<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/setting/setting'" onmouseover="$(this).removeClass().addClass('tr_menus')" onmouseout="$(this).removeClass().addClass('tr_menu')">
				<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
				<td style="padding-left:6px;">Management Password &nbsp;</td>
			</tr>
			<?php
			}
			if(strpos($_SERVER['REQUEST_URI'],'modul/news')){
			?>
			<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/news/setting'" onmouseover="$(this).removeClass().addClass('tr_menus')" onmouseout="$(this).removeClass().addClass('tr_menu')">
				<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_green.gif"/></td>
				<td style="padding-left:6px; color:#86C533;">Announcement&nbsp;</td>
			</tr>
			<?php
			}else{
			?>
			<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/news/setting'" onmouseover="$(this).removeClass().addClass('tr_menus')" onmouseout="$(this).removeClass().addClass('tr_menu')">
				<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
				<td style="padding-left:6px;">Announcement&nbsp;</td>
			</tr>
			<?php
			}
		}
		?>
		<tr>
			<td height="20" colspan="2" style="border-bottom: 1px solid #D7D7D7;">&nbsp;</td>
		</tr>
	</table>
<?php
	}
	elseif($_SESSION["priv_session"]=="5" ){
?>
		<table cellpadding="0" cellspacing="0" width="174">
			<?php
			if(strpos($_SERVER['REQUEST_URI'],'home/view')){
			?>
			<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/home/view'">
				<td height="20" id="arr_personal_home"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_green.gif"/></td>
				<td style="padding-left:6px; color:#86C533;" id="mn_personal_home">&nbsp;Home &nbsp;</td>
			</tr>
			<?php
			}else{
			?>
			<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/home/view'">
				<td height="20" id="arr_personal_home"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
				<td style="padding-left:6px;" id="mn_personal_home">&nbsp;Home &nbsp;</td>
			</tr>			
			<?php
			}
			if(strpos($_SERVER['REQUEST_URI'],'setting/kurs')){
			?>
			<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/setting/kurs'">
				<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_green.gif"/></td>
				<td style="padding-left:6px; color:#86C533;">&nbsp;Manage Kurs&nbsp;</td>
			</tr>
			<?php
			}else{
			?>
			<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/setting/kurs'">
				<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
				<td style="padding-left:6px;">&nbsp;Manage Kurs&nbsp;</td>
			</tr>
			<?php
			}
			?>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
				<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">Modul RTE</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<?php		
			if(substr($_SESSION["AKSES"],1,1)=="1"){
				$sql = "select COUNT(*) AS JUMLAH from TbltxSSP SSP LEFT JOIN TblJNSBYR BYR ON RTRIM(SSP.KDMAP) = BYR.KDMAP6 AND SSP.KDJNSBYR = BYR.KDJNSBYR 
				WHERE CUSREFF LIKE '".trim($_SESSION['ID'])."%' ".$user; 
				$connDB->connect();	$drec = $connDB->query($sql); $drec->next(); 
				$jum_reception = $drec->get('JUMLAH');
				if(in_array($priv = $_SESSION["priv_session"],array('1','2','4','6'))){#maker,checker,releaser,pic					
					if($priv=='1'){ #just for maker
						if(strpos($_SERVER['REQUEST_URI'],'ssp/add')){
						?>
						<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/ssp/add'">
							<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_green.gif"/></td>
							<td style="padding-left:6px; color:#86C533;">&nbsp;Create New SSP &nbsp;</td>
						</tr>
						<?php
						}else{
						?>
						<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/ssp/add'">
							<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
							<td style="padding-left:6px;">&nbsp;Create New SSP &nbsp;</td>
						</tr>
						<?php
						}		
					}		
					?>
					 <tr class="tr_menu">
						<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
						<td style="padding-left:6px;">
						<form name="frmSelect" style="margin-bottom: 0px; margin-left:-0px;">
							<select name="pil" onchange="javascript:location.href=(this.value);" style="font-size:11px;width:150px">
							<option value="#">SSP List</option>
							<?php									
							$x1 = array( base_url."modul/ssp/new",base_url."modul/ssp/valid",base_url."modul/ssp/sent",base_url."modul/ssp/terima");
							$x2 = array("- SSP New ($jml_new)","- SSP Validation ($jml_valid)","- SSP Sent ($jml_download)","- SSP Reception ($jum_reception)");
							for($i=0;$i<count($x1);$i++){
								if(strpos($_SERVER['REQUEST_URI'],str_replace(base_url,'',$x1[$i]))){
									echo("<option value=\"". $x1[$i] ."\" selected>". $x2[$i] ."</option>");
								} else {
									echo("<option value=\"". $x1[$i] ."\">". $x2[$i] ."</option>");
								}
							}
							?>
							</select>	
						</form>		
						</td>
				   </tr>		
				<?php
			   }	
			}
			if(substr($_SESSION["AKSES"],2,1)=="1" && $_SESSION['priv_session']==1 ){ #just for maker
				if(strpos($_SERVER['REQUEST_URI'],'ssp/upload')){
					?>
					<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/ssp/upload'">
						<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_green.gif"/></td>
						<td style="padding-left:6px; color:#86C533;">&nbsp;Upload SSP (Format)&nbsp;</td>
					</tr>
					<?php
				}else{
					?>
					<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/ssp/upload'">
						<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
						<td style="padding-left:6px;">&nbsp;Upload SSP (Format)&nbsp;</td>
					</tr>
					<?php
				}					
			}
			if(substr($_SESSION["AKSES"],1,1)=="1" || substr($_SESSION["AKSES"],2,1)=="1"){
			?>        
				<tr>
					<td height="20" colspan="2" style="border-bottom: 1px solid #D7D7D7;">&nbsp;</td>
				</tr>
				<tr>
					<td height="20" colspan="2">&nbsp;</td>
				</tr>
			<?php	
			}
			$connDB->connect();
			if(substr($_SESSION["AKSES"],0,1)=="1"){	
			
				///sspcp////////////////////////////////////////////////////////////////////////////////////////////////				
				$connDB->connect();
				$sql = "SELECT count(ID) as JML
						FROM tblSSPCP 						
						WHERE GROUPID = '".trim($_SESSION['ID'])."' ";
						
				$sql_new = $sql." and posisi='n' ".$user;				
				$datasspcp = $connDB->query($sql_new);$datasspcp->next();				
				$jum_sspcp_new = $datasspcp->get('JML');
				
				$sql_valid = $sql." and posisi='v' ".$user;				
				$datasspcp = $connDB->query($sql_valid);$datasspcp->next();				
				$jum_sspcp_valid = $datasspcp->get('JML');
				
				$sql_sent = $sql." and posisi='s' ".$user;				
				$datasspcp = $connDB->query($sql_sent);$datasspcp->next();				
				$jum_sspcp_sent = $datasspcp->get('JML');
				
				
				$connDB->disconnect();
				if($_SESSION['priv_session']=='1'){
					if(strpos($_SERVER['REQUEST_URI'],'sspcp/add')){
					?>
					<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/sspcp/add'">
						<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_green.gif"/></td>
						<td style="padding-left:6px; color:#86C533;">&nbsp;Create New SSPCP &nbsp;</td>
					</tr>
					<?php
					}else{
					?>
					<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/sspcp/add'">
						<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
						<td style="padding-left:6px;">&nbsp;Create New SSPCP &nbsp;</td>
					</tr>
				<?php
					}
				}
				?>	
				<tr class="tr_menu">
                    <td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
                    <td style="padding-left:6px;">
                    <form name="frmSelect" style="margin-bottom: 0px; margin-left:-0px;">
                        <select name="pil" onchange="javascript:location.href=(this.value);" style="font-size:11px;width:150px">
                        <option value="#">SSPCP List</option>
                        <?php									
                        $x1 = array( base_url."modul/sspcp/new",base_url."modul/sspcp/valid",base_url."modul/sspcp/sent");
                        $x2 = array("- SSPCP New ($jum_sspcp_new)","- SSPCP Validation ($jum_sspcp_valid)","- SSPCP Sent ($jum_sspcp_sent)");
                        for($i=0;$i<count($x1);$i++){
                            if(strpos($_SERVER['REQUEST_URI'],str_replace(base_url,'',$x1[$i]))){
                                echo("<option value=\"". $x1[$i] ."\" selected>". $x2[$i] ."</option>");
                            } else {
                                echo("<option value=\"". $x1[$i] ."\">". $x2[$i] ."</option>");
                            }
                        }
                        ?>
                        </select>	
                    </form>		
                    </td>
                </tr>                
				<?php		
						
				$connDB->connect();
				$sql = "SELECT count(a.CAR) as TOTAL FROM tblPibHdr a INNER JOIN tblSSPCP ON a.CAR = tblSSPCP.Car INNER JOIN TblStatus ON tblSSPCP.Status = TblStatus.KdRec WHERE (tblSSPCP.NPWPP = '".trim($_SESSION['npwp_session'])."' and kdTab='01')";	
				$datapib = $connDB->query($sql);
				$datapib->next();
				$jum_pib = $datapib->get('TOTAL');				
				$connDB->disconnect();
				//////////////////////////////////////////////////////////////////////////////////////////////
				  if(strpos($_SERVER['REQUEST_URI'],'modul/pib')){	
				  ?>
					<tr class="tr_menu" onclick="javascript:location.href='<?php echo base_url?>modul/pib/status'">
						<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
						<td style="padding-left:6px;color:#86C533;"> &nbsp;PIB Status (<?php echo $jum_pib?>)</td>
					</tr>
				  <?php
				  }else{
				  ?>
				  <tr class="tr_menu" onclick="javascript:location.href='<?php echo base_url?>modul/pib/status'">
					<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
					<td style="padding-left:6px;"> &nbsp;PIB Status (<?php echo $jum_pib?>)</td>
				  </tr>
				<?php
				  }
				?>
				<tr>
					<td height="20" colspan="2" style="border-bottom: 1px solid #D7D7D7;">&nbsp;</td>
				</tr>
				<tr>
					<td height="20" colspan="2">&nbsp;</td>
				</tr>
				<?php	
				   
		}
		  if(substr($_SESSION["AKSES"],3,1)=="1"){ #just user

				$connDB->connect();
				$sql = "select count(iddanamasuk) as ITEM, sum(nominal_transfer) as TOTAL from tbldmdanamasuk where kd_dana in('00') and flag_used=0 and NoRek in(".trim($_SESSION['noRek']).")";
				$data = $connDB->query($sql); $data->next(); $jml_neweks = $data->get('ITEM'); $total_neweks = $data->get('TOTAL');
				$sql = "select count(iddanamasuk) as ITEM, sum(nominal_transfer) as TOTAL from tbldmdanamasuk where kd_dana in('01') and flag_used=0 and NoRek in(".trim($_SESSION['noRek']).")";
				$data = $connDB->query($sql); $data->next(); $jml_ekspor = $data->get('ITEM'); $total_ekspor = $data->get('TOTAL');
				$sql = "select count(iddanamasuk) as ITEM, sum(nominal_transfer) as TOTAL from tbldmdanamasuk where kd_dana in('02') and flag_used=0 and NoRek in(".trim($_SESSION['noRek']).")";
				$data = $connDB->query($sql); $data->next(); $jml_nonekspor = $data->get('ITEM'); $total_nonekspor = $data->get('TOTAL');
				$sql = "select count(a.iddanamasuk) as ITEM, sum(a.nominal_transfer) as TOTAL from tbldmdanamasuk a left join tblfcdanapartial b on a.iddanamasuk=b.iddanamasuk where kd_dana in('03') and flag_used=0 and NoRek in(".trim($_SESSION['noRek']).")";
				$data = $connDB->query($sql); $data->next(); $jml_partial = $data->get('ITEM'); $total_partial = $data->get('TOTAL');
				$sql = "select count(a.iddanamasuk) as ITEM, sum(a.nominal_transfer) as TOTAL from tbldmdanamasuk a inner join tbldmdana b on a.kd_dana=b.kd_dana where a.flag_used in ('1','2','3') and a.NoRek in(".trim($_SESSION['noRek']).")";
				$data = $connDB->query($sql); $data->next(); $jml_terpakai = $data->get('ITEM'); $total_terpakai = $data->get('TOTAL');
				$sql = "select count(iddanamasuk) as ITEM, sum(nominal_transfer) as TOTAL from tbldmdanamasuk where kd_dana in('04') and flag_used=0 and NoRek in(".trim($_SESSION['noRek']).")";
				$data = $connDB->query($sql); $data->next(); $jml_uangmuka = $data->get('ITEM'); $total_uangmuka = $data->get('TOTAL');
			
				 if(strpos($_SERVER['REQUEST_URI'],'peb/create')){	
				  ?>
					<tr class="tr_menu" onclick="javascript:location.href='<?php echo base_url?>modul/peb/create'">
						<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
						<td style="padding-left:6px;color:#86C533;"> &nbsp;Input PEB&nbsp;</td>
					</tr>
				  <?php
				  }else{
				  ?>
				  <tr class="tr_menu" onclick="javascript:location.href='<?php echo base_url?>modul/peb/create'">
					<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
					<td style="padding-left:6px;"> &nbsp;Input PEB&nbsp;</td>
				  </tr>
				<?php
				  }						
				 if(strpos($_SERVER['REQUEST_URI'],'peb/upload')){	
				  ?>
					<tr class="tr_menu" onclick="javascript:location.href='<?php echo base_url?>modul/peb/upload'">
						<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
						<td style="padding-left:6px;color:#86C533;"> &nbsp;Upload PEB&nbsp;</td>
					</tr>
				  <?php
				  }else{
				  ?>
				  <tr class="tr_menu" onclick="javascript:location.href='<?php echo base_url?>modul/peb/upload'">
					<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
					<td style="padding-left:6px;"> &nbsp;Upload PEB&nbsp;</td>
				  </tr>
				<?php
				  }						
				?>  			                       
				<tr class="tr_menu">
					<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
					<td style="padding-left:6px;">
					<form name="frmSelect"style="margin-bottom: 0px; margin-left:-0px;">
					  <select name="pil" style="font-size:11px;width:150px" onchange="javascript:location.href=(this.value);">
					  <option value="#"><?php echo $bhs['Dana Masuk'][$kdbhs]?></option>
					  <?php				  
					  $x1 = array(	base_url."modul/danamasuk/baru",base_url."modul/danamasuk/ekspor",base_url."modul/danamasuk/nonekspor",base_url."modul/danamasuk/uangmuka",base_url."modul/danamasuk/terlaporkan");
									
					  $x2 = array(	"- ".$bhs['Dana Masuk Baru'][$kdbhs]." ($jml_neweks)","- ".$bhs['Ekspor'][$kdbhs]." ($jml_ekspor)","- ".$bhs['Non Ekspor'][$kdbhs]." ($jml_nonekspor)",
									"- ".$bhs['Uang Muka'][$kdbhs]." ($jml_uangmuka)","- ".$bhs['Dana Terlaporkan'][$kdbhs]." ($jml_terpakai)");
					  for($i=0;$i<count($x1);$i++){
						  if(strpos($_SERVER['REQUEST_URI'],str_replace(base_url,'',$x1[$i]))){
							  echo("<option value=\"". $x1[$i] ."\" selected>". $x2[$i] ."</option>");
						  } else {
							  echo("<option value=\"". $x1[$i] ."\">". $x2[$i] ."</option>");
						  }
					  }
					  ?>
					  </select>	
					</form>								
					</td>
				</tr>
					  
				<tr class="tr_menu">
					<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
					<td style="padding-left:6px;">
					<form name="frmSelect"style="margin-bottom: 0px; margin-left:-0px;">
					  <select name="pil" style="font-size:11px;width:150px" onchange="javascript:location.href=(this.value)">
					  <option value="#">PEB</option>
					  <?php
					  $sql = "select count(idPEB) as ITEM, sum(FOB) as TOTAL from tbldmpeb where flag_used ='0' and upper(GROUPID) ='".strtoupper(trim($_SESSION['grpID']))."'";
					  $data = $connDB->query($sql); $data->next(); $jml_newpeb = $data->get('ITEM'); $total_newpeb = $data->get('TOTAL');
					  $sql = "select count(idPEB) as ITEM, sum(FOB) as TOTAL from tbldmpeb where flag_used in ('1','2') and upper(GROUPID) ='".strtoupper(trim($_SESSION['grpID']))."'";
					  $data = $connDB->query($sql); $data->next(); $jml_pakai = $data->get('ITEM'); $total_pakai = $data->get('TOTAL');
					  $sql = "select count(idPEB) as ITEM, sum(FOB) as TOTAL from tbldmpeb where flag_used in ('3') and upper(GROUPID) ='".strtoupper(trim($_SESSION['grpID']))."'";
					  $data = $connDB->query($sql); $data->next(); $jml_plus = $data->get('ITEM'); $total_plus = $data->get('TOTAL');
					  
					  $x1 = array(base_url."modul/peb/baru",base_url."modul/peb/terlaporkan",base_url."modul/peb/plus");
					  $x2 = array("- ".$bhs['PEB Baru'][$kdbhs]." ($jml_newpeb)","- ".$bhs['PEB Terlaporkan'][$kdbhs]." ($jml_pakai)","- PEB 90+ ($jml_plus)");
					  for($i=0;$i<count($x1);$i++){
						  if(strpos($_SERVER['REQUEST_URI'],str_replace(base_url,'',$x1[$i]))){
							  echo("<option value=\"". $x1[$i] ."\" selected>". $x2[$i] ."</option>");
						  } else {
							  echo("<option value=\"". $x1[$i] ."\">". $x2[$i] ."</option>");
						  }
					  }
					  ?>
					  </select>	
					</form>	                
					</td>
				</tr>
				<tr class="tr_menu">
					<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
					<td style="padding-left:6px;">
					<form name="frmSelect"style="margin-bottom: 0px; margin-left:-0px;">
					  <select name="pil" style="font-size:11px;width:150px" onchange="javascript:location.href=(this.value)">
					  <option value="#">RTE</option>
					  <?php
					  $sql = "select count(r.idRTE) as ITEM, sum(nominal) as TOTAL from tblfcRTE r left join tbldmdanamasuk d on r.iddanamasuk=d.iddanamasuk left join tbldmPEB p on r.idPEB=p.idPEB 
							where r.status in ('0','6') and ( upper(p.GROUPID) ='".strtoupper(trim($_SESSION['grpID']))."' or d.noRek in(".trim($_SESSION['noRek'])."))";
					  $data = $connDB->query($sql); $data->next(); $jml_newrte = $data->get('ITEM'); $total_newrte = $data->get('TOTAL');
					  $sql = "select count(r.idRTE) as ITEM, sum(nominal) as TOTAL from tblfcRTE r left join tbldmdanamasuk d on r.iddanamasuk=d.iddanamasuk left join tbldmPEB p on r.idPEB=p.idPEB 
							where r.status in ('1','4','5') and (upper(p.GROUPID) ='".strtoupper(trim($_SESSION['grpID']))."'  or d.noRek in(".trim($_SESSION['noRek'])."))";
					  $data = $connDB->query($sql); $data->next(); $jml_sent = $data->get('ITEM'); $total_sent = $data->get('TOTAL'); 
					  $sql = "select count(r.idRTE) as ITEM, sum(nominal) as TOTAL from tblfcRTE r left join tbldmdanamasuk d on r.iddanamasuk=d.iddanamasuk left join tbldmPEB p on r.idPEB=p.idPEB 
							where r.status in ('1','5') and (upper(p.GROUPID) ='".strtoupper(trim($_SESSION['grpID']))."'  or d.noRek in(".trim($_SESSION['noRek']).")) and r.nominal=0 and d.flag_used!='2' and sandi_keterangan <> '0300'";
					  $data = $connDB->query($sql); $data->next(); $jml_rteuangmuka = $data->get('ITEM'); $total_rteuangmuka = $data->get('TOTAL');
					  $sql = "select count(r.idRTE) as ITEM, sum(nominal) as TOTAL from tblfcRTE r left join tbldmdanamasuk d on r.iddanamasuk=d.iddanamasuk left join tbldmPEB p on r.idPEB=p.idPEB 
							where r.status='3' and (p.NPWP in (".$_SESSION['npwp_many_session'].") or d.noRek in(".trim($_SESSION['noRek'])."))";
					  $data = $connDB->query($sql); $data->next(); $jml_pending = $data->get('ITEM'); $total_pending = $data->get('TOTAL');
					 /*  $x1 = array(base_url."modul/rte/baru",base_url."modul/rte/terkirim",base_url."modul/rte/rteuangmuka");
					  $x2 = array("- ".$bhs['RTE Baru'][$kdbhs]." ($jml_newrte)","- ".$bhs['RTE Terkirim'][$kdbhs]." ($jml_sent)"," - ".$bhs['RTE Uang Muka'][$kdbhs]." ($jml_rteuangmuka)");
					   */
					    $x1 = array(base_url."modul/rte/baru",base_url."modul/rte/terkirim",base_url."modul/rte/pending",base_url."modul/rte/rteuangmuka");
					  $x2 = array("- ".$bhs['RTE Baru'][$kdbhs]." ($jml_newrte)","- ".$bhs['RTE Terkirim'][$kdbhs]." ($jml_sent)","- RTE Pending ($jml_pending)"," - ".$bhs['RTE Uang Muka'][$kdbhs]." ($jml_rteuangmuka)");
					  
					   for($i=0;$i<count($x1);$i++){
						  if(strpos($_SERVER['REQUEST_URI'],str_replace(base_url,'',$x1[$i]))){
							  echo("<option value=\"". $x1[$i] ."\" selected>". $x2[$i] ."</option>");
						  } else {
							  echo("<option value=\"". $x1[$i] ."\">". $x2[$i] ."</option>");
						  }
					  }
					  ?>
					  </select>	
					</form>	    
					</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
					<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">Corporate Activity</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<?php			
				}			
			if(strpos($_SERVER['REQUEST_URI'],'manage/profile')){
			?>
			<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/manage/profile'">
				<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_green.gif"/></td>
				<td style="padding-left:6px; color:#86C533;">&nbsp;Edit Password / Profile&nbsp;</td>
			</tr>
			<?php
			}else{
			?>
			<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/manage/profile'">
				<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
				<td style="padding-left:6px;">&nbsp;Edit Password / Profile&nbsp;</td>
			</tr>
			<?php
			}
			
			if(substr($_SESSION['AKSES'],0,1)==1||substr($_SESSION['AKSES'],1,1)==1||substr($_SESSION['AKSES'],2,1)==1){
				if(strpos($_SERVER['REQUEST_URI'],'manage/taxpayer')){
				?>
				<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/manage/taxpayer'">
					<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_green.gif"/></td>
					<td style="padding-left:6px; color:#86C533;">&nbsp;Taxpayer&nbsp;</td>
				</tr>
				<?php
				}else{
				?>
				<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/manage/taxpayer'">
					<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
					<td style="padding-left:6px;">&nbsp;Taxpayer&nbsp;</td>
				</tr>
				<?php
				}
			}
		
			?>				
			<tr class="">
				<td height="20" colspan="2" style="border-bottom: 1px solid #D7D7D7;">&nbsp;</td>
			</tr>        
		</table>	
<?php
	}	elseif($_SESSION["priv_session"]=="4" ){
//	echo $_SESSION["priv_session"];	
	
	?>
		<table cellpadding="0" cellspacing="0" width="174">
			<?php
			if(strpos($_SERVER['REQUEST_URI'],'home/view')){
			?>
			<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/home/view'">
				<td height="20" id="arr_personal_home"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_green.gif"/></td>
				<td style="padding-left:6px; color:#86C533;" id="mn_personal_home">&nbsp;Home &nbsp;</td>
			</tr>
			<?php
			}else{
			?>
			<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/home/view'">
				<td height="20" id="arr_personal_home"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
				<td style="padding-left:6px;" id="mn_personal_home">&nbsp;Home &nbsp;</td>
			</tr>			
			<?php
			}
			?>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
				<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">Modul RTE</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<?php		
			if(substr($_SESSION["AKSES"],1,1)=="1"){
				$sql = "select COUNT(*) AS JUMLAH from TbltxSSP SSP LEFT JOIN TblJNSBYR BYR ON RTRIM(SSP.KDMAP) = BYR.KDMAP6 AND SSP.KDJNSBYR = BYR.KDJNSBYR 
				WHERE CUSREFF LIKE '".trim($_SESSION['ID'])."%' ".$user; 
				$connDB->connect();	$drec = $connDB->query($sql); $drec->next(); 
				$jum_reception = $drec->get('JUMLAH');
				if(in_array($priv = $_SESSION["priv_session"],array('1','2','4','6'))){#maker,checker,releaser,pic					
					if($priv=='1'){ #just for maker
						if(strpos($_SERVER['REQUEST_URI'],'ssp/add')){
						?>
						<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/ssp/add'">
							<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_green.gif"/></td>
							<td style="padding-left:6px; color:#86C533;">&nbsp;Create New SSP &nbsp;</td>
						</tr>
						<?php
						}else{
						?>
						<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/ssp/add'">
							<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
							<td style="padding-left:6px;">&nbsp;Create New SSP &nbsp;</td>
						</tr>
						<?php
						}		
					}		
					?>
					 <tr class="tr_menu">
						<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
						<td style="padding-left:6px;">
						<form name="frmSelect" style="margin-bottom: 0px; margin-left:-0px;">
							<select name="pil" onchange="javascript:location.href=(this.value);" style="font-size:11px;width:150px">
							<option value="#">SSP List</option>
							<?php									
							$x1 = array( base_url."modul/ssp/new",base_url."modul/ssp/valid",base_url."modul/ssp/sent",base_url."modul/ssp/terima");
							$x2 = array("- SSP New ($jml_new)","- SSP Validation ($jml_valid)","- SSP Sent ($jml_download)","- SSP Reception ($jum_reception)");
							for($i=0;$i<count($x1);$i++){
								if(strpos($_SERVER['REQUEST_URI'],str_replace(base_url,'',$x1[$i]))){
									echo("<option value=\"". $x1[$i] ."\" selected>". $x2[$i] ."</option>");
								} else {
									echo("<option value=\"". $x1[$i] ."\">". $x2[$i] ."</option>");
								}
							}
							?>
							</select>	
						</form>		
						</td>
				   </tr>		
				<?php
			   }	
			}
			if(substr($_SESSION["AKSES"],2,1)=="1" && $_SESSION['priv_session']==1 ){ #just for maker
				if(strpos($_SERVER['REQUEST_URI'],'ssp/upload')){
					?>
					<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/ssp/upload'">
						<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_green.gif"/></td>
						<td style="padding-left:6px; color:#86C533;">&nbsp;Upload SSP (Format)&nbsp;</td>
					</tr>
					<?php
				}else{
					?>
					<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/ssp/upload'">
						<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
						<td style="padding-left:6px;">&nbsp;Upload SSP (Format)&nbsp;</td>
					</tr>
					<?php
				}					
			}
			if(substr($_SESSION["AKSES"],1,1)=="1" || substr($_SESSION["AKSES"],2,1)=="1"){
			?>        
				<tr>
					<td height="20" colspan="2" style="border-bottom: 1px solid #D7D7D7;">&nbsp;</td>
				</tr>
				<tr>
					<td height="20" colspan="2">&nbsp;</td>
				</tr>
			<?php	
			}
			$connDB->connect();
			if(substr($_SESSION["AKSES"],0,1)=="1"){	
			
				///sspcp////////////////////////////////////////////////////////////////////////////////////////////////				
				$connDB->connect();
				$sql = "SELECT count(ID) as JML
						FROM tblSSPCP 						
						WHERE GROUPID = '".trim($_SESSION['ID'])."' ";
						
				$sql_new = $sql." and posisi='n' ".$user;				
				$datasspcp = $connDB->query($sql_new);$datasspcp->next();				
				$jum_sspcp_new = $datasspcp->get('JML');
				
				$sql_valid = $sql." and posisi='v' ".$user;				
				$datasspcp = $connDB->query($sql_valid);$datasspcp->next();				
				$jum_sspcp_valid = $datasspcp->get('JML');
				
				$sql_sent = $sql." and posisi='s' ".$user;				
				$datasspcp = $connDB->query($sql_sent);$datasspcp->next();				
				$jum_sspcp_sent = $datasspcp->get('JML');
				
				
				$connDB->disconnect();
				if($_SESSION['priv_session']=='1'){
					if(strpos($_SERVER['REQUEST_URI'],'sspcp/add')){
					?>
					<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/sspcp/add'">
						<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_green.gif"/></td>
						<td style="padding-left:6px; color:#86C533;">&nbsp;Create New SSPCP &nbsp;</td>
					</tr>
					<?php
					}else{
					?>
					<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/sspcp/add'">
						<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
						<td style="padding-left:6px;">&nbsp;Create New SSPCP &nbsp;</td>
					</tr>
				<?php
					}
				}
				?>	
				<tr class="tr_menu">
                    <td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
                    <td style="padding-left:6px;">
                    <form name="frmSelect" style="margin-bottom: 0px; margin-left:-0px;">
                        <select name="pil" onchange="javascript:location.href=(this.value);" style="font-size:11px;width:150px">
                        <option value="#">SSPCP List</option>
                        <?php									
                        $x1 = array( base_url."modul/sspcp/new",base_url."modul/sspcp/valid",base_url."modul/sspcp/sent");
                        $x2 = array("- SSPCP New ($jum_sspcp_new)","- SSPCP Validation ($jum_sspcp_valid)","- SSPCP Sent ($jum_sspcp_sent)");
                        for($i=0;$i<count($x1);$i++){
                            if(strpos($_SERVER['REQUEST_URI'],str_replace(base_url,'',$x1[$i]))){
                                echo("<option value=\"". $x1[$i] ."\" selected>". $x2[$i] ."</option>");
                            } else {
                                echo("<option value=\"". $x1[$i] ."\">". $x2[$i] ."</option>");
                            }
                        }
                        ?>
                        </select>	
                    </form>		
                    </td>
                </tr>                
				<?php		
						
				$connDB->connect();
				$sql = "SELECT count(a.CAR) as TOTAL FROM tblPibHdr a INNER JOIN tblSSPCP ON a.CAR = tblSSPCP.Car INNER JOIN TblStatus ON tblSSPCP.Status = TblStatus.KdRec WHERE (tblSSPCP.NPWPP = '".trim($_SESSION['npwp_session'])."' and kdTab='01')";	
				$datapib = $connDB->query($sql);
				$datapib->next();
				$jum_pib = $datapib->get('TOTAL');				
				$connDB->disconnect();
				//////////////////////////////////////////////////////////////////////////////////////////////
				  if(strpos($_SERVER['REQUEST_URI'],'modul/pib')){	
				  ?>
					<tr class="tr_menu" onclick="javascript:location.href='<?php echo base_url?>modul/pib/status'">
						<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
						<td style="padding-left:6px;color:#86C533;"> &nbsp;PIB Status (<?php echo $jum_pib?>)</td>
					</tr>
				  <?php
				  }else{
				  ?>
				  <tr class="tr_menu" onclick="javascript:location.href='<?php echo base_url?>modul/pib/status'">
					<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
					<td style="padding-left:6px;"> &nbsp;PIB Status (<?php echo $jum_pib?>)</td>
				  </tr>
				<?php
				  }
				?>
				<tr>
					<td height="20" colspan="2" style="border-bottom: 1px solid #D7D7D7;">&nbsp;</td>
				</tr>
				<tr>
					<td height="20" colspan="2">&nbsp;</td>
				</tr>
				<?php	
				   
		}
		  if(substr($_SESSION["AKSES"],3,1)=="1"){ #just user

				$connDB->connect();
				$sql = "select count(iddanamasuk) as ITEM, sum(nominal_transfer) as TOTAL from tbldmdanamasuk where kd_dana in('00') and flag_used=0 and NoRek in(".trim($_SESSION['noRek']).")";
				$data = $connDB->query($sql); $data->next(); $jml_neweks = $data->get('ITEM'); $total_neweks = $data->get('TOTAL');
				$sql = "select count(iddanamasuk) as ITEM, sum(nominal_transfer) as TOTAL from tbldmdanamasuk where kd_dana in('01') and flag_used=0 and NoRek in(".trim($_SESSION['noRek']).")";
				$data = $connDB->query($sql); $data->next(); $jml_ekspor = $data->get('ITEM'); $total_ekspor = $data->get('TOTAL');
				$sql = "select count(iddanamasuk) as ITEM, sum(nominal_transfer) as TOTAL from tbldmdanamasuk where kd_dana in('02') and flag_used=0 and NoRek in(".trim($_SESSION['noRek']).")";
				$data = $connDB->query($sql); $data->next(); $jml_nonekspor = $data->get('ITEM'); $total_nonekspor = $data->get('TOTAL');
				$sql = "select count(a.iddanamasuk) as ITEM, sum(a.nominal_transfer) as TOTAL from tbldmdanamasuk a left join tblfcdanapartial b on a.iddanamasuk=b.iddanamasuk where kd_dana in('03') and flag_used=0 and NoRek in(".trim($_SESSION['noRek']).")";
				$data = $connDB->query($sql); $data->next(); $jml_partial = $data->get('ITEM'); $total_partial = $data->get('TOTAL');
				$sql = "select count(a.iddanamasuk) as ITEM, sum(a.nominal_transfer) as TOTAL from tbldmdanamasuk a inner join tbldmdana b on a.kd_dana=b.kd_dana where a.flag_used in ('1','2','3') and a.NoRek in(".trim($_SESSION['noRek']).")";
				$data = $connDB->query($sql); $data->next(); $jml_terpakai = $data->get('ITEM'); $total_terpakai = $data->get('TOTAL');
				$sql = "select count(iddanamasuk) as ITEM, sum(nominal_transfer) as TOTAL from tbldmdanamasuk where kd_dana in('04') and flag_used=0 and NoRek in(".trim($_SESSION['noRek']).")";
				$data = $connDB->query($sql); $data->next(); $jml_uangmuka = $data->get('ITEM'); $total_uangmuka = $data->get('TOTAL');
			
				 if(strpos($_SERVER['REQUEST_URI'],'peb/create')){	
				  ?>
					<tr class="tr_menu" onclick="javascript:location.href='<?php echo base_url?>modul/peb/create'">
						<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
						<td style="padding-left:6px;color:#86C533;"> &nbsp;Input PEB&nbsp;</td>
					</tr>
				  <?php
				  }else{
				  ?>
				  <tr class="tr_menu" onclick="javascript:location.href='<?php echo base_url?>modul/peb/create'">
					<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
					<td style="padding-left:6px;"> &nbsp;Input PEB&nbsp;</td>
				  </tr>
				<?php
				  }						
				 if(strpos($_SERVER['REQUEST_URI'],'peb/upload')){	
				  ?>
					<tr class="tr_menu" onclick="javascript:location.href='<?php echo base_url?>modul/peb/upload'">
						<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
						<td style="padding-left:6px;color:#86C533;"> &nbsp;Upload PEB&nbsp;</td>
					</tr>
				  <?php
				  }else{
				  ?>
				  <tr class="tr_menu" onclick="javascript:location.href='<?php echo base_url?>modul/peb/upload'">
					<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
					<td style="padding-left:6px;"> &nbsp;Upload PEB&nbsp;</td>
				  </tr>
				<?php
				  }						
				?>  			                       
								  
				<tr class="tr_menu">
					<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
					<td style="padding-left:6px;">
					<form name="frmSelect"style="margin-bottom: 0px; margin-left:-0px;">
					  <select name="pil" style="font-size:11px;width:150px" onchange="javascript:location.href=(this.value)">
					  <option value="#">PEB</option>
					  <?php
					  $sql = "select count(idPEB) as ITEM, sum(FOB) as TOTAL from tbldmpeb where flag_used ='0' and upper(GROUPID) ='".strtoupper(trim($_SESSION['grpID']))."'";
					  $data = $connDB->query($sql); $data->next(); $jml_newpeb = $data->get('ITEM'); $total_newpeb = $data->get('TOTAL');
					  $sql = "select count(idPEB) as ITEM, sum(FOB) as TOTAL from tbldmpeb where flag_used in ('1','2') and upper(GROUPID) ='".strtoupper(trim($_SESSION['grpID']))."'";
					  $data = $connDB->query($sql); $data->next(); $jml_pakai = $data->get('ITEM'); $total_pakai = $data->get('TOTAL');
					  $sql = "select count(idPEB) as ITEM, sum(FOB) as TOTAL from tbldmpeb where flag_used in ('3') and upper(GROUPID) ='".strtoupper(trim($_SESSION['grpID']))."'";
					  $data = $connDB->query($sql); $data->next(); $jml_plus = $data->get('ITEM'); $total_plus = $data->get('TOTAL');
					  
					  $x1 = array(base_url."modul/peb/baru",base_url."modul/peb/terlaporkan",base_url."modul/peb/plus");
					  $x2 = array("- ".$bhs['PEB Baru'][$kdbhs]." ($jml_newpeb)","- ".$bhs['PEB Terlaporkan'][$kdbhs]." ($jml_pakai)","- PEB 90+ ($jml_plus)");
					  for($i=0;$i<count($x1);$i++){
						  if(strpos($_SERVER['REQUEST_URI'],str_replace(base_url,'',$x1[$i]))){
							  echo("<option value=\"". $x1[$i] ."\" selected>". $x2[$i] ."</option>");
						  } else {
							  echo("<option value=\"". $x1[$i] ."\">". $x2[$i] ."</option>");
						  }
					  }
					  ?>
					  </select>	
					</form>	                
					</td>
				</tr>
				
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td style="background:url(<?php echo base_url?>img/tab1.png); width:20px; height:22px;">&nbsp;</td>
					<td style="background:url(<?php echo base_url?>img/tab2.png); width:100%; height:22px; padding-left:7px; color:#ffffff; font-size:11px; font-family:arial; font-weight:bold;">Corporate Activity</td>
				</tr>
				<tr>
					<td colspan="2">&nbsp;</td>
				</tr>
				<?php			
				}			
			
			if(strpos($_SERVER['REQUEST_URI'],'manage/profile')){
			?>
			<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/manage/profile'">
				<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_green.gif"/></td>
				<td style="padding-left:6px; color:#86C533;">&nbsp;Edit Password / Profile&nbsp;</td>
			</tr>
			<?php
			}else{
			?>
			<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/manage/profile'">
				<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
				<td style="padding-left:6px;">&nbsp;Edit Password / Profile&nbsp;</td>
			</tr>
			<?php
			}
			
			if(substr($_SESSION['AKSES'],0,1)==1||substr($_SESSION['AKSES'],1,1)==1||substr($_SESSION['AKSES'],2,1)==1){
				if(strpos($_SERVER['REQUEST_URI'],'manage/taxpayer')){
				?>
				<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/manage/taxpayer'">
					<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_green.gif"/></td>
					<td style="padding-left:6px; color:#86C533;">&nbsp;Taxpayer&nbsp;</td>
				</tr>
				<?php
				}else{
				?>
				<tr class="tr_menu" onclick="document.location = '<?php echo base_url?>modul/manage/taxpayer'">
					<td height="20"><img style="margin-left:5px;" src="<?php echo base_url?>img/arrow_blue.gif"/></td>
					<td style="padding-left:6px;">&nbsp;Taxpayer&nbsp;</td>
				</tr>
				<?php
				}
			}
		
			?>				
			<tr class="">
				<td height="20" colspan="2" style="border-bottom: 1px solid #D7D7D7;">&nbsp;</td>
			</tr>        
		</table>	
<?php
	}
}
?>

