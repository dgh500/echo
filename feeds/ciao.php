<?php
// Include all the classes (but don't include the javascript autoload - so that the javascript include doesnt come before the XML start)
$disableJavascriptAutoload = 1;
include_once('../autoload.php');

// Debug
$fh = fopen('ciaoDebug.txt','w+');

// Helpers needed
$timerHelper 		= new TimerHelper;			// For debugging
$presentationHelper	= new PresentationHelper;	// To make the price the correct format
$validationHelper 	= new ValidationHelper;		// To make it XML safe
$publicLayoutHelper = new PublicLayoutHelper; 	// To get the product link

// Controllers Needed
$productController	= new ProductController;

// Start time
$startTime = $timerHelper->GetTime();

// Give us some time to execute
set_time_limit(600);

// Tell us what the MIME type is
header('Content-Type: text/xml');

// Start the XML
$xmlData[] = '<?xml version="1.0" encoding="UTF-8"?>';
$xmlData[] = '<productFeed>';

// Get all products
foreach($productController->GetAllProductsInCatalogue($registry->catalogue) as $product) {
	// Start Product
	$xmlData[] = '<product>';
	
	// Brand
	($product->GetManufacturer() ? $brand = trim($product->GetManufacturer()->GetDisplayName()) : $brand = 'NO BRAND' );
	// Link
	$link = $publicLayoutHelper->LoadLinkHref($product);
	// Price
	$price = $presentationHelper->Money($product->GetActualPrice());
	// Category
	$categories = $product->GetCategories();
	$category 	= $categories[0]->GetDirectoryPath();
	// Image Link
	$imageLink = $publicLayoutHelper->ImageHref($product->GetMainImage());
	
	// Required fields
	$xmlData[] = '<name>'	 			.$validationHelper->MakeXmlSafe(	$product->GetDisplayName()	,true).'</name>';
	$xmlData[] = '<brand>'	 			.$validationHelper->MakeXmlSafe(	$brand						,true).'</brand>';
	$xmlData[] = '<deeplink>'			.$validationHelper->MakeXmlSafe(	$link						).'</deeplink>';
	$xmlData[] = '<price>'	 			.$validationHelper->MakeXmlSafe(	$price						,true).'</price>';	
	$xmlData[] = '<merchantcategory>'	.$validationHelper->MakeXmlSafe(	$category					,true).'</merchantcategory>';	
	
	// Optional fields
	$xmlData[] = '<imageLink>'			.$validationHelper->MakeXmlSafe(	$imageLink						).'</imageLink>';
	$xmlData[] = '<description>'		.$validationHelper->MakeXmlSafe(	$product->GetDescription()	,true).'</description>';
	$xmlData[] = '<shippingcost>0.00 GBP</shippingcost>';	
	$xmlData[] = '<delivery>In Stock - 24 hour delivery</delivery>';	
	$xmlData[] = '<currency>GBP</currency>';	
	
	// End Product
	$xmlData[] = '</product>';
}
$xmlData[] = '</productFeed>';

// Echo the XML to the screen
foreach($xmlData as $data) { echo $data; }

// End time
$endTime = $timerHelper->GetTime();

// Debug File time
fwrite($fh,"Page was generated in ".$timerHelper->Difference($startTime,$endTime)." seconds.");

// Close debug file
fclose($fh);

?>