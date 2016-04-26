<?php
if(!isset($_SESSION))session_start();
    $_SESSION=array();
    if (isset($_COOKIE["mosigecookie"])) {
      setcookie("mosigecookie",false);
	  setcookie("mosigecookie2", false);
    }
    session_destroy();
    header('Location:index.php');

?>