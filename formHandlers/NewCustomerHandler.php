<?php

require_once ('../autoload.php');

/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}*/

class NewCustomerHandler {
	
	var $mClean;
	
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mSessionHelper = new SessionHelper ( );
		$this->mBasket = $this->mSessionHelper->GetBasket ();
	}
	
	function Redirect() {
		$this->mSessionHelper->SetCheckoutStage ( 'registration' );
		$registry = Registry::getInstance ();
		$redirectTo = $registry->secureBaseDir . '/checkout.php';
		header ( 'Location: ' . $redirectTo );
	}

}

try {
	$handler = new NewCustomerHandler ( );
	$handler->Redirect ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>