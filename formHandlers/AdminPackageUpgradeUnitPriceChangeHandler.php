<?php

require_once ('../autoload.php');

//! Form handler for changing the price of a Package upgrade in a basket
class AdminPackageUpgradeUnitPriceChangeHandler {
	
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
		$this->mClean ['upgradeSkuId'] 				= $postArr['upgradeSkuId'];
		$this->mClean ['upgradeSkuUnitPrice'] 		= $postArr['upgradeSkuUnitPrice'];
		$this->mClean ['prevUpgradeSkuUnitPrice'] 	= $postArr['prevUpgradeSkuUnitPrice'];		
		$this->mClean ['unitPriceUpgradeSkuQty']	= $postArr['unitPriceUpgradeSkuQty'];				
	}
	
	//! Actually update the basket with the new quantity
	function UpdateBasket() {
		$registry = Registry::getInstance ();
		$sku = new SkuModel($this->mClean['upgradeSkuId']);
		$this->mBasket->ChangePriceForSku($sku,$this->mClean['upgradeSkuUnitPrice'],false,true);
		
		// The adjustment is:  (No.Package Upgrade * OldPrice) - (No.Package Upgrade * NewPrice)
		$adjustment = ($this->mClean['unitPriceUpgradeSkuQty'] * $this->mClean['prevUpgradeSkuUnitPrice']) - ($this->mClean['unitPriceUpgradeSkuQty'] * $this->mClean['upgradeSkuUnitPrice']);
		$newTotal = $this->mBasket->GetTotal() - $adjustment;
		
		$this->mBasket->SetTotal($newTotal);
		
		header ( 'Location: ' . $this->mRedirectTo );
	}
} // End QuantityChangeHandler


try {
	$handler = new AdminPackageUpgradeUnitPriceChangeHandler();
	$handler->Validate ( $_POST );
	$handler->UpdateBasket ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>