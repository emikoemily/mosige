<?php
	session_start();
	if(!$_SESSION["adminuserid"]) header("Location:index.php");
	include("header.inc.php");
	include("dbconnect.inc.php");
	$id = $_SESSION["adminuserid"];
	$sql = "select * from users where id={$id}";
	$res = $db->query($sql);
	$res->setFetchMode(PDO::FETCH_ASSOC);
	$row = $res->fetch();
	$reg_time = $row["reg_time"];
	$photo = $row["photo"];
	if($photo == "") {
		$photo = "logo.jpg";
	}
?>
 <table id="content">
  <tr>
   <td id="sidebar-left"><div class="block block-user" id="block-user-1">
  <h2 class="title"><?php echo $_SESSION["username"]; ?></h2>
 <div class="content">
<ul class="menu">

<?php
include ("account_menu.php");
?>
<li class="leaf"><a href="logout.php">注销登录</a></li>
<?php
if($_SESSION["adminuserid"]=="1" ) {
echo "<li class='leaf'><a href='view_review.php' >数据统计</a></li>";
echo "<li class='leaf'><a href='register.php' class='active'>注册后台用户</a></li>";

}
?>
</ul>
</div>
</div>
</td>
   <td id="main">
<div class="breadcrumb"><a href="./">主页</a> &raquo; <a href="./">用户帐号</a></div><h2><?php echo $_SESSION["username"]; ?></h2><ul class="tabs primary">
<?php
   if($_SESSION["adminuserid"]=="1") {//1:admin 2:frontdesk 3:teacher
	   
echo "<li ><a href='add_account.php'>注册新会员</a></li>";   
echo "<li ><a href='view_account.php'  class='active'>查看会员信息</a></li>";
echo "<li ><a href='manage_class.php'>课程管理</a></li>";
echo "<li ><a href='manage_leave.php'>请假管理</a></li>";
echo "<li><a href='manage_attend.php' target='_blank'>签到表</a></li>";
echo "<li><a href='manage_export.php'>导入导出</a></li>";
echo "<li ><a href='member_class.php'>会员上课情况</a></li>";
echo "<li ><a href='manage_rm.php'>跑步机管理</a></li>";

	   
   }elseif($_SESSION["adminuserid"]=="2"){
	   
	echo	"<li ><a href='add_account.php'>注册新会员</a></li>";   
echo "<li ><a href='view_account.php'>查看会员信息</a></li>";
echo "<li ><a href='manage_class.php'>课程管理</a></li>";
echo "<li ><a href='manage_leave.php'>请假管理</a></li>";
echo "<li ><a href='manage_attend.php'  target='_blank'>签到表</a></li>";
echo "<li ><a href='member_class.php'>会员上课情况</a></li>";
echo "<li ><a href='manage_rm.php'>跑步机管理</a></li>";   
   }
   elseif($_SESSION["adminuserid"]=="3"){

echo "<li><a href='manage_class.php'>课程管理</a></li>"; 
echo "<li   ><a href='manage_attend.php'  target='_blank'>签到表</a></li>";
echo "<li ><a href='manage_export.php'>导入导出</a></li>";
	   
   }
elseif($_SESSION["adminuserid"]=="4"){
	echo	"<li ><a href='add_account.php'>注册新会员</a></li>";
	echo "<li ><a href='view_account.php'>查看会员信息</a></li>";
echo "<li ><a href='manage_class.php'>课程管理</a></li>"; 
echo "<li   ><a href='manage_attend.php'  target='_blank'>签到表</a></li>";
 	   
	   
   }
?>
</ul>
<!-- begin content -->
<div class="profile"><h2 class="title">莫圣瑜伽生活管后台系统</h2>
<dl><dt class="user-member"></dt><dd class="user-member"><a href="photo.php"><img src="<?php echo $photo; ?>" border="0" /></a></dd></dl>
<!--<dl><dt class="user-member">注册时间</dt><dd class="user-member"><?php echo $reg_time; echo "已签到日期".$_SESSION['QIANDAO_DAY'];?></dd></dl></div>-->
<!-- end content -->
   </td>
  </tr>
 </table>

 </body>
</html>
