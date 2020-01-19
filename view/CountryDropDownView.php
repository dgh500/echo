<?php

class CountryDropDownView extends View {
	
	function __construct() {
		parent::__construct ();
	}
	
	function LoadDefault($defaultCountry,$prefix='Ship To: ',$selfForm=true,$admin=false) {
		$countryController = new CountryController ( );
		$allCountries = $countryController->GetAll ();
		if($admin) {
			$fh = 'AdminCountryDropDownHandler.php';
		} else {
			$fh = 'CountryDropDownHandler.php';	
		}
		if ($selfForm) {
			$this->mPage .= '<form id="countryDropDownForm" name="countryDropDownForm" method="post" action="' . $this->mBaseDir . '/formHandlers/'.$fh.'">';
			$this->mPage .= '<label for="countryDropDownMenu">' . $prefix . '</label><select name="countryDropDownMenu" id="countryDropDownMenu" onChange="this.form.submit();">';
		} else {
			$this->mPage .= '<label for="countryDropDownMenu">' . $prefix . '</label><select name="countryDropDownMenu" id="countryDropDownMenu">';
		}
		foreach ( $allCountries as $country ) {
			if ($defaultCountry->GetShortDescription () == $country->GetShortDescription ()) {
				$this->mPage .= '<option value="' . $country->GetCountryId () . '" selected="selected">' . $country->GetShortDescription () . '</option>';
			} else {
				$this->mPage .= '<option value="' . $country->GetCountryId () . '">' . $country->GetShortDescription () . '</option>';
			}
		}
		$this->mPage .= '</select>';
		if ($selfForm) {
			$this->mPage .= '</form>';
		}
		return $this->mPage;
	}

}

?>