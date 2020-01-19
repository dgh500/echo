<?php

//! Defines and performs operations with pricing models (eg. by price/weight etc.) NB. Should really be called PricingModelModel...
class PricingModel {
	
	var $mPricingModelId;
	var $mDisplayName;
	var $mDatabase;
	
	//! Constructor, initialises the pricing model
	/*!
	 * @param $id [in] Int
	 * @return Void
	 */
	function __construct($id) {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
		$sql = 'SELECT COUNT(Pricing_Model_ID) AS PricingCount FROM tblPricing_Model WHERE Pricing_Model_ID = ' . $id;
		if ($result = $this->mDatabase->query ( $sql )) {
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			if ($resultObj->PricingCount > 0) {
				$this->mPricingModelId = $id;
			} else {
				$error = new Error ( 'Could not initialise pricing model ' . $id . ' because it does not exist in the database.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		} else {
			$error = new Error ( 'Could not initialise pricing model ' . $id . ' because query: ' . $sql . ' failed to produce a result.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	//! Returns the identifier
	function GetPricingModelId() {
		return $this->mPricingModelId;
	}
	
	//! Returns the name of the pricing model
	/*!
	* @return String
	*/
	function GetDisplayName() {
		if (! isset ( $this->mDisplayName )) {
			$sql = 'SELECT Display_Name FROM tblPricing_Model WHERE Pricing_Model_ID = ' . $this->mPricingModelId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the display name for pricing model ' . $this->mPricingModelId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$display_name = $result->fetch ( PDO::FETCH_OBJ );
			$this->mDisplayName = $display_name->Display_Name;
		}
		return $this->mDisplayName;
	}
	
	//! Returns the content that shows the full description to the public
	/*!
	* @return Obj:ContentModel
	*/
	function GetContent() {
		if (! isset ( $this->mContent )) {
			$sql = 'SELECT Content_ID FROM tblPricing_Model WHERE Pricing_Model_ID = ' . $this->mPricingModelId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the content for pricing model ' . $this->mPricingModelId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mContent = new ContentModel ( $resultObj->Content_ID );
		}
		return $this->mContent;
	}

}

?>