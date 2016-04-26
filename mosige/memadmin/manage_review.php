<?php
	if($_POST["op"] == "更    新") {
		include("do_member_class.inc.php");
		exit;
	}
	session_start();
	include("header.inc.php");
	include("dbconnect.inc.php");
	
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
	$rows=$res->fetchAll();
	//$row = mysqli_fetch_array($res);
	//extract($row);
	
	//echo $sql;
	}else {
			$sql_all = "SELECT * FROM yoga_lu.package_design inner join 
	package_subscribe on package_design.package_id =package_subscribe.package_id inner join member_user where package_subscribe.member_id=member_user.member_id ";
	
	$res_all = $db->query($sql_all);
	$res_all->setFetchMode(PDO::FETCH_ASSOC);
	$row_alls=$res_all->fetchAll();
	
	$total=count($row_alls); //查询数据的总数  
    $pagenum=ceil($total/$num); 
	
	
	//假如传入的页数参数大于总页数，则显示错误信息  
	If($page>$pagenum || $page == 0){  
		   Echo "Error : Can Not Found The page .";  
		   Exit;  
	}  

	$offset=($page-1)*$num;                                        //获取limit的第一个参数的值，假如第一页则为(1-1)*10=0,第二页为(2-1)*10=10。  

	$sql = "SELECT * FROM yoga_lu.package_design inner join 
	package_subscribe on package_design.package_id =package_subscribe.package_id inner join member_user where package_subscribe.member_id=member_user.member_id limit $offset,$num";
	
	$db->query('set names UTF8');
	 
	
	$info=$db->query($sql);   //获取相应页数所需要显示的数据  
	                                                             //显示数据  
echo "每页显示50条。当前页:{$page}  页数：";
	For($i=1;$i<=$pagenum;$i++){  
		   
		   $show=($i!=$page)?"<a href='member_class.php?page=".$i."'>$i   </a>":"<b>$i</b>";  
		   Echo $show." ";  
	}  

	
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

<li ><a href="manage_class.php">导入导出</a></li>
<li class="active"><a href="member_class.php">会员上课情况</a></li>
<li ><a href="manage_rm.php">跑步机管理</a></li>
</ul>
<?php
if($_GET["id"]!="" && $_SESSION["adminuserid"]==1) {
	echo "<input type='hidden' name='id' value='{$_GET['id']}' />";
}
?>


<table>
 <thead><tr><th> </th>
 <th>用户名</th>
 <th >packageid</th>
 <th>课程名</th>
 <th>总次数</th>
  <th>已上次数</th>
 </tr>
 </thead>
<tbody>
<?php $i=1;
 
$info->setFetchMode(PDO::FETCH_ASSOC);
$rowinfos=$info->fetchAll();
	foreach($rowinfos as $row) {		
	
		echo "<tr ><td>$i   </td>";
		echo "<td>{$row['member_name']}</td>";
		echo "<td>{$row['package_id']}</td>";
		echo "<td >{$row['package_name']}</td>";
 		echo "<td>{$row['package_course_count']}</td>";
		 echo "<td>{$row['package_attended']}</td></tr>";
		//echo "<td class='active'>{$row['reg_time']}</td>";
		 $i=$i+1;
	}
?>
</tbody></table>
<?php 
	if(count($rowinfos)==0) echo "没有检索到相关的课程卡或单项卡";
?>
<!-- end content -->
   </td>
  </tr>
 </table>

 </body>
</html>
