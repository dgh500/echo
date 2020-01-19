<?php

//! Deals with tax code tasks (create, delete etc)
class TaxCodeController {
	
	//! Gets all tax codes in the database and returns them in an array
	/*!
	 * @return Array of Obj:TaxCodeModel
	 */
	function GetAllTaxCodes() {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$get_all_tax_codes_sql = 'SELECT Tax_Code_ID FROM tblTaxCode';
		if (! $result = $database->query ( $get_all_tax_codes_sql )) {
			$error = new Error ( 'Could not fetch all tax codes.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$taxCodes = $result->fetchAll ( PDO::FETCH_OBJ );
		foreach ( $taxCodes as $taxCode ) {
			$newTaxCode = new TaxCodeModel ( $taxCode->Tax_Code_ID );
			$retTaxCodes [] = $newTaxCode;
		}
		if (0 == count ( $retTaxCodes )) {
			$retTaxCodes = array ();
		}
		return $retTaxCodes;
	}
	
	//! Gets an (arbitrary) tax code from the database when a default is needed
	/*!
	 * @return Obj:TaxCodeModel
	 */
	function GetDefaultTaxCode() {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$get_tax_code_sql = 'SELECT Tax_Code_ID FROM tblTaxCode LIMIT 1';
		if (! $result = $database->query ( $get_tax_code_sql )) {
			$error = new Error ( 'Could not fetch tax code.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$taxCode = $result->fetch ( PDO::FETCH_OBJ );
		$newTaxCode = new TaxCodeModel ( $taxCode->Tax_Code_ID );
		return $newTaxCode;
	}

}

?>