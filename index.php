<?php
require_once('db_config.php');
$row_count = array();
$stm = $pdo->query("SHOW TABLES");
$data = $stm->fetchAll();
$tables = array();
$sql = '';
/*
foreach($data as $d){
 	$tables[]=$d[0];
 	$sql .= export_structure($d[0]);
}
*/
$i=0;
foreach($data as $d){
	if($i++>10){
		$tables[]=$d[0];
		$sql .= export_structure($d[0]);
	}
}

$fpt = fopen('dump.sql','a');
fwrite($fpt,$sql);
fclose($fpt);
?>
<!DOCTYPE html>
<html>
<head>
	<title>MySQL Ajax Dump</title>
	<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
</head>
<body>
	<script type="text/javascript">
	batches = <?php echo json_encode($tables);?>;
	row_count = <?php echo json_encode($row_count);?>;
	current_index = 0;
	current_row = 0;
	batch2 = 0;
	big_dump = false;
	
	function processDump(batch){
		document.getElementById('btn').value = 'Calculating...';	
		document.getElementById('btn').setAttribute("disabled", "disabled");		
		
		if(typeof notRunning == 'undefined'){
			notRunning = setInterval(function() {processDump(batch)}, 3000);
		}
		if(batch2!=batches[current_index]||big_dump==true){
			batch2 = batches[current_index];
			row_count2 = row_count[batch2];
			if(row_count2>10000){
				var batch3 = batch2;
				big_dump = true;
				jQuery.ajax({
					type: "POST",
					url: "ajax.php?table="+batch3+"&start="+current_row,
					data: {},
					success: function(response){
						if(current_index==batches.length-1){
							clearInterval(notRunning);
							fetchReport();	
						}
						if(row_count2>current_row){
							current_row+=1000;
						}else{
							current_index++;
							$('#img_'+batch2).attr("src", "images/done.png");						
						}									
					} 
				});				
			}else{
				jQuery.ajax({
					type: "POST",
					url: "ajax.php?table="+batch2,
					data: {},
					success: function(response){					
						if(current_index==batches.length-1){
							clearInterval(notRunning);
							fetchReport();	
						}
						$('#img_'+batch2).attr("src", "images/done.png");						
						current_index++;		
					} 
				});
			}
			$('#img_'+batch2).attr("src", "images/loading.gif");			
		}
		return false;
	}
	function fetchReport() {
		
	}
	function checkReport() {
		
		return false;
	}
</script>
<?php

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
<button id="btn" onclick="processDump();">Export</button>
<div id="all_batches">
	<?php foreach($tables as $table):?>
		<img src="images/pending.png" id="img_<?php echo $table;?>" alt="Pending" />&nbsp;&nbsp;<?php echo $table;?><br />
	<?php endforeach;?>
</div>
</body>
</html>