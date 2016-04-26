<?php
	 
	session_start();
	include("header.inc.php");
	//include("dbconnect.inc.php");
	 
?>
 <table id="content">
  <tr>
   <td id="sidebar-left"><div class="block block-user" id="block-user-1">
  <h2 class="title"><?php echo $_SESSION["username"]; ?></h2>
 <div class="content">
<ul class="menu">
<?php
include ("account_menu.php");
include ("entity/Member.php");
?>
<?php


echo "<li class='leaf'><a href='account.php' class='active'>会员管理</a></li>";
echo "<li class='leaf'><a href='manage_attend.php' target='blank'>签到表</a></li>";
 

if($_SESSION["adminuserid"]=="1" ) {
	echo "<li class='leaf'><a href='view_review.php' >数据统计</a></li>";
	echo "<li class='leaf'><a href='register.php' class='active'>注册后台用户</a></li>";

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
	   
echo "<li  ><a href='add_account.php'>注册新会员</a></li>";   
echo "<li ><a href='view_account.php'>查看会员信息</a></li>";
echo "<li ><a href='manage_class.php'>课程管理</a></li>";
echo "<li ><a href='manage_leave.php'>请假管理</a></li>";
echo "<li  ><a href='manage_attend.php'  target='blank'>签到表</a></li>";
echo "<li class='active'><a href='manage_export.php'>导入导出</a></li>";
echo "<li ><a href='member_class.php'>会员上课情况</a></li>";
echo "<li ><a href='manage_rm.php'>跑步机管理</a></li>";

	   
   }elseif($_SESSION["adminuserid"]=="2"){
	   
	echo	"<li  class='active'><a href='add_account.php'>注册新会员</a></li>";   
echo "<li ><a href='view_account.php'>查看会员信息</a></li>";
echo "<li ><a href='manage_class.php'>课程管理</a></li>";
echo "<li ><a href='manage_leave.php'>请假管理</a></li>";
echo "<li  ><a href='manage_attend.php'  target='blank'>签到表</a></li>";
echo "<li ><a href='member_class.php'>会员上课情况</a></li>";
echo "<li ><a href='manage_rm.php'>跑步机管理</a></li>";   
   }
   elseif($_SESSION["adminuserid"]=="3"){

echo "<li ><a href='manage_class.php'>课程管理</a></li>"; 
echo "<li  ><a href='manage_attend.php'  target='blank'>签到表</a></li>";
echo "<li class='active'><a href='manage_export.php'>导入导出</a></li>";
	   
   }elseif($_SESSION["adminuserid"]=="4"){

echo "<li class='active'><a href='manage_class.php'>课程管理</a></li>"; 
echo "<li   ><a href='manage_attend.php'  target='blank'>签到表</a></li>";
 	   
	   
   }

?>


</ul>
<br>
1.生成全部数据：
 <a href="export.php">点此下载全部会员上课数据</a>
 <br>
2.按日期生成上课数据<br>
 <form action="exportwithdate.php"  method="get" >
 开课日期 起始：<input type="date" name="checkdate" /><br /><br />

开课日期 截止：<input type="date" name="checkdate_end"  /><br /><br />
 <input type="submit" value="下载"  class="form-submit" />
 </form>

 
 3.课程包会员上课情况(统计开卡以来至今的上课次数):
 
  <form id="memberattenddata" action="exportmemberattend.php"  method="get" >
 会员名称： 
<?php  
 

echo "<select id='filter' name='member_id'> ";
		$allname=Member::getAllPackageMember();
		foreach($allname as $row){
			echo "<option value='{$row['member_id']}'>{$row['member_name']} </option> ";
			 
		}
        echo "</select>";
		

        
		?>
		
		
 开课日期 起始：<input type="date" name="checkdate3" /><br /><br />

开课日期 截止：<input type="date" name="checkdate_end3" /><br /><br />
 <input type="submit" value="下载"  class="form-submit" />
 </form>


4.会员上课记录（含星期几）

 <form id="memberattenddata" action="exportmemberitems.php"  method="get" >
 会员名称： 
<?php  
 

echo "<select id='filter2' name='member_id'> ";
		$allname2=Member::getAllPackageMember();
		foreach($allname2 as $row2){
			echo "<option value='{$row2['member_id']}'>{$row2['member_name']} </option> ";
			 
		}
        echo "</select>";
		

        
		?>
		
		
 开课日期 起始：<input type="date" name="checkdate4" /><br /><br />

开课日期 截止：<input type="date" name="checkdate_end4" /><br /><br />
 <input type="submit" value="下载"  class="form-submit" />
 </form>
 
5。更多report，请点击 
 <a href="http://yoga.ibreezee.com/report_project/cv1ctb.php" target="blank">这里</a>
 
 

 </br> </br> </br> </br>
 
 6.下载评语

 <form id="teacherreviewdata" action="exportteacherreview.php"  method="get" >

		
 开课日期 起始：<input type="date" name="checkdate6" /><br /><br />

开课日期 截止：<input type="date" name="checkdate_end6" /><br /><br />
 <input type="submit" value="下载"  class="form-submit" />
 </form>
 </td>
 
 </tr>
 
 
 
 </table>
 </body>
</html>
