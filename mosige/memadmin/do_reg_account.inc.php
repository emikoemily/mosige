<?php
	//include("dbconnect.inc.php");
	include ("entity/Member.php");
	include ("entity/Cardrule.php");
	include ("entity/Payment.php");
	include("functions.inc.php");
	date_default_timezone_set('PRC');
	#如果php配置中，magic_quotes_gpc没有被设置，则执行过滤字符串。
	$form = check_form($_POST["edit"]);	
	$form["pass"] = md5($form["pass"]);
	extract($form);	 

	 
	
		$rule=new Cardrule($type);
		//echo $payment;
		$member=new Member($name,$pass,$mail,$sex,$tel,$birthday,$point,$intro,$cardid,$rule,$channel,$payment);
		 
		$member->createMemberInDB();
		echo $rule->has_kongzhong;
		if($rule->has_kongzhong==1){
			
			$member->subKongZhong();
			
		}
		if($rule->has_ertong==1){
				
			$member->subErTongYoga();
				
		}
		if($payment>0){Payment::addaccountingByName($payment,$name,"注册时录入金额");}
		
		echo $member->memid;
		Member::saveAdditionalDays($member->memid, $additionaldays);
		//print_r(error_get_last());
	//print_r(debug_backtrace());
	

	header("Location:msg.php?m=register_success");
?>