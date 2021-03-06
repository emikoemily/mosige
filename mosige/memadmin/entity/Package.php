<?php
Class Package{
	
	private static $dsn = "mysql:host=127.0.0.1;dbname=yoga_lu";
	private static $dbuser =	"yoga_lu";
	private static $dbpass = "Yooq_yoga_lu";
	
	
	
	
	
	public static function listMemberPerPackage($pid){
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
	
	
		$sqladdsub ="SELECT package_name,member_name,package_subscribe.member_id  FROM `package_subscribe` inner join member_user on `package_subscribe`.member_id = member_user.member_id 
		inner join package_design on package_subscribe.package_id=package_design.package_id 
		WHERE package_subscribe.`package_id` LIKE '{$pid}'  AND  member_user.member_id!=73   AND  member_user.member_id!=60   AND  member_user.member_id!=48   AND  member_user.member_id!=61 AND  member_user.member_id!=329
 AND  member_user.member_id!=74  AND  member_user.member_id!=76  AND  member_user.member_id!=48  AND  member_user.member_id!=102  AND  member_user.member_id!=349  AND  member_user.member_id!=367
		order by member_name;";
			
		$db->query("set names UTF8;");
		$rs = $db->query($sqladdsub);
		$db=null;
		$rs->setFetchMode(PDO::FETCH_ASSOC);
		return $rs->fetchAll();
	}
	
	
	
	public static function findprogress($mid,$pid){
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sqlprogress="SELECT distinct class_arrange.class_id, class_name,class_description
		FROM class_arrange
		inner join yoga_lu.register_record ON register_record.arrange_id = class_arrange.arrange_id
		inner join class_design ON class_design.class_id=class_arrange.class_id
		where
		is_attended!=0
		AND
		class_design.package_id='{$pid}'
		AND
		register_record.member_id='{$mid}' Order by class_description
		";
		//echo $sqlprogress;
		$db->query("set names UTF8;");
		$rs = $db->query($sqlprogress);
		$db=null;
		$rs->setFetchMode(PDO::FETCH_ASSOC);
		 
		return $rs->fetchAll();
	
	}
	
	
	
	
	
	public static function endpackage($mid,$pid){
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sqlend="UPDATE package_subscribe set is_finished=1 where is_finished!=1 
		AND
		package_id='{$pid}'
		AND
		member_id='{$mid}' limit 1
		";
		//echo $sqlprogress;
	 
		$db->exec($sqlend);
		$db=null;
		 
	}
	
	
	public static function addpackageMAX($num,$pid,$mid){
		echo "test";
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		echo "bye";
		$sqlend="UPDATE package_subscribe set set_max=set_max+{$num} where is_finished!=1
		AND
		package_id='{$pid}'
		AND
		member_id='{$mid}' limit 1
		";
	//	echo $sqlend;
	
		$db->exec($sqlend);
		$db=null;
		
	}
	
	
	
	
}





?>