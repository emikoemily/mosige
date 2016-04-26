<?php 
//header('content-type:text/html; charset=gb2312');
session_start();
	if(!$_SESSION["usercell"]) 
   {
	   header("location:index.php");
  }
	include("header.inc.php");

	
?>
<body>  


<div data-role="dialog">  
   <header data-role="header"><h1>申请补课</h1></header>  
   <div data-role="content" class="content">  
      
	<?php
	//header("Content-Type:text/html;charset=GB2312");
   include(dirname(__FILE__).'/'."dbconf/settings.inc.php");
	include(dirname(__FILE__).'/'."dbconf/dbconnect.inc.php");	
	//include(dirname(__FILE__).'/'."mail/sendmail.php");
	if( $_POST ){ 	
	    $missclassid=Array();
		$missclassid = $_POST['applyaddclassids']; 
		//$missclassnames = $_POST['missclassnames'];
		$applyaddclasstext = $_POST['applyaddclasstext'];
		echo "你的申请已提交，稍后会和你联系具体安排";
		 $applyaddclasstext=mb_convert_encoding($applyaddclasstext,'gb2312','utf-8'); 
	
		$where="where class_id=0";
		foreach ($missclassid as $eachdiff ){
		 
		//echo "$eachdiff <br />";
		$sql = "INSERT INTO `mos_ige`.`apply_missed` (`member_id`, `class_id`,`apply_accepted`) VALUES ({$_SESSION['userid']}, '{$eachdiff}','submitted');";
	    //echo $sql;
		$db->query('set names UTF8'); 
		$res = $db->query($sql);
		$where=$where. " or class_id = " .$eachdiff;
			 
	}
		
	    $sql1="SELECT class_name,class_description from class_design ";
		$sql1=$sql1.$where.";";
		//echo $sql1;
		$db->query('set names UTF8');   
		$res1 = $db->query($sql1);
		$missclassnames="";
		while($row1 = mysqli_fetch_array($res1)){		
	 
		 	$missclassnames =$missclassnames.$row1['class_name'].$row1['class_description'];
			//$missid[]=$row1['class_id'];
			//$missname[]=$row1['class_name'];
			} 
		 //echo $missclassnames;
	/*	 $mail->Subject =$_SESSION['user_name']."申请补课";
	     $mail->Body=" 会员:".$_SESSION['user_name']. "   手机号1:".$_SESSION['usercell']."     申请补课 ".$missclassnames."      申请内容：".$applyaddclasstext;
		 $mail->send();*/
			
}

?>  
   </div>
	<a href="memberinfo.php#missclass" data-role="button" data-ajax="false">返回</a>   
   <footer data-role="footer"><h4></h4></footer>  
</div>  
</body>  

</html>