<?php
class Leave{
	public $name; 
	public $memid=0;
	public $leave_days;
	public $leave_count;
	private static $dsn = "mysql:host=127.0.0.1;dbname=yoga_lu";
	private static $dbuser =	"yoga_lu";
	private static $dbpass = "Yooq_yoga_lu";





		public static function applyLeaveRequest($memid,$from,$to,$reason){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);	
			//isinprogress=2 means apply request ,not confirmed.
			$db->exec("set names utf8;");
			$sqladdleave ="INSERT INTO `yoga_lu`.`apply_leave` (`member_id`,`isinprogress`,`leave_startdate`,`leave_enddate`,`leave_reason`) VALUES ('{$memid}','2','{$from}','{$to}','{$reason}');";
			$db->exec($sqladdleave);		
			$sqlrequestleave ="UPDATE `yoga_lu`.`member_user` set member_isleave=2 where member_id='{$memid}';";
			$db->exec($sqlrequestleave);
			
			$db=null;
	
				
		}

		public static function endLeave($memid){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			//isinprogress=2 means apply request ,not confirmed.
			
		//	$sqladddate ="UPDATE `yoga_lu`.`apply_leave` set leave_enddate=date('Y-m-d') where member_id='{$memid}' and isinprogress=1 and leave_enddate<date('Y-m-d');";
		//	$db->exec($sqladddate);
				
			$sqlendleave ="UPDATE `yoga_lu`.`member_user` set member_isleave=0 where member_id='{$memid}';";
			$db->exec($sqlendleave);
			
			$sqlsetend ="UPDATE `yoga_lu`.`apply_leave` set isinprogress=3 where member_id='{$memid}' AND (isinprogress=2 or isinprogress=1)  ;";//3 is end,1 is approved,2 is request
			$db->exec($sqlsetend);
			$db=null;
		
		
		}

		public static function ifOverEnddate($date,$memid){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sql ="SELECT leave_enddate from `yoga_lu`.`apply_leave` where member_id='{$memid}' order by idapply_leave desc limit 1 ";
			
			$end=$db->query($sql);
			$enddate=$end->fetch();
			$db=null;
			if($date>$enddate[0]){
				return true;
			}else{
				return false;
			}
		
		}	
		public static function getRequestDays($memid){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sql ="SELECT  DATEDIFF(leave_enddate,leave_startdate)+1 as requestdays from `yoga_lu`.`apply_leave` where member_id='{$memid}' order by idapply_leave desc limit 1 ";
			echo $sql;
			$rs = $db->query($sql);
			$db=null;
			$days=$rs->fetch();
			return $days[0];
		//return 0;
		}
		
		public static function getDaysCompareToday($memid){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sql ="SELECT DATEDIFF(CURDATE(),leave_startdate) as CompareTodaydays from `yoga_lu`.`apply_leave` where member_id='{$memid}' order by idapply_leave desc limit 1 ";
			$rs = $db->query($sql);
			$db=null;
			$days=$rs->fetch();
			return $days[0];
		}
		
		public static function getLeaveDays($memid){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sql ="SELECT member_leavedays from `yoga_lu`.`member_user` where member_id='{$memid}' ";			 		
			$rs = $db->query($sql);
			$db=null;
			$days=$rs->fetch();
			return $days[0];		
		}
		public static function getMaxLeaveDays($mem){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sql ="SELECT member_leavemaxdays from `yoga_lu`.`member_user` where member_id='{$memid}' ";
			$rs = $db->query($sql);
			$db=null;
			$days=$rs->fetch();
			return $days[0];
		
		}
		public  static function getLeaveStart($memid){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sql ="SELECT leave_startdate from `yoga_lu`.`apply_leave` where member_id='{$memid}' AND isinprogress!=4 order by idapply_leave desc limit 1";
			$rs = $db->query($sql);
			$db=null;
			 $date=$rs->fetch();
			 if($date!=NULL){
			 	return $date[0];
			 }
			 else{
			 	return "2199-12-12";
			 }
		
		}
		public  static function getLeaveReason($memid){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sql ="SELECT leave_reason from `yoga_lu`.`apply_leave` where member_id='{$memid}' AND isinprogress!=4 order by idapply_leave desc limit 1";
			$rs = $db->query($sql);
			$db=null;
			$date=$rs->fetch();
			if($date!=NULL){
				return $date[0];
			}
			else{
				return "wu jie guo";
			}
		
		}
		public  static function getLeaveEnd($memid){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sql ="SELECT leave_enddate from `yoga_lu`.`apply_leave` where member_id='{$memid}' order by idapply_leave desc limit 1";
			$rs = $db->query($sql);
			$db=null;
			$date=$rs->fetch();
			return $date[0];
		
		}
		
		public static function getLeaveCounts($memid){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sql ="SELECT member_leavecount from `yoga_lu`.`member_user` where member_id='{$memid}' ";			
			$rs = $db->query($sql);
			$db=null;
			$c=$rs->fetch();
			return $c[0];
		
		}
		public static function reduceLeaveDays($days,$memid){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sql ="Update `yoga_lu`.`member_user` set  member_leavedays=member_leavedays-{$days} where member_id='{$memid}' AND member_leavedays>={$days} ";
			//echo $sql;
			$rs = $db->exec($sql);
			$db=null;
				
		
		}
		public static function extendLeaveDaysToMem($days,$memid){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sql ="Update `yoga_lu`.`member_user` set  member_enddate=date_add(member_enddate, interval {$days} day) where member_id='{$memid}'";
			//echo $sql;
			$rs = $db->exec($sql);
			$sql2 ="Update `yoga_lu`.`payment_table` set payment_enddate=date_add(payment_enddate, interval {$days} day) where member_id='{$memid}' AND is_started=1 AND is_archieved!=1 order by id_payment_table asc limit 1";
			//echo $sql2;
			$rs2 = $db->exec($sql2);
			
			$db=null;
		
		
		}
		public static function reduceLeaveCounts($memid){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sql ="Update `yoga_lu`.`member_user` set  member_leavecount=member_leavecount-1 where member_id='{$memid}' AND member_leavecount>=1 ";
			$rs = $db->exec($sql);
			$db=null;
			
		
		}

		public static function updateEndDate($enddate,$memid){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sql ="Update `yoga_lu`.`apply_leave` set leave_enddate=$enddate where member_id='{$memid}' order by idapply_leave desc limit 1 ";
			$rs = $db->exec($sql);
			$db=null;
				
		
		}
		public static function updateEndDateToToday($memid){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sql ="Update `yoga_lu`.`apply_leave` set leave_enddate=CURDATE() where member_id='{$memid}' order by idapply_leave desc limit 1 ";
			$rs = $db->exec($sql);
			$db=null;
		
		
		}
}