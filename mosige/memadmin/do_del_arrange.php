<?php
	session_start();
	include("dbconnect.inc.php");
	include("functions.inc.php");
	#如果php配置中，magic_quotes_gpc没有被设置，则执行过滤字符串。

	if( $_GET ){ 
		 
		 $mid = $_GET['mid'];
		 
         $sqldel ="DELETE from `yoga_lu`.`class_arrange` WHERE `class_arrange`.`arrange_id`={$mid};";
		 //echo 
	       $res = $db->exec($sqldel);
            		   
	 
	}
	
	
	
	header("Location:manage_class.php");
?>