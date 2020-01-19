<?php

include ('../autoload.php');
#include ('../phpMailer/class.phpmailer.php');
error_reporting ( E_ALL );

//! Validates, preps and sends price match requests
class NewsletterViewHandler {

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
		$this->mClean ['signUpEmail'] = $this->mValidationHelper->MakeSafe ( $postArr ['signUpEmail'] );
		return true;
	}

	//! Prepares the PHPMailer class
	function Prepare() {
		$this->mMail = new PHPMailer;
		$body = '<span style="font-family: Arial; font-size: 10pt;">';
		$body .= '<h1>Newsletter Request</h1>';
		$body .= (trim ( $this->mClean ['signUpEmail'] ) ? ' Email Address: ' . $this->mClean ['signUpEmail'] . '<br />' : '');
		$body .= "</span>";
#		var_dump($body);die();
		$text_body = "Newsletter Request\r\n
				Email Address: " . $this->mClean ['signUpEmail'] . "\r\n
				";
		$this->mMail->From = "info@echosupplements.com";
		$this->mMail->FromName = "Newsletter Request";
		$this->mMail->Subject = "Newsletter Request";
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
	function SendPriceMatchRequest() {
		$registry = Registry::getInstance ();
		if($this->mMail->Send()) {
			header ( 'Location: ' . $registry->baseDir . '/newsletter/success' );
		}
	}


}

$handler = new NewsletterViewHandler ( );
$handler->Validate ($_POST);
$handler->Prepare ();
$handler->SendPriceMatchRequest ();

?>