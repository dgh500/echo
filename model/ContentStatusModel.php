<?php

//! Models a single content status (Eg. Manufacturer, Dive...)
class ContentStatusModel {
	
	var $mContentStatusId;
	var $mDisplayName;
	
	//! Constructor, initialises the status
	function __construct($identifier) {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
		$sql = 'SELECT COUNT(Content_Status_ID) AS StatusCount FROM tblContent_Status WHERE Content_Status_ID = ' . $identifier;
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not initialise content status ' . $identifier . ' because it does not exist in the database.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		} else {
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			if ($resultObj->StatusCount > 0) {
				$this->mContentStatusId = $identifier;
			} else {
				$error = new Error ( 'Could not initialise content status ' . $identifier . ' because it does not exist in the database.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
	}
	
	function GetDisplayName() {
		if (! isset ( $this->mDisplayName )) {
			$sql = 'SELECT Display_Name FROM tblContent_Status WHERE Content_Status_ID = ' . $this->mContentStatusId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the content status ' . $this->mContentStatusId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$display_name = $result->fetch ( PDO::FETCH_OBJ );
			$this->mDisplayName = $display_name->Display_Name;
		}
		return $this->mDisplayName;
	}
	
	function SetDisplayName($newDisplayName) {
		$sql = 'UPDATE tblContent_Status SET Display_Name = \'' . $newDisplayName . '\' WHERE Content_Status_ID = ' . $this->mContentStatusId;
		if (! $database->query ( $sql )) {
			$error = new Error ( 'Could not update the order status ' . $this->mContentStatusId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDisplayName = $newDisplayName;
		return true;
	}
	
	function GetContentStatusId() {
		return $this->mContentStatusId;
	}

}

?>