<?php

session_start();
	if(!$_SESSION["usercell"]) 
   {
	   header("location:index.php");
  }
	include("header.inc.php");
	include("dbconf/settings.inc.php");
	include("dbconf/dbconnect.inc.php");
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
      <li><a href='register.php'  data-transition='flip' data-ajax='false'>约课</a></li>
      <li><a href="#"  data-ajax="false" class="ui-btn-active ui-state-persist">已选</a></li>
      <li><a href="attendhistory.php" data-transition="flip" data-ajax="false">评价</a></li>
	  <li><a href="registerrm.php" data-transition="flip" data-ajax="false">跑步机</a></li>
	  <li><a href="memberinfo.php" data-transition="flip" data-ajax="false" >个人</a></li>
    </ul>
  </div>
</div>
 
<?php
 	
	
	$sql = "SELECT register_id,class_name,class_design.class_id,class_type,package_id, arrangedate,teacher_name,starttime,endtime,register_record.arrange_id,inner_id
	FROM register_record 
	INNER JOIN class_arrange ON register_record.arrange_id= class_arrange.arrange_id
	INNER JOIN class_design ON class_design.class_id = class_arrange.class_id 
	INNER JOIN teacher_table ON class_arrange.teacher_id = teacher_table.teacher_id
	WHERE register_record.is_attended = 0 AND is_canceled =0 AND member_id={$_SESSION['userid']}";
     $db->query('set names UTF8');  
    
	$res = $db->query($sql);
	$res->setFetchMode(PDO::FETCH_ASSOC);
//echo $sql;
?>
    <form name ="formselect"  method ='post' action ="">
	  <fieldset data-role='controlgroup'>
<?php
		$rows=$res->fetchAll();
		foreach($rows as $row) {		
                
				if($row['arrangedate']>=date('Y-m-d'))
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
	   记得上完课以后签到哦~
	  <input type='submit' data-rel="dialog" id='signbutton' value='签到所选课' name='sign' onclick='signclass()'/> 

    </form>

<script type="text/javascript">

function signclass()
{
  
 document.formselect.action = "signclass.php";
}
function cancelclass()
{
  
		document.formselect.action = "cancelclass.php";
	

}
function checkavailable()
{
  
}

</script>
<!--<script>
$(document).on("pageinit","#registeredclass",function(){
  $("label").on("tap",function(){
    document.getElementById(y).disabled = true;
		alert("You swiped left!");
	
});
});                       

</script> -->
</div>		
 <div data-role="footer" data-position="fixed">Copyright 2015 MoSige Yoga.</div>  
</body>
</html>