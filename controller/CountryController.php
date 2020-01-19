<?php

class CountryController {
	
	function __construct() {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
	}
	
	//! Gets the default country
	function GetDefault() {
		$sql = 'SELECT `Country_ID` FROM tblCountry WHERE ISO3166_Three_Letter = \'GBR\' LIMIT 1';
		if ($result = $this->mDatabase->query ( $sql )) {
			$resultObj = $result->fetchObject ();
			return new CountryModel ( $resultObj->Country_ID );
		} else {
			$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	function GetAll() {
		$sql = 'SELECT `Country_ID` FROM tblCountry ORDER BY Description ASC';
		if ($result = $this->mDatabase->query ( $sql )) {
			$retArr = array ();
			while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
				$retArr [] = new CountryModel ( $resultObj->Country_ID );
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