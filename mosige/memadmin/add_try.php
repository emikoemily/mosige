<?php
	if($_POST["op"] == "录入新帐号") {
		include("do_reg_try.inc.php");
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
	$sql = "select * from users where id={$id}";
	$res = $db->query($sql);
	$res->setFetchMode(PDO::FETCH_ASSOC);
	$row = $res->fetchAll();
	extract($row);
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
</ul>
</div>
</div>
</td>
   <td id="main">
<div class="breadcrumb"><a href="./">主页</a> &raquo; <a href="./">用户帐号</a></div><h2><?php echo $_SESSION["username"]; ?></h2><ul class="tabs primary">
<?
   if($_SESSION["adminuserid"]=="1") {//1:admin 2:frontdesk 3:teacher
	   
echo "<li  class='active'><a href='add_account.php'>注册新会员</a></li>";   
echo "<li ><a href='view_account.php'>查看会员信息</a></li>";
echo "<li ><a href='manage_class.php'>课程管理</a></li>";
echo "<li ><a href='manage_leave.php'>请假管理</a></li>";
echo "<li  ><a href='manage_attend.php' target='blank'>签到表</a></li>";
echo "<li ><a href='manage_export.php'>导入导出</a></li>";
echo "<li ><a href='member_class.php'>会员上课情况</a></li>";
echo "<li ><a href='manage_rm.php'>跑步机管理</a></li>";

	   
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
echo "<li ><a href='manage_export.php'>导入导出</a></li>";
	   
   }elseif($_SESSION["adminuserid"]=="4"){
   	echo	"<li  class='active'><a href='add_account.php'>注册新会员</a></li>";
   	echo "<li ><a href='view_account.php'>查看会员信息</a></li>";
echo "<li class='active'><a href='manage_class.php'>课程管理</a></li>"; 
echo "<li   ><a href='manage_attend.php'>签到表</a></li>";
 	   
	   
   }?>
</ul>

<script>
	function check_form() {
		username = document.getElementById("edit-name").value;
		password = document.getElementById("edit-pass").value;
		password2 = document.getElementById("edit-pass2").value;
		//mail = document.getElementById("edit-tel").value;
		emsg = "";
		if(username == "") emsg += "用户名没有填写. \n";
		if(password == "") emsg += "密码没有填写. \n";
		if(password != password2) emsg += "两次输入密码不同. \n";
		//var pattern = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
		//if(!pattern.test(mail)) emsg += "邮件格式不正确. \n";
		if(emsg != "" ) {
			emsg = "------------------------------------------\n\n"+emsg;
			emsg = emsg+"\n------------------------------------------";
			alert(emsg);
			return false;
		}else {
			return true;
		}
	}
</script>
<!-- begin content -->



团购会员录入      <a href="add_account.php">点此录入常规会员</a>
<form action="add_try.php"  method="post" id="user_register" >
<div><div class="form-item">
 <label for="edit-tid">团购券id: <span class="form-required" title="This field is required.">*</span></label>
 
 <input type="text" maxlength="60" name="edit[tid]" id="edit-tid"  size="30" value="" class="form-text required" />
 <div class="description">团购券后4位</div>
</div>
<div><div class="form-item">
 <label for="edit-name">用户名: <span class="form-required" title="This field is required.">*</span></label>
 
 <input type="text" maxlength="60" name="edit[name]" id="edit-name"  size="30" value="" class="form-text required" />
 <div class="description">你的全名或你更喜欢的名字。允许中英文、空格和数字。</div>
</div>
<div class="form-item">
 <label for="edit-pass">密码: <span class="form-required" title="This field is required.">*</span></label>
 <input type="password" maxlength="64" name="edit[pass]" id="edit-pass"  size="30" value="" class="form-text required" />
 <div class="description">请输入您的密码。</div>
 <input type="password" maxlength="64" name="edit[pass2]" id="edit-pass2"  size="30" value="" class="form-text required" />
 <div class="description">请再次输入您的密码。</div>
</div>
<div class="form-item">
 <label for="edit-tel">电话: </label><span class="form-required" title="This field is required.">*</span></label>
 <input type="text" maxlength="64" name="edit[tel]" id="edit-tel"  size="30" value="" />
 <div class="description">必填，您的电话，请确保它是正确的。</div>
</div>
<div class="form-item">
 <label for="edit-cardid">会员卡卡号: </label>
 <input type="text" maxlength="64" name="edit[cardid]" id="edit-cardid"  size="30" value="" />
 <div class="description">会员系统卡号。</div>
</div>

<label for="edit-type">会员类型: </br></label>
儿童亲子单次卡<input type="radio" name="edit[type]"  value="try_ertong"  checked=checked /></br>
周卡 <input type="radio" name="edit[type]"   value="common_week" /></br>
空中瑜伽单次卡<input type="radio" name="edit[type]"  value="try_kong" /></br>
常规课程单次卡<input type="radio" name="edit[type]"  value="common_1" /></br>
25号当天有效<input type="radio" name="edit[type]"  value="25day" /></br>
微信体验2次券<input type="radio" name="edit[type]"  value="tiyan_2" /></br>
体验1次卡<input type="radio" name="edit[type]"  value="tiyan_1" /></br>
体验3次卡<input type="radio" name="edit[type]"  value="tiyan_3" /></br>
</div>

 

<input type="submit" name="op" value="录入新帐号"  class="form-submit" onclick="return check_form();"  />
<br /><br />
</div></form>
<!-- end content -->
   </td>
  </tr>
 </table>

 </body>
</html>
