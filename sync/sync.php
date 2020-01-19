<?php
set_time_limit ( 600 );
header('Content-Type: text/xml');
/*function getmicrotime()
{
list($usec, $sec) = explode(" ",microtime());
return ((float)$usec + (float)$sec*1000);
}
// Start script
$time=getmicrotime();*/

$disableJavascriptAutoload = 1;
include ('../autoload.php');

date_default_timezone_set ( 'GMT' );
$now = time ();
$gmt = date ( 'd F Y H:i:s', $now );

$orderController 	= new OrderController;
$validationHelper 	= new ValidationHelper;
$moneyHelper 		= new MoneyHelper;
$allNotDownloaded 	= $orderController->GetNotDownloaded();

$xmlData [] = '<?xml version="1.0" encoding="UTF-8"?>';
$xmlData [] = '<OrderList StoreAccountName="Deep Blue Dive">';
foreach($allNotDownloaded as $order) {
	$customer = $order->GetCustomer ();
	$shippingAddr = $order->GetShippingAddress ();
	$billingAddr = $order->GetBillingAddress ();
	$xmlData [] = '<Order id="ECHO'.$order->GetOrderId ().'" currency="GBP">';
	$xmlData [] = '<Time>'.$gmt.'</Time>';
	$xmlData [] = '<ShipTime>'.$gmt.'</ShipTime>';
	$xmlData [] = '<NumericTime>'.$now.'</NumericTime>';
	$xmlData [] = '<AddressInfo type="ship">';
	$xmlData [] = '<Name>';
	$xmlData [] = '<First>'.$validationHelper->MakeLinkSafe ( $customer->GetFirstName (), true , true, true).'</First>';
	$xmlData [] = '<Last>'.$validationHelper->MakeLinkSafe ( $customer->GetLastName (), true , true, true ).'</Last>';
	$xmlData [] = '<Full>'.$validationHelper->MakeLinkSafe ( $customer->GetFirstName (), true  , true, true).' '.$validationHelper->MakeLinkSafe ( $customer->GetLastName (), true ).'</Full>';
	$xmlData [] = '</Name>';
	$xmlData [] = '<Company>'.$validationHelper->MakeLinkSafe ( $shippingAddr->GetCompany (), true , true, true ).'</Company>';
	$xmlData [] = '<Address1>'.$validationHelper->MakeLinkSafe ( $shippingAddr->GetLine1 (), true  , true, true).'</Address1>';
	$xmlData [] = '<Address2>'.$validationHelper->MakeLinkSafe ( $shippingAddr->GetLine2 (), true  , true, true).'</Address2>';
	$xmlData [] = '<City>'.$validationHelper->MakeLinkSafe ( $shippingAddr->GetLine3 (), true  , true, true).'</City>';
	$xmlData [] = '<State>'.$validationHelper->MakeLinkSafe ( $shippingAddr->GetCounty (), true  , true, true).'</State>';
	$xmlData [] = '<Country>United Kingdom</Country>';
	$xmlData [] = '<Zip>'.$validationHelper->MakeLinkSafe ( $shippingAddr->GetPostcode (), true  , true, true).'</Zip>';
	$xmlData [] = '<Phone>'.trim( $validationHelper->MakeLinkSafe ( $customer->GetDaytimeTelephone ()) ).'</Phone>';
	$xmlData [] = '</AddressInfo>';
	$xmlData [] = '<AddressInfo type="bill">';
	$xmlData [] = '<Name>';
	$xmlData [] = '<First>'.$validationHelper->MakeLinkSafe ( $customer->GetFirstName (), true  , true, true).'</First>';
	$xmlData [] = '<Last>'.$validationHelper->MakeLinkSafe ( $customer->GetLastName (), true , true, true ).'</Last>';
	$xmlData [] = '<Full>'.$validationHelper->MakeLinkSafe ( $customer->GetFirstName (), true , true, true ).' '.$validationHelper->MakeLinkSafe ( $customer->GetLastName (), true ).'</Full>';
	$xmlData [] = '</Name>';
	$xmlData [] = '<Company>'.$validationHelper->MakeLinkSafe ( $billingAddr->GetCompany (), true , true, true ).'</Company>';
	$xmlData [] = '<Address1>'.$validationHelper->MakeLinkSafe ( $billingAddr->GetLine1 (), true , true, true ).'</Address1>';
	$xmlData [] = '<Address2>'.$validationHelper->MakeLinkSafe ( $billingAddr->GetLine2 (), true , true, true ).'</Address2>';
	$xmlData [] = '<City>'.$validationHelper->MakeLinkSafe ( $billingAddr->GetLine3 (), true , true, true ).'</City>';
	$xmlData [] = '<State>'.$validationHelper->MakeLinkSafe ( $billingAddr->GetCounty (), true , true, true ).'</State>';
	$xmlData [] = '<Country>United Kingdom</Country>';
	$xmlData [] = '<Zip>'.$validationHelper->MakeLinkSafe ( $billingAddr->GetPostcode (), true  , true, true).'</Zip>';
	$xmlData [] = '<Phone>'.trim ($validationHelper->MakeLinkSafe (  $customer->GetDaytimeTelephone () ,false, true, true) ).'</Phone>';
	$xmlData [] = '</AddressInfo>';
	$xmlData [] = '<Shipping />';
	$xmlData [] = '<CreditCard expiration="10/10" type="cc" />';
	// All shipped items
	$i = 0;
	$basket = $order->GetBasket ();
	foreach($order->GetShippedItems() as $orderItem) {
		// Only consider non-packages
		if(!$orderItem->GetPackageId()) {
			// Have to handle package items and non-package items differently...
			if(!$orderItem->GetPackageId() && $orderItem->GetPackageUpgrade()) {
				// An upgrade - take its normal package price and add the upgrade cost
				$unitPrice = $orderItem->GetPackageItemPrice() + $orderItem->GetPrice();
			} elseif(!$orderItem->GetPackageId() && $orderItem->GetPackageProduct()) {
				// A package product - just the package price divided by the number of items in it
				$unitPrice = $orderItem->GetPackageItemPrice();
			} else {
				// A regular product
				$unitPrice = $orderItem->GetPrice();
			}
			$xmlData [] = '<Item num="'.$i.'">';
			$xmlData [] = '<Id>'.$orderItem->GetOrderItemId().'</Id>'; // Limited to 255 chars
			$xmlData [] = '<Code>'.$orderItem->GetSageCode().'</Code>';
			$xmlData [] = '<Quantity>1</Quantity>'; // Limited to 9 digits
			$xmlData [] = '<Unit-Price>'.$unitPrice.'</Unit-Price>';
			if($orderItem->GetTaxable()) {
				$xmlData [] = '<Tax>'.$moneyHelper->VAT($unitPrice).'</Tax>';
			} else {
				$xmlData [] = '<Tax>0.00</Tax>';
			}
			$xmlData [] = '<Shipping>0.00</Shipping>';
			$xmlData [] = '<Shipping-Tax>0.00</Shipping-Tax>';
			$xmlData [] = '<Description>'.$validationHelper->MakeLinkSafe($orderItem->GetDisplayName(),false,true,true).'</Description>';
			$xmlData [] = '<Url>www.deepbluedive.com</Url>'; // Limited to 1024 chars
			$xmlData [] = '<Taxable>'.$orderItem->GetTaxable().'</Taxable>'; // YES or NO
			$xmlData [] = '</Item>';
			$i++;
		} // End if package
	} // End foreach
	$xmlData [] = '<Total>';
	$xmlData [] = '<Line name="Shipping">';
	$xmlData [] = round($order->GetTotalPostage(),2);
	$xmlData [] = '</Line>';
	$xmlData [] = '<Line name="ShippingTax">';
	$xmlData [] = '0.00';
	$xmlData [] = '</Line>';
	$xmlData [] = '<Line name="Total">';
	$xmlData [] = $order->GetTotalPrice();
	$xmlData [] = '</Line>';
	$xmlData [] = '</Total>';
	$xmlData [] = '</Order>';
	$order->SetDownloaded(1);
	$order->SetDownloadDate(time());
}
$xmlData [] = '</OrderList>';


$fh = fopen('sync.xml','w+');
foreach($xmlData as $data) {
	fwrite($fh,$data);
}
//fwrite($fh,"Page was generated in ".sprintf("%01.7f",((getmicrotime()-$time)/1000))." seconds.");
fclose($fh);

/*foreach($xmlData as $data) {
	echo $data;	
}*/

?>