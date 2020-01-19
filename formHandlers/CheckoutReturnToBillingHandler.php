<?php
require_once ('../autoload.php');

class CheckoutReturnToBillingHandler extends Handler {
	
	var $mClean;
	
	function __construct() {
		parent::__construct();
		$this->mSessionHelper = new SessionHelper ( );
	}
	
	function Redirect() {
		$this->mSessionHelper->SetCheckoutStage ( 'billingDetails' );
		$redirectTo = $this->mRegistry->secureBaseDir . '/checkout.php';
		header ( 'Location: ' . $redirectTo );
	}
}

try {
	$handler = new CheckoutReturnToBillingHandler ( );
	$handler->Redirect ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>