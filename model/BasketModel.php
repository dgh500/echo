<?php

//! Models a single basket/shopping cart. The Basket ID will be the PHP session id
class BasketModel {

	//! Int : Unique basket identifier
	var $mBasketId;
	//! Array of SKUs that are in the basket
	var $mSkus;
	//! Int : Unix timestamp - when the basket was created
	var $mCreated;
	//! Decimal : The current total of the basket
	var $mTotal;
	//! Obj:PDO - database connection used to access database level
	var $mDatabase;

	//! Constructor, initialises the basket
	/*!
	 * @param $basketId [in] Int
	 * @return Void
	 */
	function __construct($basketId) {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
		$this->mMoneyHelper = new MoneyHelper;
		$check_sql = 'SELECT COUNT(Basket_ID) FROM tblBasket WHERE Basket_ID = \'' . $basketId . '\'';
		if (! $result = $this->mDatabase->query ( $check_sql )) {
			$error = new Error ( 'Could not construct basket.' );
		#	die(var_dump($this->mDatabase->errorInfo()));
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		if ($result->fetchColumn () > 0) {
			$this->mBasketId = $basketId;
		} else {
			$error = new Error ( 'Could not initialise basket ' . $basketId . ' because it does not exist in the database.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

	//! Sets a SKU as 'shipped' - so the customer is charged for it and it displays properly
	/*!
	 * @param $sku [in] Obj:SkuModel - the SKU to set as shipped
	 * @return Boolean - True if successful / Exception
	 */
	function SetShipped($sku) {
		$sql = 'UPDATE tblBasket_Skus SET Shipped = \'1\' WHERE SKU_ID = ' . $sku->GetSkuId () . ' AND Basket_ID = \'' . $this->mBasketId . '\'';
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not set shipped for SKU ' . $sku->GetSkuId () . ' with SQL: ' . $sql );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		return true;
	}

	//! Sets a package as 'shipped' - so the customer is charged for it and it displays properly
	/*!
	 * @param $package [in] Obj:PackageModel - the package to set as shipped
	 * @return Boolean - True if successful / Exception
	 */
	function SetPackageShipped($package) {
		$sql = 'UPDATE tblBasket_Packages SET Shipped = \'1\' WHERE Package_ID = ' . $package->GetPackageId () . ' AND Basket_ID = \'' . $this->mBasketId . '\'';
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not set shipped for package ' . $package->GetPackageId () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		foreach ( $package->GetContents () as $product ) {
			$skus = $product->GetSkus ();
			foreach ( $skus as $sku ) {
				if ($this->InBasket ( $sku )) {
					// Meaning if there is a problem (Eg. the product has been upgraded) then dont throw the exception
					try {
						$this->SetShipped ( $sku );
					} catch ( Exception $e ) {
						//null
					}
				}
			}
		}
		return true;
	}

	//! Checks whether a given SKU is a package upgrade
	/*!
	 * @param $sku [in] Obj:SkuModel - the SKU to check
	 * @return Boolean - True if the SKU is a package upgrade, false otherwise
	 */
	function IsPackageUpgrade($sku) {
		$sql = 'SELECT COUNT(SKU_ID) AS SkuCount FROM tblBasket_Skus WHERE SKU_ID = ' . $sku->GetSkuId () . ' AND Basket_ID = \'' . $this->mBasketId . '\' AND Package_Upgrade = \'1\'';
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not fetch the upgrade status for SKU ' . $sku->GetSkuId () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$shipped = $result->fetch ( PDO::FETCH_OBJ );
		if ($shipped->SkuCount > 0) {
			return true;
		} else {
			return false;
		}
	}

	//! Checks whether a given SKU is a package item
	/*!
	 * @param $sku [in] Obj:SkuModel - the SKU to check
	 * @return Boolean - True if the SKU is a package item, false otherwise
	 */
	function IsPackage($sku) {
		$sql = 'SELECT COUNT(SKU_ID) AS SkuCount FROM tblBasket_Skus WHERE SKU_ID = ' . $sku->GetSkuId () . ' AND Basket_ID = \'' . $this->mBasketId . '\' AND Package = \'1\'';
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not fetch the upgrade status for SKU ' . $sku->GetSkuId () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$shipped = $result->fetch ( PDO::FETCH_OBJ );
		if ($shipped->SkuCount > 0) {
			return true;
		} else {
			return false;
		}
	}

	//! Checks whether a given SKU has been shipped
	/*!
	 * @param $sku [in] Obj:SkuModel - the SKU to check
	 * @return Boolean - True if the SKU has been shipped, false otherwise
	 */
	function IsShipped($sku) {
		$sql = 'SELECT Shipped FROM tblBasket_Skus WHERE SKU_ID = ' . $sku->GetSkuId () . ' AND Basket_ID = \'' . $this->mBasketId . '\' LIMIT 1';
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not fetch the shipped status for SKU ' . $sku->GetSkuId () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$shipped = $result->fetch ( PDO::FETCH_OBJ );
		if ($shipped->Shipped == '1') {
			return true;
		} else {
			return false;
		}
	}

	//! Checks whether a given package has been shipped
	/*!
	 * @param $package [in] Obj:PackageModel - the package to check
	 * @return Boolean - True if the pacakage has been shipped, false otherwise
	 */
	function IsShippedPackage($package) {
		$sql = 'SELECT Shipped FROM tblBasket_Packages WHERE Package_ID = ' . $package->GetPackageId () . ' AND Basket_ID = \'' . $this->mBasketId . '\' LIMIT 1';
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not fetch the shipped status for package ' . $package->GetPackageId () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$shipped = $result->fetch ( PDO::FETCH_OBJ );
		if ($shipped->Shipped == '1') {
			return true;
		} else {
			return false;
		}
	}

	//! Gets the order associated with a basket, returns false on no order
	/*!
	 * @return Obj:OrderModel
	 */
	function GetOrder() {
		$sql = 'SELECT Order_ID FROM tblOrder WHERE Basket_ID = \'' . $this->mBasketId . '\' ORDER BY Created_Date DESC LIMIT 1';
		if ($result = $this->mDatabase->query ( $sql )) {
			$resultObj = $result->fetchObject ();
			if ($resultObj) {
				$newOrder = new OrderModel ( $resultObj->Order_ID );
				return $newOrder;
			} else {
				return false;
			}
		} else {
			$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

	//! Adds a SKU to a basket (incorporating multibuys)
	/*!
	 * @param [in] sku : Obj:SkuModel 	- The stock keeping unit to add to the basket (Eg. Large Wetsuit)
	 * @param [in] packageIndicator 	- Boolean, optional. If present then it will be marked as part of a package
	 * @param [in] changedPrice 		- Decimal if used, else boolean false
	 * @param [in] packageUpgrade 		- Bool - Indicator of a package upgrade
	 * @param [in] vetFree 				- Bool - Default false, if true then VAT is removed from whatever the price should be
	 * @return Boolean 					- True if successful, exception thrown if not
	 */
	function AddToBasket($sku, $packageIndicator = false, $changedPrice = false, $packageUpgrade = false, $vatFree=false) {
		if ($packageIndicator === false && $packageUpgrade === false) {
			$msg = 'Neither package nor upgrade.<br />';
			$sql = 'INSERT INTO tblBasket_Skus (`Basket_ID`,`SKU_ID`,`Package`,`Package_Upgrade`) VALUES (\'' . $this->mBasketId . '\',\'' . $sku->GetSkuId () . '\',\'0\',\'0\')';
		} elseif ($packageIndicator === true && $packageUpgrade === false) {
			$msg = 'Package, not upgrade';
			$sql = 'INSERT INTO tblBasket_Skus (`Basket_ID`,`SKU_ID`,`Package`,`Package_Upgrade`) VALUES (\'' . $this->mBasketId . '\',\'' . $sku->GetSkuId () . '\',\'1\',\'0\')';
		} elseif ($packageIndicator === false && $packageUpgrade === true) {
			$msg = 'Package Upgrade';
			$sql = 'INSERT INTO tblBasket_Skus (`Basket_ID`,`SKU_ID`,`Package`,`Package_Upgrade`) VALUES (\'' . $this->mBasketId . '\',\'' . $sku->GetSkuId () . '\',\'0\',\'1\')';
		}
		if ($sku->GetParentProduct ()->GetMultibuy ()) {
			$multibuy = '(Multibuy)';
		} else {
			$multibuy = '';
		}
		#echo $msg.' - '.$multibuy.'<br />';


		if (FALSE === $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the basket: ' . $this->mBasketId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}

		// Just add the normal SKU price if no override is in place
		if ($changedPrice === false) {
			$newTotal = $this->GetTotal() + $sku->GetSkuPrice();
			$this->ChangePriceForSku($sku,$sku->GetSkuPrice(),false,false);
			#echo "$newTotal = ".$this->GetTotal()." + $changedPrice<br />";
			$this->SetTotal($newTotal);
		}

		// For when it isn't anything to do with a package but staff have changed the price (Last condition ensures multibuy has priority)
		// Removed  && !$sku->GetParentProduct ()->GetMultibuy () condition so that VAT-Free over-rides the multibuy
		if ($changedPrice !== false && $packageIndicator === false && $packageUpgrade === false && !$sku->GetParentProduct()->GetMultibuy()) {
			$newTotal = $this->GetTotal () + $changedPrice;
			$this->ChangePriceForSku ( $sku, $changedPrice, false, false );
			#echo "$newTotal = ".$this->GetTotal()." + $changedPrice<br />";
			$this->SetTotal ( $newTotal );
		}

		// Amend using changed price if need to
		if ($changedPrice !== false && ($packageIndicator === true || $packageUpgrade === true)) {
			if ($packageIndicator === true && $packageUpgrade === false) {
				$this->ChangePriceForSku ( $sku, $changedPrice, true, false );
			} elseif ($packageIndicator === false && $packageUpgrade === true) {
				$this->ChangePriceForSku ( $sku, $changedPrice, false, true );
			} elseif ($packageIndicator === false && $packageUpgrade === false) {
				$this->ChangePriceForSku ( $sku, $changedPrice, false, false );
			}
			$newTotal = $this->GetTotal () + $changedPrice;
			#echo "$newTotal = ".$this->GetTotal()." + $changedPrice<br />";
			$this->SetTotal ( $newTotal );
		}

		// Multibuy discount
		if ($sku->GetParentProduct()->GetMultibuy () && $packageIndicator === false && $packageUpgrade === false) {
			// Check to see if any multibuy discounts need to be applied
			$numberAlreadyInBasket = $this->ProductsInBasket ( $sku->GetParentProduct (), true );
			// Get the price for this amount
			$multibuyDiscountPrice = $sku->GetParentProduct ()->GetMultibuyPriceFor ( $numberAlreadyInBasket );
			// Change the price
			if($vatFree === false) {
				$this->ChangePriceForSku ( $sku, $multibuyDiscountPrice, false, false );
			} else {
				$this->ChangePriceForSku ( $sku, $this->mMoneyHelper->RemoveVAT($multibuyDiscountPrice), false, false );
			}
			// Also change the price for the other related SKUs
			$productCount = 0;
			foreach ( $sku->GetParentProduct ()->GetSkus () as $checkSku ) {
				if ($this->InBasket ( $checkSku )) {
					try {
						if($vatFree === false) {
							$this->ChangePriceForSku($checkSku,$multibuyDiscountPrice,false,false);
						} else {
							$this->ChangePriceForSku($checkSku,$this->mMoneyHelper->RemoveVAT($multibuyDiscountPrice),false,false);
						}
					} catch ( Exception $e ) {
						// Null
					}
					$productCount ++;
				}
			}
			// Need to adjust for the previously more expensive quantity of SKUs
			$multibuyAdjustmentQuantity = $numberAlreadyInBasket - 1;
			$prevMultibuyUnitPrice = $sku->GetParentProduct()->GetMultibuyPriceFor($multibuyAdjustmentQuantity);
			#die('Qty: '.$multibuyAdjustmentQuantity);
			$multibuyAdjustment = round ( (floatval ( $multibuyAdjustmentQuantity ) * floatval ( $prevMultibuyUnitPrice )) - (floatval ( $multibuyAdjustmentQuantity ) * floatval ( $multibuyDiscountPrice )), 2 );
			#echo "($multibuyAdjustmentQuantity * $prevMultibuyUnitPrice) - ($multibuyAdjustmentQuantity * $multibuyDiscountPrice)<br />";
			#echo "(".$this->GetTotal()." + $multibuyDiscountPrice) - $multibuyAdjustment";
			#die('Multibuy Adj = ('.$multibuyAdjustmentQuantity.' * '.$prevMultibuyUnitPrice.') - ('.$multibuyAdjustmentQuantity.' * '.$multibuyDiscountPrice.')');
			if($vatFree === false) {
				$newTotal = ($this->GetTotal () + $multibuyDiscountPrice) - $multibuyAdjustment;
			} else {
				$newTotal = ($this->GetTotal () + $this->mMoneyHelper->RemoveVAT($multibuyDiscountPrice)) - $this->mMoneyHelper->RemoveVAT($multibuyAdjustment);
			}
			#echo "(".$this->GetTotal()." + $multibuyDiscountPrice) - $multibuyAdjustment";
			$this->SetTotal ( $newTotal );
		}
		#echo '<br />'.$sku->GetParentProduct()->GetDisplayName().'<br /> ---------- <br />';
		return true;
	}

	//! Decrements the number of SKU supplied within a basket
	/*!
	 * @param [in] sku : Obj:SkuModel - The SKU to decrement
	 * @return Boolean : True if successful, exception thrown if not
	 */
	function DecFromBasket($sku,$vatFree=false) {
		$sql = 'DELETE FROM tblBasket_Skus WHERE Basket_ID = \'' . $this->mBasketId . '\' AND SKU_ID = ' . $sku->GetSkuId () . ' AND Package = \'0\' AND Package_Upgrade = \'0\' LIMIT 1';
		if (FALSE === $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not decrement the basket: ' . $this->mBasketId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}

		// Multibuy discount (SKU has aready been removed)
		if ($sku->GetParentProduct ()->GetMultibuy ()) {
			// The number of SKUs remaining
			$numberRemainingInBasket = $this->ProductsInBasket ( $sku->GetParentProduct (), true );
			// Get the price for this amount
			$multibuyDiscountPrice = $sku->GetParentProduct ()->GetMultibuyPriceFor ( $numberRemainingInBasket );
			// Change the price
			$this->ChangePriceForSku ( $sku, $multibuyDiscountPrice );
			// Also change the price for the other related SKUs
			$productCount = 0;
			foreach ( $sku->GetParentProduct ()->GetSkus () as $checkSku ) {
				if ($this->InBasket ( $checkSku )) {
					if($vatFree) {
						$this->ChangePriceForSku ( $checkSku, $this->mMoneyHelper->RemoveVAT($multibuyDiscountPrice));
					} else {
						$this->ChangePriceForSku ( $checkSku, $multibuyDiscountPrice );
					}
					$productCount ++;
				}
			}
			// Need to adjust for the previously more expensive quantity of SKUs
			$previousQuantityInBasket = $numberRemainingInBasket + 1;
			$priceOfSingleSKUBeforeDecrement = $sku->GetParentProduct ()->GetMultibuyPriceFor ( $previousQuantityInBasket );

			$multibuyAdjustment = ($priceOfSingleSKUBeforeDecrement - $multibuyDiscountPrice) * $numberRemainingInBasket;
			#die('Multibuy Adj = ('.$multibuyAdjustmentQuantity.' * '.$prevMultibuyUnitPrice.') - ('.$multibuyAdjustmentQuantity.' * '.$multibuyDiscountPrice.')');
			$newTotal = ($this->GetTotal () - $priceOfSingleSKUBeforeDecrement) - $multibuyAdjustment;
			$this->SetTotal ( $newTotal );
		} else {
			if($this->HasOverruledSku($sku,false,false)) {
				$newTotal = $this->GetTotal () - $this->GetOverruledSkuPrice ($sku,false,false);
			} else {
				$newTotal = $this->GetTotal () - $sku->GetSkuPrice ();
			}
			$this->SetTotal ( $newTotal );
		}

		return true;
	}

	//! Removes a SKU from a basket - note this removes ALL SKUs of this ID from the basket, so if someone has order 2 SKUs that are the same, BOTH are removed - use DecFromBasket to just remove one at a time
	/*!
	 * @param [in] sku : Obj:SkuModel - The stock keeping unit to remove from the basket (Eg. Large Wetsuit)
	 * @return Boolean : True if successful, exception thrown if not
	 */
	function RemovePackageFromBasket($package) {
		$numberOfPackages = $this->PackagesInBasket ( $package );
		$sql = 'DELETE FROM tblBasket_Packages WHERE Package_ID = ' . $package->GetPackageId () . ' AND Basket_ID = \'' . $this->mBasketId . '\'';
		if (FALSE === $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the basket: ' . $this->mBasketId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		// Loop over package contents - if the SKU is not found then look for an upgrade - when you find one then get the upgrade price and add it to the package price
		$upgradeAdjustment = 0;
		$alreadyUpgradedSkus = array();
		for($i=0;$i<$numberOfPackages;$i++) {
			foreach($package->GetContents() as $contentProduct) {
				foreach($contentProduct->GetSkus() as $sku) {
					if($this->InBasket($sku)) {
						// NULL
					} else {
						// Has been upgraded - look for the upgrade SKU
						$found=false;
						foreach($package->GetUpgradesFor($contentProduct) as $upgrade) {
							foreach($upgrade->GetSkus() as $upgradeSku) {
								if($found==false) {
									if($this->InBasket($upgradeSku) && !in_array($upgradeSku,$alreadyUpgradedSkus)) {
										// Get the upgrade price
										$upgradePrice = $package->GetUpgradePrice($contentProduct,$upgrade);
										$alreadyUpgradedSkus[] = $upgradeSku;
									#	$fh = fopen('foo.txt','a+'); fwrite($fh,$upgradePrice.' - to upgrade '.$contentProduct->GetDisplayName().' to '.$upgrade->GetDisplayName().' - ');

										$upgradeAdjustment += $upgradePrice;
										$found=true;
									}
								}
							}
						}
					}
				}
			}
		}

		// Add in the upgrade adjustment
		$fullPackagePrice = ($numberOfPackages * $package->GetActualPrice()) + $upgradeAdjustment;
		$newTotal = $this->GetTotal () - $fullPackagePrice;
		$this->SetTotal ( $newTotal );

		// Contents...
		foreach ( $package->GetContents () as $contentProduct ) {
			foreach ( $contentProduct->GetSkus () as $sku ) {
				if ($this->InBasket ( $sku )) {
					// Remove it
					$this->RemoveFromBasket ( $sku, false, false );
				}
			}
		}

		// Upgrades...
		foreach ( $package->GetUpgrades () as $upgradeProduct ) {
			foreach ( $upgradeProduct->GetSkus () as $sku ) {
				if ($this->InBasket ( $sku )) {
					// Remove it
					$this->RemoveFromBasket ( $sku, true, true );
				}
			}
		}
		return true;
	}

	//! Removes a SKU from a basket - note this removes ALL SKUs of this ID from the basket, so if someone has order 2 SKUs that are the same, BOTH are removed - use DecFromBasket to just remove one at a time
	/*!
	 * @param [in] sku : Obj:SkuModel - The stock keeping unit to remove from the basket (Eg. Large Wetsuit)
	 * @return Boolean : True if successful, exception thrown if not
	 */
	function RemoveFromBasket($sku, $changeTotal = true, $packageUpgrade = false) {
		$numberOfSkus = $this->SkusInBasket ( $sku );
		if ($packageUpgrade) {
			$sql = 'DELETE FROM tblBasket_Skus WHERE SKU_ID = ' . $sku->GetSkuId () . ' AND Basket_ID = \'' . $this->mBasketId . '\' AND Package_Upgrade = \'1\'';
		} else {
			$sql = 'DELETE FROM tblBasket_Skus WHERE SKU_ID = ' . $sku->GetSkuId () . ' AND Basket_ID = \'' . $this->mBasketId . '\' AND Package_Upgrade = \'0\'';
		}
	/*	if ($this->HasOverruledSku ( $sku, $packageUpgrade )) {
			$newTotal = $this->GetTotal () - ($numberOfSkus * $this->GetOverruledSkuPrice ( $sku ));
		} else {
			$newTotal = $this->GetTotal () - ($numberOfSkus * $sku->GetSkuPrice ());
		}	*/
		if (FALSE === $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the basket: ' . $this->mBasketId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}

		// Multibuy discount (SKU has aready been removed)
		if ($sku->GetParentProduct ()->GetMultibuy ()) {
			// The number of SKUs remaining
			$numberRemainingInBasket = $this->ProductsInBasket ( $sku->GetParentProduct (), true );
			// Get the price for this amount
			$multibuyDiscountPrice = $sku->GetParentProduct ()->GetMultibuyPriceFor ( $numberRemainingInBasket );
			// Change the price
			$this->ChangePriceForSku ( $sku, $multibuyDiscountPrice );
			// Also change the price for the other related SKUs
			$productCount = 0;
			foreach ( $sku->GetParentProduct ()->GetSkus () as $checkSku ) {
				if ($this->InBasket ( $checkSku )) {
					if($vatFree) {
						$this->ChangePriceForSku ( $checkSku, $this->mMoneyHelper->RemoveVAT($multibuyDiscountPrice));
					} else {
						$this->ChangePriceForSku ( $checkSku, $multibuyDiscountPrice );
					}
					$productCount ++;
				}
			}
			// Need to adjust for the previously more expensive quantity of SKUs
			$previousQuantityInBasket = $numberRemainingInBasket + 1;
			$priceOfSingleSKUBeforeDecrement = $sku->GetParentProduct ()->GetMultibuyPriceFor ( $previousQuantityInBasket );

			$multibuyAdjustment = ($priceOfSingleSKUBeforeDecrement - $multibuyDiscountPrice) * $numberRemainingInBasket;
			#die('Multibuy Adj = ('.$multibuyAdjustmentQuantity.' * '.$prevMultibuyUnitPrice.') - ('.$multibuyAdjustmentQuantity.' * '.$multibuyDiscountPrice.')');
			$newTotal = ($this->GetTotal () - $priceOfSingleSKUBeforeDecrement) - $multibuyAdjustment;
		} else {
			if($this->HasOverruledSku($sku,false,false)) {
				$newTotal = $this->GetTotal () - $this->GetOverruledSkuPrice ($sku,false,false);
			} else {
				$newTotal = $this->GetTotal () - $sku->GetSkuPrice ();
			}
		}

		// Update the basket total
		if ($changeTotal) {
			$this->SetTotal ( $newTotal );
		}
		return true;
	}

	//! Returns whether a sku has had its price overruled (by staff)
	/*!
	 * @param $sku [in] Obj:SkuModel
	 * @return Boolean
	 */
	function HasOverruledSku($sku, $packageUpgrade = false, $package = false) {
		if ($package) {
			$packageSql = ' AND Package = 1';
		} else {
			$packageSql = ' AND Package = 0';
		}
		if ($packageUpgrade) {
			$packageUpgradeSql = ' AND Package_Upgrade = 1';
		} else {
			$packageUpgradeSql = ' ';
		}
		$sql = 'SELECT count(SKU_ID) AS SkuCount FROM tblBasket_Skus
		WHERE SKU_ID = ' . $sku->GetSkuId () . ' AND Basket_ID = \'' . $this->mBasketId . '\' AND Adjusted_Price IS NULL ' . $packageUpgradeSql . ' ' . $packageSql;
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not check SKU ' . $sku->GetSkuId () . ' for basket ' . $this->mBasketId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		if ($resultObj->SkuCount == 0) {
			return true;
		} else {
			return false;
		}
	}

	//! Gets the overruled price of a sku that has been overruled
	/*!
	 * @param $sku [in] Obj:SkuModel
	 * @return Decimal
	 */
	function GetOverruledSkuPrice($sku, $package = false, $packageUpgrade = false) {
		if ($package) {
			$packageSql = ' AND Package = 1';
		} else {
			$packageSql = ' AND Package = 0';
		}
		if ($packageUpgrade) {
			$packageUpgradeSql = ' AND Package_Upgrade = 1';
		} else {
			$packageUpgradeSql = ' AND (Package_Upgrade = 0 OR Package_Upgrade IS NULL)';
		}

		$sql = 'SELECT Adjusted_Price FROM tblBasket_Skus WHERE SKU_ID = ' . $sku->GetSkuId () . ' AND Basket_ID = \'' . $this->mBasketId . '\' ' . $packageSql . ' ' . $packageUpgradeSql;
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not check SKU ' . $sku->GetSkuId () . ' for basket ' . $this->mBasketId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$resObj = $result->fetch ( PDO::FETCH_OBJ );
		if ($resObj) {
			return $resObj->Adjusted_Price;
		} else {
			return false;
		}
	}

	//! Gets the overruled price of a package that has been overruled. If the returned price is zero (or null) then the actual price of the package is returned, on the basis it is an order from before packages got the option to have overruled prices
	/*!
	 * @param $package [in] Obj:PackageModel
	 * @return Decimal
	 */
	function GetOverruledPackagePrice($package) {
		$sql = 'SELECT Price FROM tblBasket_Packages WHERE Package_ID = ' . $package->GetPackageId () . ' AND Basket_ID = \'' . $this->mBasketId . '\'';
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not check package ' . $package->GetPackageId () . ' for basket ' . $this->mBasketId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$resObj = $result->fetch ( PDO::FETCH_OBJ );
		if ($resObj->Price == 0) {
			return $package->GetActualPrice ();
		} else {
			return $resObj->Price;
		}

	}

	//! Returns the number of times this SKU is stored as an upgrade as in the DB
	function GetNumberOfUpgradesFor($sku) {
		$sql = 'SELECT COUNT(SKU_ID) AS SkuCount FROM tblBasket_Skus WHERE Basket_ID = \'' . $this->mBasketId . '\' AND SKU_ID = ' . $sku->GetSkuId () . ' AND Package_Upgrade = 1';
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not count SKU upgrades ' . $sku->GetSkuId () . ' for basket ' . $this->mBasketId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$resObj = $result->fetch ( PDO::FETCH_OBJ );
		return $resObj->SkuCount;
	}

	//! Returns the number of the parameter SKU in the basket
	/*!
	 * @param $sku [in] : Obj:SkuModel
	 * @param $includePackageUpgrade [in] : Bool : If true then counts package upgrades as well as regular SKUs
	 * @param $includePackageContents [in] : Bool : If true then counds package contents as well as regular SKUs
	 * @return Int
	 */
	function SkusInBasket($sku, $includePackageUpgrade = false, $includePackageContents = false) {
		if ($includePackageUpgrade && $includePackageContents) {
			$sql = 'SELECT COUNT(SKU_ID) AS NumberOfSkus FROM tblBasket_Skus WHERE SKU_ID = ' . $sku->GetSkuId () . ' AND BASKET_ID = \'' . $this->mBasketId . '\'';
		} elseif (! $includePackageUpgrade && $includePackageContents) {
			$sql = 'SELECT COUNT(SKU_ID) AS NumberOfSkus FROM tblBasket_Skus WHERE SKU_ID = ' . $sku->GetSkuId () . ' AND BASKET_ID = \'' . $this->mBasketId . '\' AND Package_Upgrade = \'0\'';
		} elseif ($includePackageUpgrade && ! $includePackageContents) {
			$sql = 'SELECT COUNT(SKU_ID) AS NumberOfSkus FROM tblBasket_Skus WHERE SKU_ID = ' . $sku->GetSkuId () . ' AND BASKET_ID = \'' . $this->mBasketId . '\' AND Package = \'0\'';
		} elseif (! $includePackageUpgrade && ! $includePackageContents) {
			$sql = 'SELECT COUNT(SKU_ID) AS NumberOfSkus FROM tblBasket_Skus
			WHERE SKU_ID = ' . $sku->GetSkuId () . ' AND BASKET_ID = \'' . $this->mBasketId . '\' AND Package_Upgrade = \'0\' AND Package = \'0\'';
		}
		$result = $this->mDatabase->query ( $sql );
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		return $resultObj->NumberOfSkus;
	}

	//! Returns the number of the parameter product in the basket
	/*!
	 * @param $product [in] : Obj:ProductModel
	 * @param $excludePackageProducts - Boolean, whether to exclude those products that are package products (or upgrades)
	 * @return Int
	 */
	function ProductsInBasket($product, $excludePackageProducts = false) {
		if ($excludePackageProducts) {
			$packageProductsSql = ' AND Package = \'0\' AND Package_Upgrade = \'0\' ';
		} else {
			$packageProductsSql = ' ';
		}
		$skus = $product->GetSkus ();
		$skusList = '';
		foreach ( $skus as $sku ) {
			$skusList .= $sku->GetSkuId () . ', ';
		}
		$skusList = substr ( $skusList, 0, (count ( $skusList ) - 3) );
		$sql = 'SELECT COUNT(SKU_ID) AS NumberOfProducts FROM tblBasket_Skus WHERE SKU_ID IN (' . $skusList . ') AND BASKET_ID = \'' . $this->mBasketId . '\' ' . $packageProductsSql . '';
		$result = $this->mDatabase->query ( $sql );
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		return $resultObj->NumberOfProducts;
	}

	function PackagesInBasket($package) {
		$sql = 'SELECT COUNT(Package_ID) AS NumberOfPackages FROM tblBasket_Packages WHERE Package_ID = ' . $package->GetPackageId () . ' AND BASKET_ID = \'' . $this->mBasketId . '\'';
		$result = $this->mDatabase->query ( $sql );
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		return $resultObj->NumberOfPackages;
	}

	//! Increments the number of SKU supplied within a basket. Synonymous with AddToBasket, included as a compliment to DecFromBasket
	/*!
	 * @param [in] sku : Obj:SkuModel - The SKU to increment
	 * @return Boolean : True if successful, exception thrown if not
	 */
	function IncToBasket($sku) {
		$this->AddToBasket ( $sku );
	}

	//! Gets the total cost of the order (currently)
	/*!
	 * @return Decimal
	 */
	function GetTotal() {
		if (! isset ( $this->mTotal )) {
			$sql = 'SELECT SUM(Adjusted_Price) AS Total FROM tblBasket_Skus WHERE Basket_ID = \''.$this->mBasketId.'\'';
			$sql2 = 'SELECT SUM(Price) AS Total FROM tblBasket_Packages WHERE Basket_ID = \''.$this->mBasketId.'\'';
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$skusTotal = $resultObj->Total;
				if ($result = $this->mDatabase->query ( $sql2 )) {
					$resultObj = $result->fetchObject();
					$packagesTotal = $resultObj->Total;

					$this->mTotal = round($skusTotal + $packagesTotal,2);
				}
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mTotal;
	}

	//! Gets the total amount of VAT-able products in the basket
	/*!
	 * @return Decimal
	 */
	function GetVatableTotal() {
		$sql = ' SELECT SUM( tblBasket_Skus.Adjusted_Price ) AS Total
					FROM tblTaxCode
					INNER JOIN tblProduct ON tblProduct.Tax_Code_ID = tblTaxCode.Tax_Code_ID
					INNER JOIN tblProduct_SKUs ON tblProduct.Product_ID = tblProduct_SKUs.Product_ID
					INNER JOIN tblBasket_Skus ON tblProduct_SKUs.SKU_ID = tblBasket_Skus.SKU_ID
					WHERE tblBasket_Skus.Basket_ID = \''.$this->mBasketId.'\'
					AND tblTaxCode.Rate <> \'0\'';
		if ($result = $this->mDatabase->query($sql)) {
			$resultObj = $result->fetchObject();
			$vatAmount = $resultObj->Total;
			return round($vatAmount,2);
		} else {
			$error = new Error ( 'Could not run query x: ' . $sql . ' and get a result.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	} // End GetVatableTotal

	//! Gets the total amount of zero rate products in the basket
	/*!
	 * @return Decimal
	 */
	function GetNonVatableTotal() {
		$sql = ' SELECT SUM( tblBasket_Skus.Adjusted_Price ) AS Total
					FROM tblTaxCode
					INNER JOIN tblProduct ON tblProduct.Tax_Code_ID = tblTaxCode.Tax_Code_ID
					INNER JOIN tblProduct_SKUs ON tblProduct.Product_ID = tblProduct_SKUs.Product_ID
					INNER JOIN tblBasket_Skus ON tblProduct_SKUs.SKU_ID = tblBasket_Skus.SKU_ID
					WHERE tblBasket_Skus.Basket_ID = \''.$this->mBasketId.'\'
					AND tblTaxCode.Rate = \'0\'';
		if ($result = $this->mDatabase->query($sql)) {
			$resultObj = $result->fetchObject();
			$vatAmount = $resultObj->Total;
			return round($vatAmount,2);
		} else {
			$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	} // End GetNonVatableTotal

	//! Gets the total of the basket (excluding VAT) taking into account zero/standard rates of VAT
	/*!
	 * @return Decimal
	 */
	function GetExcVatTotal() {
		$mh = new MoneyHelper;
		$vatableTotal = $this->GetVatableTotal();
		$excVatableTotal = $this->GetNonVatableTotal();
		$excVatTotal = $excVatableTotal + $mh->RemoveVAT($vatableTotal);
		return $excVatTotal;
	} // End GetVat

	//! Returns the number of items in the basket
	/*
	 * @return Int
	 */
	function GetNumberOfItems() {
		$skus = $this->GetSkus ();
		$packages = $this->GetPackages ();
		return count ( $skus ) + count ( $packages );
	}

	//! Gets the time the basket was created, as a UNIX timestamp - \see{TimeHelper} to manipulate this
	/*!
	 * @return Int : Timestamp
	 */
	function GetCreated() {
		if (! isset ( $this->mCreated )) {
			$sql = 'SELECT Created FROM tblBasket WHERE Basket_ID = \'' . $this->mBasketId . '\'';
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mCreated = $resultObj->Created;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mCreated;
	}

	//! Set the total for this basket
	/*!
	 * @param [in] newTotal : Decimal - the new total
	 * @return Boolean : true if successful
	 */
	function SetTotal($newTotal) {
		$sql = 'UPDATE tblBasket SET Total = \'' . $newTotal . '\' WHERE Basket_ID = \'' . $this->mBasketId . '\'';
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the total of basket: ' . $this->mBasketId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mTotal = $newTotal;
		return true;
	}

	//! Set the postage upgrade for this basket
	/*!
	 * @param [in] newPostage : Decimal - the new postage
	 * @return Boolean : true if successful
	 */
	function SetPostageUpgrade($newPostage) {
		$sql = 'UPDATE tblBasket SET Postage_Upgrade = \'' . $newPostage . '\' WHERE Basket_ID = \'' . $this->mBasketId . '\'';
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the postage of basket: ' . $this->mBasketId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mPostageUpgrade = $newPostage;
		return true;
	}

	//! Gets the postage upgrade of the order (currently)
	/*!
	 * @return Decimal
	 */
	function GetPostageUpgrade() {
		if (! isset ( $this->mPostageUpgrade )) {
			$sql = 'SELECT Postage_Upgrade FROM tblBasket WHERE Basket_ID = \'' . $this->mBasketId . '\'';
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mPostageUpgrade = $resultObj->Postage_Upgrade;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mPostageUpgrade;
	}

	//! Returns the Stock Keeping Units associated with a basket
	/*!
	* @param $excludeUpgrades : Boolean			- Whether or not to include package upgrades
	* @param $excludePackageProducts : Boolean	- Whether or not to include package products
	* @param $excludeNonPackage : Boolean		- Whether or not to include NON-package SKUs
	* @param $refresh : Boolean					- Whether or not to 'refresh' from the previous GetSkus() call - Use if changing the other parameters since the previous call
	* @return Array of SkuModel objects, empty if none
	*/
	function GetSkus($excludeUpgrades=false,$excludePackageProducts=true,$excludeNonPackage=false,$refresh=false) {
		if ($excludeUpgrades) {
			$excludeUpgradesSql = ' AND (Package_Upgrade IS NULL OR Package_Upgrade = \'0\') ';
		} else {
			$excludeUpgradesSql = ' ';
		}
		if ($excludePackageProducts) {
			$excludePackageProductsSql = ' AND tblBasket_Skus.Package = \'0\' ';
		} else {
			$excludePackageProductsSql = ' ';
		}
		if ($excludeNonPackage) {
			$excludeNonPackageSql = ' AND tblBasket_Skus.Package = \'1\' ';
		} else {
			$excludeNonPackageSql = ' ';
		}

		// If refreshing, empty the SKUs arr
		if($refresh) { $this->mSkus = array(); }

		if(!isset($this->mSkus) || 0 == count($this->mSkus) || $refresh) {
			$sql = 'SELECT
						tblBasket_Skus.SKU_ID
					FROM tblBasket_Skus
					INNER JOIN tblProduct_SKUs
						ON tblBasket_Skus.SKU_ID = tblProduct_SKUs.SKU_ID
					INNER JOIN tblProduct_Text
						ON tblProduct_SKUs.Product_ID = tblProduct_Text.Product_ID
					WHERE Basket_ID = \'' . $this->mBasketId . '\'
					'.$excludeUpgradesSql.'
					'.$excludePackageProductsSql.'
					'.$excludeNonPackageSql.'
					ORDER BY tblProduct_Text.Display_Name ASC
					';#echo $sql.'<br><br>';die($sql);
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the SKUs for basket ' . $this->mBasketId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$skus = $result->fetchAll ( PDO::FETCH_OBJ );
			// For each SKU, add it to the array
			foreach ( $skus as $value ) {
				$newSku = new SkuModel ( $value->SKU_ID );
				$this->mSkus [] = $newSku;
			}
			if (0 == count ( $skus )) {
				$this->mSkus = array ();
			}
		}
		return $this->mSkus;
	}
	//! Gets all the package in the basket
	/*!
	 * @param $fail : Boolean - Whether to fail if a package doesn't exist, defaults to false (dont fail)
	 * @return Array of Obj:PackageModel - the packages in the basket, empty if there are none
	 */
	function GetPackages($fail = false) {
		if (! isset ( $this->mPackages ) || 0 == count ( $this->mPackages )) {
			$sql = 'SELECT
						tblBasket_Packages.Package_ID
					FROM tblBasket_Packages
					WHERE Basket_ID = \'' . $this->mBasketId . '\'
					';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the packages for basket ' . $this->mBasketId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$packages = $result->fetchAll ( PDO::FETCH_OBJ );
			// For each package, add it to the array
			foreach ( $packages as $packageObj ) {
				$newPackage = new PackageModel ( $packageObj->Package_ID, false );
				if ($newPackage) {
					$this->mPackages [] = $newPackage;
				}
			}
			if (0 == count ( $packages )) {
				$this->mPackages = array ();
			}
		}
		return $this->mPackages;
	}

	//! Updates stock levels for this basket
	function UpdateStockLevels() {
		// Get all skus including package items etc. - the falses indicate 'exclude foo'
		$allSkus = $this->GetSkus(false,false,false);
		foreach($allSkus as $sku) {
			$currentStockLevel = $sku->GetQty();
			// No point in setting negative stock levels!
			if($currentStockLevel > 0) {
				$newStockLevel = $currentStockLevel - 1;
				$sku->SetQty($newStockLevel);
			}
		}
	} // End UpdateStockLevels

	//! Checks whether anything in the basket is sold out
	function ContainsSoldOutProducts($returnList=false) {
		$nonStock = false; 		// Initialise
		$productList = '';		// This will hold a list of products
		$productListCount = 0; 	// If this is greater than 1 then need to remove a comma-space
		$allSkus = $this->GetSkus(false,false,false);
		// For all SKUs, if they are non stock then set nonstock to true for the basket, and build the product list
		foreach($allSkus as $sku) {
			if($sku->GetQty() == 0) {
				$soldOut = true;
				$productList .= $sku->GetParentProduct()->GetDisplayName().', ';
				$productListCount++;
			}
		}
		// If we're returning a list then do so, stripping the extra comma if needed. Return a 'false' if there is no product list
		if($returnList) {
			if($productListCount > 1) {
				$productList = substr($productList,0,strlen($productList)-2);
			}
			if($productList == '') {
				return false;
			} else {
				return $productList;
			}
		} else {
			return $soldOut;
		}
	} // End ContainsNonStockProducts

	//! Checks whether anything in the basket is 3-5 day delivery
	function ContainsNonStockProducts($returnList=false) {
		$nonStock = false; 		// Initialise
		$productList = '';		// This will hold a list of products
		$productListCount = 0; 	// If this is greater than 1 then need to remove a comma-space
		$allSkus = $this->GetSkus(false,false,false);
		// For all SKUs, if they are non stock then set nonstock to true for the basket, and build the product list
		foreach($allSkus as $sku) {
			if($sku->GetParentProduct()->IsNonStockProduct()) {
				$nonStock = true;
				$productList .= $sku->GetParentProduct()->GetDisplayName().', ';
				$productListCount++;
			}
		}
		// If we're returning a list then do so, stripping the extra comma if needed. Return a 'false' if there is no product list
		if($returnList) {
			if($productListCount > 1) {
				$productList = substr($productList,0,strlen($productList)-2);
			}
			if($productList == '') {
				return false;
			} else {
				return $productList;
			}
		} else {
			return $nonStock;
		}
	} // End ContainsNonStockProducts

	//! Get the number of times $package is in the basket
	/*!
	 * @param $package [in] Obj:PackageModel - The package to check against
	 * @return Int - The number of times it occurs
	 */
	function GetPackageQty($package) {
		$sql = 'SELECT
					COUNT(tblBasket_Packages.Package_ID) AS PackageCount
				FROM tblBasket_Packages
				WHERE Basket_ID = \'' . $this->mBasketId . '\'
				AND Package_ID = ' . $package->GetPackageId () . '
				';
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not fetch the package count for basket ' . $this->mBasketId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		return $resultObj->PackageCount;
	}

	//! Returns whether a sku is in the basket
	/*!
	 * @param $sku [in] Obj:SkuModel
	 * @return Boolean
	 */
	function InBasket($sku) {
		$sql = 'SELECT COUNT(SKU_ID) AS SkuCount FROM tblBasket_Skus WHERE Basket_ID = \'' . $this->mBasketId . '\' AND SKU_ID = ' . $sku->GetSkuId () . '';
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not check SKU ' . $sku->GetSkuId () . ' for basket ' . $this->mBasketId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		if (0 == $resultObj->SkuCount) {
			return false;
		} else {
			return true;
		}
	}

	//! Alters the price for a SKU to the param $price
	/*!
	 * @param $sku [in] Obj:SkuModel
	 * @param $price [in] Decimal
	 * @param $package [in] - Bool - Whether or not to allow package products to be considered
	 * @param $packageUpgrade [in] - Bool - Whether or not to allow package upgrades to be considered
	 * @return void
	 */
	function ChangePriceForSku($sku, $price, $package = false, $packageUpgrade = false) {
		if ($package) {
			$packageSql = ' AND Package = 1';
		} else {
			$packageSql = ' AND Package = 0';
		}
		if ($packageUpgrade) {
			$packageUpgradeSql = ' AND Package_Upgrade = 1';
		} else {
			$packageUpgradeSql = ' AND Package_Upgrade = 0';
		}

		// Previous Price
		$previousPrice = $this->GetOverruledSkuPrice($sku,false,false);

		/* This check means that non-already-adjusted skus (of same ID) are prioritised for changing, rather than change the same sku again
		$checkSql = 'SELECT COUNT(TempID) AS NullPriceCount FROM tblBasket_Skus WHERE SKU_ID = ' . $sku->GetSkuId () . ' AND Basket_ID = \'' . $this->mBasketId . '\' AND Adjusted_Price IS NULL ' . $packageSql . ' ' . $packageUpgradeSql;
		$result = $this->mDatabase->query ( $checkSql );
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		if ($resultObj->NullPriceCount > 0) {
			$adjustedPriceSql = ' AND Adjusted_Price IS NULL ';
		} else {
			$adjustedPriceSql = ' ';
		}*/

		// Main query
		$sql1 = 'SELECT TempID
				 FROM tblBasket_Skus
				 WHERE SKU_ID = '.$sku->GetSkuId().'
				 AND Basket_ID = \''.$this->mBasketId.'\' '.$packageSql.' '.$packageUpgradeSql;
		$result = $this->mDatabase->query($sql1);
		while($resultObj = $result->fetch(PDO::FETCH_OBJ)) {
			$sql = '
			UPDATE tblBasket_Skus
				SET Adjusted_Price = '.$price.'
				WHERE TempID = '.$resultObj->TempID;
				if (! $this->mDatabase->query ( $sql )) {
					$error = new Error ( 'Could not adjust price for SKU: ' . $sku->GetSkuId () . ' in basket: ' . $this->mBasketId . ' ' . $sql );
					$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
					throw new Exception ( $error->GetErrorMsg () );
				}
		}

		// Update the basket total
		$adjustment = $previousPrice - $price;
#		echo 'The adjustment: '.$previousPrice.' - '.$price.' = '.$adjustment.'<br>';	die();
#		if($adjustment > 0) {
			// Decrease in price
		$newTotal = $this->GetTotal() - $adjustment;
		$this->SetTotal($newTotal);
#		} else {
			// Increase in price
#			$newTotal = $this->GetTotal() + $adjustment;
#			$this->SetTotal($newTotal);
#		}

	} // End ChangePriceForSku

	//! Adds a package to a basket
	/*
	 * @param [in] package, Obj:PackageModel - the package to add
	 * @param [in] packagePrice, Optional - If present the decimal value is used as the package price, otherwise the default package price is used
	 * @return Boolean - true if successful
	 */
	function AddPackageToBasket($package, $packagePriceOverride = false) {
		if ($packagePriceOverride) {
			$packagePrice = $packagePriceOverride;
		} else {
			$packagePrice = $package->GetActualPrice ();
		}
		$sql = 'INSERT INTO tblBasket_Packages (`Basket_ID`,`Package_ID`,`Price`) VALUES (\'' . $this->mBasketId . '\',\'' . $package->GetPackageId () . '\',' . $packagePrice . ')';
		if (FALSE === $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the basket: ' . $this->mBasketId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		// Add the package price to the basket total
		$newTotal = $this->GetTotal () + $packagePrice;
		#echo "$newTotal = ".$this->GetTotal()." + $packagePriceOverride<br />";
		#echo $package->GetDisplayName().'<br /> ---------- <br />';
		$this->SetTotal ( $newTotal );
		return true;
	}

	//! Changes the price of a package in the basket
	function ChangePriceForPackage($package,$price) {
		$sql = 'UPDATE tblBasket_Packages SET Price = \''.$price.'\' WHERE Basket_ID = \''.$this->mBasketId.'\' AND Package_ID = '.$package->GetPackageId();
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not adjust price for Package: '.$package->GetPackageId().' in basket: '.$this->mBasketId.$sql);
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		return true;
	} // End ChangePriceForPackage



	//! Set the quantity of a given SKU
	/*!
	 * @param $sku [in] : The SKU to change the qty of
	 * @param $qty [in] : The new quantity
	 */
	function SetSkuQty($sku, $qty, $vatFree=false) {
		$currentSkus = $this->SkusInBasket ( $sku );
		$difference = $currentSkus - $qty;
		if ($qty == 0) {
			$this->RemoveFromBasket ( $sku );
		} else {
			if ($difference < 0) {

				// Adding some
				$difference = abs ( $difference );
				for($i = 0; $i < $difference; $i ++) {

					// If the SKU has an overwritten price, keep it!
					if($this->HasOverruledSku($sku,false,false)) {
						$this->AddToBasket ( $sku, false, $this->GetOverruledSkuPrice($sku,false,false), false, $vatFree );
					} else {
						$this->AddToBasket ( $sku, false, $sku->GetSkuPrice (), false, $vatFree );
					}
				}
			} elseif ($difference > 0) {
				// Removing some
				for($i = 0; $i < $difference; $i ++) {
					$this->DecFromBasket ( $sku, $vatFree );
				}
			}
		}
		return true;
	}

	//! Returns the postage of the items in the basket, without the upgrade added on
	/*
	 * @return Decimal - The current postage
	 */
	function GetPostageWithoutUpgrade() {
		$this->mCurrentPostage = 0;
		foreach ( $this->GetSkus () as $sku ) {
			$product = $sku->GetParentProduct ();
			if ($product->GetPostage () > $this->mCurrentPostage) {
				$this->mCurrentPostage = $product->GetPostage ();
			}
		}
		foreach ( $this->GetPackages () as $package ) {
			if ($package->GetPostage () > $this->mCurrentPostage) {
				$this->mCurrentPostage = $package->GetPostage ();
			}
		}
		return $this->mCurrentPostage;
	}

	//! Gets the postage method has a user not upgraded - so would be second class/parcelforce 48 etc
	/*
	 * @return $method : Obj:PostageMethodModel - the default method
	 */
	function GetDefaultPostageMethod() {
		// Get an appropriate postage method based on the basket weight
		$pmController = new PostageMethodController;
		$allMethods = $pmController->GetAll();
		foreach($allMethods as $method) {
			if(($this->GetWeight() >= $method->GetMinWeight() && $this->GetWeight() < $method->GetMaxWeight()) || $method->GetMaxWeight() == 0) {
#				echo $method->GetPostageMethodId();
				return $method;
			}
		}
		// If that fails...
		return new PostageMethod(1);
	} // End GetDefaultPostageMethod

	//! Gets the weight of the basket by adding the weight of each SKU
	/*!
	 * return Decimal
	 */
	function GetWeight() {
		$skus = $this->GetSkus ();
		$weight = 0;
		foreach ( $skus as $sku ) {
			$weight += $sku->GetParentProduct ()->GetWeight ();
		}
		$packages = $this->GetPackages ();
		foreach ( $packages as $package ) {
			$products = $package->GetContents ();
			foreach ( $products as $product ) {
				$weight += $product->GetWeight ();
			}
		}
		return $weight;
	}

	//! Returns the unique basket identifier
	function GetBasketId() {
		return $this->mBasketId;
	}

	//! Gets the catalogue that contains the items in this basket
	function GetCatalogue() {
		$sql = 'SELECT tblCategory.Catalogue_ID FROM tblCategory
					INNER JOIN tblCategory_Products ON tblCategory_Products.Category_ID = tblCategory.Category_ID
					INNER JOIN tblProduct_SKUs ON tblProduct_SKUs.Product_ID = tblCategory_Products.Product_ID
					WHERE tblProduct_SKUs.SKU_ID IN
				(
					SELECT tblBasket_Skus.SKU_ID FROM tblBasket_Skus WHERE tblBasket_Skus.Basket_ID = \''.$this->mBasketId.'\'
				)
				LIMIT 1';
		$result = $this->mDatabase->query ( $sql );
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		if ($resultObj) {
			$catalogueId = $resultObj->Catalogue_ID;
		} else {
			$catalogueId = 1;
		}
		return new CatalogueModel ( $catalogueId );
	}

	//! Does any of the products in the basket have postage set? (Eg. tanks/gun safes)
	/*
	 * @para, $returnAmount - If true, and it HAS postage, return the amount of postage
	 * @return Boolean unless $returnAmount is true, in which case returns the amount (which may be zero)
	 */
	function HasManualPostage($returnAmount=false) {
		$sql = '
				SELECT MAX(tblProduct.Postage) AS MaxPostage
				FROM tblProduct
					INNER JOIN tblProduct_SKUs ON tblProduct_SKUs.Product_ID = tblProduct.Product_ID
					INNER JOIN tblBasket_Skus ON tblBasket_Skus.SKU_ID = tblProduct_SKUs.SKU_ID
				WHERE tblBasket_Skus.Basket_ID = \''.$this->mBasketId.'\'
				';
		$result = $this->mDatabase->query($sql);
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		if($resultObj->MaxPostage > 0) {
			if($returnAmount) {
				return $resultObj->MaxPostage;
			} else {
				return true;
			}
		} else {
			if($returnAmount) {
				return 0;
			} else {
				return false;
			}
		}
	} // End HasManualPostage

	// Make any items in the basket that aren't VAT exempt VAT free
	function MakeContentsVatFree() {
		$registry = Registry::getInstance();
		$moneyHelper = new MoneyHelper;
		$productStack = array();
		$packageStack = array();

		// Products...
		foreach($this->GetSkus(true,true,false,true) as $sku) {
			$product = $sku->GetParentProduct();
			if(!in_array($product->GetProductId(),$productStack)) {
				if($product->GetTaxCode()->GetRate() != 0) {
					$this->ChangePriceForSku($sku,$moneyHelper->RemoveVAT($this->GetOverruledSkuPrice($sku,false,false)),false,false);
				}
				$productStack[] = $product->GetProductId();
			}
		}

		// Packages...
		foreach($this->GetPackages() as $package) {
			if(!in_array($package->GetPackageId(),$packageStack)) {
				if($registry->packageVatFreeAllowed) {
					$this->ChangePriceForPackage($package,$moneyHelper->RemoveVAT($this->GetOverruledPackagePrice($package)));
				}
				$packageStack[] = $package->GetPackageId();
			}
		}

	} // End MakeContentsVatFree

	// Make any items in the basket that aren't VAT exempt VAT inclusive
	function MakeContentsVatInclusive() {
		$registry = Registry::getInstance();
		$moneyHelper = new MoneyHelper;
		$productStack = array();
		$packageStack = array();

		// Products...
		foreach($this->GetSkus(true,true,false,true) as $sku) {
			$product = $sku->GetParentProduct();
			if(!in_array($product->GetProductId(),$productStack)) {
				if($product->GetTaxCode()->GetRate() != 0) {
					$this->ChangePriceForSku($sku,$moneyHelper->AddVAT($this->GetOverruledSkuPrice($sku,false,false)),false,false);
				}
				$productStack[] = $product->GetProductId();
			}
		}

		// Packages...
		foreach($this->GetPackages() as $package) {
			if(!in_array($package->GetPackageId(),$packageStack)) {
				if($registry->packageVatFreeAllowed) {
					$this->ChangePriceForPackage($package,$moneyHelper->AddVAT($this->GetOverruledPackagePrice($package)));
				}
				$packageStack[] = $package->GetPackageId();
			}
		}

	} // End MakeContentsVatInclusive

	//! Checks whether the basket has had it's free item added already
	/*!
	 * @return Boolean - True if the free item has been applied, false otherwise
	 */
	function GetFreeOfferApplied() {
		$sql = 'SELECT Free_Offer_Applied FROM tblBasket WHERE Basket_ID = \'' . $this->mBasketId . '\' LIMIT 1';
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not fetch the free offer applied information for basket.');
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		if ($resultObj->Free_Offer_Applied == '1') {
			return true;
		} else {
			return false;
		}
	} // End GetFreeOfferApplied

	//! Sets the basket as having had its 'free item' already added so that it can't be added twice
	/*!
	 * @param $status Int - Bool 1 or 0
	 * @return Boolean - True if successful / Exception
	 */
	function SetFreeOfferApplied($status) {
		$sql = 'UPDATE tblBasket SET Free_Offer_Applied = \''.$status.'\' WHERE Basket_ID = \'' . $this->mBasketId . '\'';
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not set status for free item on basket');
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		return true;
	}


} // End BasketModel


/* DEBUG
try {
	$bas = new BasketModel('2irnmiuui1tr9qml4tjsb8ks67');
	$sku = new SkuModel(60);
	echo '<a href="BasketModel.php?add=60">Add SKU 60 To Basket</a>';
	if(isset($_GET['add'])) {
		$bas->IncToBasket($sku);
		echo $bas->GetTotal();
		$bas->SetTotal(5.95);
		echo $bas->GetCreated();
		var_dump($bas->GetSkus());
	}
} catch (Exception $e) {
	echo $e->getMessage();
}*/

?>
