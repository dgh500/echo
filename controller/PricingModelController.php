<?php

//! Deals with Catalogue tasks (create, delete etc)
class PricingModelController {
	
	function __construct() {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
	}
	
	//! Gets the first catalogue that was entered in the database, to be used when a default is needed
	/*!
	* @return Obj:PricingModel
	 */
	function GetDefaultPricingModel() {
		$sql = 'SELECT Pricing_Model_ID FROM tblPricing_Model ORDER BY Pricing_Model_ID ASC LIMIT 1';
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not fetch default pricing model.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$pricing_model_id = $result->fetch ( PDO::FETCH_OBJ );
		$defaultPricingModel = new PricingModel ( $pricing_model_id->Pricing_Model_ID );
		return $defaultPricingModel;
	}

}

?>