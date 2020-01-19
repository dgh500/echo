<?php

require_once ('../autoload.php');

/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}*/

class BackHandler {
	
	function __construct() {
		$this->mSessionHelper = new SessionHelper ( );
	}
	
	function Redirect() {
		$registry = Registry::getInstance ();
		$this->mSessionHelper->SetCheckoutStage ( false );
		$registry = Registry::getInstance ();
		$secureBaseDir = $registry->secureBaseDir;
		$sendTo = $secureBaseDir . '/checkout.php';
		header ( 'Location: ' . $sendTo );
	}

}

try {
	$handler = new BackHandler ( );
	$handler->Redirect ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>