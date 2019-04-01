<?php
$user = "";
$passwd = "";
$dbname = "";
$dsn = "mysql:host=localhost;dbname=".$dbname;
$pdo = new PDO($dsn, $user, $passwd);
?>