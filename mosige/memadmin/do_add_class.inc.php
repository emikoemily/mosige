<?php
	session_start();
	include("dbconnect.inc.php");
	include("functions.inc.php");
	 include("sendmail.php");
	#如果php配置中，magic_quotes_gpc没有被设置，则执行过滤字符串。
	//$form = check_form($_POST["edit"]);
	if($_POST){
		
	$classrefid=$_POST["classid"];
	$arrangedate=$_POST["arrangedate"];
	$starttime=$_POST["starttime"];
	//$endtime=$_POST["endtime"];
	$mins=$_POST["mins"];
	$endtime= date('H:i:s',strtotime($starttime)+$mins*60);
	$max=$_POST["max"];
	$trymax=$_POST["trymax"];
	$classroom=$_POST["classroom"];
	$teacherid=$_POST["teacherid"];
	if($_POST["overlap"]!=0){
		$overlap=$_POST["arrangedate"]."_".$_POST["overlap"];
	}else{
		$overlap="0";
	}
	
	
	$same=$_POST["same"];
	$commonmax=$_POST["commonmax"];
	//$forref=$_POST["forref"];
	$select_value=explode(",",$classrefid);
	$classid=$select_value[0];
	$forref=$select_value[1];
	
	echo "bbbb".$classid;
	echo "asdf".$forref;
	$fordebug="";
	if($same=="on" and $forref!=""){
		 
		$sqladdclass ="INSERT INTO `yoga_lu`.`class_arrange` (`class_id`, `arrangedate`,`starttime`,`endtime`,`teacher_id`,`maxposition`,`classroom`,`overlap`) VALUES ({$classid},'{$arrangedate}','{$starttime}','{$endtime}','{$teacherid}','{$max}','{$classroom}','{$overlap}');";
		$sqladdrefclass ="INSERT INTO `yoga_lu`.`class_arrange` (`class_id`, `arrangedate`,`starttime`,`endtime`,`teacher_id`,`maxposition`,`try_maxposition`,`classroom`,`overlap`) VALUES ({$forref},'{$arrangedate}','{$starttime}','{$endtime}','{$teacherid}','{$commonmax}','{$trymax}','{$classroom}','{$overlap}');";
		$fordebug=$sqladdclass.$sqladdrefclass;
		$db->exec('set names UTF8'); 
		$res = $db->exec($sqladdclass);	
		$resref = $db->exec($sqladdrefclass);
	
		/*$mailcontent="admin:{$_SESSION['username']} arrange class  ";
							//$mailcontent=$mailcontent.getpackageinfo($pids);
							$mailcontent=$mailcontent."class_id={$classid}";
							$mail->Subject=$mailcontent;
							$mail->Body=$mailcontent;
							$mail->send();*/
		$mailcontent="admin: arrange 2 class  ";
		//$mailcontent=$mailcontent.getpackageinfo($pids);
		$mailcontent=$mailcontent."class_id={$classid}";
		$mail->Subject=$mailcontent;
		$mail->Body=$mailcontent."sql,sqlref:".$fordebug;
		$mail->send();
	
		if(!$res or !$resref) {
			echo mysqli_error();
			die("数据库出错，请返回重试。");
		}
	}
	else{
		
		$sqladdclass ="INSERT INTO `yoga_lu`.`class_arrange` (`class_id`, `arrangedate`,`starttime`,`endtime`,`teacher_id`,`maxposition`,`try_maxposition`,`classroom`,`overlap`) VALUES ('{$classid}','{$arrangedate}','{$starttime}','{$endtime}','{$teacherid}','{$max}','{$trymax}','{$classroom}','{$overlap}');";
		//$sqladdrefclass ="INSERT INTO `yoga_lu`.`class_arrange` (`class_id`, `arrangedate`,`starttime`,`endtime`,`teacher_id`,`maxposition`,`try_maxposition`,`classroom`,`overlap`) VALUES ('{$forref}','{$arrangedate}','{$starttime}','{$endtime}','{$teacherid}','{$commonmax}','{$trymax}','{$classroom}','{$overlap}');";
		$fordebug="else:".$sqladdclass;
		$db->exec('set names UTF8'); 
		$res = $db->exec($sqladdclass);	
		//$resref = $db->query($sqladdrefclass);
	
	$mailcontent="admin: arrange class  ";
							//$mailcontent=$mailcontent.getpackageinfo($pids);
							$mailcontent=$mailcontent."class_id={$classid}";
							$mail->Subject=$mailcontent;
							$mail->Body=$mailcontent."sql: ".$fordebug;
							$mail->send();
	
		
		
	}
	header("Location:manage_class.php");
		
		
	}
	
?>