<?php

//! Models a particular courier, Eg. Royal Mail
class CourierModel {
	
	//! Unique identifier for a courier
	var $mCourierId;
	//! String - a textual description of the courier (Eg. Parcelforce)
	var $mDisplayName;
	//! String - the tracking URL for this courier, to which a tracking number can be appended
	var $mTrackingUrl;
	//! Obj:PDO - database connection used to access database level
	var $mDatabase;
	
	//! Constructor, initialises the courier
	function __construct($courierId) {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
		$check_sql = 'SELECT COUNT(Courier_ID) FROM tblCourier WHERE Courier_ID = ' . $courierId;
		if (! $result = $this->mDatabase->query ( $check_sql )) {
			$error = new Error ( 'Could not construct courier.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		if ($result->fetchColumn () > 0) {
			$this->mCourierId = $courierId;
		} else {
			$error = new Error ( 'Could not initialise courier ' . $courierId . ' because it does not exist in the database.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	//! Returns the display name of the courier
	/*!
	 * @return String
	 */
	function GetDisplayName() {
		if (! isset ( $this->mDisplayName )) {
			$sql = 'SELECT Display_Name FROM tblCourier WHERE Courier_ID = ' . $this->mCourierId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mDisplayName = $resultObj->Display_Name;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mDisplayName;
	}
	
	//! Set the display name of this courier
	/*!
	 * @param [in] newDisplayName : String - the new display name
	 * @return Boolean : true if successful
	 */
	function SetDisplayName($newDisplayName) {
		$sql = 'UPDATE tblCourier SET Display_Name = \'' . $newDisplayName . '\' WHERE Courier_ID = ' . $this->mCourierId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the display name for courier: ' . $this->mCourierId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDisplayName = $newDisplayName;
		return true;
	}
	
	//! Returns the tracking url of the courier
	/*!
	 * @return String
	 */
	function GetTrackingUrl() {
		if (! isset ( $this->mTrackingUrl )) {
			$sql = 'SELECT Track_Url FROM tblCourier WHERE Courier_ID = ' . $this->mCourierId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mTrackingUrl = $resultObj->Track_Url;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mTrackingUrl;
	}
	
	//! Set the tracking url of this courier
	/*!
	 * @param [in] newTrackingUrl : String - the new tracking URL
	 * @return Boolean : true if successful
	 */
	function SetTrackingUrl($newTrackingUrl) {
		$sql = 'UPDATE tblCourier SET Track_URL = \'' . $newTrackingUrl . '\' WHERE Courier_ID = ' . $this->mCourierId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the tracking URL for courier: ' . $this->mCourierId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mTrackingUrl = $newTrackingUrl;
		return true;
	}
	
	//! Return status identifier
	function GetCourierId() {
		return $this->mCourierId;
	}

}

?>