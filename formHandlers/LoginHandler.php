<?php

require_once ('../autoload.php');

/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}*/

class LoginHandler {
	
	var $mClean;
	
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mSessionHelper = new SessionHelper ( );
	}
	
	function Validate($postArr) {
		$this->mClean ['loginEmail'] = $this->mValidationHelper->MakeSafe ( $postArr ['loginEmail'] );
		$this->mClean ['loginPassword'] = $this->mValidationHelper->MakeSafe ( $postArr ['loginPassword'] );
	}
	
	function Login() {
		$registry = Registry::getInstance ();
		$customerController = new CustomerController ( );
		try {
			$customer = new CustomerModel ( $this->mClean ['loginEmail'] );
			if ($customerController->Login ( $customer, $this->mClean ['loginPassword'] )) {
				// Success
				$this->mSessionHelper->SetCustomer ( $customer->GetCustomerId () );
				$this->mSessionHelper->SetLoginStage ( 'logggedIn' );
			} else {
				// Failure
				$this->mSessionHelper->SetLoginStage ( 'loginFailure' );
			}
		} catch ( Exception $e ) {
			$this->mSessionHelper->SetLoginStage ( 'loginFailure' );
		}
		$redirectTo = $registry->baseDir . '/account';
		header ( 'Location: ' . $redirectTo );
	}

}

try {
	$handler = new LoginHandler ( );
	$handler->Validate ( $_POST );
	$handler->Login ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>