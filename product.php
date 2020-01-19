<?php

require_once ('autoload.php');

try {
	if(isset($_GET['sku'])) {
		$sku = new SkuModel($_GET['productId']);
		$productId = $sku->GetParentProduct()->GetProductId();
	} else {
		$productId = $_GET['productId'];
	}
	$productPage = new ProductView ( $productId );
	echo $productPage->LoadDefault ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>
