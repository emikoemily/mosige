<?php
session_start();

echo $_GET['code'];
echo "s-".$_SESSION['code'];
if(isset($_GET['code'])) {

	if($_GET['code'] == $_SESSION['code']){
			
			if(!$_SESSION["adminuserid"])
			{
				header("Location:index.php");
			}
			
			include("functions.inc.php");
			include("sendmail.php");
			
			$regid=$_GET["regid"];
			$attdid=$_GET["attdid"];
			$mid=$_GET["mid"];
			$code=$_GET["code"];
			include("entity/TryRegister.php");
			include("entity/ClassArrange.php");
			echo "line1";
			
			
			TryRegister::cancelRegister($regid);
			echo "line2";
			TryRegister::addCancelReason($attdid, $mid,"管理员帮团购会员取消");
			echo "line3";
			ClassArrange::minusArrangeCount($attdid);
			echo "line4";
			$mailcontent="admin:{$_SESSION['username']}  为团购会员:{$mid} 取消arrid:{$attdid} regid:{$regid}";
			$mailcontent=$mailcontent."_{$code}";
			$maildebug->Subject=$mailcontent;
			$maildebug->Body=$mailcontent;
			$maildebug->send();
			
			header("Location:msg.php?m=update_success_manage_attend");

	}else{

		echo ‘请不要刷新本页面或重复提交表单！’;
		header("Location:manage_attend.php");
	}

}


?>