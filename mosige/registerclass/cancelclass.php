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
	include("sendmail.php");
	if($_POST )
	{ 


		if($_POST['classcancel'])
		{
			$choices = $_POST['classcancel']; 
			$classcount = $_POST['classcancelcount-'.$choices];
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
			elseif(date('Y-m-d')==$classstartdate AND date('H:i:s',strtotime('+15 minutes'))>=$starttime){//need fix
				echo "现在时间:".date('H:i:s'); 
				$now=date('H:i:s');
				echo " </br>   课程开始时间:".$starttime."</br>";
					echo "  马上就要上课啦，不能取消了.";
			}	 	
			else{
		 
				$sql = "UPDATE register_record SET `is_canceled` =1 WHERE register_record.register_id= {$choices}";
				$sqlminuscount = "UPDATE class_arrange SET `registercount` =`registercount`-1 WHERE `arrange_id`={$classcount} AND `registercount`>0 ;";
				$sqladdreason ="INSERT INTO `yoga_lu`.`cancel_register` (`arrange_id`, `cancel_reason`,`member_id`) VALUES ('{$classcount}','{$reason}','{$_SESSION['userid']}');";
				//echo $sql;
				//echo $sqlminuscount;
				//echo $sqladdreason ;
				$db->exec('set names UTF8');
				$res = $db->exec($sql);
				$res2 = $db->exec($sqlminuscount);
				$res3 = $db->exec($sqladdreason);
				echo "成功取消";
				
				$sqldebugcount="select registercount,try_registercount from class_arrange where `arrange_id`={$classcount}";
					$resdebugcount = $db->query($sqldebugcount);
					$resdebugcount->setFetchMode(PDO::FETCH_ASSOC);
					$rowdebugcount=$resdebugcount->fetch();
					 
				
				$maildebug->Subject="Update reg count for cancel.";
							$maildebug->Body="Update reg count.Affect：Debug:sql:".$sqladdcount."sqlminuscount：".$sql."The register count：".$rowdebugcount['registercount']." Try register count:".$rowdebugcount['try_registercount'] ;
							$maildebug->send();
				//$reasonc=mb_convert_encoding($reason,'gb2312','utf-8');
				function getpackageinfo($pid){
				$sqlgp="SELECT package_name  FROM package_design WHERE package_design.package_id  = '{$pid}'";
				//echo $sqlgp;
				$db->query('set names UTF8');
				$return='';
				$resgp=$db->query($sqlgp);
				$rowgps=$resgp->fetchAll();
				
					foreach($rowgp as $rowgp){
						$return=$return.$rowgp['package_name'];
					}
					return $return;
				}
				
				function getarrinfo($db,$arrid){
				$sqlga="SELECT class_name,inner_id,arrangedate,starttime  FROM class_arrange inner join class_design
				on class_arrange.class_id=class_design.class_id WHERE arrange_id  = {$arrid}";
				//echo $sqlgp;
				$db->query('set names UTF8');
				$return='';
				$resga=$db->query($sqlga);
				$resga->setFetchMode(PDO::FETCH_ASSOC);
				$rows=$resga->fetchAll();
					foreach($rows as $rowga){
						$return=$return.$rowga['arrangedate']."  ".$rowga['starttime'].$rowga['class_name'].$rowga['inner_id'];
					}
					return $return;
				}
				$mail->Subject="{$_SESSION['user_name']} 取消预约". getarrinfo($db,$classcount) ."理由={$reason}" ;
				$mail->Body="{$_SESSION['user_name']} 取消 regid ={$choices}". getarrinfo($db,$classcount) ."理由={$reason}" ;
				$mail->send();
				if($_SESSION['userlevel']=='both_count'){
					$sql_2ci="SELECT COUNT(*) FROM `register_record` WHERE member_id={$_SESSION["userid"]} AND is_canceled!=1";
					
					$res_2ci=$db->query($sql_2ci);
					$res_2ci->setFetchMode(PDO::FETCH_NUM);
					$rs=$res_2ci->fetch();
					
						if($rs[0]>=2){
							$_SESSION["tiyanover"]=1;}
						else{
							$_SESSION["tiyanover"]=0;
						}
			
				
				}
			
			}
	
	}
	else {echo "没有选择课程";}
	}
	
	else{
	echo "没有选择课程";
}
?>
<a href="registered.php" data-role="button" data-inline="false" data-ajax="false">返回</a>
</div>  
  
   <footer data-role="footer"> </footer>  
 
</div> 


</body>
</html>
