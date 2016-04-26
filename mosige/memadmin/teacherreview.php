<meta name="viewport" content="width=device-width,initial-scale=1" charset="utf8">  
<meta http-equiv="content-type" content="text/html;charset=utf8">

<?php
	session_start();
	//include("header.inc.php");
	include("dbconnect.inc.php");
	include("functions.inc.php");
	#如果php配置中，magic_quotes_gpc没有被设置，则执行过滤字符串。
	//$form = check_form($_POST["edit"]);
	//$regid=$_GET["regid"];
echo "<form action='do_teacherreview.inc.php' method='POST'>";	 
echo "<input type='hidden' name='regid' value='{$_GET["regid"]}'>";
echo "<b>上课进度</b></br>";	
echo "<textarea name='classprogress' cols='100' rows='10'>";



echo "</textarea>";
	 
echo "</br><b>完成进度</b></br>";	
echo "<textarea name='completeprogress' cols='100' rows='10'>";



echo "</textarea>";
	 
echo "</br><b>会员身体备注</b></br>";	
echo "<textarea name='memberbody' cols='100' rows='10'>";



echo "</textarea>";
echo "</br><b>会员反映</b></br>";	
echo "<textarea name='membercomment' cols='100' rows='10'>";



echo "</textarea>";
echo  "</BR><input type='submit' value='   提交    '>";

echo "</form>";	
?>
<a href="manage_attend.php"  target="blank">返回</a>