<?php

/**
 * Produces an email list and emails it to me!
 */
$debug = false;

 include_once('../autoload.php');

 $cController = new CustomerController;
 $product = new ProductModel($_GET['q']);

 if($debug) {
 	$list = $cController->GetAllCustomersByProduct($product,true);
	echo $list;
 } else {

	$list = '';
	foreach($cController->GetAllCustomersByProduct($product) as $customer) {
		$list .= $customer->GetEmail()."\n";
	}
	mail('dave@echosupplements.com','Newsletter List - '.$product->GetDisplayName(),$list,"From: Echo");
}
?>