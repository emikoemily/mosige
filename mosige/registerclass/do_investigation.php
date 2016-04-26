<?php
session_start();
include("header.inc.php");
include("entity/Investigation.php");
//echo "选完了";
if($_POST){
$form_workingday = $_POST["favtime"];
$form_weekend = $_POST["favtimew"];
$form_self = $_POST["selftime"];



if($form_workingday!=NULL){
	foreach($form_workingday as $value){
	
		//echo $value." ,";
		if($value!=""){
		
			Investigation::addTime($_SESSION['userid'], $value,"working");
		 
		
		}
	
	}
	
}
if($form_weekend !=NULL){
	foreach($form_weekend as $value){

		//echo $value." ,";
		if($value!=""){

			Investigation::addTime($_SESSION['userid'], $value,"weekend");
		

		}

	}
}

if($form_self!=NULL and $form_self!=""){
	foreach($form_self as $value){

	 
		if($value!=""){

				Investigation::addSelftime($_SESSION['userid'], $value);


		}

	}
}
if($form_workingday==NULL and  $form_weekend==NULL){

	echo "亲，你的时间全部都空着，你确定上面的时间没有合适的吗，返回再看看吧？";
	echo "<br>";
	//echo "<a href='register.php'  data-role='button' data-ajax='false' data-inline='true'>确定了，提交</a>";
	echo "<a href='investigation.php'  data-role='button' data-ajax='false' data-inline='true'>返回再填</a>";

}else{
	echo "问卷已提交";
	echo "<br>";
	echo "<a href='register.php'  data-role='button' data-ajax='false' data-inline='true'>辛苦啦，去选课吧~</a>";
}
//echo Investigation::ismemdone($_SESSION['userid']);
//header("investigation_weekend.php");
}
?>

