<?php
if($user!=''&&$passwd!=''&&$dbname!=''){
	$pdo = new PDO($dsn, $user, $passwd);
}else{
	$pdo = null;
}
function export_structure($table){
	global $pdo,$row_count;
	$stm = $pdo->query("DESCRIBE $table");
	$data = $stm->fetchAll();	
	$sql = "CREATE TABLE IF NOT EXISTS `$table` (";
	$fields = array();	
	if(sizeof($data)>0){
		$col = $data[0]['Field'];
		$stm = $pdo->query("SELECT COUNT($col) FROM $table");
		$col_data = $stm->fetch();	
		$row_count[$table]=$col_data[0];		
	}
	$primary_key = array();
	foreach($data as $d){

		$field="`$d[Field]` $d[Type] ";
		if($d['Null']=='NO'){
			$field.= " NOT NULL";	
		}else{
			$field.= " NULL";
		}
		if($d['Extra']=='auto_increment'){
			$field.= " AUTO_INCREMENT";	
		}
		if($d['Default']!=''){
			$field.= " Default '$d[Default]'";	
		}
		if($d['Key']=='PRI'){
			$primary_key[]=$d['Field'];
		}
		$fields[]=$field;
	}
	$sql.=implode(',',$fields);
	if(sizeof($primary_key)>0){
		$sql.= ', PRIMARY KEY('.implode(',',$primary_key).')';
	}
	$sql.=");\n";
	return $sql;
}
?>