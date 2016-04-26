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
	
	$sql = "select * from member_user where member_id={$id}";
	$db->query('set names UTF8');
	$res = $db->query($sql);
	$row = $res->fetch();
	extract($row);
	
	
	}else {
		$id = "";
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
<li class="leaf"><a href="manage_attend.php"  target="blank" target="blank">签到表</a></li>
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
<script>
	function check_form() {
		password = document.getElementById("edit-pass").value;
		password2 = document.getElementById("edit-pass2").value;
		 
		emsg = "";
		if(password != password2) emsg += "两次输入密码不同. \n";
		//var pattern = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
		 
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
<form action="edit_account.php"  method="post" id="user_edit">

<div><div class="form-item">
 <label for="edit-name">用户名: </label>
<span><?php echo $row['member_name'];?></span>
<input type="text" maxlength="64" name="edit[name]" id="edit-name"  size="30" value="<?php echo $row['member_name'];?>" class="form-text required" />
</div>
<div class="form-item">
 <label for="edit-pass">密码(不修改密码请留空): </label>
 <input type="password" maxlength="64" name="edit[pass]" id="edit-pass"  size="30" value="" class="form-text required" />
 <div class="description">请输入您的密码。</div>
 <input type="password" maxlength="64" name="edit[pass2]" id="edit-pass2"  size="30" value="" class="form-text required" />
 <div class="description">请再次输入您的密码。</div>
</div>
<div class="form-item">
 <label for="edit-mail">E-mail地址: 
 <input type="text" maxlength="64" name="edit[mail]" id="edit-mail"  size="30" class="form-text required" value="<?php echo $row['member_email'];?>"  />
 <div class="description">您的邮件地址。</div>
</div>
会员来源： 
<select name='edit[channel]'  />
    <option value='huiyuanjieshao' <?php if($row['member_channel'] == 'huiyuanjieshao'){echo 'selected="selected"';} ?>>会员介绍</option>
	<option value='jinyihuodong' <?php if($row['member_channel'] == 'jinyihuodong'){echo 'selected="selected"';} ?>>金逸活动</option>
	<option value='39yuantiyan' <?php if($row['member_channel'] == '39yuantiyan'){echo 'selected="selected"';} ?>>39元体验</option>
	<option value='thana' <?php if($row['member_channel'] == 'thana'){echo 'selected="selected"';} ?>>THANA</option>
	<option value='louxiahuodong' <?php if($row['member_channel'] == 'louxiahuodong'){echo 'selected="selected"';} ?>>楼下活动</option>
	<option value='meituansousuo' <?php if($row['member_channel'] == 'meituansousuo'){echo 'selected="selected"';} ?>>美团搜索</option>
	<option value='meituantuangou' <?php if($row['member_channel'] == 'meituantuangou'){echo 'selected="selected"';} ?>>美团团购</option>
	<option value='tongcengbangong' <?php if($row['member_channel'] == 'tongcengbangong'){echo 'selected="selected"';} ?>>同层办公</option>
	<option value='qita' <?php if($row['member_channel'] == 'qita'){echo 'selected="selected"';} ?>>其他</option>
	<option value='mamatuantiyan' <?php if($row['member_channel'] == 'mamatuantiyan'){echo 'selected="selected"';} ?>>妈妈团体验</option>
</select>
<div class="form-item">
 <label for="edit-tel">电话: </label><span class="form-required" title="This field is required.">*</span></label>
 <input type="text" maxlength="64" name="edit[tel]" id="edit-tel"  size="30" value=<?php echo $row['member_cell'];?> />
 <div class="description">必填，会员的电话，请确保它是正确的。</div>
</div>
<!--!<label for="edit-type">会员类型: </br></label>
课程卡 <input type="radio" name="edit[type]"  value="package"  /></br>
年卡 <input type="radio" name="edit[type]"   value="common" /></br>
次卡 <input type="radio" name="edit[type]"  value="common_count" /></br>
课程卡高级 <input type="radio" name="edit[type]"  value="both" /></br>-->
</div>



会员卡：
<select name="edit[type]"> 
<option value="package" <?php if($row['rule_name'] == 'package'){echo 'selected="selected"';} ?>>课程卡或单项卡</option> 
<option value="common_1" <?php if($row['rule_name'] == 'common_1'){echo 'selected="selected"';} ?>>习练卡年卡</option> 
<option value="common_c1y_both" <?php if($_GET['type'] == 'common_c1y_both'){echo 'selected="selected"';} ?>>习练卡年卡(含空中)</option>
<option value="common_count" <?php if($row['rule_name'] == 'common_count'){echo 'selected="selected"';} ?>>习练卡次卡 </option> 
<option value="common_count_both" <?php if($row['rule_name'] == 'common_count_both'){echo 'selected="selected"';} ?>>习练卡次卡(含空中) </option> 
<option value="both_c1y" <?php if($row['rule_name'] == 'both_c1y'){echo 'selected="selected"';} ?>>vip</option>
<option value="both_c10" <?php if($row['rule_name'] == 'both_c10'){echo 'selected="selected"';} ?>>体验卡</option> 
<option value="common_2" <?php if($row['rule_name'] == 'common_2'){echo 'selected="selected"';} ?>>双年卡</option> 
<option value="common_half" <?php if($row['rule_name'] == 'common_half'){echo 'selected="selected"';} ?>>习练卡半年卡</option> 
<option value="both_quarter" <?php if($row['rule_name'] == 'both_quarter'){echo 'selected="selected"';} ?>>习练卡季卡 </option> 
<option value="both_weekend" <?php if($row['rule_name'] == 'both_weekend'){echo 'selected="selected"';} ?>>习练卡周末卡</option>
<option value="common_month" <?php if($_GET['type'] == 'common_month'){echo 'selected="selected"';} ?>>月卡</option> 
<option value="both_month" <?php if($_GET['type'] == 'both_month'){echo 'selected="selected"';} ?>>月卡(含空中)</option>  
</select>
<div class="form-item">
 <label for="edit-days">有效期时长: </label>
 <input type="text" maxlength="64" name="edit[days]" id="edit-days"  size="30" value="<?php echo $row['member_days'];?>" />
 <div class="description">会员习练卡的有效期时长(年卡为365，二年卡为730，半年卡为183，次卡为365，单纯课程包会员可不设置或设置为730)。</div>
</div>
<div class="form-item">
 <label for="edit-days">习练卡次卡次数: </label>
 <input type="text" maxlength="64" name="edit[maxdays]" id="edit-maxdays"  size="30" value="<?php echo $row['member_attendmax'];?>" />
 <div class="description">可上课的总次数，一般请填10，</div>
 习练卡次卡已上次数：<?php if((substr($row['member_level'],0,12)=='common_count')) echo $row['member_classcount'];?>
</div>
<!--<div class="form-item">
 <label for="edit-birthday">出生年月日: </label>
 <input type="text" maxlength="64" name="edit[birthday]" id="edit-birthday"  size="30" value="<?php echo $row['member_birthday'];?>" />
 <div class="description">会员的出生年月日。</div>
</div>-->

<div class="form-item">
 <label for="edit-point">消费级别: </label>
 <input type="text" maxlength="64" name="edit[point]" id="edit-point"  size="30" value=<?php echo $row['member_points'];?> />
</div>
<div class="form-item">
 <label for="edit-birthday">有效期开始: </label>
 <input type="text" maxlength="64" name="edit[startdate]" id="edit-birthday"  size="30" value="<?php echo $row['member_startdate'];?>" />
 <div class="description">年-月-日 时:分:秒</div>
</div>
<div class="form-item">
 <label for="edit-birthday">有效期截至: </label>
 <input type="text" maxlength="64" name="edit[enddate]" id="edit-birthday"  size="30" value="<?php echo $row['member_enddate'];?>" />
 <div class="description">年-月-日 时:分:秒</div>
</div>
<div class="form-item">
 <label for="edit-birthday">假期剩余次数: </label>
 <input type="text" maxlength="5" name="edit[leavecounts]" id="edit-leavecounts"  size="30" value="<?php echo $row['member_leavecount'];?>" />
  
</div>
<div class="form-item">
 <label for="edit-birthday">假期剩余天数: </label>
 <input type="text" maxlength="6" name="edit[leavedays]" id="edit-leavedays"  size="30" value="<?php echo $row['member_leavedays'];?>" />
 
</div>
<div class="form-item">
 <label for="edit-intro">补充说明:  </label>
 <textarea  name="edit[intro]" id="edit-intro"  rows="6" cols="30" value="<?php echo $row['member_intro'];?>" ><?php echo $row['member_intro'];?></textarea>
</div>

<?php
if($_GET["id"]!="" && $_SESSION["adminuserid"]==1) {
	echo "<input type='hidden' name='id' value='{$_GET['id']}' />";
}
?>
<input type="submit" name="op" value="更    新"  class="form-submit" onclick="return check_form();"  />
<br /><br />
</div></form>
<!-- end content -->
   </td>
  </tr>
 </table>

 </body>
</html>
