<?php

session_start();
	if(!$_SESSION["usercell"]) 
   {
	   header("location:index.php");
  }
	include("header.inc.php");
 
?>

<body>
<script type="text/javascript">
$(document).ready(function() {
//$("input[type=submit]").click(function(e) {
$("input[name=cancelselected]").click(function(e) {
var name = $(":text").val();
 
if (name == '') {
e.preventDefault();
//alert("请填写取消理由");
$('#placeholder').html("请填写取消理由");
}
});
});
</script>
<div data-role="page" id="registeredclass" data-title="已选课列表">
<div data-role="header">
与莫圣携手遇见更好的自己
   <div data-role="navbar">
    <ul>
<?php
	
	?>
      <li><a href='try_register.php'  data-transition='flip' data-ajax='false'>约课</a></li>
      <li><a href="#"  data-ajax="false" class="ui-btn-active ui-state-persist">已选</a></li>
 
	  <li><a href="try_memberinfo.php" data-transition="flip" data-ajax="false" >个人</a></li>
    </ul>
  </div>
</div>
 
<?php
	include(dirname(__FILE__).'/'."dbconf/settings.inc.php");
	include(dirname(__FILE__).'/'."dbconf/dbconnect.inc.php");		
	
	$sql = "SELECT register_id,class_name,class_design.class_id,class_type,package_id, arrangedate,teacher_name,starttime,endtime,try_register_record.arrange_id,inner_id
	FROM try_register_record 
	INNER JOIN class_arrange ON try_register_record.arrange_id= class_arrange.arrange_id
	INNER JOIN class_design ON class_design.class_id = class_arrange.class_id 
	INNER JOIN teacher_table ON class_arrange.teacher_id = teacher_table.teacher_id
	WHERE try_register_record.is_attended = 0 AND is_canceled =0 AND member_id={$_SESSION['userid']}";
     $db->query('set names UTF8');  
	
	$res = $db->query($sql);
//echo $sql;
?>
    <form name ="formselect"  method ='post' action ="">
	  <fieldset data-role='controlgroup'>
<?php
		$res->setFetchMode(PDO::FETCH_ASSOC);
		$rows = $res->fetchAll();
		foreach($rows as $row) {		
                
				if($row['arrangedate']>=date('Y-m-d') )
				{
					//echo $row['register_id'];
					//echo $row['arrange_id'];
					echo "<input type='radio'  name='classcancel' id='radio-{$row['register_id']}' value={$row['register_id']} >";
					echo "<input type='hidden'  name='classcancelcount-{$row['register_id']}' id='hid-{$row['register_id']}' value={$row['arrange_id']} >";
				    echo "<label for='radio-{$row['register_id']}' id='label-{$row['register_id']}'>{$row['arrangedate']} {$row['starttime']} - {$row['endtime']} </br> {$row['class_name']}  {$row['teacher_name']}</label>";		
					echo "<input type='hidden'  name='classstartdate-{$row['register_id']}' id='hid-date-{$row['register_id']}' value={$row['arrangedate']} >";	
					echo "<input type='hidden'  name='classstarttime-{$row['register_id']}' id='hid-time-{$row['register_id']}' value={$row['starttime']} >";	
					echo "<input type='hidden'  name='classtype-{$row['register_id']}' id='hid-classtype-{$row['register_id']}' value={$row['class_type']} >";				 					
				    echo "<input type='hidden'  name='packageid-{$row['register_id']}' id='hid-packageid-{$row['register_id']}' value={$row['package_id']} >";	
					echo "<input type='hidden'  name='innerid-{$row['register_id']}' id='hid-innerid-{$row['register_id']}' value={$row['inner_id']} >";	
				   //echo $row['inner_id'];
			 	
				}else{
					//echo "课程已过期";
				}
			
        } 
?>
      </fieldset>
	  
    <?php 
	
	  echo "<label for='review'>取消预约请填写原因:</label>";
	  echo "<span id='placeholder'></span>";
	  echo "<input type='text' name='cancelreason' id='cancelreason' class='required'>";
	  echo "<input type='submit' value='取消预约所选课' name='cancelselected' onclick='cancelclass()' /> ";
	  ?>
	  </br>


    </form>
	
	
<script type="text/javascript">


function cancelclass()
{
  
		document.formselect.action = "try_cancelclass.php";
	

}
function checkavailable()
{
  
}

</script>

</div>		
 <div data-role="footer" data-position="fixed">Copyright 2015 MoSige Yoga.</div>  
</body>
</html>