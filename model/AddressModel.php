<?php

//! Models an address - town, postcode etc.
class AddressModel {
	
	//! Int : Address unique identifier
	var $mAddressId;
	//! String : Company Name
	var $mCompany;
	//! String : First line of the address
	var $mAddressLine1;
	//! String : Second line of the address
	var $mAddressLine2;
	//! String : Third line of the address
	var $mAddressLine3;
	//! String : City name
	var $mCity;
	//! String : County Name
	var $mCounty;
	//! String : Post code
	var $mPostcode;
	//! Obj:PDO : Used to access the database level
	var $mDatabase;
	
	//! Constructor, initialises an address. Throws an exception if the address doesn't exist.
	/*!
	 * @param $addressId - Int - Unique identifier for the address
	 */
	function __construct($addressId) {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
		$check_sql = 'SELECT COUNT(Address_ID) FROM tblAddress WHERE Address_ID = ' . $addressId;
		if (! $result = $this->mDatabase->query ( $check_sql )) {
			$error = new Error ( 'Could not construct address.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		if ($result->fetchColumn () > 0) {
			$this->mAddressId = $addressId;
		} else {
			$error = new Error ( 'Could not initialise address ' . $statusId . ' because it does not exist in the database.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	//! Returns the company name for this address
	/*!
	 * @return String
	 */
	function GetCompany() {
		if (! isset ( $this->mCompany )) {
			$sql = 'SELECT Company FROM tblAddress WHERE Address_ID = ' . $this->mAddressId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mCompany = $resultObj->Company;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return trim($this->mCompany);
	}
	
	//! Set the company of this address
	/*!
	 * @param [in] newCompany : String - the new company
	 * @return Boolean : true if successful
	 */
	function SetCompany($newCompany) {
		$sql = 'UPDATE tblAddress SET Company = \'' . $newCompany . '\' WHERE Address_ID = ' . $this->mAddressId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the company for address: ' . $this->mAddressId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mCompany = $newCompany;
		return true;
	}
	
	//! Returns the first line of the address
	/*!
	 * @return String
	 */
	function GetLine1() {
		if (! isset ( $this->mAddressLine1 )) {
			$sql = 'SELECT Address_Line_1 FROM tblAddress WHERE Address_ID = ' . $this->mAddressId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mAddressLine1 = $resultObj->Address_Line_1;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return trim($this->mAddressLine1);
	}
	
	//! Set the first line of this address
	/*!
	 * @param [in] newLine : String - the new line
	 * @return Boolean : true if successful
	 */
	function SetLine1($newLine) {
		$sql = 'UPDATE tblAddress SET Address_Line_1 = \'' . $newLine . '\' WHERE Address_ID = ' . $this->mAddressId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the address line 1 for address: ' . $this->mAddressId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mAddressLine1 = $newLine;
		return true;
	}
	
	//! Returns the second line of the address
	/*!
	 * @return String
	 */
	function GetLine2() {
		if (! isset ( $this->mAddressLine2 )) {
			$sql = 'SELECT Address_Line_2 FROM tblAddress WHERE Address_ID = ' . $this->mAddressId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mAddressLine2 = $resultObj->Address_Line_2;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return trim($this->mAddressLine2);
	}
	
	//! Set the second line of this address
	/*!
	 * @param [in] newLine : String - the new line
	 * @return Boolean : true if successful
	 */
	function SetLine2($newLine) {
		$sql = 'UPDATE tblAddress SET Address_Line_2 = \'' . $newLine . '\' WHERE Address_ID = ' . $this->mAddressId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the address line 2 for address: ' . $this->mAddressId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mAddressLine2 = $newLine;
		return true;
	}
	
	//! Returns the third line of the address
	/*!
	 * @return String
	 */
	function GetLine3() {
		if (! isset ( $this->mAddressLine3 )) {
			$sql = 'SELECT Address_Line_3 FROM tblAddress WHERE Address_ID = ' . $this->mAddressId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mAddressLine3 = $resultObj->Address_Line_3;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return trim($this->mAddressLine3);
	}
	
	//! Set the third line of this address
	/*!
	 * @param [in] newLine : String - the new line
	 * @return Boolean : true if successful
	 */
	function SetLine3($newLine) {
		$sql = 'UPDATE tblAddress SET Address_Line_3 = \'' . $newLine . '\' WHERE Address_ID = ' . $this->mAddressId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the address line 3 for address: ' . $this->mAddressId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mAddressLine3 = $newLine;
		return true;
	}
	
	//! Returns the county of the address
	/*!
	 * @return String
	 */
	function GetCounty() {
		if (! isset ( $this->mCounty )) {
			$sql = 'SELECT County FROM tblAddress WHERE Address_ID = ' . $this->mAddressId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mCounty = $resultObj->County;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return trim($this->mCounty);
	}
	
	//! Set the county of this address
	/*!
	 * @param [in] newCounty : String - the new county
	 * @return Boolean : true if successful
	 */
	function SetCounty($newCounty) {
		$sql = 'UPDATE tblAddress SET County = \'' . $newCounty . '\' WHERE Address_ID = ' . $this->mAddressId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the county for address: ' . $this->mAddressId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mCounty = $newCounty;
		return true;
	}
	
	//! Returns the postcode of the address
	/*!
	 * @return String
	 */
	function GetPostcode() {
		if (! isset ( $this->mPostcode )) {
			$sql = 'SELECT Postcode FROM tblAddress WHERE Address_ID = ' . $this->mAddressId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mPostcode = $resultObj->Postcode;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return trim($this->mPostcode);
	}
	
	//! Set the postcode of this address
	/*!
	 * @param [in] newPostcode : String - the new county
	 * @return Boolean : true if successful
	 */
	function SetPostcode($newPostcode) {
		$sql = 'UPDATE tblAddress SET Postcode = \'' . $newPostcode . '\' WHERE Address_ID = ' . $this->mAddressId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the postcode for address: ' . $this->mAddressId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mPostcode = $newPostcode;
		return true;
	}
	
	//! Returns the country of the address
	/*!
	 * @return Obj:CountryModel
	 */
	function GetCountry() {
		if (! isset ( $this->mCountry )) {
			$sql = 'SELECT Country_ID FROM tblAddress WHERE Address_ID = ' . $this->mAddressId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mCountry = new CountryModel ( $resultObj->Country_ID );
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mCountry;
	}
	
	//! Set the country of this address
	/*!
	 * @param [in] newCountry : Obj:CountryModel - the new country
	 * @return Boolean : true if successful
	 */
	function SetCountry($newCountry) {
		$sql = 'UPDATE tblAddress SET Country_ID = \'' . $newCountry->GetCountryId () . '\' WHERE Address_ID = ' . $this->mAddressId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the country ID for address: ' . $this->mAddressId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mCountry = $newCountry;
		return true;
	}
	
	// Retuns the unique address identifier
	/*!
	 * @return Int
	 */
	function GetAddressId() {
		return $this->mAddressId;
	}

}

?>