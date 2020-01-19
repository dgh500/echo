<?php
set_time_limit ( 500 );
include ('autoload.php');

$productController = new ProductController ( );
$catalogue = new CatalogueModel ( 120 );

$allProducts = $productController->GetAllProducts ( $catalogue );

foreach ( $allProducts as $product ) {
	if ($product->GetMainImage ()) {
		$product->GetMainImage ()->SetAltText ( $product->GetDisplayName () );
	}
}

?>