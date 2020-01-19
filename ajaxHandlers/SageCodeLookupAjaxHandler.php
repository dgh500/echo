<?php
include_once('../autoload.php');
$presentationHelper = new PresentationHelper;
$OIController = new OrderItemController;
$currentSimilar = $OIController->LookupProductBySageCode($_GET['sofar']);

$str = '';


$str  .= '
		<style>
			.sageCodeSuggestion { border: 1px solid #A5ACB2; border-top: 0px; width: 478px; position: relative; left: 72px;}
			.sageCodeSuggestion a { text-decoration: none; }
			.sageCodeSuggestion a:hover { text-decoration: underline; }
		</style>
		';
$str .= '<div class="sageCodeSuggestion">';

foreach($currentSimilar as $similarProduct) {
	// For the display
	if(trim($similarProduct['Attribute_Value'])=='') {
		$valueSect = ')';
	} else {
		$valueSect = ' - '.trim($similarProduct['Attribute_Value']).')';
	}
	// For the order form view
	if(trim($similarProduct['Attribute_Value'])=='') {
		$valueSect2 = '';
	} else {
		$valueSect2 = ' ('.trim($similarProduct['Attribute_Value']).')';
	}	
	$str .= '<a 
				id="SAGECODE'.$similarProduct['Sage_Code'].'PRICE'.$presentationHelper->Money($similarProduct['Price']).'" 
				class="'.$similarProduct['Display_Name'].' '.$valueSect2.'"
				>'.trim($similarProduct['Sage_Code']).' ('.trim($similarProduct['Display_Name']).$valueSect.'
			</a>
			<br />';	
}

$str .= '</div>';

echo $str;

?>