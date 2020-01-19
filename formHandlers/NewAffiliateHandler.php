<?php

require_once ('../autoload.php');

/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}*/

class NewAffiliateHandler {
	
	var $mClean;
	
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mSessionHelper = new SessionHelper ( );
		$this->mAffiliateController = new AffiliateController ( );
	}
	
	function Validate($postArr) {
		$registry = Registry::getInstance ();
		$this->mClean ['affName'] = $this->mValidationHelper->MakeSafe ( $postArr ['affName'] );
		$this->mClean ['affEmail'] = $this->mValidationHelper->MakeSafe ( $postArr ['affEmail'] );
		$this->mClean ['affTelNo'] = $this->mValidationHelper->MakeSafe ( $postArr ['affTelNo'] );
		$this->mClean ['affWebsiteUrl'] = $this->mValidationHelper->MakeSafe ( $postArr ['affWebsiteUrl'] );
		if ($postArr ['affPassword'] == $postArr ['affPasswordCheck']) {
			$this->mClean ['affPassword'] = sha1 ( $postArr ['affPassword'] );
			$this->mClean ['error'] = false;
		} else {
			$this->mClean ['error'] = true;
		}
		if ($this->mAffiliateController->AffiliateAlreadyExists ( $this->mClean ['affEmail'] )) {
			$this->mClean ['affiliateExists'] = true;
		} else {
			$this->mClean ['affiliateExists'] = false;
		}
	}
	
	function CreateAffiliate() {
		$registry = Registry::getInstance ();
		if ($this->mClean ['affiliateExists']) {
			$this->mSessionHelper->SetAffiliateStage ( 'registrationFailure' );
			$this->mSessionHelper->SetAffiliateRegistrationError ( 'this email address is already in use' );
		} else {
			if ($this->mClean ['error']) {
				$this->mSessionHelper->SetAffiliateStage ( 'registrationFailure' );
				$this->mSessionHelper->SetAffiliateRegistrationError ( 'there was a problem validating your details' );
			} else {
				$newAffiliate = $this->mAffiliateController->CreateAffiliate ();
				$newAffiliate->SetName ( $this->mClean ['affName'] );
				$newAffiliate->SetPassword ( $this->mClean ['affPassword'] );
				$newAffiliate->SetEmail ( $this->mClean ['affEmail'] );
				$newAffiliate->SetTelephone ( $this->mClean ['affTelNo'] );
				$newAffiliate->SetUrl ( $this->mClean ['affWebsiteUrl'] );
				$this->mSessionHelper->SetAffiliateStage ( 'loggedIn' );
				$this->mSessionHelper->SetAffiliate ( $newAffiliate->GetAffiliateId () );
			}
		}
		$redirectTo = $registry->baseDir . '/affiliateArea';
		header ( 'Location: ' . $redirectTo );
	}

}

try {
	$handler = new NewAffiliateHandler ( );
	$handler->Validate ( $_POST );
	$handler->CreateAffiliate ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>