<?php
session_start();
if(isset($_GET['code'])) {
	
	if($_GET['code'] == $_SESSION['code']){
	
			if(!$_SESSION["adminuserid"])
		{
			header("Location:index.php");
		}
		include("dbconnect.inc.php");
		include("functions.inc.php");
		include("sendmail.php");
		
		$regid=$_GET["regid"];
		$attdid=$_GET["attdid"];
		$mid=$_GET["mid"];
	 
		
		$sql = "UPDATE register_record SET `is_canceled` =1,`is_attended`=0 WHERE register_record.register_id= {$regid}";
		$sqlminuscount = "UPDATE class_arrange SET `registercount` =`registercount`-1 WHERE `arrange_id`={$attdid};";
		$sqladdreason ="INSERT INTO `yoga_lu`.`cancel_register` (`arrange_id`, `cancel_reason`,`member_id`) VALUES ('{$attdid}','管理员操作','{$mid}');";
		$db->exec('set names UTF8');
		$rowqx = $db->exec($sql);
		 
		if($rowqx>0){
			
			$res2 = $db->exec($sqlminuscount);
			
			$res3 = $db->exec($sqladdreason);
			//echo $sqlminuscount;
			//echo $sqladdreason;
				
		}
			$mailcontent="admin:{$_SESSION['username']}  为正式会员:{$mid} 取消arrid:{$attdid} regid:{$regid}";
			$mailcontent=$mailcontent."_{$code}";
			$maildebug->Subject=$mailcontent;
			$maildebug->Body=$mailcontent;
			$maildebug->send();
		
		 
		 
			header("Location:msg.php?m=update_success_manage_attend");
		
			}else{
		
				echo ‘请不要刷新本页面或重复提交表单！’;
				header("Location:manage_attend.php");
		
			}
		
	}else{
		
		
	}
	
?>