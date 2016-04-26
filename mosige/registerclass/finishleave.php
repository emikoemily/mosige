<?php 
session_start();
	if(!$_SESSION["usercell"]) 
   {
	   header("location:index.php");
  }
	include("header.inc.php");

	
?>
<body>
<div data-role="dialog" id="submitcancel" data-title="取消">
 <header data-role="header"><h1>结束请假</h1></header>  
   <div data-role="content" class="content">  

<?php
 	
	//include(dirname(__FILE__).'/'."dbconf/settings.inc.php");
	//include(dirname(__FILE__).'/'."dbconf/dbconnect.inc.php");
 
	include("sendmail.php");
	include("entity/Leave.php");
	
	$_SESSION["isleave"]=0;
	$requestdays=Leave::getRequestDays($_SESSION["userid"]);
	$actualdays=Leave::getDaysCompareToday($_SESSION["userid"]);
	if($requestdays<$actualdays){
		
		$mail->Subject =$_SESSION['user_name']."结束请假出错";
		$mail->Body=" 会员:".$_SESSION['user_name']. "   手机号:".$_SESSION['usercell']."request:".$requestdays." actual:".$actualdays;
		$mail->send();
		
	}
	
	
	Leave::reduceLeaveDays($actualdays,$_SESSION['userid']);
	Leave::updateEndDateToToday($_SESSION['userid']);
	Leave::reduceLeaveCounts($_SESSION['userid']);
	$_SESSION['leavecount']=$_SESSION['leavecount']-1;
	$_SESSION["leavedays"]=$_SESSION["leavedays"]-$actualdays;
	Leave::endLeave($_SESSION['userid']);
	Leave::extendLeaveDaysToMem($actualdays, $_SESSION['userid']);
	
	
	
	
	echo "本次请假已结束.剩余请假天数:".$_SESSION["leavedays"]."实际请假天数:".$actualdays;
	$mail->Subject =$_SESSION['user_name']."结束请假,剩余请假天数:".$_SESSION["leavedays"]."实际请假天数:".$actualdays."剩余次数：".$_SESSION['leavecount'];
	$mail->Body=" 会员:".$_SESSION['user_name']. "   手机号:".$_SESSION['usercell'];      
	$mail->send();
	


?>
<a href="memberinfo.php" data-role="button" data-inline="false" data-ajax="false">返回</a>
</div>  
  
   <footer data-role="footer"> </footer>  
 
</div> 


</body>
</html>
