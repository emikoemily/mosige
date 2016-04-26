<?php

Class TeacherReview{
	private static $dsn = "mysql:host=127.0.0.1;dbname=yoga_lu";
	private static $dbuser =	"yoga_lu";
	private static $dbpass = "Yooq_yoga_lu";
	
	
	
	
	public static function addReview($regid,$review,$type){
	
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql="INSERT INTO teacher_review (`register_id`,`review_content`,`review_type`) VALUES ('{$regid}','{$review}','{$type}');";
	
		//echo $sql;
		$db->exec("set names UTF8;");
		$rs = $db->exec($sql);
		$db=null;
	}
	public static function updateReview($regid,$review,$type){
	
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql="UPDATE teacher_review set `review_content`='{$review}' where register_id= '{$regid}' AND review_type='{$type}';";
	
		//echo $sql;
		$db->exec("set names UTF8;");
		$rs = $db->exec($sql);
		$db=null;
	}
	
	public static function getReview($regid,$type){
	
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql="SELECT review_content from teacher_review  where register_id= '{$regid}' and review_type='{$type}';";
	
		//echo $sql;
		$db->query("set names UTF8;");
		$rs = $db->query($sql);
		
		$rs->setFetchMode(PDO::FETCH_ASSOC);
		$db=null;
		return $rs->fetch();	
	}
	
	public static function listReviewByDate($startdate,$enddate){
	
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$sql="SELECT member_name as 会员,class_name as 课名,inner_id as 级别,arrangedate as 上课日期,starttime as 上课时间,review_content as 评语,(CASE WHEN review_type='classprogress'  THEN '上课进度' 
WHEN review_type='completeprogress'  THEN '完成进度' 
WHEN review_type='memberbody'  THEN '会员身体状况' 
WHEN review_type='membercomment'  THEN '会员反映' 
ELSE '' END) as 评语类型,teacher_name as 教师 FROM `teacher_review`
inner join register_record on teacher_review.register_id=register_record.register_id 
inner join class_arrange on class_arrange.arrange_id=register_record.arrange_id
inner join class_design on class_design.class_id=class_arrange.class_id
inner join member_user on member_user.member_id=register_record.member_id 
inner join teacher_table on class_arrange.teacher_id=teacher_table.teacher_id 
				where arrangedate >='{$startdate}' AND arrangedate <='{$enddate}';";
	
		//echo $sql;
		$db->query("set names UTF8;");
		$rs = $db->query($sql);
	
		//$rs->setFetchMode(PDO::FETCH_ASSOC);
		$db=null;
		return $rs->fetchALL();
	}
}
?>