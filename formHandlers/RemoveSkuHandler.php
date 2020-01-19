<?php

require_once ('../autoload.php');

/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}*/

class RemoveSkuHandler {
	
	var $mClean;
	
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mSessionHelper = new SessionHelper ( );
		$this->mBasket = $this->mSessionHelper->GetBasket ();
	}
	
	function Validate($postArr) {
		$this->mClean ['skuToRemove'] = $_POST ['skuToRemove'];
	}
	
	function RemoveSku() {
		$registry = Registry::getInstance ();
		$sku = new SkuModel ( $this->mClean ['skuToRemove'] );
		$this->mBasket->RemoveFromBasket ( $sku );
		$redirectTo = $registry->baseDir . '/basket.php';
		header ( 'Location: ' . $redirectTo );
	}

}

try {
	$handler = new RemoveSkuHandler ( );
	$handler->Validate ( $_POST );
	$handler->RemoveSku ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>