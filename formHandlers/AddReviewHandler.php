<?php

include ('../autoload.php');
#include ('../phpMailer/class.phpmailer.php');
error_reporting ( E_ALL );

//! Validates, preps and sends add review requests
class AddReviewHandler {

	//! Cleaned array of user input
	var $mClean;
	//! PHPMailer Mail class
	var $mMail;

	//! Initialises mail and validation (and session) classes
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mSessionHelper = new SessionHelper ( );}

	//! Validates the user input
	function Validate($postArr) {
		$this->mClean ['reviewProduct'] = $this->mValidationHelper->MakeSafe($postArr['reviewProduct'] );
		$this->mClean ['reviewRating'] 	= $this->mValidationHelper->MakeSafe($postArr['reviewRating'] );
		$this->mClean ['reviewName'] 	= $this->mValidationHelper->MakeSafe($postArr['reviewName'] );
		$this->mClean ['reviewIP'] 		= $this->mValidationHelper->MakeSafe($postArr['reviewIP'] );
		$this->mClean ['reviewText'] 	= $this->mValidationHelper->MakeSafe($postArr['reviewText'] );
		return true;
	}

	//! Prepares the PHPMailer class
	function Prepare() {
		$product = new ProductModel($this->mClean['reviewProduct']);
		$plHelper = new PublicLayoutHelper;
		$url = $plHelper->LoadLinkHref($product);
		$this->mMail = new PHPMailer;
		$body = '<span style="font-family: Arial; font-size: 10pt;">';
		$body .= '<h1>New Review</h1>';
		$body .= (trim ( $this->mClean ["reviewName"] ) ? 'Reviewer Name: ' . $this->mClean ["reviewName"] . '<br />' : '');
		$body .= (trim ( $this->mClean ["reviewText"] ) ? 'Review Text: ' . $this->mClean ["reviewText"] . '<br />' : '');
		$body .= (trim ( $this->mClean ["reviewIP"] ) ? 'Review IP: ' . $this->mClean ["reviewIP"] . '<br />' : '');
		$body .= (trim ( $this->mClean ["reviewRating"] ) ? 'Review Rating: ' . $this->mClean ["reviewRating"] . '<br />' : '');
		$body .= 'Product: <a href="'.$url.'">'.$product->GetDisplayName().'</a>';
		$body .= "</span>";
		$text_body = "New Review\r\n
				" . $this->mClean ["reviewName"] . "\r\n
				" . $this->mClean ["reviewIP"] . "\r\n
				" . $this->mClean ["reviewText"] . "\r\n
				";
		$this->mMail->From = "info@echosupplements.com";
		$this->mMail->FromName = "Echo Supplements - New Review";
		$this->mMail->Subject = "New Product Review";
		$this->mMail->Host = "smtp.gmail.com";
		$this->mMail->Port = 465;
		$this->mMail->Mailer = "smtp";
		$this->mMail->SMTPAuth = true;
		$this->mMail->Username = "info@echosupplements.com";
		$this->mMail->Password = "bl00dlu5t";
		$this->mMail->Body = $body;
		$this->mMail->SMTPSecure = "ssl"; // option
		$this->mMail->AltBody = $text_body;
		$this->mMail->AddAddress ( "info@echosupplements.com" );
	}

	//! Sends the email
	function SendReview() {
		$registry = Registry::getInstance ();
		// Try and get rid of SOME spam..
		if(!preg_match_all('/(viagra|cialis|cigarette|http|penis)/', strtolower($this->mClean['reviewText']),$matches) && $this->mClean['reviewIP'] != '80.82.68.109' && $this->mClean ['reviewIP'] != '94.100.24.174') {
			if (! $this->mMail->Send ()) {
				echo 0;
			} else {
				echo 1;
			}
		} else {
			echo 0;
		}
	}

	function ValidationFailure() {
		$registry = Registry::getInstance ();
		header ( 'Location:' . $registry->baseDir . '/priceMatch/failure/valid' );
	}

}

$handler = new AddReviewHandler ( );
if ($handler->Validate ( $_POST )) {
	$handler->Prepare();
	$handler->SendReview();
} else {
	$handler->ValidationFailure ( 'Validation failure.' );
}

?>