<?php

// Get DB Access
include('autoload.php');

$str = '<?php $dictionary = array( ';
$sql = "SELECT Product_ID FROM tblProduct WHERE Hidden = '0'";
$result = $registry->database->query($sql);
$products = $result->fetchAll(PDO::FETCH_OBJ);
foreach($products as $product_id) {
	$newProduct = new ProductModel ( $product_id->Product_ID );
	$str .= '"'.$newProduct->GetDisplayName().'"=>"'.$newProduct->GetProductId().'",';
}
$str = substr($str,0,strlen($str)-1);
$str .= '); ?>';


$fh = fopen('dictionary.php','w+');
fwrite($fh,$str);
fclose($fh);


?>