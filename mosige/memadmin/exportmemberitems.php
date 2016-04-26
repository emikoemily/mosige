<?php

date_default_timezone_set('PRC');
include("do_export_classattend.php");
include("entity/Member.php");
$memid=$_GET["member_id"];
//$memname=$_GET["member_name"];
$startdate=$_GET["checkdate4"];
$enddate=$_GET["checkdate_end4"];
if($startdate==""){

	$startdate="2015-09-01";

}
if($enddate==""){

	$enddate=date('Y-m-d');

}
$dt=date('Ymd');
$mname="";
$name=Member::getNamebyID($memid);
$mname=$name[0][0];
header('Content-type: text/html; charset=utf-8');
header("Content-type:application/vnd.ms-excel;charset=UTF-8");
header("Content-Disposition:filename=课程卡会员上课记录含星期几_{$startdate}_{$enddate}_{$mname}.xls");
//输出的表格名称
echo "会员\t";
echo "课程包名\t";
echo "内部序号\t";
echo "级别\t";
echo "上课日期\t";
echo "星期几\t";
echo "上课时间\t";
echo "下课时间\t\n";
//这是表格头字段 加\T就是换格,加\T\N就是结束这一行,换行的意思

$result=Exportdata::exportMemberAttendItemswithdate($memid,$startdate,$enddate);
//$result=Exportdata::exportMemberPackageAttendCount(91,"2015-10-01","2015-12-01");
foreach($result as $row)
{
	echo $row['0']."\t";
	echo $row['1']."\t";
	echo $row['2']."\t";
	echo $row['3']."\t";
	echo $row['4']."\t";
	echo $row['5']."\t";
	echo $row['6']."\t";
	echo $row['7']."\t\n";



}

//print_r(debug_backtrace());
//print_r(error_get_last());
?>
