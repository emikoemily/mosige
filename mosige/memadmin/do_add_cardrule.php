<?php
include ("entity/Cardrule.php");
include("functions.inc.php");
$form = check_form($_POST["card"]);
	//$form["reg_time"] = date("Y-m-d H:i:s");
	//$form["pass"] = md5($form["pass"]);
extract($form);
//echo $rule_name;
//echo $has_kongzhong;
if(isset($has_kongzhong)){
	
	$has_kongzhong = 1;
}
else{
	$has_kongzhong = 0;
}
if(isset($has_ertong)){
	
	$has_ertong= 1;
}else{
	
	$has_ertong = 0;
}

Cardrule::addRuleToDB($rule_name,$level_actual,$rule_displayname,$rule_days,$rule_startdate,$rule_enddate,$rule_description,$rule_maxcount,$has_kongzhong,$has_ertong);


 //print_r(debug_backtrace());
//print_r(error_get_last());
header("Location:msg.php?m=addcard_success");

?>