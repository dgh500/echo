<?php

class CurrencyController {
	
	//! Gets the default order status (Looks for "Created" - if not found will throw an exception)
	function GetDefault() {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$sql = 'SELECT Currency_ID FROM tblCurrency WHERE ISO4217_Three_Letter LIKE \'GBP\'';
		if ($result = $database->query ( $sql )) {
			$resultObj = $result->fetchObject ();
			return new CurrencyModel ( $resultObj->Currency_ID );
		} else {
			$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

}

?>