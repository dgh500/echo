<?php
set_time_limit ( 600 );
include('autoload.php');

$orderController = new OrderController;
/*foreach($orderController->GetAuthorisedOrders() as $order) {
	$order->ConvertBasketIntoOrder();
	$catalogue = $order->GetBasket()->GetCatalogue();
	$order->SetCatalogue($catalogue);
}*/

foreach($orderController->GetOrders('113,117,118,120,122', 1241240400, 1243947796) as $order) {
	$order->ConvertBasketIntoOrder();
	$catalogue = $order->GetBasket()->GetCatalogue();
	$order->SetCatalogue($catalogue);
}



?>