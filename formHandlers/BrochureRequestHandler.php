<?php

include ('../autoload.php');
error_reporting ( E_ALL );

//! Validates, preps and sends brochure requests
class BrochureRequestHandler {
	
	//! Cleaned array of user input 
	var $mClean;
	//! PHPMailer Mail class
	var $mMail;
	
	//! Initialises mail and validation (and session) classes
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mSessionHelper = new SessionHelper ( );
	}
	
	//! Validates the user input
	function Validate($postArr) {
		try {
			$this->mClean ['catalogue'] = new CatalogueModel ( $postArr ['catalogueId'] );
		} catch ( Exception $e ) {
			return false;
		}
		$this->mClean ['catReqName'] = $this->mValidationHelper->MakeSafe ( $postArr ['catReqName'] );
		$this->mClean ['catReqAddress1'] = $this->mValidationHelper->MakeSafe ( $postArr ['catReqAddress1'] );
		$this->mClean ['catReqAddress2'] = $this->mValidationHelper->MakeSafe ( $postArr ['catReqAddress2'] );
		$this->mClean ['catReqAddress3'] = $this->mValidationHelper->MakeSafe ( $postArr ['catReqAddress3'] );
		$this->mClean ['catReqTown'] = $this->mValidationHelper->MakeSafe ( $postArr ['catReqTown'] );
		$this->mClean ['catReqCounty'] = $this->mValidationHelper->MakeSafe ( $postArr ['catReqCounty'] );
		$this->mClean ['catReqPostcode'] = $this->mValidationHelper->MakeSafe ( $postArr ['catReqPostcode'] );
		return true;
	}
	
	//! Prepares and sends the email (NB. 123-reg doesn't like phpmailer!)
	function PrepareAndSend() {
		$registry = Registry::getInstance();
		$body =  $this->mClean ["catalogue"]->GetDisplayName () . " Catalogue Request\n\r";
		$body .= (trim ( $this->mClean ["catReqName"] ) ? $this->mClean ["catReqName"] . "\n\r" : "");
		$body .= (trim ( $this->mClean ["catReqAddress1"] ) ? $this->mClean ["catReqAddress1"] . "\n\r" : "");
		$body .= (trim ( $this->mClean ["catReqAddress2"] ) ? $this->mClean ["catReqAddress2"] . "\n\r" : "");
		$body .= (trim ( $this->mClean ["catReqAddress3"] ) ? $this->mClean ["catReqAddress3"] . "\n\r" : "");
		$body .= (trim ( $this->mClean ["catReqTown"] ) ? $this->mClean ["catReqTown"] . "\n\r" : "");
		$body .= (trim ( $this->mClean ["catReqCounty"] ) ? $this->mClean ["catReqCounty"] . "\n\r" : "");
		$body .= (trim ( $this->mClean ["catReqPostcode"] ) ? $this->mClean ["catReqPostcode"] . "\n\r" : "");
		$body .= "</span>";
		if(mail('dgh500@gmail.com','Catalogue Request',$body)) {
			header ( 'Location: ' . $registry->baseDir . '/brochure/success' );
		} else {
			header ( 'Location: ' . $registry->baseDir . '/brochure/failure/send' );
		}
		
	}
	
	function ValidationFailure() {
		$registry = Registry::getInstance ();
		header ( 'Location:' . $registry->baseDir . '/brochure.php/failure/valid' );
	}

}

$handler = new BrochureRequestHandler ( );
if ($handler->Validate ( $_POST )) {
	$handler->PrepareAndSend();
} else {
	$handler->ValidationFailure ( 'Validation failure.' );
}

?>