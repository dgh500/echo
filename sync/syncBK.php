<?php
set_time_limit ( 600 );
header ( 'Content-Type: text/xml' );
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

$orderController = new OrderController ( );
$validationHelper = new ValidationHelper ( );
$moneyHelper = new MoneyHelper ( );
$allNotDownloaded = $orderController->GetNotDownloaded();

$xmlData [] = '<?xml version="1.0" encoding="UTF-8"?>';
$xmlData [] = '<OrderList StoreAccountName="Deep Blue Dive">';
foreach($allNotDownloaded as $order) {
	$customer = $order->GetCustomer ();
	$shippingAddr = $order->GetShippingAddress ();
	$billingAddr = $order->GetBillingAddress ();
	$xmlData [] = '<Order id="ECHO' . $order->GetOrderId () . '" currency="GBP">';
	$xmlData [] = '<Time>' . $gmt . '</Time>';
	$xmlData [] = '<ShipTime>' . $gmt . '</ShipTime>';
	$xmlData [] = '<NumericTime>' . $now . '</NumericTime>';
	$xmlData [] = '<AddressInfo type="ship">';
	$xmlData [] = '<Name>';
	$xmlData [] = '<First>' . $validationHelper->MakeLinkSafe ( $customer->GetFirstName (), true ) . '</First>';
	$xmlData [] = '<Last>' . $validationHelper->MakeLinkSafe ( $customer->GetLastName (), true ) . '</Last>';
	$xmlData [] = '<Full>' . $validationHelper->MakeLinkSafe ( $customer->GetFirstName (), true ) . ' ' . $validationHelper->MakeLinkSafe ( $customer->GetLastName (), true ) . '</Full>';
	$xmlData [] = '</Name>';
	$xmlData [] = '<Company>' . $validationHelper->MakeLinkSafe ( $shippingAddr->GetCompany (), true ) . '</Company>';
	$xmlData [] = '<Address1>' . $validationHelper->MakeLinkSafe ( $shippingAddr->GetLine1 (), true ) . '</Address1>';
	$xmlData [] = '<Address2>' . $validationHelper->MakeLinkSafe ( $shippingAddr->GetLine2 (), true ) . '</Address2>';
	$xmlData [] = '<City>' . $validationHelper->MakeLinkSafe ( $shippingAddr->GetLine3 (), true ) . '</City>';
	$xmlData [] = '<State>' . $validationHelper->MakeLinkSafe ( $shippingAddr->GetCounty (), true ) . '</State>';
	$xmlData [] = '<Country>United Kingdom</Country>';
	$xmlData [] = '<Zip>' . $validationHelper->MakeLinkSafe ( $shippingAddr->GetPostcode (), true ) . '</Zip>';
	$xmlData [] = '<Phone>' . trim ( $customer->GetDaytimeTelephone () ) . '</Phone>';
	$xmlData [] = '</AddressInfo>';
	$xmlData [] = '<AddressInfo type="bill">';
	$xmlData [] = '<Name>';
	$xmlData [] = '<First>' . $validationHelper->MakeLinkSafe ( $customer->GetFirstName (), true ) . '</First>';
	$xmlData [] = '<Last>' . $validationHelper->MakeLinkSafe ( $customer->GetLastName (), true ) . '</Last>';
	$xmlData [] = '<Full>' . $validationHelper->MakeLinkSafe ( $customer->GetFirstName (), true ) . ' ' . $validationHelper->MakeLinkSafe ( $customer->GetLastName (), true ) . '</Full>';
	$xmlData [] = '</Name>';
	$xmlData [] = '<Company>' . $validationHelper->MakeLinkSafe ( $billingAddr->GetCompany (), true ) . '</Company>';
	$xmlData [] = '<Address1>' . $validationHelper->MakeLinkSafe ( $billingAddr->GetLine1 (), true ) . '</Address1>';
	$xmlData [] = '<Address2>' . $validationHelper->MakeLinkSafe ( $billingAddr->GetLine2 (), true ) . '</Address2>';
	$xmlData [] = '<City>' . $validationHelper->MakeLinkSafe ( $billingAddr->GetLine3 (), true ) . '</City>';
	$xmlData [] = '<State>' . $validationHelper->MakeLinkSafe ( $billingAddr->GetCounty (), true ) . '</State>';
	$xmlData [] = '<Country>United Kingdom</Country>';
	$xmlData [] = '<Zip>' . $validationHelper->MakeLinkSafe ( $billingAddr->GetPostcode (), true ) . '</Zip>';
	$xmlData [] = '<Phone>' . trim ( $customer->GetDaytimeTelephone () ) . '</Phone>';
	$xmlData [] = '</AddressInfo>';
	$xmlData [] = '<Shipping />';
	$xmlData [] = '<CreditCard expiration="10/10" type="cc" />';
	// All shipped items
	$allShipped = $order->GetShippedItems ();
	$i = 0;
	$fixPrice = 0;
	$basket = $order->GetBasket ();
	foreach ( $allShipped as $sku ) {
		$fixVat = false;
		if ($sku->GetParentProduct ()) {
			if ($sku->GetSageCode () == ' ') {
				$sageCode = 'DUMMYSAGECODE';
			} else {
				$sageCode = trim ( $sku->GetSageCode () );
			}
			$xmlData [] = '<Item num="' . $i . '">';
			$xmlData [] = '<Id>' . $sku->GetSkuId () . '</Id>'; // Limited to 255 chars
			$xmlData [] = '<Code>' . $sageCode . '</Code>';
			$xmlData [] = '<Quantity>1</Quantity>'; // Limited to 9 digits
			if ($basket->IsPackage ( $sku ) || $basket->IsPackageUpgrade ( $sku )) {
				try {
					$product = $sku->GetParentProduct ();
					$packages = $basket->GetPackages ();
					foreach ( $packages as $package ) {
						$numberOfItems = count ( $package->GetContents () );
						if ($package->IsPart ( $product )) {
							$unitPrice = round ( $order->GetBasket ()->GetOverruledPackagePrice ( $package ) / $numberOfItems, 2 );
						}
						if ($order->GetBasket ()->IsPackageUpgrade ( $sku )) {
							$unitPrice = round ( $order->GetBasket ()->GetOverruledPackagePrice ( $package ) / $numberOfItems, 2 );
							$unitPrice = $unitPrice + $order->GetBasket ()->GetOverruledSkuPrice ( $sku, false, true );
						}
					}
				} catch ( Exception $e ) {
					// Do Nothing	
				}
			} else {
				if ($order->GetBasket ()->HasOverruledSku ( $sku )) {
					$unitPrice = round ( $order->GetBasket ()->GetOverruledSkuPrice ( $sku ), 2 );
				} else {
					$unitPrice = round ( $sku->GetSkuPrice (), 2 );
				}
			}
			if (intval ( $unitPrice ) == 0) {
				$unitPrice = 1.00;
				$fixPrice += 1;
				$fixVat = true;
			}
			if ($order->GetShippingAddress ()->GetCountry ()->IsVatFree ()) {
				round ( $unitPrice = $moneyHelper->RemoveVAT ( $unitPrice ), 2 );
			}
			$xmlData [] = '<Unit-Price>' . $unitPrice . '</Unit-Price>';
			if (intval ( $sku->GetParentProduct ()->GetTaxCode ()->GetRate () ) == 0 || $fixVat) {
				$xmlData [] = '<Tax>0.00</Tax>';
			} else {
				$xmlData [] = '<Tax>' . $moneyHelper->VAT ( $unitPrice ) . '</Tax>';
			}
			$xmlData [] = '<Shipping>0.00</Shipping>';
			$xmlData [] = '<Shipping-Tax>0.00</Shipping-Tax>';
			$xmlData [] = '<Description>' . $validationHelper->MakeLinkSafe ( $sku->GetParentProduct ()->GetDisplayName (), true ) . '</Description>';
			$xmlData [] = '<Url>www.deepbluedive.com</Url>'; // Limited to 1024 chars
			if (intval ( $sku->GetParentProduct ()->GetTaxCode ()->GetRate () ) == 0 || $fixVat) {
				$xmlData [] = '<Taxable>NO</Taxable>'; // YES or NO
			} else {
				$xmlData [] = '<Taxable>YES</Taxable>'; // YES or NO
			}
			$xmlData [] = '</Item>';
			$i ++;
		} // End if can get parent product
	}
	$xmlData [] = '<Total>';
	$xmlData [] = '<Line name="Shipping">';
	$xmlData [] = round ( $order->GetTotalPostage (), 2 );
	$xmlData [] = '</Line>';
	$xmlData [] = '<Line name="ShippingTax">';
	$xmlData [] = '0.00';
	$xmlData [] = '</Line>';
	$xmlData [] = '<Line name="Total">';
	$xmlData [] = $order->GetTotalPrice () + $fixPrice;
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
/*
foreach($xmlData as $data) {
	echo $data;	
}*/

?>