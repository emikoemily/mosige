<?php
class Arrange{
	
	private static $dsn = "mysql:host=127.0.0.1;dbname=yoga_lu";
	private static $dbuser =	"yoga_lu";
	private static $dbpass = "Yooq_yoga_lu";
	
	
	static function findlatestlevel($mid,$pid){
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sqlprogress="SELECT max(inner_id) as latest
		FROM package_subscribe
		inner join class_design on package_subscribe.package_id=class_design.package_id
		inner join class_arrange on class_arrange.class_id=class_design.class_id
		inner join register_record on	class_arrange.arrange_id=register_record.arrange_id
		where
		package_subscribe.package_id='{$pid}' and package_subscribe.member_id='{$mid}' and register_record.member_id='{$mid}'
		";
		//echo  $sqlprogress;
		$db->query("set names UTF8;");
		$prgress=$db->query($sqlprogress);
		$prgress->setFetchMode(PDO::FETCH_ASSOC);
		$rowprgresss = $prgress->fetch();
	
		return $rowprgresss['latest'];
	}
	
	
	static function findjumped($mid,$pid){
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sqlprogress="SELECT distinct inner_id 
		FROM jump_record
		where
		jump_record.package_id='{$pid}' and jump_record.member_id='{$mid}'
		";
		//echo  $sqlprogress;
		$prgress=$db->query($sqlprogress);
		$prgress->setFetchMode(PDO::FETCH_ASSOC);
		$rowprgress = $prgress->fetchAll();
		//var_dump($rowprgress);
		$sqlall="SELECT distinct inner_id
		FROM class_design
		where
		class_design.package_id='{$pid}'
		";
 		//echo $sqlall;
 		$db->query("set names UTF8;");
		$all=$db->query($sqlall);
		$all->setFetchMode(PDO::FETCH_ASSOC);
		$rowall= $all->fetchAll();
		//var_dump($rowall);
		
		$allid=array();
		$attendedid=array();
		foreach($rowall as $row) {
		
			$allid[] =  $row['inner_id'];
 
		}
		
		foreach($rowprgress as $row1) {
			 
			$attendedid[]=$row1['inner_id'];
			 
		}
		 
		$diff=Array();
		$diff=array_diff($allid,$attendedid);
		//var_dump($diff);
		
		return $diff;
	}
	
	
	static function listMemberbyoption($time,$day){
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql="SELECT distinct investigation_time.member_id,member_name 
		FROM investigation_time
		inner join member_user on investigation_time.member_id =member_user.member_id
		where
		member_user.member_id!=73
		AND
		day='{$day}' and time='{$time}'
		";
		//echo $sql;
		$db->query("set names UTF8;");
		$res=$db->query($sql);
		$res->setFetchMode(PDO::FETCH_ASSOC);
		$rows = $res->fetchAll();

		return $rows;
	}
	static function listPackagebymem($mem){
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql="SELECT package_name,package_subscribe.package_id  
		FROM package_subscribe 
		inner join package_design on package_subscribe.package_id=package_design.package_id
		where
		member_id='{$mem}' and is_finished!=1
		";
		//echo $sql;
		$db->query("set names UTF8;");
		$res=$db->query($sql);
		$res->setFetchMode(PDO::FETCH_ASSOC);
		$rows = $res->fetchAll();
		
		return $rows;
	}
	
	static function ifarranged($package,$level,$time,$day){
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql="SELECT count(*) as cnt 
		FROM class_arrange
		inner join class_design on class_arrange.class_id=class_design.class_id
		where
		package_id='{$package}' and inner_id=$level and starttime ='{$time}' and arrangedate>='".date('Y-m-d')."'";
		//echo $sql;
		if($day=='weekend')
		{
			$sql=$sql. "and (DAYOFWEEK(arrangedate)=6 or DAYOFWEEK(arrangedate)=7)";
		}else{
			$sql=$sql. "and (DAYOFWEEK(arrangedate)!=6 AND DAYOFWEEK(arrangedate)!=7)";
		}
		$db->query("set names UTF8;");
		$res=$db->query($sql);
		$res->setFetchMode(PDO::FETCH_ASSOC);
		$rows = $res->fetch();
		//echo $rows['cnt'];
		if($rows['cnt']>0){
			return true;
		}else{
			return false;
		}
		
		
	}
	
	static function isWeekend($datetocheck){
		$a = date("w",strtotime($datetocheck));
			
		if($a =="0" || $a=="6")
		{
			return true;
		}
		else{
			return false;
		}
	}
	
}
 ?>

