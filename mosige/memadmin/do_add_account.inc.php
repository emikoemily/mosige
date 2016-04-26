<?php
	include("dbconnect.inc.php");
	include("functions.inc.php");
	#如果php配置中，magic_quotes_gpc没有被设置，则执行过滤字符串。
	$form = check_form($_POST["edit"]);
	$form["reg_time"] = date("Y-m-d H:i:s");
	$form["pass"] = md5($form["pass"]);
	extract($form);
	$days=NULL;
	if($level=='common'){$days=365;$level_actual='common';$level_actual='common';}
	elseif($level=='common_half') {$days=183;$level_actual='common';}
	elseif($level=='package' ){$days=365;$level_actual='package';}
	elseif($level=='common_count'){$days=365;$level_actual='common_count';}
	elseif($level=='common_2'){$days=730;$level_actual='common';}
	elseif($level=='both_c2y'){$days=730;$level_actual='both';}
	elseif($level=='both_chalf'){$days=183;$level_actual='both';}
	elseif($level=='both_c1y'){$days=365;$level_actual='both';}
	elseif($level=='both_c10'){$days=365;$level_actual='both_count';}
	else{$days=365;}
	date_default_timezone_set('PRC');
    //$now=date('Y-m-d H:i:s');
	$sql = "insert into member_user (member_name,member_pass,member_email,member_sex,member_cell,member_level,member_birthday,member_point,intro,member_days) ";
	#这里{}符号是代表在字符串中引用当前环境的变量
	echo $name;
	$sql .= " values('{$name}',";
	$sql .= " '{$pass}',";
	
	$sql .= " '{$mail}', ";
	$sql .= " '{$sex}',";
	$sql .= " '{$tel}', ";
	$sql .= " '{$level_actual}', ";
	$sql .= " '{$birthday}', ";
	$sql .= " '{$point}', ";
	$sql .= " '{$intro}', ";
	//$sql .= " '{$now}', ";
	$sql .= " {$days}) ";
	//$db->query('set names UTF8'); 	
	$res = $db->exec($sql);
	echo $sql;
	
	
	//header("Location:msg.php?m=register_success");
?>