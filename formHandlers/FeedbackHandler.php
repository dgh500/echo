<?php

include ('../autoload.php');
error_reporting ( E_ALL );

//! Validates, preps and sends price match requests
class FeedbackHandler {

	//! Cleaned array of user input
	var $mClean;
	//! PHPMailer Mail class
	var $mMail;

	//! Initialises mail and validation (and session) classes
	function __construct() {
		$this->mValidationHelper= new ValidationHelper();
		$this->mSessionHelper 	= new SessionHelper();}

	//! Validates the user input
	function Validate($postArr) {
		$this->mClean ['feedbackName'] 	= $this->mValidationHelper->MakeSafe ( $postArr ['feedbackName'] );
		$this->mClean ['feedbackEmail'] = $this->mValidationHelper->MakeSafe ( $postArr ['feedbackEmail'] );
		$this->mClean ['feedbackText'] 	= $this->mValidationHelper->MakeSafe ( $postArr ['feedbackText'] );
		return true;
	}

	//! Prepares the PHPMailer class
	function Prepare() {
		$body = '<span style="font-family: Arial; font-size: 10pt;">';
		$body .= '<h1>Feedback</h1>';
		$body .= (trim ( $this->mClean ["feedbackName"] ) 	? 'Name: '.$this->mClean["feedbackName"].'<br />' 		: '');
		$body .= (trim ( $this->mClean ["feedbackEmail"] ) 	? 'Email: '.$this->mClean["feedbackEmail"].'<br />' 	: '');
		$body .= (trim ( $this->mClean ["feedbackText"] ) 	? 'Feedback: '.$this->mClean["feedbackText"].'<br />' 	: '');
		$body .= "</span>";
		$text_body = 'Feedback';
		$this->mMail->From = "feedback@echosupplements.com";
		$this->mMail->FromName = "Website Feedback";
		$this->mMail->Subject = "Website Feedback";
		$this->mMail->Host = "smtp.gmail.com";
		$this->mMail->Port = 465;
		$this->mMail->Mailer = "smtp";
		$this->mMail->SMTPAuth = true;
		$this->mMail->Username = "info@echosupplements.com";
		$this->mMail->Password = "c00piesway";
		$this->mMail->Body = $body;
		$this->mMail->SMTPSecure = "ssl"; // option
		$this->mMail->AltBody = $text_body;
		$this->mMail->AddAddress ( "info@echosupplements.com" );
	}

	//! Sends the email
	function SendFeedback() {
		$registry = Registry::getInstance ();
		if (! $this->mMail->Send ()) {
			header ('Location: '.$registry->baseDir.'/feedback/failure/send' );
		} else {
			header ('Location: '.$registry->baseDir.'/feedback/success' );
		}
	}

	function ValidationFailure() {
		$registry = Registry::getInstance ();
		header ( 'Location:' . $registry->baseDir . '/feedback/failure/valid' );
	}

}

$handler = new FeedbackHandler();
if ($handler->Validate($_POST)) {
	$handler->Prepare();
	$handler->SendFeedback();
} else {
	$handler->ValidationFailure('Validation failure.');
}

?>