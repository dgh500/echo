<?php

//! Deals with SKU Attribute tasks (create, delete etc)
class SkuAttributeController {
	
	//! Creates a new SKU attribute in the database then returns this SKU attribute as an object of type SkuAttributeModel
	/*!
	 * @param [in] sku : Obj:SkuModel : The SKU that the attribute is related to
	 * @return Obj:SkuAttributeModel - the new SKU attribute
	 */
	function CreateSkuAttribute($sku, $productAttribute) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$create_sku_attribute_sql = 'INSERT INTO tblSku_Attributes (`SKU_ID`,`Product_Attribute_ID`) VALUES (\'' . $sku->GetSkuId () . '\',\'' . $productAttribute->GetProductAttributeId () . '\')';
		if ($database->query ( $create_sku_attribute_sql )) {
			$get_latest_sku_attribute_sql = 'SELECT SKU_Attribute_ID FROM tblSku_Attributes ORDER BY SKU_Attribute_ID DESC LIMIT 1';
			if (! $result = $database->query ( $get_latest_sku_attribute_sql )) {
				$error = new Error ( 'Could not select new SKU attribute' );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$latest_sku_attribute = $result->fetch ( PDO::FETCH_OBJ );
			$newSkuAttribute = new SkuAttributeModel ( $latest_sku_attribute->SKU_Attribute_ID );
			return $newSkuAttribute;
		} else {
			$error = new Error ( 'Could not insert SKU attribute' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	//! Attempts to delete a SKU attribute from the database, throws an exception if this fails
	/*!
	 * @return true if successful
	 * @param [in] skuAttribute : Obj:SkuAttributeModel - the sku attribute to delete
	 */
	function DeleteSkuAttribute($skuAttribute) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$delete_sku_attribute_sql = 'DELETE FROM tblSku_Attributes WHERE SKU_Attribute_ID = ' . $skuAttribute->GetSkuAttributeId ();
		if (! $database->query ( $delete_sku_attribute_sql )) {
			$error = new Error ( 'Could not delete SKU attribute ' . $skuAttribute->GetSkuAttributeId () );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->getErrorMsg () );
		} else {
			return true;
		}
	}
	
	//! Given an SKU and a ProductAttribute, returns the SkuAttribute that they cross reference
	/*!
	 * @return Obj:SkuAttributeModel
	 * @param [in] sku : Obj:SkuModel
	 * @param [in] productAttribute : Obj:ProductAttributeModel
	 */
	function GetSkuAttributeFor($sku, $productAttribute) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$get_sku_attribute_sql = 'SELECT SKU_Attribute_ID FROM tblSku_Attributes WHERE SKU_ID = ' . $sku->GetSkuId () . ' AND Product_Attribute_ID = ' . $productAttribute->GetProductAttributeId ();
		if (! $result = $database->query ( $get_sku_attribute_sql )) {
			$error = new Error ( 'Could not get SKU Attribute for product attribute ' . $productAttribute->GetProductAttributeId () . ' and sku ' . $sku->GetSkuId () . '.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$sku_attribute = $result->fetch ( PDO::FETCH_OBJ );
		$newSkuAttribute = new SkuAttributeModel ( $sku_attribute->SKU_Attribute_ID );
		return $newSkuAttribute;
	}

}

/* DEBUG SECTION 
try { 
		
		$currentProduct = new ProductModel(114);
		$productAttributeController = new ProductAttributeController;
		$newProductAttribute = $productAttributeController->CreateProductAttribute($_POST['addProductAttribute'],$currentProduct,0);
		
		// Create values for the new attribute for each SKU
		$skuAttributeController = new SkuAttributeController;
		$skus = $currentProduct->getSkus();
		foreach($skus as $sku) {
			$skuAttributeController->CreateSkuAttribute($sku,$newProductAttribute);
		}
	
	
} catch(Exception $e) {
	echo $e->GetMessage();
}*/

?>