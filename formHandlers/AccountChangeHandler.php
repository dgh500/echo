<?php

require_once ('../autoload.php');

/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}*/

//! For when a customer changes their details
class AccountChangeHandler extends Handler {
	
	//! Clean array of validated input
	var $mClean;
	
	//! Initialises the validation helper
	function __construct() {
		parent::__construct ();
	}
	
	//! Makes all of the values safe using ValidationHelper -> MakeSafe()
	/*!
	 * @param $postArr - The _POST array
	 * @return Void
	 */
	function Validate($postArr) {
		$this->mClean ['daytimePhone'] = $this->mValidationHelper->MakeSafe ( $postArr ['daytimePhone'] );
		$this->mClean ['title'] = $this->mValidationHelper->MakeSafe ( $postArr ['title'] );
		$this->mClean ['firstName'] = $this->mValidationHelper->MakeSafe ( $postArr ['firstName'] );
		$this->mClean ['lastName'] = $this->mValidationHelper->MakeSafe ( $postArr ['lastName'] );
		$this->mClean ['mobilePhone'] = $this->mValidationHelper->MakeSafe ( $postArr ['mobilePhone'] );		
		$this->mClean ['customerEmail'] = $this->mValidationHelper->MakeSafe ( $postArr ['customerEmail'] );
	}
	
	//! Update the customer to reflect the changes requested on the form, redirects the user afterwards to their account
	/*!
	 * @return Void
	 */
	function Process() {
		$customer = new CustomerModel ( $this->mClean ['customerEmail'] );
		$customer->SetTitle ( $this->mClean ['title'] );
		$customer->SetFirstName ( $this->mClean ['firstName'] );
		$customer->SetLastName ( $this->mClean ['lastName'] );		
		$customer->SetDaytimeTelephone ( $this->mClean ['daytimePhone'] );
		$customer->SetMobileTelephone ( $this->mClean ['mobilePhone'] );
		header ( 'Location: ' . $this->mBaseDir . '/account' );
	}
}

try {
	$handler = new AccountChangeHandler ( );
	$handler->Validate ( $_POST );
	$handler->Process ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>