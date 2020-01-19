<?php

//! Models the status of an order, such as "In Transit" or "Complete"
class OrderStatusModel {
	
	//! Unique identifier for an order status
	var $mStatusId;
	//! String - a textual description of the order status (Eg. In transit)
	var $mDescription;
	//! Obj:PDO - database connection used to access database level
	var $mDatabase;
	
	//! Constructor, initialises the order status
	function __construct($statusId) {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
		$check_sql = 'SELECT COUNT(Status_ID) FROM tblOrder_Status WHERE Status_ID = ' . $statusId;
		if (! $result = $this->mDatabase->query ( $check_sql )) {
			$error = new Error ( 'Could not construct order status.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		if ($result->fetchColumn () > 0) {
			$this->mStatusId = $statusId;
		} else {
			$error = new Error ( 'Could not initialise order status ' . $statusId . ' because it does not exist in the database.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	//! Returns the textual description of the order status
	/*!
	 * @return String
	 */
	function GetDescription() {
		if (! isset ( $this->mDescription )) {
			$sql = 'SELECT Description FROM tblOrder_Status WHERE Status_ID = ' . $this->mStatusId;
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
	
	//! Set the description of this order status
	/*!
	 * @param [in] newDescription : String - the new description
	 * @return Boolean : true if successful
	 */
	function SetDescription($newDescription) {
		$sql = 'UPDATE tblOrder_Status SET Description = \'' . $newDescription . '\' WHERE Status_ID = ' . $this->mStatusId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the description for order status: ' . $this->mStatusId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDescription = $newDescription;
		return true;
	}
	
	function IsCancelled() {
		switch ($this->GetDescription ()) {
			case 'Cancelled By User' :
			case 'Cancelled By Merchant' :
				return true;
				break;
			default :
				return false;
				break;
		}
	}
	
	function IsFailed() {
		switch ($this->GetDescription ()) {
			case 'Failed' :
				return true;
				break;
			default :
				return false;
				break;
		}	
	}
	
	function IsAuthorised() {
		switch ($this->GetDescription ()) {
			case 'Authorised' :
				return true;
				break;
			default :
				return false;
				break;
		}
	}
	
	function IsInTransit() {
		switch ($this->GetDescription ()) {
			case 'In Transit' :
			case 'In Transit - Charged':
				return true;
				break;
			default :
				return false;
				break;
		}
	}
	
	function IsAwaitingAuth() {
		switch ($this->GetDescription ()) {
			case 'Awaiting Authorisation' :
				return true;
				break;
			default :
				return false;
				break;
		}	
	}
	
	function IsComplete() {
		switch ($this->GetDescription ()) {
			case 'Cancelled By User' :
			case 'Cancelled By Merchant' :
			case 'In Transit' :
			case 'Failed' :
				return true;
				break;
			default :
				return false;
				break;
		}
	}
	
	//! Return status identifier
	function GetStatusId() {
		return $this->mStatusId;
	}

}

?>