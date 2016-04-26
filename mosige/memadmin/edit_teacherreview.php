<meta name="viewport" content="width=device-width,initial-scale=1" charset="utf8">  
<meta http-equiv="content-type" content="text/html;charset=utf8">

<?php
	session_start();
	include("entity/TeacherReview.php");
	
		
	
echo "<form action='do_edit_teacherreview.inc.php' method='POST'>";	 
echo "<input type='hidden' name='regid' value='{$_GET["regid"]}'>";
echo "<b>上课进度</b></br>";	
echo "<textarea name='classprogress' cols='100' rows='10'>";
$cp=TeacherReview::getReview($_GET["regid"], 'classprogress');
echo $cp['review_content'];

echo "</textarea>";

echo "</br><b>完成进度</b></br>";	
echo "<textarea name='completeprogress' cols='100' rows='10'>";
$cp=TeacherReview::getReview($_GET["regid"], 'completeprogress');
echo $cp['review_content'];



echo "</textarea>";
	 
echo "</br><b>会员身体备注</b></br>";	
echo "<textarea name='memberbody' cols='100' rows='10'>";
$cp=TeacherReview::getReview($_GET["regid"], 'memberbody');
echo $cp['review_content'];



echo "</textarea>";
echo "</br><b>会员反映</b></br>";	
echo "<textarea name='membercomment' cols='100' rows='10'>";

$cp=TeacherReview::getReview($_GET["regid"], 'membercomment');
echo $cp['review_content'];


echo "</textarea>";
echo  "</BR><input type='submit' value='   提交    '>";

echo "</form>";	
?>
<a href="manage_attend.php"  target="blank">返回</a>