<?php
require_once ('../autoload.php');

class CheckoutReturnToStartHandler {
	
	var $mClean;
	
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mSessionHelper = new SessionHelper ( );
		$this->mBasket = $this->mSessionHelper->GetBasket ();
	}
	
	function Redirect() {
		$this->mSessionHelper->UnsetCheckoutStage ();
		$registry = Registry::getInstance ();
		$redirectTo = $registry->secureBaseDir . '/checkout.php';
		header ( 'Location: ' . $redirectTo );
	}
}

try {
	$handler = new CheckoutReturnToStartHandler ( );
	$handler->Redirect ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>