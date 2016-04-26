<?php

session_start();
	include("header.inc.php");

 

?>
</br></br></br></br>
<form method="post" action="do_add_cardrule.php"></br>
卡片规则名称（英文）:
<input type="text" maxlength="64" name="card[rule_name]" id="rule_name"  size="30" value="" /></br>
中文名称:
<input type="text" maxlength="64" name="card[rule_displayname]" id="rule_displayname"  size="30" value="" /></br>

在课表中是否能看到课程进度：</br>
  全部课程-是
<input type="radio" maxlength="64" name="card[level_actual]" id="level_actual1"  size="30" value="package" /></br>
  全部课程-否
<input type="radio" maxlength="64" name="card[level_actual]" id="level_actual2"  size="30" value="common" /></br>
部分。购买了课包可见进度，同时可看到其它无进度课程
<input type="radio" maxlength="64" name="card[level_actual]" id="level_actual2"  size="30" value="both" /></br>
有效天数：
<input type="text" maxlength="64" name="card[rule_days]" id="rule_days"  size="30" value="" /></br>
有效期起始日期：
<input type="date" maxlength="64" name="card[rule_startdate]" id="rule_startdate"  size="30" value="" /></br>
有效期截止日期：
<input type="date"  name="card[rule_enddate]" id="rule_enddate"  size="30" value="" ></br>
卡片规则详细描述：</br>
<textarea type="text" name=" card[rule_description]" id="rule_description"  cols="100" rows="10" value="" /> </textarea></br>
总共允许上课次数：
<input type="text" maxlength="64" name="card[rule_maxcount]" id="rule_maxcount"  size="30" value="" /></br>
是否允许上空中：
<input type="checkbox" maxlength="64" name="card[has_kongzhong]" id="has_kongzhong"  size="30" value="" /> </br>
是否允许上儿童瑜伽：
<input type="checkbox" maxlength="64" name="card[has_ertong]" id="has_ertong"  size="30" value="" /></br>
特殊时间要求（填一个英语和数字组成的词，中间无空格），如周末上课则填：weekend，某一天就填例如：20151025，没有则不填：
<input type="text" maxlength="64" name="card[time_rule]" id="time_rule"  size="30" value="" /></br>
</br></br>
<input type="submit" name="op" value="添加新卡片规则" />
</form>
<div>
</br>
</br>
</br>
</br>
现有卡片规则：</br>
<table width="100%">
<tr><th>卡片名称</th><th>英文名</th><th>有效期天数</th><th>起始日期</th><th>截止日期</th><th>最高次数</th><th>含空中</th><th>含儿童</th><th>特殊日期要求</th><th>操作</th></tr>
<?php 

include ("entity/Cardrule.php");
$allname=Cardrule::getAllRule();
foreach($allname as $value){
	echo "<tr>";
	echo "<td>";
	echo $value['rule_displayname'];
	echo "</td>";
	echo "<td>";
	echo $value['rule_alias'];
	echo "</td>";
	
	echo "<td>";
	echo $value['rule_days'];
	echo "</td>";
		
	echo "<td>";
	echo $value['rule_startdate'];
	echo "</td>";
	
	
	echo "<td>";
	echo $value['rule_enddate'];
	echo "</td>";
	
	//echo "<td width='10%'>";
	//echo $value['rule_description'];
	//echo "</td>";
	
	echo "<td>";
	echo $value['rule_maxcount'];
	echo "</td>";
	
	echo "<td>";
	echo $value['has_kongzhong'];
	echo "</td>";
	
	echo "<td>";
	echo $value['has_ertong'];
	echo "</td>";
	
	echo "<td>";
	echo $value['time_rule'];
	echo "</td>";
	
	
	echo "<td>";
	echo "<a href='do_deletecardrule.php?ruleid={$value['rule_id']}'>删除</a>";
	echo "</td>";
	
	echo "</tr>";
	 
}


?>
</table>
</div>
</br>
</br>
<a href="account.php">返回</a>
 </body>
</html>