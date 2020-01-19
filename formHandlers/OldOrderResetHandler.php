<?php

require_once ('../autoload.php');

/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}*/

class OldOrderResetHandler {
	
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
	}
	
	function Process($postArr) {
		$orderId = $postArr ['resetOrderId'];
		try {
			$order = new OrderModel ( $orderId );
			$order->SetDownloaded ( 0 );
			echo 'Order ' . $postArr ['resetOrderId'] . ' has been reset.';
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	}
}

try {
	$handler = new OldOrderResetHandler ( );
	$handler->Process ( $_POST );
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>