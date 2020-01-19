<?php

require_once ('../autoload.php');

/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}*/

class AffiliatePreRegisterHandler {
	
	var $mClean;
	
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mSessionHelper = new SessionHelper ( );
	}
	
	function Redirect() {
		$this->mSessionHelper->SetAffiliateStage ( 'registration' );
		$registry = Registry::getInstance ();
		$redirectTo = $registry->baseDir . '/affiliateArea';
		header ( 'Location: ' . $redirectTo );
	}

}

try {
	$handler = new AffiliatePreRegisterHandler ( );
	$handler->Redirect ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>