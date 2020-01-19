<?php

/**
 * Produces an email list and emails it to me!
 */
$debug = false;

 include_once('../autoload.php');

 $cController = new CustomerController;
 $brand = new ManufacturerModel($_GET['q']);

 if($debug) {
 	$list = $cController->GetAllCustomersByBrand($brand,true);
	echo $list;
 } else {

	$list = '';
	foreach($cController->GetAllCustomersByBrand($brand) as $customer) {
		$list .= $customer->GetEmail()."\n";
	}
	mail('dave@echosupplements.com','Newsletter List - '.$brand->GetDisplayName(),$list);
}
?>