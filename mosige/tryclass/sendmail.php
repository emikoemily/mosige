<?php
/**
 * This example shows sending a message using a local sendmail binary.
 */

include("./PHPMailerAutoload.php");
//include("./class.phpmailer.php");
//include("./class.smtp.php");
//Create a new PHPMailer instance
$mail = new PHPMailer;
// Set PHPMailer to use the sendmail transport
$mail->SMTPDebug = 0;		
$mail->IsSMTP(); 
 $mail->SMTPAuth = true;    
$mail->Username = "mosige_life";     // SMTP username  注意：普通邮件认证不需要加 @域名    
$mail->Password = "cfoiuxtjehcpcttw"; // SMTP password    
$mail->From = "mosige_life@163.com";      // 发件人邮箱    
$mail->FromName =  "管理员";               // send via SMTP    
$mail->Host = "smtp.163.com"; 
//Set who the message is to be sent from
$mail->setFrom('mosige_life@163.com', 'Mosige Yoga');
//Set an alternative reply-to address
$mail->addReplyTo('mosige_life@163.com', 'Mosige Yoga');
//Set who the message is to be sent to
$mail->addAddress('service_mosige@163.com', 'Mosige');
$mail->addAddress('mosige_life@163.com', 'zz');
//$mail->addAddress('30540281@qq.com','mosige');
//Set the subject line
$mail->Subject = 'mosige';
$mail->Charset = 'utf-8';
//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
//$mail->msgHTML(file_get_contents('contents.html'), dirname(__FILE__));
//Replace the plain text body with one created manually
//$mail->Body = 'asdf';
//$mail->AltBody = 'This is a plain-text message body';
//Attach an image file
//$mail->addAttachment('images/phpmailer_mini.png');

//send the message, check for errors
//if (!$mail->send()) {
//    echo "Mailer Error: " . $mail->ErrorInfo;
//} else {
//    echo "Message sent!";
//}