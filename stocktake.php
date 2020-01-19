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

// And a random var (weak security measure)
if(isset($_GET['longwalk']) && $_GET['longwalk'] == '5.14m') {

	// Page Header
	echo '<h1>'.$manufacturer->GetDisplayName().'</h1>';
	// Table Start
	echo '<table>';
	// Header Row
	echo '<tr><th>ACTUAL</th><th>QTY</th><th>PRODUCT</th></tr>';

	// Loop over products and display prices
	foreach($mController->GetAllSkusIn($manufacturer) as $sku) {
		echo '<tr><td style="width: 100px">&nbsp;</td><td align="center">'.$sku->GetQty().'</td><td>'.$sku->GetParentProduct()->GetDisplayName().' '.$sku->GetSkuAttributesList().'</td></tr>';
	} // End foreach

	// Table End
	echo '</table>';

}
?>