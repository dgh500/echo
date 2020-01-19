<?php

require_once ('../autoload.php');

//! Form handler for changing the price of a SKU in a basket
class AdminPostageChangeHandler {
	
	//! A cleaned (validated) array of internal variables representative of the POST-ed ones
	var $mClean;
	
	//! Initialises the validation helper, session helper and basket
	function __construct($redirectTo=false) {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mSessionHelper = new SessionHelper ( );
		$this->mBasket = $this->mSessionHelper->GetBasket ();
		$registry = Registry::getInstance ();
		if($redirectTo) {
			$this->mRedirectTo = $redirectTo;
		} else {
			$this->mRedirectTo = $registry->viewDir.'/AddOrderView2.php#basketTab';	
		}		
	}
	
	//! Validates and internalises the post variables
	function Validate($postArr) {
		$this->mClean ['editableCurrentPostage'] 	= $postArr ['editableCurrentPostage'];
		$this->mClean ['currentPostage'] 			= $postArr['currentPostage'];
	}
	
	//! Actually update the basket with the new quantity
	function UpdateBasket() {
		$registry = Registry::getInstance ();
		$this->mBasket->SetPostageUpgrade($this->mClean ['editableCurrentPostage']);
		$_SESSION['postageChanged'] = true;
		
		header ( 'Location: ' . $this->mRedirectTo );
	}
} // End QuantityChangeHandler


try {
	$handler = new AdminPostageChangeHandler();
	$handler->Validate ( $_POST );
	$handler->UpdateBasket ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>