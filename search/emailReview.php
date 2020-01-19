<?php
// Include models, controllers etc.
$noSessions = true;
include('../autoload.php');
include('unsubscribes.php');
$oController = new OrderController;
$toBeSent = $oController->GetNotSentReviewEmail();
echo count($toBeSent);
$sent = array();
$count = 0;

foreach($toBeSent as $order) {

		// Customer's Email
		$addAddress = $order->GetCustomer()->GetEmail();
		$sent[] = $addAddress.' ('.$order->GetOrderId().') ';

		// Generate Email
		$emailReviewView = new EmailReviewView();
		$email = new PHPMailer;
		$body = $emailReviewView->LoadDefault($order->GetOrderId());
		$text_body =
		"
		Thank you for your order from Echo Supplements\r\n
		We hope that you are enjoying your purchase and would love it if you could find the time to write a short review of your experience with it.\r\n
		To do this simply find the product online, scroll past the description and you will be able to fill in a review which will help other customers.\r\n
		Echo Supplements: www.echosupplements.com\r\n
		";
		$email->From = "orders@echosupplements.com";
		$email->FromName = "Echo Supplements";
		$email->Subject = 'Review your recent purchases at Echo Supplements';
		$email->Host = "smtp.gmail.com";
		$email->Port = 465;
		$email->Mailer = "smtp";
		$email->SMTPAuth = true;
		$email->Username = "info@echosupplements.com";
		$email->Password = "bl00dlu5t";
		$email->Body = $body;
		$email->SMTPSecure = "ssl"; // option
		$email->AltBody = $text_body;
		$email->AddAddress($addAddress); // use $addAddress when live!!
		echo $body;
/*		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: orders@echosupplements.com' . "\r\n";
		$body = $orderView->LoadDefault ( $order->GetOrderId () );
		$text_body = "Order ".$order->GetOrderId()." received. \r\n";
		if(mail($address,'Website Order - '.$prefix.$order->GetOrderId(),$body,$headers)) {*/
		if($body && !in_array($addAddress,$unsubscribes)) {
			try {
				$email->Send();
			} catch (Exception $e) {
				// Null
			}
		}
		///// EITHER WAY MARK THIS ORDER AS HAVING BEEN SPAMMED ALREADY!
		$order->SetReviewEmailSent(1);
	$count++;
} // End foreach

$message = '';
foreach($sent as $emailSent) {
	$message .= $emailSent.' - ';
}
$mailer = new PHPMailer;
$body = 'Completed: '.$message;
$mailer->From = "info@echosupplements.com";
$mailer->FromName = "ECHO";
$mailer->Subject = "EMAIL REVIEW SENT";
$mailer->Host = "smtp.gmail.com";
$mailer->Port = 465;
$mailer->Mailer = "smtp";
$mailer->SMTPAuth = true;
$mailer->Username = "info@echosupplements.com";
$mailer->Password = "bl00dlu5t";
$mailer->Body = $body;
$mailer->SMTPSecure = "ssl"; // option
#$mailer->AltBody = $body;
$mailer->AddAddress ( "info@echosupplements.com" );
$mailer->Send()
//mail('dgh500@gmail.com','EMAIL REVIEW SENT','Completed: '.$message);


?>