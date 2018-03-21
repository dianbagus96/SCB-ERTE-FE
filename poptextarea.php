<?php require_once("configurl.php");
if($_GET['code']==md5(($type=$_GET['type']).($id=$_GET['id']).($toggle=$_GET['toggle']))){
?>
	<link type="text/css" href="<?php echo base_url?>css/style.css" rel="stylesheet" />	
	<script type="text/javascript" src="<?php echo base_url?>js/jquery.js"></script> 
	<script type="text/javascript">
		$(document).ready(function(){
			$('#keterangan').val($('#ajaxMod10Text<?php echo $id?>',window.opener.document).val())
			<?php
			if($type=='view'){
				echo "$('#tombol').hide();\n";
				echo "$('#keterangan').attr('readonly','readonly');\n";
			}else{
				echo "$('#tombol').show();\n";			
			}
			if($toggle=='off'){
				echo "window.close();\n";
			}
			?>
		})
		function save(){
			$('#ajaxMod10Text<?php echo $id?>',window.opener.document).val($('#keterangan').val());					
			window.close();
		}
	</script>
	<style type="text/css">
	body{
		background:#ECF4F9;
	}
	fieldset{
		font-family:Tahoma;
		font-weight:bold;
		font-size:11px;
		border:1px #FFCC00 solid;
		margin-top:5px;
	}
	textarea{
		width:100%;
		height:100px;
		padding:5px;
		font-family:Tahoma;
	}
	dt{
		border-bottom:2px #003366 solid;
		padding-bottom:3px;
	}
	dd{
		margin-top:5px;
		margin-left:0px;
	}
	</style>
	<fieldset>
		<dl>
			<dt>Keterangan RTE</dt>
			<dd><textarea id="keterangan"></textarea></dd>
		</dl>
		<dl id='tombol'>
			<a href="javascript:save()" class="htmlbutton" style="float:right">
				<span style="margin-top:-2px;">Save</span>
			</a>
		</dl>
	</fieldset>
<?php
}
?>