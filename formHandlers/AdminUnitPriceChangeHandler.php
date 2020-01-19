<?php

require_once ('../autoload.php');

//! Form handler for changing the price of a SKU in a basket
class AdminUnitPriceChangeHandler {
	
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
		$this->mClean ['skuId'] 			= $postArr ['skuId'];
		$this->mClean ['skuUnitPrice'] 		= $postArr['skuUnitPrice'];
		$this->mClean ['prevSkuUnitPrice'] 	= $postArr['prevSkuUnitPrice'];		
		$this->mClean ['unitPriceSkuQty'] 	= $postArr['unitPriceSkuQty'];				
	}
	
	//! Actually update the basket with the new quantity
	function UpdateBasket() {
		$registry = Registry::getInstance ();
		$sku = new SkuModel ( $this->mClean ['skuId'] );
		$this->mBasket->ChangePriceForSku( $sku, $this->mClean ['skuUnitPrice'], false, false );
		
		// The adjustment is:  (No.SKU * OldPrice) - (No.SKU * NewPrice)
		$adjustment = ($this->mClean['unitPriceSkuQty'] * $this->mClean['prevSkuUnitPrice']) - ($this->mClean['unitPriceSkuQty'] * $this->mClean['skuUnitPrice']);
		$newTotal = $this->mBasket->GetTotal() - $adjustment;
		
		$this->mBasket->SetTotal($newTotal);
		
		header ( 'Location: ' . $this->mRedirectTo );
	}
} // End QuantityChangeHandler


try {
	$handler = new AdminUnitPriceChangeHandler();
	$handler->Validate ( $_POST );
	$handler->UpdateBasket ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>