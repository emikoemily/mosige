<?php
class Cardrule{
		
		public $rulename;
		public $level_actual;
		public $rule_alias="";
		public $rule_displayname="";
		public $rule_startdate="0000-00-00 00:00:00";
		public $rule_description="";
		public $has_kongzhong="";
		public $has_ertong="";
		public $days;
		public $enddate;
		public $maxcount;
		public $time_rule;
		public $leavedays;
		public $leavecounts;
		private static $dsn = "mysql:host=127.0.0.1;dbname=yoga_lu";
		private static $dbuser =	"yoga_lu";
		private static $dbpass = "Yooq_yoga_lu";
		
			function __construct($type)
		{ 
			$this->rulename=$type;
			//$count=0;
			//$days=0;
			//$level_actual="";
			
			
			//$dsn = "mysql:host=127.0.0.1;dbname=yoga_lu";
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sql = "SELECT * from card_rule where rule_name = '{$type}'";
			 
			//echo $sql;
			$rs = $db->query($sql);
			$rows = $rs->fetchAll();
			foreach($rows as $row){
				 
				$this->days=$row['rule_days'];
				$this->maxcount=$row['rule_maxcount'];
				$this->level_actual=$row['level_actual'];
				$this->rule_alias=$row['rule_alias'];
				$this->rule_displayname=$row['rule_displayname'];
				$this->enddate=$row['rule_enddate'];
				$this->rule_startdate=$row['rule_startdate'];
				$this->rule_description=$row['rule_description'];
				$this->has_kongzhong=$row['has_kongzhong'];
				$this->has_ertong=$row['has_ertong'];
				$this->time_rule=$row['time_rule'];
				$this->leavedays=$row['card_leavedays'];
				$this->leavecounts=$row['card_leavecounts'];
			}
			$db=null;			
			
			//$this->days=$days;
			//$this->maxcount=$count;
			//$this->rulename=$level_actual;
		}
		
		public static function getAllRulename(){
			
				
			//$dsn = "mysql:host=127.0.0.1;dbname=yoga_lu";
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sql = "SELECT rule_name,rule_displayname from card_rule";
			$db->query("set names UTF8;");
			$rs = $db->query($sql);
			$db=null;
			
			return $rs->fetchAll();
			
			
		}
		public static function getAllRule(){
				
		
			//$dsn = "mysql:host=127.0.0.1;dbname=yoga_lu";
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sql = "SELECT * from card_rule";
			$db->query("set names UTF8;");
			$rs = $db->query($sql);
			$db=null;
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			return $rs->fetchAll();
				
				
		}
		public static function addRuleToDB($rule_name,$level_actual,$rule_displayname,$rule_days,$rule_startdate,$rule_enddate,$rule_description,$rule_maxcount,$has_kongzhong,$has_ertong){
		
				
			 
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sql = "INSERT INTO card_rule (rule_name,level_actual,rule_alias,rule_displayname,rule_days,rule_startdate,rule_enddate,rule_description,rule_maxcount,has_kongzhong,has_ertong,time_rule) ";
			
			$sql .= " values('{$rule_name}',";
		 
			$sql .= " '{$level_actual}',";
			$sql .= " '{$rule_name}',";
			$sql .= " '{$rule_displayname}', ";
			$sql .= " '{$rule_days}',";
			$sql .= " '{$rule_startdate}', ";
			$sql .= " '{$rule_enddate}', ";
			$sql .= " '{$rule_description}', ";
			$sql .= " '{$rule_maxcount}', ";
			$sql .= " '{$has_kongzhong}', ";
			$sql .= " '{$has_ertong}', ";
			$sql .= " '{$time_rule}') ;";
			echo $sql;
			//$db->query('set names UTF8'); 
			//$res = $db->query($sql);
			$db->exec("set names UTF8;");
			$db->exec($sql);
			$db=null;
			print_r(debug_backtrace());
			print_r(error_get_last());
		
	    }
	    public static function deleteRule($rid){
	    
	    
	    	//$dsn = "mysql:host=127.0.0.1;dbname=yoga_lu";
	    	$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
	    	$sql = "DELETE * from card_rule where rule_id ={$rid}";
	    	
			$db->exec($sql);
			$db=null;
			//print_r(debug_backtrace());
			//print_r(error_get_last());
	    
	    
	    }
	    
	    public static function getDaysByRulename($rulename){
	    
	    	$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
	    	$sql="Select rule_days FROM `yoga_lu`.`card_rule` where rule_name='{$rulename}' limit 1;";
	   // echo $sql;
	    	$rs = $db->query($sql);
	    	$db=null;
	    		
	    	return $rs->fetch();
	    }
		
	}
	
	
?>	