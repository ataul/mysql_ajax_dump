<?php
$user = "root";
$passwd = "";
$dbname = "db";

$dsn = "mysql:host=localhost;dbname=".$dbname;
$pdo = new PDO($dsn, $user, $passwd);

$stm = $pdo->query("SHOW TABLES");
$data = $stm->fetchAll();

foreach($data as $d){
	echo $d[0].'<br />';
}
?>