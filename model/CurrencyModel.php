<?php

//! Models a currency (Eg. US Dollar)
class CurrencyModel {
	
	//! Unique identifier for a currency
	var $mCurrencyId;
	//! String - a textual description of the currency (Eg. Colombian Peso)
	var $mDescription;
	//! String - a short description of the currency (Eg. Peso)
	var $mShortDescription;
	//! String - a textual description of the currency in plural (Eg. Colombian Pesos)
	var $mPluralDescription;
	//! String - a short, plural textual description of the currency (Eg. Pesos)
	var $mShortPluralDescription;
	//! String - The ISO4217 three letter code for the currency (Eg. COP)
	var $mThreeLetterCode;
	//! String - The ISO4217 three digit code for the currency (Eg. 170)
	var $mThreeDigitCode;
	//! Int - the minor unit of the currency - thee USD has 2 (ie. 1.00 dollars) - effectively the digits after decimal place (Eg. 2)
	var $mMinorDigit;
	//! String - the symbol of the currency (Eg. USD = $, GBP = £)
	var $mSymbol;
	//! Obj:PDO - database connection used to access database level
	var $mDatabase;
	
	//! Constructor, initialises the order status
	function __construct($currencyId) {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
		$check_sql = 'SELECT COUNT(Currency_ID) FROM tblCurrency WHERE Currency_ID = ' . $currencyId;
		if (! $result = $this->mDatabase->query ( $check_sql )) {
			$error = new Error ( 'Could not construct currency.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		if ($result->fetchColumn () > 0) {
			$this->mCurrencyId = $currencyId;
		} else {
			$error = new Error ( 'Could not initialise currency ' . $currencyId . ' because it does not exist in the database.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	//! Returns the textual description of the currency
	/*!
	 * @return String
	 */
	function GetDescription() {
		if (! isset ( $this->mDescription )) {
			$sql = 'SELECT Description FROM tblCurrency WHERE Currency_ID = ' . $this->mCurrencyId;
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
	
	//! Set the description of this currency
	/*!
	 * @param [in] newDescription : String - the new description
	 * @return Boolean : true if successful
	 */
	function SetDescription($newDescription) {
		$sql = 'UPDATE tblCurrency SET Description = \'' . $newDescription . '\' WHERE Currency_ID = ' . $this->mCurrencyId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the description for currency: ' . $this->mCurrencyId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDescription = $newDescription;
		return true;
	}
	
	//! Returns the short textual description of the currency
	/*!
	 * @return String
	 */
	function GetShortDescription() {
		if (! isset ( $this->mShortDescription )) {
			$sql = 'SELECT Short_Description FROM tblCurrency WHERE Currency_ID = ' . $this->mCurrencyId;
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
	
	//! Set the short description of this currency
	/*!
	 * @param [in] newShortDescription : String - the new short description
	 * @return Boolean : true if successful
	 */
	function SetShortDescription($newShortDescription) {
		$sql = 'UPDATE tblCurrency SET Short_Description = \'' . $newShortDescription . '\' WHERE Currency_ID = ' . $this->mCurrencyId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the short description for currency: ' . $this->mCurrencyId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mShortDescription = $newShortDescription;
		return true;
	}
	
	//! Returns the plural textual description of the currency
	/*!
	 * @return String
	 */
	function GetPluralDescription() {
		if (! isset ( $this->mPluralDescription )) {
			$sql = 'SELECT Plural_Description FROM tblCurrency WHERE Currency_ID = ' . $this->mCurrencyId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mPluralDescription = $resultObj->Plural_Description;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mPluralDescription;
	}
	
	//! Set the plural description of this currency
	/*!
	 * @param [in] newPluralDescription : String - the new plural description
	 * @return Boolean : true if successful
	 */
	function SetPluralDescription($newPluralDescription) {
		$sql = 'UPDATE tblCurrency SET Plural_Description = \'' . $newPluralDescription . '\' WHERE Currency_ID = ' . $this->mCurrencyId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the plural description for currency: ' . $this->mCurrencyId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mPluralDescription = $newPluralDescription;
		return true;
	}
	
	//! Returns the short plural textual description of the currency
	/*!
	 * @return String
	 */
	function GetShortPluralDescription() {
		if (! isset ( $this->mShortPluralDescription )) {
			$sql = 'SELECT Short_Plural_Description FROM tblCurrency WHERE Currency_ID = ' . $this->mCurrencyId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mShortPluralDescription = $resultObj->Short_Plural_Description;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mShortPluralDescription;
	}
	
	//! Set the short plural description of this currency
	/*!
	 * @param [in] newShortPluralDescription : String - the new short plural description
	 * @return Boolean : true if successful
	 */
	function SetShortPluralDescription($newShortPluralDescription) {
		$sql = 'UPDATE tblCurrency SET Short_Plural_Description = \'' . $newShortPluralDescription . '\' WHERE Currency_ID = ' . $this->mCurrencyId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the short plural description for currency: ' . $this->mCurrencyId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mShortPluralDescription = $newShortPluralDescription;
		return true;
	}
	
	//! Returns the ISO4217 three letter code for this currency
	/*!
	 * @return String
	 */
	function GetThreeLetterCode() {
		if (! isset ( $this->mThreeLetterCode )) {
			$sql = 'SELECT ISO4217_Three_Letter FROM tblCurrency WHERE Currency_ID = ' . $this->mCurrencyId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mThreeLetterCode = $resultObj->ISO4217_Three_Letter;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mThreeLetterCode;
	}
	
	//! Set the ISO4217 three letter code for this currency
	/*!
	 * @param [in] newThreeLetterCode : String - the new three letter code
	 * @return Boolean : true if successful
	 */
	function SetThreeLetterCode($newThreeLetterCode) {
		$sql = 'UPDATE tblCurrency SET ISO4217_Three_Letter = \'' . $newThreeLetterCode . '\' WHERE Currency_ID = ' . $this->mCurrencyId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the three letter code for currency: ' . $this->mCurrencyId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mThreeLetterCode = $newThreeLetterCode;
		return true;
	}
	
	//! Returns the ISO4217 three digit code for this currency
	/*!
	 * @return Int
	 */
	function GetThreeDigitCode() {
		if (! isset ( $this->mThreeDigitCode )) {
			$sql = 'SELECT ISO4217_Three_Digit FROM tblCurrency WHERE Currency_ID = ' . $this->mCurrencyId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mThreeDigitCode = $resultObj->ISO4217_Three_Digit;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mThreeDigitCode;
	}
	
	//! Set the ISO4217 three digit code for this currency
	/*!
	 * @param [in] newThreeDigitCode : String - the new three digit code
	 * @return Boolean : true if successful
	 */
	function SetThreeDigitCode($newThreeDigitCode) {
		$sql = 'UPDATE tblCurrency SET ISO4217_Three_Digit = \'' . $newThreeDigitCode . '\' WHERE Currency_ID = ' . $this->mCurrencyId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the three digit code for currency: ' . $this->mCurrencyId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mThreeDigitCode = $newThreeDigitCode;
		return true;
	}
	
	//! Returns the minor unit for this currency
	/*!
	 * @return String
	 */
	function GetMinorUnit() {
		if (! isset ( $this->mMinorUnit )) {
			$sql = 'SELECT Minor_Unit FROM tblCurrency WHERE Currency_ID = ' . $this->mCurrencyId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mMinorUnit = $resultObj->Minor_Unit;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mMinorUnit;
	}
	
	//! Set the minor unit for this currency
	/*!
	 * @param [in] newMinorUnit : String - the new minor unit
	 * @return Boolean : true if successful
	 */
	function SetMinorUnit($newMinorUnit) {
		$sql = 'UPDATE tblCurrency SET Minor_Unit = \'' . $newMinorUnit . '\' WHERE Currency_ID = ' . $this->mCurrencyId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the minor unit for currency: ' . $this->mCurrencyId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mMinorUnit = $newMinorUnit;
		return true;
	}
	
	//! Returns the symbol for this currency
	/*!
	 * @return String
	 */
	function GetSymbol() {
		if (! isset ( $this->mSymbol )) {
			$sql = 'SELECT Symbol FROM tblCurrency WHERE Currency_ID = ' . $this->mCurrencyId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mSymbol = $resultObj->Symbol;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mSymbol;
	}
	
	//! Set the symbol for this currency
	/*!
	 * @param [in] newSymbol : String - the new minor unit
	 * @return Boolean : true if successful
	 */
	function SetSymbol($newSymbol) {
		$sql = 'UPDATE tblCurrency SET Symbol = \'' . $newSymbol . '\' WHERE Currency_ID = ' . $this->mCurrencyId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the symbol for currency: ' . $this->mCurrencyId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mSymbol = $newSymbol;
		return true;
	}
	
	//! Return status identifier
	function GetCurrencyId() {
		return $this->mCurrencyId;
	}

}

?>