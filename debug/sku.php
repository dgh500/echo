<?php

include('../autoload.php');

if(isset($_GET['sku'])) {
	$sku = new SkuModel($_GET['sku']);
	echo $sku->GetParentProduct()->GetDisplayName().' '.$sku->GetAttributeList();
	
}

?>