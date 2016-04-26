<?php
	session_start();
	include("dbconnect.inc.php");
	include("functions.inc.php");
	#如果php配置中，magic_quotes_gpc没有被设置，则执行过滤字符串。
	//$form = check_form($_POST["edit"]);
	echo "如果不打算修改则不要改动输入框里的默认值</br>";
	if($_GET){
		$newendday=$_GET["newenddate"];
		$payid=$_GET["paymentid"];
		$days=$_GET["newdays"];
		$sql = "UPDATE payment_table SET `payment_enddate` ='{$newendday}',`payment_days`={$days}  WHERE `payment_table`.`payment_id`= '{$payid}';";
			//echo $sql;	
				$res = $db->exec($sql);
			

	
	}
	
	header("Location:msg.php?m=update_enddate_success");
?>