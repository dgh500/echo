<?php

require_once ('autoload.php');

try {
	$registry = Registry::getInstance ();
	if (isset ( $_GET ['threeDSecureComplete'] )) {
		$threeDSecureComplete = true;
	} else {
		$threeDSecureComplete = false;
	}
	$checkoutPage = new CheckoutView ( $registry->catalogue, $threeDSecureComplete );
	echo $checkoutPage->LoadDefault ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>