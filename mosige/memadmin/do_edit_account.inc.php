<?php
	session_start();
	include("dbconnect.inc.php");
	include("functions.inc.php");
	 include("sendmail.php");
	 include("entity/Member.php");
	#如果php配置中，magic_quotes_gpc没有被设置，则执行过滤字符串。
	$form = check_form($_POST["edit"]);
	//extract($form);
	$id="";
	if($_POST["id"]!="" && $_SESSION["adminuserid"]==1&& is_numeric($_POST["id"])) {
		$id = $_POST["id"];
	}else {
		$id = $_SESSION["adminuserid"];
	}
	
	#这里{}符号是代表在字符串中引用当前环境的变量member_name,member_password,
	#member_cell,member_level,member_birthday,member_points,member_intro,member_regtime,member_startdate,member_enddate
	
	Member::updateMemberbyID($id,$form);	
		
	
		//$sql .= "member_password='{$pass}', ";
	 
	/*$mailcontent="admin:{$_SESSION['username']} edit user  ";
							//$mailcontent=$mailcontent.getpackageinfo($pids);
							$mailcontent=$mailcontent."sql={$sql}";
							$mail->Subject="admin:{$_SESSION['username']} edit user  ";
							$mail->Body=$mailcontent;
							$mail->send();*/
	
	header("Location:msg.php?m=update_success_edit_account");
?>