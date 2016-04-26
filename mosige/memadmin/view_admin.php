<?php
	session_start();
	if(!$_SESSION["adminuserid"]) header("Location:index.php");
	include("header.inc.php");
	include("dbconnect.inc.php");
	if($_GET["name"] != "") {
		$name = addslashes($_GET["name"]);
		$where .= " and username like '%{$name}%' ";
	}
	$sql = "select * from users where 1 {$where} limit 20";
	$res = $db->query($sql);
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
echo "<li class='leaf'><a href='register.php' class='active'>注册后台用户</a></li>";
}
?>
<li class="leaf"><a href="logout.php">注销登录</a></li>
 
</ul>
</div>
</div>
</td>
   <td id="main">
<div class="breadcrumb"><a href="./">主页</a></div><h2>用户列表</h2><div class="help"> 
</div><hr />
<ul class="tabs primary">
<?php
   if($_SESSION["adminuserid"]=="1") {//1:admin 2:frontdesk 3:teacher
	   
echo "<li ><a href='register.php'>注册后台用户</a></li>";   
echo "<li class='active'><a href='view_admin.php'  class='active'>用户列表</a></li>";


	   
   }elseif($_SESSION["adminuserid"]=="2"){
	   
 
   }
   elseif($_SESSION["adminuserid"]=="3"){


 	   
	   
   }

?>
</ul>
<!-- begin content -->
<script language="javascript">
	function doDel(title,id) {
		if(confirm('你确定要删除用户？\n-------------------------\n'+title+'\n-------------------------'))
			location.href='del_account.php?id='+id;
	}
</script>
<form action="admin.php"  method="get" >
<div><div class="container-inline"><div class="form-item">
 <label >检索用户: </label>
用户名模糊搜索：<input type="text" name="name"  value="<?php echo $_GET['name']; ?>" /><br />
</div>
<input type="submit" value="检    索"  class="form-submit" />
</div>
</div></form>

<table>
 <thead><tr><th> </th><th>用户名</th><th >级别</th><th>注册时间</th><th>操作</th> </tr></thead>
<tbody>
<?php
	$res->setFetchMode(PDO::FETCH_ASSOC);
	$rows = $res->fetchAll();
	foreach($rows as $row) {
		 
		switch($row["level"]) {
			case	"1"	:
				$l = "管理员";
				break;
			case	"2"	:
				$l = "客服";
				break;
			case  "3"		:
				$l = "老师";
				break;
			case  "4"		:
					$l = "销售";
					break;
			case  "0"		:
				$l = "游客";
				break;
		}
		echo "<tr ><td></td>";
		echo "<td>{$row['username']}</td>";
		 echo "<td>{$l}</td>";
		echo "<td class='active'>{$row['reg_time']}</td>";
		if($_SESSION["adminuserid"]=="1") {
			echo "<td><a href='edit_admin.php?id={$row['id']}'>编辑</a><br /><a  href='#' onclick='return doDel(\"{$row['username']}\",{$row['id']});'>删除</a></td> </tr>";
		}else {
			echo "<td><a href='detail.php?id={$row['id']}'>查看</a></td> </tr>";
		}
	}
?>
</tbody></table>
<?php 
	if(count($rows)==0) echo "没有检索到相关的用户";
?>
<!-- end content -->
   </td>
  </tr>
 </table>
<?php echo $page_link; ?>
 </body>
</html>
