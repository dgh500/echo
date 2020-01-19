<?php
include('phpMailer/class.phpmailer.php');

mail('dgh500@gmail.com','test email','email message');

$mailObj = new PHPMailer ( );
$body = 'email body';
$text_body = 'text email body';
$mailObj->From = "dgh500@gmail.com";
$mailObj->FromName = "test email 2";
$mailObj->Subject = "test email 2";
$mailObj->Host = "smtp.gmail.com";
$mailObj->Port = 587;
$mailObj->Mailer = "smtp";
$mailObj->SMTPAuth = true;
$mailObj->Username = "info@deepbluedive.com";
$mailObj->Password = "d33pblu3";
$mailObj->Body = $body;
$mailObj->SMTPSecure = "ssl"; // option
$mailObj->AltBody = $text_body;
$mailObj->AddAddress ('dgh500@gmail.com');
$mailObj->Send();


?>