﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<style type="text/css">
*{margin:0;padding:0;list-style-type:none;}
a,img{border:0;}
body{font:22px/180% Arial, Helvetica, sans-serif, "新宋体";}
.blank30{height:30px;overflow:hidden;}
/* jQuery jcImgScroll */
.jcImgScroll{position:relative;margin:40px auto 0 auto;}
.jcImgScroll li{border:1px solid #ccc;}
.jcImgScroll li a{background:#fff;display:block;position:relative;z-index:99;}
.jcImgScroll li.loading a{background:#fff url(loading.gif) no-repeat center center;} 
.jcImgScroll li img,.jcImgScroll li,.jcImgScroll em,.jcImgScroll dl{display:none;border:0 none;}
.jcImgScroll li img{width: 100%;height: 100%;}
.jcImgScroll em.sPrev{background:url(arrow-left.png) no-repeat left center;width: 150%;height: 150%;}
.jcImgScroll em.sNext{background:url(arrow-right.png) no-repeat right center;}
.jcImgScroll dl dd{background:url(NumBtn.png) no-repeat 0 bottom;text-indent:-9em;}
.jcImgScroll dl dd:hover,.jcImgScroll dl dd.curr{background-position:0 0;}

@media (min-width:1025px) and (max-width:1440px) {*{font-size:42px;}}
@media (min-width:1441px) and (max-width:1600px){*{font-size:43px;}}
</style>

<script type="text/javascript" src="/tryclass/js/jquery-1.4.2.min.js"></script>

<script type="text/javascript" src="/tryclass/js/jQuery-easing.js"></script>
<script type="text/javascript" src="/tryclass/js/jQuery-jcImgScroll.js"></script>

<script type="text/javascript">
$(function(){
	//演示三 ID改变下试试
	$("#datouwang").jcImgScroll({
		arrow : {
			width:45,	
			height:400,
			x:60,
			y:0
		},
	    width : 330, //设置图片宽度
		height:469, //设置图片高度
		imgtop:22,//每张图片的上下偏移量
		imgleft:-10,//每张图片的左边偏移量
		imgwidth:30,//每张图片的宽度偏移量
		imgheight:44,//每张图片的高度偏移量
		count : 9,
		offsetX : 60,
		NumBtn : false,
		title:false,
		setZoom:.8,
	});

});
</script>

<title>MOSIGE</title>  
 
</head>
<body bgcolor="#ebedba">
<font size="6">
<h3 align="center">髋位瑜伽</h3>
<a href="../try_register.php">返回</a>
</br>
柔韧指数：★★★★☆
</br>
力量指数：★★★☆☆
</br>
难度指数：★★★☆☆
</br>
实用指数：★★★★☆
</br>
</br>
通过髋部体式来舒展骨盆区域，调整骨盆的位置，加强髋及腿部的柔韧性，达到修复及缓解髋部区域亚健康状态。
</br>
</br>
上课周期：8节课/期 </br>

课程内容：舒展/柔韧/打开，打开髋部空间。滚动式学习，每期体式略有不同，根据会员实际习练情况由老师安排 </br>

适合人群：适合所有人群，尤其是久坐人群。备孕女性可习练</br>
</br>
</br>
</br>
</font >
<div id="datouwang" class="jcImgScroll" align="center" width="120%" height="120%">
	<ul>
		<li><a  path="../img/package/kuanwei_yoga/1.JPG"></a></li>
		<li><a  path="../img/package/kuanwei_yoga/2.JPG"></a></li>
		<li><a  path="../img/package/kuanwei_yoga/3.JPG"></a></li>
		<li><a  path="../img/package/kuanwei_yoga/4.JPG"></a></li>
		<li><a  path="../img/package/kuanwei_yoga/5.JPG"></a></li>
		<li><a  path="../img/package/kuanwei_yoga/6.JPG"></a></li>
		 

	</ul>
</div>

<!-- 代码 结束 -->

<div style="text-align:center;margin:130px 0; font:normal 14px/24px 'MicroSoft YaHei';">

</div>
<br>
<h2><a href="../try_register.php">返回</a></h2>

</body>

<script type="text/javascript">
function checkdate(overend,count_overmax,isleave,checkbox)
{
	

    if (overend==1||count_overmax==1||isleave==1) {		
        //text = "Input not valid"+x;
		document.getElementById(checkbox).disabled= true;
    }  
    
 
  
}
function checkdisable(dis,checkbox)
{
	

    if (dis==1) {		
        //text = "Input not valid"+x;
		document.getElementById(checkbox).disabled= true;
    }  
    
 
  
}
function checkavailable(x,y)
{
	

    if (x<=0) {		
        //text = "Input not valid"+x;
		document.getElementById(y).disabled = true;
    } else {
         
    }
    
 
  
}
 
</script>
</html>