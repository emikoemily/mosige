<?php
	include("dbconnect.inc.php");
	include("functions.inc.php");
	#如果php配置中，magic_quotes_gpc没有被设置，则执行过滤字符串。
	$form = check_form($_POST["edit"]);
	//$form["reg_time"] = date("Y-m-d H:i:s");
	//$form["pass"] = md5($form["pass"]);
	extract($form);
	$sql = "insert into users( username,password,level) ";
	#这里{}符号是代表在字符串中引用当前环境的变量
	$sql .= " values('{$name}',";
	$sql .= " '{$pass}',";
	$sql .= " '{$level}')";
	//$sql .= " '{$reg_time}') ";
	
	$res = $db->exec($sql);
	 
	
	header("Location:msg.php?m=register_success");
?>