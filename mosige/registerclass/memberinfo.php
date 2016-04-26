<?php 

session_start();
include("entity/Investigation.php");

	if(!$_SESSION["usercell"]) 
   {
	   header("location:index.php");
  }
 /* if($_SESSION["usercell"]=='13810532905'){  
  	 
  	if(!Investigation::ismemdone($_SESSION['userid'])){
  		 
  		header("location:investigation.php");
  	 
  	}
  }*/
  
  

   
include("header.inc.php");


?>
<script type="text/javascript">


$(document).ready(function() {


$("input[name=submitmiss]").click(function(e) {
	//var name = $('input[type="date"]').val();
	var name = $('#seldate').val();
 //alert(name);
	if (name == '') {
		e.preventDefault();
		$('#placeholder').html("请选择补课日期");
	}
	yourtime = name.replace("-","/");//替换字符，变成标准格式
	var d2=new Date();//取今天的日期
	d2.setDate(d2.getDate() + 31);
	var d1 = new Date(Date.parse(yourtime));

	if (d1>d2) {
	e.preventDefault();
	//alert(d2);
	$('#placeholder').html("补课日期请选择一个月内的时间");
}
});
});



</script>
<body>
<!-- first page-->
<div data-role="page" id="meminfo" data-title="会员信息">
<div data-role="header">
与莫圣携手遇见更好的自己
<div data-role="navbar">
    <ul>
     	<?php
	include(dirname(__FILE__).'/'."dbconf/settings.inc.php");
	include(dirname(__FILE__).'/'."dbconf/dbconnect.inc.php"); 
	include("entity/Leave.php");
	//include("entity/Investigation.php");
	
	?>
	<li><a href='register.php'  data-transition='flip' data-ajax='false'>约课</a></li>
      <li><a href="registered.php"  data-ajax="false" >已选</a></li>
      <li><a href="attendhistory.php" data-transition="flip" data-ajax="false">评价</a></li>
	  <li><a href="registerrm.php" data-transition="flip" data-ajax="false" >跑步机</a></li>
	  <li><a href="#" data-transition="flip" data-ajax="false" class="ui-btn-active ui-state-persist">个人</a></li>
    </ul>
</div>
</div>

<p>
 <?php 
 
if($_SESSION["usercell"]=='13810532905'){
	
	echo "<a href='detail.php'>detail</a>";
	
	if(!Investigation::ismemdone($_SESSION['userid'])){
		//echo "<div data-role='dialog' id='invest'>";
		header("location:investigation.php");
		//echo "<a href='investigation.php' data-role='button' data-ajax='false' data-inline='true'>调查问卷</a>";
		//echo "</div>";
	}
	
	
	//$actualdays=Leave::getRequestDays($_SESSION["userid"]);
	//Leave::reduceLeaveDays($actualdays,$_SESSION['userid']);
	//echo "可用的假期:";//.$actualdays;
	//echo "</br> 还剩".$_SESSION[leavecount]."次.</br>";
//	echo "</br>。还剩".$_SESSION[leavedays]."天.</br>";
	//echo "<a href='#leaverequest' data-role='button' data-inline='true' data-transition='slidedown' >请假操作</a>";
	
}
function applyleave(){
	
	echo "<br><br>可用的假期:";//.$actualdays;
	echo "</br>还有".$_SESSION[leavecount]."次.</br>";
	echo "</br>还有".$_SESSION[leavedays]."天.</br>";
	echo "<a href='#leaverequest' data-role='button' data-inline='true' data-transition='slidedown' >请假操作</a>";
	
	
	
}
function checkjump($db,$memid,$pid){
	   
	  $sql_chkjump="SELECT DISTINCT inner_id FROM jump_record WHERE `member_id`={$memid} AND `package_id`='{$pid}' ORDER BY inner_id";  
	  $res_chkjump=$db->query($sql_chkjump);
	  $attendinnerid=Array();
	  $res_chkjump->setFetchMode(PDO::FETCH_ASSOC);
	  $rows=$res_chkjump->fetchAll();
	  foreach($rows as $row_chkjump){		
		  			 
			$attendinnerid[] =  $row_chkjump['inner_id'];
		
     } 
	 $where="";
	 if(count($attendinnerid)!=0){
		$last=$attendinnerid[count($attendinnerid)-1];
		 
	  
	 
	  foreach($attendinnerid as $eachattend){
	    $where=$where." AND `inner_id` !={$eachattend}";
	  }
	  $where=$where." AND `inner_id` <={$last}";
	  //echo $where;
	 
	 
	  $sql_jump="SELECT class_id,class_name,class_description FROM class_design WHERE `package_id`='{$pid}' ".$where ." ORDER BY inner_id;";
	  $res_jump=$db->query($sql_jump);
	  $res_jump->setFetchMode(PDO::FETCH_ASSOC);
	  $rows = $res_jump->fetchAll();
	  $rowCount = count($rows);
	  if($rowCount>0)
	  {
		  echo "跳课:";
		  
	  	  foreach($rows as $row_jump){
		 	 echo "{$row_jump['class_name']} ";
		 	 echo "{$row_jump['class_description']} ";
	      }
	  }
		else{
			echo "课程都按进度上了";
		}
	 }
	 else{
		 
		 
	 }
	  
}
function findprogress($db,$mid,$pid){
	$sqlprogress="SELECT DISTINCT class_description 
	FROM class_arrange 
	inner join yoga_lu.register_record ON register_record.arrange_id = class_arrange.arrange_id
	inner join class_design ON class_design.class_id=class_arrange.class_id
	 
	 where  
	is_attended!=0
	
	AND
	class_design.package_id='{$pid}'
	AND
    register_record.member_id={$mid} 
    ORDER BY class_description;
    
	 ";
	 
	 //echo  $sqlprogress;//AND 
	//class_arrange.arrangedate>='{$_SESSION[start_date]}'
	 $prgress=$db->query($sqlprogress);
	 $prgress->setFetchMode(PDO::FETCH_ASSOC);
	 $rows = $prgress->fetchAll();
     
	 $show="";
	 foreach($rows as $row){
		 $show=$show.$row['class_description']." ";
		 
	 }
	   
	 return $show;
}
function classprocess($db,$type){
		if($type=='package')// or $type=='set'
		{
			$sql_checkset="select package_subscribe.package_id,package_name,package_attended,payment_id FROM package_subscribe inner join package_design ON package_subscribe.package_id=package_design.package_id where member_id={$_SESSION['userid']} and package_subscribe.is_finished!=1  order by payment_id,package_name";// AND package_subscribe.is_finished!=1
			//echo $sql_checkset;
			$db->query('set names UTF8'); 
			$res_checkset=$db->query($sql_checkset);
			$res_checkset->setFetchMode(PDO::FETCH_ASSOC);
			$rows = $res_checkset->fetchAll();
			
			foreach($rows as $row_checkset) {	
				echo "</br><b>{$row_checkset['package_name']}</b>(课程包编号:{$row_checkset['payment_id']})";
				echo " 上了";
								
				echo "{$row_checkset['package_attended']}节课</br>";
				 
				echo "已完成： ".findprogress($db,$_SESSION['userid'],$row_checkset['package_id'])."</br>";
				
				}
		}
	    elseif($type=='common'){
			echo "</br>开卡日期:{$_SESSION[start_date]}</br>";
		    echo "有效期至：{$_SESSION[end_date]}";
			echo "</br>已上次数:    $_SESSION[classcount]</br>";
			 
		}
		elseif($type=='common_half'){
			echo "</br>开卡日期:{$_SESSION[start_date]}</br>";
		   echo "有效期至：{$_SESSION[end_date]}";
			echo "</br>已上次数:    $_SESSION[classcount]</br>";
		}
		elseif((substr($type,0,12)=='common_count')){
			echo "</br>开卡日期:{$_SESSION[start_date]}</br>";
		    echo "有效期至：{$_SESSION[end_date]}";
			echo "</br>已上次数/总次数:    $_SESSION[classcount]/$_SESSION[attendmax]</br>";
			if($_SESSION["count_overmax"]==1){
			echo "</br>次数已上完";
		    }
		}
		elseif($type=='both_count'){
			echo "体验卡";
			
		    echo "有效期至：2015-11-08";//{$_SESSION[end_date]}";
			$yishang=0;
			$sql_2ci="SELECT COUNT(*) FROM `register_record` WHERE member_id={$_SESSION["userid"]} AND is_canceled!=1";
				$res_2ci=$db->query($sql_2ci);
				$res_2ci->setFetchMode(PDO::FETCH_NUM);
				$rs = $res_2ci->fetch();
				$rowCount = count($rs);
				if($rowCount>0){
    
					 
					$yishang=$rs[0];
					if($rs[0]>=2){
					$_SESSION["tiyanover"]=1;}
					else{
						$_SESSION["tiyanover"]=0;
						}
			
				}
			echo "</br>已使用次数/总次数:   {$yishang}/2</br>";
				
			if($_SESSION["tiyanover"]==1){
			echo "</br>次数已上完";
		    }
		}
		elseif($type=='common_two'){
			echo "</br>开卡日期:{$_SESSION[start_date]}</br>";
		    echo "有效期至：{$_SESSION[end_date]}";
			echo "</br>已上次数:    $_SESSION[classcount]</br>";
		}
		else{
			
		}
}
     
    $arr_paymentid = unserialize($_SESSION['paymentOfUser']); 
 
	$arr_paymentstartdate = unserialize($_SESSION['paymentOfUser_startdate']);
	$arr_paymentenddate = unserialize($_SESSION['paymentOfUser_enddate']);
	 
 
    echo "你好 {$_SESSION[user_name]}</br>";
	echo "手机号:".$_SESSION[usercell];
	 
   if($_SESSION[userlevel]=='common' or (substr($_SESSION[userlevel],0,12)=='common_count'))// or $_SESSION[userlevel]=='common_count' or $_SESSION[userlevel]=='both')both 过期随package
	{ 
	echo "</br>卡状态:";
	if($_SESSION[overend]==1)
	{
		echo "已到期</br>";
		
	}
	elseif($_SESSION[isleave]==1){
		
		echo "已失效</br>";
		
	}
	else{
		echo "正常</br>";
	}
 }
  
 
/////////////////////////////////////////////////////////////////////////////	
	function checkSubs($payid){
		
		
	}
 
	
	if($_SESSION[userlevel]=='package' or ($_SESSION[userlevel]=='both' AND $_SESSION[userrule]!='common_count_both') )
	{
		
	 
		if($_SESSION[userlevel]=='both'){
		 
			};
	 
		echo "</br>";
		echo "<table>";
		echo "<tr><td>已开卡课程包编号：    </td>";
		foreach($arr_paymentid as $pidvalue){
			echo "<td>{$pidvalue}  &nbsp  </td>";
		}
		echo "<tr><td>课程卡卡包开卡日期：    </td>";
		foreach($arr_paymentstartdate as $pstartvalue){
		echo "<td>{$pstartvalue}  &nbsp  </td>";	
	}
		echo "</tr>";
		echo "<tr><td>课程卡卡包有效期至：</td>";
		foreach($arr_paymentenddate as $pendvalue){
		echo "<td>{$pendvalue} &nbsp</td>";	
	}
		echo "</tr>";
	    echo "</table>";
		echo "</br>上课情况:</br>";
		classprocess($db,'package');
		
		
		if($_SESSION["userrule"]=="both_c1y" or $_SESSION["userrule"]=="package"){
			applyleave();
		}
		
		
		
		
		if($_SESSION["paymentend"]==2 ){
			if($_SESSION["hasextended"]==0){
	    		echo "<a href='#missclass' data-role='button' data-inline='true' data-transition='slideup'>有课程包已经到期，看看是否需要补课</a></br>";
				}
			else{
				
				
			}
		}
		
		}
	elseif($_SESSION[userlevel]=='common' or $_SESSION[userlevel]=='common_half')
	{
		if($_SESSION["userrule"]=="common_1" or $_SESSION["userrule"]=="common_half"){
			applyleave();
		}
		
		echo "</br>上课情况:</br>";
		classprocess($db,'common');
		 
	
	}
	
	elseif($_SESSION[userlevel]=='common_count' or $_SESSION[userrule]=='common_count_both')
	{    
	 
		echo "</br>上课情况:</br>";
		classprocess($db,'common_count');
		 
	}
	elseif($_SESSION[userlevel]=='both_count')
	{    
	 
		echo "</br>上课情况:</br>";
		classprocess($db,'both_count');
		 
	}

	echo "</br>";
	
 ?>




<a href="logout.php" data-ajax="false">退出登录 </a>
</p>
</div>



<div data-role="dialog" id="leaverequest">
<header data-role="header"><h1>请假系统</h1></header>  
   <div data-role="content" class="content">  

<?php
if(($_SESSION["end_date"]!=NULL) AND ($_SESSION["end_date"]!='0000-00-00 00:00:00') ){

if($_SESSION[isleave]==0){
echo "<form id='leaveform' action=applyleave.php method=post accept-charset='GB2312'>";
 echo "请填写请假的时间,并简述请假理由 ";
 echo "<input type='date' name='leavestart' id='leavestart' placeholder='从哪天？'>";
 echo "<input type='date' name='leaveend' id='leaveend' placeholder='到哪天？'>";
 echo "<input type='text' name='leavereason' id='leavereason' placeholder='请假理由'>";
 //echo "<a href='applyleave.php' data-role='button' data-inline='true' data-transition='slidedown' onclick='return confirm('确定？')'>确定？</a>";
 echo "<input type='submit' value='确定请假' name='confirmleave' />";
 echo "</form>";
}
elseif($_SESSION[isleave]==3){
	 
	echo "请假申请已提交，请等待确认";
}
elseif($_SESSION[isleave]==1){
  $start=Leave::getLeaveStart($_SESSION["userid"]);
  $end=Leave::getLeaveEnd($_SESSION["userid"]);
  echo "请假期:".$start."到".$end."期间无法约课,如有需要可在此期间内点击提前结束请假按钮。请假流程结束后将相应修正有效期";
  echo "<br><a href='finishleave.php' data-role='button' data-inline='true' data-transition='slidedown' onclick='return confirm('确定？')'>提前结束请假</a>";
}
elseif($_SESSION[isleave]==2){//wei dao start
  $start=Leave::getLeaveStart($_SESSION["userid"]);
  $end=Leave::getLeaveEnd($_SESSION["userid"]);
  echo "请假申请已批准:".$start."到".$end."期间无法约课,如有需要可在此期间内点击提前结束请假按钮。请假流程结束后将相应修正有效期";
  }
  
}
else{
  echo "还未开卡，不用请假";	
	
}
?>
	<a href='memberinfo.php' data-role='button' data-inline='false' data-ajax='false'>返回</a>
</div>
</div>






<div data-role="page" id="missclass">
<div data-role="header">
与莫圣携手遇见更好的自己
 <div data-role="navbar">
    <ul>

	<li><a href='register.php'  data-transition='flip' data-ajax='false'>约课</a></li>
      <li><a href="registered.php"  data-ajax="false" >已选</a></li>
      <li><a href="attendhistory.php" data-transition="flip" data-ajax="false">历史/评价</a></li>
	  <li><a href="registerrm.php" data-transition="flip" data-ajax="false" >跑步机</a></li>
	  <li><a href="#" data-transition="flip" data-ajax="false" class="ui-btn-active ui-state-persist">个人</a></li>
    </ul>
  </div>
</div>
<div data-role="content">
 <?php
 $now=date('Y-m-d H:i:s');
	$where1=" AND (payment_table.payment_id=1 ";
	foreach($arr_paymentid as $value){
		$where1=$where1."or (payment_enddate<'{$now}' AND payment_table.payment_id='".$value."' AND payment_table.is_archieved!=1)";
		
	}
	
	$where1=$where1.");";
	
	//SELECT class_name  FROM class_design,class_arrange where class_design.class_id = class_arrange.class_id AND class_arrange.arrange_id=55;
	//SELECT arrange_id ,class_arrange.class_id,class_design.class_id,class_name,teacher_id,starttime,endtime FROM class_arrange INNER JOIN class_design WHERE class_design.class_id = class_arrange.class_id AND (arrangedate = 2015-07-14 OR 2015-07-15)
	$sqlall = "SELECT class_name,class_id,class_description,payment_enddate 
	FROM yoga_lu.class_design 
	inner join package_subscribe ON class_design.package_id =package_subscribe.package_id
	inner join payment_table ON payment_table.payment_id =package_subscribe.payment_id
	where  	 
	class_type='package'
	AND
	package_subscribe.member_id={$_SESSION['userid']}".$where1;
    //echo $sqlall;
	$sqlattended="SELECT DISTINCT class_arrange.class_id, class_name,class_description 
	FROM class_arrange 
	inner join yoga_lu.register_record ON register_record.arrange_id = class_arrange.arrange_id
	inner join class_design ON class_design.class_id=class_arrange.class_id
	inner join package_subscribe ON class_design.package_id=package_subscribe.package_id
    inner join payment_table ON package_subscribe.payment_id=payment_table.payment_id
	 where  
	is_attended!=0
	AND
    register_record.member_id={$_SESSION['userid']}".$where1;
	
	//echo $sqlattended;
	
    if($_SESSION["usercell"]=='13810532905'){
    
    	 
    }
	 
    $db->query("set names UTF8;");  
	$resall = $db->query($sqlall);
	$resall->setFetchMode(PDO::FETCH_ASSOC);
	$rows = $resall->fetchAll();
	$countall=count($rows);
	
	$resattended= $db->query($sqlattended);
	$resattended->setFetchMode(PDO::FETCH_ASSOC);
	$rows1 = $resattended->fetchAll();
	$countattend=count($rows1);
	$countmiss=$countall-$countattend;
	if($countmiss>0)
	{
		echo "你落了".$countmiss."节课</br>";
		echo "请逐一选择补哪节课，参考大课表后，在该课后的<b>申请日期</b>框里选择自己可以参加补课的时间，并点击<b>申请补课</b>按钮，方便我们尽快安排:";
	$all=array();
	$allname=array();
	$attended=array();
	$attendedname=array();

	foreach($rows as $row) {		
		    			 
			$all[] =  $row['class_id'];
			$allname[] = $row['class_name'].$row['class_description'];
			 
			
    } 
		
	foreach($rows1 as $row1) {		
		  		 
			$attended[]=$row1['class_id'];
			$attendedname[]=$row1['class_name'].$row1['class_description'];
			 
	} 	
		
	$diff=array_diff($all,$attended);
	$diffname=array_diff($allname,$attendedname);
	
   	foreach ($diff as $eachdiffid ){
		
		$sql_addmiss1 = "INSERT INTO `yoga_lu`.`missed_table` (`member_id`,`class_id`,`missed_status`) SELECT {$_SESSION['userid']},'{$eachdiffid}','0' FROM dual WHERE not exists (select * from missed_table 
where class_id = {$eachdiffid} AND member_id={$_SESSION['userid']});";
		 
		$db->query("set names UTF8;"); 
		$db->exec($sql_addmiss1);
		}
    	
		
	$sql_listmissed = "SELECT missed_table.class_id,class_design.class_name,class_description,missed_table.missed_status   FROM missed_table INNER JOIN class_design on missed_table.class_id= class_design.class_id WHERE missed_status='0'  AND member_id={$_SESSION['userid']};";
	 
	$db->query("set names UTF8;"); 
	$res_listmissed = $db->query($sql_listmissed);
	$res_listmissed->setFetchMode(PDO::FETCH_ASSOC);
	$rows = $res_listmissed->fetchAll();
	 
	echo "<form name ='formaddclass'  action='applysinglemiss.php'  method='post' accept-charset='utf-8'> ";
    echo "<fieldset data-role='fieldcontain'>";
	echo "<label for='missandday'>选择要补的课</label>";
	echo "<select name='missandday' id='day'>";

    foreach($rows as $row_listmissed) {
		 echo " <option value={$row_listmissed['class_id']}{$row_listmissed['class_name']}{$row_listmissed['class_description']}>{$row_listmissed['class_name']}{$row_listmissed['class_description']}</option>";	
	 }	
		echo "</select>";
		echo "</fieldset>";	
		echo "<b>申请日期：</b> <span id='placeholder'></span>";
		echo  "<input type='date' name='seldate' id='seldate'/>";		
	  echo  "<input type='submit' name='submitmiss' value='申请补课'/> ";		
 
	    echo "</form>";
	
	 }
	elseif($countmiss>0 AND $_SESSION["hasextended"]==1){
		
		"补课加油加油~!";
	}	 
	else{		
		echo "真棒~完成了所有课包~";
	}
	
	?>
      <a href="memberinfo.php" data-role="button" data-inline="true" data-transition="slidedown">返回</a>
  
  </div>
</div>
 <div data-role="footer" data-position="fixed">Copyright 2015 MoSige Yoga.</div> 
</body>
</html> 