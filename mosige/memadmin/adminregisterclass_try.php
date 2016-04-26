<?php
	
	session_start();
	 
	
?>
<html>
<head>
 <title>Mosige 后台预约</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
<style type="text/css" media="all">@import "style.css";</style>
<style type="text/css" media="all">@import "common.css";</style>
</head>
<body>


      
	<?php
	
	$result=0;
	echo $_GET['code'];
	echo "s-".$_SESSION['code'];
	if(isset($_GET['code'])) {
	
		if($_GET['code'] == $_SESSION['code']){
				
			if(!$_SESSION["adminuserid"]) header("Location:index.php");
			include("header.inc.php");
			include("dbconnect.inc.php");
	 		include("sendmail.php");
	 		include("entity/TryRegister.php");
	 		$choices = $_GET['classchoice'];
	 		$memcell=$_GET['memcell-'.$choices];
	 		$tempname=$_GET['temp_name'];
	 		if($_GET['temp']=="on" or isset($tempname)){
	 		
	 			TryRegister::createRegisterForStoreGuest($memcell, $choices,$tempname);
	 			$mailcontent="admin:{$_SESSION['username']}  为会员:{$tempname} 预约 ";
	 				
	 			$mailcontent=$mailcontent.$memcell;
	 			$maildebug->Subject=$mailcontent;
	 			$maildebug->Body=$mailcontent;
	 			$maildebug->send();
	 			echo '预约成功。</br>';
	 		
	 		}else{
	 		
	 			$sqlmem="select * from try_member_user where `member_cell`={$memcell} limit 1";
	 			//echo $sqlmem;
	 			$db->query('set names UTF8');
	 			$resmem = $db->query($sqlmem);
	 			$resmem->setFetchMode(PDO::FETCH_ASSOC);
	 			$rowmem=$resmem->fetch();
	 			$memid=$rowmem["member_id"];
	 			$memname=$rowmem["member_name"];
	 		
	 				
	 			if(!isset($memid) or $memid==""){
	 		
	 				echo '预约失败，会员不存在。</br>';
	 		
	 			}else{
	 					
	 				$sql = "INSERT INTO `yoga_lu`.`try_register_record` (`arrange_id`, `member_id`) select " .$choices.",'{$memid}' from dual where not exists (select * from try_register_record
	 				where arrange_id={$choices} AND is_canceled=0 AND member_id='{$memid}');";
	 				echo $sql;
	 		
	 				$row = $db->exec($sql);
	 		
	 					
	 					
	 					
	 				if($row==0){
	 					//$db->query("ROLLBACK");
	 					echo '已预约过这门课了</br>';
	 		
	 				}
	 				else {
	 					//$db->query("BEGIN");
	 					echo $sqladdcount;
	 					$sqladdcount = "UPDATE class_arrange SET `try_registercount` =`try_registercount`+1 WHERE `arrange_id`={$choices};";
	 					$row2 = $db->exec($sqladdcount);
	 					echo $sqladdcount;
	 		
	 						
	 					if($row!=0&&$row2!=0){
	 						//$db->query("COMMIT");
	 							
	 						echo '预约成功。.</br>';
	 							
	 						$mailcontent="admin:{$_SESSION['username']}  为会员:{$memname} 预约 ";
	 						//$mailcontent=$mailcontent.getpackageinfo($pids);
	 						$mailcontent=$mailcontent."arrange_id={$choices}.member_id:$memid";
	 						$mail->Subject=$mailcontent;
	 						$mail->Body=$mailcontent;
	 						$mail->send();
	 					}
	 		
	 				}
	 			}
	 		}	
			 
			 
				
		 
	
		}else{
	
			echo ‘请不要刷新本页面或重复提交表单！’;
			header("Location:manage_class.php");
		}
	}
	

?>  
   </div>

	
	<a href='manage_class.php'   >返回</a>
 
</div>  
</body>  

</html>