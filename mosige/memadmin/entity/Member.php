<?php
class Member{
		public $name;
		public $pass;
		public $mail;
		public $sex;
		public $tel;
		public $birthday;
		public $point;
		public $intro="";
		public $cardid;
		public $rule;
		public $memid=0;
		public $channel;
		public $allaccount;
		
		private static $dsn = "mysql:host=127.0.0.1;dbname=yoga_lu";
		private static $dbuser =	"yoga_lu";
		private static $dbpass = "Yooq_yoga_lu";
	 
		function __construct($name,$pass,$mail,$sex,$tel,$birthday,$point,$intro,$cardid,$rule,$channel,$allaccount)
		{ 
			$this->name=$name; 	
			$this->pass=$pass; 	
			$this->mail=$mail; 
			$this->sex=$sex;
			$this->tel=$tel;
			$this->birthday=$birthday; 
			$this->point=$point; 
			$this->intro=$intro; 
			$this->cardid=$cardid; 		
			$this->rule=$rule; 
			$this->channel=$channel;
			$this->allaccount=$allaccount;
			
		// print_r(debug_backtrace());
		 //print_r(error_get_last());
		}
		
		public function createMemberInDB(){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sql = "INSERT INTO member_user (member_name,member_password,member_email,member_sex,member_cell,member_level,rule_name,member_birthday,member_points,member_intro,member_attendmax,member_cardid,member_enddate,member_days,member_channel,member_allaccount,member_leavecount,member_leavemaxdays,member_leavedays) ";
			
			$sql .= " values('{$this->name}',";
			
			$sql .= " '{$this->pass}',";
			$sql .= " '{$this->mail}', ";
			$sql .= " '{$this->sex}',";
			$sql .= " '{$this->tel}', ";
			$sql .= " '{$this->rule->level_actual}', ";
			$sql .= " '{$this->rule->rulename}', ";
			$sql .= " '{$this->birthday}', ";
			$sql .= " '{$this->point}', ";
			$sql .= " '{$this->intro}', ";
			$sql .= " '{$this->rule->maxcount}', ";
			$sql .= " '{$this->cardid}',";
			$sql .= " '{$this->rule->enddate}',";
			$sql .= " '{$this->rule->days}',";
			$sql .= " '{$this->channel}',";
			$sql .= " '{$this->allaccount}',";
			$sql .= " '{$this->rule->leavecounts}',";
			$sql .= " '{$this->rule->leavedays}',";
			$sql .= " '{$this->rule->leavedays}') ;";
			//echo $sql;
			//$db->query('set names UTF8'); 
			//$res = $db->query($sql);
			$db->exec("set names UTF8;");
			$db->exec($sql);
			$this->memid = $db->lastInsertId();
			
			$db=null;
			//if(!$res) {
			//	die("");
			//}
			//print_r(error_get_last());
		//	print_r(debug_backtrace());
			
		}
		 
		
		
		
		public function subKongZhong(){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			 
			//$db->query('set names UTF8');
			//$res = $db->query($sql);
					
			
			 
			$dt=date('Ymd');
			$payid="set1-".$this->memid.$dt;
			$sqladdsub ="INSERT INTO `yoga_lu`.`package_subscribe` (`member_id`,`package_id`,`payment_id`,`set_max`) select '{$this->memid}','set1','{$payid}',10  from dual where not exists (select * from package_subscribe where package_id='set1' AND member_id='{$this->memid}' AND payment_id ='{$payid}');";
			//echo $sqladdsub;
			$db->exec($sqladdsub);;
			$sqladdpayment="INSERT INTO `yoga_lu`.`payment_table` (`payment_id`,`member_id`) select '{$payid}','{$this->memid}' from dual where not exists (select * from payment_table where payment_id ='{$payid}');";
			echo $sqladdpayment;
			$db->exec($sqladdpayment);
			$sqlupdatedays="UPDATE `yoga_lu`.`payment_table` SET `payment_days` ='{$this->rule->days}' where `payment_id` ='{$payid}'";
			//echo $sqlupdatedays;
			$db->exec($sqlupdatedays);
			$db=null;
			//if(!$res) {
			//	die("");
			//}
			//print_r(error_get_last());
			//	print_r(debug_backtrace());
				
		}
		
		
		public function subErTongYoga(){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			 
			//$db->query('set names UTF8');
			//$res = $db->query($sql);
			//$db->exec("set names UTF8;");
		
			
			$dt=date('Ymd');
			$payid="set2-".$this->memid.$dt;
			$sqladdsub ="INSERT INTO `yoga_lu`.`package_subscribe` (`member_id`,`package_id`,`payment_id`) select '{$this->memid}','set2','{$payid}'  from dual where not exists (select * from package_subscribe where package_id='set1' AND member_id='{$this->memid}' AND payment_id ='{$payid}');";
			$db->exec($sqladdsub);
			//echo $sqladdsub;
			$sqladdpayment="INSERT INTO `yoga_lu`.`payment_table` (`payment_id`,`member_id`) select '{$payid}','{$this->memid}' from dual where not exists (select * from payment_table where payment_id ='{$payid}');";
			$db->exec($sqladdpayment);
			//echo $sqladdpayment;
			$sqlupdatedays="UPDATE `yoga_lu`.`payment_table` SET `payment_days` ='{$this->rule->days}' where `payment_id` ='{$payid}'";
			$db->exec($sqlupdatedays);
			//echo $sqlupdatedays;
			$db=null;
			//if(!$res) {
			//	die("");
			//}
			//print_r(error_get_last());
			//	print_r(debug_backtrace());
		}
		
		public static function getAllPackageMember(){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
 
			 
			$sqladdsub ="SELECT member_id,member_name from `yoga_lu`.`member_user` where member_level='package' or (member_level='both' and rule_name='both_c1y') order by member_name;";
			
			$db->query("set names UTF8;");
			$rs = $db->query($sqladdsub);
			$db=null;
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			return $rs->fetchAll();		
		}
	
	

	public static function getNamebyID($memid){
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
	
	
		$sql ="SELECT member_name from `yoga_lu`.`member_user` where member_id={$memid};";
			
		$db->query("set names UTF8;");
		$rs = $db->query($sql);
		$db=null;
		//$rs->setFetchMode(PDO::FETCH_ASSOC);
		return $rs->fetchAll();		
	}
	
	
	public static function updateMemberbyID($memid,$form){
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		//extract($form);
		$sql = "update member_user set ";
		
		if($form["name"] != "") {
			 
			$sql .= "member_name='{$form['name']}', ";
		}
		
		if($form["pass"] != "") {
		$form["pass"] = md5($form["pass"]);
		$sql .= "member_password='{$form['pass']}', ";
		}
		
		
	  
		$sql .= " member_cell='{$form['tel']}', ";
		if($form['type']=="both_weekend"){
			$sql .= "member_level='both_weekend', ";
			
		}
		else if(substr($form['type'],0,4)=='both'){
			$sql .= "member_level='both', ";
			
		}
		else if(substr($form['type'],0,7)=='package'){
			$sql .= "member_level='package', ";
				
		}
		else if(substr($form['type'],0,6)=='common' and $form['type']!='common_count_both' ){
			$sql .= "member_level='common', ";
				
		}else{
			$sql .= "member_level='both', ";
		}
		$sql .= "rule_name='{$form['type']}', ";
		if(substr($type,0,12)=='common_count'){$sql .= " member_attendmax='{$form['maxdays']}', ";};
		$sql .= " member_days='{$form['days']}', ";
		//$sql .= " member_attendmax='{$maxdays}', "
		$sql .= " member_points='{$form['point']}', ";
		$sql .= " member_startdate='{$form['startdate']}', ";
		$sql .= " member_enddate='{$form['enddate']}', ";
		$sql .= " member_leavecount='{$form['leavecounts']}', ";
		$sql .= " member_leavedays='{$form['leavedays']}', ";
		$sql .= " member_intro='{$form['intro']}' , ";
		$sql .= " member_channel='{$form['channel']}' ";
		$sql .= " where member_id='{$memid}' ";
		//echo $sql;
		$db->exec("set names UTF8;");
		$db->exec($sql);
		$db=null;
		 
	}

	public static function saveAdditionalDays($memid,$days){
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);		
		$sql ="INSERT INTO additional_days (member_id,days) values ('{$memid}','{$days}');";	
	//echo $sql;
		$db->exec($sql);
		$db=null;
		print_r(error_get_last());
		print_r(debug_backtrace());
		
	}
	
	public static function getAdditionnalDays($memid){
	
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql="Select days FROM `yoga_lu`.`additional_days` where member_id={$memid} and is_used=0 limit 1;";
	
		 //echo $sql;
		$rs = $db->query($sql);
		$db=null;
		 
		return $rs->fetch();	
	}
	
	public static function setAdditionnalDaysUsed($memid){
	
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql="Update `yoga_lu`.`additional_days` set is_used=1 where member_id={$memid} limit 1;";
			
		$rs = $db->exec($sql);
		$db=null;
			
		 
	}
	
	public static function getMemberRuleByID($memid){
	
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql="Select rule_name FROM `yoga_lu`.`member_user` where member_id={$memid} limit 1;";
		//echo $sql;
		$rs = $db->query($sql);
		$db=null;
			
		return $rs->fetch();
	}
	
	public static function isNearOver($memid){
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql="SELECT distinct payment_table.payment_id, payment_startdate,payment_enddate FROM payment_table
		INNER JOIN package_subscribe
		ON package_subscribe.payment_id=payment_table.payment_id
		WHERE  payment_enddate !='0000-00-00 00:00:00' AND payment_table.`member_id`= {$memid} AND payment_table.is_archieved!=1;";
		//echo $sql;
		
		 
		$rs = $db->query($sql);	
		
		 
		$rs->setFetchMode(PDO::FETCH_ASSOC);
		$rows=$rs->fetchAll();
		 
		$nowminus10=date("Y-m-d H:i:s",strtotime("+10 days"));
		//echo $nowminus10;
		foreach($rows as $row){
			//echo $row["payment_enddate"];
			$num=0;
			if($row["payment_enddate"]<=$nowminus10){
				$num=$num+1;
				echo "(有课包将于".$row["payment_enddate"]."过期)";
				 
			}
			if($num>0){
				return true;
			}			
			else{
				return false;
					
			}
			
		}
		
	}
	
	public static function isMemberNearOver($memid){
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql="SELECT distinct member_id, member_startdate,member_enddate FROM member_user
		 
		WHERE  member_enddate !='0000-00-00 00:00:00' AND member_user.`member_id`= {$memid};";
		
		
		//echo $sql;
	
			
		$rs = $db->query($sql);	
			
		$rs->setFetchMode(PDO::FETCH_ASSOC);
		$rows=$rs->fetchAll();
			
		$nowminus10=date("Y-m-d H:i:s",strtotime("+10 days"));
		//echo $nowminus10;
		foreach($rows as $row){
			//echo $row["payment_enddate"];
			$num=0;
			if($row["member_enddate"]<=$nowminus10){
				$num=$num+1;
				echo "(会员卡将于".$row["member_enddate"]."过期)";
					
			}
			if($num>0){
				return true;
			}
			else{
				return false;
					
			}
				
		}
	
	}
}

/*if(Member::isNearOver(73)){
	echo "over";
}
else{
	echo "not over";
}*/

	?>