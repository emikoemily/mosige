<?php
	session_start();
	date_default_timezone_set('PRC');	
	//include("dbconnect.inc.php");
	include("functions.inc.php");
	include("sendmail.php");
	include("entity/Member.php");
	$yestoday=date("Y-m-d",strtotime("-1 day"));
	//$yestoday="2015-11-10";
	//$today=date("Y-m-d");
	//$_SESSION['QIANDAO_DAY']=date("Y-m-d",strtotime("-1 day"));
	//echo $_SESSION['QIANDAO_DAY'];
	$dsn = "mysql:host=127.0.0.1;dbname=yoga_lu";
	$dbuser =	"yoga_lu";
	$dbpass = "Yooq_yoga_lu";
	
	Class Registerrecord{
		private static $dsn = "mysql:host=127.0.0.1;dbname=yoga_lu";
		private static $dbuser =	"yoga_lu";
		private static $dbpass = "Yooq_yoga_lu";
		public $regid;
		public $mid;
		public $mlevel;
		public $pid;
		public $inid;
		public $classtype;
		public $arrid;
		
		/*function __construct($regid,$mid,$mlevel,$pid,$inid,$classtype,$arrid){
			
			$this->regid=$regid;
			$this->mid=$mid;
			$this->mlevel=$mlevel;
			$this->pid=$pid;
			$this->inid=$inid;
			$this->classtype=$classtype;
			$this->arrid=$arrid;
			
		}*/
		
		
	static	function addMembeCount($memid){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sqladd = "UPDATE member_user SET `member_classcount` = `member_classcount`+1 WHERE `member_id`= {$memid};";
			$db->exec($sqladd);
			//$rowqiandao= mysqli_affected_rows();
		}
		static	function addMembeCount2($memid){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sqladd = "UPDATE member_user SET `member_classcount` = `member_classcount`+2 WHERE `member_id`= {$memid};";
			$db->exec($sqladd);
			//$rowqiandao= mysqli_affected_rows();
		}
	static	function addInnerRecored($memid,$pid,$inid){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sql_addinnerrec = "INSERT into jump_record (`member_id`,`package_id`,`inner_id`) values ({$memid},'{$pid}',{$inid})";
			$db->exec($sql_addinnerrec);
		}
	static	function addPackageSub($memid,$pid){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sqladdpackage ="UPDATE package_subscribe SET `package_attended` = `package_attended`+1 WHERE `member_id`= {$memid} AND `package_id`='{$pid}' AND is_finished=0;";
			echo $sqladdpackage;
			$db->exec($sqladdpackage);
			 
			 
		}
	static	function markattend($regid){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sql = "UPDATE register_record SET `is_attended` =`is_attended`+1000 WHERE `register_record`.`register_id`= {$regid} and `is_attended`<2;";
			$db->exec($sql);
			 
		}
	static function getarrinfo($arrid){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$sqlga="SELECT arrangedate,starttime  FROM class_arrange WHERE arrange_id  = {$arrid}";
			 
			$db->query('set names UTF8');
			$return='';
			$rs=$db->query($sqlga);
			$rs->setFetchMode(PDO::FETCH_ASSOC);
			$rowall=$rs->fetchAll();
			foreach($rowall as $rowga){
				$return=$return.$rowga['arrangedate']." ".$rowga['starttime'];
			}
			 
			return $return;
		}
		
		
	static	function startcard($memid,$pid,$arrid,$classtype,$mlevel){
			$db = new PDO(self::$dsn, self::$dbuser,  self::$dbpass);
			$start = self::getarrinfo($arrid);
			
			//echo $start;
			
			
			
			/*有课包的人是package 或both的（vip bothweekend bothmonth），先开payment_table 然后同步到member_user 如果是一个人的第二个课包开卡，则看这个课包的有效期结束时间如果比member_user里的enddate晚则同步过去*/
			if($classtype=='package' or $classtype=='set'){
				//echo "hhhh";
				$sqlgetdays = "select payment_days,additional_days from payment_table inner join package_subscribe on payment_table.payment_id = package_subscribe.payment_id where package_id='{$pid}' AND payment_table.member_id = '{$memid}';";
				$queryResult=$db->query($sqlgetdays);
				$rows = $queryResult ->fetch(PDO::FETCH_ASSOC);
				$paymentdays=$rows["payment_days"];
				
				$additionaldays=$rows["additional_days"];
				
				//echo $additionaldays;
				//echo $paymentdays;
				$all=$paymentdays+$additionaldays;
				//echo "start".$start;
				$end=date("Y-m-d H:i:s",strtotime("{$start} + {$all} day"));
					
				
				//$sqlUpdateStart="UPDATE payment_table a inner join package_subscribe b on a.payment_id = b.payment_id SET a.payment_startdate = '{$start}',a.payment_enddate=date_add('{$start}', interval payment_days day) WHERE (a.payment_enddate is NULL or a.payment_enddate='0000-00-00 00:00:00') AND b.package_id='{$this->pid}' AND b.member_id = '{$this->memid}';";
				$sqlUpdateStart="UPDATE payment_table a inner join package_subscribe b on a.payment_id = b.payment_id SET a.payment_startdate = '{$start}',a.payment_enddate=date_add('{$start}', interval payment_days+$additionaldays day) WHERE (a.payment_startdate is NULL or a.payment_startdate='0000-00-00 00:00:00') AND b.package_id='{$pid}' AND b.member_id = '{$memid}';";
				
				$affected_rows=$db->exec($sqlUpdateStart);
				
			 
				
				/*payment有变化判断为开卡了*/
				if($affected_rows>0){
					
					$sqlUpdatecommonStart="UPDATE member_user SET member_enddate='{$end}' WHERE (member_user.member_enddate is NULL or member_user.member_enddate ='0000-00-00 00:00:00' or member_enddate<'{$end}' ) AND member_user.member_id ='{$memid}';";
					$db->exec($sqlUpdatecommonStart);
					//echo "4.".$sqlUpdatecommonStart;
				}
				else{
					
				}
				//print_r(debug_backtrace());
				//print_r(error_get_last());
		
			}
			/*common 习练卡的只开member_user*/
			elseif(($classtype=='common') AND ($mlevel =='common' or (substr($mlevel,0,12)=='common_count')) ){
				$memadditdays=Member::getAdditionnalDays($memid);
				$sqlUpdatecommonStart="UPDATE member_user SET member_startdate = '{$start}',member_enddate= date_add('{$start}', interval member_days+{$memadditdays} day) WHERE (member_user.member_startdate is NULL or member_user.member_startdate ='0000-00-00 00:00:00') AND member_user.member_id ={$memid};";
				//echo "1.".$sqlUpdatecommonStart;
				$db->exec($sqlUpdatecommonStart);
				 
			}/*elseif(($classtype=='common') AND ($mlevel =='both_weekend') ){
				$memadditdays=Member::getAdditionnalDays($memid);
				//如果是周卡和月卡，days是在member_user 和 payment_table都设好了，两边都开就行
				$sqlUpdatecommonStart="UPDATE member_user SET member_startdate = '{$start}',member_enddate= date_add('{$start}', interval member_days+{$memadditdays[0]} day) WHERE (member_user.member_startdate is NULL or member_user.member_startdate ='0000-00-00 00:00:00') AND member_user.member_id ={$memid};";
				//echo "7.".$sqlUpdatecommonStart;
				$db->exec($sqlUpdatecommonStart);
				
				$sqlUpdateStart="UPDATE payment_table SET payment_startdate = '{$start}',payment_enddate=date_add('{$start}', interval payment_days+{$memadditdays[0]} day) WHERE (payment_enddate is NULL or payment_enddate='0000-00-00 00:00:00') AND member_id='{$memid}' order by payment_id asc LIMIT 1;";
				// echo "8.".$sqlUpdateStart;
				$db->exec($sqlUpdateStart); 
			}*/
			else{
				$sqlgetdays = "select payment_days,additional_days from payment_table where payment_table.member_id = '{$memid}' and (payment_enddate is NULL or payment_enddate='0000-00-00 00:00:00') Order by payment_id asc limit 1;";
				//echo $sqlgetdays; 
				$queryResult=$db->query($sqlgetdays);
				$rows = $queryResult ->fetch(PDO::FETCH_ASSOC);
				$paymentdays=$rows["payment_days"];
				$additionaldays=$rows["additional_days"];
				//echo "aday".$additionaldays;
				//echo "pday".$paymentdays;
				$all=$paymentdays+$additionaldays;
				//echo "start".$start;
				$end=date("Y-m-d H:i:s",strtotime("{$start} + {$all} day"));
				//echo "end".$end;
				//$sqlUpdateStart="UPDATE payment_table SET payment_startdate = '{$start}',payment_enddate=date_add('{$start}', interval payment_days+{$memadditdays[0]} day) WHERE (payment_enddate is NULL or payment_enddate='0000-00-00 00:00:00') AND member_id='{$memid}' order by payment_id asc LIMIT 1;";
				$sqlUpdateStart="UPDATE payment_table SET payment_startdate = '{$start}',payment_enddate='{$end}'  WHERE (payment_enddate is NULL or payment_enddate='0000-00-00 00:00:00') AND member_id='{$memid}' order by payment_id asc LIMIT 1;";
				//echo "8.".$sqlUpdateStart;
				$affected=$db->exec($sqlUpdateStart);
				//$memadditdays=Member::getAdditionnalDays($memid);
				/*如果是vip，选了common的课也算开卡，日期是payment+additional*/
				//$sqlUpdatecommonStart="UPDATE member_user SET member_startdate = '{$start}',member_enddate= date_add('{$start}', interval member_days+{$memadditdays[0]} day) WHERE (member_user.member_startdate is NULL or member_user.member_startdate ='0000-00-00 00:00:00') AND member_user.member_id ={$memid};";
				if($affected>0){
					$sqlUpdatecommonStart="UPDATE member_user SET member_startdate = '{$start}',member_enddate='{$end}' WHERE (member_user.member_startdate is NULL or member_user.member_startdate ='0000-00-00 00:00:00' or member_user.member_enddate<'{$end}') AND member_user.member_id ={$memid};";
					//echo "7.".$sqlUpdatecommonStart;
					$db->exec($sqlUpdatecommonStart);
				}
			
				
			}
			
		
		}
		
		
	}
	
	
$db = new PDO($dsn, $dbuser, $dbpass);		
 $sqlqiandaoall="  
SELECT register_record.member_id,member_level,rule_name,register_id,inner_id,class_type,
       starttime,endtime,register_record.arrange_id,is_attended,is_canceled,reviewed,class_description,class_design.package_id
	   FROM class_arrange left JOIN register_record
on register_record.arrange_id= class_arrange.arrange_id 
      inner JOIN class_design on class_design.class_id = class_arrange.class_id 
left
JOIN member_user on  member_user.member_id=register_record.member_id 
where register_record.is_canceled=0 AND register_record.is_attended<1000 AND class_arrange.arrangedate ='{$yestoday}'";
		if(!isset($_SESSION['QIANDAO_DAY'])){
			
			$_SESSION['QIANDAO_DAY']="0000-00-00";
		}
		if($_SESSION["adminuserid"]==1 and $_SESSION['QIANDAO_DAY']!=date("Y-m-d",strtotime("-1 day"))){
			$db->query("set names UTF8;");
			$resqiandaoall = $db->query($sqlqiandaoall);
			$resqiandaoall->setFetchMode(PDO::FETCH_ASSOC);
			$allrows=$resqiandaoall->fetchAll();
			foreach($allrows as $rowqd){
			
				 
				Registerrecord::markattend($rowqd['register_id']);
			 
				
				
				if($rowqd['package_id']=='set1' AND $rowqd['rule_name']='common_count_both' AND $rowqd['member_id']!=373){
					Registerrecord::addMembeCount2($rowqd['member_id']);
				}
				else{
					Registerrecord::addMembeCount($rowqd['member_id']);
				}
			
				//开卡
				Registerrecord::startcard($rowqd['member_id'],$rowqd['package_id'],$rowqd['arrange_id'],$rowqd['class_type'],$rowqd['member_level']);
				
				if($rowqd['class_type']=='package')
				{
					Registerrecord::addInnerRecored($rowqd['member_id'],$rowqd['package_id'],$rowqd['inner_id']);
					Registerrecord::addPackageSub($rowqd['member_id'],$rowqd['package_id']);
						
				}
				if($rowqd['class_type']=='set'){
					Registerrecord::addPackageSub($rowqd['member_id'],$rowqd['package_id']);
				}
				
			 
			}
				
			
			
			 
			//print_r(error_get_last());
			//print_r(debug_backtrace());
		$_SESSION['QIANDAO_DAY']=date("Y-m-d",strtotime("-1 day"));	
	}
	header("Location:account.php");
	?>