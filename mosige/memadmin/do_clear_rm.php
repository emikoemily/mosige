<?php
	session_start();
	include("dbconnect.inc.php");
	include("functions.inc.php");
	#如果php配置中，magic_quotes_gpc没有被设置，则执行过滤字符串。


	if($_GET["rmid"]!="") {
	

	 
	$sqldel ="UPDATE `yoga_lu`.`running_arrange` set is_reg=0,reg_mins=0,member_id=NULL WHERE idrunning_arrange={$_GET['rmid']} ;";
		 //echo 
	       $res = $db->exec($sqldel);
          // $db->query('set names UTF8'); 		   
	 
	}
   else{	
         $sqldel ="UPDATE `yoga_lu`.`running_arrange` set is_reg=0,reg_mins=0,member_id=NULL;";
		 //echo 
	       $res = $db->exec($sqldel);
          // $db->query('set names UTF8'); 		   
	 
	
	
		
   }
	header("Location:msg.php?m=update_success_manage_rm");
?>