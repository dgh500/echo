<?php

require_once ('../autoload.php');

class AffiliateLogoutHandler {
	
	var $mClean;
	
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mSessionHelper = new SessionHelper ( );
	}
	
	function Logout() {
		$registry = Registry::getInstance ();
		$this->mSessionHelper->SetAffiliateStage ( false );
		$this->mSessionHelper->SetAffiliate ( false );
		$redirectTo = $registry->baseDir . '/affiliates';
		header ( 'Location: ' . $redirectTo );
	}

}

try {
	$handler = new AffiliateLogoutHandler ( );
	$handler->Logout ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>