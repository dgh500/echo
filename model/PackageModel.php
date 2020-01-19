<?php

//! Models a single package
class PackageModel {
	//! Int : Unique Package ID
	var $mPackageId;
	//! String : The package name
	var $mDisplayName;
	//! String : The package description
	var $mDescription;
	//! Decimal(19,4) : Price the customer will pay
	var $mActualPrice;
	//! Decimal(19,4) : Price that will be crossed out
	var $mWasPrice;
	//! Decimal(19,4) : Cost of postage
	var $mPostage;
	//! Obj:Image : The main image of the package
	var $mImage;
	//! Array : An array of Obj:ProductModel objects
	var $mContents;
	//! Array : An array of Obj:ProductModel objects
	var $mUpgrades;

	var $mOfferOfWeek;

	var $mCatalogue;

	//! Constructor, initialises the package ID
	/*!
	 * @param $fail - Boolean - Whether or not to fail if the package doesn't exist any more, defaults to true
	 */
	function __construct($packageId, $fail = true) {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
		$does_this_package_sql = 'SELECT COUNT(Package_ID) FROM tblPackage WHERE Package_ID = ' . $packageId;
		if ($result = $this->mDatabase->query ( $does_this_package_sql )) {
			if ($result->fetchColumn () > 0) {
				$this->mPackageId = $packageId;
			} else {
				if ($fail) {
					$error = new Error ( 'Could not initialise package ' . $packageId . ' because it does not exist in the database.' );
					$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
					throw new Exception ( $error->GetErrorMsg () );
				} else {
					return false;
				}
			}
		} else {
			if ($fail) {
				$error = new Error ( 'Could not initialise package ' . $packageId . ' because it does not exist in the database.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			} else {
				return false;
			}
		}
	}

	//! Returns the price paid by the customer. If this has already been done then it is returned directly, otherwise it is retrieved from the database
	/*!
	* @return Decimal(19,4)
	*/
	function GetActualPrice() {
		if (! isset ( $this->mActualPrice )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_actual_price_sql = 'SELECT Actual_Price FROM tblPackage WHERE Package_ID = ' . $this->mPackageId.' LIMIT 1';
			if (! $result = $database->query ( $get_actual_price_sql )) {
				$error = new Error ( 'Could not fetch the actual price for package ' . $this->mPackageId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$actual_price = $result->fetch ( PDO::FETCH_OBJ );
			$this->mActualPrice = $actual_price->Actual_Price;
		}
		return $this->mActualPrice;
	}

	//! Sets the actual price of the package
	/*!
	* @param [in] newActualPrice Decimal(19,4) : The new actual price
	* @return Bool : true if successful
	*/
	function SetActualPrice($newActualPrice) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_actual_price_sql = 'UPDATE tblPackage SET Actual_Price = \'' . $newActualPrice . '\' WHERE Package_ID = ' . $this->mPackageId;
		if (! $database->query ( $set_actual_price_sql )) {
			$error = new Error ( 'Could not update the actual price for package ' . $this->mPackageId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mActualPrice = $newActualPrice;
		return true;
	}

	//! Returns the price that is crossed out
	/*!
	* @return Decimal(19,4)
	*/
	function GetWasPrice() {
		if (! isset ( $this->mWasPrice )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_was_price_sql = 'SELECT Was_Price FROM tblPackage WHERE Package_ID = ' . $this->mPackageId.' LIMIT 1';
			if (! $result = $database->query ( $get_was_price_sql )) {
				$error = new Error ( 'Could not fetch the was price for package ' . $this->mPackageId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$was_price = $result->fetch ( PDO::FETCH_OBJ );
			$this->mWasPrice = $was_price->Was_Price;
		}
		return $this->mWasPrice;
	}

	//! Sets the was price of the package
	/*!
	* @param [in] newWasPrice : Decimal(19,4) : New was price
	* @return Bool : true if successful
	*/
	function SetWasPrice($newWasPrice) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_was_sql = 'UPDATE tblPackage SET Was_Price = \'' . $newWasPrice . '\' WHERE Package_ID = ' . $this->mPackageId;
		if (! $database->query ( $set_was_sql )) {
			$error = new Error ( 'Could not update the was price for package ' . $this->mPackageId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mWasPrice = $newWasPrice;
		return true;
	}

	//! Returns postage to be paid
	/*!
	* @return Decimal(19,4)
	*/
	function GetPostage() {
		if (! isset ( $this->mPostage )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_postage_sql = 'SELECT Postage FROM tblPackage WHERE Package_ID = ' . $this->mPackageId.' LIMIT 1';
			if (! $result = $database->query ( $get_postage_sql )) {
				$error = new Error ( 'Could not fetch the postage for package ' . $this->mPackageId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$postage = $result->fetch ( PDO::FETCH_OBJ );
			$this->mPostage = $postage->Postage;
		}
		return $this->mPostage;
	}

	//! Sets the postage of the product
	/*!
	* @param [in] newPostage : Decimal(19,4) : New postage price
	* @return Bool : true if successful
	*/
	function SetPostage($newPostage) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_postage_sql = 'UPDATE tblPackage SET Postage = \'' . $newPostage . '\' WHERE Package_ID = ' . $this->mPackageId;
		if (! $database->query ( $set_postage_sql )) {
			$error = new Error ( 'Could not update the postage for package ' . $this->mPackageId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mPostage = $newPostage;
		return true;
	}

	//! Returns the name of the package
	/*!
	* @return String
	*/
	function GetDisplayName() {
		if (! isset ( $this->mDisplayName )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_display_name_sql = 'SELECT Display_Name FROM tblPackage WHERE Package_ID = ' . $this->mPackageId.' LIMIT 1';
			if (! $result = $database->query ( $get_display_name_sql )) {
				$error = new Error ( 'Could not fetch the display name for package ' . $this->mPackageId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$display_name = $result->fetch ( PDO::FETCH_OBJ );
			$this->mDisplayName = $display_name->Display_Name;
		}
		return $this->mDisplayName;
	}

	//! Sets the name of the tax code
	/*!
	* @param [in] newDisplayName : String
	* @return Bool : true if successful
	*/
	function SetDisplayName($newDisplayName) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_display_name_sql = 'UPDATE tblPackage SET Display_Name = \'' . mysql_escape_string($newDisplayName) . '\' WHERE Package_ID = ' . $this->mPackageId;
		if (! $database->query ( $set_display_name_sql )) {
			$error = new Error ( 'Could not update the display name for package ' . $this->mPackageId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDisplayName = $newDisplayName;
		return true;
	}

	//! Returns the description of the package
	/*!
	* @return String
	*/
	function GetDescription() {
		if (! isset ( $this->mDescription )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_description_sql = 'SELECT Description FROM tblPackage WHERE Package_ID = ' . $this->mPackageId.' LIMIT 1';
			if (! $result = $database->query ( $get_description_sql )) {
				$error = new Error ( 'Could not fetch the description for package ' . $this->mPackageId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$description = $result->fetch ( PDO::FETCH_OBJ );
			$this->mDescription = $description->Description;
		}
		return $this->mDescription;
	}

	//! Sets the description of the package
	/*!
	* @param [in] newDescription : String
	* @return Bool : true if successful
	*/
	function SetDescription($newDescription) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_description_sql = 'UPDATE tblPackage SET Description = \'' . mysql_escape_string($newDescription) . '\' WHERE Package_ID = ' . $this->mPackageId;
		if (! $database->query ( $set_description_sql )) {
			$error = new Error ( 'Could not update the description for package ' . $this->mPackageId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDescription = $newDescription;
		return true;
	}

	//! Returns the long description of the package
	/*!
	* @return String
	*/
	function GetLongDescription() {
		if (! isset ( $this->mLongDescription )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_long_description_sql = 'SELECT Long_Description FROM tblPackage WHERE Package_ID = ' . $this->mPackageId.' LIMIT 1';
			if (! $result = $database->query ( $get_long_description_sql )) {
				$error = new Error ( 'Could not fetch the long description for package ' . $this->mPackageId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$longDescription = $result->fetch ( PDO::FETCH_OBJ );
			$this->mLongDescription = $longDescription->Long_Description;
		}
		return $this->mLongDescription;
	}

	//! Sets the long description of the package
	/*!
	* @param [in] newLongDescription : String
	* @return Bool : true if successful
	*/
	function SetLongDescription($newLongDescription) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_long_description_sql = 'UPDATE tblPackage SET Long_Description = \'' . mysql_escape_string($newLongDescription) . '\' WHERE Package_ID = ' . $this->mPackageId;
		if (! $database->query ( $set_long_description_sql )) {
			$error = new Error ( 'Could not update the long description for package ' . $this->mPackageId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mLongDescription = $newLongDescription;
		return true;
	}

	//! Returns image of the package
	/*!
	* @return Obj:ImageModel
	*/
	function GetImage() {
		if (! isset ( $this->mImage )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_image_sql = 'SELECT Image_ID FROM tblPackage WHERE Package_ID = ' . $this->mPackageId.' LIMIT 1';
			if (! $result = $database->query ( $get_image_sql )) {
				$error = new Error ( 'Could not fetch the image for package ' . $this->mPackageId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$image = $result->fetch ( PDO::FETCH_OBJ );
			if (NULL === $image->Image_ID || 0 == $image->Image_ID) {
				$this->mImage = NULL;
			} else {
				$this->mImage = new ImageModel ( $image->Image_ID );
			}
		}
		return $this->mImage;
	}

	//! Returns catalogue associated with a package
	/*!
	* @return Catalogue objects
	*/
	function GetCatalogue() {
		if (! isset ( $this->mCatalogue )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_category_sql = 'SELECT Category_ID FROM tblCategory_Packages WHERE Package_ID = ' . $this->mPackageId.' LIMIT 1';
			if (! $result = $database->query ( $get_category_sql )) {
				$error = new Error ( 'Could not fetch the category for package ' . $this->mPackageId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$category = $result->fetch ( PDO::FETCH_OBJ );
			$categoryId = $category->Category_ID;
			$get_catalogue_sql = 'SELECT Catalogue_ID FROM tblCategory WHERE Category_ID = ' . $categoryId;
			if (! $result = $database->query ( $get_catalogue_sql )) {
				$error = new Error ( 'Could not fetch the catalogue for package ' . $this->mPackageId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$catalogue = $result->fetch ( PDO::FETCH_OBJ );
			$catalogueId = $catalogue->Catalogue_ID;
			$this->mCatalogue = new CatalogueModel ( $catalogueId );
		}
		return $this->mCatalogue;
	}

	//! Returns category containing this package
	/*!
	* @return Obj:CategoryModel
	*/
	function GetParentCategory() {
		if (! isset ( $this->mParentCategory )) {
			$sql = 'SELECT Category_ID FROM tblCategory_Packages WHERE Package_ID = ' . $this->mPackageId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the category for package ' . $this->mPackageId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$category = $result->fetch ( PDO::FETCH_OBJ );
			$categoryId = $category->Category_ID;
			$this->mParentCategory = new CategoryModel ( $categoryId );
		}
		return $this->mParentCategory;
	}

	//! Sets the image of the package
	/*!
	* @param [in] newImage : Obj:ImageModel : New image
	* @return Bool : true if successful
	*/
	function SetImage($newImage) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_image_sql = 'UPDATE tblPackage SET Image_ID = \'' . $newImage->GetImageId () . '\' WHERE Package_ID = ' . $this->mPackageId;
		if (! $database->query ( $set_image_sql )) {
			$error = new Error ( 'Could not update the image for package ' . $this->mPackageId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mImage = $newImage;
		return true;
	}

	//! Returns all products in this package
	/*!
	* @return Array of Product objects
	*/
	function GetContents() {
		if (! isset ( $this->mContents )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_contents_sql = '	SELECT
										tblPackage_Products.Product_ID,
										tblProduct_Text.Display_Name
									FROM tblPackage_Products
										INNER JOIN tblProduct_Text
											ON tblProduct_Text.Product_ID = tblPackage_Products.Product_ID
									WHERE Package_ID = ' . $this->mPackageId . '
									ORDER BY tblProduct_Text.Display_Name ASC';
			if (! $result = $database->query ( $get_contents_sql )) {
				$error = new Error ( 'Could not fetch the contents for package ' . $this->mPackageId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$package_contents = $result->fetchAll ( PDO::FETCH_OBJ );
			// For each product attribute, create a new instance of it and store it in the mAttributes member variable
			foreach ( $package_contents as $productId ) {
				$newProduct = new ProductModel ( $productId->Product_ID );
				$this->mContents [] = $newProduct;
			}
			if (0 == count ( $package_contents )) {
				$this->mContents = array ();
			}
		}
		return $this->mContents;
	}

	function IsPart($productIn) {
		foreach ( $this->GetContents () as $product ) {
			if ($product->GetProductId () == $productIn->GetProductId ()) {
				return true;
			}
		}
		return false;
	}

	//! Returns all the upgrades for $product in the package
	/*!
	* @param $product [in] Obj:ProductModel
	* @return Array of Product objects (upgrades)
	*/
	function GetUpgradesFor($product) {
		$sql = '	SELECT
							tblPackage_Upgrades.Upgrade_ID,
							tblProduct_Text.Display_Name
						FROM tblPackage_Upgrades
							INNER JOIN tblProduct_Text
								ON tblProduct_Text.Product_ID = tblPackage_Upgrades.Upgrade_ID
						WHERE Package_ID = ' . $this->mPackageId . '
							AND tblPackage_Upgrades.Product_ID = ' . $product->GetProductId () . '
						 ORDER BY tblPackage_Upgrades.Upgrade_Price ASC';
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not fetch the upgrades for package ' . $this->mPackageId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$package_upgrades = $result->fetchAll ( PDO::FETCH_OBJ );
		// For each product attribute, create a new instance of it and store it in the mAttributes member variable
		foreach ( $package_upgrades as $productId ) {
			$newProduct = new ProductModel ( $productId->Upgrade_ID );
			$retArr [] = $newProduct;
		}
		if (0 == count ( $package_upgrades )) {
			$retArr = array ();
		}
		return $retArr;
	}

	function GetUpgrades() {
		$sql = '	SELECT
							tblPackage_Upgrades.Upgrade_ID,
							tblProduct_Text.Display_Name
						FROM tblPackage_Upgrades
							INNER JOIN tblProduct_Text
								ON tblProduct_Text.Product_ID = tblPackage_Upgrades.Upgrade_ID
						WHERE Package_ID = ' . $this->mPackageId . '
						 ORDER BY tblPackage_Upgrades.Upgrade_Price ASC';
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not fetch the upgrades for package ' . $this->mPackageId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$package_upgrades = $result->fetchAll ( PDO::FETCH_OBJ );
		// For each product attribute, create a new instance of it and store it in the mAttributes member variable
		foreach ( $package_upgrades as $productId ) {
			$newProduct = new ProductModel ( $productId->Upgrade_ID );
			$retArr [] = $newProduct;
		}
		if (0 == count ( $package_upgrades )) {
			$retArr = array ();
		}
		return $retArr;
	}

	function IsUpgrade($product) {
		$upgrades = $this->GetUpgrades ();
		foreach ( $upgrades as $upgrade ) {
			if ($product->GetProductId () == $upgrade->GetProductId ()) {
				return true;
			}
		}
		return false;
	}

	//! Sets the on offer of the week option of the package
	/*!
	* @param [in] newOnOfferOfWeek String(1) : Either 0 or 1
	* @return Bool : true if successful
	*/
	function SetOfferOfWeek($newOnOfferOfWeek) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_on_offer_of_week_sql = 'UPDATE tblPackage SET Offer_Of_Week = \'' . $newOnOfferOfWeek . '\' WHERE Package_ID = ' . $this->mPackageId;
		if (! $database->query ( $set_on_offer_of_week_sql )) {
			$error = new Error ( 'Could not update the on offer of the week option for package ' . $this->mPackageId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mOfferOfWeek = $newOnOfferOfWeek;
		return true;
	}

	//! Returns whether the product is on offer of the week
	/*!
	* @return String(1) - Either 0 or 1 (False or True)
	*/
	function GetOfferOfWeek() {
		if (! isset ( $this->mOfferOfWeek )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_on_offer_of_week_sql = 'SELECT Offer_Of_Week FROM tblPackage WHERE Package_ID = ' . $this->mPackageId.' LIMIT 1';
			if (! $result = $database->query ( $get_on_offer_of_week_sql )) {
				$error = new Error ( 'Could not fetch the on offer of the week information for package ' . $this->mPackageId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$offer_of_the_week = $result->fetch ( PDO::FETCH_OBJ );
			$this->mOfferOfWeek = $offer_of_the_week->Offer_Of_Week;
		}
		return $this->mOfferOfWeek;
	}

	//! Change the quantity of a given product in the package - so to include 2xproduct1 in the package you would set $product to product1 and $newQty to 2.
	/*!
	 * @param $product - Obj : ProductModel - The product to increase the quantity of
	 * @param $newQty - Int - The quantity to change to
	 * @return true on success, exception otherwise
	 */
	function SetProductQty($product,$newQty) {
		$sql = 'UPDATE tblPackage_Products SET Qty = '.$newQty.' WHERE Product_ID = '.$product->GetProductId().' AND Package_ID = '.$this->GetPackageId();
		if(!$this->mDatabase->query($sql)) {
			$error = new Error('Could not update the quantity of '.$product->GetDisplayName().' for package '.$this->mPackageId);
			$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
			throw new Exception($error->GetErrorMsg());
		} else {
			return true;
		}
	}

	//! Get the quantity of a product in the package
	/*!
	 * @param $product - The product to get the quantity of
	 * @return Int on success, exception on failure
	 */
	function GetProductQty($product) {
		$sql = 'SELECT Qty FROM tblPackage_Products WHERE Package_ID = '.$this->GetPackageId().' AND Product_ID = '.$product->GetProductId();
		if(!$result = $this->mDatabase->query($sql)) {
			$error = new Error('Could not fetch the quantity of '.$product->GetDisplayName().' for package '.$this->mPackageId);
			$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
			throw new Exception($error->GetErrorMsg());
		}
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		return $resultObj->Qty;
	}

	//! Get the quantity of an upgrade in the package
	/*!
	 * @param $product - The upgrade to get the quantity of
	 * @return Int on success, exception on failure
	 */
	function GetUpgradeQty($upgrade) {
		$sql = 'SELECT Product_ID FROM tblPackage_Upgrades WHERE Package_ID = '.$this->GetPackageId().' AND Upgrade_ID = '.$upgrade->GetProductId();
		if(!$result = $this->mDatabase->query($sql)) {
			$error = new Error('Could not fetch the quantity of '.$upgrade->GetDisplayName().' for package '.$this->mPackageId);
			$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
			throw new Exception($error->GetErrorMsg());
		}
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		$product = new ProductModel($resultObj->Product_ID);
		return $this->GetProductQty($product);
	}

	//! Adds a product to the package
	/*!
	 * @param $product - Obj : ProductModel - The product to add
	 * @return True on success, exception on failure
	 */
	function AddProduct($product) {
		$registry = Registry::getInstance();
		$database = $registry->database;
		//! Checks not already a link here. Not throwing an exception here makes this non-destructive; adding more links between 2 products has no effect.
		$check_sql = 'SELECT Package_ID FROM tblPackage_Products WHERE Package_ID = ' . $this->GetPackageId () . ' AND Product_ID = ' . $product->GetProductId ();
		if ($result = $database->query ( $check_sql )) {
			if ($result->fetchColumn () == 0) {
				$add_product_sql = 'INSERT INTO tblPackage_Products (`Package_ID`,`Product_ID`,`Qty`) VALUES ('.$this->GetPackageId().','.$product->GetProductId().',1)';
				if (FALSE === $database->query ( $add_product_sql )) {
					$error = new Error ( 'Problem creating link between product ' . $product->GetProductId () . ' and package ' . $this->GetPackageId () . ' with SQL:<br /> ' . $add_product_sql );
					$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
					throw new Exception ( $error->getErrorMsg () );
				}
			}
		} else {
			$error = new Error ( 'Could not check the product you are trying to add to ' . $this->mPackageId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		return true;
	}

	//! Adds $upgrade as an upgrade from $product
	/*!
	 * @param $product [in] Obj:ProductModel
	 * @param $upgrade [in] Obj:ProductModel
	 * @return Boolean True if successful, Exception thrown otherwise
	 */
	function AddUpgrade($product, $upgrade) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		//! Checks not already a link here. Not throwing an exception here makes this non-destructive; adding more links between 2 products has no effect.
		$check_sql = 'SELECT Package_ID FROM tblPackage_Upgrades WHERE Package_ID = ' . $this->GetPackageId () . ' AND Product_ID = ' . $product->GetProductId () . ' AND Upgrade_ID = ' . $upgrade->GetProductId ();
		if ($result = $database->query ( $check_sql )) {
			if ($result->fetchColumn () == 0) {
				$add_product_sql = 'INSERT INTO tblPackage_Upgrades (`Package_ID`,`Product_ID`,`Upgrade_ID`) VALUES (' . $this->GetPackageId () . ',' . $product->GetProductId () . ',' . $upgrade->GetProductId () . ')';
				if (FALSE === $database->query ( $add_product_sql )) {
					$error = new Error ( 'Problem creating link between product ' . $product->GetProductId () . ' and upgrade ' . $upgrade->GetProductId () . ' and package ' . $this->GetPackageId () . ' with SQL:<br /> ' . $add_product_sql );
					$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
					throw new Exception ( $error->getErrorMsg () );
				}
			}
		} else {
			$error = new Error ( 'Could not check the product you are trying to upgrade to ' . $this->mPackageId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		return true;
	}

	//! Gets the upgrade price for upgrading $product to $upgrade in the current package
	/*!
	 * @param $product [in] Obj:ProductModel
	 * @param $upgrade [in] Obj:ProductModel
	 * @return $upgradePrice [out] Decimal
	 */
	function GetUpgradePrice($product, $upgrade) {
		$sql = 'SELECT tblPackage_Upgrades.Upgrade_Price FROM tblPackage_Upgrades WHERE Product_ID = ' . $product->GetProductId () . ' AND Upgrade_ID = ' . $upgrade->GetProductId () . ' AND Package_ID = \'' . $this->GetPackageId () . '\'';
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not fetch the upgrade price for package ' . $this->mPackageId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		$upgradePrice = $resultObj->Upgrade_Price;
		return $upgradePrice;
	}

	function GetProductForUpgrade($upgrade) {
		$sql = 'SELECT tblPackage_Upgrades.Product_ID FROM tblPackage_Upgrades WHERE Package_ID = ' . $this->mPackageId . ' AND Upgrade_ID = ' . $upgrade->GetProductId ();
		$result = $this->mDatabase->query ( $sql );
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		return new ProductModel ( $resultObj->Product_ID );
	}

	//! Sets the upgrade price for upgrading $product to $upgrade
	/*!
	 * @param $product [in] Obj:ProductModel
	 * @param $upgrade [in] Obj:ProductModel
	 * @param $upgradePrice [in] Decimal
	 * @return Boolean True if successful, Exception thrown otherwise
	 */
	function SetUpgradePrice($product, $upgrade, $upgradePrice) {
		$sql = 'UPDATE tblPackage_Upgrades SET Upgrade_Price = \'' . $upgradePrice . '\' WHERE Product_ID = ' . $product->GetProductId () . ' AND Upgrade_ID = ' . $upgrade->GetProductId () . ' AND Package_ID = ' . $this->GetPackageId () . '';
		if (FALSE === $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Problem updating upgrade price between product ' . $product->GetProductId () . ' and package ' . $this->GetPackageId () . ' with SQL:<br /> ' . $sql );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->getErrorMsg () );
		}
		return true;
	}

	//! Removes $upgrade from being an upgrade from $product
	/*!
	 * @param $product [in] Obj:ProductModel
	 * @param $upgrade [in] Obj:ProductModel
	 * @return Boolean True if successful, Exception thrown otherwise
	 */
	function RemoveUpgrade($product, $upgrade) {
		$delete_product_sql = 'DELETE FROM tblPackage_Upgrades WHERE Package_ID = ' . $this->GetPackageId () . ' AND Product_ID = ' . $product->GetProductId () . ' AND Upgrade_ID = ' . $upgrade->GetProductId ();
		if (FALSE === $this->mDatabase->query ( $delete_product_sql )) {
			$error = new Error ( 'Problem removing upgrade between product ' . $product->GetProductId () . ' and package ' . $this->GetPackageId () . ' with SQL:<br /> ' . $delete_product_sql );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->getErrorMsg () );
		}
		return true;
	}

	function RemoveProduct($product) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$delete_product_sql = 'DELETE FROM tblPackage_Products WHERE Package_ID = ' . $this->GetPackageId () . ' AND Product_ID = ' . $product->GetProductId ();
		if (FALSE === $database->query ( $delete_product_sql )) {
			$error = new Error ( 'Problem removing link between product ' . $product->GetProductId () . ' and package ' . $this->GetPackageId () . ' with SQL:<br /> ' . $delete_product_sql );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->getErrorMsg () );
		}
		return true;
	}

	//! Returns true if all products in the package are in stock, false otherwise
	function AllInStock() {
		$allInStock = true;
		foreach($this->GetContents() as $product) {
			// If not marked as for sale, or is hidden
			if(!$product->GetForSale() || $product->GetHidden()) {
				$allInStock = false;
			} else {
				// Only check SKUs if we need to, and a single-SKU product (still OK to show a flavour out of stock)
				if(count($product->GetAttributes()) == 0) {
					foreach($product->GetSkus() as $sku) {
						// If SKU quantity is zero
						if($sku->GetQty() == 0) {
							$allInStock = false;
						}
					} // end foreach
				} else {
					$allSkusOut = true; // Init to true, set to false if ONE is in stock
					// The product has flavours - make sure ALL aren't out of stock
					foreach($product->GetSkus() as $sku) {
						// If SKU quantity is zero
						if($sku->GetQty() > 0) {
							$allSkusOut = false;
						}
					} // end foreach
					// If all SKUs out then whole product out, otherwise allow to choose
					if($allSkusOut) {
						$allInStock = false;
					}
				} // end if
			}
		}
		return $allInStock;
	} // End AllInStock

	//! Checks to make sure the package is better value than buying the products alone
	function IsBetterValue() {
		$betterValue = true;
		$runningTotal = 0;
		foreach($this->GetContents() as $product) {
			$runningTotal += ($this->GetProductQty($product) * $product->GetActualPrice());
		}
		if($runningTotal < $this->GetActualPrice()) {
			$betterValue = false;
		}
		return $betterValue;
	}

	//! Returns how much the user is saving between the RRP and Actual Price.
	/*!
	 * @param $percentage - Boolean - If true returns a percentage, otherwise returns the direct amount
	 * @return Decimal - The percentage or actual saving as appropriate
	 */
	function GetSaving($percentage=false) {
		$savings = $this->GetWasPrice()-$this->GetActualPrice();
		if($percentage) {
			if($this->GetWasPrice()>0) {
				$savingsPercentage = round(($savings/$this->GetWasPrice())*100,0);
				return $savingsPercentage;
			} else {
				return '';
			}
		} else {
			return $savings;
		}
	}

	//! Returns the unique package ID (Set in the constructor)
	/*!
	* @return Int
	*/
	function GetPackageId() {
		return $this->mPackageId;
	}

}

?>