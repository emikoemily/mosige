<?php
	//if($_POST["op"] == "更    新") {
	//	include("do_member_class.inc.php");
	//	exit;
	//}
	session_start();
	include("header.inc.php");
	include("dbconnect.inc.php");
	 include("entity/Leave.php");
	//if($_GET["id"]!="" && is_numeric($_GET["id"])) {
		//$id = $_GET["id"];
	//}
	$sql = "SELECT * FROM yoga_lu.member_user where member_isleave != 0"; 
	$db->query('set names UTF8');
	$res = $db->query($sql);
	//$row = mysqli_fetch_array($res);
	//extract($row);
	
	//echo $sql;
	
	
	
	
	//echo $row['member_email'];
?>
 <table id="content">
  <tr>
   <td id="sidebar-left"><div class="block block-user" id="block-user-1">
  <h2 class="title"><?php echo $_SESSION["username"]; ?></h2>
 <div class="content">
<ul class="menu">
<li class="leaf"><a href="account.php" class="active">会员管理</a></li>
<li class="leaf"><a href="manage_attend.php"  target="blank" target="_blank">签到表</a></li>
<li class="leaf"><a href="view_review.php" >数据统计</a></li>
</ul>
</div>
</div>
</td>
   <td id="main">
<div class="breadcrumb"><a href="./">主页</a> &raquo; <a href="./">用户帐号</a></div><h2><?php echo $_SESSION["username"]; ?></h2>

<ul class="tabs primary">


<?php
   if($_SESSION["adminuserid"]=="1") {//1:admin 2:frontdesk 3:teacher
	   
echo "<li ><a href='add_account.php'>注册新会员</a></li>";   
echo "<li ><a href='view_account.php'>查看会员信息</a></li>";
echo "<li ><a href='manage_class.php'>课程管理</a></li>";
echo "<li  class='active'><a href='manage_leave.php'>请假管理</a></li>";
echo "<li  ><a href='manage_attend.php'>签到表</a></li>";
echo "<li ><a href='manage_export.php'>导入导出</a></li>";
echo "<li ><a href='member_class.php'>会员上课情况</a></li>";
echo "<li ><a href='manage_rm.php'>跑步机管理</a></li>";

	   
   }elseif($_SESSION["adminuserid"]=="2"){
	   
	echo	"<li  class='active'><a href='add_account.php'>注册新会员</a></li>";   
echo "<li ><a href='view_account.php'>查看会员信息</a></li>";
echo "<li ><a href='manage_class.php'>课程管理</a></li>";
echo "<li ><a href='manage_leave.php'>请假管理</a></li>";
echo "<li  ><a href='manage_attend.php'>签到表</a></li>";
echo "<li ><a href='member_class.php'>会员上课情况</a></li>";
echo "<li ><a href='manage_rm.php'>跑步机管理</a></li>";   
   }
   elseif($_SESSION["adminuserid"]=="3"){

echo "<li ><a href='manage_class.php'>课程管理</a></li>"; 
echo "<li  ><a href='manage_attend.php'>签到表</a></li>";
 	   
	   
   }elseif($_SESSION["adminuserid"]=="4"){

echo "<li class='active'><a href='manage_class.php'>课程管理</a></li>"; 
echo "<li   ><a href='manage_attend.php'>签到表</a></li>";
 	   
	   
   }

?>

</ul>
<form method=post action="do_select_class.inc.php" name="form1">
<?php
if($_GET["id"]!="" && $_SESSION["adminuserid"]==1) {
	echo "<input type='hidden' name='mid' value={$_GET['id']} />";
}
?>

<table>
 <thead><tr>
 
 
 <th>申请请假的会员</th>
  <th>申请内容</th>
  <th> 状态</th>
  <th> </th>
 </tr>
 </thead>
<tbody>


<?php //$i=1;
	$res->setFetchMode(PDO::FETCH_ASSOC);
	$rows=$res->fetchAll();
	foreach($rows as $row) {		
		$r=Leave::getLeaveReason($row['member_id']);
		$s=Leave::getLeaveStart($row['member_id']);
		$e=Leave::getLeaveEnd($row['member_id']);
	
	switch($row["member_isleave"]) {
		case	"1"	:
			$level = "请假中（还未登录系统）";
			break;
		case	"2"	:
			$level = "请假申请已提交 待批";
			break;
		case	"3"	:
			$level = "请假已批准，还未到请假时间";
			break;
		case	"4"	:
			$level = "请假结束了";
			break;		
		default		:
			$level = "未设定";
			break;
	}
	
	
    echo "<tr >";

	echo "<td>{$row['member_name']}</td>";
	echo "<td>{$r},{$s}-{$e}</td>";
	echo "<td>{ $level}</td>";
	if($row["member_isleave"]==2){
	echo "<td><a href='do_agree_leave.php?mid={$row['member_id']}'>同意</a><br /></td> ";
	echo "<td><a href='do_not_agree_leave.php?mid={$row['member_id']}'>不同意</a><br /></td> ";
	}else{
		echo "<td> </td>";
		echo "<td> </td>";
		
	}
    echo "</tr > ";

		//echo "<td class='active'>{$row['reg_time']}</td>";
		// $i=$i+1;
	}
?>
</tbody></table>

<?php 
echo "=======================================";
echo "<br>";
echo "请假记录";
echo "<br>";

$sql_leaveold="SELECT `member_name` ,`leave_startdate`,`leave_enddate`,`isinprogress`,`leave_reason` FROM `apply_leave` inner join member_user on apply_leave.member_id=member_user.member_id
 WHERE isinprogress!=4";
$db->query('set names UTF8');
$res_leaveold = $db->query($sql_leaveold);
$rows_leaveold=$res_leaveold->fetchAll();
foreach($rows_leaveold as $row){
	echo $row["member_name"];
	echo "     ";
	echo $row["leave_startdate"];
	echo " 至 ";
	echo $row["leave_enddate"];
	echo "     ";
	
	switch($row["isinprogress"]) {
		case	"1"	:
			$level = "请假中";
			break;
		case	"2"	:
			$level = "申请已提交 待批";
			break;
		case	"3"	:
			$level = "已结束";
			break;
		case	"4"	:
			$level = "被拒绝";
			break;
		default		:
			$level = "未设定";
			break;
	}
	echo $level;
	echo "     ";
	echo "(".$row["leave_reason"].")";
	echo "<br>";
	
}

?>
<!-- end content -->
   </td>
  </tr>
 </table>

 </body>
</html>
