<?php
	
	session_start();
	if(!$_SESSION["adminuserid"]) header("Location:index.php");
	include("header.inc.php");
	include("dbconnect.inc.php");
	 include("sendmail.php");
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
	
if($_GET){ 
   if(isset($_GET['code'])) {
		
	if($_GET['code'] == $_SESSION['code']){
		
		$choices = $_GET['classchoice']; 
	    $memcell=$_GET['memcell-'.$choices];
	
	
			$sqlmem="select * from member_user where `member_cell`={$memcell} limit 1";
			//echo $sqlmem;
			$db->query('set names UTF8');
			$resmem = $db->query($sqlmem);
			$resmem->setFetchMode(PDO::FETCH_ASSOC);
			$rowmem=$resmem->fetch() ;
			$memid=$rowmem["member_id"];
			$memname=$rowmem["member_name"];
			 
			if(!isset($memid) or $memid==""){
				
				echo '预约失败，会员不存在。</br>';
					
			}else{
			
			
			$sql = "INSERT INTO `yoga_lu`.`register_record` (`arrange_id`, `member_id`,`frombackend`) values ({$choices},{$memid},1);";
				//echo $sql;		
			 
			$row = $db->exec($sql);
			 
			 
				$sqladdcount = "UPDATE class_arrange SET `registercount` =`registercount`+1 WHERE `arrange_id`={$choices};";
				$row2 = $db->exec($sqladdcount);
				 
				//echo $sqladdcount;
	
			}	
			
			if($row!=0&&$row2!=0){
				 
				
				echo '预约成功。</br>';
				 
				$mailcontent="admin:{$_SESSION['username']}  为会员:{$memname} 预约 ";
							//$mailcontent=$mailcontent.getpackageinfo($pids);
							$mailcontent=$mailcontent."arrange_id={$choices}.member_id:$memid";
							$mail->Subject=$mailcontent;
							$mail->Body=$mailcontent;
							$mail->send();
			     }
			     

	   }
	}else{
	
		echo ‘表单重复提交了’;
		header("Location:manage_class.php");
	}		
}
else{
	echo "没有选择课程";
}
?>  
   </div>
    <?php
	//if($_SESSION["userlevel"]=='package' or $_SESSION["userlevel"]=='both')
//	echo  "<a href='registerweek.php' data-role='button' data-inline='false' data-ajax='false'>返回</a>  ";
	//if($_SESSION["userlevel"]=='common' or $_SESSION["userlevel"]=='common_count')
	//echo  "<a href='register.php' data-role='button' data-inline='false' data-ajax='false'>返回</a>   ";
	?>
	<a href='manage_class.php'   >返回</a>
 
</div>  
</body>  

</html>