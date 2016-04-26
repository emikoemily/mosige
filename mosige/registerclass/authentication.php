<?php



function Authenticate($Username, $Password)

{

	$query = "SELECT COUNT(*) FROM member_user " . 

		"WHERE member_cell = '" . $username . "' AND " . 

		"member_password = '" . $password . "';";

		

	$result = $db->query($query);

	

	$_SESSION["CurrentUser"] = $username;



	return mysqli_result($result, 0); 

}



?>