<?php
require_once('db_config.php');
function export_data($table,$start,$limit){
	global $pdo;
	$stm = $pdo->query("SELECT * FROM $table LIMIT $start,$limit");
	$data = $stm->fetchAll();
	$sql = "INSERT INTO $table VALUES(";	
	foreach($data as $d){
		$s = "";
		for($i=0;$i<sizeof($d)/2;$i++){
			$s .= "'".addslashes($d[$i])."',";
		}
		$sql .= substr($s,0,strlen($s)-1)."),(";		
	}
	$sql = substr($sql,0,strlen($sql)-2).";\n";
	if(sizeof($data)>0){
		return $sql;
	}else{
		return "";
	}
}
$table = $_REQUEST['table'];
if(isset($_REQUEST['start'])&&strlen($_REQUEST['start'])>0){
	$start = $_REQUEST['start'];
	$limit = $_REQUEST['limit'];
	$sql = export_data($table,$start,$limit);
}else{
	$sql = export_data($table,0,1000000);
}
$fpt = fopen('dump.sql','a');
fwrite($fpt,$sql);
fclose($fpt);
?>