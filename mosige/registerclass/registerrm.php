<?php 
session_start();
	if(!$_SESSION["usercell"]) 
   {
	   header("location:index.php");
  }
	include("header.inc.php");

	
?>
<body>
<!-- first page-->
<div data-role="page" id="regrm" data-title="预约跑步机">
<div data-role="header">
与莫圣携手遇见更好的自己
  <div data-role="navbar">
    <ul>
	<li><a href='register.php'  data-transition='flip' data-ajax='false'>约课</a></li>
      <li><a href="registered.php"  data-ajax="false" >已选</a></li>
      <li><a href="attendhistory.php" data-transition="flip" data-ajax="false">评价</a></li>
	  <li><a href="#" data-transition="flip" data-ajax="false" class="ui-btn-active ui-state-persist">跑步机</a></li>
	  <li><a href="memberinfo.php" data-transition="flip" data-ajax="false" >个人</a></li>
    </ul>
  </div>
</div>
 <div data-role="content">
  
你可预约今天的跑步机</br>
<?php
	include(dirname(__FILE__).'/'."dbconf/settings.inc.php");
	include(dirname(__FILE__).'/'."dbconf/dbconnect.inc.php");	
	 
	$sql = "SELECT reg_mins ,start_hour  FROM running_arrange WHERE member_id ={$_SESSION['userid']};";
	$db->query('set names UTF8');  
	$res = $db->query($sql);
	 echo"已预约：</br>";
	 $res->setFetchMode(PDO::FETCH_ASSOC);
	 $rows=$res->fetchAll();
	foreach($rows as $row) {	
	 echo $row['start_hour']."点，准备跑".$row['reg_mins']."分钟</br>";
	}
	//echo $sql;
	
    


?>
    <form method="post" action="chooserm.php">
      <fieldset data-role="fieldcontain">
        <label for="rmhour">选择时间段</label>
        <select name="rmhour" id="rmhour">
         <option value="10">10点</option>
         <option value="11">11点</option>
         <option value="12">12点</option>
         <option value="13">13点</option>
         <option value="14">14点</option>
         <option value="15">15点</option>
         <option value="16">16点</option>
		 <option value="17">17点</option>
		 <option value="18">18点</option>
		 <option value="19">19点</option>		 
        </select>
      </fieldset>
	  <fieldset data-role="fieldcontain">
        <label for="rmmin">选择跑步时长</label>
        <select name="rmmin" id="rmmin">
         <option value="30">跑半小时</option>
         <option value="45">跑45分钟</option>
         <option value="60">跑1小时</option>
        	 
        </select>
      </fieldset>
	<?php 

if($_SESSION[userlevel]=='both_count') 
{echo "仅对会员开放跑步机";}
else{	
   echo  "<input type='submit' data-inline='true' data-transition='flip' value='提交'/>";
}
	  ?>
    </form>
  </div>
</div>

 <footer data-role="footer" data-position="fixed">Copyright 2015 MoSige Yoga.</footer> 
</body>
</html> 