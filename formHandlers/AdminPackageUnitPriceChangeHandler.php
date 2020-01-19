<?php

require_once ('../autoload.php');

//! Form handler for changing the price of a Package in a basket
class AdminPackageUnitPriceChangeHandler {
	
	//! A cleaned (validated) array of internal variables representative of the POST-ed ones
	var $mClean;
	
	//! Initialises the validation helper, session helper and basket
	function __construct($redirectTo=false) {
		$this->mValidationHelper 	= new ValidationHelper();
		$this->mSessionHelper 		= new SessionHelper();
		$this->mBasket 				= $this->mSessionHelper->GetBasket();
		$registry 					= Registry::getInstance();
		if($redirectTo) {
			$this->mRedirectTo = $redirectTo;
		} else {
			$this->mRedirectTo = $registry->viewDir.'/AddOrderView2.php#basketTab';	
		}		
	}
	
	//! Validates and internalises the post variables
	function Validate($postArr) {
		$this->mClean ['packageId'] 			= $postArr['packageId'];
		$this->mClean ['packageUnitPrice'] 		= $postArr['packageUnitPrice'];
		$this->mClean ['prevPackageUnitPrice'] 	= $postArr['prevPackageUnitPrice'];		
		$this->mClean ['unitPricePackageQty'] 	= $postArr['unitPricePackageQty'];				
	}
	
	//! Actually update the basket with the new quantity
	function UpdateBasket() {
		$registry = Registry::getInstance ();
		$package  = new PackageModel($this->mClean['packageId']);
		$this->mBasket->ChangePriceForPackage($package,$this->mClean['packageUnitPrice']);
		
		// The adjustment is:  (No.Package * OldPrice) - (No.Package * NewPrice)
		$adjustment = ($this->mClean['unitPricePackageQty'] * $this->mClean['prevPackageUnitPrice']) - ($this->mClean['unitPricePackageQty'] * $this->mClean['packageUnitPrice']);
		$newTotal = $this->mBasket->GetTotal() - $adjustment;
		
		$this->mBasket->SetTotal($newTotal);
		
		header ( 'Location: ' . $this->mRedirectTo );
	}
} // End QuantityChangeHandler


try {
	$handler = new AdminPackageUnitPriceChangeHandler();
	$handler->Validate ( $_POST );
	$handler->UpdateBasket ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>