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
 <header data-role="header"><h1>请假</h1></header>  
   <div data-role="content" class="content">  

<?php
 	
	include "entity/Leave.php";
	include("sendmail.php");
	 	
	if($_POST){
		
		$leavestart=$_POST['leavestart'];
		$leaveend=$_POST['leaveend'];
		$leavereason=$_POST['leavereason'];
	}
	echo "申请请假:";
	echo $leavestart;
	echo "-";
	echo $leaveend;
	//echo $leavereason;
	$request=strtotime($leaveend)-strtotime($leavestart);
	// echo $request;
	 
	$requestdays= ceil($request/60/60/24)+1;
	echo ",";
	echo $requestdays;
	echo "天。";
	
	if($_SESSION['leavecount']<=0){
		
		echo "请假次数已用完，不能再请了";
		
	}
	elseif($request<0){
		echo "申请起始日期不能晚于截止日期";
	}
	elseif($_SESSION['leavedays']<=0){
	
		echo "请天数已用完，不能再请了";
	
	}
	elseif($_SESSION['leavedays']<$requestdays){
		$leaveend=date('Y-m-d',strtotime("{$leavestart}"."+"."{$_SESSION['leavedays']} days"));
		
		echo "剩下可用天数是 ".$_SESSION['leavedays']."天,最多可请假到 ".$leaveend;
		echo "</br>请假申请已提交。";
		
		Leave::applyLeaveRequest($_SESSION['userid'], $leavestart,$leaveend, $leavereason);
		 
	 	
		$_SESSION[isleave]=2;
		
		$mail->Subject =$_SESSION['user_name']."申请请假，从".$leavestart."到".$leaveend;
		$leavereasonc =mb_convert_encoding($leavereason,'gb2312','utf-8');
		$mail->Body=" 会员:".$_SESSION['user_name']. "   手机号:".$_SESSION['usercell']."请假申请：从".$leavestart."到".$leaveend." 理由:".$leavereasonc."。假期时间已使用完";
		$mail->send();
	 
	
	}
	else{
		Leave::applyLeaveRequest($_SESSION['userid'], $leavestart,$leaveend, $leavereason);		
		
		
	 	$_SESSION[isleave]=2;
	 
	 	
	 	echo "请假已提交";
	 	$mail->Subject =$_SESSION['user_name']."申请请假";
	 	$leavereasonc=mb_convert_encoding($leavereason,'gb2312','utf-8');
	 	$mail->Body=" 会员:".$_SESSION['user_name']. "   手机号:".$_SESSION['usercell']."请假申请：从".$leavestart."到".$leaveend." 理由:".$leavereasonc;
	 	$mail->send();
	}
	

?>
<a href="memberinfo.php" data-role="button" data-inline="false" data-ajax="false">返回</a>
</div>  
  
   <footer data-role="footer"> </footer>  
 
</div> 


</body>
</html>
