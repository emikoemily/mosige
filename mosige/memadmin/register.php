<?php
	session_start();
	if(!$_SESSION["adminuserid"]) header("Location:index.php");
	 
	include("dbconnect.inc.php");
	if($_POST["op"] == "ע�����ʺ�") {
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
<li class="leaf"><a href="account.php" class="active">��Ա����</a></li>
<li class="leaf"><a href="manage_attend.php"  target="blank" target="_blank">ǩ����</a></li>
<?php
if($_SESSION["adminuserid"]=="1" ) {
echo "<li class='leaf'><a href='view_review.php' >����ͳ��</a></li>";
echo "<li class='leaf'><a href='register.php' class='active'>ע���̨�û�</a></li>";
}
?>
<li class="leaf"><a href="logout.php">ע����¼</a></li>
 
</ul>
</div>
</div>
</td>
   <td id="main">
<div class="breadcrumb"><a href="./">��ҳ</a> &raquo; <a href="./">�û��ʺ�</a></div><h2><?php echo $_SESSION["username"]; ?></h2><ul class="tabs primary">
<?php
   if($_SESSION["adminuserid"]=="1") {//1:admin 2:frontdesk 3:teacher
	   
echo "<li class='active'><a href='register.php'>ע���̨�û�</a></li>";   
echo "<li ><a href='view_admin.php'  class='active'>�û��б�</a></li>";


	   
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
		if(username == "") emsg += "�û���û����д. \n";
		if(password == "") emsg += "����û����д. \n";
		if(password != password2) emsg += "�����������벻ͬ. \n";
		var pattern = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
		if(!pattern.test(mail)) emsg += "�ʼ���ʽ����ȷ. \n";
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
 <label for="edit-name">��¼��: <span class="form-required" title="This field is required.">*</span></label>
 <input type="text" maxlength="60" name="edit[name]" id="edit-name"  size="30" value="" class="form-text required" />
 
</div>
<div class="form-item">
 <label for="edit-pass">����: <span class="form-required" title="This field is required.">*</span></label>
 <input type="password" maxlength="64" name="edit[pass]" id="edit-pass"  size="30" value="" class="form-text required" />
 <div class="description">�������������롣</div>
 <input type="password" maxlength="64" name="edit[pass2]" id="edit-pass2"  size="30" value="" class="form-text required" />
 <div class="description">���ٴ������������롣</div>
</div>
 
 
<label for="edit-type">�û�Ȩ��: </label></br>
����Ա <input type="radio" name="edit[type]"  value="1" /></br>
�ͷ� <input type="radio" name="edit[type]"   value="2" /></br>
��ʦ <input type="radio" name="edit[type]"  value="3" /></br>
��Ȩ�� <input type="radio" name="edit[type]"  value="0" /></br>
</div>

<input type="submit" name="op" value="ע�����ʺ�"  class="form-submit" onclick="return check_form();"  />
<br /><br />
</div></form>

<!-- end content -->
   </td>
  </tr>
 </table>
 </body>
</html>
