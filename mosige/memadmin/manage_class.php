<?php
	
	session_start();
	$code = mt_rand(0,1000000);
	$_SESSION['code'] = $code;
	if(!$_SESSION["adminuserid"]) header("Location:index.php");
	include("header.inc.php");
	include("dbconnect.inc.php");
	include("sendmail.php"); 
?>
 <table id="content">
  <tr>
   <td id="sidebar-left"><div class="block block-user" id="block-user-1">
  <h2 class="title"><?php echo $_SESSION["username"]; ?></h2>
 <div class="content">
<ul class="menu">
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
<script language="javascript">
	function doDel(title,id) {
		if(confirm('你确定要删除？\n-------------------------\n'+title+'\n-------------------------'))
			location.href='do_del_arrange.php?mid='+id;
	}</script>
 
<?php
   if($_SESSION["adminuserid"]=="1") {//1:admin 2:frontdesk 3:teacher
	   
echo "<li ><a href='add_account.php'>注册新会员</a></li>";   
echo "<li ><a href='view_account.php'  class='active'>查看会员信息</a></li>";
echo "<li class='active'><a href='manage_class.php'>课程管理</a></li>";
echo "<li ><a href='manage_leave.php'>请假管理</a></li>";
echo "<li><a href='manage_attend.php'  target='blank'>签到表</a></li>";
echo "<li ><a href='manage_export.php'>导入导出</a></li>";
echo "<li ><a href='member_class.php'>会员上课情况</a></li>";
echo "<li ><a href='manage_rm.php'>跑步机管理</a></li>";

	   
   }elseif($_SESSION["adminuserid"]=="2"){
	   
	echo	"<li ><a href='add_account.php'>注册新会员</a></li>";   
echo "<li ><a href='view_account.php'>查看会员信息</a></li>";
echo "<li class='active'><a href='manage_class.php'>课程管理</a></li>";
echo "<li ><a href='manage_leave.php'>请假管理</a></li>";
echo "<li ><a href='manage_attend.php'  target='blank'>签到表</a></li>";
echo "<li ><a href='member_class.php'>会员上课情况</a></li>";
echo "<li ><a href='manage_rm.php'>跑步机管理</a></li>";   
   }
   elseif($_SESSION["adminuserid"]=="3"){

echo "<li class='active'><a href='manage_class.php'>课程管理</a></li>"; 
echo "<li   ><a href='manage_attend.php'  target='blank'>签到表</a></li>";
echo "<li ><a href='manage_export.php'>导入导出</a></li>";
 	   
	   
   }
    elseif($_SESSION["adminuserid"]=="4"){
    	echo	"<li ><a href='add_account.php'>注册新会员</a></li>";
    	echo "<li ><a href='view_account.php'>查看会员信息</a></li>";
echo "<li class='active'><a href='manage_class.php'>课程管理</a></li>"; 
echo "<li   ><a href='manage_attend.php'  target='blank'>签到表</a></li>";
 	   
	   
   }

?>
</ul>

<div>
添加新课：
<table>
 <thead><tr>
  <th>日期</th>
 <th>上课时间</th>
 <th >课程时间</th>
 <th>课</th>
 <th>老师</th>
 </tr>
 </thead>
 
<tbody>
<form name="addclass" action="do_add_class.inc.php" method="post">
<?php
		echo "<tr ><td><input type='date' name='arrangedate'  value=''/></td>";
		echo "<td><select name='starttime'  value=''/>";
		echo "<option value='8:30:00'>8:30:00</option>";
		echo "<option value='10:30:00'>10:30:00</option>";
		echo "<option value='12:00:00'>12:00:00</option>";
		echo "<option value='13:00:00'>13:00:00</option>";
		echo "<option value='14:00:00'>14:00:00</option>";
		echo "<option value='15:00:00'>15:00:00</option>";
		echo "<option value='17:30:00'>17:30:00</option>";
		echo "<option value='17:45:00'>17:45:00</option>";
		echo "<option value='18:00:00'>18:00:00</option>";
		echo "<option value='18:15:00'>18:15:00</option>";
		echo "<option value='18:30:00'>18:30:00</option>";
		echo "<option value='18:45:00'>18:45:00</option>";
		echo "<option value='18:50:00'>18:50:00</option>";
		echo "<option value='19:00:00'>19:00:00</option>";
		echo "<option value='19:15:00'>19:15:00</option>";
		echo "<option value='19:30:00'>19:30:00</option>";
		echo "<option value='19:45:00'>19:45:00</option>";
		echo "<option value='19:50:00'>19:50:00</option>";
		echo "<option value='20:00:00'>20:00:00</option>";
		echo "<option value='20:15:00'>20:15:00</option>";
		echo "<option value='20:50:00'>20:50:00</option>";
		echo "</select></td>";
		 
		echo "<td><select name='mins'  value=''/>";
		//echo "<option value='30'>30分钟</option>";
		echo "<option value='45'>45分钟</option>";
		echo "<option value='60'>60分钟</option>";
		echo "<option value='75'>75分钟</option>";
		echo "<option value='90'>90分钟</option>";

 
		echo "</select></td>";
		 /*
		echo "<td><select name='endtime'  value=''/>";
		echo "<option value='10:00:00'>10:00:00</option>";
		echo "<option value='11:15:00'>11:15:00</option>";
		echo "<option value='11:30:00'>11:30:00</option>";
		echo "<option value='13:00:00'>13:00:00</option>";
		echo "<option value='13:15:00'>13:15:00</option>";
		echo "<option value='15:00:00'>15:00:00</option>";
		echo "<option value='15:15:00'>15:00:00</option>";
		echo "<option value='16:00:00'>16:00:00</option>";
		echo "<option value='16:15:00'>16:15:00</option>";
		echo "<option value='18:15:00'>18:15:00</option>";
		echo "<option value='18:30:00'>18:30:00</option>";
		echo "<option value='18:45:00'>18:45:00</option>";
		echo "<option value='19:00:00'>19:00:00</option>";
		echo "<option value='19:15:00'>19:15:00</option>";
		echo "<option value='19:30:00'>19:30:00</option>";
		echo "<option value='19:45:00'>19:45:00</option>";
		echo "<option value='19:50:00'>19:50:00</option>";
		echo "<option value='20:00:00'>20:00:00</option>";
		echo "<option value='20:15:00'>20:15:00</option>";
		echo "<option value='20:30:00'>20:30:00</option>";
		echo "<option value='20:45:00'>20:45:00</option>";
		echo "<option value='20:50:00'>20:50:00</option>";
		echo "<option value='21:00:00'>21:00:00</option>";
		echo "<option value='21:15:00'>21:15:00</option>";
		echo "</select></td>";
		*/
	 
        echo "<td><select name='classid'> ";
		$sql1 = "select class_id,class_name,inner_id,class_description,package_id,for_ref from class_design where class_type = 'package' or package_id='set2'  or package_id='set3' or package_id='set4' or package_id='set5' ORDER BY class_name,inner_id ";
	    $db->query('set names UTF8');
	    $res1 = $db->query($sql1);
	    $res1->setFetchMode(PDO::FETCH_ASSOC);
	    $rows=$res1->fetchAll();
	    foreach($rows as $row) {	
	    	 
	    		
	    		echo "<option value='{$row['class_id']},{$row['for_ref']}'>{$row['class_name']}-{$row['inner_id']}-{$row['class_description']}</option> ";
	    	 
 
	
	}
        echo "</select>";
		echo "</td>";
		
		
		 echo "<td><select name='teacherid'> ";
		$sqlteacher = "select teacher_id,teacher_name from teacher_table";
	    $db->query('set names UTF8');
	    $resteacher = $db->query($sqlteacher);
	    $resteacher->setFetchMode(PDO::FETCH_ASSOC);
	    $rowteachers=$resteacher->fetchAll();
	   foreach($rowteachers as $rowteacher) {
	
        echo "<option value='{$rowteacher['teacher_id']}'>{$rowteacher['teacher_name']}</option> ";
	}
        echo "</select>";
		echo "</td>";?></tr>
		
</tbody>
</table>

<table>
 <thead>
		 <th>课程卡人数 </th>
 <th>习练卡人数(包含体验券)</th>
 <th>团购人数</th>

  <th>教室</th>
  <th>时间重叠数字标记</th>
   
 <th>操作</th>
 <tr>
 <?php
		echo "<td width= 12px><input type='text' name='max'  value='6'/></td>";
		echo "<td><input type='text' name='commonmax'  value='6'/></td>";
		echo "<td><input type='text' name='trymax'  value=''/></td>";
		echo "<td><input type='text' name='classroom'  value='1'/></td>";
		echo "<td><input type='text' name='overlap'  value='0'/></td>";
		echo "<td><input type='checkbox' name='same'  checked='true'/></td>";
        if($_SESSION["adminuserid"]=="1" or $_SESSION["adminuserid"]=="2" or $_SESSION["adminuserid"]=="3" or $_SESSION["adminuserid"]=="4") {
			echo "<td><input type='submit' name='submitclass' value='     添加      '></input> <br />
		</td></tr>";}
?>
</form>

</tbody>
</table>

</div>
<a href="http://yoga.ibreezee.com/memadmin/classarrangement/classarrange.html " target="_blank">系统排课建议（正在研发）</a>
<div>
</br>
</br>
</br></br>
</br>
<form action="manage_class.php"  method="get" >
</script>
<div><div class="container-inline"><div class="form-item">
 <label > </label>
按日期搜索：<input type="date" name="dd"  value="<?php echo date('Y-m-d'); ?>" />

<input type="submit" value="检    索"  class="form-submit" />
</div>
</div></form>

<table id="0"  border="2">
 <thead><tr><th> </th>
  <th>上课日期</th>
 <th>上课时间</th>
 
 <th>课程名称</th>
 <th>会员人数</th>
 <th>团购人数</th>
  <th>老师</th>
  <th>教室</th>
 <th>操作</th><th>操作</th><th>为 会员 预约 </th><th></th><th>为<b> 团购</b>会员 预约 </th><th></th> </tr>
 </thead>
<tbody>



<?php
	if($_GET["dd"] != "") {
		$dd = addslashes($_GET["dd"]);
		$where .= " and arrangedate like '%{$dd}%' ";
	}
	 
       // echo "<td><select name='aa'> ";
	   
		$sql2 = "select arrange_id,class_type,arrangedate,starttime,endtime,maxposition,class_arrange.teacher_id,try_maxposition,class_arrange.class_id,class_name,inner_id,class_description,package_id,classroom,for_ref,teacher_name 
		from class_arrange 
		inner join class_design on class_arrange.class_id=class_design.class_id  
		left join teacher_table on class_arrange.teacher_id=teacher_table.teacher_id
		where 1 {$where} ORDER BY arrangedate desc,starttime asc limit 50";
	    //echo $sql2;
		$db->query('set names UTF8');
	    $res2 = $db->query($sql2);
	    $res2->setFetchMode(PDO::FETCH_ASSOC);
	    $row2s = $res2->fetchAll();
	   foreach($row2s as $row2) {
		echo "<tr ><td></td> ";
		//echo $row2[0];
		echo "<td>{$row2['arrangedate']}</td>";
		echo "<td>{$row2['starttime']}</br>-</br>{$row2['endtime']}</td>";
		echo "<td>{$row2['class_name']}-{$row2['inner_id']}-{$row2['class_description']} arrid:({$row2['arrange_id']}) refid:({$row2['for_ref']})</td>";
	    echo "<td>{$row2['maxposition']} </td>";
		 echo "<td>{$row2['try_maxposition']} </td>";
		 echo "<td>{$row2['teacher_name']}</td>";
	    echo "<td>{$row2['classroom']}</td>";
		   if($_SESSION["adminuserid"]=="1" or $_SESSION["adminuserid"]=="2"  or $_SESSION["adminuserid"]=="3") {
		echo "<td><a  href='#' onclick='return doDel(\"{$row2['class_name']}\",{$row2['arrange_id']});'>删除</a></td>";
		echo "<td><a href='edit_arrange.php?id={$row2['arrange_id']}&class={$row2['class_id']}'>编辑</a></td>";
		   
		   
		   }
		   else{
			   
			      echo "<td> </td>";
		    echo "<td></td>";
		   }
		
		echo "<form id='{$row2['arrange_id']}' method='get' action='adminregisterclass.php'>";
		echo "<td><input type='text' placeholder='会员手机号' name='memcell-{$row2['arrange_id']}'><br><input type='submit' value='预约'></td>";
		echo "<input type='hidden'  name='classchoice' id='arrid-{$row2['arrange_id']}' value={$row2['arrange_id']}>";
		echo "<input type='hidden'  name='code' value={$code}>";
		echo "<td></td>";
		echo "</form>";
		
		if($row2['class_type']!='package'){
		echo "<form id='{$row2['arrange_id']}-2' method='get' action='adminregisterclass_try.php'>";
		echo "<td><input type='text' placeholder='团购或临时体验手机号' name='memcell-{$row2['arrange_id']}'>";
		echo "<input type='hidden'  name='classchoice' id='arrid-{$row2['arrange_id']}' value={$row2['arrange_id']}>";
		echo "<br>是门店临时预约请勾选:<input type='checkbox'  name='temp'>";
		echo "<br><input type='textbox'   name='temp_name' placeholder='临时体验人姓名'><br>";
		echo "<input type='hidden'   name='code' value={$code}>";
		echo "<input type='submit' value='团购及临时预约'>";
		
		echo "</td>";
		
		echo "<td>";
		
		echo "</td>";
		
		
		
		echo "</form>";}
		else{
			echo "<td>";
			echo "</td>";
			
			echo "<td>";
			
			echo "</td>";
		}
        echo "</tr>";
	}
     
		
		
		
		

?>

</tbody>
</table>

</div>
 </body>
</html>
