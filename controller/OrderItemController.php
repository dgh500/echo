<?php

class OrderItemController extends Controller {

	function __construct() {
		parent::__construct();
	}

	//! Gets all the packages in a given order
	/*!
	 * @param	$order - Obj : OrderModel - The order to get the packages for
	 * @return	$retArr - Array of OrderItemModel Objects - The package items for this order
	 */
	function GetPackagesForOrder($order,$shippedOnly=false) {
		if($shippedOnly) { $shippedSql = ' AND Shipped = \'1\''; } else { $shippedSql = ''; }
		$sql = 'SELECT Order_Item_ID FROM tblOrder_Items WHERE Package_ID IS NOT NULL AND Package_ID <> \'0\' AND Order_ID = '.$order->GetOrderId().$shippedSql;
		if(!$result = $this->mDatabase->query($sql)) {
			$error = new Error ( 'Could not fetch the package items for order: '.$orderId);
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$resultObjSet = $result->fetchAll(PDO::FETCH_OBJ);
		foreach($resultObjSet as $resultObj) {
			$retArr[] = new OrderItemModel($resultObj->Order_Item_ID);
		}
		(!isset($retArr) ? $retArr = array() : NULL );
		return $retArr;
	} // End GetPackagesForOrder

	//! Gets all the products in a given order
	/*!
	 * @param	$order - Obj : OrderModel - The order to get the products for
	 * @return	$retArr - Array of OrderItemModel Objects - The product items for this order
	 */
	function GetProductsForOrder($order,$shippedOnly=false) {
		if($shippedOnly) { $shippedSql = ' AND Shipped = \'1\''; } else { $shippedSql = ''; }
		$sql = 'SELECT Order_Item_ID FROM tblOrder_Items WHERE
								(Package_ID IS NULL OR Package_ID = \'0\')
							AND (Package_Product IS NULL OR Package_Product = \'0\')
							AND (Package_Upgrade IS NULL OR Package_Upgrade = \'0\')
							'.$shippedSql.'
							AND Order_ID = '.$order->GetOrderId().'
							ORDER BY Price DESC';
		if(!$result = $this->mDatabase->query($sql)) {
			$error = new Error ( 'Could not fetch the product items for order: '.$orderId);
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$resultObjSet = $result->fetchAll(PDO::FETCH_OBJ);
		foreach($resultObjSet as $resultObj) {
			$retArr[] = new OrderItemModel($resultObj->Order_Item_ID);
		}
		(!isset($retArr) ? $retArr = array() : NULL );
		return $retArr;
	} // End GetPackagesForOrder

	//! Attempts to return a ProductModel for an OrderItem
	/*!
	 * @param $orderItem - The order item to get the product for
	 8 @return Either a ProductModel or False
	 */
	function GetProductForOrderItem($orderItem) {
		$sql = 'SELECT SKU_ID
				FROM tblSku
				WHERE Sage_Code = \''.$orderItem->GetSageCode().'\'';
		if(!$result = $this->mDatabase->query($sql)) {
			$error = new Error ( 'Could not fetch the SKU for order item: '.$orderItem->GetSageCode());
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		if($resultObj && isset($resultObj->SKU_ID)) {
			$sku = new SkuModel($resultObj->SKU_ID);
			return $sku->GetParentProduct();
		} else {
			return false;
		}
	} // End GetProductsForOrderItem

	//! Gets the contents of a package item in an order
	/*!
	 * @param	$packageItem - Obj : OrderItemModel - The package item to get the contents of
	 * @return	$retArr - Array of OrderItemModel Objects - The contents of the order
	 */
	function GetContentsOfPackageItem($packageItem,$shippedOnly=false) {
		if($shippedOnly) { $shippedSql = ' AND Shipped = \'1\''; } else { $shippedSql = ''; }
		$sql = 'SELECT Order_Item_ID FROM tblOrder_Items WHERE Package_Product = '.$packageItem->GetPackageId().' AND Order_ID = '.$packageItem->GetOrderId().$shippedSql;
		if(!$result = $this->mDatabase->query($sql)) {
			$error = new Error ( 'Could not fetch the package contents for package: '.$packageItem->GetPackageId());
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$resultObjSet = $result->fetchAll(PDO::FETCH_OBJ);
		foreach($resultObjSet as $resultObj) {
			$retArr[] = new OrderItemModel($resultObj->Order_Item_ID);
		}
		(!isset($retArr) ? $retArr = array() : NULL );
		return $retArr;
	} // End GetContentsOfPackageItem

	//! Gets the contents of a package item in an order
	/*!
	 * @param	$packageItem - Obj : OrderItemModel - The package item to get the upgrades of
	 * @return	$retArr - Array of OrderItemModel Objects - The upgrades of the order
	 */
	function GetUpgradesOfPackageItem($packageItem,$shippedOnly=false) {
		if($shippedOnly) { $shippedSql = ' AND Shipped = \'1\''; } else { $shippedSql = ''; }
		$sql = 'SELECT Order_Item_ID FROM tblOrder_Items WHERE Package_Upgrade = '.$packageItem->GetPackageId().' AND Order_ID = '.$packageItem->GetOrderId().$shippedSql;
		if(!$result = $this->mDatabase->query($sql)) {
			$error = new Error ( 'Could not fetch the package upgrades for package: '.$packageItem->GetPackageId());
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$resultObjSet = $result->fetchAll(PDO::FETCH_OBJ);
		foreach($resultObjSet as $resultObj) {
			$retArr[] = new OrderItemModel($resultObj->Order_Item_ID);
		}
		(!isset($retArr) ? $retArr = array() : NULL );
		return $retArr;
	} // End GetContentsOfPackageItem

	//! Returns true if the whole order has been shipped, false otherwise
	function WholeOrderShipped($order) {
		$sql = 'SELECT COUNT(Order_Item_ID) AS UnshippedCount FROM tblOrder_Items WHERE Shipped = \'0\' AND Order_ID = '.$order->GetOrderId();
		if(!$result = $this->mDatabase->query($sql)) {
			$error = new Error ( 'Could not fetch the unshipped items for order: '.$packageItem->GetOrderId());
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		if($resultObj->UnshippedCount == 0) {
			return true;
		} else {
			return false;
		}
	}

	//! Gets the price of the items that HAVE been shipped
	function GetTotalShippedPrice($order) {
		$sql = 'SELECT SUM(Price) AS TotalPrice FROM tblOrder_Items WHERE Shipped = \'1\' AND Order_ID = '.$order->GetOrderId();
		if(!$result = $this->mDatabase->query($sql)) {
			$error = new Error ( 'Could not fetch the shipped items for order: '.$packageItem->GetOrderId());
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		return $resultObj->TotalPrice;
	}

	//! Takes a sage code and returns an array with the similar sage codes in and the product name
	function LookupProductBySageCode($sageCode) {
		$sql = 'SELECT
					tblSKU.Sage_Code,
					tblSKU.SKU_Price,
					tblProduct_Text.Display_Name,
					tblSku_Attributes.Attribute_Value
				FROM
					tblSKU
				INNER JOIN tblProduct_SKUs ON tblProduct_SKUs.SKU_ID = tblSKU.SKU_ID
				INNER JOIN tblProduct_Text ON tblProduct_SKUs.Product_ID = tblProduct_Text.Product_ID
				LEFT JOIN tblSku_Attributes ON tblSku_Attributes.SKU_ID = tblSKU.SKU_ID
				WHERE
					tblSKU.Sage_Code LIKE \''.$sageCode.'%\'
				';
		if(!$result = $this->mDatabase->query($sql)) {
			$error = new Error ( 'Could not fetch the sage codes for product');
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$i=0;
		$resultObjSet = $result->fetchAll(PDO::FETCH_OBJ);
		$retArr = array();
		foreach($resultObjSet as $resultObj) {
			if($resultObj->Sage_Code) {
				$retArr[$i]['Sage_Code'] = $resultObj->Sage_Code;
			} else { $retArr[$i]['Sage_Code'] = 'DUMMYSAGECODE'; }
			$retArr[$i]['Price'] = $resultObj->SKU_Price;
			$retArr[$i]['Display_Name'] = $resultObj->Display_Name;
			$retArr[$i]['Attribute_Value'] = $resultObj->Attribute_Value;
			$i++;
		}
		return $retArr;
	} // End LookupProductBySageCode

} // End OrderItemController


?>