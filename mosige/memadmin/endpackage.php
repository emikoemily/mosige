<?php
	include ("entity/Package.php");
	
	if($_GET){
	$mid=$_GET["mid"];
	$pid=$_GET["pid"];
	echo $mid;
	echo $pid;
	Package::endpackage($mid,$pid);
	
	
	
	}
	header("Location:member_class.php?id={$mid}");
		
?>