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
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
<main class="main h-100 w-100">
	<div class="container h-100">
		<div class="row h-100">
			<div class="col-sm-10 col-md-8 col-lg-6 mx-auto d-table h-100">
				<div class="d-table-cell align-middle">
					<h4 class="display-4 project-name">MySQL Ajax Dump</h4>
	
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
	
	function addCheckBox(){
		$.each($('.img_table'), function (){
			var id=$(this).attr('id');
			id = id.substring(3,id.length);
			$(this).append('<input type="checkbox" name="skipped_table[]" value="'.id.'" />');
			$(this).remove();
		});
	}
	
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
							$('#all_tables').html('<div class="alert alert-info">SQL file should download automatically. If it doesn\'t work, please click <a href="dump.sql">here</a></div>');
							location.href='dump.sql';	
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
							$('#all_tables').html('<div class="alert alert-info">SQL file should download automatically. If it doesn\'t work, please click <a href="dump.sql">here</a></div>');
							location.href='dump.sql';
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
<div class="card">
	<div class="card-body">
		<div class="m-sm-4">
		<a href="javascript:" onclick="addCheckBox();">Skipped table</a>
			<button class="btn btn-lg btn-primary" id="btn" onclick="processDump();">Export</button>
			<div id="all_tables">
				<?php foreach($tables as $table):?>
					<img src="images/pending.png" class="img_table" id="img_<?php echo $table;?>" alt="Pending" />&nbsp;&nbsp;<?php echo $table;?><br />
				<?php endforeach;?>
			</div>
		</div>	
	</div>	
</div>	
<?php } ?>
				</div>
			</div>
		</div>
	</div>
</main>	
	<script type="text/javascript" src="js/jquery-3.3.1.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
</body>
</html>