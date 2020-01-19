<?php

require_once ('../autoload.php');

/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}*/

class AffiliateLoginHandler {
	
	var $mClean;
	
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mSessionHelper = new SessionHelper ( );
	}
	
	function Validate($postArr) {
		$this->mClean ['affLoginEmail'] = $this->mValidationHelper->MakeSafe ( $postArr ['affLoginEmail'] );
		$this->mClean ['affLoginPassword'] = $this->mValidationHelper->MakeSafe ( $postArr ['affLoginPassword'] );
	}
	
	function Login() {
		$registry = Registry::getInstance ();
		$affiliateController = new AffiliateController ( );
		try {
			$affiliate = new AffiliateModel ( $this->mClean ['affLoginEmail'], 'email' );
			if ($affiliateController->Login ( $affiliate, $this->mClean ['affLoginPassword'] )) {
				// Success
				$this->mSessionHelper->SetAffiliate ( $affiliate->GetAffiliateId () );
				$this->mSessionHelper->SetAffiliateStage ( 'loggedIn' );
			} else {
				// Failure
				$this->mSessionHelper->SetAffiliateStage ( 'loginFailure' );
			}
		} catch ( Exception $e ) {
			$this->mSessionHelper->SetAffiliateStage ( 'loginFailure' );
		}
		$redirectTo = $registry->baseDir . '/affiliateArea';
		header ( 'Location: ' . $redirectTo );
	}

}

try {
	$handler = new AffiliateLoginHandler ( );
	$handler->Validate ( $_POST );
	$handler->Login ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>