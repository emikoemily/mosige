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
		 
		TeacherReview::updateReview($regid, $classprogress,"classprogress");
		TeacherReview::updateReview($regid, $completeprogress, "completeprogress");
		TeacherReview::updateReview($regid, $memberbody, "memberbody");
		TeacherReview::updateReview($regid, $membercomment,"membercomment");
        
		  
             
	 
	}
		 
	
	header("Location:manage_attend.php");
?>