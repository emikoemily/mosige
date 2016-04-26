<?php
	session_start();
	if(!$_SESSION["adminuserid"]) header("Location:index.php");
	 
	include("dbconnect.inc.php");
	if($_POST["op"] == "注册新帐号") {
		include("do_register.inc.php");
		exit;
	}
	include("header.inc.php");
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
<div class="breadcrumb"><a href="./">主页</a> &raquo; <a href="./">用户帐号</a></div><h2><?php echo $_SESSION["username"]; ?></h2><ul class="tabs primary">
<?php
   if($_SESSION["adminuserid"]=="1") {//1:admin 2:frontdesk 3:teacher
	   
echo "<li class='active'><a href='register.php'>注册后台用户</a></li>";   
echo "<li ><a href='view_admin.php'  class='active'>用户列表</a></li>";


	   
   }elseif($_SESSION["adminuserid"]=="2"){
	   
 
   }
   elseif($_SESSION["adminuserid"]=="3"){


 	   
	   
   }

?>
</ul>
<script>
	function check_form() {
		username = document.getElementById("edit-name").value;
		password = document.getElementById("edit-pass").value;
		password2 = document.getElementById("edit-pass2").value;
		mail = document.getElementById("edit-tel").value;
		emsg = "";
		if(username == "") emsg += "用户名没有填写. \n";
		if(password == "") emsg += "密码没有填写. \n";
		if(password != password2) emsg += "两次输入密码不同. \n";
		var pattern = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
		if(!pattern.test(mail)) emsg += "邮件格式不正确. \n";
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
<form action="register.php"  method="post" id="user_register" accept-charset='GB2312'>

<div><div class="form-item">
 <label for="edit-name">登录名: <span class="form-required" title="This field is required.">*</span></label>
 <input type="text" maxlength="60" name="edit[name]" id="edit-name"  size="30" value="" class="form-text required" />
 
</div>
<div class="form-item">
 <label for="edit-pass">密码: <span class="form-required" title="This field is required.">*</span></label>
 <input type="password" maxlength="64" name="edit[pass]" id="edit-pass"  size="30" value="" class="form-text required" />
 <div class="description">请输入您的密码。</div>
 <input type="password" maxlength="64" name="edit[pass2]" id="edit-pass2"  size="30" value="" class="form-text required" />
 <div class="description">请再次输入您的密码。</div>
</div>
 
 
<label for="edit-type">用户权限: </label></br>
管理员 <input type="radio" name="edit[type]"  value="1" /></br>
客服 <input type="radio" name="edit[type]"   value="2" /></br>
老师 <input type="radio" name="edit[type]"  value="3" /></br>
无权限 <input type="radio" name="edit[type]"  value="0" /></br>
</div>

<input type="submit" name="op" value="注册新帐号"  class="form-submit" onclick="return check_form();"  />
<br /><br />
</div></form>

<!-- end content -->
   </td>
  </tr>
 </table>
 </body>
</html>
