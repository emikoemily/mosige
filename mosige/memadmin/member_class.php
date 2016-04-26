<?php
	if($_POST["op"] == "更    新") {
		include("do_member_class.inc.php");
		exit;
	}
	session_start();
	include("header.inc.php");
	include("dbconnect.inc.php");
	include("entity/Member.php");
	$page=isset($_GET['page'])?intval($_GET['page']):1;        //这句就是获取page=18中的page的值，假如不存在page，那么页数就是1。  
	$num=50;                                      //每页显示10条数据  

	
	
	if($_GET["id"]!="" && is_numeric($_GET["id"])) {
		$id = $_GET["id"];
	
	$sql = "SELECT * FROM yoga_lu.package_design inner join 
	package_subscribe on package_design.package_id =package_subscribe.package_id inner join member_user where package_subscribe.member_id=member_user.member_id
	AND package_subscribe.member_id={$id}";
	$db->query('set names UTF8');
	$res = $db->query($sql);
	$res->setFetchMode(PDO::FETCH_ASSOC);
	$rows = $res->fetchAll();
	}else {
			$sql_all = "SELECT * FROM yoga_lu.package_design inner join 
	package_subscribe on package_design.package_id =package_subscribe.package_id inner join member_user where package_subscribe.member_id=member_user.member_id ";
	$db->query('set names UTF8');
	$res = $db->query($sql_all);
	$res->setFetchMode(PDO::FETCH_ASSOC);
	$rows = $res->fetchAll();
	
	$total=count($rows); //查询数据的总数  
    $pagenum=ceil($total/$num); 
	//$info=$db->query($sql); 
	 

	
	}
	
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
echo "<li ><a href='register.php' class='active'>注册后台用户</a></li>";
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
echo "<li  ><a href='manage_attend.php'  target='blank'>签到表</a></li>";
echo "<li ><a href='manage_class.php'>导入导出</a></li>";
echo "<li  class='active' ><a href='member_class.php'>会员上课情况</a></li>";
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
 	   
	   
   }elseif($_SESSION["adminuserid"]=="4"){

echo "<li class='active'><a href='manage_class.php'>课程管理</a></li>"; 
echo "<li   ><a href='manage_attend.php'>签到表</a></li>";
 	   
	   
   }

?>

</ul>
会员名称： 
<form method='get' action="member_class.php">

<?php  

echo "<select id='filter2' name='id'> ";
		$allname2=Member::getAllPackageMember();
		foreach($allname2 as $row2){
			echo "<option value='{$row2['member_id']}'>{$row2['member_name']} </option> ";
			 
		}
        echo "</select>";
		
		?>
		<input type='submit'>
</form>

<?php

echo "<a href=http://yoga.ibreezee.com/memadmin/change_package_date.php?id={$_GET['id']}>【查看课包有效期】</a>";
if($_GET["id"]!="") {
	echo "<input type='hidden' name='id' value='{$_GET['id']}' />";
}
?>


<table>
 <thead><tr><th> </th>
 <th>用户名</th>
 <th >packageid</th>
 <th>课程名</th>
 <th>总次数(限单项课)</th>
  <th>已上次数</th>
   <th>具体</th>
    <th>课包状态</th>
    <th>操作</th>
 </tr>
 </thead>
<tbody>
 
<?php $i=1;

function findprogress($db,$mid,$pid){
	$sqlprogress="SELECT class_arrange.class_id, class_name,class_description 
	FROM class_arrange 
	inner join yoga_lu.register_record ON register_record.arrange_id = class_arrange.arrange_id
	inner join class_design ON class_design.class_id=class_arrange.class_id
	 where  
	is_attended!=0
	AND
	class_design.package_id='{$pid}'
	AND
    register_record.member_id={$mid}
	
	 ";
	$j=1;
	//echo  $sqlprogress;
	 $prgress=$db->query($sqlprogress);
	 $show="";
	 
	 $prgress->setFetchMode(PDO::FETCH_ASSOC);
	 $rowprgresss = $prgress->fetchAll();
	 foreach($rowprgresss as $row){
		 $show=$show.$row['class_description']."-.{$j}.,</br>";
		 $j=$j+1;
	 }
	   
	 return $show;
}


	$status;
	//$res->setFetchMode(PDO::FETCH_ASSOC);
	//$rows = $res->fetchAll();
	foreach($rows as $row) {		
	   
		switch($row["is_finished"]) {
			case	"0"	:
				$status = "正常";
				break;
			case	"1"	:
				$status = "课程已结束";
				break;			 
			default		:
				$status = "未设定";
				break;
		}
		 
		echo "<tr ><td>$i   </td>";
		echo "<td>{$row['member_cardid']}   {$row['member_name']}  （{$row['member_id']}）</td>";
		echo "<td>{$row['package_id']}</td>";
		echo "<td >{$row['package_name']}</td>";
 		echo "<td>{$row['set_max']}</td>";
		 echo "<td>{$row['package_attended']}</td>";
		 echo "<td>".findprogress($db,$row['member_id'],$row['package_id'])."</td>";
		 echo "<td>$status</td>";
		echo "<td ><a href='endpackage.php?pid={$row['package_id']}&mid={$row['member_id']}'>结束课包</a></td>";
		
	echo "	<td ><a href='addPackageMax.php?pid={$row['package_id']}&mid={$row['member_id']}'>续一课时</a></td>";
		
		
	echo "	</tr>";
		//print_r(error_get_last());
		 $i=$i+1;
	}
	
	

?>
</tbody></table>
<?php 
	if(count($rows)==0) echo "没有检索到相关的课程卡或单项卡";
?>
<!-- end content -->
   </td>
  </tr>
 </table>

 </body>
</html>
