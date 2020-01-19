<?php

//! Models a single Dispatch date (Eg. Same day)
class DispatchDateModel {
	//! Int : Unique Dispatch Date ID
	var $mDispatchDateId;
	//! String : The dispatch date name (Eg. Same Day/1 Week etc.)
	var $mDisplayName;
	
	//! Constructor, initialises the dispatch date ID
	function __construct($dispatchDateId) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$does_this_dispatch_date_exist_sql = 'SELECT COUNT(Dispatch_Date_ID) FROM tblDispatch_Date WHERE Dispatch_Date_ID = ' . $dispatchDateId;
		if (! $result = $database->query ( $does_this_dispatch_date_exist_sql )) {
			$error = new Error ( 'Could not construct dispatch date.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		if ($result->fetchColumn () > 0) {
			$this->mDispatchDateId = $dispatchDateId;
		} else {
			$error = new Error ( 'Could not initialise dispatch date ' . $dispatchDateId . ' because it does not exist in the database.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	//! Returns the name of the dispatch date
	/*!
	* @return String
	*/
	function GetDisplayName() {
		if (! isset ( $this->mDisplayName )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_display_name_sql = 'SELECT Display_Name FROM tblDispatch_Date WHERE Dispatch_Date_ID = ' . $this->mDispatchDateId.' LIMIT 1';
			if (! $result = $database->query ( $get_display_name_sql )) {
				$error = new Error ( 'Could not fetch the display name for tax code ' . $this->mDispatchDateId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$display_name = $result->fetch ( PDO::FETCH_OBJ );
			$this->mDisplayName = $display_name->Display_Name;
		}
		return $this->mDisplayName;
	}
	
	//! Sets the name of the dispatch date
	/*!
	* @param [in] newDisplayName : String
	* @return Bool : true if successful
	*/
	function SetDisplayName($newDisplayName) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_display_name_sql = 'UPDATE tblDispatch_Date SET Display_Name = \'' . $newDisplayName . '\' WHERE Dispatch_Date_ID = ' . $this->mDispatchDateId;
		if (! $database->query ( $set_display_name_sql )) {
			$error = new Error ( 'Could not update the display name for dispatch date ' . $this->mDispatchDateId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDisplayName = $newDisplayName;
		return true;
	}
	//! Returns the unique dispatch date ID (Set in the constructor)
	/*!
	* @return Int
	*/
	function GetDispatchDateId() {
		return $this->mDispatchDateId;
	}

}

?>