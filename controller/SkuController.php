<?php

//! Deals with SKU tasks (create, delete etc)
class SkuController {

	function __construct() {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
	}

	//! Creates a new SKU in the database then returns this SKU as an object of type SkuModel
	/*!
	 * @return Obj:SkuModel - the new SKU
	 */
	function CreateSku() {
		$create_sku_sql = 'INSERT INTO tblSku (`SKU_Price`) VALUES (\'0\')';
		if ($this->mDatabase->query ( $create_sku_sql )) {
			$get_latest_sku_sql = 'SELECT SKU_ID FROM tblSku ORDER BY SKU_ID DESC LIMIT 1';
			if (! $result = $this->mDatabase->query ( $get_latest_sku_sql )) {
				$error = new Error ( 'Could not select new SKU attribute' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$latest_sku = $result->fetch ( PDO::FETCH_OBJ );
			$newSku = new SkuModel ( $latest_sku->SKU_ID );
			return $newSku;
		} else {
			$error = new Error ( 'Could not insert SKU' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

	//! Attempts to delete a SKU from the database, throws an exception if this fails
	/*!
	 * @return true if successful
	 * @param [in] sku : Obj:SkuModel - the SKU  to delete
	*/
	function DeleteSku($sku) {
		$delete_sku_sql [] = 'DELETE FROM tblSku_Attributes WHERE SKU_ID = ' . $sku->GetSkuId ();
		$delete_sku_sql [] = 'DELETE FROM tblProduct_SKUs WHERE SKU_ID = ' . $sku->GetSkuId ();
		$delete_sku_sql [] = 'DELETE FROM tblSku WHERE SKU_ID = ' . $sku->GetSkuId ();
		foreach ( $delete_sku_sql as $sql ) {
			if (FALSE === $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Problem deleting SKU ' . $sku->GetSkuId () . ' with SQL:<br /> ' . $sql );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->getErrorMsg () );
			}
		}
		return true;
	}

	function GetAllSkus($catalogueList) {
		$retArr = array ();
		$sql = 'SELECT tblSku.SKU_ID FROM tblSku
					INNER JOIN tblProduct_SKUs
						ON tblProduct_SKUs.SKU_ID = tblSku.SKU_ID
					INNER JOIN tblProduct
						ON tblProduct_SKUs.Product_ID = tblProduct.Product_ID
					INNER JOIN tblProduct_Text
						ON tblProduct_Text.Product_ID = tblProduct.Product_ID
					INNER JOIN tblCategory_Products
						ON tblCategory_Products.Product_ID = tblProduct.Product_ID
					INNER JOIN tblCategory
						ON tblCategory_Products.Category_ID = tblCategory.Category_ID
					WHERE tblCategory.Catalogue_ID IN (' . $catalogueList . ')
					ORDER BY tblCategory.Display_Name';
		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$sku = new SkuModel ( $resultObj->SKU_ID );
			$retArr [] = $sku;
		}
		return $retArr;
	}

	//! Get those SKUs that are out of stock
	function GetOutOfStockSkus($catalogueList) {
		$retArr = array ();
		$sql = 'SELECT DISTINCT tblSku.SKU_ID FROM tblSku
					INNER JOIN tblProduct_SKUs
						ON tblProduct_SKUs.SKU_ID = tblSku.SKU_ID
					INNER JOIN tblProduct
						ON tblProduct_SKUs.Product_ID = tblProduct.Product_ID
					INNER JOIN tblProduct_Text
						ON tblProduct_Text.Product_ID = tblProduct.Product_ID
					INNER JOIN tblCategory_Products
						ON tblCategory_Products.Product_ID = tblProduct.Product_ID
					INNER JOIN tblCategory
						ON tblCategory_Products.Category_ID = tblCategory.Category_ID
					WHERE tblCategory.Catalogue_ID IN (' . $catalogueList . ')
					AND tblSku.Qty = 0
					AND tblCategory.Display_Name <> \'Samples\'
					ORDER BY tblCategory.Display_Name';
		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$sku = new SkuModel ( $resultObj->SKU_ID );
			$retArr [] = $sku;
		}
		return $retArr;
	}

	//! Gets the top SKU sales within the given time
	/*!
	 * @param $catalogueList	- Str - A comma-seperated list of catalogue IDs to be used directly by the SQL - NB. No checking done, eg. 123,154,156,152
	 * @param $startTimestamp	- Int - The timestamp to constrain the start of the search to, defaults to 01/01/2008
	 * @param $endTimestamp		- Int - The timestamp to constrain the end of the search to, defaults to current time
	 * @return $retArr			- Arr - An array of SKUs/Counts for the SKUs
	 	$retArr['sku'][] - SkuModel
		$retArr['count'][SKU_ID] - Int, the count for that SKU
	 */
	function GetTopSkus($catalogueList, $startTimestamp = false, $endTimestamp = false) {
		// Set Defaults
		if (! $startTimestamp) {
			$startTimestamp = mktime ( 0, 0, 0, 1, 1, 2008 );
		}
		if (! $endTimestamp) {
			$endTimestamp = time ();
		}

		$retArr = array ();
		$sql = 'SELECT tblBasket_Skus.SKU_ID, COUNT(tblBasket_Skus.SKU_ID) AS SkuCount
				FROM tblBasket_Skus
				WHERE tblBasket_Skus.Basket_ID IN(
					SELECT DISTINCT tblBasket_Skus.Basket_ID
					FROM tblBasket_Skus
					INNER JOIN tblOrder ON tblBasket_Skus.Basket_ID = tblOrder.Basket_ID
					INNER JOIN tblProduct_SKUs ON tblProduct_SKUs.SKU_ID = tblBasket_Skus.SKU_ID
					INNER JOIN tblProduct_Text ON tblProduct_SKUs.Product_ID = tblProduct_Text.Product_ID
					INNER JOIN tblCategory_Products ON tblCategory_Products.Product_ID = tblProduct_SKUs.Product_ID
					INNER JOIN tblCategory ON tblCategory_Products.Category_ID = tblCategory.Category_ID
					WHERE tblCategory.Catalogue_ID IN (' . $catalogueList . ')
					AND tblOrder.Status_ID = 3
					AND tblOrder.Created_Date BETWEEN  ' . $startTimestamp . ' AND ' . $endTimestamp . '
					AND tblProduct_Text.Product_ID IN (SELECT tblCategory_Products.Product_ID FROM tblCategory_Products)
				)
				AND tblBasket_Skus.SKU_ID IN (SELECT SKU_ID FROM tblProduct_SKUs)
				GROUP BY tblBasket_Skus.SKU_ID ORDER BY SkuCount DESC';

		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$sku = new SkuModel ( $resultObj->SKU_ID );
			$retArr ['sku'] [] = $sku;
			$retArr ['count'] [$sku->GetSkuId ()] = $resultObj->SkuCount;
		}
		return $retArr;
	}

	//! Gets the SKU that for example 'black, large' refers to
	/*!
	 * @param $attributeIds - An array of attribute IDs with which to find the SKU they represent
	 * @return The SKU ID of the SKU or false if not found
	 */
	function RetrieveSKUFromAttributes($attributeIds,$product) {
		$idStr = '';
		$productAttributeStr = '';
		foreach($attributeIds as $attrId) { $idStr .= $attrId.', '; }
		foreach($product->GetAttributes() as $attribute) { $productAttributeStr .= $attribute->GetProductAttributeId().', '; }
		$attrCount = count($product->GetAttributes());
		$idStr = substr($idStr,0,strlen($idStr)-2);
		$productAttributeStr = substr($productAttributeStr,0,strlen($productAttributeStr)-2);
		$sql = 'SELECT SKU_ID from tblSku_Attributes WHERE Attribute_Value IN
				(SELECT Attribute_Value FROM tblSku_Attributes
				WHERE Sku_Attribute_ID IN ('.$idStr.'))
				AND Product_Attribute_ID IN ('.$productAttributeStr.')
				GROUP BY SKU_ID
				HAVING COUNT(SKU_ID) = '.$attrCount;
		$result = $this->mDatabase->query($sql);
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		if(is_object($resultObj)) {
			return $resultObj->SKU_ID;
		} else {
			return false;
		}
	}

}

/* DEBUG
try {
	$skuCont = new SkuController;
	$sku = $skuCont->CreateSku();
	#$sku = new SkuModel(16);
	#$skuCont->DeleteSku($sku);
	var_dump($sku);
} catch(Exception $e) {
	echo $e->GetMessage();
}*/

?>