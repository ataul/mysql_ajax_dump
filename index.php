<?php
$user = "root";
$passwd = "";
$dbname = "db";

$dsn = "mysql:host=localhost;dbname=".$dbname;
$pdo = new PDO($dsn, $user, $passwd);

$stm = $pdo->query("SHOW TABLES");
$data = $stm->fetchAll();

foreach($data as $d){
	$table = $d[0];
	$sql = export_structure($table);
	//echo $d[0].'<br />';
	echo $sql.'<br />';
}

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
?>