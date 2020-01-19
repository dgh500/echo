<?php
session_start();
include_once('../autoload.php');

$productController 	= new ProductController;
$basket				= new BasketModel(session_id()); 

$product = $productController->CreateProduct();
$product->SetDisplayName($_POST['display_name']);
$product->SetActualPrice($_POST['actual_price']);
$skus = $product->GetSkus();
$sku  = $skus[0];
$basket->AddToBasket($sku,false,$_POST['actual_price'],false,false);
$_SESSION['miscProductsToDelete'][] = $product->GetProductId();

?>