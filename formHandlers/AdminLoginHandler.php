<?php

require_once ('../autoload.php');
/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}*/

class AdminLoginHandler {

	var $mClean;

	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mSessionHelper = new SessionHelper ( );
	}

	function Validate($postArr) {
		$this->mClean ['loginName'] = $this->mValidationHelper->MakeSafe ( $postArr ['loginName'] );
		$this->mClean ['loginPassword'] = $this->mValidationHelper->MakeSafe ( $postArr ['loginPassword'] );
		$this->mClean['secure'] = $this->mValidationHelper->MakeSafe ( $postArr ['secure'] );
	}

	function Login() {
		$registry = Registry::getInstance ();
		$adminHelper = new AdminHelper ( );
		if ($this->mClean ['loginName'] == 'echo' && $this->mClean ['loginPassword'] == 'Bu11Kaugmentat10n') {
			$adminHelper->SetLoggedIn ( true );
		} else {
			////// INCORRECT CREDENTIALS //////
			$ip = $_SERVER['REMOTE_ADDR'];

			// Log Attempt
			$adminHelper->IncAttempts($ip);

			// Stop brute force attacks
			if($adminHelper->NumberOfAttempts($ip) > 3) {
				die('No more attempts!');
			}
			$adminHelper->SetLoggedIn ( false );
		} #var_dump($_SESSION);var_dump(session_save_path()); die('test2');
		if($this->mClean['secure'] == 'secure') {
			$redirectTo = $registry->secureBaseDir.'/admin/orders.php';
		} else {
			$redirectTo = $registry->adminDir . '/index.php';
		}
		header ( 'Location: ' . $redirectTo );
	}

}

try {
	$handler = new AdminLoginHandler ( );
	$handler->Validate ( $_POST );
	$handler->Login ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>