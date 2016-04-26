
<?php
	

	include("header.inc.php");
	
?>
<script type="text/javascript">
$(document).ready(function() {

$("input[name=op]").click(function(e) {
	
var password_new = $("#password_new").val();
var password_new2 = $("#password_new2").val();
if (password_new == '' or password_new2=='' or password_new!=password_new2) {
e.preventDefault();
//alert("请填写取消理由");
$('#placeholder').html("请填写取消理由");
}
});
});
</script>
<script>
	function check_form() {
		//username = document.getElementById("edit-name").value;
		password = document.getElementById("password_new").value;
		password2 = document.getElementById("password_new2").value;
		//mail = document.getElementById("edit-tel").value;
		emsg = "";
		//if(username == "") emsg += "用户名没有填写. \n";
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
<div>


 

 <h2 class="title"  align="center"> <img src=img/getheadimg.jpg style="vertical-align:middle;" > </h2>
  <h3 class="title"  align="center">莫圣瑜伽生活馆会员自助约课系统</h3>
 <div class="content">
 <form method="post" id="user-login-form"  data-ajax="false" action="do_resetp.php">
<div>
修改密码
<span id='placeholder'></span>;
<div class="form-item">
 <label for="usercell">请填写已注册手机号: <span class="form-required" title="This field is required.">*</span></label>
 <input type="number" maxlength="60" name="usercell" id="usercell"  size="15" value="" class="form-text required" />
</div>
<div class="form-item">
 <label for="password_old">旧密码: <span class="form-required" title="This field is required.">*</span></label>
 <input type="password" maxlength="" name="password_old" id="password_old"  size="15"  class="form-text required" />
</div>
<div class="form-item">
 <label for="password_new">新密码: <span class="form-required" title="This field is required.">*</span></label>
 <input type="password" maxlength="" name="password_new" id="password_new"  size="15"  class="form-text required" />
</div>
<div class="form-item">
 <label for="password_confirm">请再输一遍新密码: <span class="form-required" title="This field is required.">*</span></label>
 <input type="password" maxlength="" name="password_new2" id="password_new2"  size="15"  class="form-text required" />
</div>
<input type="submit" id="submit" name="op" value="确定"  class="form-submit" />

</div>
</form>
</div>
<a href="index.php" data-inline='false' data-ajax='false'>返回</a>
</div>


 </body>
</html>