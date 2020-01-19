<?php

require_once ('../autoload.php');

//! Form handler for changing the amount of a SKU in a basket
class QuantityChangeHandler {
	
	//! A cleaned (validated) array of internal variables representative of the POST-ed ones
	var $mClean;
	
	//! Initialises the validation helper, session helper and basket
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mSessionHelper = new SessionHelper ( );
		$this->mCountry = new CountryModel($_SESSION ['countryId']);
		$this->mBasket = $this->mSessionHelper->GetBasket ();
	}
	
	//! Validates and internalises the post variables
	function Validate($postArr) {
		$this->mClean ['skuId'] = $postArr ['skuId'];
		$this->mClean ['skuQty'] = $postArr ['skuQty'];
	}
	
	//! Actually update the basket with the new quantity
	function UpdateBasket() {
		$registry = Registry::getInstance ();
		$sku = new SkuModel ( $this->mClean ['skuId'] );
		$this->mBasket->SetSkuQty ( $sku, $this->mClean ['skuQty'], $this->mCountry->IsVatFree() );
		$redirectTo = $registry->baseDir . '/basket.php';
		header ( 'Location: ' . $redirectTo );
	}
} // End QuantityChangeHandler


try {
	$handler = new QuantityChangeHandler ( );
	$handler->Validate ( $_POST );
	$handler->UpdateBasket ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>