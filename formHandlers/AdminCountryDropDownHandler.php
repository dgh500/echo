<?php

require_once ('../autoload.php');

/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}*/

class AdminCountryDropDownHandler {
	
	var $mClean;
	
	function __construct($redirectTo=false) {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mSessionHelper = new SessionHelper ( );
		$this->mBasket = $this->mSessionHelper->GetBasket();
		$registry = Registry::getInstance ();
		if($redirectTo) {
			$this->mRedirectTo = $redirectTo;
		} else {
			$this->mRedirectTo = $registry->viewDir.'/AddOrderView2.php#basketTab';	
		}
	}
	
	function Validate($postArr) {
		$this->mClean ['countryDropDownMenu'] = $_POST ['countryDropDownMenu'];
	}
	
	function UpdatePostages() {
		// Update the prices of items in the basket depending if they are VAT exempt or not
		$previousCountry = new CountryModel($_SESSION ['countryId']);
		$country = new CountryModel($this->mClean ['countryDropDownMenu']);
		
		// Changing from VAT-Inclusive (UK) to VAT-Free (BFPO)
		if($country->IsVatFree() && !$previousCountry->IsVatFree()) {
			$this->mBasket->MakeContentsVatFree();
		}
		
		// Changing from VAT-Free (Jersey) to VAT-Inclusive (UK)
		if(!$country->IsVatFree() && $previousCountry->IsVatFree()) {
			$this->mBasket->MakeContentsVatInclusive();	
		}		
		$this->mSessionHelper->SetCountry ( $this->mClean ['countryDropDownMenu'] );
		header('Location: '.$this->mRedirectTo);
	}

}

try {
	$handler = new AdminCountryDropDownHandler ( );
	$handler->Validate ( $_POST );
	$handler->UpdatePostages ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>