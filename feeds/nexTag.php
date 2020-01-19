<?php
// Include all the classes (but don't include the javascript autoload - so that the javascript include doesnt come before the XML start)
$disableJavascriptAutoload = 1;
include_once('../autoload.php');

// Debug
$fh = fopen('nextagDebug.txt','w+');

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

// Start the columns
$dataFeed = "MPN,Manufacturer Name, UPC, Product Name, Product Description, Product Price, Product URL, Image URL, Dealtime.co.uk Categorisation, Stock Availability, Stock Description, Standard Shipping, Weight\n";

// Get all products
foreach($productController->GetAllProductsInCatalogue($registry->catalogue) as $product) {
	
	// MPN - Use Internal
	($product->GetManufacturer() ? $mpn = $product->GetManufacturer()->GetManufacturerId() : 0 );
	
	// Manufacturer Name
	($product->GetManufacturer() ? $manufacturerName = trim($product->GetManufacturer()->GetDisplayName()) : $manufacturerName = 'NO BRAND' );
	
	// UPC - Use Internal
	$upc = $product->GetProductId();
	
	// Product Name
	$productName = $validationHelper->MakeXmlSafe($product->GetDisplayName(),true);
	
	// Product Description
	$productDescription = $validationHelper->MakeXmlSafe($product->GetDescription(),true);
	
	// Price
	$productPrice = $presentationHelper->Money($product->GetActualPrice());
	
	// Product URL
	$productUrl = $publicLayoutHelper->LoadLinkHref($product);
	
	// Image URL
	$imageUrl = $publicLayoutHelper->ImageHref($product->GetMainImage());
	
	// Dealtime.co.uk Categorisation
	$categorisation = 'UK / Health & Beauty / Health / Vitamins & Nutrition';
	
	// Product Weight
	$productWeight = $product->GetWeight();
	
	// Make the feed
	$dataFeed .= $mpn.",";
	$dataFeed .= $manufacturerName.",";
	$dataFeed .= $upc.",";
	$dataFeed .= $productName.",";
	$dataFeed .= $productDescription.",";
	$dataFeed .= $productPrice.",";
	$dataFeed .= $productUrl.",";
	$dataFeed .= $imageUrl.",";
	$dataFeed .= $categorisation.",";
	$dataFeed .= "Y,";
	$dataFeed .= "Ships Next Day,";
	$dataFeed .= "0,";
	$dataFeed .= $productWeight."";
	$dataFeed .= "\n";	
}


// Echo the XML to the file
$csvFH = fopen('nexTag.csv','w');
fwrite($csvFH,$dataFeed);
fclose($csvFH);

// End time
$endTime = $timerHelper->GetTime();

// Debug File time
fwrite($fh,"Page was generated in ".$timerHelper->Difference($startTime,$endTime)." seconds.");

// Close debug file
fclose($fh);

?>