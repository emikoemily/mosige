<?php 
session_start();
$code = mt_rand(0,1000000);
$_SESSION['code'] = $code;
	if(!$_SESSION["usercell"]) 
   {
	  // header("location:index.php");
  }
	include("header.inc.php");
 
	
?>


<?php


?>

<body>
<!--<div data-role="page" id="viewclass" data-title="课程介绍" style="background:url(img/package/IMG_4968.JPG) 50% 0 no-repeat;background-size:cover">-->
<div data-role="page" id="desc" data-title="课程介绍">

<img src="img/package/kongzhong/1.JPG" style="width:100%;height:auto" ></img>


<b>空中瑜伽-反重力瑜伽</b></br>
</br>
空中瑜伽是以顺重力、向心力、反重力这三大原理结合的新型瑜伽方式，源自艾扬格吊绳的演变，该力量原理来自于物理运动学。与传统地面上的瑜伽不同，利用绳索吊床(Hammock)作为铺具，把传统哈达瑜伽的体位法(asana)、结合中医按摩手法、太极的圆融，普拉提的力量，舞蹈的优雅与瑜伽体式相融合，帮助练习者事半功倍地完成所有空中瑜伽的体式。
</br></br><b>上课周期：</b>8节课/期 75分钟课程
 </br>
</br><b>课程内容：</b>基础/初级/力量/理疗四部分组成，根据会员实际情况老师会适度调整课程内容
</br>
</br><b>适合人群：</b>大部分人群均可
</br>高血压，心脏病等人群慎重选择

</br>
</br><b>会员评价（显示最新5条）：</b>
<?php
	include(dirname(__FILE__).'/'."dbconf/settings.inc.php");
	include(dirname(__FILE__).'/'."dbconf/dbconnect.inc.php");	
	 
$sql = "select review_content,arrangedate,starttime,star,class_design.class_name,member_name,member_cardid,teacher_name 
	from member_review 
inner join class_arrange on member_review.arrange_id=class_arrange.arrange_id
inner join class_design on class_arrange.class_id=class_design.class_id
inner join teacher_table on class_arrange.teacher_id=teacher_table.teacher_id
inner join member_user on member_user.member_id=member_review.member_id where class_design.package_id ='package7' limit 5";
	//echo $sql;
	$db->query('set names UTF8');
	$res=$db->query($sql);  
	while($row = mysqli_fetch_array($res)) {	
	// echo $row['member_cardid']."打分：";
	 //for($i=0;$i<$row['star'];$i++){
	//	 echo "*";
	 //
	 if($row['review_content']!=""){
	 echo "</br><b>会员".$row['member_cardid']."</b></br>".$row['review_content'] ."</br>";}
	}
	
?>
</br>
看看我们莫圣老师习练空中瑜伽的美丽身姿吧~
 
 <img src="img/package/kongzhong/2.JPG" style="width:100%;height:auto" ></img>
<img src="img/package/kongzhong/3.JPG" style="width:100%;height:auto" ></img>
 <img src="img/package/kongzhong/4.JPG" style="width:100%;height:auto" ></img>
 <img src="img/package/kongzhong/5.JPG" style="width:100%;height:auto" ></img>
 <img src="img/package/kongzhong/6.JPG" style="width:100%;height:auto" ></img>
  <img src="img/package/kongzhong/7.JPG" style="width:100%;height:auto" ></img>
<img src="img/package/kongzhong/8.JPG" style="width:100%;height:auto" ></img>
<!--<script>
$("#pic1").on("swipeleft",function(){  
             $("#pic1").hide("fast");  
             $("#pic2").show("fast");  
           
        }); 
$("#pic1").on("swiperight",function(){  
             $("#pic1").hide("fast");  
             $("#reviews").show("fast");  
           
        }); 
</script>-->
</div>
 
 
</body>
</html> 
