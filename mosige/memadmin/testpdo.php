<?php
	//include("dbconnect.inc.php");
	$dsn = "mysql:host=127.0.0.1;dbname=yoga_lu";
	$db = new PDO($dsn, 'yoga_lu', 'Yooq_yoga_lu');
	$sql = "INSERT INTO member_user (member_name,member_password,member_email,member_sex,member_cell,member_level,member_birthday,member_points,member_intro,member_attendmax,member_cardid,member_days) values ('testpdo','aa','email','1','1111','asdf','asdf',1,'asdf',1,'aaa',1) ";
		 
			 
			echo $sql;
			 
			$db->exec($sql);
			
			
				print_r(error_get_last());
			print_r(debug_backtrace());
	?>