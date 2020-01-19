<?php

// Make the page styles OK
echo '
	<style>
		html {
			font-family: Arial, Sans-Serif;
			font-size: 10pt;
		}
		table {
			border-collapse: collapse;
		}
		td {
			border: 1px solid #000;
			padding: 5px;
			font-size: 10pt;
		}
		h1 {
			text-decoration: underline;
		}
	</style>
	';

// Get models etc.
include('autoload.php');

// We need a manufacturer
$manufacturer = new ManufacturerModel($_REQUEST['q']);

// And a controller
$mController = new ManufacturerController;

// Page Header
echo '<h1>'.$manufacturer->GetDisplayName().'</h1>';
// Table Start
echo '<table>';

// Loop over products and display prices
foreach($mController->GetProductsIn($manufacturer,100,'Display_Name') as $product) {
	echo '<tr><td>&pound;'.$product->GetActualPrice().'</td><td>'.$product->GetDisplayName().'</td></tr>';
} // End foreach

// Table End
echo '</table>';

?>