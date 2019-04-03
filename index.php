<?php
require_once('db_config.php');
if($pdo){
	$row_count = array();
	$stm = $pdo->query("SHOW TABLES");
	$data = $stm->fetchAll();
	$tables = array();
	$sql = '';
	foreach($data as $d){
		$tables[]=$d[0];
		$sql .= export_structure($d[0]);
	}
	$fpt = fopen('dump.sql','a');
	fwrite($fpt,$sql);
	fclose($fpt);
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>MySQL Ajax Dump</title>	
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
</head>
<body>	
<?php
if(!$pdo){
	require_once('template/login.php');
}else{
?>
<script type="text/javascript">
	batches = <?php echo json_encode($tables);?>;
	row_count = <?php echo json_encode($row_count);?>;
	current_index = 0;
	current_row = 0;
	batch2 = 0;
	limit = 500;
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
					url: "ajax.php?table="+batch3+"&start="+current_row+"&limit="+limit,
					data: {},
					success: function(response){
						if(current_index==batches.length-1){
							clearInterval(notRunning);
							fetchReport();	
						}
						if(row_count2>current_row){
							current_row+=limit;
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
<button id="btn" onclick="processDump();">Export</button>
<div id="all_batches">
	<?php foreach($tables as $table):?>
		<img src="images/pending.png" id="img_<?php echo $table;?>" alt="Pending" />&nbsp;&nbsp;<?php echo $table;?><br />
	<?php endforeach;?>
</div>
<?php } ?>
	<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
</body>
</html>