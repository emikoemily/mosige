<?php
echo  "<li class='leaf'><a href='account.php'>会员管理</a></li>";
echo "<li class='leaf'><a href='manage_attend.php' target='_blank'>签到表</a></li>";
if($_SESSION["adminuserid"]=="1" ) {
	echo "<li class='leaf'><a href='view_review.php' class='active' >数据统计和后台管理</a></li>";
	echo "<li class='leaf'><a href='register.php'>注册后台用户</a></li>";
	echo "<li class='leaf'><a href='manage_cardrule.php' >添加会员卡规则</a></li>";
}?>