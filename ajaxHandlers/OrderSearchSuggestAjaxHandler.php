<?php
header ( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); // Date in the past
header ( "Last-Modified: " . gmdate ( "D, d M Y H:i:s" ) . " GMT" );
header ( "Cache-Control: no-cache, must-revalidate" ); // HTTP/1.1
header ( "Pragma: no-cache" );
header ( "content-type: text/xml" );

$disableJavascriptAutoload = 1;

include_once ('../controller/OrderController.php');
include_once ('../model/OrderModel.php');

$content = '';

if (isset ( $_GET ['sofar'] ) && isset ( $_GET ['method'] )) {
	switch ($_GET ['method']) {
		case 'orderNumber' :
			$orderController = new OrderController ( );
			$orders = $orderController->SearchOnOrderId ( $_GET ['sofar'] );
			$content .= '
						<root>
							<who>OrderSuggest</who>
							<sofar>' . $_GET ['sofar'] . '</sofar>';
			foreach ( $orders as $order ) {
				$content .= '<order>' . $order->GetOrderId () . '</order>';
				$content .= '<orderId>' . $order->GetOrderId () . '</orderId>';
				$content .= '<created>' . date ( 'd/m/Y', $order->GetCreatedDate () ) . '</created>';
			}
			$content .= '</root>';
			break;
		case 'postcode' :
			$content .= '<root><who>OrderSuggest</who>';
			
			$orderController = new OrderController ( );
			$arr = $orderController->SearchOnPostcode ( $_GET ['sofar'] );
			if (0 != count ( $arr )) {
				$content .= '<sofar>' . $_GET ['sofar'] . '</sofar>';
				foreach ( $arr ['address'] as $address ) {
					$content .= '<postcode>' . $address->GetPostcode () . '</postcode>';
				}
				foreach ( $arr ['order'] as $order ) {
					$content .= '<created>' . date ( 'd/m/Y', $order->GetCreatedDate () ) . '</created>';
					$content .= '<orderId>' . $order->GetOrderId () . '</orderId>';
				}
			}
			$content .= '</root>';
			break;
		case 'lastName' :
			$content .= '<root><who>OrderSuggest</who>';
			$orderController = new OrderController ( );
			$arr = $orderController->SearchOnLastName ( $_GET ['sofar'] );
			$content .= '<sofar>' . $_GET ['sofar'] . '</sofar>';
			if (0 != count ( $arr )) {
				foreach ( $arr ['customer'] as $customer ) {
					$content .= '<firstName>' . $customer->GetFirstName () . '</firstName>';
					$content .= '<lastName>' . $customer->GetLastName () . '</lastName>';
				}
				foreach ( $arr ['order'] as $order ) {
					$content .= '<created>' . date ( 'd/m/Y', $order->GetCreatedDate () ) . '</created>';
					$content .= '<orderId>' . $order->GetOrderId () . '</orderId>';
				}
			}
			$content .= '</root>';
			break;
	}
}
echo $content;

?>