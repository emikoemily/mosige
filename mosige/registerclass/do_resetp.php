<?php
	
session_start();	
date_default_timezone_set('PRC');
if($_POST){
	$usercell=$_POST['usercell'];
	$password_new=md5($_POST['password_new']);
	$password_old=md5($_POST['password_old']);
	$password_new2=md5($_POST['password_new2']);
	//echo $_POST;
}

	require_once(dirname(__FILE__).'/'."dbconf/settings.inc.php");
	require_once(dirname(__FILE__).'/'."dbconf/dbconnect.inc.php");
	$query = "SELECT * FROM member_user WHERE member_cell = '{$usercell}' limit 1;";
	//echo $query;
		
	$db->query('set names UTF8'); 
	$res = $db->query($query);
	$row = mysqli_fetch_array($res);
	//echo $row["member_password"];
	if($usercell==NULL or $password_old !== $row["member_password"]) {
		
		header("Location:msg.php?m=login_error"); 
		exit;
	}
	if($password_new !== $password_new2) {
		
		header("Location:msg.php?m=pass_diff"); 
		exit;
	}
    else{
		$query_password = "UPDATE member_user set member_password = '{$password_new}' WHERE member_cell = '{$usercell}';";
	//echo $query;
		
	$db->query('set names UTF8'); 
	$res_password = $db->query($query_password);
	//$row = mysqli_fetch_array($res);
	//echo $row["member_password"];
	}
	
	
	
	if(!$res or !$res_password) {
		echo mysqli_error();
		die("数据库出错，请返回重试。");
	}
	//if(!isset($_SESSION))session_start();
    //$_SESSION=array();
    if (isset($_COOKIE["mosigecookie"])) {
      setcookie("mosigecookie",false);
	  setcookie("mosigecookie2", false);
    }
    session_destroy();
    //header('Location:index.php');
	header("Location:msg.php?m=reset_ok");
?>