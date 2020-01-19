<?php

/**
 * Produces an email list and emails it to me!
 */


 include_once('../autoload.php');

 $cController = new CustomerController;
 $list = '';
 foreach($cController->GetAllCustomers() as $customer) {
 	$list .= $customer->GetEmail()."\n";
 }
#echo($list);
$mailer = new PHPMailer;
$body = $list;
$mailer->From = "info@echosupplements.com";
$mailer->FromName = "ECHO";
$mailer->Subject = "Newsletter List";
$mailer->Host = "smtp.gmail.com";
$mailer->Port = 465;
$mailer->Mailer = "smtp";
$mailer->SMTPAuth = true;
$mailer->Username = "info@echosupplements.com";
$mailer->Password = "bl00dlu5t";
$mailer->Body = $body;
$mailer->SMTPSecure = "ssl"; // option
$mailer->AltBody = $body;
$mailer->AddAddress ( "info@echosupplements.com" );
$mailer->Send()

?>