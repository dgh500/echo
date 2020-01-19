<?php

//! Models a referrer
class ReferrerModel {
	
	//! Unique identifier for a referrer
	var $mReferrerId;
	//! String - Name of the referrer
	var $mDescription;
	//! Obj:PDO - database connection used to access database level
	var $mDatabase;
	
	//! Constructor, initialises the referrer
	function __construct($referrerId) {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
		$check_sql = 'SELECT COUNT(Referrer_ID) FROM tblReferrer WHERE Referrer_ID = ' . $referrerId;
		if (! $result = $this->mDatabase->query ( $check_sql )) {
			$error = new Error ( 'Could not construct referrer.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		if ($result->fetchColumn () > 0) {
			$this->mReferrerId = $referrerId;
		} else {
			$error = new Error ( 'Could not initialise referrer ' . $referrerId . ' because it does not exist in the database.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	//! Returns the referrer description
	/*!
	 * @return String
	 */
	function GetDescription() {
		if (! isset ( $this->mDescription )) {
			$sql = 'SELECT Description FROM tblReferrer WHERE Referrer_ID = ' . $this->mReferrerId;
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
	
	//! Set the description of the referrer
	/*!
	 * @param [in] newDescription : String - the new description
	 * @return Boolean : true if successful
	 */
	function SetDescription($newDescription) {
		$sql = 'UPDATE tblReferrer SET Description = \'' . $newDescription . '\' WHERE Referrer_ID = ' . $this->mReferrerId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the description of referrer: ' . $this->mReferrerId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDescription = $newDescription;
		return true;
	}
	
	function GetReferrerId() {
		return $this->mReferrerId;
	}
}
?>