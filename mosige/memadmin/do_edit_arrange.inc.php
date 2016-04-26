<?php
	session_start();
	include("dbconnect.inc.php");
	include("functions.inc.php");
	#如果php配置中，magic_quotes_gpc没有被设置，则执行过滤字符串。
	//$form = check_form($_POST["edit"]);
	if($_POST){
	$arrid=$_POST["arrangeid"];
	$classid=$_POST["classid"];
	$arrangedate=$_POST["arrangedate"];
	$starttime=$_POST["starttime"];
	$endtime=$_POST["endtime"];
	$max=$_POST["max"];
	$trymax=$_POST["trymax"];
	$classroom=$_POST["classroom"];
	$teacherid=$_POST["teacherid"];
	
	$sqladdclass ="UPDATE `yoga_lu`.`class_arrange` 
	set 
	`class_id`='{$classid}',
	`arrangedate`='{$arrangedate}',
	`starttime`='{$starttime}',
	`endtime`='{$endtime}',
	`teacher_id`='{$teacherid}',
	`maxposition`='{$max}',
	`try_maxposition`='{$trymax}',
	`classroom`='{$classroom}'
	where `arrange_id`={$arrid};";
	//$db->query('set names UTF8'); 
	$res = $db->exec($sqladdclass);
	
	
	
	
	header("Location:manage_class.php");
		
		
	}
	
?>