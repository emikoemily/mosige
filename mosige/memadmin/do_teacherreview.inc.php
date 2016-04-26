<?php
	session_start();
	 	include("entity/TeacherReview.php");
	 
	if($_POST){ 		 
		 $regid = $_POST['regid'];
		// $classprogress=mb_convert_encoding($_POST['classprogress'],'utf-8','gbK');
		 $classprogress=$_POST['classprogress'];
		 $completeprogress=$_POST['completeprogress'];
		 $memberbody=$_POST['memberbody'];
		 $membercomment=$_POST['membercomment'];
		//echo $classprogress;
		//echo $completeprogress;
		TeacherReview::addReview($regid, $classprogress,"classprogress");
		TeacherReview::addReview($regid, $completeprogress, "completeprogress");
		TeacherReview::addReview($regid, $memberbody, "memberbody");
		TeacherReview::addReview($regid, $membercomment,"membercomment");
        
		  
             
	 
	}
		 
	
	header("Location:manage_attend.php");
?>