<?php

require_once ('../autoload.php');

/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}*/

//! For when a customer changes their details
class AccountChangePasswordHandler extends Handler {

	//! Clean array of validated input
	var $mClean;
	//! Obj : CustomerModel - The customer whose passsword is being changed
	var $mCustomer;

	//! Initialises the validation helper
	function __construct() {
		parent::__construct ();
		$this->mCustomerController = new CustomerController;
	}

	//! Makes all of the values safe using ValidationHelper -> MakeSafe(). Initialises $this->mCustomer usign the email address supplied. Checks passwords match (and correct) server side
	/*!
	 * @param $postArr - The _POST array
	 * @return Void
	 */
	function Validate($postArr) {
		$this->mError = false;
		$this->mClean ['newPassword1'] = $this->mValidationHelper->MakeSafe ( $postArr ['newPassword1'] );
		$this->mClean ['newPassword2'] = $this->mValidationHelper->MakeSafe ( $postArr ['newPassword2'] );
		$this->mClean ['oldPassword'] = $this->mValidationHelper->MakeSafe ( $postArr ['oldPassword'] );
		$this->mClean ['customerEmail'] = $this->mValidationHelper->MakeSafe ( $postArr ['customerEmail'] );
		$this->mCustomer = new CustomerModel ( $this->mClean ['customerEmail'] );

		// Check new passwords the same
		if ($this->mClean ['newPassword1'] != $this->mClean['newPassword2']) {
			$this->mError = true;
		}

		// Check old passsword is correct (by attempting to login with it)
		if (!$this->mCustomerController->Login($this->mCustomer,$this->mClean['oldPassword'])) {
			$this->mError = true;
		}
	}

	//! Actually changes the passwords if no errors have occurred (ie. the passwords are right and matched)
	function Process() {
		if (!$this->mError) {
			$this->mCustomer->SetPassword($this->mClean['newPassword1']);
			header ( 'Location: ' . $this->mRegistry->baseDir . '/account/passwordSuccess' );
		} else {
			header ( 'Location: ' . $this->mRegistry->baseDir . '/account/passwordFailure' );
		}
	}
}

try {
	$handler = new AccountChangePasswordHandler ( );
	$handler->Validate ( $_POST );
	$handler->Process ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>