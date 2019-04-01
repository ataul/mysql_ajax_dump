<?php
require_once('db_config.php');
function export_data($table,$start,$limit){
	global $pdo;
	$stm = $pdo->query("SELECT * FROM $table LIMIT $start,$limit");
	$data = $stm->fetchAll();
	$sql = "INSERT INTO $table VALUES(";
	echo '<pre>';
	foreach($data as $d){
		$s = "";
		for($i=0;$i<sizeof($d)/2;$i++){
			$s .= "'".$d[$i]."',";
		}
		$sql .= substr($s,0,strlen($s)-1)."),(";		
	}
	$sql = substr($sql,0,strlen($sql)-2).";";
	return $sql;
}
$table = $_REQUEST['table'];
$sql = export_data($table,0,1000000);
$fpt = fopen('dump.sql','a');
fwrite($fpt,$sql);
fclose($fpt);
?>