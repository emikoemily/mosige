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
 <header data-role="header"><h1>补课申请</h1></header>  
   <div data-role="content" class="content">  

<?php
	include(dirname(__FILE__).'/'."dbconf/settings.inc.php");
	include(dirname(__FILE__).'/'."dbconf/dbconnect.inc.php");	
	include("sendmail.php");
	
			if($_POST){
		
		$cname=$_POST['missandday'];
		$seldate=$_POST['seldate'];
	    
        $sql_addmiss = "UPDATE `yoga_lu`.`missed_table` set apply_date='{$seldate}',missed_status='submitted' WHERE member_id={$_SESSION['userid']} AND class_id={$cname};";
	   // echo $sql_addmiss;
		$db->query('set names UTF8'); 
		$res = $db->query($sql_addmiss);
				
 
		
 
	echo "申请已提交";
	// $leavetimec=mb_convert_encoding($seldate,'gb2312','utf-8');
	 //$cnamec=mb_convert_encoding($cname,'gb2312','utf-8');
	 $mail->Subject =$_SESSION['user_name']."申请补课".$cname;
	
	 $mail->Body=" 会员:".$_SESSION['user_name']. "   手机号:".$_SESSION['usercell']."申请补课：".$cname."时间".$seldate; 
	 $mail->send();
	}
	else{
		"未成功，请重新提交";
	}

?>
<a href="memberinfo.php" data-role="button" data-inline="false" data-ajax="false">返回</a>
</div>  
  
   <footer data-role="footer"> </footer>  
 
</div> 


</body>
</html>
