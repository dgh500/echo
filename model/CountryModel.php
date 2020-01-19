<?php

//! Models a particular country, Eg. UK
class CountryModel {
	
	//! Int - Unique identifier for a country
	var $mCountryId;
	//! String - Short description of the country (Eg. Holy See (Vatican City))
	var $mShortDescription;
	//! String - Full description of the country (Eg. Holy See (Vatican City State))
	var $mDescription;
	//! String - ISO3166 Two-letter country code (Eg. VA)
	var $mTwoLetter;
	//! String - ISO3166 Three-letter country code (Eg. VAT)
	var $mThreeLetter;
	//! String - ISO3166 Three-digit country code (Eg. 336)
	var $mThreeDigit;
	//! Obj:CurrencyModel - the currency used by the country
	var $mCurrency;
	//! Obj:PDO - database connection used to access database level
	var $mDatabase;
	
	//! Initialises the country
	/*!
	 * @param $countryId [in] - Int - The ID of the country
	 */
	function __construct($countryId,$useCountryCode=false) {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
		if($useCountryCode) {
			$check_sql = 'SELECT COUNT(Country_ID) FROM tblCountry WHERE ISO3166_Two_Letter = \'' . $countryId.'\'';			
		} else {
			$check_sql = 'SELECT COUNT(Country_ID) FROM tblCountry WHERE Country_ID = ' . $countryId;
		}
		if (! $result = $this->mDatabase->query ( $check_sql )) {
			$error = new Error ( 'Could not construct country.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		if ($result->fetchColumn () > 0) {
			if($useCountryCode) {
				$this->mCountryId = $this->GetCountryIdFromTwoLetter($countryId);
			} else {
				$this->mCountryId = $countryId;
			}
		} else {
			$error = new Error ( 'Could not initialise country ' . $countryId . ' because it does not exist in the database.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	//! @private - Gets the country ID from the database using the two-letter code
	function GetCountryIdFromTwoLetter($twoLetter) {
		$sql = 'SELECT Country_ID FROM tblCountry WHERE ISO3166_Two_Letter = \''.$twoLetter.'\' LIMIT 1';
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not find country ID from ISO3166 Two Letter Code.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		} else {
			$resultObj = $result->fetch(PDO::FETCH_OBJ);
			return $resultObj->Country_ID;
		}
	}
	
	//! Returns the description of this country
	/*!
	 * @return String
	 */
	function GetDescription() {
		if (! isset ( $this->mDescription )) {
			$sql = 'SELECT Description FROM tblCountry WHERE Country_ID = ' . $this->mCountryId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mDescription = $resultObj->Description;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mDescription;
	}
	
	//! Set the description of this country
	/*!
	 * @param [in] newDescription : String - the new description
	 * @return Boolean : true if successful
	 */
	function SetDescription($newDescription) {
		$sql = 'UPDATE tblCountry SET Description = \'' . $newDescription . '\' WHERE Country_ID = ' . $this->mCountryId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the description for country: ' . $this->mCountryId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDescription = $newDescription;
		return true;
	}
	
	//! Returns the short description of this country
	/*!
	 * @return String
	 */
	function GetShortDescription() {
		if (! isset ( $this->mShortDescription )) {
			$sql = 'SELECT Short_Description FROM tblCountry WHERE Country_ID = ' . $this->mCountryId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mShortDescription = $resultObj->Short_Description;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mShortDescription;
	}
	
	//! Set the short description of this country
	/*!
	 * @param [in] newShortDescription : String - the new short description
	 * @return Boolean : true if successful
	 */
	function SetShortDescription($newShortDescription) {
		$sql = 'UPDATE tblCountry SET Short_Description = \'' . $newShortDescription . '\' WHERE Country_ID = ' . $this->mCountryId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the short description for country: ' . $this->mCountryId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mShortDescription = $newShortDescription;
		return true;
	}
	
	//! Returns the ISO3166 Two-Letter code of this country
	/*!
	 * @return String
	 */
	function GetTwoLetter() {
		if (! isset ( $this->mTwoLetter )) {
			$sql = 'SELECT ISO3166_Two_Letter FROM tblCountry WHERE Country_ID = ' . $this->mCountryId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mTwoLetter = $resultObj->ISO3166_Two_Letter;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mTwoLetter;
	}
	
	//! Set the ISO3166 Two letter code of this country
	/*!
	 * @param [in] newTwoLetter : String - the new two letter code
	 * @return Boolean : true if successful
	 */
	function SetTwoLetter($newTwoLetter) {
		$sql = 'UPDATE tblCountry SET ISO3166_Two_Letter = \'' . $newTwoLetter . '\' WHERE Country_ID = ' . $this->mCountryId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the two letter code for country: ' . $this->mCountryId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mTwoLetter = $newTwoLetter;
		return true;
	}
	
	//! Returns the ISO3166 Three-Letter code of this country
	/*!
	 * @return String
	 */
	function GetThreeLetter() {
		if (! isset ( $this->mThreeLetter )) {
			$sql = 'SELECT ISO3166_Three_Letter FROM tblCountry WHERE Country_ID = ' . $this->mCountryId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mThreeLetter = $resultObj->ISO3166_Three_Letter;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mThreeLetter;
	}
	
	//! Set the ISO3166 Three letter code of this country
	/*!
	 * @param [in] newThreeLetter : String - the new three letter code
	 * @return Boolean : true if successful
	 */
	function SetThreeLetter($newThreeLetter) {
		$sql = 'UPDATE tblCountry SET ISO3166_Three_Letter = \'' . $newThreeLetter . '\' WHERE Country_ID = ' . $this->mCountryId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the three letter code for country: ' . $this->mCountryId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mThreeLetter = $newThreeLetter;
		return true;
	}
	
	//! Returns the ISO3166 Three-Digit code of this country
	/*!
	 * @return String
	 */
	function GetThreeDigit() {
		if (! isset ( $this->mThreeDigit )) {
			$sql = 'SELECT ISO3166_Three_Digit FROM tblCountry WHERE Country_ID = ' . $this->mCountryId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mThreeDigit = $resultObj->ISO3166_Three_Digit;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mThreeDigit;
	}
	
	//! Set the ISO3166 Three digit code of this country
	/*!
	 * @param [in] newThreeDigit : String - the new three digit code
	 * @return Boolean : true if successful
	 */
	function SetThreeDigit($newThreeDigit) {
		$sql = 'UPDATE tblCountry SET ISO3166_Three_Digit = \'' . $newThreeDigit . '\' WHERE Country_ID = ' . $this->mCountryId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the three digit code for country: ' . $this->mCountryId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mThreeDigit = $newThreeDigit;
		return true;
	}
	
	//! Returns the currency of this country
	/*!
	 * @return Obj:CurrencyModel
	 */
	function GetCurrency() {
		if (! isset ( $this->mCurrency )) {
			$sql = 'SELECT Currency_ID FROM tblCountry WHERE Country_ID = ' . $this->mCountryId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mCurrency = new CurrencyModel ( $resultObj->Currency_ID );
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mCurrency;
	}
	
	//! Set the currency of this country
	/*!
	 * @param [in] newCurrency : String - the new currency
	 * @return Boolean : true if successful
	 */
	function SetCurrency($newCurrency) {
		$sql = 'UPDATE tblCountry SET Currency_ID = \'' . $newCurrency->GetCurrencyId () . '\' WHERE Country_ID = ' . $this->mCountryId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the currency for country: ' . $this->mCountryId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mCurrency = $newCurrency;
		return true;
	}
	
	//! Whether or not the country is in europe
	/*!
	 * @return Boolean
	 */
	function InEurope() {
		$europe = array ('AT', 'BE', 'BG', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'GB', 'AL', 'AD', 'AM', 'AZ', 'BY', 'BA', 'GE', 'IS', 'LI', 'MD', 'MC', 'NO', 'RU', 'SM', 'CH', 'UA' );
		if (in_array ( $this->GetTwoLetter (), $europe )) {
			return true;
		} else {
			return false;
		}
	}
	
	//! Whether or not the country is in the EU
	/*!
	 * @return Boolean
	 */
	function InEU() {
		$europe = array ('AT', 'BE', 'BG', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LI', 'LU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'GB' );
		if (in_array ( $this->GetTwoLetter (), $europe )) {
			return true;
		} else {
			return false;
		}
	}
	
	//! Whether or not the country is in the EEA (European Econimic Area)
	/*!
	 * @return Boolean
	 */
	function InEEA() {
		$europe = array ('AT', 'BE', 'BG', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LI', 'LU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE', 'GB', 'NO', 'IS', 'LI' );
		if (in_array ( $this->GetTwoLetter (), $europe )) {
			return true;
		} else {
			return false;
		}
	}
	
	//! In UK (whole of)
	/*!
	 * @return Boolean
	 */
	function InUk() {
		$uk = array ('GB' );
		if (in_array ( $this->GetTwoLetter (), $uk )) {
			return true;
		} else {
			return false;
		}
	}
	
	//! In DPD Zone 1 - Belgium, France, Germany, Luxembourg, Netherlands, Rep. Ireland
	function InDPDZone1() {
		$zone1 = array('BE','FR','DE','LU','NL','IE');
		if(in_array($this->GetTwoLetter(),$zone1)) {
			return true;
		} else {
			return false;
		}	
	}

	//! In DPD Zone 2
	function InDPDZone2() {
		$zone2 = array('AT','DK','LI','CH');
		if(in_array($this->GetTwoLetter(),$zone2)) {
			return true;
		} else {
			return false;
		}	
	}

	//! In DPD Zone 3
	function InDPDZone3() {
		$zone3 = array('CZ','IT','SK','ES');
		if(in_array($this->GetTwoLetter(),$zone3)) {
			return true;
		} else {
			return false;
		}	
	}

	//! In DPD Zone 4
	function InDPDZone4() {
		$zone4 = array('EE','FI','HV','PL','PT','SI','SE');
		if(in_array($this->GetTwoLetter(),$zone4)) {
			return true;
		} else {
			return false;
		}	
	}

	//! In DPD Zone 5
	function InDPDZone5() {
		$zone5 = array('BA','BG','HR','GR','IS','LV','LT','CS','NO','RO','CS');
		if(in_array($this->GetTwoLetter(),$zone5)) {
			return true;
		} else {
			return false;
		}	
	}

	//! In DPD Zone 6
	function InDPDZone6() {
		$zone6 = array('TN','TR');
		if(in_array($this->GetTwoLetter(),$zone6)) {
			return true;
		} else {
			return false;
		}	
	}

	//! In DPD Zone 7
	function InDPDZone7() {
		$zone7 = array('MX','RU');
		if(in_array($this->GetTwoLetter(),$zone7)) {
			return true;
		} else {
			return false;
		}	
	}

	//! In Rep. Ireland
	/*!
	 * @return Boolean
	 */
	function InPFZone5() {
		$zone5 = array ('IE' );
		if (in_array ( $this->GetTwoLetter (), $zone5 )) {
			return true;
		} else {
			return false;
		}
	}
	
	//! In Belgium, Netherlands & Luxembourg
	/*!
	 * @return Boolean
	 */
	function InPFZone6() {
		$zone6 = array ('BE', 'NL', 'LU' );
		if (in_array ( $this->GetTwoLetter (), $zone6 )) {
			return true;
		} else {
			return false;
		}
	}
	
	//! In France, Germany & Denmark
	/*!
	 * @return Boolean
	 */
	function InPFZone7() {
		$zone7 = array ('FR', 'DE', 'DK' );
		if (in_array ( $this->GetTwoLetter (), $zone7 )) {
			return true;
		} else {
			return false;
		}
	}
	
	//! In Italy, Portugal, Spain, Greece
	/*!
	 * @return Boolean
	 */
	function InPFZone8() {
		$zone8 = array ('IT', 'PT', 'ES', 'GR' );
		if (in_array ( $this->GetTwoLetter (), $zone8 )) {
			return true;
		} else {
			return false;
		}
	}
	
	//! Whether or not the country is greece
	/*!
	 * @return Boolean
	 */
	function InGreece() {
		$greece = array ('GR' );
		if (in_array ( $this->GetTwoLetter (), $greece )) {
			return true;
		} else {
			return false;
		}
	}
	
	function IsBFPO() {
		$bfpo = array ('BFPO' );
		if (in_array ( $this->GetShortDescription (), $bfpo )) {
			return true;
		} else {
			return false;
		}
	}
	
	function IsJersey() {
		$needle = array ('Jersey' );
		if (in_array ( $this->GetShortDescription (), $needle )) {
			return true;
		} else {
			return false;
		}
	}
	
	function IsGuernsey() {
		$needle = array ('Guernsey' );
		if (in_array ( $this->GetShortDescription (), $needle )) {
			return true;
		} else {
			return false;
		}
	}
	
	function IsCanaryIsles() {
		$needle = array ('Tenerife', 'Lanzarote' );
		if (in_array ( $this->GetShortDescription (), $needle )) {
			return true;
		} else {
			return false;
		}
	}
	
	function IsNIreland() {
		$haystack = array ('Northern Ireland');
		if (in_array ( $this->GetShortDescription (), $haystack )) {
			return true;
		} else {
			return false;
		}
	}

	function IsIsleMan() {
		$haystack = array ('Isle of Man');
		if (in_array ( $this->GetShortDescription (), $haystack )) {
			return true;
		} else {
			return false;
		}
	}

	function IsScottishHighlandsAndIslands() {
		$haystack = array ('Scottish Highlands & Islands');
		if (in_array ( $this->GetShortDescription (), $haystack )) {
			return true;
		} else {
			return false;
		}
	}

	//! Returns true if the country qualifies for VAT-Free shipping
	function IsVatFree() {
		// Special cases - BFPO etc.
		if ($this->IsBFPO () || $this->IsJersey () || $this->IsGuernsey () || $this->IsCanaryIsles ()) {
			return true;
		}
		// If in EEA then not vat free
		if ($this->InEEA ()) {
			return false;
		} else {
			return true;
		}
	}
	
	//! In rest of europe - Ie. In europe but not uk or any zones defined
	/*!
	 * @return Boolean
	 */
	function InPFZone9() {
		if ($this->InEurope () && ! $this->InUK () && ! $this->InZone5 () && ! $this->InZone6 () && ! $this->InZone7 () && ! $this->InZone8 ()) {
			return true;
		} else {
			return false;
		}
	}
	
	//! Whether the country has a customs surcharge applied
	/*!
	 * @return Boolean
	 */
	function HasCustomsSurcharge() {
		$customsSurcharge = array('BA','HR','IS','LI','NO','CS','CH','TR','RU','MX','BG');
		if(in_array($this->GetTwoLetter(),$customsSurcharge)) {
			return true;
		} else {
			return false;
		}
	}
	
	/*! Gets the surcharge for the country  
	 * Boznia-Herzegovina, Croatia, Iceland, Liechtenstein, Montenegro, Norway, Serbia, Switzerland - £25
	 * Bulgaria - £35
	 * Mexico - £45
	 * Turkey - £50
	 * Russia - £105
	 */	
	function GetCustomsSurcharge() {
		$customsSurcharge1 = array('BA','HR','IS','LI','NO','CS','CH'); // £25 Applied
		$customsSurcharge2 = array('BG'); // £35 Applied
		$customsSurcharge3 = array('MX'); // £45 Applied
		$customsSurcharge4 = array('TR'); // £50 Applied
		$customsSurcharge5 = array('RU'); // £105 Applied
		switch($this->GetTwoLetter()) {
			case in_array($this->GetTwoLetter(),$customsSurcharge1):
				return 25;
			break;
			case in_array($this->GetTwoLetter(),$customsSurcharge2):
				return 35;
			break;			
			case in_array($this->GetTwoLetter(),$customsSurcharge3):
				return 45;
			break;
			case in_array($this->GetTwoLetter(),$customsSurcharge4):
				return 50;
			break;
			case in_array($this->GetTwoLetter(),$customsSurcharge5):
				return 105;
			break;			
			default:	// Should never get here - check using HasCustomsSurcharge first
				return 0;
			break;
		} // End Switch
	} // End GetCustomsSurcharge
	
	
	//! Return status identifier
	function GetCountryId() {
		return $this->mCountryId;
	}

}

?>