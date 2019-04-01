<?php
require_once('db_config.php');
$dsn = "mysql:host=localhost;dbname=".$dbname;
$pdo = new PDO($dsn, $user, $passwd);
$stm = $pdo->query("SHOW TABLES");
$data = $stm->fetchAll();
$tables = array();
foreach($data as $d){
 	$tables[]=$d[0];
 }
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
	current_index = 0;
	batch2 = 0;
	
	function processDump(batch){
		document.getElementById('btn').value = 'Calculating...';	
		document.getElementById('btn').setAttribute("disabled", "disabled");		
		
		if(typeof notRunning == 'undefined'){
			notRunning = setInterval(function() {processDump(batch)}, 3000);
		}
		if(batch2!=batches[current_index]){
			batch2 = batches[current_index];

			jQuery.ajax({
				type: "POST",
				url: "ajax.php?table="+batch2,
				data: {},
				success: function(response){
					//console.log(response);
					if(current_index==batches.length-1){
						var element = document.getElementById("btn");
						element.parentNode.removeChild(element);
						//document.getElementById('btn').value = 'Calculate';	
						//document.getElementById('btn').removeAttribute("disabled");
						clearInterval(notRunning);
						fetchReport();	
					}
					$('img_'+batch2).attr("src", "images/done.png");						
					current_index++;		
				} 
			});
			$('img_'+batch2).attr("src", "images/loading.gif");			
		}
		return false;
	}
	function fetchReport() {
		/*		
		var day = document.getElementById('age_day').value;
		var month = document.getElementById('age_month').value;
		var year = document.getElementById('age_year').value;
		
	    var oOptions = {
				method: "get",	
				parameters: "op=report&day="+day+"&month="+month+"&year="+year,
				onSuccess: function (oXHR, oJson) {
					document.getElementById('all_batches').innerHTML = oXHR.responseText;
					document.getElementById('all_batches').style.display = 'block';
					var element = document.getElementById("btn");
					element.parentNode.removeChild(element);
				}
			};
		var oRequest = new Ajax.Request("{/literal}{php}echo $_SERVER['SCRIPT_NAME']{/php}{literal}?ctg=calc_age", oOptions);
		*/
	}
	function checkReport() {
		/*
		var day = document.getElementById('age_day').value;
		var month = document.getElementById('age_month').value;
		var year = document.getElementById('age_year').value;
		
	    var oOptions = {
				method: "get",	
				parameters: "op=check_report&day="+day+"&month="+month+"&year="+year,
				onSuccess: function (oXHR, oJson) {
					var status = oXHR.responseText;
					if(status=='found'){
						fetchReport();	
					}else{	
						document.getElementById('all_batches').style.display = 'block';
						processDump(122);
					}
				}
			};
		var oRequest = new Ajax.Request("{/literal}{php}echo $_SERVER['SCRIPT_NAME']{/php}{literal}?ctg=calc_age", oOptions);
		*/
		return false;
	}
</script>
<?php
// foreach($data as $d){
// 	$table = $d[0];
// 	$sql = export_structure($table);
	//echo $d[0].'<br />';
	//echo $sql.'<br />';
	
//}
//$sql = export_data($data[0][0],0,100);
//echo $sql;

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
<button id="btn" onclick="processDump();">Export</button>
<div id="all_batches">
	<?php foreach($tables as $table):?>
		<img src="images/pending.png" id="img_<?php echo $table;?>" alt="Pending" />&nbsp;&nbsp;<?php echo $table;?><br />
	<?php endforeach;?>
</div>
</body>
</html>