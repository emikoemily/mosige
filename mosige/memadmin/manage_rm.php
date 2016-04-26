<?php
	if($_POST["op"] == "注册新帐号") {
		include("do_reg_account.inc.php");
		exit;
	}
	session_start();
	include("header.inc.php");
	include("dbconnect.inc.php");
	if($_GET["id"]!="" && $_SESSION["adminuserid"]==1 && is_numeric($_GET["id"])) {
		$id = $_GET["id"];
	}else {
		$id = $_SESSION["adminuserid"];
	}
    $sql = "SELECT idrunning_arrange,start_hour,is_reg,reg_mins,member_name FROM yoga_lu.running_arrange inner join member_user on running_arrange.member_id=member_user.member_id";
	$db->query('set names UTF8');
	$res = $db->query($sql);
	//$row = mysqli_fetch_array($res);
	//extract($row);
	
	//echo $sql;
?>
 <table id="content">
  <tr>
   <td id="sidebar-left"><div class="block block-user" id="block-user-1">
  <h2 class="title"><?php echo $_SESSION["username"]; ?></h2>
 <div class="content">
<ul class="menu">
<li class="leaf"><a href="account.php" class="active">会员管理</a></li>
<li class="leaf"><a href="manage_attend.php"  target="blank" target="_blank">签到表</a></li>
<?php
if($_SESSION["adminuserid"]=="1" ) {
echo "<li class='leaf'><a href='view_review.php' >数据统计</a></li>";
}
?>
<li class="leaf"><a href="logout.php">注销登录</a></li>

</ul>
</div>
</div>
</td>
   <td id="main">
<div class="breadcrumb"><a href="./">主页</a> &raquo; <a href="./">用户帐号</a></div><h2><?php echo $_SESSION["username"]; ?></h2><ul class="tabs primary">

<?php
   if($_SESSION["adminuserid"]=="1") {//1:admin 2:frontdesk 3:teacher
	   
echo "<li ><a href='add_account.php'>注册新会员</a></li>";   
echo "<li ><a href='view_account.php'>查看会员信息</a></li>";
echo "<li ><a href='manage_class.php'>课程管理</a></li>";
echo "<li ><a href='manage_leave.php'>请假管理</a></li>";
echo "<li  ><a href='manage_attend.php'>签到表</a></li>";
echo "<li ><a href='manage_export.php'>导入导出</a></li>";
echo "<li ><a href='member_class.php'>会员上课情况</a></li>";
echo "<li class='active' ><a href='manage_rm.php'>跑步机管理</a></li>";

	   
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
 
<table>
 <thead><tr>
 
 
 <th>时间</th>
  <th>预约跑步长度</th>
  <th>会员</th>
  <th>操作</th>
 </tr>
 </thead>
<tbody>


<?php //$i=1;
	$res->setFetchMode(PDO::FETCH_ASSOC);
	$rows=$res->fetchAll();
	foreach($rows as $row) {		
	
		
    echo "<tr >";
echo "<td>{$row['start_hour']}</td>";
	echo "<td>{$row['reg_mins']}</td>";
	echo "<td>{$row['member_name']}</td>";
	echo "<td><a href='do_clear_rm.php?rmid={$row['idrunning_arrange']}'>取消预约</a><br /></td>";
	 
    echo "</tr > ";

		//echo "<td class='active'>{$row['reg_time']}</td>";
		// $i=$i+1;
	}
?>
</tbody></table> 
<a href='do_clear_rm.php'>清零</a><br />

 </body>
</html>
