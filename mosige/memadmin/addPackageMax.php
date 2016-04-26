<?php
include ("entity/Package.php");

if($_GET){
	$mid=$_GET["mid"];
	$pid=$_GET["pid"];
	
	Package::addpackageMAX(1,$pid,$mid);



}else{
	
	
}
header("Location:member_class.php?id={$mid}");

?>