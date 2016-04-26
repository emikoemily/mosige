<?php
	session_start();
	include("dbconnect.inc.php");
	include("functions.inc.php");

	$mid=$_GET["mid"];
 	
	$sqlsetleave = "UPDATE member_user SET `member_isleave` =1 WHERE `member_id`='{$mid}';";
	 
	$res = $db->exec($sqlsetleave);
	 
	//$db->exec('set names UTF8'); 
	$sqlsetrequest = "UPDATE apply_leave SET `isinprogress` =1 WHERE `member_id`='{$mid}' AND isinprogress =2 order by idapply_leave desc limit 1;";
	$ressetrequest = $db->exec($sqlsetrequest);
	
	
	
	header("Location:msg.php?m=leave_agree");
?>