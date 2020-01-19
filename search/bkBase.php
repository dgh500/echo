<?php

//! Makes the google base feed
class GoogleBaseHelper {
		
}

#header('Content-type: application/xml; charset="utf-8"',true);
// For use via the command line..
chdir ( dirname ( __FILE__ ) );
include ('../autoload.php');
$registry = Registry::getInstance ();
$catalogue = $registry->catalogue;
$validationHelper = new ValidationHelper ( );
$publicLayoutHelper = new PublicLayoutHelper ( );
$today = date ( 'Y-m-d' );

$sitemap [] = '<?xml version="1.0" encoding="iso-8859-1"?>';
$sitemap [] = '<feed xmlns="http://www.w3c.org/2005/Atom" xmlns:g="http://base.google.com/ns/1.0">';
$sitemap [] = '<g:item_type>products</g:item_type>';
$sitemap [] = '<author>';
$sitemap [] = '<name>' . $catalogue->GetDisplayName () . '</name>';
$sitemap [] = '<email>info@deepbluedive.com</email>';
$sitemap [] = '</author>';
$sitemap [] = '<title>' . $catalogue->GetDisplayName () . '</title>';

// Products
$productController = new ProductController ( );
$allProducts = $productController->GetAllProductsInCatalogue ( $catalogue );
foreach ( $allProducts as $product ) {
	$href = $publicLayoutHelper->LoadLinkHref ( $product );
	if ($product->GetManufacturer ()) {
		$brand = $product->GetManufacturer ()->GetDisplayName ();
		$brandId = $product->GetManufacturer ()->GetManufacturerId ();
	} else {
		$brand = 'NA';
		$brandId = 0;
	}
	if ($product->GetMainImage ()) {
		$image = $product->GetMainImage ();
		$imageHref = $publicLayoutHelper->ImageHref ( $image );
	} else {
		$imageHref = '';
	}
	$categories = $product->GetCategories ();
	$category = trim ( $categories [0]->GetDisplayName () );
	
	$sitemap [] = '<entry>
	';
	$sitemap [] = '<title>' . $validationHelper->MakeXmlSafe ( $product->GetDisplayName () ) . '</title>
	';
	$sitemap [] = '<g:brand>' . $validationHelper->MakeXmlSafe ( $brand, true ) . '</g:brand>
	';
	$sitemap [] = '<g:condition>new</g:condition>
	';
	#$sitemap[] = '<g:delivery_notes>Overnight</g:delivery_notes>
	#';
	#$sitemap[] = '<g:delivery_radius>1000.0km</g:delivery_radius>
	#';
	if ($product->GetDescription () != '' && $product->GetDescription () != ' ') {
		$sitemap [] = '<summary>' . $validationHelper->MakeXmlSafe ( $product->GetDescription (), true ) . '</summary>';
	} else {
		$sitemap [] = '<summary>' . $product->GetDisplayName () . '</summary>';
	}
	$sitemap [] = '<id>' . $product->GetProductId () . '</id>
	';
	$sitemap [] = '<g:image_link>' . $imageHref . '</g:image_link>
	';
	$sitemap [] = '<link href="' . $href . '" />
	';
	$sitemap [] = '<g:mpn>' . $brandId . '</g:mpn>
	';
	$sitemap [] = '<g:price>' . $product->GetActualPrice () . '</g:price>
	';
	$sitemap [] = '<g:product_type>Sporting Goods &gt; Water Sports &gt; Scuba Diving &amp; Snorkeling &gt; ' . $validationHelper->MakeXmlSafe ( $category, true ) . '</g:product_type>
	';
	$sitemap [] = '<g:quantity>1</g:quantity>
	';
	$sitemap [] = '<g:upc>0</g:upc>
	';
	$sitemap [] = '<g:weight>' . $product->GetWeight () . '</g:weight>
	';
	$sitemap [] = '</entry>
	';
}

$sitemap [] = '</feed>';

$fh = fopen ( $registry->GoogleBaseFile, 'w+' );
foreach ( $sitemap as $value ) {
	#echo $value;
	fwrite ( $fh, $value );
}
fclose ( $fh );

?>