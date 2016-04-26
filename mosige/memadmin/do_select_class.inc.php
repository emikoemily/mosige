<?php
	session_start();
	include("dbconnect.inc.php");
	include("functions.inc.php");
	#如果php配置中，magic_quotes_gpc没有被设置，则执行过滤字符串。
	$dt=date('Ymd');
	$rate=10;//This is for available time range. class count/rate x 7 + days +(31days for missed class)
	if( $_POST ){ 
		 $choices = $_POST['t1']; 
		 $mid = $_POST['mid'];
		 $setcount=Array();
		 $has_set1=0;
		 $has_set2=0;
		 $has_set3=0;
		 for($i=0;$i<count($choices);$i++){
		   if(substr($choices[$i],0,4)=='set1'){
			$sqladdsub ="INSERT INTO `yoga_lu`.`package_subscribe` (`member_id`,`package_id`,`payment_id`) VALUES ('{$mid}','$choices[$i]','{$mid}{$dt}_set1');";   
		    $has_set1=1;
		   }
		   elseif(substr($choices[$i],0,4)=='set2'){
			$sqladdsub ="INSERT INTO `yoga_lu`.`package_subscribe` (`member_id`,`package_id`,`payment_id`) VALUES ('{$mid}','$choices[$i]','{$mid}{$dt}_set2');";   
		    $has_set2=1; 
		   }
		   elseif(substr($choices[$i],0,4)=='set3'){
			$sqladdsub ="INSERT INTO `yoga_lu`.`package_subscribe` (`member_id`,`package_id`,`payment_id`) VALUES ('{$mid}','$choices[$i]','{$mid}{$dt}_set3');";   
		    $has_set3=1;
		   } 
		   
		   else{
           $sqladdsub ="INSERT INTO `yoga_lu`.`package_subscribe` (`member_id`,`package_id`,`payment_id`) VALUES ('{$mid}','$choices[$i]','{$mid}{$dt}');";
		   }
	       $res = $db->exec($sqladdsub);
           $db->exec('set names UTF8'); 		   
	     }
		//创建payment
		$sqladdpayment="INSERT INTO `yoga_lu`.`payment_table` (`payment_id`) VALUES ('{$mid}{$dt}')";
		echo $sqladdpayment;
		 //$sqladdpayment="INSERT INTO `yoga_lu`.`payment_table` (`payment_id`) VALUES ({$mid}-".{date('Y-m-d')}).";";
		$resaddpayment = $db->exec($sqladdpayment);	
        //计算所选课程包的课程数总数		
		$sqlgetdays="SELECT SUM(package_course_count) as total_days FROM `package_design` inner join `package_subscribe` WHERE package_design.package_id=package_subscribe.package_id and package_subscribe.payment_id='{$mid}{$dt}' AND package_subscribe.package_id not like 'set%'";
		echo $sqlgetdays;
		$resday=$db->query($sqlgetdays);
		$resday->setFetchMode(PDO::FETCH_NUM);
		$rowday=$resday->fetch();
		if ($rowday[0]>=0)
        {	$pcounts=$rowday[0];
			echo $rowday[0];
	    }
		$a=ceil($pcounts/$rate);
		//计算有效期天数
		$resgetdays=ceil($pcounts/$rate*30.5+($pcounts/$rate)*4);
		//为每个payment添加有效期天数
		$sqlsetdays="UPDATE `yoga_lu`.`payment_table` SET `payment_days` ='{$resgetdays}' where `payment_id` ='{$mid}{$dt}' AND package_subscribe.package_id not like 'set%'";
	    echo $sqlsetdays;		 
		$ressetdays = $db->exec($sqlsetdays);
		echo $ressetdays;
		
		
		
		if($has_set1==1)//空中10次，有效期一年
		{
		$sqlsetdays_set1="UPDATE `yoga_lu`.`payment_table` SET `payment_days` =365 where `payment_id` ='{$mid}{$dt}_set1'";
	    echo $sqlsetdays_set1;		 
		$ressetdays_set1 = $db->exec($sqlsetdays_set1);
		echo $ressetdays_set1;
		}
		if($has_set2==1)//儿童瑜伽10次，有效期三个月
		{
		$sqlsetdays_set2="UPDATE `yoga_lu`.`payment_table` SET `payment_days` =93 where `payment_id` ='{$mid}{$dt}_set2'";
	    echo $sqlsetdays_set2;		 
		$ressetdays_set2 = $db->exec($sqlsetdays_set2);
		echo $ressetdays_set2;
		}
		if($has_set3==1)//儿童肚皮舞10次，有效期四个月
		{
		$sqlsetdays_set3="UPDATE `yoga_lu`.`payment_table` SET `payment_days` =124 where `payment_id` ='{$mid}{$dt}_set3'";
	    echo $sqlsetdays_set3;		 
		$ressetdays_set3 = $db->exec($sqlsetdays_set3);
		echo $ressetdays_set3;
		}
	 
		
	}

	
	
	 
	header("Location:msg.php?m=update_success_select_class");
?>