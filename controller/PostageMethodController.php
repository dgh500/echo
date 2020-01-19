<?php

class PostageMethodController {
	
	function __construct() {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
	}
	
	//! Gets the default postage method
	function GetDefault() {
		$sql = 'SELECT Postage_Method_ID FROM tblPostage_Method ORDER BY Postage_Method_ID ASC LIMIT 1';
		if ($result = $this->mDatabase->query ( $sql )) {
			$resultObj = $result->fetchObject ();
			return new PostageMethodModel ( $resultObj->Postage_Method_ID );
		} else {
			$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	function GetAll() {
		$sql = 'SELECT Postage_Method_ID FROM tblPostage_Method ORDER BY Display_Name ASC';
		if ($result = $this->mDatabase->query ( $sql )) {
			$retArr = array ();
			while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
				$retArr [] = new PostageMethodModel ( $resultObj->Postage_Method_ID );
			}
			return $retArr;
		} else {
			$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

}

?>