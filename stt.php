<?php
require_once("dbconn.php");
	$conn->connect();
	$sql = "select * from TBLDMSANDISTT";
	
	$data = $conn->query($sql);
	$isi .= '<select name="popup_prompt" id="popup_prompt" >';
	while($data->next()){
		$isi .= "<option value='".$data->get(0)."'>". $data->get(0) . '-' . $data->get(1) ."</option>";
	}
	$conn->disconnect();
	$isi .= '</select>';
	echo  json_encode( array( "name"=>$isi));
?>