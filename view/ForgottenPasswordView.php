<?php

class ForgottenPasswordView extends View {

	function __construct() {
		parent::__construct ();
		$this->IncludeJs('validateForgottenPassword.js',true,true);
	}

	//! Loads the forgotten password form
	/*!
	 * @param $referrer - used to redirect the user to the correct page
	 * @return the code for this view
	 */
	function LoadDefault($referrer = '') {
		switch ($referrer) {
			case 'checkout' :
				$redirectTo = 'checkout';
				$dir = $this->mSecureBaseDir;
				break;
			default :
				$redirectTo = 'default';
				$dir = $this->mBaseDir;
				break;
		}
		$this->mPage .= '<form action="' . $dir . '/formHandlers/ForgottenPasswordHandler.php" method="post"
		id="passwordResetForm" onsubmit="return validateForgottenPasswordForm()">
							<strong>Enter the email address you use to log-in then click continue and we will reset your password and email the new password to you.</strong><br />
							<br />
							<strong>Please be aware - if you have only ordered by phone in the past, or using PayPal / Google Checkout then you do NOT have an account and need to create one.</strong><br />
							<br />
							<label for="email">Email Address</label>
								<input type="text" name="email" id="email" /><br />
							<label for="emailConfirmation">Re-Confirm Email Address</label>
								<input type="text" name="emailConfirmation" id="emailConfirmation" /><br />
							<input type="submit" id="submit" value="Reset Password" />
							<input type="hidden" name="redirectTo" id="redirectTo" value="' . $redirectTo . '" />
							<div id="error"></div>
						</form>';
		return $this->mPage;
	}
}

?>