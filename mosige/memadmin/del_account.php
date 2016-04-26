<?php
	session_start();
	include("dbconnect.inc.php");
	include("functions.inc.php");
	if($_GET["id"]!="" && $_SESSION["adminuserid"]==1 && is_numeric($_GET["id"]) && $_GET["id"]!="1") {
		$id = $_GET["id"];
	}else {
		die("您无权访问该页，请返回或重试。");
	}
	$sql = "delete from member_user where member_id={$id}";
	$res = $db->exec($sql);
	
	
	header("Location:msg.php?m=del_success");
?>