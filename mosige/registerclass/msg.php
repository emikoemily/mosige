<?php
	include("header.inc.php");
	 
	$msg="";
	switch($_GET["m"]) {
		case	"register_success"	:
			
			$msg = "恭喜，帐号注册成功。<br />现在您可以使用您的用户名和密码登陆本系统了。<br />";
			echo $msg;
			$href = "<a href='index.php'>返回</a>";
			break;

		case	"login_error"	:
			$msg = "对不起，用户名或密码填写错误。<br />请返回重新填写。<br />";
			echo $msg;
			$href = "<a href='index.php' data-ajax='false'>返回</a>";
			break;
		case	"pass_diff"	:
			$msg = "两遍密码不一样<br />";
			echo $msg;
			$href = "<a href='index.php' data-ajax='false'>返回</a>";
			break;
			
		case	"reset_ok":
			$msg = "密码更新成功，请返回重新登录<br />";
			echo $msg;
			$href = "<a href='index.php' data-ajax='false'>返回</a>";
			break;
	}
?>
 <table id="content">
  <tr>
   <td id="main">
 
<?php echo $href; ?>

<!-- end content -->
   </td>
  </tr>
 </table>
 </body>
</html>
