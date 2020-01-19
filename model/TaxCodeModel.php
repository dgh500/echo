<?php

//! Models a single tax code (Eg. Standard)
class TaxCodeModel {
	//! Int : Unique Tax Code ID
	var $mTaxCodeId;
	//! String : The tax code name (Eg. Standard/Zero etc.)
	var $mDisplayName;
	//! Decimal : The actual rate of tax
	var $mRate;
	
	//! Constructor, initialises the Tax code ID
	function __construct($taxCodeId) {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
		$sql = 'SELECT COUNT(Tax_Code_ID) FROM tblTaxCode WHERE Tax_Code_ID = ' . $taxCodeId;
		$result = $this->mDatabase->query ( $sql );
		if ($result->fetchColumn () > 0) {
			$this->mTaxCodeId = $taxCodeId;
		} else {
			$error = new Error ( 'Could not initialise tax code ' . $taxCodeId . ' because it does not exist in the database.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	//! Returns the name of the tax code
	/*!
	* @return String
	*/
	function GetDisplayName() {
		if (! isset ( $this->mDisplayName )) {
			$get_display_name_sql = 'SELECT Display_Name FROM tblTaxCode WHERE Tax_Code_ID = ' . $this->mTaxCodeId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $get_display_name_sql )) {
				$error = new Error ( 'Could not fetch the display name for tax code ' . $this->mTaxCodeId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$display_name = $result->fetch ( PDO::FETCH_OBJ );
			$this->mDisplayName = $display_name->Display_Name;
		}
		return $this->mDisplayName;
	}
	
	//! Sets the name of the tax code
	/*!
	* @param [in] newDisplayName : String
	* @return Bool : true if successful
	*/
	function SetDisplayName($newDisplayName) {
		$set_display_name_sql = 'UPDATE tblTaxCode SET Display_Name = \'' . $newDisplayName . '\' WHERE Tax_Code_ID = ' . $this->mTaxCodeId;
		if (! $this->mDatabase->query ( $set_display_name_sql )) {
			$error = new Error ( 'Could not update the display name for tax code ' . $this->mTaxCodeId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDisplayName = $newDisplayName;
		return true;
	}
	
	//! Gets the numeric rate of this tax code
	/*!
	 * @return Decimal : The tax rate (eg. 17.5%)
	 */
	function GetRate() {
		if (! isset ( $this->mRate )) {
			$sql = 'SELECT Rate FROM tblTaxCode WHERE Tax_Code_ID = ' . $this->mTaxCodeId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the rate for tax code ' . $this->mTaxCodeId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mRate = $resultObj->Rate;
		}
		return $this->mRate;
	}
	
	//! Returns the unique tax code (Set in the constructor)
	/*!
	* @return Int
	*/
	function GetTaxCodeId() {
		return $this->mTaxCodeId;
	}

}

?>