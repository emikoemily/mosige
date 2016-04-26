<?php
	
	session_start();
	include("header.inc.php");
	include("dbconnect.inc.php");


	if($_GET){
	$start=$_GET['startdate'];
	$end=$_GET['enddate'];
	$packageid=$_GET['packageid'];
	$where="";
    if($start!=''){
		
		$where=$where. " AND arrangedate >='{$start}' ";
	}
	if($end!=""){
		
		$where=$where. " AND arrangedate <='{$end}' ";
	}
 if($packageid!=""){
		
		$where=$where. " AND package_id ='{$packageid}' ";
	}
	
	$sql = "SELECT starttime,count(*) as NumberOfPeople,sum(case when is_attended!=0 then 1 else 0 end) 
as NumberOfAttended,sum(case when is_canceled!=0 then 1 else 0 end) as NumberOfCanceled 
FROM `register_record` INNER JOIN class_arrange on register_record.arrange_id = class_arrange.arrange_id 
INNER JOIN class_design on class_design.class_id = class_arrange.class_id where member_id!=73 
and member_id!=76 and member_id!=61 and member_id!=102 and member_id!=60 and member_id!=48 and member_id!=256 ".$where."
GROUP by class_arrange.starttime ";//GROUP by class_arrange.class_id
	 echo $sql;
	$db->query('set names UTF8');
	$res = $db->query($sql);
		
		

		
	} 
	
	//echo $row['member_email'];
?>
 <table id="content">
  <tr>
   <td id="sidebar-left"><div class="block block-user" id="block-user-1">
  <h2 class="title"><?php echo $_SESSION["username"]; ?></h2>
 <div class="content">
<ul class="menu">
<?php
include ("view_review_menu.php");
?>
<li class="leaf"><a href="logout.php">注销登录</a></li>

</ul>
</div>
</div>
</td>
   <td id="main">
<div class="breadcrumb"><a href="./">主页</a> &raquo; <a href="./">用户帐号</a></div><h2><?php echo $_SESSION["username"]; ?></h2><ul class="tabs primary">


<li><a href="view_review.php">课程评价</a></li>
<li  class="active" ><a href="statistics.php">人数统计</a></li>
</ul>
<form action="statistics.php"  method="get" >
</script>
<div><div class="container-inline"><div class="form-item">
 <label >条件: </label>
起始日期：<input type="date" name="startdate"  value="<?php echo $_GET['startdate']; ?>" /><br />
结束日期：<input type="date" name="enddate"  value="<?php echo $_GET['enddate']; ?>" /><br />

课程：<?php
  echo "<select name='packageid'> ";
   
		$sql1 = "select distinct class_design.package_id,class_name,class_type from class_design left join package_design on class_design.package_id =package_design.package_id ORDER BY class_name ";
	    $db->query('set names UTF8');
	    $res1 = $db->query($sql1);
		
		echo "<option value=''></option>";
		$res1->setFetchMode(PDO::FETCH_ASSOC);
		$rows = $res1->fetchAll();
	    foreach($rows as $row) {	
        echo "<option value='{$row['package_id']}'>{$row['class_name']}{$row['class_type']}</option> ";
	//echo "<input type='hidden' name='forref'  value='{$row['for_ref']}'/>";
	
	}
	
        echo "</select>";
		echo "";
		
		
		
 ?>
  

</br>
<input type="submit" value="检    索"  class="form-submit" />
</div>
</div></form>

<table cellspacing="10%" cellpadding="10">
 <thead><tr>
 <th>时间</th>
 <th>总预约人次</th>
 <th>已参加的人次</th>
 <th>取消的人次</th>
 </tr>
 </thead>
<tbody>

<!-- begin content --><?php
$res->setFetchMode(PDO::FETCH_ASSOC);
$rows = $res->fetchAll();
	foreach($rows as $row){
		echo "	<tr>";
		echo "<td>";
		//		echo $row["class_name"].$row["inner_id"].$row["class_description"];
		echo $row["starttime"];
		echo "</td>";
		 
		echo "<td>";		
		echo $row["NumberOfPeople"];
		echo "</td>";
		 echo "<td>";		
		echo $row["NumberOfAttended"];
		echo "</td>";
		echo "<td>";		
		echo $row["NumberOfCanceled"];
		echo "</td>";
		 
		echo "</tr>";
		//echo "</br>";
	}



?>

<!-- end content -->
   </td>
  </tr>
 </table>

 </body>
</html>
