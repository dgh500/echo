<?php
require_once ('../autoload.php');

class AddOrderBillingFormHandler extends Handler {
	
	//! Clean array of validated input
	var $mClean;
	
	//! Initialises the validation helper
	function __construct() {
		parent::__construct();
	}
	
	//! Makes all of the values safe using ValidationHelper -> MakeSafe()
	/*!
	 * @param $postArr - The _POST array
	 * @return Void
	 */
	function Validate($postArr) {
		$this->mClean['referrerTab'] 			= $this->mValidationHelper->MakeSafe($postArr['referrerTab']);
		$this->mClean['cardHoldersName'] 		= $this->mValidationHelper->MakeMysqlSafe($postArr['cardHoldersName']);
		$this->mClean['cardType'] 				= $this->mValidationHelper->MakeMysqlSafe($postArr['cardType']);
		$this->mClean['cardNumber'] 			= $this->mValidationHelper->MakeMysqlSafe($postArr['cardNumber']);
		$this->mClean['validFromMonth'] 		= $this->mValidationHelper->MakeMysqlSafe($postArr['validFromMonth']);
		$this->mClean['validFromYear'] 			= $this->mValidationHelper->MakeMysqlSafe($postArr['validFromYear']);
		$this->mClean['expiryDateMonth'] 		= $this->mValidationHelper->MakeMysqlSafe($postArr['expiryDateMonth']);
		$this->mClean['expiryDateYear'] 		= $this->mValidationHelper->MakeMysqlSafe($postArr['expiryDateYear']);
		$this->mClean['cardVerificationNumber'] = $this->mValidationHelper->MakeMysqlSafe($postArr['cardVerificationNumber']);
		$this->mClean['issueNumber'] 			= $this->mValidationHelper->MakeMysqlSafe($postArr['issueNumber']);
		
	} // End Validate()
	
	//! Update the customer to reflect the changes requested on the form, redirects the user afterwards to their account
	/*!
	 * @return Void
	 */
	function Process() {
		// Customer Details
		$_SESSION['cardHoldersName'] 		= $this->mClean['cardHoldersName'];
		$_SESSION['cardType'] 				= $this->mClean['cardType'];
		$_SESSION['cardNumber'] 			= $this->mClean['cardNumber'];
		$_SESSION['validFromMonth'] 		= $this->mClean['validFromMonth'];
		$_SESSION['validFromYear'] 			= $this->mClean['validFromYear'];
		$_SESSION['expiryDateMonth'] 		= $this->mClean['expiryDateMonth'];
		$_SESSION['expiryDateYear'] 		= $this->mClean['expiryDateYear'];
		$_SESSION['cardVerificationNumber'] = $this->mClean['cardVerificationNumber'];
		$_SESSION['issueNumber'] 			= $this->mClean['issueNumber'];
	} // End Process
}

try {
	$handler = new AddOrderBillingFormHandler;
	$handler->Validate($_POST);
	$handler->Process();
} catch(Exception $e) {
	echo $e->getMessage();
}

?>