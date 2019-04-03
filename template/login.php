<?php
if(isset($_REQUEST['username'])&&strlen($_REQUEST['username'])){
	$fpt = fopen('db_config.php','w');
	$data = '<?php
	$user = "'.$_REQUEST['username'].'";
	$passwd = "'.$_REQUEST['password'].'";
	$dbname = "'.$_REQUEST['database'].'";
	$host = "localhost";
	$dsn = "mysql:host=$host;dbname=".$dbname;
	require_once("include.php");
	?>';
	fwrite($fpt,$data);
	fclose($fpt);
?>
<script>
location.href='index.php';
</script>	
<?php
}
?>
<div class="card">
	<div class="card-body">
		<div class="m-sm-4">									
			<form method="post" action="">
				<div class="form-group">
					<label>Username</label>
					<input class="form-control form-control-lg" type="text" name="username" placeholder="Enter database username" />
				</div>
				<div class="form-group">
					<label>Password</label>
					<input class="form-control form-control-lg" type="password" name="password" placeholder="Enter database password" />					
				</div>
				<div class="form-group">
					<label>Database</label>
					<input class="form-control form-control-lg" type="text" name="database" placeholder="Enter database name" />
				</div>
				<div>
					<div class="custom-control custom-checkbox align-items-center">
						<input type="checkbox" class="custom-control-input" value="remember-me" name="remember-me" checked>
						<label class="custom-control-label text-small">Remember me (save to configuration file)</label>
					</div>
				</div>
				<div class="text-center mt-3">					
					<button type="submit" class="btn btn-lg btn-primary">Connect</button>
				</div>
			</form>
		</div>
	</div>
</div>