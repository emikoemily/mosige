<?php 
//echo "email";
//echo dirname(__FILE__).'/'."mail/sendmail.php";
include("sendmail.php");
 $mail->Subject ="zhangzheng test email 正正测试是否有乱码";
	
	 $mail->Body="testest正正测试是否有乱码"; 
	 $mail->send();

?>