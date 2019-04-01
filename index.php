<?php
require_once('db_config.php');

$dsn = "mysql:host=localhost;dbname=".$dbname;
$pdo = new PDO($dsn, $user, $passwd);

$stm = $pdo->query("SHOW TABLES");
$data = $stm->fetchAll();

foreach($data as $d){
	$table = $d[0];
	$sql = export_structure($table);
	//echo $d[0].'<br />';
	//echo $sql.'<br />';
	
}
$sql = export_data($data[0][0],0,100);
echo $sql;

function export_structure($table){
	global $pdo;
	$stm = $pdo->query("DESCRIBE $table");
	$data = $stm->fetchAll();	
	$sql = "CREATE TABLE IF NOT EXISTS `$table` (";
	$fields = array();	
	foreach($data as $d){
		$field="`$d[Field]` $d[Type] ";
		if($d['Null']=='NO'){
			$field.= " NOT NULL";	
		}else{
			$field.= " NULL";
		}
		$fields[]=$field;
	}
	$sql.=implode(',',$fields).');';
	return $sql;
}
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
?>