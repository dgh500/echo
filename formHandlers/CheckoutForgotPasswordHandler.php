<?php

require_once ('../autoload.php');

/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}*/

class CheckoutForgotPasswordHandler {
	
	var $mClean;
	
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mSessionHelper = new SessionHelper ( );
		$this->mBasket = $this->mSessionHelper->GetBasket ();
	}
	
	function Redirect() {
		$this->mSessionHelper->SetCheckoutStage ( 'forgottenPassword' );
		$registry = Registry::getInstance ();
		$redirectTo = $registry->secureBaseDir . '/checkout.php';
		header ( 'Location: ' . $redirectTo );
	}

}

try {
	$handler = new CheckoutForgotPasswordHandler ( );
	$handler->Redirect ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>