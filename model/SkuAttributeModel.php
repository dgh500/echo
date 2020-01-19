<?php

//! A single SKU Attribute (Eg. Blue)
class SkuAttributeModel {
	
	//! Int : The SKU this attribute value is related to
	var $mSkuId;
	//! Int : The unique SKU attribute ID
	var $mSkuAttributeId;
	//! String(100) : The text value of this attribute (Eg. Large)
	var $mAttributeValue;
	//! Int : The product attribute that it is a value for (Eg. Size)
	var $mProductAttributeId;
	
	//! Constructor, initialises the SKU Attribute ID
	function __construct($skuAttributeId) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$does_this_sku_attribute_exist_sql = 'SELECT COUNT(SKU_Attribute_ID) FROM tblSku_Attributes WHERE SKU_Attribute_ID = ' . $skuAttributeId;
		$result = $database->query ( $does_this_sku_attribute_exist_sql );
		if ($result) {
			if ($result->fetchColumn () > 0) {
				$this->mSkuAttributeId = $skuAttributeId;
			} else {
				$error = new Error ( 'Could not initialise sku attribute ' . $skuAttributeId . ' because it does not exist in the database.' );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		} else {
			$error = new Error ( 'Could not initialise sku attribute ' . $skuAttributeId . ' with sql ' . $does_this_sku_attribute_exist_sql . '.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	//! Returns the SKU ID
	/*!
	* @return Int
	*/
	function GetSkuId() {
		if (! isset ( $this->mSkuId )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_sku_id_sql = 'SELECT SKU_ID FROM tblSku_Attributes WHERE SKU_Attribute_ID = ' . $this->mSkuAttributeId.' LIMIT 1';
			if (! $result = $database->query ( $get_sku_id_sql )) {
				$error = new Error ( 'Could not fetch the SKU ID for SKU attribute ' . $this->mSkuAttributeId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$sku_id = $result->fetch ( PDO::FETCH_OBJ );
			$this->mSkuId = $sku_id->SKU_ID;
		}
		return $this->mSkuId;
	}
	
	//! This isnt needed; a SKU attribute is only ever related to ONE SKU, and it is only created or remover, never edited
	function SetSkuId() {
		//VOID
	}
	
	//! Returns the unique SKU Attribute ID (Set in the constructor)
	/*!
	* @return Int
	*/
	function GetSkuAttributeId() {
		return $this->mSkuAttributeId;
	}
	
	//! Returns the Attribute Value (Eg. A-Clamp, Blue, Large etc.)
	/*!
	* @return String(100)
	*/
	function GetAttributeValue() {
		if (! isset ( $this->mAttributeValue )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_attribute_value_sql = 'SELECT Attribute_Value FROM tblSku_Attributes WHERE SKU_Attribute_ID = ' . $this->mSkuAttributeId.' LIMIT 1';
			if (! $result = $database->query ( $get_attribute_value_sql )) {
				$error = new Error ( 'Could not fetch the attribute value for SKU attribute ' . $this->mSkuAttributeId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$attribute_value = $result->fetch ( PDO::FETCH_OBJ );
			$this->mAttributeValue = str_replace('£','&pound;',$attribute_value->Attribute_Value);
		}
		return $this->mAttributeValue;
	}
	
	//! Sets the value of this SKU attribute
	/*!
	* @param [in] newAttributeValue : String : The new value
	* @return Bool : true if successful
	*/
	function SetAttributeValue($newAttributeValue) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_attribute_value_sql = 'UPDATE tblSku_Attributes SET Attribute_Value = \'' . $newAttributeValue . '\' WHERE SKU_Attribute_ID = ' . $this->mSkuAttributeId;
		if (! $database->query ( $set_attribute_value_sql )) {
			$error = new Error ( 'Could not update the attribute value for product ' . $this->mSkuAttributeId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mAttributeValue = $newAttributeValue;
		return true;
	}
	
	//! Returns the product attribute ID this sku attribute is a value of
	/*!
	* @return Int
	*/
	function GetProductAttributeId() {
		if (! isset ( $this->mProductAttributeId )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_product_attribute_sql = 'SELECT Product_Attribute_ID FROM tblSku_Attributes WHERE SKU_Attribute_ID = ' . $this->mSkuAttributeId.' LIMIT 1';
			$result = $database->query ( $get_product_attribute_sql );
			$product_attribute_id = $result->fetch ( PDO::FETCH_OBJ );
			$this->mProductAttributeId = $product_attribute_id->Product_Attribute_ID;
		}
		return $this->mProductAttributeId;
	}
	
	//! This isnt needed; a SKU attribute is only ever related to ONE product attribute, and it is either created or destroyed but never edited
	function SetProductAttributeId() {
		//VOID
	}

}
/* DEBUG
$skuAttributeId = 10;
try {
	$skuAttribute = new SkuAttributeModel($skuAttributeId);
	$skuAttribute->SetAttributeValue('test');
} catch(Exception $e) {
	echo $e->GetMessage();
}*/

?>