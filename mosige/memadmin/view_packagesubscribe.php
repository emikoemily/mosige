<html>
<head>

<meta name="viewport" content="width=device-width,initial-scale=1" charset="utf-8">  
<meta http-equiv="content-type" content="text/html;charset=utf-8">
</head>
<body>
<?php

include("entity/Package.php");
$pid=$_GET['pid'];
$memberperpackage=Package::listMemberPerPackage($pid);

$i=1;
 ?>
 
 <table>
 <tr><th></th><th>课包名</th><th>会员</th><th>进度（已去重复）</th></tr>
 <?php 
foreach($memberperpackage as $value){
	echo "<tr>";
	echo "<td>";
	echo $i;
	echo "</td>";
	echo "<td>";
	echo $value['package_name']; 
	echo "&nbsp &nbsp &nbsp";
	echo "</td>";
	//print_r(debug_backtrace());
	echo "<td>";
	echo $value['member_name'];
	echo "&nbsp &nbsp &nbsp";
	echo "</td>";
	//echo ;
	//echo $pid;
	echo "<td>";
	$progress=Package::findprogress($value['member_id'], $pid);
	echo $progress[0][0];
	foreach($progress as $row){
		echo $row['class_description']."&nbsp,";
		
	}
	echo "</td>";
	
	
	
	
	echo "</br>";
	$i++;
	echo "</tr>";
}

?>
</table>
</body>
</table>
</html>