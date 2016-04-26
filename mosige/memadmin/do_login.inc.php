<?php
	session_start();
	print_r(error_get_last());
	print_r(debug_backtrace());
	include("dbconnect.inc.php");
	include("functions.inc.php");
	$form = check_form($_POST["edit"]);
	$username = $form["name"];
	//$password = md5($form["pass"]);
	$password = $form["pass"];
	$sql = "select  *  from users where username='{$username}'  limit 1 ";
	$res = $db->query($sql);
	$row = $res->fetch();
	if($password != $row["password"]) {
		header("Location:msg.php?m=login_error");
		exit;
	}
	$lifeTime = 6000; 
    session_set_cookie_params($lifeTime);  
   // session.setMaxInactiveInterval(48 * 3600);
	$_SESSION["adminuserid"] = $row["level"];
	$_SESSION["username"] = $username;
	$_SESSION["user_id"]=$row["id"];
	echo $_SESSION["adminuserid"];
	//if($row["id"]==2){
	if($_SESSION["adminuserid"]==1){
	//require("do_qiandao_all.php");
	
	echo	$_SESSION["adminuserid"];
	echo $_SESSION["username"];
		
	header("Location:do_qiandao_all.php");
		}
	else{
		header("Location:view_account.php");
		
	}
	
	//
?>