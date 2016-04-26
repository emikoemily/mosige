
<?php
include("do_export_classattend.php");
$dt=date('Ymd');
header('Content-type: text/html; charset=utf-8');
header("Content-type:application/vnd.ms-excel;charset=UTF-8");
header("Content-Disposition:filename=ClassAttendData_{$dt}.xls"); //输出的表格名称
		echo "class_name\t";
		echo "date\t";
		echo "time\t";
		echo "class_description\t";
		echo "member_name\t";
		echo "attend\t\n";
		//这是表格头字段 加\T就是换格,加\T\N就是结束这一行,换行的意思		 
		
		$result=Exportdata::exportAllMemberAttendData();
		foreach($result as $row)
		 {
			echo $row['0']."\t";
			echo $row['1']."\t";
			echo $row['2']."\t";
			echo $row['3']."\t";
			echo $row['4']."\t";
			echo $row['5']."\t\n";
			
			
			
		 }
		
		 print_r(debug_backtrace());
		 print_r(error_get_last());
?>
