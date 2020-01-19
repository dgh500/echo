<?php

//! A single Product Attribute (Eg. Colour)
class ProductAttributeModel {
	
	//! Int : The unique product attribute ID
	var $mProductAttributeId;
	//! Int : The product that this attribute relates to
	var $mProductId;
	//! Int : The type of attribute (Eg. Drop-Down)
	var $mType;
	//! String(100) : The product attribute name (Eg. Size)
	var $mAttributeName;
	//! An array of SkuAttributeModel objects
	var $mSkuAttributes;
	//! Obj:PDO - database connection used to access database level
	var $mDatabase;
	
	//! Constructor, initialises the Product Attribute ID
	function __construct($productAttributeId) {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
		$does_this_product_attribute_exist_sql = 'SELECT COUNT(Product_Attribute_ID) FROM tblProduct_Attributes WHERE Product_Attribute_ID = ' . $productAttributeId;
		$result = $this->mDatabase->query ( $does_this_product_attribute_exist_sql );
		if ($result) {
			if ($result->fetchColumn () > 0) {
				$this->mProductAttributeId = $productAttributeId;
			} else {
				$error = new Error ( 'Could not initialise product attribute ' . $productAttributeId . ' because it does not exist in the database.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
	}
	
	//! Returns the SKU attributes associated with the attribute (Eg. Small/Med/Large would be associated with Size)
	/*
	 * @return Array of Obj:SkuAttributeModel 
	 */
	function GetSkuAttributes() {
		if (! isset ( $this->mSkuAttributes )) {
			$sql = 'SELECT SKU_Attribute_ID FROM tblSku_Attributes WHERE Product_Attribute_ID = ' . $this->mProductAttributeId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultArr = $result->fetchAll ( PDO::FETCH_OBJ );
				foreach ( $resultArr as $skuAttributeId ) {
					$this->mSkuAttributes [] = new SkuAttributeModel ( $skuAttributeId->SKU_Attribute_ID );
				}
				if (0 == count ( $resultArr )) {
					$this->mSkuAttributes = array ();
				}
			}
		}
		return $this->mSkuAttributes;
	}
	
	//! Returns the Attribute Name
	/*!
	* @return Int
	*/
	function GetAttributeName() {
		if (! isset ( $this->mAttributeName )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_attribute_name_sql = 'SELECT Attribute_Name FROM tblProduct_Attributes WHERE Product_Attribute_ID = ' . $this->mProductAttributeId.' LIMIT 1';
			if (! $result = $database->query ( $get_attribute_name_sql )) {
				$error = new Error ( 'Could not fetch the attribute name for product attribute ' . $this->mProductAttributeId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$attribute_name = $result->fetch ( PDO::FETCH_OBJ );
			$this->mAttributeName = $attribute_name->Attribute_Name;
		}
		return $this->mAttributeName;
	}
	
	//! Sets the attribute name of the attribute
	/*!
	* @param [in] newAttributeName int : The new attribute name
	* @return Bool : true if successful
	*/
	function SetAttributeName($newAttributeName) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_attribute_name_sql = 'UPDATE tblProduct_Attributes SET Attribute_Name = \'' . $newAttributeName . '\' WHERE Product_Attribute_ID = ' . $this->mProductAttributeId;
		if (! $database->query ( $set_attribute_name_sql )) {
			$error = new Error ( 'Could not update the attribute name for attribute ' . $this->mProductAttributeId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mAttributeName = $newAttributeName;
		return true;
	}
	
	//! Returns the Attribute Name (Set in constructor)
	/*!
	* @return Int
	*/
	function GetProductAttributeId() {
		return $this->mProductAttributeId;
	}
	
	//! Returns the product the attribute is related to
	/*!
	* @return Int
	*/
	function GetProductId() {
		if (! isset ( $this->mProductId )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$sql = 'SELECT Product_ID FROM tblProduct_Attributes WHERE Product_Attribute_ID = ' . $this->mProductAttributeId.' LIMIT 1';
			if (! $result = $database->query ( $sql )) {
				$error = new Error ( 'Could not fetch the product ID for product attribute ' . $this->mProductAttributeId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mProductId = $resultObj->Product_ID;
		}
		return $this->mProductId;
	}
	
	//! Sets the product ID of the attribute
	/*!
	* @param [in] newProductId int : The new product ID
	* @return Bool : true if successful
	*/
	function SetProductId($newProductId) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		// Don't need to explicitly check for referential integrity here because mysql will throw an error if the product doesnt exist, which will be caught by the exception
		$set_product_id_sql = 'UPDATE tblProduct_Attributes SET Product_ID = ' . $newProductId . ' WHERE Product_Attribute_ID = ' . $this->mProductAttributeId;
		if (! $database->query ( $set_product_id_sql )) {
			$error = new Error ( 'Could not update the product ID for attribute ' . $this->mProductAttributeId . ' with SQL: ' . $set_product_id_sql );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mProductId = $newProductId;
		return true;
	}
	
	//! Returns the type of attribute (Eg. Drop Down) as an index
	/*!
	* @return Int
	*/
	function GetType() {
		if (! isset ( $this->mType )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_attribute_type_sql = 'SELECT Type FROM tblProduct_Attributes WHERE Product_Attribute_ID = ' . $this->mProductAttributeId.' LIMIT 1';
			if (! $result = $database->query ( $get_attribute_type_sql )) {
				$error = new Error ( 'Could not fetch the attribute type for product attribute ' . $this->mProductAttributeId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$type = $result->fetch ( PDO::FETCH_OBJ );
			$this->mType = $type->Type;
		}
		return $this->mType;
	}

}
/* DEBUG SECTION 
$productAttributeId = 3;
try {
	$productAttribute = new ProductAttributeModel($productAttributeId);
} catch (Exception $e) {
	echo $e->getMessage();
}
try {
	$productAttribute->GetType();
} catch(Exception $e) {
	echo $e->getMessage();
}
*/

?>