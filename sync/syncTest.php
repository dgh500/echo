<?php

header ( 'Content-Type: text/xml' );
$disableJavascriptAutoload = 1;
include ('../autoload.php');

date_default_timezone_set ( 'GMT' );
$now = time ();
$gmt = date ( 'd F Y H:i:s', $now );

$orderController = new OrderController ( );
$validationHelper = new ValidationHelper ( );
$moneyHelper = new MoneyHelper ( );

if (isset ( $_GET ['o'] )) {
	$order = new OrderModel ( $_GET ['o'] );
} else {
	echo 'Need an order ID';
}

$xmlData [] = '<OrderList>';
$customer = $order->GetCustomer ();
$shippingAddr = $order->GetShippingAddress ();
$billingAddr = $order->GetBillingAddress ();
$xmlData [] = '<Order id="ECHO' . $order->GetOrderId () . '">';
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
$xmlData [] = '<CreditCard />';
// All shipped items
$allShipped = $order->GetShippedItems ();
$i = 0;
$basket = $order->GetBasket ();
foreach ( $allShipped as $sku ) {
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
		$product = $sku->GetParentProduct ();
		$packages = $basket->GetPackages ();
		foreach ( $packages as $package ) {
			$numberOfItems = count ( $package->GetContents () );
			if ($package->IsPart ( $product )) {
				$unitPrice = round ( $order->GetBasket ()->GetOverruledPackagePrice ( $package ) / $numberOfItems, 2 );
			}
			if ($order->GetBasket ()->IsPackageUpgrade ( $sku )) {
				$unitPrice = round ( $order->GetBasket ()->GetOverruledPackagePrice ( $package ) / $numberOfItems, 2 );
				$unitPrice += $order->GetBasket ()->GetOverruledSkuPrice ( $sku, false, true );
			}
		}
	} else {
		if ($order->GetBasket ()->HasOverruledSku ( $sku )) {
			$unitPrice = round ( $order->GetBasket ()->GetOverruledSkuPrice ( $sku ), 2 );
		} else {
			$unitPrice = round ( $sku->GetSkuPrice (), 2 );
		}
	}
	if ($order->GetShippingAddress ()->GetCountry ()->IsVatFree ()) {
		round ( $unitPrice = $moneyHelper->RemoveVAT ( $unitPrice ), 2 );
	}
	$xmlData [] = '<Unit-Price>' . $unitPrice . '</Unit-Price>';
	$xmlData [] = '<Url />'; // Limited to 1024 chars
	if (intval ( $sku->GetParentProduct ()->GetTaxCode ()->GetRate () ) == 0) {
		$xmlData [] = '<Taxable>NO</Taxable>'; // YES or NO
		$xmlData [] = '<Tax>0.00</Tax>';
	} else {
		$xmlData [] = '<Taxable>YES</Taxable>'; // YES or NO
		$xmlData [] = '<Tax>' . $moneyHelper->VAT ( $unitPrice ) . '</Tax>';
	}
	$xmlData [] = '<Description>' . $validationHelper->MakeLinkSafe ( $sku->GetParentProduct ()->GetDisplayName (), true ) . '</Description>';
	$xmlData [] = '<Shipping>0.00</Shipping>';
	$xmlData [] = '<Shipping-Tax>0.00</Shipping-Tax>';
	$xmlData [] = '</Item>';
	$xmlData [] = '<Total>';
	$xmlData [] = '<Line name="Shipping">';
	$xmlData [] = round ( $order->GetTotalPostage (), 2 );
	$xmlData [] = '</Line>';
	$xmlData [] = '<Line name="ShippingTax">';
	$xmlData [] = '0.00';
	$xmlData [] = '</Line>';
	$xmlData [] = '<Line name="Total">';
	$xmlData [] = $unitPrice;
	$xmlData [] = '</Line>';
	$xmlData [] = '</Total>';
	$i ++;
}
$xmlData [] = '</Order>';
$xmlData [] = '</OrderList>';

foreach ( $xmlData as $data ) {
	echo ($data);
}

?>