<?php
require_once ('../autoload.php');

// Redirects the customer to the appropriate catalogue URL to allow them to continue shopping
class CheckoutContinueShoppingHandler extends Handler {

	//! Constructor
	function __construct() {
		// Continue the session
		$this->mSessionHelper = new SessionHelper();
		// Find out what the order ID was so that we can send them to the correct catalogue
		$this->mOrder = new OrderModel($this->mSessionHelper->GetSavedOrderId());
		// Then reset the session to prevent corrupt orders
		$this->mSessionHelper->Reset();
	} // End __construct()

	//! Redirect the user
	function Process() {
		$redirectTo = $this->mOrder->GetCatalogue()->GetUrl();
		header('Window-target: _parent');
		header('Location: '.$redirectTo);
	} // End Process
}

try {
	$handler = new CheckoutContinueShoppingHandler();
	$handler->Process();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>