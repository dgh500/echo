<?php

//! Deals with Product Attribute tasks (create, delete etc)
class ProductAttributeController {
	
	//! Creates a new product attribute in the database then returns this product attribute as an object of type ProductAttributeModel
	/*!
	 * @param [in] attributeName : String : The name of the attribute (Eg. Size)
	 * @param [in] product : Obj:ProductModel : The product that the attribute is related to
	 * @param [in] type : Int : The type of attribute
	 * @return Obj:ProductAttributeModel - the new product attribute
	 */
	function CreateProductAttribute($attributeName, $product, $type) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$create_product_attribute_sql = 'INSERT INTO tblProduct_Attributes (`Product_ID`,`Type`,`Attribute_Name`) VALUES (\'' . $product->GetProductId () . '\',\'' . $type . '\',\'' . $attributeName . '\')';
		if ($database->query ( $create_product_attribute_sql )) {
			$get_latest_product_attribute_sql = 'SELECT Product_Attribute_ID FROM tblProduct_Attributes ORDER BY Product_Attribute_ID DESC LIMIT 1';
			if (! $result = $database->query ( $get_latest_product_attribute_sql )) {
				$error = new Error ( 'Could not select new product attribute' );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$latest_product_attribute = $result->fetch ( PDO::FETCH_OBJ );
			$newProductAttribute = new ProductAttributeModel ( $latest_product_attribute->Product_Attribute_ID );
			return $newProductAttribute;
		} else {
			$error = new Error ( 'Could not insert product attribute' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	//! Attempts to delete a product attribute from the database, throws an exception if this fails
	/*!
	 * @return true if successful
	 * @param [in] productAttribute : Obj:ProductAttributeModel - the product attribute to delete
	 */
	function DeleteProductAttribute($productAttribute) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$product = new ProductModel ( $productAttribute->GetProductId () );
		$delete_product_attribute_sql = 'DELETE FROM tblProduct_Attributes WHERE Product_Attribute_ID = ' . $productAttribute->GetProductAttributeId ();
		if (! $database->query ( $delete_product_attribute_sql )) {
			$error = new Error ( 'Could not delete product attribute ' . $productAttribute->GetProductAttributeId () );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->getErrorMsg () );
		}
		$allSkus = $product->GetSkus ();
		foreach ( $allSkus as $sku ) {
			$sql = 'DELETE FROM tblSku_Attributes WHERE SKU_ID = ' . $sku->GetSkuId () . ' AND Product_Attribute_ID = ' . $productAttribute->GetProductAttributeId ();
			$database->query ( $sql );
		}
		return true;
	}

}

/* DEBUG SECTION
try { 
	$prodAttCont = new ProductAttributeController();
	$currentProduct = new ProductModel(114);
	$newProductAttribute = $productAttributeController->CreateProductAttribute($_POST['addProductAttribute'],$currentProduct,0);
	
} catch(Exception $e) {
	echo $e->GetMessage();
} */

?>