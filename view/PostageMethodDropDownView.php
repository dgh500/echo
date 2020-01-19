<?php

class PostageMethodDropDownView extends View {

	function __construct() {
		parent::__construct ();
	}

	//! Default load function
	/*!
	 * @param $weight - The weight of the basket
	 * @param $country - Which country is selected
	 * @param $defaultMethod - The postage method that is selected by default
	 * @param $admin - Boolean, whether or not this is for the live or admin views
	 */
	function LoadDefault($weight, $country, $defaultMethod,$admin=false) {
		// Different handler depending on live/admin
		if($admin) {
			$fh = 'AdminPostageDropDownHandler.php';
		} else {
			$fh = 'PostageDropDownHandler.php';
		}

		// Get all of the postage methods
		$postageMethodController = new PostageMethodController();
		$allPostageMethods = $postageMethodController->GetAll();
		// Open the form
		$this->mPage .= '<form action="' . $this->mBaseDir . '/formHandlers/'.$fh.'" method="post" id="postageDropDownForm" name="postageDropDownForm">
						Delivery Method: <select name="postageMethodDropDownMenu" id="postageMethodDropDownMenu" onChange="this.form.submit();">';
		// Loop over all postage methods
		foreach ( $allPostageMethods as $postageMethod ) {
			// Which is selected
			if ($defaultMethod->GetPostageMethodId () == $postageMethod->GetPostageMethodId ()) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
			// Constrain by weight
		/*	if ($weight < $postageMethod->GetMaxWeight () || $postageMethod->GetMaxWeight () == 0) {
				switch ($country->GetTwoLetter()) {
					// Postage methods available in the UK (excluding n.ireland and scottish highlands/islands if over 2kg)
					case $country->InUk() && (!$country->IsScottishHighlandsAndIslands() && !$country->IsNIreland() || $weight < 2000):
						switch ($postageMethod->GetPostageMethodId ()) {
							case 1 :	// Royal Mail Second Class
								if ($weight >= $postageMethod->GetMinWeight()) {*/
				// Standard delivery!
			#	$this->mPage .= '<option value="'.$postageMethod->GetPostageMethodId().'" '.$selected.'>'.$postageMethod->GetDisplayName().'</option>';
				// Upgrade to Courier if under 1kg
				if(($weight <= $postageMethod->GetMaxWeight() && $weight >= $postageMethod->GetMinWeight()) || $postageMethod->GetMaxWeight() == 0) {
					$this->mPage .= '<option value="'.$postageMethod->GetPostageMethodId().'" '.$selected.'>'.$postageMethod->GetDisplayName().'</option>';
				}
/*								}
							break;
						} // End switch(postage method id)
						break;
					// Scottish highlands and N.Ireland
					case $country->IsScottishHighlandsAndIslands() || $country->IsNIreland():
						switch ($postageMethod->GetPostageMethodId()) {
							case 16 :	// DPD Two Day
								if ($weight >= $postageMethod->GetMinWeight()) {
									$this->mPage .= '<option value="'.$postageMethod->GetPostageMethodId().'" '.$selected.'>'.$postageMethod->GetDisplayName().'</option>';
								}
							break;
						}
						break;
					// Those for outside the UK
					default :
						switch ($postageMethod->GetPostageMethodId ()) {
							case 7 :	// Int. Signed For
								$this->mPage .= '<option value="' . $postageMethod->GetPostageMethodId () . '" ' . $selected . '>' . $postageMethod->GetDisplayName () . '</option>';
								break;
							case 6 :	// DPD Classic
								if ($country->InEurope ()) {
									$this->mPage .= '<option value="' . $postageMethod->GetPostageMethodId () . '" ' . $selected . '>' . $postageMethod->GetDisplayName () . '</option>';
								}
								break;
							case 8 :	// DPD Air Express
								if (! $country->InEurope ()) {
									$this->mPage .= '<option value="' . $postageMethod->GetPostageMethodId () . '" ' . $selected . '>' . $postageMethod->GetDisplayName () . '</option>';
								}
								break;
						}
						break;	// End Non-UK
				} // End switch(country)
			} // End if(in weight range)*/
		} // End foreach(countries)
		// Close the form
		$this->mPage .= '</select></form>';
		return $this->mPage;
	} // End LoadDefault

} // End class


?>