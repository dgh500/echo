<?php

require_once ('../autoload.php');

/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}*/

class AffiliateBackHandler {
	
	function __construct() {
		$this->mSessionHelper = new SessionHelper ( );
	}
	
	function Redirect() {
		$registry = Registry::getInstance ();
		$this->mSessionHelper->SetAffiliateStage ( false );
		$redirectTo = $registry->baseDir . '/affiliateArea';
		header ( 'Location: ' . $redirectTo );
	}

}

try {
	$handler = new AffiliateBackHandler ( );
	$handler->Redirect ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>