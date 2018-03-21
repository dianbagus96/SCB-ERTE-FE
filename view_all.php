<?php
session_start();
require("configurl.php");
if($_SESSION['verified']==""){
	echo "<script> window.location.href='".base_url."err/3/".md5(3)."';</script>";exit;
}else{
	$field = $_GET['field'];
	$table = $_GET['table'];
	$where_field = $_GET['field_where'];
	$where_isi = $_GET['isi_where'];
	if($_GET['cek']=='1'){
		require_once("dbconndb.php");
		$connDB->connect();
		$field_arr = explode(",",$field);
		
		$where_field = explode(",",$where_field);
		$where_isi = explode(",",$where_isi);

		$sql = "select $field from $table where ";
		$x=0;
		foreach($where_field as $where){
			$sql .= $where."='".$where_isi[$x]."',";
		}
		$sql = substr($sql,0,strlen($sql)-1);		
		$data = $connDB->query($sql);
		while($data->next()){
			$x=0;
			foreach($field_arr as $f){
				echo $f." : ".$data->get($x++)."<br>";
			}
			echo "<hr>";
		}
		echo "jumlah = ".$data->size();
		
	}
}
?>