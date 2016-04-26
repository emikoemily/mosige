<?php
	if($_POST["op"] == "注册新帐号") {
		include("do_reg_account.inc.php");
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
	$row = $res->fetch();
	extract($row);
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
echo "<li  ><a href='manage_attend.php' target='blank'>签到表</a></li>";
echo "<li ><a href='member_class.php'>会员上课情况</a></li>";
echo "<li ><a href='manage_rm.php'>跑步机管理</a></li>";   
   }
   elseif($_SESSION["adminuserid"]=="3"){

echo "<li ><a href='manage_class.php'>课程管理</a></li>"; 
echo "<li  ><a href='manage_attend.php' target='blank'>签到表</a></li>";
echo "<li ><a href='manage_export.php'>导入导出</a></li>"; 	   
	   
   }elseif($_SESSION["adminuserid"]=="4"){
   	echo	"<li  class='active'><a href='add_account.php'>注册新会员</a></li>";
   	echo "<li ><a href='view_account.php'>查看会员信息</a></li>";
echo "<li class='active'><a href='manage_class.php'>课程管理</a></li>"; 
echo "<li   ><a href='manage_attend.php' target='blank'>签到表</a></li>";
 	   
	   
   }

?>


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


常规会员录入                  <a href="add_try.php">点此录入团购会员</a>
<form action="add_account.php"  method="post" id="user_register" >

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
 <label for="edit-cardid">会员卡卡号: </label>
 <input type="text" maxlength="64" name="edit[cardid]" id="edit-cardid"  size="30" value="0" />
 <div class="description">会员系统卡号。</div>
</div>
<div class="form-item">
 <label for="edit-mail">E-mail地址: 
 <input type="text" maxlength="64" name="edit[mail]" id="edit-mail"  size="30" value="" class="form-text required" />
 <div class="description">您的邮件地址。</div>
</div>
<div class="form-item">
<label for="edit-sex">性别: </label>






男 <input type="radio" name="edit[sex]"   value="1" />
女 <input type="radio" name="edit[sex]"  value="2" checked=checked/>
</div>

会员来源：
<select name='edit[channel]'  />
    <option value='huiyuanjieshao'>会员介绍</option>
	<option value='jinyihuodong'>金逸活动</option>
	<option value='39yuantiyan'>39元体验</option>
	<option value='thana'>THANA</option>
	<option value='louxiahuodong'>楼下活动</option>
	<option value='meituansousuo'>美团搜索</option>
	<option value='meituantuangou'>美团团购</option>
	<option value='tongcengbangong'>同层办公</option>
	<option value='qita'>其他</option>
	<option value='mamatuantiyan'>妈妈团体验</option>
</select>
		
<div class="form-item">
 <label for="edit-tel">电话: </label><span class="form-required" title="This field is required.">*</span></label>
 <input type="text" maxlength="64" name="edit[tel]" id="edit-tel"  size="30" value="" />
 <div class="description">必填，您的电话，请确保它是正确的。</div>
</div>
<label for="edit-type">会员类型: </br></label>
<?php

include ("entity/Cardrule.php");
$allname=Cardrule::getAllRulename();
foreach($allname as $value){
	
	echo $value['1'];
	echo "<input type='radio' name='edit[type]'  value=\"";
	echo $value['0'];
	echo "\"/></br>";
}

?>
<!--

课程卡或(及)单项卡<input type="radio" name="edit[type]"  value="package"  checked=checked /></br>
习练卡年卡 <input type="radio" name="edit[type]"   value="common_1" /></br>
习练卡半年卡 <input type="radio" name="edit[type]"  value="common_half" /></br>
习练卡10次卡 <input type="radio" name="edit[type]"  value="common_count" /></br>
习练卡双年卡 <input type="radio" name="edit[type]"  value="common_2" /></br>
课程卡+习练卡年卡 <input type="radio" name="edit[type]"  value="both_c1y" /></br>
课程卡+习练卡半年卡 <input type="radio" name="edit[type]"  value="both_chalf" /></br>
体验卡（需要手动改有效期并手动选课：空中10次 儿童瑜伽10次 ） <input type="radio" name="edit[type]"  value="both_c10" /></br>
课程卡+习练卡双年卡 <input type="radio" name="edit[type]"  value="both_c2y" /></br>-->
</div>
<div class="form-item">
 <label for="edit-birthday">生日: </label>
 <input type="text" maxlength="64" name="edit[birthday]" id="edit-birthday"  size="30" value="" />
 <div class="description">出生年月日。</div>
</div>
<div class="form-item">
 <label for="edit-payment">实际消费金额: </label>
 <input type="text" maxlength="64" name="edit[payment]" id="edit-payment"  size="30" value="0" />
</div>
<div class="form-item">
 <label for="edit-payment">赠送有效期(天数): </label>
 <input type="text" maxlength="20" name="edit[additionaldays]" id="edit-additionaldays"  size="30" value="0" />
</div>

<div class="form-item">
 <label for="edit-point">消费级别: </label>
 <input type="text" maxlength="64" name="edit[point]" id="edit-point"  size="30" value="0" />
</div>

<div class="form-item">
 <label for="edit-intro">补充说明: </label>
 <textarea  name="edit[intro]" id="edit-intro"  rows="6" cols="30" value="N/A"></textarea>
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
