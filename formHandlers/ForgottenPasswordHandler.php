<?php

require_once ('../autoload.php');
#include ('../phpMailer/class.phpmailer.php');

class ForgottenPasswordHandler extends Handler {

	var $mClean;

	function __construct() {
		parent::__construct();
		$this->mSessionHelper = new SessionHelper ( );
	}

	function Validate($postArr) {
		$this->mClean ['email'] = $this->mValidationHelper->MakeSafe ( $postArr ['email'] );
		$this->mClean ['emailConfirmation'] = $this->mValidationHelper->MakeSafe ( $postArr ['emailConfirmation'] );
		$this->mClean ['redirectTo'] = $this->mValidationHelper->MakeSafe ( $postArr ['redirectTo'] );
	}

	//! Generate a new password
	function GenerateNewPassword($len = 6) {
		// function calculates 32-digit hexadecimal md5 hash of some random data
		return substr(md5(rand().rand()),0,$len);
	}

	function Process() {
		// Generate new password
		$this->mNewPassword = $this->GenerateNewPassword();
		try {
			$customer = new CustomerModel($this->mClean['email']);
			$this->mCustomer = new CustomerModel($this->mClean['email']);
			if(!$customer->SetPassword($this->mNewPassword)) {
				die('Unable to change password - please call for support!');
			}

			// Email password to user
			if(!$this->PrepareAndSendEmail()) {
				die('Unable to send email - please call for support!');
			}
		} catch(Exception $e) {
			// null
		}

		// Redirect
		switch ($this->mClean ['redirectTo']) {
			case 'checkout' :
				$this->mSessionHelper->SetCheckoutStage('forgottenPasswordReset');
				$redirectTo = $this->mRegistry->secureBaseDir.'/checkout.php';
				header('Location: '.$redirectTo);
				break;
			default :
				$redirectTo = $this->mRegistry->baseDir.'/account/passwordReset';
				header('Location: '.$redirectTo);
				break;
		}
	}

	function PrepareAndSendEmail() {
		$today = date ( 'l jS F Y' );
		$body = "
Greetings from Echo Supplements<br />
We received a request to reset the password associated with this e-mail address. You can find your new password below - feel free to log in with it and change it to something more memorable!<br />
Email Username: ".$this->mClean ['email']."<br />
New Password:   ".$this->mNewPassword."<br />
Thank you for visiting Echo Supplements!<br />
-------------------------------------------------------------<br />
	Echo Supplements<br />
	01753 572741<br />
-------------------------------------------------------------<br />";
		$this->mMail = new PHPMailer;
		$this->mMail->From = "info@echosupplements.com";
		$this->mMail->FromName = "Password Reset";
		$this->mMail->Subject = "Password Reset";
		$this->mMail->Host = "smtp.gmail.com";
		$this->mMail->Port = 465;
		$this->mMail->Mailer = "smtp";
		$this->mMail->SMTPAuth = true;
		$this->mMail->Username = "info@echosupplements.com";
		$this->mMail->Password = "bl00dlu5t";
		$this->mMail->Body = $body;
		$this->mMail->SMTPSecure = "ssl"; // option
		$this->mMail->AltBody = $body;
	#	$this->mMail->AddAddress ( "info@echosupplements.com" );
		try {
			$this->mMail->AddAddress($this->mCustomer->GetEmail());
		} catch(Exception $e) {
			// null
		}
		if($this->mMail->Send()) {
			return true;
		} else {
			return false;
		}
	} // End PrepareAndSendEmail

} // End ForgottenHandler

try {
	$handler = new ForgottenPasswordHandler ( );
	$handler->Validate ( $_POST );
	$handler->Process ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>