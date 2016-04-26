<?php 
session_start();
	if(!$_SESSION["usercell"]) 
   {
	   header("location:index.php");
  }
	include("header.inc.php");
 
	
?>


<?php
$disable=0;
$overcount=0;

$now=date('H:i:s');
$sqlbase="SELECT DISTINCT arrange_id ,arrangedate,class_arrange.class_id,class_design.class_id,class_design.class_suitename,
		class_name,teacher_name,starttime,endtime,class_description,try_maxposition,try_registercount,class_design.package_id,class_type,classroom
		FROM class_arrange 
		INNER JOIN class_design ON class_design.class_id = class_arrange.class_id 
		INNER JOIN teacher_table ON class_arrange.teacher_id = teacher_table.teacher_id 
		WHERE  
		(class_design.class_type ='common' )
	    AND";
 
		
		
$sqlpackagebase="SELECT DISTINCT arrange_id ,arrangedate,class_arrange.class_id,class_design.class_id, class_name,class_description, try_maxposition,try_registercount,
teacher_name,starttime,endtime,class_description,maxposition,registercount,class_design.package_id,class_type,classroom,
package_attended,try_package_subscribe.package_enddate,package_course_count
FROM class_arrange 
INNER JOIN class_design ON class_design.class_id = class_arrange.class_id  
INNER JOIN teacher_table ON class_arrange.teacher_id = teacher_table.teacher_id 
INNER JOIN try_package_subscribe ON class_design.package_id = try_package_subscribe.package_id 
INNER JOIN package_design ON class_design.package_id=package_design.package_id 
WHERE try_package_subscribe.member_id = '{$_SESSION[userid]}' 
AND";
		
if($_SESSION['overend']==1 OR $_SESSION["tiyanover"]==1){ 
	$disable=1;
}
if($_SESSION['count_overmax']==1){
	
	$overcount=1;
}



function totalregcount($db,$arrangedate,$starttime,$classroom){
		
	$sqltc="SELECT SUM(try_registercount)+SUM(registercount) as total FROM `class_arrange` where arrangedate='{$arrangedate}' and starttime='{$starttime}' and classroom='{$classroom}'";
	//echo $sqltc;
	$restc=$db->query($sqltc);
	$restc->setFetchMode(PDO::FETCH_ASSOC);
	$rowtc = $restc->fetch();
	return $rowtc['total'];
}


?>

<body>
<div data-role="page" id="chooseclass" data-title="预约课程">
<div data-role="header">
<?php 
//echo "室外气温低，注意保暖，坚持锻炼，提高抵抗力~";
//echo "<img src='img/laba.gif' width='38' height='48'>春节期间瑜伽馆2月1日-2月14日放假 ， 2月15日开课";
echo "<img src='img/laba.gif' width='38' height='48'>大厦1层新设门禁需登记进入或从商场5层电梯来馆";
//echo "<marquee direction='left' behavior='alternate' scrollamount='2' scrolldelay='30'> <img src='img/laba.gif' width='38' height='48'><font size=+1 color=white>亲爱的同学们，本周六由于老师集体外出学习，课程于下午2点开始，具体请关注当日课表哦！</font></marquee>";
?>

<div data-role="navbar">
    <ul>
      <li><a href="#"  data-transition="flip" data-ajax="false" class="ui-btn-active ui-state-persist" >约课</a></li>
      <li><a href="try_registered.php"  data-ajax="false"  >已选</a></li> 
	  <li><a href="try_memberinfo.php" data-transition="flip" data-ajax="false" data-prefetch="true">个人</a></li>
    </ul>
  </div>

</div>

<form method ='post' action='try_chooseclass.php' >
<div data-role="collapsible" >
  
<?php
	echo "<h1>点击查看 ".date('Y-m-d')." 的课表</h1>";
	//echo $_SESSION["tiyanover"];
    echo "<p>";
	include(dirname(__FILE__).'/'."dbconf/settings.inc.php");
	include(dirname(__FILE__).'/'."dbconf/dbconnect.inc.php");
	//echo dirname(__FILE__).'/'."dbconf/settings.inc.php";
	$sqlenable=0;
	$sql1enable=0;
	$sql= $sqlpackagebase." (arrangedate = \"".date('Y-m-d')."\") AND (class_arrange.starttime>'{$now}') AND (package_enddate is NULL or package_enddate >= \"".date('Y-m-d')."\")  ORDER BY endtime;";
		
		$sql1=$sqlbase." (arrangedate = \"".date('Y-m-d')."\") AND  (class_arrange.starttime>'{$now}') ORDER BY endtime;";
	if($_SESSION[userlevel]=='common'  or $_SESSION[userlevel]=='common_count' or $_SESSION[userlevel]=='common_half' ){
		
		$sql1enable=1;
	}
	elseif($_SESSION[userlevel]=='package'){
		
		$sqlenable=1;
	}
	elseif($_SESSION[userlevel]=='both'  or $_SESSION[userlevel]=='both_count' or substr($_SESSION[userlevel],0,5)=='tiyan'){//sql:package not check  sql1:common
		
		$sqlenable=1;
		
		$sql1enable=1;
	}
	else{
		
		echo "欢迎";
		 
	}

	$db->query('set names UTF8'); 
	function draw_packagetable($db,$rows,$disable){
		
	   foreach($rows as $row) {		
		 
				if($row['class_type']=='set' & $row['package_attended']>1){
						 
					continue;
					}
	            else{
					
				$availeble=$row['try_maxposition']-$row['try_registercount'];
				echo "<input  type='radio'  name='classchoice' id='checkbox-{$row['arrange_id']}' value={$row['arrange_id']}>";
				echo "<label for='checkbox-{$row['arrange_id']}'>  {$row['starttime']} - {$row['endtime']}  </label>";
			    
				echo "<script>checkavailable({$availeble},'checkbox-{$row['arrange_id']}');</script>";
				echo "<script>checkdisable({$_SESSION[$row['package_id']]},'checkbox-{$row['arrange_id']}');</script>";
				echo "<script>checkdisable({$_SESSION[$row['package_id'].'_attended']},'checkbox-{$row['arrange_id']}');</script>";
				echo "<script>checkdisable({$disable},'checkbox-{$row['arrange_id']}');</script>";
				echo "<ul data-role='listview' data-inset='false'>";
				echo "<li data-inset='false'><a href='packagedetail/{$row['package_id']}.php' data-ajax='false'  ><img src=img/{$row['package_id']}.png ><b>{$row['class_name']}</b></br> {$row['teacher_name']} 教室:{$row['classroom']} </br>已预约 ". totalregcount($db,$row['arrangedate'],$row['starttime'],$row['classroom'])."  空位： {$availeble} </a> </li>";
				echo "</ul>";
				
				echo "<input type='hidden'  name='classpid-{$row['arrange_id']}' id='pid-{$row['arrange_id']}' value={$row['package_id']}>";
				echo "<input type='hidden'  name='classpcount-{$row['arrange_id']}' id='pcount-{$row['arrange_id']}' value={$row['package_course_count']}>";
				echo "<input type='hidden'  name='classtype-{$row['arrange_id']}' id='type-{$row['arrange_id']}' value={$row['class_type']}>";
				//echo " <li><a href='packagedetail/set1.html' ></a><li>";
				
				}
				 
		 } 
	   
        }
		
		function draw_commontable($db,$rows1,$disable,$overcount){
			foreach($rows1 as $row1) {		
		 
 
				$availeble=$row1['try_maxposition']-$row1['try_registercount'];
				
                echo "<input  type='radio'  name='classchoice' id='checkbox-{$row1['arrange_id']}' value={$row1['arrange_id']}>";
				
				echo "<label for='checkbox-{$row1['arrange_id']}'>    {$row1['starttime']} - {$row1['endtime']}  </label>";
				echo "<script>checkavailable({$availeble},'checkbox-{$row1['arrange_id']}');</script>";
				echo "<script>checkdisable({$disable},'checkbox-{$row1['arrange_id']}');</script>";
				echo "<script>checkdisable({$overcount},'checkbox-{$row1['arrange_id']}');</script>"; 
				echo "<ul data-role='listview' data-inset='false'>";
				echo "<li data-inset='false'><a href='packagedetail/{$row1['package_id']}.php' data-ajax='false'  ><img src=img/{$row1['package_id']}.png ><b>{$row1['class_name']}</b>  {$row['try_maxposition']} {$row1['teacher_name']} 教室:{$row1['classroom']}</br>已预约 ". totalregcount($db,$row1['arrangedate'],$row1['starttime'],$row1['classroom'])."   空位： {$availeble}</a> </br></li>";
				echo "</ul>";
				echo "<input type='hidden'  name='classpid-{$row1['arrange_id']}' id='pid-{$row1['arrange_id']}' value={$row1['package_id']}>";
				echo "<input type='hidden'  name='classpcount-{$row1['arrange_id']}' id='pcount-{$row1['arrange_id']}' value={$row1['package_course_count']}>";
				echo "<input type='hidden'  name='classtype-{$row1['arrange_id']}' id='type-{$row1['arrange_id']}' value={$row1['class_type']}>";
						
		}
			
		}
	
 if($sqlenable==1){	
	$res = $db->query($sql);
	$res->setFetchMode(PDO::FETCH_ASSOC);
	$rows = $res->fetchAll();
	//echo $sql;
	echo "体验课单项课课表:";//set1".$_SESSION["set1"]."a".$_SESSION["set1_attended"]."set1".$_SESSION["set2"]."a".$_SESSION["set2_attended"];
	echo "	<fieldset data-role='controlgroup'>";
	if(($_SESSION["intro"]=="25day" and date('Y-m-d')=="2015-10-25") or $_SESSION["intro"]!="25day" ){
    draw_packagetable($db,$rows,$disable);
	}
	echo   "</fieldset>"; 
 }
 

 if($sql1enable==1){
	$res1 = $db->query($sql1);
	$res1->setFetchMode(PDO::FETCH_ASSOC);
	$rows1 = $res1->fetchAll();
   echo "体验课习练课课表:";
    echo " <fieldset data-role='controlgroup'>";
		if(($_SESSION["intro"]=="25day" and date('Y-m-d')=="2015-10-25") or $_SESSION["intro"]!="25day" ){
		draw_commontable($db,$rows1,$disable,$overcount);}
    echo "</fieldset>";	 
 }
 



?>       
 


</p>
</div>
<?php

echo "<div data-role='collapsible'>";
echo   "<h1>点击查看 ".date("Y-m-d",strtotime("+1 day"))." 的课表</h1>";
echo   "<p>";

	
	   
	if($_SESSION[userlevel]=='common' or $_SESSION[userlevel]=='common_count' ){
		$sqlming1 = $sqlbase." (arrangedate = \"".date("Y-m-d",strtotime("+1 day"))."\") ORDER BY endtime;";	
		$sql1enable=1;
	}
	elseif($_SESSION[userlevel]=='package'){
		$sqlming= $sqlpackagebase." (arrangedate = \"".date("Y-m-d",strtotime("+1 day"))."\") AND (package_enddate is NULL or package_enddate >= \"".date("Y-m-d",strtotime("+1 day"))."\") ORDER BY endtime;";
		$sqlenable=1;
	}
	
	elseif($_SESSION[userlevel]=='both'  or $_SESSION[userlevel]=='both_count' or substr($_SESSION[userlevel],0,5)=='tiyan'){//sql:package not check  sql1:common
		$sqlming= $sqlpackagebase." (arrangedate = \"".date("Y-m-d",strtotime("+1 day"))."\") AND (package_enddate  is NULL or package_enddate  >= \"".date("Y-m-d",strtotime("+1 day"))."\") ORDER BY endtime;";
		$sqlenable=1;
		$sqlming1=$sqlbase." (arrangedate = \"".date("Y-m-d",strtotime("+1 day"))."\");";
		$sql1enable=1;
	}
	
	else{
		
		echo "欢迎";
		
	}

	if($sqlenable==1){	
	$resming = $db->query($sqlming);
	$resming->setFetchMode(PDO::FETCH_ASSOC);
	$rowsming = $resming->fetchAll();
	echo "体验课单项课课表:";
 
	echo "	<fieldset data-role='controlgroup'>";
		if(($_SESSION["intro"]=="25day" and date("Y-m-d",strtotime("+1 day"))=="2015-10-25") or $_SESSION["intro"]!="25day" ){
		draw_packagetable($db,$rowsming,$disable); }
	echo   "</fieldset>"; 
 }
 

 if($sql1enable==1){
		$resming1 = $db->query($sqlming1);
		$resming1->setFetchMode(PDO::FETCH_ASSOC);
		$rowsming1 = $resming1->fetchAll();
   echo "体验课习练课课表:";

   echo " <fieldset data-role='controlgroup'>";
	if(($_SESSION["intro"]=="25day" and date("Y-m-d",strtotime("+1 day"))=="2015-10-25")or $_SESSION["intro"]!="25day" ){
		//echo $_SESSION["intro"];
		//echo date("Y-m-d",strtotime("+1 day");
	draw_commontable($db,$rowsming1,$disable,$overcount); }
	echo "</fieldset>";	 
 }
	


?>
</p>
</div>
<div data-role="collapsible">
 <?php echo " <h1>点击查看 ".date("Y-m-d",strtotime("+2 day"))." 的课表 </h1>";
 echo " <p>";

	//include("..\dbconf\settings.inc.php");
		   
		if($_SESSION[userlevel]=='common'  or $_SESSION[userlevel]=='common_count' or $_SESSION[userlevel]=='common_half' ){
		$sqlhou1 = $sqlbase." (arrangedate = \"".date("Y-m-d",strtotime("+2 day"))."\") ORDER BY endtime;";	
		$sql1enable=1;
	}
	elseif($_SESSION[userlevel]=='package'){
		$sqlhou= $sqlpackagebase." (arrangedate = \"".date("Y-m-d",strtotime("+2 day"))."\") AND (package_enddate is NULL or package_enddate >= \"".date("Y-m-d",strtotime("+2 day"))."\") ORDER BY endtime;";
		$sqlenable=1;
	}
	
	elseif($_SESSION[userlevel]=='both'  or $_SESSION[userlevel]=='both_count' or substr($_SESSION[userlevel],0,5)=='tiyan'){//sql:package not check  sql1:common
		$sqlhou= $sqlpackagebase." (arrangedate = \"".date("Y-m-d",strtotime("+2 day"))."\") AND (package_enddate  is NULL or package_enddate >= \"".date("Y-m-d",strtotime("+2 day"))."\") ORDER BY endtime;";
		$sqlenable=1;
		$sqlhou1=$sqlbase." (arrangedate = \"".date("Y-m-d",strtotime("+2 day"))."\");";
		$sql1enable=1;
	}
 
	else{
		
		echo "欢迎";
		
	}
 
	if($sqlenable==1){	
		$reshou = $db->query($sqlhou);
		$reshou->setFetchMode(PDO::FETCH_ASSOC);
		$rowshou = $reshou->fetchAll();
		echo "体验课单项课课表:";
		echo "	<fieldset data-role='controlgroup'>";
		if(($_SESSION["intro"]=="25day" and date("Y-m-d",strtotime("+2 day"))=="2015-10-25") or $_SESSION["intro"]!="25day" ){
		draw_packagetable($db,$rowshou,$disable);}
		echo   "</fieldset>"; 
 }
 

 if($sql1enable==1){
		$reshou1 = $db->query($sqlhou1);
		$reshou1->setFetchMode(PDO::FETCH_ASSOC);
		$rowshou1 = $reshou1->fetchAll();
		echo "体验课习练课课表:";
		echo " <fieldset data-role='controlgroup'>";
		if(($_SESSION["intro"]=="25day" and date("Y-m-d",strtotime("+2 day"))=="2015-10-25") or $_SESSION["intro"]!="25day" ){
		draw_commontable($db,$rowshou1,$disable,$overcount);}
		echo "</fieldset>";	 
 }
?> 
 
</p>

</div>  

<?php 
function showclass($title,$date,$userlevel,$db,$disable,$sqlpackagebase,$sqlbase,$overcount){
	echo "<div data-role='collapsible'>"; 
	echo " <h1>".$title." ".$date."</h1>";
	echo " <p>";

	if($userlevel=='common'  or $userlevel=='common_count' or $userlevel=='common_half' ){
		$sqlhou1 = $sqlbase." (arrangedate = \"".$date."\") ORDER BY endtime;";
		$sql1enable=1;
	}
	elseif($userlevel=='package'){
		$sqlhou= $sqlpackagebase." (arrangedate = \"".$date."\") AND (package_enddate is NULL or package_enddate >= \"".$date."\") ORDER BY endtime;";
		$sqlenable=1;
	}
	
	elseif($userlevel=='both'  or $userlevel=='both_count' or substr($userlevel,0,5)=='tiyan'){//sql:package not check  sql1:common
		$sqlhou= $sqlpackagebase." (arrangedate = \"".$date."\") AND (package_enddate  is NULL or package_enddate >= \"".$date."\") ORDER BY endtime;";
		$sqlenable=1;
		$sqlhou1=$sqlbase." (arrangedate = \"".$date."\");";
		$sql1enable=1;
	}
	
	else{
	
		echo "欢迎";
	
	}
	
	if($sqlenable==1){
		$reshou = $db->query($sqlhou);
		$reshou->setFetchMode(PDO::FETCH_ASSOC);
		$rowshou = $reshou->fetchAll();
		echo "体验课单项课课表:";
		echo "	<fieldset data-role='controlgroup'>";
		if(($_SESSION["intro"]=="25day" and $date=="2015-10-25") or $_SESSION["intro"]!="25day" ){
			draw_packagetable($db,$rowshou,$disable);}
			echo   "</fieldset>";
	}
	
	
	if($sql1enable==1){
		$reshou1 = $db->query($sqlhou1);
		$reshou1->setFetchMode(PDO::FETCH_ASSOC);
		$rowshou1 = $reshou1->fetchAll();
		echo "体验课习练课课表:";
		echo " <fieldset data-role='controlgroup'>";
		if(($_SESSION["intro"]=="25day" and $date=="2015-10-25") or $_SESSION["intro"]!="25day" ){
			draw_commontable($db,$rowshou1,$disable,$overcount);}
			echo "</fieldset>";
	}

	echo  "</p>";
	echo "</div>";
}





//if($_SESSION["usercell"]=='18500333680'){
	showclass("[假期提前预约]四月三十日","2016-04-30",$_SESSION['userlevel'],$db,$disable,$sqlpackagebase,$sqlbase,$overcount);
	showclass("[假期提前预约]五月一日","2016-05-01",$_SESSION['userlevel'],$db,$disable,$sqlpackagebase,$sqlbase,$overcount);
	showclass("[假期提前预约]五月二日","2016-05-02",$_SESSION['userlevel'],$db,$disable,$sqlpackagebase,$sqlbase,$overcount);
	//showclass("五月三日","2016-05-03",$_SESSION['userlevel'],$_SESSION["hasextended"],$db,$disable,$sqlpackagebase,$sqlbase,$overcount,$arr_packageid);
	
	//} 

	?>


		<input type='submit' value='提交' data-inline="false" onclick="checkavailable()" />
        </form>
  <div data-role="footer"  data-position="fixed">Copyright 2015 MoSige Yoga.</div>  

</body>
</html> 
