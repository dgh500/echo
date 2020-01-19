<?php

include ('../autoload.php');
$registry = Registry::getInstance ();
$catalogue = $registry->catalogue;
$validationHelper = new ValidationHelper ( );
$publicLayoutHelper = new PublicLayoutHelper ( $registry->baseDir );

$productFeed [] = '<?xml version="1.0" encoding="iso-8859-1"?>';
$productFeed [] = '<Catalog>';

// Products
$productController = new ProductController ( );
$allProducts = $productController->GetAllProductsInCatalogue ( $catalogue );
foreach ( $allProducts as $product ) {
	$productFeed [] = '<Product>';
	$productFeed [] = '<DisplayName>' . $validationHelper->MakeXmlSafe ( $product->GetDisplayName () ) . '</DisplayName>';
	$productFeed [] = '<ProductId>' . $validationHelper->MakeXmlSafe ( $product->GetProductId () ) . '</ProductId>';
	$productFeed [] = '</Product>';
}
$productFeed [] = '</Catalog>';

$fh = fopen ( 'productFeed.xml', 'w+' );
foreach ( $productFeed as $value ) {
	#echo $value;
	fwrite ( $fh, $value );
}
fclose ( $fh );

?>