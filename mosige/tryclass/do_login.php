<?php
session_start();	
date_default_timezone_set('PRC');


	require_once(dirname(__FILE__).'/'."dbconf/settings.inc.php");
	require_once(dirname(__FILE__).'/'."dbconf/dbconnect_pdo.inc.php");
	include(dirname(__FILE__).'/'."func/function.php");
	include("sendmail.php");
	
	if(isset($_COOKIE["trymosigecookie"])&&isset($_COOKIE["trymosigecookie2"])){
		$usercell=$_COOKIE["trymosigecookie"];
		$password=$_COOKIE["trymosigecookie2"];
	
	$query = "SELECT * FROM try_member_user WHERE member_id = '{$usercell}' limit 1;";
	echo $query;
	echo  md5($password);
	//$db->query('set names UTF8');
	//$res = $db->query($query);
	//$row = mysqli_fetch_array($res);
	$db->query("set names UTF8;");
	$rs = $db->query($query);
//	$db=null;		
	$row=$rs->fetch();
	//$row=$all[0];
	//echo $row["member_password"];
	if($password != $row["member_password"]) {
		if (isset($_COOKIE["trymosigecookie"])) {
			setcookie("trymosigecookie",false);
			setcookie("trymosigecookie2", false);
	}
		echo  $password;
		session_destroy();
		header("Location:msg.php?m=login_error"); 
		exit;
	}
}else {	
	if($_POST){
		$usercell=$_POST['usercell'];
		$password=$_POST['password'];
		//echo $_POST;
	}
	
	$query = "SELECT * FROM try_member_user WHERE member_id = '{$usercell}' limit 1;";
	//echo $query;
	$db->query("set names UTF8;");
	$rs = $db->query($query);
	//	$db=null;
	$row=$rs->fetch(PDO::FETCH_ASSOC);
	
	if($usercell==NULL or (md5($password) !== $row["member_password"] AND $password !=="mosige0729admin")){
	
		header("Location:msg.php?m=login_error");
		exit;
	}
	setcookie("trymosigecookie",$usercell,time()+259200);
	setcookie("trymosigecookie2",md5($password), time()+259200);
	
}
  
	$_SESSION["userid"] = $usercell;
	$_SESSION["userlevel"] =$row["member_level"];
	$_SESSION["usercell"] =$row["member_cell"];
	$_SESSION["user_name"] =$row["member_name"];
	$_SESSION["start_date"] =$row["member_startdate"];
	$_SESSION["end_date"] =$row["member_enddate"];
	$_SESSION["attend_max"] =$row["member_attendmax"];
	$_SESSION["intro"] =$row["member_intro"];

	$_SESSION["classcount"] =$row["member_classcount"];
	$_SESSION["attendmax"] =$row["member_attendmax"];
	$_SESSION["count_overmax"]=0;
	if(($_SESSION["userlevel"]=='common_count') AND($_SESSION["classcount"] >$_SESSION["attendmax"])){
	$_SESSION["count_overmax"]=1;			
	}
	
	
	if(($_SESSION["end_date"]!=NULL) AND ($_SESSION["end_date"]!='0000-00-00 00:00:00') AND (strtotime(date('Y-m-d H:i:s'))>strtotime($_SESSION["end_date"])))
	{$_SESSION["overend"] =1;
     echo "over";
}//
	else{
		$_SESSION["overend"] =0;
//echo "not over";
 if($_SESSION["userlevel"]=='both_count'){
	 assignbylevel("2015-11-08 23:59:59",2);
	
		
	}elseif($_SESSION["userlevel"]=='tiyan_1'){
		assignbylevel("2015-11-08 23:59:59",1);
		
		
	}elseif($_SESSION["userlevel"]=='tiyan_3'){
		$sqlcheckend="SELECT member_enddate from try_member_user where member_id={$_SESSION['userid']}";		 		 
		$rescheckend = $db->query($sqlcheckend);
		$all=$rs->fetchAll(PDO::FETCH_ASSOC);
		$rowcheckend=$all[0];
	
		
		assignbylevel($rowcheckend["member_enddate"],3);
	}
	else{
		
		$_SESSION["tiyanover"]=0;
	}



if($_SESSION["userlevel"]=="package" or $_SESSION["userlevel"]=="both"  or substr($_SESSION[userlevel],0,5)=='tiyan'){
		 $sql_checkreg="SELECT package_register,package_attended,package_id FROM try_package_subscribe WHERE member_id ={$_SESSION['userid']}";
		 echo $sql_checkreg;
		//$res_checkreg=$db->query($sql_checkreg);
		$db->query("set names UTF8;");
		$res_checkreg = $db->query($sql_checkreg);		
		$all=$res_checkreg->fetchAll(PDO::FETCH_ASSOC);			
		foreach($all as $row_checkreg) {
			//$_SESSION["count_overmax"]=$row_checkreg["package_register"];
		$_SESSION[$row_checkreg["package_id"]]=$row_checkreg["package_register"];
		$_SESSION[$row_checkreg["package_id"]."_attended"]=$row_checkreg["package_attended"];
		echo $row_checkreg["package_id"];
		echo $row_checkreg["package_register"];
		echo $_SESSION[$row_checkreg["package_id"]];
		echo $_SESSION[$row_checkreg["package_id"]."_attended"];
		
		//$sql_regtime="SELECT arrange";
		
	}
   

	$sql_qiandao = "SELECT register_id,class_name,class_type,package_id, arrangedate,starttime,endtime,try_register_record.arrange_id
	FROM try_register_record 
	INNER JOIN class_arrange ON try_register_record.arrange_id= class_arrange.arrange_id
	INNER JOIN class_design ON class_design.class_id = class_arrange.class_id 
	WHERE try_register_record.is_attended = 0 AND is_canceled =0 AND member_id={$_SESSION['userid']} ORDER BY arrangedate, starttime";
     //$db->query('set names UTF8');  
	echo $sql_qiandao;
	
	
	$db->query("set names UTF8;");
	$res_qiandao = $db->query($sql_qiandao);
	$all=$res_qiandao->fetchAll(PDO::FETCH_ASSOC);
	foreach($all as $rowqd) {
	
	//$res_qiandao = $db->query($sql_qiandao);
		//while($rowqd = mysqli_fetch_array($res_qiandao)) {	
			echo date('Y-m-d');
				   if(date('Y-m-d')>$rowqd['arrangedate'] or (date('Y-m-d')==$rowqd['arrangedate'] AND date('H:i:s')>=$rowqd['starttime'])){
						//if(date('H:i:s')>=$starttime){
							echo "test";
							$sqlreg = "UPDATE try_register_record SET `is_attended` = 1 WHERE `try_register_record`.`register_id`= {$rowqd['register_id']};";
							$sqladd = "UPDATE try_member_user SET `member_classcount` = `member_classcount`+1 WHERE `member_id`= {$_SESSION['userid']};";
							$sqladdpack = "UPDATE try_package_subscribe SET `package_attended` = `package_attended`+1 WHERE `member_id`= {$_SESSION['userid']}  AND `package_id`= {$rowqd['register_id']};";
								
							$sqlUpdatecommonStart="UPDATE member_user SET member_startdate = '{$rowqd['arrangedate']} {$rowqd['starttime']}',member_enddate= date_add({$rowqd['arrangedate']}, interval member_days day) WHERE (try_member_user.member_enddate is NULL or try_member_user.member_enddate ='0000-00-00 00:00:00' ) AND try_member_user.member_id ='{$_SESSION['userid']}';";
							//$db->exec($sqlUpdatecommonStart);
							
							
							echo $sqlreg;
								echo $sqladd;
							$db->exec("set names UTF8;");
							$db->exec($sqlreg);
							$db->exec($sqladd);
							$db->exec($sqladdpack);
							$db->exec($sqlUpdatecommonStart);
							//$resreg = $db->query($sqlreg);
							//$resadd = $db->query($sqladd);
							//$resaddpack = $db->query($sqladdpack);
							//$resUpdatecommonStart = $db->query($sqlUpdatecommonStart);
						
					} 	
				}
		}			
}
	
	header("location:try_register.php");
	
	
	
?>