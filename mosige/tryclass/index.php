
<?php
	

	include("header.inc.php");
	
?>

<div>


 

 <h2 class="title"  align="center"> <img src=img/getheadimg.jpg style="vertical-align:middle;" > </h2>
  <h3 class="title"  align="center">莫圣瑜伽生活馆团购体验自助约课系统</h3>
 <div class="content">
 <form method="post" id="user-login-form"  data-ajax="false" action="do_login.php">
<div>
<div class="form-item">

 <label for="usercell">请填写已注册的体验号: <span class="form-required" title="This field is required.">*</span></label>
 <input type="number" maxlength="60" name="usercell" id="usercell"  size="15" value="" class="form-text required" />
</div>
<div class="form-item">
 <label for="password">密码: <span class="form-required" title="This field is required.">*</span></label>
 <input type="password" maxlength="" name="password" id="password"  size="15"  class="form-text required" />
</div>
<input type="submit" id="submit" name="op" value="登录"  class="form-submit" />

</div>
</form>
</div>

</div>


 </body>
</html>