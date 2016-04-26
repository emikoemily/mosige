<?php
	//if($_POST["op"] == "更    新") {
	///	include("do_edit_account.inc.php");
	//	exit;
	//}
	session_start();
	include("header.inc.php");
	include("dbconnect.inc.php");
	if($_GET["id"]!="" && is_numeric($_GET["id"])) {
		$arrid = $_GET["id"];
	
	$sql = "select * from class_arrange where arrange_id={$arrid}";
	//echo $sql;
	$db->query('set names UTF8');
	$res = $db->query($sql);
	$row = $res->fetch();
	extract($row);
	echo $row['arrangedate'];
	echo $row['starttime'];
	
	}else {
		$arrid = "";
	}
	
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


<li><a href="add_account.php">注册新会员</a></li>
<li ><a href="view_account.php">查看会员信息</a></li>
<li ><a href="manage_class.php">课程管理</a></li>
<li ><a href="manage_leave.php">请假管理</a></li>
<li  ><a href="manage_attend.php"  target="blank">签到表</a></li>

<li ><a href="manage_export.php">导入导出</a></li>
<li ><a href="member_class.php">会员上课情况</a></li>
<li ><a href="manage_rm.php">跑步机管理</a></li>
</ul>

<div>
<a href="manage_class.php">返回</a>
<table>
 <thead><tr><th> </th>
  <th>日期</th>
 <th>上课时间</th>
 <th >下课时间</th>
 <th width='50%' >课程名称及描述</th>
 <th>老师</th>
 <th>会员人数</th>
 <th>团购体验人数</th>
 
  <th>教室</th>
 <th>操作</th> </tr>
 </thead>
 
<tbody>
<form name="addclass" action="do_edit_arrange.inc.php" method="post">

<?php
		echo "<input type='hidden' name='arrangeid' value={$arrid} >";
		echo "<tr ><td></td><td><input type='text' name='arrangedate'  value={$row['arrangedate']}></td>";
		echo "<td><input type='text' name='starttime'  value='{$row['starttime']}'>";
		 
		
		echo "<td><input type='text' name='endtime'  value='{$row['endtime']}'>";
		 
		
		 
	   echo "<td><select name='classid' > ";
	   
		$sql1 = "select class_id,class_name,inner_id,class_description,package_id,for_ref from class_design  ORDER BY class_name,inner_id ";
	    $db->query('set names UTF8');
	    $res1 = $db->query($sql1);
	    $rows1=  $res1->fetchAll();
	    foreach($rows1 as $rowc) {	
		
        echo "<option value='{$rowc['class_id']},{$rowc['for_ref']}'";
		
		if($rowc['class_id']==$_GET['class']){
			echo " selected='selected'";
		}
		
		
		echo ">{$rowc['class_name']}-{$rowc['inner_id']}-{$rowc['class_description']} </option> ";
	//echo "<input type='hidden' name='forref'  value='{$row['for_ref']}'/>";
	
	}
        echo "</select>";
		echo "</td>";
		
		
		 echo "<td><select name='teacherid' value='{$row['teacher_id']}'> ";
		$sqlteacher = "select teacher_id,teacher_name from teacher_table";
	    $db->query('set names UTF8'); 
	    $resteacher = $db->query($sqlteacher);
	    $trows=$resteacher->fetchAll();
	    foreach($trows as $rowteacher ) {
	
	    
        echo "<option value='{$rowteacher['teacher_id']}' ";
		if($rowteacher['teacher_id']==$row['teacher_id'])
		{echo " selected='selected'";}
		echo ">{$rowteacher['teacher_name']}</option>";
	}
        echo "</select>";
		echo "</td>";
		
		echo "<td><input type='text' name='max'  value={$row['maxposition']}></td>";
		echo "<td><input type='text' name='trymax'  value={$row['try_maxposition']}></td>";
		echo "<td><input type='text' name='classroom'  value={$row['classroom']}></td>";
        if($_SESSION["adminuserid"]=="1" or $_SESSION["adminuserid"]=="2" or $_SESSION["adminuserid"]=="3" ) {
			echo "<td><input type='submit' name='submitclass' value='更改'></input> <br />
		</td></tr>";}
?>
</form>

</tbody>
</table>

</div>
 </body>
</html>
