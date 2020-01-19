<?php

class ContentStatusController {
	
	function __construct() {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
	}
	// Hello there
	function GetAll() {
		$sql = 'SELECT Content_Status_ID FROM tblContent_Status';
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not fetch all content statuses.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$resultObjs = $result->fetchAll ( PDO::FETCH_OBJ );
		foreach ( $resultObjs as $resultObj ) {
			$newStatus = new ContentStatusModel ( $resultObj->Content_Status_ID );
			$retArr [] = $newStatus;
		}
		if (0 == count ( $retArr )) {
			$retArr = array ();
		}
		return $retArr;
	}

}

?>
