<?php
include_once('../autoload.php');
$order = new OrderModel($_POST['orderId']);
$order->SetTotalPostage(0);

?>