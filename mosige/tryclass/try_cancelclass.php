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
 <header data-role="header"><h1>取消已选课</h1></header>  
   <div data-role="content" class="content">  

<?php
	include(dirname(__FILE__).'/'."dbconf/settings.inc.php");
	include(dirname(__FILE__).'/'."dbconf/dbconnect.inc.php");
	include(dirname(__FILE__).'/'."func/function.php");
	include("sendmail.php");
	if($_POST )
	{ 

 // echo $_POST['classcancel'];
  //echo $_POST['classcancelcount'];
	if($_POST['classcancel'])
	{
		$choices = $_POST['classcancel']; 
		$classcount = $_POST['classcancelcount-'.$choices];
		$classtype = $_POST['classtype-'.$choices];
		$pid =  $_POST['packageid-'.$choices];
		$classstartdate = $_POST['classstartdate-'.$choices];
		$starttime = $_POST['classstarttime-'.$choices];
		$reason = $_POST['cancelreason'];
	
	if(date('Y-m-d')>$classstartdate ){
		
		echo "课程已开始，无法取消";
	}
	elseif(date('Y-m-d')==$classstartdate AND date('H:i:s')>=$starttime){
		echo "现在时间:".date('H:i:s'); 
		$now=date('H:i:s');
		 
		echo " </br>   课程开始时间:".$starttime."</br>";
         echo "课程已开始，无法取消";
			}
	elseif(date('Y-m-d')==$classstartdate AND date('H:i:s',strtotime('+30 minutes'))>=$starttime){//need fix
		 echo "现在时间:".date('H:i:s'); 
		$now=date('H:i:s');
		 
		echo " </br>   课程开始时间:".$starttime."</br>";
			echo "  马上就要上课啦，不能取消了.";
		}			
	else{
			
		 
			$sql = "UPDATE try_register_record SET `is_canceled` =1 WHERE try_register_record.register_id= {$choices}";
			$sqlminuscount = "UPDATE class_arrange SET `try_registercount` =`try_registercount`-1 WHERE `arrange_id`={$classcount} AND `try_registercount`>0;";
			$sqladdreason ="INSERT INTO `yoga_lu`.`try_cancel_register` (`arrange_id`, `cancel_reason`,`member_id`) VALUES ('{$classcount}','{$reason}','{$_SESSION['userid']}');";
			if($classtype=='package' or $classtype=='set'){
					$sqlUpdateStart="UPDATE try_package_subscribe SET package_register=0 WHERE package_id='{$pids}' AND member_id = '{$_SESSION['userid']}';";
				//	echo $sqlUpdateStart;
					$resUpdateStart = $db->exec($sqlUpdateStart);	
					//$row3= mysqli_affected_rows();
				
			}
			
			//echo $sql;
			//echo $sqlminuscount;
			//echo $sqladdreason ;
			$res = $db->exec($sql);
			$res2 = $db->exec($sqlminuscount);
			//$db->exec('set names UTF8'); 
			$res3 = $db->exec($sqladdreason);
			echo "成功取消";
			//$reasonc=mb_convert_encoding($reason,'gb2312','utf-8');
			if($_SESSION["userlevel"]=="package"  or $_SESSION["userlevel"]=="both"){
							//$_SESSION["count_overmax"]=0;
					$_SESSION[$pid]=0;		
                  // echo "session:{$pid}".$_SESSION[$pid];
			//echo $_SESSION["count_overmax"];
			}
		/*	if($_SESSION['userlevel']=='both_count'){
					checkcount(2);
				}elseif($_SESSION['userlevel']=='tiyan_1'){
					checkcount(1);
				}*/
				setbylevel();
				function getarrinfo($db,$arrid){
				$sqlga="SELECT arrangedate,starttime  FROM class_arrange WHERE arrange_id  = {$arrid}";
				//echo $sqlgp;
				$db->query('set names UTF8');
				$return='';
				$resga=$db->query($sqlga);
				$resga->setFetchMode(PDO::FETCH_ASSOC);
				$rows=$resga->fetchAll();
					foreach($rows as $rowga){
						$return=$return.$rowga['arrangedate']."  ".$rowga['starttime'];
					}
					return $return;
				}
			$mail->Subject="团购:{$_SESSION['user_name']} 取消：".getarrinfo($db,$classcount);
			$mail->Body="团购:{$_SESSION['user_name']} 取消：".getarrinfo($db,$classcount)."理由：{$reason}";
			$mail->send();
			
			}
       // }
	
	}
	else {echo "没有选择课程";}
	}
	
	else{
	echo "没有选择课程";
}
?>
<a href="try_registered.php" data-role="button" data-inline="false" data-ajax="false">返回</a>
</div>  
  
   <footer data-role="footer"> </footer>  
 
</div> 


</body>
</html>
