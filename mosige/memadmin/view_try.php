<?php
	
	session_start();
	if(!$_SESSION["adminuserid"]) header("Location:index.php");
	include("header.inc.php");
	include("dbconnect.inc.php");
	$page=isset($_GET['page'])?intval($_GET['page']):1;        //这句就是获取page=18中的page的值，假如不存在page，那么页数就是1。  
	$num=50;                                      //每页显示10条数据  

	//echo $_GET["name"];
	if($_GET["name"] != "") {
		$name = addslashes($_GET["name"]);
		$where .= " and member_name like '%{$name}%' ";
	}
	if($_GET["tel"] != "") {
		$tel = addslashes($_GET["tel"]);
		$where .= " and member_cell like '%{$tel}%' ";
	}
	if($_GET["type"] != "") {
		$type = addslashes($_GET["type"]);
		$where .= " and member_level = '{$type}' ";
	}
	$sql_all = "select * from try_member_user where 1 {$where}";
	$res_all = $db->query($sql_all);
	$res_all->setFetchMode(PDO::FETCH_ASSOC);
	$rows = $res_all->fetchAll();
	
	$total=count($rows); //查询数据的总数  
    $pagenum=ceil($total/$num); 
	 
	
	//假如传入的页数参数大于总页数，则显示错误信息  
	If($page>$pagenum){  
		   Echo "Error : Can Not Found The page .";  
		   Exit;  
	}  

	$offset=($page-1)*$num;
	if($offset<0){$offset=1;}
	
	$sql = "select * from try_member_user where 1 {$where}  limit $offset,$num";
	//echo $sql;
	$db->query('set names UTF8');
	$res = $db->query($sql);
	//$info=$db->query($sql);   //获取相应页数所需要显示的数据  
	//While($it=mysqli_fetch_array($info)){  
	//	   Echo $it['name']."<br />";  
	//}                                                              //显示数据  
echo "每页显示50条。当前页:{$page} 页数：";
$current;
	For($i=1;$i<=$pagenum;$i++){  
		   
		   $show=($i!=$page)?"<a href='view_try.php?page=".$i."'>$i   </a>":"<b>$i</b>";  
		   Echo $show." ";  
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
echo "<li class='active'><a href='view_account.php'  class='active'>查看团购会员信息</a></li>";
echo "<li ><a href='manage_class.php'>课程管理</a></li>";
echo "<li ><a href='manage_leave.php'>请假管理</a></li>";
echo "<li  ><a href='manage_attend.php'>签到表</a></li>";
echo "<li ><a href='manage_export.php'>导入导出</a></li>";
echo "<li ><a href='member_class.php'>会员上课情况</a></li>";
echo "<li ><a href='manage_rm.php'>跑步机管理</a></li>";

	   
   }elseif($_SESSION["adminuserid"]=="2"){
	   
	echo	"<li ><a href='add_account.php'>注册新会员</a></li>";   
echo "<li ><a href='view_account.php'  class='active'>查看团购会员信息</a></li>";
echo "<li ><a href='manage_class.php'>课程管理</a></li>";
echo "<li ><a href='manage_leave.php'>请假管理</a></li>";
echo "<li  ><a href='manage_attend.php'>签到表</a></li>";
echo "<li ><a href='member_class.php'>会员上课情况</a></li>";
echo "<li ><a href='manage_rm.php'>跑步机管理</a></li>";   
   }
   elseif($_SESSION["adminuserid"]=="3"){

echo "<li ><a href='manage_class.php'>课程管理</a></li>"; 
echo "<li  ><a href='manage_attend.php'>签到表</a></li>";
 	   
	   
   }
 elseif($_SESSION["adminuserid"]=="4"){

echo "<li ><a href='manage_class.php'>课程管理</a></li>"; 
echo "<li  ><a href='manage_attend.php'>签到表</a></li>";
 	   
	   
   }?>
</ul>
<h2><a href="view_account.php">返回查看常规会员</a></h2>
<!-- begin content -->
<script language="javascript">
	function doDel(title,id) {
		if(confirm('你确定要删除用户？\n-------------------------\n'+title+'\n-------------------------'))
			location.href='del_try.php?id='+id;
	}</script>
<form action="view_account.php"  method="get" >
</script>
<div><div class="container-inline"><div class="form-item">
 <label >检索用户: </label>
用户名模糊搜索：<input type="text" name="name"  value="<?php echo $_GET['member_name']; ?>" /><br />
手机模糊搜索：<input type="text" name="tel"  value="<?php echo $_GET['member_cell']; ?>" /><br />
按会员卡类型检索：
<select name="type"> 
<option value="package" <?php if($_GET['member_level'] == 'package'){echo 'selected="selected"';} ?>>空中or儿童1次</option> 
<option value="common" <?php if($_GET['member_level'] == 'both'){echo 'selected="selected"';} ?>>周卡(含空中和儿童)</option> 
<option value="common_count" <?php if($$_GET['member_level'] == 'common_count'){echo 'selected="selected"';} ?>>常规1次卡 </option> 
 
<option value="" <?php if($_GET['member_level'] == ''){echo 'selected="selected"';} ?>>返回全部</option> 
</select>
<input type="hidden" name="page"  value="" /><br />
</br>
<input type="submit" value="检    索"  class="form-submit" />
</div>
</div></form>

<table cellspacing="10%" cellpadding="10">
 <thead><tr><th> </th>
 <th>用户名</th><th></th>
 <th>会员卡号</th><th></th>
 <!--<th >id</th>-->
 <th>手机号</th><th></th>
 <!--<th>请假状态</th>-->
  <th>会员类型</th><th></th>
 <!-- <th>上课次数</th>
  <th>会员卡总次数</th><th>截止日期</th>-->
  <th>办卡日期</th>
  <th>开卡日期</th>
  <th>到期日期</th>
 <th>具体</th><th>操作</th></tr>
 </thead>
<tbody>
<?php
$m_id=1;

$res->setFetchMode(PDO::FETCH_ASSOC);
$rows1 = $res->fetchAll();
	foreach($rows1 as $row) {
		//echo $row["member_level"];
		switch($row["member_level"]) {
			case	"package"	:
				$level = "团购空中或儿童1次";
				break;
			case	"both"	:
				$level = "团购周卡";
				break;
			case	"common_count"	:
				$level = "常规1次卡";
				break;
			case	"both_count":
				$level = "微信体验券 ";
				break;
			case	"tiyan_1"	:
				$level = "团购体验1次";
				break;
			case	"tiyan_2"	:
				$level = "团购体验2次";
				break;
			case	"tiyan_3"	:
				$level = "10.31体验3次";
				break; 
			default		:
				$level = "未设定";
				break;
		}
		 
		echo "<tr ><td>{$m_id}</td>";
		$m_id=$m_id+1;
		echo "<td>{$row['member_name']}</td>";
		echo "<td></td>";
		echo "<td>{$row['member_id']}</td>";
		echo "<td></td>";
		//echo "<td>{$row['member_id']}</td>";
		echo "<td >{$row['member_cell']}</td>";
		echo "<td></td>";
		//echo "<td>{$leave}</td>";
		echo "<td>{$level}</td>";
		echo "<td></td>";
	//	echo "<td>{$row['member_classcount']}</td>";
		//echo "<td>{$row['member_attendmax']}</td>";
		echo "<td>{$row['member_regtime']}</td>";
		//echo "<td>{$row['member_enddate']}</td>";
		//echo "<td class='active'>{$row['reg_time']}</td>";
		echo "<td>{$row['member_startdate']}</td>";
		echo "<td>{$row['member_enddate']}</td>";
		echo "<td>{$row['member_intro']}</td>";
		echo "<td>";	
		if($_SESSION["adminuserid"]=="1") {
			echo "<a onclick='return doDel(\"{$row['member_name']}\",{$row['member_id']});'>删除</a>
			</td>";			
		echo "</tr>";}
		
	}
?>
</tbody></table>
<?php 
	if(count($rows1)==0) echo "没有检索到相关的用户";
?>
<!-- end content -->
   </td>
  </tr>
 </table>

 </body>
</html>