<?php

//! Models a single order item
class OrderItemModel {

	//! Constructor, initialises the product ID. Throws an exception if the product doesn't exist
	function __construct($orderItemId) {
		$registry = Registry::getInstance ();
		$this->mPresentationHelper = new PresentationHelper;
		$this->mDatabase = $registry->database;
		$sql = 'SELECT COUNT(Order_Item_ID) AS OrderItemCount FROM tblOrder_Items WHERE Order_Item_ID = '.$orderItemId;
		$result = $this->mDatabase->query ( $sql );
		if ($result) {
			$resultObj = $result->fetch(PDO::FETCH_OBJ);
			if ($resultObj->OrderItemCount > 0) {
				$this->mOrderItemId = $orderItemId;
			} else {
				$error = new Error ( 'Could not initialise order item ID ' . $orderItemId . ' because it does not exist in the database.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		} else {
			$error = new Error ( 'Could not initialise order item ' . $orderItemId . ' because it does not exist in the database.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

	//! Return the product name if a string is needed
	function __toString() {
		return $this->GetDisplayName ();
	}

	function GetOrderItemId() {
		return $this->mOrderItemId;
	}

	//! Returns the display name of the item
	/*!
	* @return String
	*/
	function GetDisplayName() {
		if(!isset($this->mDisplayName)) {
			$sql = 'SELECT Display_Name FROM tblOrder_Items WHERE Order_Item_ID = '.$this->mOrderItemId.' LIMIT 1';
			if (! $result = $this->mDatabase->query($sql)) {
				$error = new Error('Could not fetch the display name for order item '.$this->mOrderItemId);
				$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
				throw new Exception($error->GetErrorMsg());
			}
			$resultObj = $result->fetch(PDO::FETCH_OBJ);
			$this->mDisplayName = $resultObj->Display_Name;
		}
		return $this->mDisplayName;
	}

	//! Sets the display name for the item
	/*!
	* @param [in] newDisplayName : String - The new display name
	* @return Bool : true if successful
	*/
	function SetDisplayName($newDisplayName) {
		$sql = 'UPDATE tblOrder_Items SET Display_Name = \''.$newDisplayName.'\' WHERE Order_Item_ID = '.$this->mOrderItemId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the display name for order item '.$this->mOrderItemId);
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
			}
		$this->mDisplayName = $newDisplayName;
		return true;
	}

	//! Returns the price of the item
	/*!
	* @return Decimal(19,4)
	*/
	function GetPrice() {
		if(!isset($this->mPrice)) {
			$sql = 'SELECT Price FROM tblOrder_Items WHERE Order_Item_ID = '.$this->mOrderItemId.' LIMIT 1';
			if (! $result = $this->mDatabase->query($sql)) {
				$error = new Error('Could not fetch the actual price for order item '.$this->mOrderItemId);
				$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
				throw new Exception($error->GetErrorMsg());
			}
			$resultObj = $result->fetch(PDO::FETCH_OBJ);
			$this->mPrice = $resultObj->Price;
		}
		return $this->mPresentationHelper->Money($this->mPrice);
	}

	//! Sets the price of the item
	/*!
	* @param [in] newPrice Decimal(19,4) : The new price
	* @return Bool : true if successful
	*/
	function SetPrice($newPrice) {
		$sql = 'UPDATE tblOrder_Items SET Price = \''.$newPrice.'\' WHERE Order_Item_ID = '.$this->mOrderItemId;
		if (is_numeric ( $newPrice )) {
			if (! $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not update the price for order item '.$this->mOrderItemId);
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		$this->mPrice = $newPrice;
		return true;
	}

	//! Returns the order ID of the item
	/*!
	* @return Decimal(19,4)
	*/
	function GetOrderId() {
		if(!isset($this->mPrice)) {
			$sql = 'SELECT Order_ID FROM tblOrder_Items WHERE Order_Item_ID = '.$this->mOrderItemId.' LIMIT 1';
			if (! $result = $this->mDatabase->query($sql)) {
				$error = new Error('Could not fetch the order ID for order item '.$this->mOrderItemId);
				$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
				throw new Exception($error->GetErrorMsg());
			}
			$resultObj = $result->fetch(PDO::FETCH_OBJ);
			$this->mOrderId = $resultObj->Order_ID;
		}
		return $this->mOrderId;
	}

	//! Sets the order ID of the item
	/*!
	* @param [in] newOrderId Int : The new order ID
	* @return Bool : true if successful
	*/
	function SetOrderId($newOrderId) {
		$sql = 'UPDATE tblOrder_Items SET Order_ID = \''.$newOrderId.'\' WHERE Order_Item_ID = '.$this->mOrderItemId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the order ID for order item '.$this->mOrderItemId);
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mOrderId = $newOrderId;
		return true;
	}

	//! Returns the shipped status of the item
	/*!
	* @return Bool/Int
	*/
	function GetShipped() {
		if(!isset($this->mShipped)) {
			$sql = 'SELECT Shipped FROM tblOrder_Items WHERE Order_Item_ID = '.$this->mOrderItemId.' LIMIT 1';
			if (! $result = $this->mDatabase->query($sql)) {
				$error = new Error('Could not fetch the shipped info for order item '.$this->mOrderItemId);
				$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
				throw new Exception($error->GetErrorMsg());
			}
			$resultObj = $result->fetch(PDO::FETCH_OBJ);
			$this->mShipped = $resultObj->Shipped;
		}
		return $this->mShipped;
	}

	//! Sets the shipped status of the item
	/*!
	* @param [in] newShippedStatus : Boolean - The new shipped status
	* @return Bool : true if successful
	*/
	function SetShipped($newShippedStatus) {
		$sql = 'UPDATE tblOrder_Items SET Shipped = \''.$newShippedStatus.'\' WHERE Order_Item_ID = '.$this->mOrderItemId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the shipped info for order item '.$this->mOrderItemId);
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mShipped = $newShippedStatus;
		return true;
	}

	//! Returns the package ID of the item (zero indicates not a package)
	/*!
	* @return Decimal(19,4)
	*/
	function GetPackageId() {
		if(!isset($this->mPackageId)) {
			$sql = 'SELECT Package_ID FROM tblOrder_Items WHERE Order_Item_ID = '.$this->mOrderItemId.' LIMIT 1';
			if (! $result = $this->mDatabase->query($sql)) {
				$error = new Error('Could not fetch the package ID for order item '.$this->mOrderItemId);
				$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
				throw new Exception($error->GetErrorMsg());
			}
			$resultObj = $result->fetch(PDO::FETCH_OBJ);
			$this->mPackageId = $resultObj->Package_ID	;
		}
		return $this->mPackageId;
	}

	//! Sets the shipped status of the item
	/*!
	* @param [in] newShippedStatus : Boolean - The new shipped status
	* @return Bool : true if successful
	*/
	function SetPackageId($newPackageId) {
		$sql = 'UPDATE tblOrder_Items SET Package_ID = \''.$newPackageId.'\' WHERE Order_Item_ID = '.$this->mOrderItemId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the package ID for order item '.$this->mOrderItemId);
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
			}
		$this->mPackageId = $newPackageId;
		return true;
	}

	//! Returns which package the item belongs to (zero indicates not in a package)
	/*!
	* @return Int
	*/
	function GetPackageProduct() {
		if(!isset($this->mPackageProduct)) {
			$sql = 'SELECT Package_Product FROM tblOrder_Items WHERE Order_Item_ID = '.$this->mOrderItemId.' LIMIT 1';
			if (! $result = $this->mDatabase->query($sql)) {
				$error = new Error('Could not fetch the package ID for order item '.$this->mOrderItemId);
				$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
				throw new Exception($error->GetErrorMsg());
			}
			$resultObj = $result->fetch(PDO::FETCH_OBJ);
			$this->mPackageProduct = $resultObj->Package_Product;
		}
		return $this->mPackageProduct;
	}

	//! Sets the package ID that an item belongs to
	/*!
	* @param [in] newPackageProduct : Boolean - The new package ID
	* @return Bool : true if successful
	*/
	function SetPackageProduct($newPackageProduct) {
		$sql = 'UPDATE tblOrder_Items SET Package_Product = \''.$newPackageProduct.'\' WHERE Order_Item_ID = '.$this->mOrderItemId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the package product ID for order item '.$this->mOrderItemId);
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
			}
		$this->mPackageProduct = $newPackageProduct;
		return true;
	}

	//! Returns which package the item is an upgrade for (zero indicates not in a package)
	/*!
	* @return Int
	*/
	function GetPackageUpgrade() {
		if(!isset($this->mPackageUpgrade)) {
			$sql = 'SELECT Package_Upgrade FROM tblOrder_Items WHERE Order_Item_ID = '.$this->mOrderItemId.' LIMIT 1';
			if (! $result = $this->mDatabase->query($sql)) {
				$error = new Error('Could not fetch the package upgrade ID for order item '.$this->mOrderItemId);
				$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
				throw new Exception($error->GetErrorMsg());
			}
			$resultObj = $result->fetch(PDO::FETCH_OBJ);
			$this->mPackageUpgrade = $resultObj->Package_Upgrade;
		}
		return $this->mPackageUpgrade;
	}

	//! Sets the package ID that an item is an upgrade for
	/*!
	* @param [in] newPackageProduct : Boolean - The new package ID
	* @return Bool : true if successful
	*/
	function SetPackageUpgrade($newPackageUpgrade) {
		$sql = 'UPDATE tblOrder_Items SET Package_Upgrade = \''.$newPackageUpgrade.'\' WHERE Order_Item_ID = '.$this->mOrderItemId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the package upgrade ID for order item '.$this->mOrderItemId);
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
			}
		$this->mPackageUpgrade = $newPackageUpgrade;
		return true;
	}

	//! Returns which sage code for the item
	/*!
	* @return String
	*/
	function GetSageCode() {
		if(!isset($this->mSageCode)) {
			$sql = 'SELECT Sage_Code FROM tblOrder_Items WHERE Order_Item_ID = '.$this->mOrderItemId.' LIMIT 1';
			if (! $result = $this->mDatabase->query($sql)) {
				$error = new Error('Could not fetch the sage code for order item '.$this->mOrderItemId);
				$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
				throw new Exception($error->GetErrorMsg());
			}
			$resultObj = $result->fetch(PDO::FETCH_OBJ);
			$this->mSageCode = $resultObj->Sage_Code;
		}
		return $this->mSageCode;
	}

	//! Sets the sage code for the item
	/*!
	* @param [in] newSageCode : String - The new sage code
	* @return Bool : true if successful
	*/
	function SetSageCode($newSageCode) {
		$sql = 'UPDATE tblOrder_Items SET Sage_Code = \''.$newSageCode.'\' WHERE Order_Item_ID = '.$this->mOrderItemId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the sage code for order item '.$this->mOrderItemId);
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
			}
		$this->mSageCode = $newSageCode;
		return true;
	}

	//! Returns whether an item is taxable
	/*!
	* @return String
	*/
	function GetTaxable() {
		if(!isset($this->mTaxable)) {
			$sql = 'SELECT Taxable FROM tblOrder_Items WHERE Order_Item_ID = '.$this->mOrderItemId.' LIMIT 1';
			if (! $result = $this->mDatabase->query($sql)) {
				$error = new Error('Could not fetch the taxable info for order item '.$this->mOrderItemId);
				$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
				throw new Exception($error->GetErrorMsg());
			}
			$resultObj = $result->fetch(PDO::FETCH_OBJ);
			$this->mTaxable = $resultObj->Taxable;
		}
		return $this->mTaxable;
	}

	//! Sets the sage code for the item
	/*!
	* @param [in] newSageCode : String - The new sage code
	* @return Bool : true if successful
	*/
	function SetTaxable($newTaxable) {
		$sql = 'UPDATE tblOrder_Items SET Taxable = \''.$newTaxable.'\' WHERE Order_Item_ID = '.$this->mOrderItemId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the taxable info for order item '.$this->mOrderItemId);
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
			}
		$this->mTaxable = $newTaxable;
		return true;
	}

	//! To be used only with a package - returns the price inclusive of upgrades
	/*!
	* @return Decimal : The total price of the package item
	*/
	function GetPackagePrice() {
		$basePrice = $this->GetPrice();
		$sql = 'SELECT SUM(Price) AS "PackagePrice" FROM tblOrder_Items WHERE Package_Upgrade = '.$this->GetPackageId().' AND Order_ID = '.$this->GetOrderId();
		if(!$result = $this->mDatabase->query($sql)) {
			$error = new Error('Could not fetch the total price for package item '.$this->mOrderItemId);
			$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
			throw new Exception($error->GetErrorMsg());
		}
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		$upgradesPrice = $resultObj->PackagePrice;
		return $this->mPresentationHelper->Money($basePrice + $upgradesPrice);
	}

	//! Returns the number of items in the item (assuming it is a package)
	function GetItemCount() {
		$sql = 'SELECT COUNT(Order_Item_ID) AS ItemCount
		FROM tblOrder_Items WHERE Order_ID = '.$this->GetOrderId().' AND (Package_Product = '.$this->GetPackageId().' OR Package_Upgrade = '.$this->GetPackageId().')';
		if (! $result = $this->mDatabase->query($sql)) {
			$error = new Error('Could not fetch the package item count order item '.$this->mOrderItemId);
			$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
			throw new Exception($error->GetErrorMsg());
		}
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		return $resultObj->ItemCount;
	}

	//! Gets the order item that is the package which contains this item
	function GetParentPackageItem() {
		if($this->GetPackageProduct()) { $constraint = $this->GetPackageProduct(); } else { $constraint = $this->GetPackageUpgrade(); }
		$sql = 'SELECT Order_Item_ID FROM tblOrder_Items WHERE Package_ID = '.$constraint.' and Order_ID = '.$this->GetOrderId();
		if (! $result = $this->mDatabase->query($sql)) {
			$error = new Error('Could not fetch the package item count order item '.$this->mOrderItemId);
			$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
			throw new Exception($error->GetErrorMsg());
		}
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		return new OrderItemModel($resultObj->Order_Item_ID);
	}

	//! If the item is in a package, this returns its "share" of the package price
	function GetPackageItemPrice() {
		$packageItemCount = $this->GetParentPackageItem()->GetItemCount();
		return $this->GetParentPackageItem()->GetPrice()/$packageItemCount;
	}

	// A bit naughty - checks to see if this product is a 'Non Stock Item' (3-5 day) based on the recorded sage code.
	// Would be better to change the DB structure to add a flag in...
	// Even more naughty it used the 'on clearance' flag to indicate 'non-stock'
	function IsNonStockItem() {
		$sql = 'SELECT On_Clearance AS NonStockFlag
				FROM tblProduct
				INNER JOIN tblProduct_SKUs ON tblProduct.Product_ID = tblProduct_SKUs.Product_ID
				INNER JOIN tblSku ON tblSku.SKU_ID = tblProduct_SKUs.SKU_ID
				WHERE tblSku.Sage_Code = \''.$this->GetSageCode().'\'';
		if (! $result = $this->mDatabase->query($sql)) {
			$error = new Error('Could not fetch the package item count order item '.$this->mOrderItemId);
			$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
			throw new Exception($error->GetErrorMsg());
		}
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		return @$resultObj->NonStockFlag;
	}


} // End OrderItemModel

?>