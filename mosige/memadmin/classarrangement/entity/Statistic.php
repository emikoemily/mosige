<?php
class Statistic{

	private static $dsn = "mysql:host=127.0.0.1;dbname=yoga_lu";
	private static $dbuser =	"yoga_lu";
	private static $dbpass = "Yooq_yoga_lu";


	static function getlowmem(){
		$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
		$start='2016-01-01';
		$end=date("Y-m-d",strtotime("{$start}+7 days"));
		
		for($end;$end<='2016-03-15';){
			echo "<br>";echo "<br>";
			echo "from ".$start." ";
			echo "- ".$end."<br>";
			$sql="SELECT member_name, count(*) as times
FROM register_record
inner join class_arrange on register_record.arrange_id= class_arrange.arrange_id
inner JOIN member_user on  member_user.member_id=register_record.member_id
where register_record.is_canceled=0 and is_attended>0
AND class_arrange.arrangedate >='{$start}' AND class_arrange.arrangedate <='{$end}'  
Group by register_record.member_id having times<=2";
			//echo $sql;
			$start=date("Y-m-d",strtotime("{$start}+7 days"));
			$end=date("Y-m-d",strtotime("{$end}+7 days"));
			
			$db->query("set names UTF8;");
			$res=$db->query($sql);
			$res->setFetchMode(PDO::FETCH_ASSOC);
			$rows = $res->fetchAll();
			 
			foreach($rows as $row){
				
				echo "".$row["member_name"]."<br>";
				echo "".$row["times"]."<br>";
			}
			
		}
		
		
	
	//echo $end;
		
	
	}
}	
?>

<head>

<meta name="viewport" content="width=device-width,initial-scale=1" charset="utf8">
<meta http-equiv="content-type" content="text/html;charset=utf8">
<style type="text/css" media="all">@import "style.css";</style>
<style type="text/css" media="all">@import "common.css";</style>
<title>mosige管理系统</title>
</head>
<?php Statistic::getlowmem(); ?>

