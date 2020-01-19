<?php
header ( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // Date in the past
header ( "Last-Modified: " . gmdate ( "D, d M Y H:i:s" ) . " GMT" );
header ( "Cache-Control: no-cache, must-revalidate" ); // HTTP/1.1
header ( "Pragma: no-cache" );
header ( "content-type: text/xml" );
$disableJavascriptAutoload = 1;
include_once('../autoload.php');
include_once ('../controller/AddressController.php');
include_once ('../model/AddressModel.php');
include_once ('../helpers/ValidationHelper.php');

$content = '';

if (isset ( $_GET ['sofar'] ) && isset ( $_GET ['method'] )) {
	switch ($_GET ['method']) {
		case 'postcode' :
			$content .= '<root><who>AddressSuggest</who>';
			
			$addressController = new AddressController ( );
			$validationHelper = new ValidationHelper ( );
			$arr = $addressController->SearchOnPostcode ( $_GET ['sofar'] );
			if (0 != count ( $arr )) {
				$content .= '<sofar>' . $_GET ['sofar'] . '</sofar>';
				$i = 0;
				foreach ( $arr ['address'] as $address ) {
					$content .= '<addressId>' . $validationHelper->RemoveNasties ( $address->GetAddressId () ) . '</addressId>';
					$content .= '<postcode>' . $validationHelper->RemoveNasties ( $address->GetPostcode () ) . '</postcode>';
					$content .= '<line1>' . $validationHelper->RemoveNasties ( $address->GetLine1 () ) . ' </line1>';
					$content .= '<line2>' . $validationHelper->RemoveNasties ( $address->GetLine2 () ) . ' </line2>';
					$content .= '<line3>' . $validationHelper->RemoveNasties ( $address->GetLine3 () ) . ' </line3>';
					$content .= '<county>' . $validationHelper->RemoveNasties ( $address->GetCounty () ) . ' </county>';
					$content .= '<customerName>' . $validationHelper->RemoveNasties ( $arr ['name'] [$i] ) . ' </customerName>';
					$content .= '<customerEmail>' . $validationHelper->RemoveNasties ( $arr ['email'] [$i],true ) . ' </customerEmail>';
					$content .= '<customerPhone>' . $validationHelper->RemoveNasties ( $arr ['phone'] [$i] ) . ' </customerPhone>';
					$i ++;
				}
			}
			$content .= '</root>';
			break;
	}
}
echo $content;

?>