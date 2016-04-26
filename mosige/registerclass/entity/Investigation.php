<?php
class Investigation{
	
	private static $dsn = "mysql:host=127.0.0.1;dbname=yoga_lu";
	private static $dbuser =	"yoga_lu";
	private static $dbpass = "Yooq_yoga_lu";
	
	

		public static function addTime($memid,$time,$day){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);	
		 
			$sqladdtime ="INSERT INTO `yoga_lu`.`investigation_time` (`member_id`,`time`,`day`) VALUES ('{$memid}','{$time}','{$day}');";
			//echo $sqladdtime;
			$db->exec($sqladdtime);
			$db=null;
	
				
		}
		public static function addSelftime($memid,$option){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		
			$sqladdtime ="INSERT INTO `yoga_lu`.`investigation_other` (`member_id`,`option`) VALUES ('{$memid}','{$option}');";
			//echo $sqladdtime;
			$db->exec("set names utf8");
			$db->exec($sqladdtime);
			$db=null;
		
		
		}
		public static function addDay($memid,$day){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
				
			$sqladdtime ="INSERT INTO `yoga_lu`.`investigation_day` (`member_id`,`day`) VALUES ('{$memid}','{$day}');";
			$db->exec($sqladdtime);
			$db=null;
		
		
		}
		public static function getMembytime($time){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		
			$sqladdtime ="SELECT member_id from `yoga_lu`.`investigation_time` where time= '{$time}';";
			$db->query($sqladdtime);
			$res->setFetchMode(PDO::FETCH_ASSOC);
			$rows = $res->fetchAll();
			$db=null;
			return $rows;
		
		
		}
	
		
		
		public static function getLevelbymem($memid){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		
			$sqladdtime ="SELECT inner_id from `yoga_lu`.`investigation_time` where time= '{$memid}';";
			$res=$db->query($sqladdtime);
			$res->setFetchMode(PDO::FETCH_ASSOC);
			$rows = $res->fetchAll();
			$db=null;
			return $rows;
		
		
		}
		public static function ismemdone($memid){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		
			$sql ="SELECT count(*) as total from `yoga_lu`.`investigation_time` where member_id= '{$memid}';";
			//echo $sql;
			
			$result=$db->query($sql);
			$result->setFetchMode(PDO::FETCH_ASSOC);
			
			$row = $result->fetch();
		//	$db=null;
	
			if($row['total']>0){
				//print_r(error_get_last());
				//echo $row['total'];
				return true;
				
			}else{
				
				return false;
			}
		 
		
		
		}
}






?>
