<?php

if(isset($_GET['orderId'])) {
	echo 'order '.$_GET['orderId'];
} else {
	echo 'no order';
}

?>