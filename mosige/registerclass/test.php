<?php 
session_start();
$code = mt_rand(0,1000000);
$_SESSION['code'] = $code;
//echo $_SESSION["usercell"];
	if(!$_SESSION["usercell"]) 
   {
	   header("location:do_login.php");
  }
	include("header.inc.php");
	
	
	
	include 'entity/Leave.php';
	
	
	Leave::applyLeaveRequest(73,"2016-01-13","2016-01-14","过年");
 
	
?>