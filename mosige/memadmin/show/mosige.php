

<?php

	session_start();
	include("header.inc.php");
	include("dbconnect.inc.php");
	$db = new PDO($dsn, $dbuser,  $dbpass);
	$sql = "SELECT * FROM yoga_lu.package_show ORDER BY idclass_package_design"; 
	//echo $sql;
	$db->query('set names UTF8');
	$res = $db->query($sql);

?>
 <table id="content">
  <tr>
<td id="sidebar-left"><div class="block block-user" id="block-user-1">
  
 <div class="content">
 
 
<form method=post action="finish_selectclass2.php" name="form1" target="_blank">


<table border="1">
 <thead><tr>
 
 <th></th>
 <th>课程名</th>
 <th>类目</th>
 <th>课时数</th>

  <th>介绍</th>

  <th>价格</th>
 </tr>
 </thead>
<tbody>


<?php 
//$i=1;
$rows=$res->fetchAll();
	foreach($rows as $row) {		
		
    echo "<tr >";
    //echo "<td>{$row['member_name']}</td>";
	echo "<td><input type='checkbox' name='t1[]' value='{$row['package_id']}'></td>";
    echo "<td><b><a href='{$row['weidianlink']}'>{$row['package_name']}</a></b></td>";
	echo "<td>{$row['category']}</td>";
	echo "<td>{$row['package_course_count']}</td>";
	//echo "<td></td>";
	//echo "<td>{$row['package_id']}</td>";

	echo "<td>{$row['package_description']}</td>";
	
	echo "<td>{$row['package_price']}</td>";
    echo "</tr > ";
 
	}
?>
</tbody></table>
<input type="text" name="discount" value="1">
<input type="submit" value="提交">
</form>

<?php 
//	if(mysqli_num_rows($res)==0) echo "没有检索到相关的课程卡或单项卡";
?>
<!-- end content -->
   </td>
  </tr>
 </table>

 </body>
</html>
