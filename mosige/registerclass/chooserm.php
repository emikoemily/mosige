<?php 
session_start();
	if(!$_SESSION["usercell"]) 
   {
	   header("location:index.php");
  }
	include("header.inc.php");

	
?>

<body>  


<div data-role="page" id="regrm2">
<div data-role="header">
 <div data-role="navbar">
    <ul>
      <li><a href="register.php"  data-transition="flip" data-ajax="false">约课</a></li>
      <li><a href="registered.php"  data-ajax="false" >已选</a></li>
      <li><a href="attendhistory.php" data-transition="flip" data-ajax="false">历史/评价</a></li>
	  <li><a href="#" data-transition="flip" data-ajax="false" class="ui-btn-active ui-state-persist">跑步机</a></li>
	  <li><a href="memberinfo.php" data-transition="flip" data-ajax="false" >个人</a></li>
    </ul>
  </div>
</div>
<div data-role="content">

 
<?php
if($_POST)
	{ 
	 $rmhour= $_POST['rmhour'];
	 $rmmin= $_POST['rmmin'];
	}
	include(dirname(__FILE__).'/'."dbconf/settings.inc.php");
	include(dirname(__FILE__).'/'."dbconf/dbconnect.inc.php");
    include("sendmail.php");	
	$sql = "Select is_reg from running_arrange where start_hour='{$rmhour}'";
	//echo $sql;
	//$db->query('set names UTF8');  
	$res = $db->query($sql);
	$res->setFetchMode(PDO::FETCH_NUM);
	$row=$res->fetch();
	//echo $row[0];
    if($row[0]=='0'){
		$sql1 = "update running_arrange set is_reg =1,reg_mins={$rmmin},member_id ={$_SESSION['userid']} where start_hour='{$rmhour}'";
		$res1 = $db->exec($sql1);
		$sqladd = "insert into running_record (machine_id,running_starttime,member_id) values ('1','{$rmhour}',{$_SESSION['userid']})";
		//echo $sqladd;
		$resadd = $db->exec($sqladd);
		
		 echo "预约成功</br>";
		 $mail->Subject="{$_SESSION['user_name']} 约跑步机 at  {$rmhour} 点";
		 $mail->Body="{$_SESSION['user_name']} register running for {$rmmin}  start from {$rmhour}";
			$mail->send();
			
	}
	else{
	 echo "这个时间段已被预约，请选择其它时间";
	}

	
?>
<a href="registerrm.php" data-role="button" data-inline="false" data-transition="flip" data-ajax="false">返回</a>
</div>
</div> 
</body>  

</html>