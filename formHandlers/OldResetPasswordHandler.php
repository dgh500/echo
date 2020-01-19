<?php

require_once ('../autoload.php');

/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}*/

class OldResetPasswordHandler {
	
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
	}
	
	function Process($postArr) {
		$email = $postArr ['passEmail'];
		$sha1Password = sha1 ( $postArr ['password'] );
		try {
			$customer = new CustomerModel ( $email );
			$customer->SetPassword ( $sha1Password );
			echo 'Password changed to ' . $postArr ['password'] . ' for email: ' . $postArr ['passEmail'];
		} catch ( Exception $e ) {
			echo $e->getMessage ();
		}
	}
}

try {
	$handler = new OldResetPasswordHandler ( );
	$handler->Process ( $_POST );
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>