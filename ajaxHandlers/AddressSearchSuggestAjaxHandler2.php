<?php
$disableJavascriptAutoload = 1;
include_once('../autoload.php');
include_once ('../controller/AddressController.php');
include_once ('../model/AddressModel.php');
include_once ('../helpers/ValidationHelper.php');

$content = '';
if (isset ( $_POST ['sofar'] ) && isset ( $_POST ['method'] )) {
	switch ($_POST['method']) {
		case 'postcode' :			
			$addressController = new AddressController ( );
			$validationHelper = new ValidationHelper ( );
			$arr = $addressController->SearchOnPostcode ( $_POST ['sofar'] );
			if (0 != count ( $arr )) {
				$i = 0;
				foreach ( $arr ['address'] as $address ) {
					$content .= '<a href="#" onclick="selectSuggestion(
							\''.$validationHelper->RemoveNasties($address->GetPostcode()).'\',
							\''.$validationHelper->RemoveNasties($address->GetAddressId()).'\',
							\''.$validationHelper->RemoveNasties($address->GetLine1()).'\',
							\''.$validationHelper->RemoveNasties($address->GetLine2()).'\',
							\''.$validationHelper->RemoveNasties($address->GetLine3()).'\',
							\''.$validationHelper->RemoveNasties($address->GetCounty()).'\',
							\''.$validationHelper->RemoveNasties($address->GetPostcode()).'\',
							\''.$validationHelper->RemoveNasties($arr['name'][$i]).'\',
							\''.$arr['email'][$i].'\',
							\''.$validationHelper->RemoveNasties($arr['phone'][$i]).'\')">
									'.$validationHelper->RemoveNasties($address->GetPostcode()).' ('.$validationHelper->RemoveNasties($address->GetLine1()).' ...)
								</a><br>';
					$i ++;
				}
			}
			break;
	}
}
echo $content;

?>
