<?php 
session_start();
	if(!$_SESSION["usercell"]) 
   {
	   header("location:index.php");
  }
	include("header.inc.php");

?>
<body>  


<div data-role="dialog">  
   <header data-role="header"><h1>约课确认</h1></header>  
   <div data-role="content" class="content">  
      
	<?php
	include(dirname(__FILE__).'/'."dbconf/settings.inc.php");
	include(dirname(__FILE__).'/'."dbconf/dbconnect.inc.php");

 	include("sendmail.php");
	$result=0;
	$rate=3;//This is for available time range. class count/rate x 7 + days +(31days for missed class)
	if( $_POST ){ 
		$choices = $_POST['classchoice']; 
		$pids = $_POST['classpid-'.$choices];
		$pcounts= $_POST['classpcount-'.$choices];
	    $classtype=$_POST['classtype-'.$choices];
	
	      if($_POST['classchoice']){
           
			$sql = "INSERT INTO `yoga_lu`.`try_register_record` (`arrange_id`, `member_id`) select " .$choices.",{$_SESSION['userid']} from dual where not exists (select * from try_register_record 
				where arrange_id={$choices} AND is_canceled=0 AND member_id={$_SESSION['userid']});";	
			$row = $db->exec($sql);
			
			if($row==0){
				//$db->query("ROLLBACK");
				echo '已预约过这门课了</br>';
				//echo $row;
				//echo $row2;
			}
			else {
				//$db->query("BEGIN"); 
				$sqladdcount = "UPDATE class_arrange SET `try_registercount` =`try_registercount`+1 WHERE `try_registercount`<`try_maxposition` AND `arrange_id`={$choices};";
				
				$row2=$db->exec($sqladdcount);
				 
			   if($row2==0)  {
					//$db->query("ROLLBACK");
					echo '手慢了，好可惜，没有抢到最后一个空位</br>';
					//echo $row;
					//echo $row2;
			   }
				else{
					//开始开卡
					$start=(date('Y-m-d H:i:s'));
			 
					if($classtype=='package' or $classtype=='set'){
						$sqlUpdateStart="UPDATE try_package_subscribe SET package_startdate = '{$start}',package_register=1,package_enddate=date_add('{$start}', interval try_days day) WHERE (package_enddate is NULL or package_enddate='0000-00-00 00:00:00') AND package_id='{$pids}' AND member_id = '{$_SESSION['userid']}';";
						//echo $sqlUpdateStart;
						$row3 = $db->exec($sqlUpdateStart);	
					 
				
					}
					elseif($classtype=='common'  ){
						$sqlUpdateStart="UPDATE try_member_user SET member_startdate = '{$start}',member_enddate= date_add('{$start}', interval member_days day) WHERE (try_member_user.member_enddate is NULL or try_member_user.member_enddate ='0000-00-00 00:00:00') AND try_member_user.member_id ={$_SESSION['userid']};";
						//echo $sqlUpdateStart;
						$row3 = $db->exec($sqlUpdateStart);	
				 
					}

					if($row!=0&&$row2!=0){
					//$db->query("COMMIT");
				
					echo '预约成功。</br>';
					if($_SESSION["userlevel"]=="package" or $_SESSION["userlevel"]=="both" ){
						
					$_SESSION[$pids]=1;
				
					}
				function checkcount($db,$count){
		
					$sql_2ci="SELECT COUNT(*) FROM `try_register_record` WHERE member_id={$_SESSION["userid"]} AND is_canceled!=1";
					$res_2ci->setFetchMode(PDO::FETCH_num);
					$res_2ci=$db->query($sql_2ci);
					$rs=$res_2ci->fetch();
					if($rs[0]>=$count){
						$_SESSION["tiyanover"]=1;
							}
					else{
						$_SESSION["tiyanover"]=0;
					}
			
					
				}
				
				
					if($_SESSION["userlevel"]=='both_count'){
								checkcount($db,2);
							} 
					elseif($_SESSION["userlevel"]=='tiyan_1'){
								checkcount($db,1);
							} 
					elseif($_SESSION["userlevel"]=='tiyan_3'){
								checkcount($db,3);
							}
							
				function getpackageinfo($db,$pid){
								$sqlgp="SELECT DISTINCT class_name  FROM class_design WHERE class_design.package_id  = '{$pid}'";
								//echo $sqlgp;
								$db->query('set names UTF8');
								$return='';
								$resgp=$db->query($sqlgp);
								$resgp->setFetchMode(PDO::FETCH_ASSOC);
								$rows = $resgp->fetchAll();
							
								foreach($rows as $rowgp){
									$return=$return.$rowgp['class_name'];
								}
								return $return;
							}
				
				function getarrinfo($db,$arrid){
					$sqlga="SELECT arrangedate,starttime  FROM class_arrange WHERE arrange_id  = {$arrid}";
					//echo $sqlgp;
					$db->query('set names UTF8');
					$return='';
					$resga=$db->query($sqlga);
					$resga->setFetchMode(PDO::FETCH_ASSOC);
					$rows = $resga->fetchAll();
					
					foreach($rows as $rowga ){
						$return=$return.$rowga['arrangedate']."  ".$rowga['starttime'];
					}
					return $return;
				}
				    $mail->Subject="团购:{$_SESSION['user_name']} 预约：".getpackageinfo($db,$pids)."  ".getarrinfo($db,$choices);
            	    $mail->Body="团购:{$_SESSION['user_name']}  预约：".getpackageinfo($db,$pids)."  ".getarrinfo($db,$choices);
            	    $mail->send();
			     }

			  }
			}
			 
		  }
		  else{
				echo "没有选择课程";
			}
     }
	
else{
	echo "没有选择课程";
}
?>  
   </div>
    <?php
	
	?>
	<a href='try_register.php' data-role='button' data-inline='false' data-ajax='false'>返回</a>
   <footer data-role="footer"><h4></h4></footer>  
</div>  
</body>  

</html>