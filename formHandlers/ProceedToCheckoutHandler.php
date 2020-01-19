<?php

include ('../autoload.php');

/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}die();*/

//! Performs any actions needed on the basket, and takes the user to the checkout stage
class ProceedToCheckoutHandler {

	//! Cleaned/Validated internal array
	var $mClean;

	//! Initialises validation, session helpers and the basket
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mSessionHelper = new SessionHelper ( );
		$this->mBasket = $this->mSessionHelper->GetBasket ();
	}

	//! Performs validation on the POST array
	function Validate($postArr) {
		// None needed
		$this->mSessionHelper->SetPostageMethod($_POST['postageMethodId']);
	}

	//! Takes the user to the checkout
	function ProceedToCheckout() {
		$registry = Registry::getInstance ();
		$secureBaseDir = $registry->secureBaseDir;
		#$secureBaseDir = $registry->baseDir;
		if (isset ( $_POST ['aid'] )) {
			$sendTo = $secureBaseDir . '/checkout/s/' . $this->mBasket->GetBasketId () . '/aid/' . $_POST ['aid'];
		} else {
			$sendTo = $secureBaseDir . '/checkout.php?s=' . $this->mBasket->GetBasketId ();
		}
		header ( 'Location: ' . $sendTo );
	}

}

try {
	$handler = new ProceedToCheckoutHandler ( );
	$handler->Validate ( $_POST );
	$handler->ProceedToCheckout ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>