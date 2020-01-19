<?php
// Expects a POST call with sageCodeP, displayNameP, orderItemP and priceP as $_POST variables, will update the correct order item ID with the new values
include_once('../autoload.php');

$orderItem = new OrderItemModel($_POST['orderItemP']);
$orderItem->SetDisplayName($_POST['displayNameP']);
$orderItem->SetPrice($_POST['priceP']);
$orderItem->SetSageCode($_POST['sageCodeP']);

?>