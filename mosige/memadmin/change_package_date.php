<?php
	if($_POST["op"] == "更    新") {
		include("do_edit_account.inc.php");
		exit;
	}
	session_start();
	include("header.inc.php");
	include("dbconnect.inc.php");
	if($_GET["id"]!="" && is_numeric($_GET["id"])) {
		$id = $_GET["id"];
	
	$sql = "SELECT distinct payment_table.payment_id,payment_startdate,payment_enddate,payment_days 
FROM payment_table inner join
package_subscribe ON payment_table.payment_id =package_subscribe.payment_id
where  
package_subscribe.member_id ={$id}";
//echo $sql;
	echo "如果不打算修改则不要改动输入框里的默认值</br>";
	$db->query('set names UTF8');
	$res = $db->query($sql);
	//$row = mysqli_fetch_array($res);
	//extract($row);
	
	
	}else {
		//$id = "";
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
<li class="leaf"><a href="admin.php" >管理员列表</a></li>
<li class="leaf"><a href="logout.php">注销登录</a></li>

</ul>
</div>
</div>
</td>
   <td id="main">
<div class="breadcrumb"><a href="./">主页</a> &raquo; <a href="./">用户帐号</a></div><h2><?php echo $_SESSION["username"]; ?></h2><ul class="tabs primary">


<li><a href="add_account.php">注册新会员</a></li>
<li><a href="view_account.php">查看会员信息</a></li>
<li ><a href="manage_class.php">课程管理</a></li>
<li ><a href="manage_leave.php">请假管理</a></li>
<li  ><a href="manage_attend.php"  target="blank">签到表</a></li>

<li ><a href="manage_class.php">导入导出</a></li>
<li ><a href="member_class.php">会员上课情况</a></li>
<li ><a href="manage_rm.php">跑步机管理</a></li>
</ul>


<table cellspacing="10%" cellpadding="10">
 <thead><tr><th> </th>
 <th>课包id </th><th></th>
  <th class="title3">课包内含课程 </th><th></th>
 <th>课包有效期开始（第一次成功预约）</th><th></th>
 <!--<th >id</th>-->
 <th>课包有效期结束</th><th></th>
 <!--<th>请假状态</th>-->
  <th>有效期天数</th><th></th>
 <th>操作</th><th></th>
 </tr>
 </thead>
<tbody>

<!-- begin content --><?php
function getpackages($db,$payid){
	$sqlgp="SELECT package_subscribe.package_id,package_name  FROM `package_subscribe` INNER join package_design on package_subscribe.package_id=package_design.package_id WHERE `payment_id` = '{$payid}'";
	//echo $sqlgp;
	$db->query('set names UTF8');
	$resgp=$db->query($sqlgp);
	$rows=$resgp->fetchAll();
	$i=1;
	foreach($rows as $rowgp){
		echo $i." ".$rowgp['package_name']."</br>";
		$i++;
	}
}
echo "<a href=http://yoga.ibreezee.com/memadmin/member_class.php?id={$_GET['id']}>【查看课包上课情况】</a>";
	$rows2=$res->fetchAll();
	foreach($rows2 as $row){
		echo "	<tr><td> </td>";
		echo "<td>";
		echo $row["payment_id"];
		echo "</td>";
		echo "<td></td>";
		echo "<td class='title3'>";		
		getpackages($db,$row["payment_id"]);
		echo "</td>";
		echo "<td></td>";
		echo "<td>";		
		echo $row["payment_startdate"];
		echo "</td>";
		echo "<td></td>";
		echo "<td>";
		echo $row["payment_enddate"];
		echo "</td>";
		echo "<td></td>";
		echo "<td>";
		echo $row["payment_days"];
		echo "</td>";
		echo "<td></td>";
		echo "<td>";
		echo "<form method='get' action='do_change_package_date.php'>";
		
		if($_SESSION["adminuserid"]==1){
			echo "新的截止日期：</br>";
		echo "<input type='text' name='newenddate' value='{$row['payment_enddate']}' maxlength='300'  style='height:51px;width:549px'>";
		echo "</br>新的有效天数：";
		echo "<input type='text' name='newdays' value={$row['payment_days']}>";
		echo "<input type='hidden' name='paymentid' value={$row['payment_id']}>";
			echo "<input type='submit' name='changedate' value='提交修改'>";
		}
		else{
			echo "<td></td>";
		}
		echo "</form>";
		echo "</td>";
		echo "<td></td>";
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
