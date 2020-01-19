<?php

require_once ('../autoload.php');

class LogoutHandler {
	
	var $mClean;
	
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mSessionHelper = new SessionHelper ( );
	}
	
	function Logout() {
		$registry = Registry::getInstance ();
		$this->mSessionHelper->SetLoginStage ( 'logggedOut' );
		$this->mSessionHelper->SetCustomer ( false );
		$redirectTo = $registry->baseDir . '/account';
		header ( 'Location: ' . $redirectTo );
	}

}

try {
	$handler = new LogoutHandler ( );
	$handler->Logout ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>