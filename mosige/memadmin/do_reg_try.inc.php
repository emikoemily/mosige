<?php
	include("dbconnect.inc.php");
	include("functions.inc.php");
	#如果php配置中，magic_quotes_gpc没有被设置，则执行过滤字符串。
	$form = check_form($_POST["edit"]);
	$form["reg_time"] = date("Y-m-d H:i:s");
	$form["pass"] = md5($form["pass"]);
	extract($form);
//$type=$_POST["type"];
	 //echo $type;
	$ertong=0;
	$kong=0;
	$common_week=0;
	$common_1=0;
	
 
	
 
	if($type=="common_week"){
		$days=7;
		$count=50;
		$level_actual="both";
		$sql = "INSERT INTO try_member_user (member_id,member_name,member_password,member_cell,member_level,member_intro,member_attendmax,member_regtime,member_days) ";
 
		$sql .= " values('{$tid}',";
		$sql .= " '{$name}',";
		$sql .= " '{$pass}',";
		$sql .= " '{$tel}', ";
		$sql .= " '{$level_actual}', ";
		$sql .= " '{$intro}', ";
		$sql .= " '{$count}', ";
		$sql .= " '{$reg_time}',";
		$sql .= " {$days}) ;";
		echo $sql;
		$db->exec('set names UTF8'); 
		$res = $db->exec($sql);
		if(!$res) {
			die("数据库出错，请返回重试1。");
		}
		
		$pid="set1";
		//add subs
		$sqlsub = "INSERT INTO try_package_subscribe (member_id,package_id,try_days) ";
		$sqlsub .= " values('{$tid}',";
		$sqlsub .= " '{$pid}',";
		$sqlsub .= " {$days}) ;";
		echo $sqlsub;
		$ressub = $db->exec($sqlsub);
		if(!$ressub) {
			die("数据库出错，请返回重试2。");
		}
		$pid2="set2";
		//add subs
		$sqlsub2 = "INSERT INTO try_package_subscribe (member_id,package_id,try_days) ";
		$sqlsub2 .= " values('{$tid}',";
		$sqlsub2 .= " '{$pid2}',";
		$sqlsub2 .= " {$days}) ;";
		echo $sqlsub2;
		$ressub2 = $db->exec($sqlsub2);
		if(!$ressub2) {
			die("数据库出错，请返回重试2。");
		}
		
		
		
		
	}elseif($type=="common_1"){
		$days=60;
		$count=1;
		$level_actual="common_count";
		$sql = "INSERT INTO try_member_user (member_id,member_name,member_password,member_cell,member_level,member_intro,member_attendmax,member_regtime,member_days) ";
 
		$sql .= " values('{$tid}',";
		$sql .= " '{$name}',";
		$sql .= " '{$pass}',";
		$sql .= " '{$tel}', ";
		$sql .= " '{$level_actual}', ";
		$sql .= " '{$intro}', ";
		$sql .= " '{$count}', ";
		$sql .= " '{$reg_time}',";
		$sql .= " {$days}) ;";
		echo $sql;
		$db->exec('set names UTF8'); 
		$res = $db->exec($sql);
		if(!$res) {
			die("数据库出错，请返回重试。");
		}
	}elseif($type=="try_ertong"){
		$days=60;
		$count=1;
		$level_actual="package";
		$intro1="儿童1次";
		$sql = "INSERT INTO try_member_user (member_id,member_name,member_password,member_cell,member_level,member_intro,member_attendmax,member_regtime,member_days) ";
 
		$sql .= " values('{$tid}',";
		$sql .= " '{$name}',";
		$sql .= " '{$pass}',";
		$sql .= " '{$tel}', ";
		$sql .= " '{$level_actual}', ";
		$sql .= " '{$intro1}', ";
		$sql .= " '{$count}', ";
		$sql .= " '{$reg_time}',";
		$sql .= " {$days}) ;";
		echo $sql;
		$db->exec('set names UTF8'); 
		$res = $db->exec($sql);
		if(!$res) {
			die("数据库出错，请返回重试。");
		}
		
		$pid="set2";
		//add subs
		$sqlsub = "INSERT INTO try_package_subscribe (member_id,package_id,try_days) ";
		$sqlsub .= " values('{$tid}',";
		$sqlsub .= " '{$pid}',";
		$sqlsub .= " {$days}) ;";
		echo $sqlsub;
		$ressub = $db->exec($sqlsub);
		if(!$ressub) {
			die("数据库出错，请返回重试。");
		}
	}elseif($type=="try_kong"){
		$days=60;
		$count=1;
		$level_actual="package";
		$intro1="空中1次";
		$sql = "INSERT INTO try_member_user (member_id,member_name,member_password,member_cell,member_level,member_intro,member_attendmax,member_regtime,member_days) ";
 
		$sql .= " values('{$tid}',";
		$sql .= " '{$name}',";
		$sql .= " '{$pass}',";
		$sql .= " '{$tel}', ";
		$sql .= " '{$level_actual}', ";
		$sql .= " '{$intro1}', ";
		$sql .= " '{$count}', ";
		$sql .= " '{$reg_time}',";
		$sql .= " {$days}) ;";
		echo $sql;
		$db->exec('set names UTF8'); 
		$res = $db->exec($sql);
		
		$pid="set1";
		//add subs
		$sqlsub = "INSERT INTO try_package_subscribe (member_id,package_id,try_days) ";
		$sqlsub .= " values('{$tid}',";
		$sqlsub .= " '{$pid}',";
		$sqlsub .= " {$days}) ;";
		echo $sqlsub;
		$ressub = $db->exec($sqlsub);
		if(!$ressub) {
			die("数据库出错，请返回重试。");
		}
	}elseif($type=="25day"){
		$days=3;
		$count=10;
		$level_actual="both";
		$sql = "INSERT INTO try_member_user (member_id,member_name,member_password,member_cell,member_level,member_intro,member_attendmax,member_regtime,member_days) ";
 
		$sql .= " values('{$tid}',";
		$sql .= " '{$name}',";
		$sql .= " '{$pass}',";
		$sql .= " '{$tel}', ";
		$sql .= " '{$level_actual}', ";
		$sql .= " '25day', ";
		$sql .= " '{$count}', ";
		$sql .= " '{$reg_time}',";
		$sql .= " {$days}) ;";
		echo $sql;
		$db->exec('set names UTF8'); 
		$res = $db->exec($sql);
		if(!$res) {
			die("数据库出错，请返回重试1。");
		}
		
		$pid="set1";
		//add subs
		$sqlsub = "INSERT INTO try_package_subscribe (member_id,package_id,try_days,package_startdate,package_enddate) ";
		$sqlsub .= " values('{$tid}',";
		$sqlsub .= " '{$pid}',";
		$sqlsub .= " {$days},";
		$sqlsub .= " '2015-10-25 00:00:00',";
		$sqlsub .= " '2015-10-26 00:00:00') ;";
		echo $sqlsub;
		$ressub = $db->exec($sqlsub);
		if(!$ressub) {
			die("数据库出错，请返回重试2。");
		}
		$pid2="set2";
		//add subs
		$sqlsub2 = "INSERT INTO try_package_subscribe (member_id,package_id,try_days,package_startdate,package_enddate) ";
		$sqlsub2 .= " values('{$tid}',";
		$sqlsub2 .= " '{$pid2}',";		
		$sqlsub2 .= " {$days},";
		$sqlsub2 .= " '2015-10-25 00:00:00',";
		$sqlsub2 .= " '2015-10-26 00:00:00');";
		echo $sqlsub2;
		$ressub2 = $db->exec($sqlsub2);
		if(!$ressub2) {
			die("数据库出错，请返回重试2。");
		}
		
	}elseif($type=="tiyan_2"){
		$days=7;
		$count=50;
		$level_actual="both";
		$sql = "INSERT INTO try_member_user (member_id,member_name,member_password,member_cell,member_level,member_intro,member_attendmax,member_regtime,member_days) ";
 
		$sql .= " values('{$tid}',";
		$sql .= " '{$name}',";
		$sql .= " '{$pass}',";
		$sql .= " '{$tel}', ";
		$sql .= " '{$level_actual}', ";
		$sql .= " '{$intro}', ";
		$sql .= " '{$count}', ";
		$sql .= " '{$reg_time}',";
		$sql .= " {$days}) ;";
		echo $sql;
		$db->exec('set names UTF8'); 
		$res = $db->exec($sql);
		if(!$res) {
			die("数据库出错，请返回重试1。");
		}
		
		$pid="set1";
		//add subs
		$sqlsub = "INSERT INTO try_package_subscribe (member_id,package_id,try_days) ";
		$sqlsub .= " values('{$tid}',";
		$sqlsub .= " '{$pid}',";
		$sqlsub .= " {$days}) ;";
		echo $sqlsub;
		$ressub = $db->exec($sqlsub);
		if(!$ressub) {
			die("数据库出错，请返回重试2。");
		}
		$pid2="set2";
		//add subs
		$sqlsub2 = "INSERT INTO try_package_subscribe (member_id,package_id,try_days) ";
		$sqlsub2 .= " values('{$tid}',";
		$sqlsub2 .= " '{$pid2}',";
		$sqlsub2 .= " {$days}) ;";
		echo $sqlsub2;
		$ressub2 = $db->exec($sqlsub2);
		if(!$ressub2) {
			die("数据库出错，请返回重试2。");
		}
		
		
		
		
	}elseif($type=="tiyan_1"){
		$days=7;
		$count=1;
		$level_actual="tiyan_1";
		$sql = "INSERT INTO try_member_user (member_id,member_name,member_password,member_cell,member_level,member_intro,member_attendmax,member_regtime,member_days) ";
 
		$sql .= " values('{$tid}',";
		$sql .= " '{$name}',";
		$sql .= " '{$pass}',";
		$sql .= " '{$tel}', ";
		$sql .= " '{$level_actual}', ";
		$sql .= " '{$intro}', ";
		$sql .= " '{$count}', ";
		$sql .= " '{$reg_time}',";
		$sql .= " {$days}) ;";
		echo $sql;
		$db->exec('set names UTF8'); 
		$res = $db->exec($sql);
		if(!$res) {
			die("数据库出错，请返回重试1。");
		}
		
		$pid="set1";
		//add subs
		$sqlsub = "INSERT INTO try_package_subscribe (member_id,package_id,try_days) ";
		$sqlsub .= " values('{$tid}',";
		$sqlsub .= " '{$pid}',";
		$sqlsub .= " {$days}) ;";
		echo $sqlsub;
		$ressub = $db->exec($sqlsub);
		if(!$ressub) {
			die("数据库出错，请返回重试2。");
		}
		$pid2="set2";
		//add subs
		$sqlsub2 = "INSERT INTO try_package_subscribe (member_id,package_id,try_days) ";
		$sqlsub2 .= " values('{$tid}',";
		$sqlsub2 .= " '{$pid2}',";
		$sqlsub2 .= " {$days}) ;";
		echo $sqlsub2;
		$ressub2 = $db->exec($sqlsub2);
		if(!$ressub2) {
			die("数据库出错，请返回重试2。");
		}
		
		
		
		
	}elseif($type=="tiyan_3"){
		$days=31;
		$count=3;
		$level_actual="tiyan_3";
		$intro="39 三次";
		$sql = "INSERT INTO try_member_user (member_id,member_name,member_password,member_cell,member_level,member_intro,member_attendmax,member_regtime,member_cardid,member_days) ";
 
		$sql .= " values('{$tid}',";
		$sql .= " '{$name}',";
		$sql .= " '{$pass}',";
		$sql .= " '{$tel}', ";
		$sql .= " '{$level_actual}', ";
		$sql .= " '{$intro}', ";
		$sql .= " '{$count}', ";
		$sql .= " '{$reg_time}',";
		$sql .= " '{$cardid}',";
		$sql .= " {$days}) ;";
		echo $sql;
		$db->exec('set names UTF8'); 
		$res = $db->exec($sql);
		if(!$res) {
			die("数据库出错，请返回重试1。");
		}
		
		$pid="set1";
		//add subs
		$sqlsub = "INSERT INTO try_package_subscribe (member_id,package_id,try_days) ";
		$sqlsub .= " values('{$tid}',";
		$sqlsub .= " '{$pid}',";
		$sqlsub .= " {$days}) ;";
		echo $sqlsub;
		$ressub = $db->exec($sqlsub);
		if(!$ressub) {
			die("数据库出错，请返回重试2。");
		}
		$pid2="set2";
		//add subs
		$sqlsub2 = "INSERT INTO try_package_subscribe (member_id,package_id,try_days) ";
		$sqlsub2 .= " values('{$tid}',";
		$sqlsub2 .= " '{$pid2}',";
		$sqlsub2 .= " {$days}) ;";
		echo $sqlsub2;
		$ressub2 = $db->exec($sqlsub2);
		if(!$ressub2) {
			die("数据库出错，请返回重试2。");
		}
		
		
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	/*if($common_week==1){
		
		$sql = "INSERT INTO try_member_user (member_name,member_password,member_email,member_sex,member_cell,member_level,member_birthday,member_points,member_intro,member_attendmax,member_cardid,member_regtime,member_days) ";
	#这里{}符号是代表在字符串中引用当前环境的变量
	}
	
	
	/*if($mt==1){
	$sql = "INSERT INTO try_member_user (member_name,member_password,member_email,member_sex,member_cell,member_level,member_birthday,member_points,member_intro,member_attendmax,member_cardid,member_regtime,member_days) ";
	#这里{}符号是代表在字符串中引用当前环境的变量
	$sql .= " values('{$name}',";
	//$sql .= " values('A哦哦哦',";
	$sql .= " '{$pass}',";
	$sql .= " '{$mail}', ";
	$sql .= " '{$sex}',";
	$sql .= " '{$tel}', ";
	$sql .= " '{$level_actual}', ";
	$sql .= " '{$birthday}', ";
	$sql .= " '{$point}', ";
	$sql .= " '{$intro}', ";
	$sql .= " '{$count}', ";
	$sql .= " '{$cardid}',";
	$sql .= " '{$reg_time}',";
	$sql .= " {$days}) ;";
	//echo $sql;
	$db->query('set names UTF8'); 
	$res = $db->query($sql);
	if(!$res) {
		die("数据库出错，请返回重试。");
	}
	
	}elseif($mt==2){
		
	$sql = "INSERT INTO member_user (member_name,member_password,member_email,member_sex,member_cell,member_level,member_birthday,member_points,member_intro,member_attendmax,member_cardid,member_regtime,member_days,member_enddate) ";
	#这里{}符号是代表在字符串中引用当前环境的变量
	$sql .= " values('{$name}',";
	//$sql .= " values('A哦哦哦',";
	$sql .= " '{$pass}',";
	$sql .= " '{$mail}', ";
	$sql .= " '{$sex}',";
	$sql .= " '{$tel}', ";
	$sql .= " '{$level_actual}', ";
	$sql .= " '{$birthday}', ";
	$sql .= " '{$point}', ";
	$sql .= " '{$intro}', ";
	$sql .= " '{$count}', ";
	$sql .= " '{$cardid}',";
	$sql .= " '{$reg_time}',";
	$sql .= " {$days}) ";
	$sql .= " {$endday}) ;";
	//echo $sql;
	$db->query('set names UTF8'); 
	$res = $db->query($sql);
	
	
	
	if(!$res) {
		die("数据库出错，请返回重试。");
		}
	}*/
	
	header("Location:msg.php?m=register_success");
?>