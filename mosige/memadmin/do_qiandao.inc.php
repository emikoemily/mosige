<?php
	session_start();
	include("dbconnect.inc.php");
	include("functions.inc.php");
	include("sendmail.php");
	#如果php配置中，magic_quotes_gpc没有被设置，则执行过滤字符串。
	//$form = check_form($_POST["edit"]);
	$choices=$_GET["regid"];
	$pid=$_GET["pid"];
	$memid=$_GET["mid"];
	$inid=$_GET["innerid"];
	$classtype=$_GET["ctype"];
	//echo $choices;
	//echo $pid;
	//echo $memid;
	//echo $inid;
	//echo $classtype;
	
			
			$sql = "UPDATE register_record SET `is_attended` =`is_attended`+1000 WHERE `register_record`.`register_id`= {$choices} and `is_attended`<2;";
			$sqladd = "UPDATE member_user SET `member_classcount` = `member_classcount`+1 WHERE `member_id`= {$memid};";
			//echo $sql;
			//echo $sqladd;
			function addInnerRecored($db,$memid,$pid,$inid){
				
				$sql_addinnerrec = "INSERT into jump_record (`member_id`,`package_id`,`inner_id`) values ({$memid},'{$pid}',{$inid})";
			    
				$resaddinnerrec = $db->exec($sql_addinnerrec);
			  }
			function addPackageSub($db,$memid,$pid){
				$sqladdpackage ="UPDATE package_subscribe SET `package_attended` = `package_attended`+1 WHERE `member_id`= {$memid} AND `package_id`='{$pid}';";
			    //echo $sqladdpackage;
				$row3 = $db->exec($sqladdpackage);	
				 
			   if($row3>0){
			
				 
			   }
			}
			function startcard($db,$classtype){
			$start=(date('Y-m-d H:i:s'));
						 
						if($classtype=='package' or $classtype=='set'){
							$sqlUpdateStart="UPDATE payment_table a inner join package_subscribe b on a.payment_id = b.payment_id SET a.payment_startdate = '{$start}',a.payment_enddate=date_add('{$start}', interval payment_days day) WHERE (a.payment_enddate is NULL or a.payment_enddate='0000-00-00 00:00:00') AND b.package_id='{$pid}' AND b.member_id = '{$memid}';";
							//echo $sqlUpdateStart;
							$row3 = $db->exec($sqlUpdateStart);	
							 
				   
							 
				    
						}
						elseif(($classtype=='common'  or $classtype=='set') AND ($_SESSION["userlevel"] =='common' or (substr($_SESSION["userlevel"],0,12)=='common_count')) ){
							$sqlUpdateStart="UPDATE member_user SET member_startdate = '{$start}',member_enddate= date_add('{$start}', interval member_days day) WHERE (member_user.member_enddate is NULL or member_user.member_enddate ='0000-00-00 00:00:00') AND member_user.member_id ={$memid};";
							//echo $sqlUpdateStart;
							$row3 = $db->exec($sqlUpdateStart);	
							 
							//echo "affect：".$row3;
						}
						else{
				
						}
			}
			//echo $classtype;
			 $mail->Subject="正 测试 邮件 后台 sign";
				  $mail->Body=$_SESSION['userid'].$pid." -".$innerid ."memberid:".$memid;
				  $mail->send(); 
			
			$rowqiandao = $db->exec($sql);
			 
			if($rowqiandao>0){
				
				startcard($db,$classtype);
				$resadd = $db->exec($sqladd);
				
				if($classtype=='package' or $classtype=='set')
			 {
				addInnerRecored($db,$memid,$pid,$innerid);
				addPackageSub($db,$memid,$pid);
				
			 }
			}
			
			
		
			
			
		
	
	
	if(!$res&&!$resadd){
		echo mysqli_error();
		die("数据库出错，请返回重试。");
	}
	
	header("Location:msg.php?m=update_success_manage_attend");
?>