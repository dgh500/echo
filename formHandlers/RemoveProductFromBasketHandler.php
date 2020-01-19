<?php
session_start ();

require_once ('autoload.php');

/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}*/

class RemoveProductFromBasketHandler {
	
	var $mProductId;
	var $mClean;
	
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
	}
	
	function Validate($postArr) {
		$this->mClean ['sku'] = new SkuModel ( $_POST ['skuId'] );
		$this->mClean ['basket'] = new BasketModel ( $_POST ['basketId'] );
	}
	
	function RemoveFromBasket() {
		$registry = Registry::getInstance ();
		$this->mClean ['basket']->RemoveFromBasket ( $this->mClean ['sku'] );
		$redirectTo = $registry->baseDir . '/basket';
		header ( 'Location: ' . $redirectTo );
	}

}

try {
	$handler = new RemoveProductFromBasketHandler ( );
	$handler->Validate ( $_POST );
	$handler->RemoveFromBasket ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>