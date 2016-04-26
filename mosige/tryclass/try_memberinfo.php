<?php 

session_start();
	if(!$_SESSION["usercell"]) 
   {
	   header("location:index.php");
  }
	include("header.inc.php");


?>
<script type="text/javascript">






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
	//include("sendmail.php");
	?>
	<li><a href='try_register.php'  data-transition='flip' data-ajax='false'>约课</a></li>
      <li><a href="try_registered.php"  data-ajax="false" >已选</a></li>
      
	  <li><a href="#" data-transition="flip" data-ajax="false" class="ui-btn-active ui-state-persist">个人</a></li>
    </ul>
</div>
</div>

<p>
 <?php 

function checktiyan($db,$ci){
	
			$yishang=0;
			$sql_2ci="SELECT COUNT(*) FROM `try_register_record` WHERE member_id={$_SESSION["userid"]} AND is_canceled!=1";
				$res_2ci=$db->query($sql_2ci);
				$res_2ci->setFetchMode(PDO::FETCH_num);
				$rs = $res_2ci->fetch();
				$yishang=$rs[0];
					if($rs[0]>=$ci){
					$_SESSION["tiyanover"]=1;}
					else{
						$_SESSION["tiyanover"]=0;
						}
			
				
			echo "已使用次数/总次数:   {$yishang}/{$ci}</br>";
}

 
    echo "你好 {$_SESSION[user_name]} ，欢迎体验我们的课程</br>";
	//echo "卡券号:".$_SESSION[userid];
	

	echo "</br>";
	
			
			if($_SESSION[userlevel]=='common_count'){
			echo "</br>开卡日期:{$_SESSION[start_date]}</br>";
		    echo "有效期至：{$_SESSION[end_date]}";
			echo "</br>已上次数/总次数:    $_SESSION[classcount]/$_SESSION[attendmax]</br>";
			if($_SESSION["count_overmax"]==1){
			echo "</br>次数已上完";
		    }
		}
		elseif($_SESSION[userlevel]=='both_count'){
			//echo "</br>开卡日期:{$_SESSION[start_date]}</br>";
		    echo "有效期至：2015-11-08";
			checktiyan($db,2);
			 
		}
		elseif($_SESSION[userlevel]=='tiyan_1'){
			//echo "</br>开卡日期:{$_SESSION[start_date]}</br>";
		    echo "有效期至：2015-11-08";
			checktiyan($db,1);
		}
		elseif($_SESSION[userlevel]=='tiyan_3'){
			//echo "</br>开卡日期:{$_SESSION[start_date]}</br>";
		   // echo "有效期至：2015-11-08";
			checktiyan($db,3);
		}
		
		else{
			echo "</br>开卡日期:{$_SESSION[start_date]}</br>";
		    echo "有效期至：{$_SESSION[end_date]}";
			echo "</br>已上次数:    $_SESSION[classcount]</br>";
		}
	
 ?>
 </br>

 <a href="logout.php" data-ajax="false">退出登录 </a>
</p>
</div>



</div>
 <div data-role="footer" data-position="fixed">Copyright 2015 MoSige Yoga.</div> 
</body>
</html> 