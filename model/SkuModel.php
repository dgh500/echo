<?php

//! Models a single Stock Keeping Unit (Eg. Blue and Large Fins)
class SkuModel {

	//! Int : Unique SKU ID
	var $mSkuId;
	//! String(100) : Sage Code
	var $mSageCode;
	//! Decimal(19,4) : SKU Price
	var $mSkuPrice;
	//! Array : Set of SKU Attributes (object SkuAttributeModel) held in an array
	var $mSkuAttributes;
	//! Database object
	var $mDatabase;

	//! Constructor, initialises the SKU ID
	function __construct($skuId, $throwException = true) {
		$registry = Registry::getInstance ();
		$this->mThrowException = $throwException;
		$this->mDatabase = $registry->database;
		$does_this_sku_exist_sql = 'SELECT COUNT(SKU_ID) FROM tblSku WHERE SKU_ID = ' . $skuId;
		if ($result = $this->mDatabase->query ( $does_this_sku_exist_sql )) {
			if ($result->fetchColumn () > 0) {
				$this->mSkuId = $skuId;
			} else {
				if ($throwException) {
					$error = new Error ( 'Could not initialise sku ' . $skuId . ' because it does not exist in the database.' );
					$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
					throw new Exception ( $error->GetErrorMsg () );
				} else {
					return false;
				}
			}
		} else {
			$error = new Error ( 'Could not check sku ' . $skuId . ' because of a database problem.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

	//! Returns the unique SKU ID (Set in constructor)
	/*!
	* @return Int
	*/
	function GetSkuId() {
		return $this->mSkuId;
	}

	//! Returns the sage code
	/*!
	* @return String(100)
	*/
	function GetSageCode() {
		if (! isset ( $this->mSageCode )) {
			$get_sage_code_sql = 'SELECT Sage_Code FROM tblSku WHERE SKU_ID = ' . $this->mSkuId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $get_sage_code_sql )) {
				if ($this->mThrowException) {
					$error = new Error ( 'Could not fetch the Sage code for SKU ' . $this->mSkuId );
					$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
					throw new Exception ( $error->GetErrorMsg () );
				} else {
					return false;
				}
			}
			$sage_code = $result->fetch ( PDO::FETCH_OBJ );
			$this->mSageCode = $sage_code->Sage_Code;
		}
		return trim($this->mSageCode);
	}

	//! Sets the sage code for this SKU
	/*!
	* @param [in] newSageCode : String : The new sage code
	* @return Bool : true if successful
	*/
	function SetSageCode($newSageCode) {
		$set_sage_code_sql = 'UPDATE tblSku SET Sage_Code = \'' . $newSageCode . '\' WHERE SKU_ID = ' . $this->mSkuId;
		if (! $this->mDatabase->query ( $set_sage_code_sql )) {
			$error = new Error ( 'Could not update the sage code for SKU ' . $this->mSkuId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mSageCode = $newSageCode;
		return true;
	}

	//! Returns the quantity
	/*!
	* @return Int
	*/
	function GetQty() {
		if (! isset ( $this->mQty )) {
			$sql = 'SELECT Qty FROM tblSku WHERE SKU_ID = ' . $this->mSkuId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				if ($this->mThrowException) {
					$error = new Error ( 'Could not fetch the quantity for SKU ' . $this->mSkuId );
					$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
					throw new Exception ( $error->GetErrorMsg () );
				} else {
					return false;
				}
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mQty = $resultObj->Qty;
		}
		return trim($this->mQty);
	}

	//! Sets the quantity for this SKU
	/*!
	* @param [in] newQty : String : The new quantity
	* @return Bool : true if successful
	*/
	function SetQty($newQty) {
		$sql = 'UPDATE tblSku SET Qty = \'' . $newQty . '\' WHERE SKU_ID = ' . $this->mSkuId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the quantity for SKU ' . $this->mSkuId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mQty = $newQty;
		return true;
	}

	//! Returns the SKU Price
	/*!
	* @return Decimal(19,4)
	*/
	function GetSkuPrice() {
		if (! isset ( $this->mSkuPrice )) {
			$get_sku_price_sql = 'SELECT SKU_Price FROM tblSku WHERE SKU_ID = ' . $this->mSkuId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $get_sku_price_sql )) {
				$error = new Error ( 'Could not fetch the price for SKU ' . $this->mSkuId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$sku_price = $result->fetch ( PDO::FETCH_OBJ );
			$this->mSkuPrice = $sku_price->SKU_Price;
			if ($this->mSkuPrice == NULL) {
				// Load from the ProductModel (because the SKU doesnt have its own price)
				// The idea here is that if the SKU price is NULL then it will just be the product place, the reason for not including this
				// in the SKU table is that it would be a lot of redundant storage, and it is only called when the SKU gets ordered
				$get_product_price_sql = '	SELECT Actual_Price FROM tblProduct
											INNER JOIN tblProduct_SKUs ON tblProduct.Product_ID = tblProduct_SKUs.Product_ID
											WHERE tblProduct_SKUs.SKU_ID = ' . $this->mSkuId.'
											LIMIT 1';
				if (! $result = $this->mDatabase->query ( $get_product_price_sql )) {
					$error = new Error ( 'Could not fetch the price (from the parent product) for SKU ' . $this->mSkuId );
					$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
					throw new Exception ( $error->GetErrorMsg () );
				}
				$product_price = $result->fetch ( PDO::FETCH_OBJ );
				$this->mSkuPrice = $product_price->Actual_Price;
			}
		}
		return $this->mSkuPrice;
	}

	//! Sets the price for this SKU
	/*!
	* @param [in] newSkuPrice : String : The new price
	* @return Bool : true if successful
	*/
	function SetSkuPrice($newSkuPrice) {
		$set_price_sql = 'UPDATE tblSku SET SKU_Price = \'' . $newSkuPrice . '\' WHERE SKU_ID = ' . $this->mSkuId;
		if (! $this->mDatabase->query ( $set_price_sql )) {
			$error = new Error ( 'Could not update the price for SKU ' . $this->mSkuId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mSkuPrice = $newSkuPrice;
		return true;
	}

	//! Returns the SKU Attributes associated with this SKU
	/*!
	* @return Array of SKU Attribute objects
	*/
	function GetSkuAttributes() {
		if (! isset ( $this->mSkuAttributes )) {
			$get_related_sku_attributes_sql = 'SELECT SKU_Attribute_ID FROM tblSku_Attributes WHERE SKU_ID = ' . $this->mSkuId;
			if (! $result = $this->mDatabase->query ( $get_related_sku_attributes_sql )) {
				$error = new Error ( 'Could not fetch the SKU Attributes for SKU ' . $this->mSkuId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$related_sku_attributes = $result->fetchAll ( PDO::FETCH_OBJ );
			// For each related SKU Attribute, create a new instance of it and store it in the mSkuAttributes member variable
			foreach ( $related_sku_attributes as $value ) {
				$newSkuAttribute = new SkuAttributeModel ( $value->SKU_Attribute_ID );
				$this->mSkuAttributes [] = $newSkuAttribute;
			}
			if (count ( $related_sku_attributes ) == 0) {
				$this->mSkuAttributes = array ();
			}
		}
		return $this->mSkuAttributes;
	}

	//! Returns the product that this is an SKU of
	function GetParentProduct() {
		$sql = 'SELECT
					tblProduct.Product_ID
				FROM
					tblProduct
				INNER JOIN tblProduct_SKUs
					ON tblProduct.Product_ID = tblProduct_SKUs.Product_ID
				WHERE tblProduct_SKUs.SKU_ID = ' . $this->mSkuId;
		if ($result = $this->mDatabase->query ( $sql )) {
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			if (isset ( $resultObj->Product_ID )) {
				return new ProductModel ( $resultObj->Product_ID );
			} else {
				return NULL;
			}
		} else {
			$error = new Error ( 'Could not fetch the product for SKU ' . $this->mSkuId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	} // End GetParentProduct


	//! Returns the product name (???) that this is an SKU of \todo{find out where this is used and make it proper OO}
	function GetParentProductName() {
		$sql = 'SELECT
					tblProduct_Text.Display_Name
				FROM
					tblProduct_Text
				INNER JOIN tblProduct_SKUs
					ON tblProduct_Text.Product_ID = tblProduct_SKUs.Product_ID
				WHERE tblProduct_SKUs.SKU_ID = ' . $this->mSkuId;
		if ($result = $this->mDatabase->query ( $sql )) {
			$resultObj = $result->fetchObject ();
			return $resultObj->Display_Name;
		} else {
			$error = new Error ( 'Could not fetch the product name for SKU ' . $this->mSkuId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	} // End GetParentProductName


	function HasAttributeValue($value) {
		$sql = 'SELECT SKU_ID FROM tblSku_Attributes WHERE SKU_ID = ' . $this->mSkuId . ' AND Attribute_Value = \'' . trim ( $value ) . '\'';
		if ($result = $this->mDatabase->query ( $sql )) {
			if ($result->fetchColumn ()) {
				return true;
			} else {
				return false;
			}
		} else {
			$error = new Error ( 'Could not queryute SQL ' . $sql );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	} // End HasAttributeValue


	//! Utility function, returns the attribute values of this SKU as a comma-separated list, with brackets around (unless disabled)
	/*!
	 * @param $brackets - Whether or not to add brackets around the combo
	 * @return Str - Eg. (Black, Large)
	 */
	function GetSkuAttributesList($brackets = true) {
		$attributes = $this->GetSkuAttributes ();
		$combo = '';
		foreach ( $attributes as $attribute ) {
			$combo .= $attribute->GetAttributeValue () . ', ';
		}
		$combo = substr ( $combo, 0, strlen ( $combo ) - 2 );
		if ($combo != '' && $brackets) {
			$combo = '(' . trim ( $combo ) . ')';
		}
		return $combo;
	} // End GetSkuAttributesList()


	function IsFinalSku() {
		$parentProduct = $this->GetParentProduct ();
		$parentProductSkus = $parentProduct->GetSkus ();
		// Log problem
		if (count ( $parentProductSkus ) == 0) {
			$fh = fopen ( 'skuModelLog.txt', 'a+' );
			fwrite ( $fh, 'Product ' . $parentProduct->GetDisplayName () . ' has zero SKUs |' );
			fclose ( $fh );
		}
		// Return yes/no
		if (count ( $parentProductSkus ) == 1) {
			return true;
		} else {
			return false;
		}
	}

	//! Returns a list of the attributes this SKU represents - eg. Black/Large. In the form (value1, value2, value3) - with brackets and commas
	function GetAttributeList() {
		$skuAttributes = $this->GetSkuAttributes ();

		// Make display list of attributes
		$skuAttributesList = '';
		if (count ( $skuAttributes ) > 0) {
			$skuAttributesList .= '(';
		}
		foreach ( $skuAttributes as $skuAttribute ) {
			$skuAttributesList .= $skuAttribute->GetAttributeValue () . ', ';
		}
		if (count ( $skuAttributes ) > 0) {
			$skuAttributesList = substr ( $skuAttributesList, 0, (count ( $skuAttributesList ) - 3) );
			$skuAttributesList .= ')';
		}
		return $skuAttributesList;
	} // End GetAttributeList

}

/* DEBUG
$skuId = 2;
try {
	$sku = new SkuModel($skuId);
	echo $sku->GetSkuPrice();
	$sku->SetSkuPrice(50.95);
} catch(Exception $e) {
	echo $e->GetMessage();
}
*/
#print_r($sku->GetSkuAttributes());


?>