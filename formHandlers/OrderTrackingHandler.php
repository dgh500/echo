<?php

require_once ('../autoload.php');

/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}*/

class OrderTrackingHandler {

	var $mClean;

	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mSessionHelper = new SessionHelper ( );
	}

	function Validate($postArr) {
		$this->mClean ['trackEmail'] = $this->mValidationHelper->MakeSafe ( $postArr ['trackEmail'] );
		if (strtolower ( $postArr ['trackOrderId'] [0] ) == 'h') {
			$this->mClean ['trackOrderId'] = substr ( $this->mValidationHelper->MakeSafe ( $postArr ['trackOrderId'] ), 3, strlen ( $this->mValidationHelper->MakeSafe ( $postArr ['trackOrderId'] ) ) );
		} else {
			$this->mClean ['trackOrderId'] = $this->mValidationHelper->MakeSafe ( $postArr ['trackOrderId'] );
		}
		// Check it exists with right customer
		try {
			$order = new OrderModel ( $this->mClean ['trackOrderId'] );
			$customer = new CustomerModel ( $this->mClean ['trackEmail'] );
			if ($order->GetCustomer ()->GetCustomerId () == $customer->GetCustomerId ()) {
				$this->mFailure = false;
			} else {
				#var_dump($customer->GetCustomerId()); var_dump($order->GetCustomer()->GetCustomerId ());die();
				$this->mFailure = true;
			}
		} catch ( Exception $e ) {
			$this->mFailure = true;
		}
	}

	function Redirect() {
		$registry = Registry::getInstance ();
		if ($this->mFailure) {
			$this->mSessionHelper->SetTrackingStatus ( 'failure' );
		} else {
			$this->mSessionHelper->SetTrackingId ( $this->mClean ['trackOrderId'] );
			$this->mSessionHelper->SetTrackingStatus ( 'success' );
		}
		$redirectTo = $registry->baseDir . '/orderTracking';
		header ( 'Location: ' . $redirectTo );
	}

}

try {
	$handler = new OrderTrackingHandler ( );
	$handler->Validate ( $_POST );
	$handler->Redirect ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>