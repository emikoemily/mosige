<?php
date_default_timezone_set('PRC');
Class Exportdata{
	
	private static $dsn = "mysql:host=127.0.0.1;dbname=yoga_lu";
	private static $dbuser =	"yoga_lu";
	private static $dbpass = "Yooq_yoga_lu";
	
	public static function exportAllMemberAttendData(){
		 
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql = "SELECT class_name, arrangedate as date,CONCAT(starttime ,'-' ,endtime)  as time,class_description,member_name,(CASE WHEN is_attended>0  THEN '1'
ELSE '0' END) as attend FROM yoga_lu.register_record inner join
class_arrange on class_arrange.arrange_id=register_record.arrange_id
inner join class_design 
on class_arrange.class_id=class_design.class_id
inner join member_user
on 
register_record.member_id=member_user.member_id 
 where register_record.member_id!=73 
and register_record.member_id!=76 and register_record.member_id!=61 and register_record.member_id!=102 and register_record.member_id!=60 and register_record.member_id!=48 and register_record.member_id!=256";
		$db->query("set names UTF8;");
		$rs = $db->query($sql);
		$db=null;
		
		return $rs->fetchAll();
		print_r(debug_backtrace());
		print_r(error_get_last());
		
	}
	
	
	public static function exportMemberAttendDatawithdate($startdate,$enddate){
			
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql = "SELECT class_name, arrangedate as date,CONCAT(starttime ,'-' ,endtime)  as time,class_description,member_name,(CASE WHEN is_attended>0  THEN '1'
ELSE '0' END) as attend FROM yoga_lu.register_record inner join
class_arrange on class_arrange.arrange_id=register_record.arrange_id
inner join class_design
on class_arrange.class_id=class_design.class_id
inner join member_user
on
register_record.member_id=member_user.member_id
 where register_record.member_id!=73
and register_record.member_id!=76 and register_record.member_id!=61 and register_record.member_id!=102 and register_record.member_id!=60 and register_record.member_id!=48 and register_record.member_id!=256
AND arrangedate >='{$startdate}' AND arrangedate<='{$enddate}'";
		//echo $sql;
		$db->query("set names UTF8;");
		$rs = $db->query($sql);
		$db=null;
	
		return $rs->fetchAll();
		//print_r(debug_backtrace());
		//print_r(error_get_last());
	}
	
	public static function exportMemberAttendItemswithdate($memid,$startdate,$enddate){
			
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql = "SELECT member_name as 会员名,class_name as 课包名,inner_id as 内部序号,class_description as 级别,arrangedate as 上课日期, dayofweek(arrangedate)-1 as 星期几, starttime as 开始时间,endtime as 结束时间
	   FROM class_arrange left JOIN register_record
on register_record.arrange_id= class_arrange.arrange_id 
      inner JOIN class_design on class_design.class_id = class_arrange.class_id 
inner JOIN teacher_table on class_arrange.teacher_id = teacher_table.teacher_id
left
JOIN member_user on  member_user.member_id=register_record.member_id 
where register_record.is_canceled=0 
		AND register_record.member_id={$memid}
		AND arrangedate >='{$startdate}' AND arrangedate<='{$enddate}' and is_attended>0
ORDER BY arrangedate,starttime,class_type,class_name";
		
		//echo $sql;
		$db->query("set names UTF8;");
		$rs = $db->query($sql);
		$db=null;
	
		return $rs->fetchAll();
		//print_r(debug_backtrace());
		//print_r(error_get_last());
	}
	
	public static function exportMemberPackageAttendCount($memid,$startdate,$enddate){
			
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql = "SELECT member_name as 会员,package_design.package_name as 课程包名, class_design.class_name as 课程名,class_description as 级别,inner_id 课程内部编号,sum(case when (register_record.member_id ={$memid} and is_attended>0 )then 1 else 0 end) as 上课次数
FROM 
package_subscribe inner join class_design on package_subscribe.package_id=class_design.package_id
inner join package_design on package_subscribe.package_id=package_design.package_id
inner join member_user on package_subscribe.member_id=member_user.member_id
left join class_arrange on class_design.class_id = class_arrange.class_id
left join register_record on class_arrange.arrange_id = register_record.arrange_id 
WHERE package_subscribe.member_id = {$memid} and arrangedate >='{$startdate}' and arrangedate<='{$enddate}' group by package_subscribe.package_id,inner_id order by package_subscribe.package_id,class_description ";
		//echo $sql;
		$db->query("set names UTF8;");
		$rs = $db->query($sql);
		$db=null;
	
		return $rs->fetchAll();
	 
	}
	
	
	public static function exportToFile(){
		
		
		echo "class_name\t";
		echo "date\t\n";
		echo "time\t\n";
		echo "class_description\t\n";
		echo "member_name\t\n";
		echo "attend\t\n";
		//这是表格头字段 加\T就是换格,加\T\N就是结束这一行,换行的意思
		
		 
		 
		
		$result=exportAllMemberAttendData();
		foreach($result as $row)
		 {
			echo $row[0]."\t";
			echo $row[1]."\t";
			echo $row[2]."\t";
			echo $row[3]."\t";
			echo $row[4]."\t";
			echo $row[5]."\t";
			echo $row[6]."\t\n";
			
			
		 }
	}
	
}







?>