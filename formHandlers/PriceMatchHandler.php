<?php

include ('../autoload.php');
#include ('../phpMailer/class.phpmailer.php');
error_reporting ( E_ALL );

//! Validates, preps and sends price match requests
class PriceMatchHandler {

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
		$this->mClean ['priceMatchName'] = $this->mValidationHelper->MakeSafe ( $postArr ['priceMatchName'] );
		$this->mClean ['priceMatchPhone'] = $this->mValidationHelper->MakeSafe ( $postArr ['priceMatchPhone'] );
		$this->mClean ['priceMatchEmail'] = $this->mValidationHelper->MakeSafe ( $postArr ['priceMatchEmail'] );
		$this->mClean ['priceMatchWebsite'] = $this->mValidationHelper->MakeSafe ( $postArr ['priceMatchWebsite'] );
		$this->mClean ['productId'] = $this->mValidationHelper->MakeSafe ( $postArr ['productId'] );
		return true;
	}

	//! Prepares the PHPMailer class
	function Prepare() {
		$product = new ProductModel($this->mClean['productId']);
		$plHelper = new PublicLayoutHelper;
		$url = $plHelper->LoadLinkHref($product);
		$this->mMail = new PHPMailer;
		$body = '<span style="font-family: Arial; font-size: 10pt;">';
		$body .= '<h1>Price Match Request</h1>';
		$body .= (trim ( $this->mClean ["priceMatchName"] ) ? 'Person Name: ' . $this->mClean ["priceMatchName"] . '<br />' : '');
		$body .= (trim ( $this->mClean ["priceMatchPhone"] ) ? 'Person Phone: ' . $this->mClean ["priceMatchPhone"] . '<br />' : '');
		$body .= (trim ( $this->mClean ["priceMatchEmail"] ) ? 'Person Email: ' . $this->mClean ["priceMatchEmail"] . '<br />' : '');
		$body .= (trim ( $this->mClean ["priceMatchWebsite"] ) ? 'Website to beat: ' . $this->mClean ["priceMatchWebsite"] . '<br />' : '');
		$body .= 'Product: <a href="'.$url.'">'.$product->GetDisplayName().'</a>';
		$body .= "</span>";
		$text_body = "Price Match Request\r\n
				" . $this->mClean ["priceMatchName"] . "\r\n
				" . $this->mClean ["priceMatchPhone"] . "\r\n
				" . $this->mClean ["priceMatchEmail"] . "\r\n
				" . $this->mClean ["priceMatchWebsite"] . "\r\n
				";
		$this->mMail->From = "info@echosupplements.com";
		$this->mMail->FromName = "Price Match Request";
		$this->mMail->Subject = "Website Price Match Request";
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
		if (! $this->mMail->Send ()) {
		#	header ( 'Location: ' . $registry->baseDir . '/priceMatch/failure/send' );
			echo 0;
		} else {
		#	header ( 'Location: ' . $registry->baseDir . '/priceMatch/success' );
			echo 1;
		}
	}

	function ValidationFailure() {
		$registry = Registry::getInstance ();
		header ( 'Location:' . $registry->baseDir . '/priceMatch/failure/valid' );
	}

}

$handler = new PriceMatchHandler ( );
if ($handler->Validate ( $_POST )) {
	$handler->Prepare ();
	$handler->SendPriceMatchRequest ();
} else {
	$handler->ValidationFailure ( 'Validation failure.' );
}

?>