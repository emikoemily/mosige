<?php 
session_start();
	if(!$_SESSION["usercell"]) 
   {
	   header("location:index.php");
  }
	include("header.inc.php");
?>
<script type="text/javascript">
    $(function() {
      $.fn.raty.defaults.path = 'lib/img';
      $("[id^='function-demo']").raty({
	  	number: 5,//多少个星星设置		
		targetType: 'hint',//类型选择，number是数字值，hint，是设置的数组值
        path      : 'demo/img',
		hints     : ['非常不喜欢','不喜欢','一般','好','特别喜欢'],
        cancelOff : 'cancel-off-big.png',
        cancelOn  : 'cancel-on-big.png',
        size      : 24,
        starHalf  : 'star-half-big.png',
        starOff   : 'star-off-big.png',
        starOn    : 'star-on-big.png',
        //target    : '#function-hint',//$(this).next("div"),//$(this).next().attr('id'),//$(this).next(),
        cancel    : false,
        targetKeep: true,
		targetText: '请选择评分',

        click: function(score, evt) {
			($(this).next().next('input')).attr("value",score); //.var(score);
         // alert('ID: ' + $(this).attr('id')+$(this).next().attr('id') + "\nscore: " + score + "\nevent: " + evt.type);
        }
      });    
	  
	    
    });


  
  </script>	
 <script>

$(document).ready(function(){
	$("#copy").click(function(){

	var content= $("form#providereview div:nth-of-type(1) div div:nth-of-type(3) input").val();
	$("form#providereview div div div:nth-of-type(3) input").val(content);
	 
	});


	 
});
</script>
<body>

<div data-role="page" id="registeredclass" data-title="上过的课">
<div data-role="header">
<?php 
include("entity/Reviews.php");
	$notreview=Reviews::getUnfinishedCount($_SESSION['userid']);
	
	if($notreview>0){
	
	echo "<marquee direction='left' behavior='alternate' scrollamount='2' scrolldelay='30'> <img src='img/laba.gif' width='38' height='48'><font size=+1 color=white>亲爱的同学请评价后再约课~</font></marquee>";
		//echo "<marquee direction='left' behavior='alternate' scrollamount='2' scrolldelay='30'> <img src='img/laba.gif' width='38' height='48'><font size=+1 color=white>亲爱的同学们，本周六由于老师集体外出学习，课程于下午2点开始，具体请关注当日课表哦！</font></marquee>";
		//echo "<img src='img/laba.gif' width='38' height='48'>春节期间瑜伽馆2月1日-2月14日放假 ， 2月15日开课";
		//echo "<img src='img/laba.gif' width='38' height='48'>大厦1层新设门禁需登记进入或从商场5层电梯来馆";
		
	}
	else{
		//echo "<img src='img/laba.gif' width='38' height='48'>春节期间瑜伽馆2月1日-2月14日放假 ， 2月15日开课";
		//echo "<img src='img/laba.gif' width='38' height='48'>大厦1层新设门禁需登记进入或从商场5层电梯来馆";
		//echo "<marquee direction='left' behavior='alternate' scrollamount='2' scrolldelay='30'> <img src='img/laba.gif' width='38' height='48'><font size=+1 color=white>亲爱的同学们，本周六由于老师集体外出学习，课程于下午2点开始，具体请关注当日课表哦！</font></marquee>";
	echo "夏天就要来啦，一起加油健康瘦身";
	}
?>
 
   <div data-role="navbar">
    <ul>
	<li><a href='register.php'  data-transition='flip' data-ajax='false'>约课</a></li>
      <li><a href="registered.php"  data-ajax="false" >已选</a></li>
      <li><a href="#" data-transition="flip" data-ajax="false" class="ui-btn-active ui-state-persist">评价</a></li>
	  <li><a href="registerrm.php" data-transition="flip" data-ajax="false">跑步机</a></li>
	  <li><a href="memberinfo.php" data-transition="flip" data-ajax="false" >个人</a></li>
    </ul>
  </div>
</div> 
<?php
	include(dirname(__FILE__).'/'."dbconf/settings.inc.php");
	include(dirname(__FILE__).'/'."dbconf/dbconnect.inc.php");	
	//SELECT class_name  FROM class_design,class_arrange where class_design.class_id = class_arrange.class_id AND class_arrange.arrange_id=55;
	//SELECT arrange_id ,class_arrange.class_id,class_design.class_id,class_name,teacher_id,starttime,endtime FROM class_arrange INNER JOIN class_design WHERE class_design.class_id = class_arrange.class_id AND (arrangedate = 2015-07-14 OR 2015-07-15)
	$sql = "SELECT register_id,class_name,class_description, arrangedate,teacher_name,starttime,endtime,register_record.arrange_id 
	FROM register_record 
	INNER JOIN class_arrange ON register_record.arrange_id= class_arrange.arrange_id 
	INNER JOIN class_design ON class_design.class_id = class_arrange.class_id
	INNER JOIN teacher_table ON  class_arrange.teacher_id = teacher_table.teacher_id 
	WHERE register_record.is_attended !=0  AND reviewed = 0 AND member_id={$_SESSION['userid']} order by arrangedate desc limit 10;";
     $db->query('set names UTF8');  
	$res = $db->query($sql);
	$res->setFetchMode(PDO::FETCH_ASSOC);
	$rows = $res->fetchAll();
//echo $sql;
?>
     
	 请同学们用真实的评价和意见来轰炸我们~~
	 </br>
	  <?php 
	  
	  
	 // if($_SESSION["usercell"]=='13810532905'){
	  		//or $_SESSION["usercell"]=='13581873973'){
	  
	  	echo "<button id='copy' data-inline='true'>填第一个后一键复制到所有评论</button>";
	  	echo "<text id='testtest'></text>";
	 // }
	  
	  
	  ?>
	 
	   <form id="providereview" name ="formselect"  method ='post' action ='reviewclass.php'>
<?php
       
		foreach($rows as $row) {	
		
        
		echo "<div id='review-{$row['register_id']}'  data-role='collapsible' >";	
		echo "<h1>{$row['arrangedate']} {$row['class_name']} {$row['class_description']} {$row['teacher_name']}</h1><p>";		
		
		echo "<label for='review'>对这节课程有什么评价？</label>";
		echo "<div id='function-demo-{$row['register_id']}'  class='target-demo'></div>";
		
		echo "<div id='function-hint-{$row['register_id']}' class='hint'></div>";
		echo "<input type='hidden'  name='review_star[]' id='star-{$row['register_id']}' >"; 
		echo "<input type='hidden'  name='arrange_id[]' id='class-{$row['arrange_id']}' value={$row['arrange_id']} >";
		echo "<input type='hidden'  name='reg_id[]' id='review-{$row['register_id']}' value={$row['register_id']} >";
			
		echo "<input type='text' name='review_content[]' id='reviewcontent-{$row['register_id']}' placeholder='进度偏快/慢/适中？老师讲解得如何？'>";
		//echo "<div style='width:500px; margin:100px auto;'>";
		echo  "<div class='demo'>";
		
		
		echo "<span id='rw-{$row['register_id']}'></span>";
		echo "</div>";
		//echo "</div>";
		
	
		echo "	</p>";
		echo "</div>";
		
		//echo "</ul>";				
			
        } 
?>

 <div style="width:500px; margin:100px auto;">
  <div class="demo">
     
    
  </div>
   
</div>

	<input type='submit' value='提交评价' name='reviewselected' />
	 
    </form>

</div>		
 
</body>
</html>