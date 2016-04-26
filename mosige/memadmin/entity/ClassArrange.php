<?php
Class ClassArrange{
	
	
	
	private static $dsn = "mysql:host=127.0.0.1;dbname=yoga_lu";
	private static $dbuser =	"yoga_lu";
	private static $dbpass = "Yooq_yoga_lu";
	
	
	
	
	public static function minusArrangeCount($arrid){
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql="UPDATE class_arrange SET `try_registercount` =`try_registercount`-1 WHERE `arrange_id`='{$arrid}'";
	
		$db->exec($sql);
		$db=null;
	
	}
	
}

?>