<?php
class Leave{
	public $name; 
	public $memid=0;
	public $leave_days;
	public $leave_count;
	private static $dsn = "mysql:host=127.0.0.1;dbname=yoga_lu";
	private static $dbuser =	"yoga_lu";
	private static $dbpass = "Yooq_yoga_lu";





	public function applyLeaveRequest($memid,$from,$to,$reason){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);	
			//isinprogress=2 means apply request ,not confirmed.
			$sqladdleave ="INSERT INTO `yoga_lu`.`apply_leave` (`member_id`,`isinprogress`,`leave_startdate`,`leave_enddate`,`leave_reason`) VALUES ('{$memid}','2','{$from}','{$to}','{$reason}');";
			$db->exec($sqladdleave);		 
			$db=null;
	
				
		}

		public  static function getLeaveReason($memid){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sql ="SELECT leave_reason from `yoga_lu`.`apply_leave` where member_id='{$memid}' AND isinprogress!=4 order by idapply_leave desc limit 1";
			$db->query("set names UTF8;");
			$rs = $db->query($sql);
			$rs->setFetchMode(PDO::FETCH_NUM);
			//echo $sql;
			$date=$rs->fetch();
			if($date!=NULL){
				return $date[0];
			}
			else{
				return "wu jie guo";
			}
			$db=null;
		}
		public  static function getLeaveStart($memid){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sql ="SELECT leave_startdate from `yoga_lu`.`apply_leave` where member_id='{$memid}' AND isinprogress!=4 order by idapply_leave desc limit 1";
			//echo $sql;
			$rs = $db->query($sql);
			$rs->setFetchMode(PDO::FETCH_NUM);
			$date=$rs->fetch();
			if($date!=NULL){
				return $date[0];
			}
			else{
				return "需要下次登陆才能显示最新状态";
			}
			$db=null;
		}
		public  static function getLeaveEnd($memid){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sql ="SELECT leave_enddate from `yoga_lu`.`apply_leave` where member_id='{$memid}' order by idapply_leave desc limit 1";
			$rs = $db->query($sql);
			$db=null;
			$date=$rs->fetch();
			return $date[0];
		
		}

}