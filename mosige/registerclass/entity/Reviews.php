<?php
Class Reviews{
	
	private static $dsn = "mysql:host=127.0.0.1;dbname=yoga_lu";
	private static $dbuser =	"yoga_lu";
	private static $dbpass = "Yooq_yoga_lu";
		
	public function createReviewInDB(){
		 
			
	}
	
	public static function getUnfinishedCount($memid){
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql = "SELECT count(*) as notreview from register_record 
		inner join class_arrange on register_record.arrange_id=class_arrange.arrange_id
		where member_id={$memid} and is_attended!=0 and reviewed = 0  
		and arrangedate>='2016-03-25'";
		
		$rs = $db->query($sql);
		$row = $rs->fetch();			
	    return $row['notreview'];
		 
		$db=null;
		
		print_r(error_get_last());
			print_r(debug_backtrace());
			
			
	}
	
}




?>