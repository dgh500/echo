<?php

require_once ('../autoload.php');

//! Used as a 'fake' form handler when a user clicks a link to activate a form (as opposed to a submit button)
class AccountForgotPasswordHandler extends Handler {
	
	//! Calls parent constructor to access directories
	function __construct() {
		parent::__construct ();
	}
	
	//! Redirects the user to /account/forgottenPassword
	function Process() {
		$redirectTo = $this->mBaseDir . '/account/forgottenPassword';
		header ( 'Location: ' . $redirectTo );
	}
} // End AccountForgotPasswordHandler()


try {
	$handler = new AccountForgotPasswordHandler ( );
	$handler->Process ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>