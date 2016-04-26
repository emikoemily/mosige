<?php
	include("header.inc.php");
	switch($_GET["m"]) {
		case	"register_success"	:
			$msg = "恭喜，帐号注册成功。<br /><br />";
			$href = "<a href='add_account.php'>返回</a>";
			break;
		case	"update_enddate_success"	:
			$msg = "有效期修改成功。<br /><br />";
			$href = "<a href='view_account.php'>返回</a>";
			break;
		case	"update_success_manage_class"	:
			$msg = "操作成功。<br />";
			$href = "<a href='manage_class.php'>返回</a>";
			break;
		case	"addcard_success"	:
			$msg = "添加成功。<br />";
			$href = "<a href='manage_cardrule.php'>返回</a>";
			break;
		case	"update_success_edit_account"	:
			$msg = "操作成功。<br />";
			$href = "<a href='view_account.php'>返回</a>";
		
			break;
		case	"leave_agree"	:
			$msg = "操作成功。<br />";
			$href = "<a href='manage_leave.php'>返回</a>";
			break;
		case	"update_success_select_class"	:
			$msg = "操作成功。<br />";
			$href = "<a href='select_class.php'>返回</a>";
			break;
        case	"update_success_manage_leave"	:
			$msg = "操作成功。<br />";
			$href = "<a href='manage_leave.php'>返回</a>";
			break;
        case	"update_success_manage_attend"	:
			$msg = "操作成功。<br />";
			$href = "<a href='manage_attend.php'>返回</a>";
			break;
case	"update_success_manage_rm":
			$msg = "操作成功。<br />";
			$href = "<a href='manage_rm.php'>返回</a>";
			break;			
		case	"upload_success"	:
			$msg = "照片上传成功。<br />";
			$href = "<a href='account.php'>返回</a>";
			break;
		case	"del_success"	:
			$msg = "帐号信息删除成功，请返回。<br />";
			$href = "<a href='view_account.php'>返回</a>";
			break;
		case	"del_success_try"	:
			$msg = "帐号信息删除成功，请返回。<br />";
				$href = "<a href='view_try.php'>返回</a>";
				break;
		case	"mail_success"	:
			$msg = "修改密码确认邮件已经发送到您的信箱，请注意查收。<br />";
			$href = "<a href='index.php'>返回</a>";
			break;
		case	"login_error"	:
			$msg = "对不起，用户名或密码填写错误。<br />请返回重新填写。<br />";
			$href = "<a href='login.php'>返回</a>";
			break;
	}
?>
 <table id="content">
  <tr>
   <td id="main">
<div class="breadcrumb"><a href="/drupal/">主页</a></div><h2>消息</h2>
<!-- begin content -->

<?php echo $msg; ?>
<?php echo $href; ?>

<!-- end content -->
   </td>
  </tr>
 </table>
 </body>
</html>
