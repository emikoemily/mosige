<?php
	include("settings.inc.php");
	$dsn = "mysql:host={$dbhost};dbname={$dbname}";
       $db = new PDO($dsn,$dbuser,$dbpass);
?>

 
 