<?php
include("dbconf/settings.inc.php");
include("dbconf/dbconnect.inc.php");
function assignbylevel($enddate,$maxcount){
		
		$_SESSION["end_date"]=$enddate;
		if(date('Y-m-d')>$enddate AND $enddate!="0000-00-00 00:00:00" AND $enddate!=NULL ){
			
			$_SESSION["tiyanover"]=1;
		}
		else{
			
		$sql_2ci="SELECT COUNT(*) FROM `try_register_record` WHERE member_id={$_SESSION["userid"]} AND is_canceled!=1";
		
		//$res_2ci=$db->query($sql_2ci);
		$res_2ci = $db->query($sql_2ci);
		$res_2ci->setFetchMode(PDO::FETCH_NUM);
		$rs=$res_2ci->fetch();
		 
    
			 
			if($rs[0]>=$maxcount){
			$_SESSION["tiyanover"]=1;}
			else{
				$_SESSION["tiyanover"]=0;
				}
			
			 
		}
	}

function checkcount($count){
		
				$sql_2ci="SELECT COUNT(*) FROM `try_register_record` WHERE member_id={$_SESSION["userid"]} AND is_canceled!=1";
				$res_2ci=$db->query($sql_2ci);
				$res_2ci->setFetchMode(PDO::FETCH_NUM);
				$rs=$res_2ci->fetch();
			 
					$rs=$all[0];
					if($rs[0]>=$count){
							$_SESSION["tiyanover"]=1;}
						else{
								$_SESSION["tiyanover"]=0;
							}
			
					 
	}
	
	
function setbylevel(){
	
		if($_SESSION["userlevel"]=='both_count'){
								checkcount(2);
							} 
		elseif($_SESSION["userlevel"]=='tiyan_1'){
								checkcount(1);
							} 
		elseif($_SESSION["userlevel"]=='tiyan_3'){
								checkcount(3);
							} 
	
}

?>