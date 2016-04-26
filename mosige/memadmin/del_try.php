<?php
	session_start();
	include("dbconnect.inc.php");
	include("functions.inc.php");
	if($_GET["id"]!="" && $_SESSION["adminuserid"]==1 && is_numeric($_GET["id"]) && $_GET["id"]!="1") {
		$id = $_GET["id"];
	}else {
		die("您无权访问该页，请返回或重试。");
	}
	$sql = "delete from try_member_user where member_id='{$id}'";
	echo $sql;
	$res = $db->exec($sql);
	$sql2 = "delete from try_package_subscribe where member_id='{$id}'";
	echo $sql2;
	$res2= $db->exec($sql2);
	 
	
	
	header("Location:msg.php?m=del_success_try");
?>