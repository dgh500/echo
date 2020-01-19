<?php

require_once ('../autoload.php');

/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}*/

class AdminLogoutHandler {
	
	var $mClean;
	
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mSessionHelper = new SessionHelper ( );
	}
	
	function Validate($postArr) {
		// null
	}
	
	function Login() {
		$registry = Registry::getInstance ();
		$adminHelper = new AdminHelper ( );
		$adminHelper->SetLoggedIn ( false );
		$redirectTo = $registry->adminDir . '/index.php';
		header ( 'Location: ' . $redirectTo );
	}

}

try {
	$handler = new AdminLogoutHandler ( );
	$handler->Validate ( $_POST );
	$handler->Login ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>