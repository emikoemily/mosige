<?php

class Payment{
	
	private static $dsn = "mysql:host=127.0.0.1;dbname=yoga_lu";
	private static $dbuser =	"yoga_lu";
	private static $dbpass = "Yooq_yoga_lu";
	
	
	
	public static function getAllPaymentPrice($memid){
			
	
		//$dsn = "mysql:host=127.0.0.1;dbname=yoga_lu";
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql = "SELECT Payment_id,payment_days,payment_startdate,payment_enddate,payment_price,payment_discount from payment_table where member_id='{$memid}'";
		$db->query("set names UTF8;");
		$rs = $db->query($sql);
		$db=null;
			
		return $rs->fetchAll();
			
			
	}
	
	
	
	public static function addaccounting($value,$memid,$reason){
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql="INSERT INTO `yoga_lu`.`member_accounting` (`member_id`,`payment_value`,`account_category`) values ({$memid},{$value},'{$reason}');";
		// echo $sql;
		$db->exec("set names UTF8;");
		$rs = $db->exec($sql);
		$db=null;
		 
	}
	
	public static function addaccountingByName($value,$memname,$reason){
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql="INSERT INTO `yoga_lu`.`member_accounting` (`member_name`,`payment_value`,`account_category`) values ('{$memname}',{$value},'{$reason}');";
		//echo $sql;
		$db->exec("set names UTF8;");
		$rs = $db->exec($sql);
		$db=null;
			
	}
	
	public static function caculateTimeRange(){
		
		
	}
	public static function updateTimeRange(){
	
	
	}
	public static function removePackagesub($memid,$pid){
	
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql="DELETE FROM `yoga_lu`.`package_subscribe` where member_id={$memid} and package_id={$pid}  ;";
		
		// echo $sql;
		//$db->exec("set names UTF8;");
		$rs = $db->exec($sql);
		$db=null;
	}
	 
	
	
	public static function getSinglePakcageDays($pid){
		
		$rate=10;
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql = "SELECT package_course_count from package_design where package_id='{$pid}'";		
		$rs = $db->query($sql);
		$db=null;		
		$pcount=$rs->fetch();
		$days= ceil($pcounts[0][0]/$rate*30.5+($pcounts[0][0]/$rate)*4);
		return $days;
	}
	
	
	public static function getPaymentIDofPackage($memid,$pid){
		
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql = "SELECT package_subscribe from payment_table where member_id={$memid} and package_id='{$pid}'";
		$rs = $db->query($sql);
		$db=null;
		$payid=$rs->fetch();
		 
		return $payid[0][0];
	}
	
	
	public static function addDays($pid,$days,$payid){
	
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql="UPDATE payment_days=payment_days+$days FROM `yoga_lu`.`payment_table` where member_id={$memid} and package_id={$pid}  ;";
	
		// echo $sql;
		//$db->exec("set names UTF8;");
		$rs = $db->exec($sql);
		$db=null;
	}
	
	public static function removeDays($pid,$days,$payid){
	
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql="UPDATE payment_days=payment_days-$days FROM `yoga_lu`.`payment_table` where member_id={$memid} and package_id={$pid}  ;";
	
		// echo $sql;
		//$db->exec("set names UTF8;");
		$rs = $db->exec($sql);
		$db=null;
	}
	
	
}



?>