<?php

Class TryRegister{
	
	
	
	private static $dsn = "mysql:host=127.0.0.1;dbname=yoga_lu";
	private static $dbuser =	"yoga_lu";
	private static $dbpass = "Yooq_yoga_lu";
	
	
	
	
	public static function cancelRegister($regid){
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass); 
		$sql="UPDATE `yoga_lu`.`try_register_record` SET `is_canceled` ='1' where `register_id` ='{$regid}'";
	
		$db->exec($sql);
		$db=null;
		
	}
	public static function addCancelReason($arrid,$memid,$reason){
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql="INSERT INTO `yoga_lu`.`try_cancel_register` (`arrange_id`, `cancel_reason`,`member_id`) VALUES ('{$arrid}','{$reason}','{$memid}');";
		$db->exec("set names UTF8;");
		$db->exec($sql);
		$db=null;
	
	}
	
	public static function createRegister($memid, $arrid){
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql="INSERT into `yoga_lu`.`try_register_record` (arrange_id,member_id) values ('{$memid}','{$arrid}')";
	
		$db->exec($sql);
		$db=null;
	
	}
	public static function createRegisterForStoreGuest($memid, $arrid,$guestname){
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql="INSERT into `yoga_lu`.`try_register_record` (member_id,arrange_id,is_storeguest,guest_name) values ('{$memid}','{$arrid}',1,'{$guestname}')";
		$db->exec("set names UTF8;");
		$db->exec($sql);
		$sqladdcount = "UPDATE class_arrange SET `try_registercount` =`try_registercount`+1 WHERE `arrange_id`={$arrid};";
		 //echo $sqladdcount ;
		$db->exec($sqladdcount);
		
		
		$db=null;
	
	}
}

?>