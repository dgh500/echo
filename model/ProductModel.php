<?php

//! Models a single product (Eg. A pair of fins). When initialising it needs the product ID
class ProductModel {

	//! Decimal(19,4) : Price the customer will pay
	var $mActualPrice;
	//! Array : An array of attriubtes, each of which is a ProductAttribute object
	var $mAttributes;
	//! Obj:CatalogueModel - the catalogue this product belongs to
	var $mCatalogue;
	//! Array : An array of categories, each is a Category object
	var $mCategories;
	//! String(200) : A short description of the product, for use on the listings page
	var $mDescription;
	//! String(100) : The product name
	var $mDisplayName;
	//! Boolean : Whether product is for sale
	var $mForSale;
	//! Array : An array of images, each of which is an Image object
	var $mImages;
	//! Boolean : Whether product is in stock
	var $mInStock;
	//! String(4000) : A full description of the product for usage on the product page
	var $mLongDescription;
	//! Obj:ImageModel : The main image of this product
	var $mMainImage;
	//! Object : An object of type ManufacturerModel
	var $mManufacturer;
	//! Boolean : Whether the product has a multibuy option
	var $mMultibuy;
	//! Boolean : Whether product is on offer of the week
	var $mOfferOfWeek;
	//! Boolean : Whether product is on clearance
	var $mOnClearance;
	//! Boolean : Whether product is on sale
	var $mOnSale;
	//! Decimal(19,4) : Cost of postage
	var $mPostage;
	//! Int : Unique product ID
	var $mProductId;
	//! Array : An array of related products, each of which is a ProductModel object
	var $mRelated;
	//! Array : An array of similar products, each of which is a ProductModel object
	var $mSimilar;
	//! Array : An array of StockKeepingUnits each of which is a SkuModel object
	var $mSkus;
	//! Int : The tax code of this product
	var $mTaxCode;
	//! Array : An array of upgrade products, each of which is a ProductModel object
	var $mUpgrades;
	//! Decimal(19,4) : Price to upgrade to this product
	var $mUpgradePrice;
	//! Decimal(19,4) : Price that will be crossed out
	var $mWasPrice;
	//! Int : Weight of the product in grams
	var $mWeight;
	//! Array : An array of TagModel objects
	var $mTags;

	//! Constructor, initialises the product ID. Throws an exception if the product doesn't exist
	function __construct($productId) {
		$this->mRegistry = Registry::getInstance ();
		$this->mDatabase = $this->mRegistry->database;
		$does_this_product_exist_sql = 'SELECT COUNT(Product_ID) AS ProductCount FROM tblProduct WHERE Product_ID = ' . $productId;
		$result = $this->mDatabase->query ( $does_this_product_exist_sql );
		if ($result) {
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			if ($resultObj->ProductCount > 0) {
				$this->mProductId = $productId;
			} else {
				$error = new Error ( 'Could not initialise product ' . $productId . ' because it does not exist in the database.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		} else {
			$error = new Error ( 'Could not initialise product ' . $productId . ' because it does not exist in the database.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

	//! Return the product name if a string is needed
	function __toString() {
		return $this->GetDisplayName ();
	}

	//! Returns the price paid by the customer. If this has already been done then it is returned directly, otherwise it is retrieved from the database
	/*!
	* @return Decimal(19,4)
	*/
	function GetActualPrice() {
		if (! isset ( $this->mActualPrice )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_actual_price_sql = 'SELECT Actual_Price FROM tblProduct WHERE Product_ID = ' . $this->mProductId.' LIMIT 1';
			if (! $result = $database->query ( $get_actual_price_sql )) {
				$error = new Error ( 'Could not fetch the actual price for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$actual_price = $result->fetch ( PDO::FETCH_OBJ );
			$this->mActualPrice = $actual_price->Actual_Price;
		}
		return $this->mActualPrice;
	}

	//! Sets the actual price of the product
	/*!
	* @param [in] newActualPrice Decimal(19,4) : The new actual price
	* @return Bool : true if successful
	*/
	function SetActualPrice($newActualPrice) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_actual_price_sql = 'UPDATE tblProduct SET Actual_Price = \'' . $newActualPrice . '\' WHERE Product_ID = ' . $this->mProductId;
		if (is_numeric ( $newActualPrice )) {
			if (! $database->query ( $set_actual_price_sql )) {
				$error = new Error ( 'Could not update the actual price for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		// If there are no attributes, update the SKU price as well
		if (count ( $this->GetAttributes () ) == 0) {
			$skuArr = $this->GetSkus ();
			$sku = $skuArr [0];
			$sql = 'UPDATE tblSKU SET SKU_Price = \'' . $newActualPrice . '\' WHERE SKU_ID = ' . $sku->GetSkuId () . '';
			$database->query ( $sql );
		}
		$this->mActualPrice = $newActualPrice;
		return true;
	}

	//! Returns those attributes (Eg. Size/Colour) that a product has
	/*!
	* @return Array of ProductAttribute objects
	*/
	function GetAttributes() {
		if (! isset ( $this->mAttributes )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_product_attributes_sql = 'SELECT Product_Attribute_ID FROM tblProduct_Attributes WHERE Product_ID = ' . $this->mProductId;
			if (! $result = $database->query ( $get_product_attributes_sql )) {
				$error = new Error ( 'Could not fetch the attributes for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$product_attributes = $result->fetchAll ( PDO::FETCH_OBJ );
			// For each product attribute, create a new instance of it and store it in the mAttributes member variable
			foreach ( $product_attributes as $value ) {
				$newAttribute = new ProductAttributeModel ( $value->Product_Attribute_ID );
				$this->mAttributes [] = $newAttribute;
			}
			if (0 == count ( $product_attributes )) {
				$this->mAttributes = array ();
			}
		}
		return $this->mAttributes;
	}

	//! Whether or not the product has any attributes
	/*!
	 * @return Boolean
	 */
	function HasNoAttributes() {
		$numberOfAttributes = count ( $this->GetAttributes () );
		if ($numberOfAttributes == 0) {
			return true;
		} else {
			return false;
		}
	}

	//! Returns catalogue associated with a product
	/*!
	* @return Catalogue objects
	*/
	function GetCatalogue() {
		if (! isset ( $this->mCatalogue )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_category_sql = 'SELECT Category_ID FROM tblCategory_Products WHERE Product_ID = ' . $this->mProductId.' LIMIT 1';
			if (! $result = $database->query ( $get_category_sql )) {
				$error = new Error ( 'Could not fetch the category for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$category = $result->fetch ( PDO::FETCH_OBJ );
			$categoryId = $category->Category_ID;
			$get_catalogue_sql = 'SELECT Catalogue_ID FROM tblCategory WHERE Category_ID = ' . $categoryId;
			if (! $result = $database->query ( $get_catalogue_sql )) {
				$error = new Error ( 'Could not fetch the catalogue for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$catalogue = $result->fetch ( PDO::FETCH_OBJ );
			$catalogueId = $catalogue->Catalogue_ID;
			$this->mCatalogue = new CatalogueModel ( $catalogueId );
		}
		return $this->mCatalogue;
	}

	//! Returns all categories associated with a product
	/*!
	* @return Array of Category objects
	*/
	function GetCategories() {
		if (! isset ( $this->mCategories )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_product_categories_sql = 'SELECT Category_ID FROM tblCategory_Products WHERE Product_ID = ' . $this->mProductId;
			if (! $result = $database->query ( $get_product_categories_sql )) {
				$error = new Error ( 'Could not fetch the categories for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$product_categories = $result->fetchAll ( PDO::FETCH_OBJ );
			// For each product category, create a new instance of it and store it in the mAttributes member variable
			foreach ( $product_categories as $value ) {
				$newCategory = new CategoryModel ( $value->Category_ID );
				$this->mCategories [] = $newCategory;
			}
			if (0 == count ( $product_categories )) {
				$this->mCategories = array ();
			}
		}
		return $this->mCategories;
	}

	//! Returns a short description of a product for use on the product listings page
	/*!
	* @return String(200)
	*/
	function GetDescription() {
		if (! isset ( $this->mDescription )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_description_sql = 'SELECT Description FROM tblProduct_Text WHERE Product_ID = ' . $this->mProductId.' LIMIT 1';
			if (! $result = $database->query ( $get_description_sql )) {
				$error = new Error ( 'Could not fetch the description for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$description = $result->fetch ( PDO::FETCH_OBJ );
			if ($description) {
				$this->mDescription = $description->Description;
			} else {
				$this->mDescription = '';
			}
		}
		return $this->mDescription;
	}

	//! Sets the description of the product
	/*!
	* @param [in] newDescription String : The new description
	* @return Bool : true if successful
	*/
	function SetDescription($newDescription) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_description_sql = 'UPDATE tblProduct_Text SET Description = \'' . mysql_escape_string($newDescription) . '\' WHERE Product_ID = ' . $this->mProductId;
		if (! $database->query ( $set_description_sql )) {
			$error = new Error ( 'Could not update the description for product ' . $this->mProductId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDescription = $newDescription;
		return true;
	}

	//! Returns the product name
	/*!
	* @return String(100)
	*/
	function GetDisplayName() {
		if (! isset ( $this->mDisplayName )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_display_name_sql = 'SELECT Display_Name FROM tblProduct_Text WHERE Product_ID = ' . $this->mProductId.' LIMIT 1';
			if (! $result = $database->query ( $get_display_name_sql )) {
				$error = new Error ( 'Could not fetch the display name for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$display_name = $result->fetch ( PDO::FETCH_OBJ );
			if ($display_name) {
				$this->mDisplayName = $display_name->Display_Name;
			} else {
				$this->mDisplayName = '';
			}
		}
		return $this->mDisplayName;
	}

	//! Sets the display name of the product
	/*!
	* @param [in] newDisplayName String : The new display name
	* @return Bool : true if successful
	*/
	function SetDisplayName($newDisplayName) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_display_name_sql = 'UPDATE tblProduct_Text SET Display_Name = \'' . $newDisplayName . '\' WHERE Product_ID = ' . $this->mProductId;
		if (! $database->query ( $set_display_name_sql )) {
			$error = new Error ( 'Could not update the display name for product ' . $this->mProductId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDisplayName = $newDisplayName;
		return true;
	}

	//! Returns whether the product is for sale
	/*!
	* @return String(1) - Either 0 or 1 (False or True)
	*/
	function GetForSale() {
		if (! isset ( $this->mForSale )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_for_sale_sql = 'SELECT For_Sale FROM tblProduct WHERE Product_ID = ' . $this->mProductId.' LIMIT 1';
			if (! $result = $database->query ( $get_for_sale_sql )) {
				$error = new Error ( 'Could not fetch the for sale information for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$for_sale = $result->fetch ( PDO::FETCH_OBJ );
			$this->mForSale = $for_sale->For_Sale;
		}
		return $this->mForSale;
	}

	//! Sets the for sale option of the product
	/*!
	* @param [in] newForSale String(1) : Either 0 or 1
	* @return Bool : true if successful
	*/
	function SetForSale($newForSale) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_for_sale_sql = 'UPDATE tblProduct SET For_Sale = \'' . $newForSale . '\' WHERE Product_ID = ' . $this->mProductId;
		if (! $database->query ( $set_for_sale_sql )) {
			$error = new Error ( 'Could not update the for sale option for product ' . $this->mProductId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mForSale = $newForSale;
		return true;
	}

	//! Returns all images associated with a product
	/*!
	* @return Array of Image objects
	*/
	function GetImages() {
		if (! isset ( $this->mImages )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_product_images_sql = 'SELECT Image_ID FROM tblProduct_Images WHERE Product_ID = ' . $this->mProductId;
			if (! $result = $database->query ( $get_product_images_sql )) {
				$error = new Error ( 'Could not fetch the images for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$product_images = $result->fetchAll ( PDO::FETCH_OBJ );
			// For each product attribute, create a new instance of it and store it in the mAttributes member variable
			foreach ( $product_images as $value ) {
				$newImage = new ImageModel ( $value->Image_ID );
				$this->mImages [] = $newImage;
			}
			if (0 == count ( $product_images )) {
				$this->mImages = array ();
			}
		}
		return $this->mImages;
	}

	//! Returns the main image associated with a product
	/*!
	* @return Image object
	*/
	function GetMainImage() {
		if (! isset ( $this->mMainImage )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_main_image_sql = 'SELECT tblImage.Image_ID
										FROM tblProduct_Images
										INNER JOIN tblImage ON tblProduct_Images.Image_ID = tblImage.Image_ID
										WHERE Product_ID = ' . $this->mProductId . '
										AND tblImage.Main_Image = 1
										LIMIT 1';
			if (! $result = $database->query ( $get_main_image_sql )) {
				$error = new Error ( 'Could not fetch the main image for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$main_image = $result->fetch ( PDO::FETCH_OBJ );
			if (@is_null ( $main_image->Image_ID )) {
				$this->mMainImage = NULL;
			} else {
				$this->mMainImage = new ImageModel ( $main_image->Image_ID );
			}
		}
		return $this->mMainImage;
	}

	//! Returns whether the product is in stock
	/*!
	* @return String(1) - Either 0 or 1 (False or True)
	*/
	function GetInStock() {
		if (! isset ( $this->mInStock )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_in_stock_sql = 'SELECT In_Stock FROM tblProduct WHERE Product_ID = ' . $this->mProductId.' LIMIT 1';
			if (! $result = $database->query ( $get_in_stock_sql )) {
				$error = new Error ( 'Could not fetch the in stock information for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$in_stock = $result->fetch ( PDO::FETCH_OBJ );
			$this->mInStock = $in_stock->In_Stock;
		}
		return $this->mInStock;
	}

	//! Sets the in stock option of the product
	/*!
	* @param [in] newInStock String(1) : Either 0 or 1
	* @return Bool : true if successful
	*/
	function SetInStock($newInStock) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_in_stock_sql = 'UPDATE tblProduct SET In_Stock = \'' . $newInStock . '\' WHERE Product_ID = ' . $this->mProductId;
		if (! $database->query ( $set_in_stock_sql )) {
			$error = new Error ( 'Could not update the in stock option for product ' . $this->mProductId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mInStock = $newInStock;
		return true;
	}

	//! Returns the full product description
	/*!
	* @return String
	*/
	// Because PHPs PDO implementation of mysql can't take more than 4096 bytes this HAS to be done direct using
	// mysql_ functions
	function GetLongDescription() {
		$registry = Registry::getInstance ();
		if (! isset ( $this->mLongDescription )) {
			mysql_connect ( $registry->host, $registry->username, $registry->password );
			mysql_select_db ( $registry->dbName );
			$sql = 'SELECT Long_Description FROM tblProduct_Text WHERE Product_ID = ' . $this->mProductId.' LIMIT 1';
			if (! $result = mysql_query ( $sql )) {
				$error = new Error ( 'Could not fetch the long description for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$long_description = mysql_fetch_object ( $result );
			$this->mLongDescription = $long_description->Long_Description;
		}
		return $this->mLongDescription;
	}

	//! Sets the long description of the product
	/*!
	* @param [in] newLongDesc String : The new long description
	* @return Bool : true if successful
	*/
	function SetLongDescription($newLongDesc) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$sql = 'UPDATE tblProduct_Text SET Long_Description = \'' . mysql_escape_string($newLongDesc) . '\' WHERE Product_ID = ' . $this->mProductId;
		if (! $database->query ( $sql )) {
			$error = new Error ( 'Could not update the long description for product ' . $this->mProductId .'<br><br>'.$sql);
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mLongDescription = $newLongDesc;
		return true;
	}

	//! Returns the echo product description, or false if none exists
	/*!
	* @return String
	*/
	// Because PHPs PDO implementation of mysql can't take more than 4096 bytes this HAS to be done direct using
	// mysql_ functions
	function GetEchoDescription() {
		$registry = Registry::getInstance ();
		if (! isset ( $this->mEchoDescription )) {
			mysql_connect ( $registry->host, $registry->username, $registry->password );
			mysql_select_db ( $registry->dbName );
			$sql = 'SELECT Echo_Description FROM tblProduct_Text WHERE Product_ID = ' . $this->mProductId.' LIMIT 1';
			if (! $result = mysql_query ( $sql )) {
				$error = new Error ( 'Could not fetch the echo description for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = mysql_fetch_object ( $result );
			$this->mEchoDescription = $resultObj->Echo_Description;
		}
		if(trim($this->mEchoDescription)) {
			return $this->mEchoDescription;
		} else {
			return false;
		}
	}

	//! Sets the echo description of the product
	/*!
	* @param [in] newEchoDesc String : The new echo description
	* @return Bool : true if successful
	*/
	function SetEchoDescription($newEchoDesc) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$sql = 'UPDATE tblProduct_Text SET Echo_Description = \'' . mysql_escape_string($newEchoDesc) . '\' WHERE Product_ID = ' . $this->mProductId;
		if (! $database->query ( $sql )) {
			$error = new Error ( 'Could not update the echo description for product ' . $this->mProductId .'<br><br>'.$sql);
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mEchoDescription = $newEchoDesc;
		return true;
	}

	//! Returns the product manufacturer
	/*!
	* @return Object of type ManufacturerModel or NULL if the product has no manufacturer
	*/
	function GetManufacturer() {
		if (! isset ( $this->mManufacturer )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$sql = 'SELECT Manufacturer_ID FROM tblProduct WHERE Product_ID = ' . $this->mProductId.' LIMIT 1';
			if (! $result = $database->query ( $sql )) {
				$error = new Error ( 'Could not fetch the manufacturer for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$manufacturer_id = $result->fetch ( PDO::FETCH_OBJ );

			if (is_null ( $manufacturer_id->Manufacturer_ID )) {
				$this->mManufacturer = NULL;
			} else {
				$this->mManufacturer = new ManufacturerModel ( $manufacturer_id->Manufacturer_ID );
			}
		}
		return $this->mManufacturer;
	}

	//! Sets the manufacturer of the product
	/*!
	* @param [in] newManufacturer Obj:Manufacturer : The new manufacturer
	* @return Bool : true if successful
	*/
	function SetManufacturer($newManufacturer) {
		$set_manufacturer_sql = 'UPDATE tblProduct SET Manufacturer_ID = \'' . $newManufacturer->GetManufacturerId () . '\' WHERE Product_ID = ' . $this->mProductId;
		if (! $this->mDatabase->query ( $set_manufacturer_sql )) {
			$error = new Error ( 'Could not update the manufacturer for product ' . $this->mProductId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mManufacturer = $newManufacturer;
		return true;
	}

	//! Set the manufacturer of a product to NULL (Used internally when creating a product)
	/*!
	 * @return Bool - True if successful
	 */
	function RemoveManufacturer() {
		$sql = 'UPDATE tblProduct SET Manufacturer_ID = NULL WHERE Product_ID = ' . $this->mProductId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not remove the manufacturer for product ' . $this->mProductId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mManufacturer = NULL;
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
			$get_on_offer_of_week_sql = 'SELECT Offer_Of_Week FROM tblProduct WHERE Product_ID = ' . $this->mProductId.' LIMIT 1';
			if (! $result = $database->query ( $get_on_offer_of_week_sql )) {
				$error = new Error ( 'Could not fetch the on offer of the week information for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$offer_of_the_week = $result->fetch ( PDO::FETCH_OBJ );
			$this->mOfferOfWeek = $offer_of_the_week->Offer_Of_Week;
		}
		return $this->mOfferOfWeek;
	}

	//! Sets the on offer of the week option of the product
	/*!
	* @param [in] newOnOfferOfWeek String(1) : Either 0 or 1
	* @return Bool : true if successful
	*/
	function SetOfferOfWeek($newOnOfferOfWeek) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_on_offer_of_week_sql = 'UPDATE tblProduct SET Offer_Of_Week = \'' . $newOnOfferOfWeek . '\' WHERE Product_ID = ' . $this->mProductId;
		if (! $database->query ( $set_on_offer_of_week_sql )) {
			$error = new Error ( 'Could not update the on offer of the week option for product ' . $this->mProductId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mOfferOfWeek = $newOnOfferOfWeek;
		return true;
	}

	//! Synonym of GetOnClearance as this flag is now used to indicate non stock products
	function IsNonStockProduct() {
		return $this->GetOnClearance();
	}

	//! Returns whether the product is on clearance
	/*!
	* @return String(1) - Either 0 or 1 (False or True)
	*/
	function GetOnClearance() {
		if (! isset ( $this->mOnClearance )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_on_clearance_sql = 'SELECT On_Clearance FROM tblProduct WHERE Product_ID = ' . $this->mProductId.' LIMIT 1';
			if (! $result = $database->query ( $get_on_clearance_sql )) {
				$error = new Error ( 'Could not fetch the on clearance information for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$on_clearance = $result->fetch ( PDO::FETCH_OBJ );
			$this->mOnClearance = $on_clearance->On_Clearance;
		}
		return $this->mOnClearance;
	}

	//! Sets the on clearance option of the product
	/*!
	* @param [in] newOnClearance String(1) : Either 0 or 1
	* @return Bool : true if successful
	*/
	function SetOnClearance($newOnClearance) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_on_clearance_sql = 'UPDATE tblProduct SET On_Clearance = \'' . $newOnClearance . '\' WHERE Product_ID = ' . $this->mProductId;
		if (! $database->query ( $set_on_clearance_sql )) {
			$error = new Error ( 'Could not update the on clearance option for product ' . $this->mProductId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mOnClearance = $newOnClearance;
		return true;
	}

	//! Returns whether the product is featured
	/*!
	* @return String(1) - Either 0 or 1 (False or True)
	*/
	function GetFeatured() {
		if(!isset($this->mFeatured)) {
			$registry = Registry::getInstance();
			$database = $registry->database;
			$sql = 'SELECT Featured FROM tblProduct WHERE Product_ID = '.$this->mProductId.' LIMIT 1';
			if (! $result = $database->query ( $sql )) {
				$error = new Error ( 'Could not fetch the featured information for product '.$this->mProductId);
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mFeatured = $resultObj->Featured;
		}
		return $this->mFeatured;
	}

	//! Returns whether the product is hidden
	/*!
	* @return String(1) - Either 0 or 1 (False or True)
	*/
	function GetHidden() {
		if(!isset($this->mHidden)) {
			$registry = Registry::getInstance();
			$database = $registry->database;
			$sql = 'SELECT Hidden FROM tblProduct WHERE Product_ID = '.$this->mProductId.' LIMIT 1';
			if (! $result = $database->query ( $sql )) {
				$error = new Error ( 'Could not fetch the hidden flag for product '.$this->mProductId);
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mHidden = $resultObj->Hidden;
		}
		return $this->mHidden;
	}

	//! Sets the featured option of the product
	/*!
	* @param [in] newFeatured String(1) : Either 0 or 1
	* @return Bool : true if successful
	*/
	function SetFeatured($newFeatured) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$sql = 'UPDATE tblProduct SET Featured = \''.$newFeatured.'\' WHERE Product_ID = '.$this->mProductId;
		if (! $database->query ( $sql )) {
			$error = new Error ( 'Could not update the featured option for product ' . $this->mProductId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mFeatured = $newFeatured;
		return true;
	}

	//! Sets the hidden option of the product
	/*!
	* @param [in] newHidden String(1) : Either 0 or 1
	* @return Bool : true if successful
	*/
	function SetHidden($newHidden) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$sql = 'UPDATE tblProduct SET Hidden = \''.$newHidden.'\' WHERE Product_ID = '.$this->mProductId;
		if (! $database->query ( $sql )) {
			$error = new Error ( 'Could not update the hidden option for product ' . $this->mProductId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mHidden = $newHidden;
		return true;
	}

	//! Returns whether the product is on sale
	/*!
	* @return String(1) - Either 0 or 1 (False or True)
	*/
	function GetOnSale() {
		if (! isset ( $this->mOnSale )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_on_sale_sql = 'SELECT On_Sale FROM tblProduct WHERE Product_ID = ' . $this->mProductId.' LIMIT 1';
			if (! $result = $database->query ( $get_on_sale_sql )) {
				$error = new Error ( 'Could not fetch the on sale information for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$on_sale = $result->fetch ( PDO::FETCH_OBJ );
			$this->mOnSale = $on_sale->On_Sale;
		}
		return $this->mOnSale;
	}

	//! Sets the on sale option of the product
	/*!
	* @param [in] newOnsale String(1) : Either 0 or 1
	* @return Bool : true if successful
	*/
	function SetOnsale($newOnsale) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_on_sale_sql = 'UPDATE tblProduct SET On_sale = \'' . $newOnsale . '\' WHERE Product_ID = ' . $this->mProductId;
		if (! $database->query ( $set_on_sale_sql )) {
			$error = new Error ( 'Could not update the on sale option for product ' . $this->mProductId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mOnsale = $newOnsale;
		return true;
	}

	//! Returns whether the product has multibuy options
	/*!
	* @return String(1) - Either 0 or 1 (False or True)
	*/
	function GetMultibuy() {
		if (! isset ( $this->mMultibuy )) {
			$sql = 'SELECT Multibuy FROM tblProduct WHERE Product_ID = ' . $this->mProductId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the multibuy information for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mMultibuy = $resultObj->Multibuy;
		}
		return $this->mMultibuy;
	}

	//! Sets whether the product has multibuy options
	/*!
	* @param [in] newMultibuy String(1) : Either 0 or 1
	* @return Bool : true if successful
	*/
	function SetMultibuy($newMultibuy) {
		$sql = 'UPDATE tblProduct SET Multibuy = \'' . $newMultibuy . '\' WHERE Product_ID = ' . $this->mProductId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the multibuy option for product ' . $this->mProductId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mMultibuy = $newMultibuy;
		return true;
	}

	//! Gets the cheapest multibuy price for this product
	function GetCheapestMultibuy() {
		if (! isset ( $this->mCheapestMultibuy )) {
			$sql = 'SELECT Price FROM tblProduct_Multibuy WHERE Product_ID = '.$this->mProductId.' ORDER BY Quantity DESC LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the multibuy information for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mCheapestMultibuy = $resultObj->Price;
		}
		return $this->mCheapestMultibuy;
	} // End GetCheapestMultibuy

	//! Gets the details of the multibuy deals available for this product
	/*!
	 * @return : Array:
	 *		$this->mMultibuyDetails['quantity'] [0] = 10
	 *		$this->mMultibuyDetails['unitPrice'][0]	= 22.95
	 *		$this->mMultibuyDetails['description'][0]= 10 or more
	 * The indices match up and are numeric
	 */
	function GetMultibuyDetails() {
		$sql = 'SELECT Price, Quantity FROM tblProduct_Multibuy WHERE Product_ID = ' . $this->mProductId . ' ORDER BY Quantity ASC';
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not fetch the multibuy options for product ' . $this->mProductId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$resultObjs = $result->fetchAll ( PDO::FETCH_OBJ );
		$i = 0;
		foreach ( $resultObjs as $resultObj ) {
			$this->mMultibuyDetails [$i] ['quantity'] = $resultObj->Quantity;
			$this->mMultibuyDetails [$i] ['unitPrice'] = $resultObj->Price;
			$i ++;
		}
		if (0 == count ( $resultObjs )) {
			$this->mMultibuyDetails = array ();
		}
		return $this->mMultibuyDetails;
	}

	//! Get the price for $quantity occurences of a product in a basket. On failure returns the single (actual) price
	/*!
	 * @param $quantity - Int - The quantity to check
	 * @return Decimal - The price
	 */
	function GetMultibuyPriceFor($quantity) {
		$sql = 'SELECT Price from tblProduct_Multibuy WHERE Quantity <= ' . $quantity . ' AND Product_ID = ' . $this->mProductId . ' ORDER BY Price ASC LIMIT 1';
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not fetch the multibuy price for product ' . $this->mProductId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		if (is_object ( $resultObj )) {
			$price = $resultObj->Price;
		} else {
			$price = $this->GetActualPrice ();
		}
		return $price;
	}

	//! Inserts a multibuy option, given the quantity and price
	/*!
	 * @param $quantity : Int
	 * @param $price : Decimal
	 */
	function InsertMultibuy($quantity, $price) {
		if ($this->IsSafeToAddMultibuy ( $quantity )) {
			$sql = 'INSERT INTO tblProduct_Multibuy (Quantity, Price, Product_ID) VALUES (\'' . $quantity . '\',\'' . $price . '\',\'' . $this->mProductId . '\')';
			if (FALSE === $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Problem inserting multibuy with SQL:<br /> ' . $sql );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->getErrorMsg () );
			}
			return true;
		}
	}

	//! Amends a multibuy option, given the quantity and price
	/*!
	 * @param $quantity : Int
	 * @param $price : Decimal
	 */
	function AmendMultibuy($quantity, $price) {
		$sql = 'UPDATE tblProduct_Multibuy SET Price = \'' . $price . '\' WHERE Product_ID = ' . $this->mProductId . ' AND Quantity = \'' . $quantity . '\'';
		$this->mDatabase->query ( $sql );
		return true;
	}

	//! Removes the multibuy associated with $quantity
	/*!
	 * @param $quantity : Int - The quantity to remove
	 */
	function RemoveMultibuy($quantity) {
		$sql = 'DELETE FROM tblProduct_Multibuy WHERE Quantity = \'' . $quantity . '\' AND Product_ID = ' . $this->mProductId;
		if (FALSE === $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Problem removing multibuy with SQL:<br /> ' . $sql );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->getErrorMsg () );
		}
		return true;
	}

	//! Returnds whether it is safe to add a multibuy with the quantity param
	function IsSafeToAddMultibuy($quantity) {
		$sql = 'SELECT Count(Product_ID) AS MultibuyCount FROM tblProduct_Multibuy WHERE Product_ID = ' . $this->mProductId . ' AND Quantity = ' . $quantity;
		$result = $this->mDatabase->query ( $sql );
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		if ($resultObj->MultibuyCount > 0) {
			return false;
		} else {
			return true;
		}
	}

	//! Returns postage to be paid
	/*!
	* @return Decimal(19,4)
	*/
	function GetPostage() {
		if (! isset ( $this->mPostage )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_postage_sql = 'SELECT Postage FROM tblProduct WHERE Product_ID = ' . $this->mProductId.' LIMIT 1';
			if (! $result = $database->query ( $get_postage_sql )) {
				$error = new Error ( 'Could not fetch the postage for product ' . $this->mProductId );
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
		$set_postage_sql = 'UPDATE tblProduct SET Postage = \'' . $newPostage . '\' WHERE Product_ID = ' . $this->mProductId;
		if (is_numeric ( $newPostage )) {
			if (! $database->query ( $set_postage_sql )) {
				$error = new Error ( 'Could not update the postage for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		$this->mPostage = $newPostage;
		return true;
	}

	//! Returns the product ID (Set in the constructor)
	/*!
	* @return Int
	*/
	function GetProductId() {
		return $this->mProductId;
	}

	//! Returns any related products
	/*!
	* @return Array of ProductModel objects (empty if none)
	*/
	function GetRelated() {
		if (! isset ( $this->mRelated )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;

			$get_similar_products_sql = '
			SELECT
				Similar_Product_ID
			FROM
				tblProduct_Similar
			INNER JOIN tblProduct ON tblProduct_Similar.Similar_Product_ID = tblProduct.Product_ID
			WHERE tblProduct_Similar.Product_ID = ' . $this->mProductId.'
			AND (SELECT COUNT(Category_ID) FROM tblCategory_Products WHERE Product_ID = Similar_Product_ID) > 0
			AND tblProduct.Hidden = 0 ';

			$sql = '
			SELECT
				Related_Product_ID
			FROM
				tblProduct_Related
			INNER JOIN
				tblProduct ON tblProduct_Related.Related_Product_ID = tblProduct.Product_ID
			WHERE tblProduct_Related.Product_ID = ' . $this->mProductId.'
			AND
				(SELECT COUNT(Category_ID) FROM tblCategory_Products WHERE Product_ID = Related_Product_ID) > 0
			AND
				tblProduct.Hidden = 0
			';
			if (! $result = $database->query ( $sql )) {
				$error = new Error ( 'Could not fetch the related products for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$related_products = $result->fetchAll ( PDO::FETCH_OBJ );
			// For each related product, create a new instance of it and store it in the mRelated member variable
			foreach ( $related_products as $value ) {
				$newRelatedProduct = new ProductModel ( $value->Related_Product_ID );
				$this->mRelated [] = $newRelatedProduct;
			}
			if (0 == count ( $related_products )) {
				$this->mRelated = array ();
			}
		}
		return $this->mRelated;
	}

	//! Returns any similar products
	/*!
	* @return Array of ProductModel objects, empty if none
	*/
	function GetSimilar() {
		if (! isset ( $this->mSimilar )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_similar_products_sql = '
			SELECT
				Similar_Product_ID
			FROM
				tblProduct_Similar
			INNER JOIN tblProduct ON tblProduct_Similar.Similar_Product_ID = tblProduct.Product_ID
			WHERE tblProduct_Similar.Product_ID = ' . $this->mProductId.'
			AND (SELECT COUNT(Category_ID) FROM tblCategory_Products WHERE Product_ID = Similar_Product_ID) > 0
			AND tblProduct.Hidden = 0 ';
			if (! $result = $database->query ( $get_similar_products_sql )) {
				$error = new Error ( 'Could not fetch the similar products for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$similar_products = $result->fetchAll ( PDO::FETCH_OBJ );
			// For each related product, create a new instance of it and store it in the mRelated member variable
			foreach ( $similar_products as $value ) {
				$newSimilarProduct = new ProductModel ( $value->Similar_Product_ID );
				$this->mSimilar [] = $newSimilarProduct;
			}
			if (0 == count ( $similar_products )) {
				$this->mSimilar = array ();
			}
		}
		return $this->mSimilar;
	}

	//! Returns the Stock Keeping Units associated with a product
	/*!
	* @return Array of SkuModel objects, empty if none
	*/
	function GetSkus() {
		if (! isset ( $this->mSkus ) || 0 == count ( $this->mSkus )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_skus_sql = 'SELECT SKU_ID FROM tblProduct_SKUs WHERE Product_ID = ' . $this->mProductId;
			if (! $result = $database->query ( $get_skus_sql )) {
				$error = new Error ( 'Could not fetch the SKUs for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$skus = $result->fetchAll ( PDO::FETCH_OBJ );
			// For each SKU, create a new instance of it and store it in the mSkus member variable
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

	//! Returns tax code of the product
	/*!
	* @return Obj:TaxCode
	*/
	function GetTaxCode() {
		if (! isset ( $this->mTaxCode )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_tax_code_sql = 'SELECT Tax_Code_ID FROM tblProduct WHERE Product_ID = ' . $this->mProductId.' LIMIT 1';
			if (! $result = $database->query ( $get_tax_code_sql )) {
				$error = new Error ( 'Could not fetch the tax code for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$taxCode = $result->fetch ( PDO::FETCH_OBJ );
			$this->mTaxCode = new TaxCodeModel ( $taxCode->Tax_Code_ID );
		}
		return $this->mTaxCode;
	}

	//! Sets the tax code of the product
	/*!
	* @param [in] newPostage : Obj:TaxCode : New tax code
	* @return Bool : true if successful
	*/
	function SetTaxCode($newTaxCode) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_tax_code_sql = 'UPDATE tblProduct SET Tax_Code_ID = \'' . $newTaxCode->GetTaxCodeId () . '\' WHERE Product_ID = ' . $this->mProductId;
		if (! $database->query ( $set_tax_code_sql )) {
			$error = new Error ( 'Could not update the tax code for product ' . $this->mProductId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mTaxCode = $newTaxCode;
		return true;
	}

	//! Returns any upgrade products
	/*!
	* @return Array of ProductModel objects, empty if none
	*/
	function GetUpgrades() {
		if (! isset ( $this->mUpgrades )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_product_upgrades_sql = 'SELECT tblProduct_Upgrade.Product_Upgrade_ID
											FROM tblProduct_Upgrade
												INNER JOIN tblProduct
												ON tblProduct_Upgrade.Product_Upgrade_ID = tblProduct.Product_ID
											WHERE tblProduct_Upgrade.Product_ID = ' . $this->mProductId . '
											ORDER BY tblProduct.Upgrade_Price ASC
											';
			if (! $result = $database->query ( $get_product_upgrades_sql )) {
				$error = new Error ( 'Could not fetch the upgrades for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$product_upgrades = $result->fetchAll ( PDO::FETCH_OBJ );
			// For each related product, create a new instance of it and store it in the mRelated member variable
			foreach ( $product_upgrades as $value ) {
				$newProductUpgrade = new ProductModel ( $value->Product_Upgrade_ID );
				$this->mUpgrades [] = $newProductUpgrade;
			}
			if (0 == count ( $product_upgrades )) {
				$this->mUpgrades = array ();
			}
		}
		return $this->mUpgrades;
	}

	//! Returns associated tags
	/*!
	* @return Array of TagModel objects, empty if none
	*/
	function GetTags() {
		if(!isset($this->mTags)) {
			$sql = 'SELECT tblProduct_Tags.Tag_ID
						FROM tblProduct_Tags
							INNER JOIN tblProduct
								ON tblProduct_Tags.Product_ID = tblProduct.Product_ID
							INNER JOIN tblTag
								ON tblProduct_Tags.Tag_ID = tblTag.Tag_ID
						WHERE tblProduct_Tags.Product_ID = '.$this->mProductId.'
						ORDER BY tblTag.Display_Name DESC';
			if(!$result = $this->mDatabase->query($sql)) {
				$error = new Error('Could not fetch the tags for product '.$this->mProductId);
				$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
				throw new Exception($error->GetErrorMsg());
			}
			$product_tags = $result->fetchAll(PDO::FETCH_OBJ);
			// For each tag, create a new instance of it and store it in the mTags member variable
			foreach($product_tags as $value) {
				$newTag = new TagModel($value->Tag_ID);
				$this->mTags[] = $newTag;
			}
			if (0 == count ( $product_tags )) {
				$this->mTags = array ();
			}
		}
		return $this->mTags;
	} // End GetTags()

	//! Returns the price that it would be to upgrade TO this product
	/*!
	* @return Decimal(19,4)
	*/
	function GetUpgradePrice() {
		if (! isset ( $this->mUpgradePrice )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_upgrade_price_sql = 'SELECT Upgrade_Price FROM tblProduct WHERE Product_ID = ' . $this->mProductId.' LIMIT 1';
			if (! $result = $database->query ( $get_upgrade_price_sql )) {
				$error = new Error ( 'Could not fetch the upgrade price for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$upgrade_price = $result->fetch ( PDO::FETCH_OBJ );
			$this->mUpgradePrice = $upgrade_price->Upgrade_Price;
		}
		return $this->mUpgradePrice;
	}

	//! Sets the upgrade price of the product
	/*!
	* @param newUpgradePrice : Decimal(19,4) : New upgrade price
	* @return Bool : true if successful
	*/
	function SetUpgradePrice($newUpgradePrice) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_upgrade_sql = 'UPDATE tblProduct SET Upgrade_Price = \'' . $newUpgradePrice . '\' WHERE Product_ID = ' . $this->mProductId;
		if (is_numeric ( $newUpgradePrice )) {
			if (! $database->query ( $set_upgrade_sql )) {
				$error = new Error ( 'Could not update the upgrade price for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		$this->mUpgradePrice = $newUpgradePrice;
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
			$get_was_price_sql = 'SELECT Was_Price FROM tblProduct WHERE Product_ID = ' . $this->mProductId.' LIMIT 1';
			if (! $result = $database->query ( $get_was_price_sql )) {
				$error = new Error ( 'Could not fetch the was price for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$was_price = $result->fetch ( PDO::FETCH_OBJ );
			$this->mWasPrice = $was_price->Was_Price;
		}
		return $this->mWasPrice;
	}

	//! Sets the was price of the product
	/*!
	* @param [in] newWasPrice : Decimal(19,4) : New was price
	* @return Bool : true if successful
	*/
	function SetWasPrice($newWasPrice) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_was_sql = 'UPDATE tblProduct SET Was_Price = \'' . $newWasPrice . '\' WHERE Product_ID = ' . $this->mProductId;
		if (is_numeric ( $newWasPrice )) {
			if (! $database->query ( $set_was_sql )) {
				$error = new Error ( 'Could not update the was price for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		$this->mWasPrice = $newWasPrice;
		return true;
	}

	//! Returns the products weight in grams
	/*!
	* @return Int
	*/
	function GetWeight() {
		if (! isset ( $this->mWeight )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_weight_sql = 'SELECT Weight FROM tblProduct WHERE Product_ID = ' . $this->mProductId.' LIMIT 1';
			if (! $result = $database->query ( $get_weight_sql )) {
				$error = new Error ( 'Could not fetch the weight price for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$weight = $result->fetch ( PDO::FETCH_OBJ );
			$this->mWeight = $weight->Weight;
		}
		return $this->mWeight;
	}

	//! Sets the weight of the product
	/*!
	* @param [in] newWeight : Decimal(19,4) : New weight
	* @return Bool : true if successful
	*/
	function SetWeight($newWeight) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_weight_sql = 'UPDATE tblProduct SET Weight = \'' . $newWeight . '\' WHERE Product_ID = ' . $this->mProductId;
		if (is_numeric ( $newWeight )) {
			if (! $database->query ( $set_weight_sql )) {
				$error = new Error ( 'Could not update the weight for product ' . $this->mProductId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		$this->mWeight = $newWeight;
		return true;
	}

	//! Returns whether this product is in a(ny) package
	/*!
	 * @return Boolean - True if it is, false otherwise
	 */
	function IsInSomePackage($includeUpgrades=false) {
		$sql = 'SELECT COUNT(Product_ID) AS ProductCount FROM tblPackage_Products WHERE Product_ID = '.$this->mProductId.' AND tblPackage_Products.Package_ID IN (SELECT Package_ID FROM tblPackage)';
		if(!$result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not fetch the in a package info for product '.$this->mProductId);
			$error->PdoErrorHelper($this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		if($resultObj->ProductCount == 0) {
			if($includeUpgrades) {
				$sql = 'SELECT COUNT(Product_ID) AS ProductCount FROM tblPackage_Upgrades WHERE Upgrade_ID = '.$this->mProductId.' AND tblPackage_Upgrades.Package_ID IN (SELECT Package_ID FROM tblPackage)';
				if(!$result = $this->mDatabase->query ( $sql )) {
					$error = new Error ( 'Could not fetch the in a package info for product '.$this->mProductId);
					$error->PdoErrorHelper($this->mDatabase->errorInfo (), __LINE__, __FILE__ );
					throw new Exception ( $error->GetErrorMsg () );
				}
				$resultObj = $result->fetch(PDO::FETCH_OBJ);
				if($resultObj->ProductCount == 0) {
					return false;
				} else {
					return true;
				}
			}
		} else {
			return true;
		}
	}

	//! Returns whether this product is in a simple (2-product) package
	/*!
	 * @return Boolean - False if not, the package ID if possible
	 */
	function IsInSimplePackage() {
		$sql = '
		SELECT Package_ID
		FROM tblPackage_Products
		WHERE Product_ID = '.$this->mProductId.'
		AND
			tblPackage_Products.Package_ID
			IN (SELECT Package_ID FROM tblPackage)';
		if(!$result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not fetch the in a package info for product '.$this->mProductId);
			$error->PdoErrorHelper($this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$resultArr = $result->fetchAll ( PDO::FETCH_OBJ );
		foreach($resultArr as $resultObj ) {
			$package = new PackageModel($resultObj->Package_ID);
			if(2 == count($package->GetContents())) {
				return $package;
			}
		}
		if (0 == count($resultArr)) {
			return false;
		}
	} // End IsInSimplePackage

	//! Returns the packages that this product is a member of
	/*!
	 * @param $num - Int
	 * @return Array of PackageModel Objects
	 */
	function GetPackages($num) {
		$retArr = array();
		$sql = 'SELECT Package_ID FROM tblPackage_Products WHERE Product_ID = '.$this->mProductId.' LIMIT '.$num;
		if(!$result = $this->mDatabase->query($sql)) {
			$error = new Error('Could not fetch the packages for product '.$this->mProductId);
			$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
			throw new Exception($error->GetErrorMsg());
		}
		$i=0;
		while($resultObj = $result->fetch(PDO::FETCH_OBJ)) {
			$package = new PackageModel($resultObj->Package_ID);
			$retArr[] = $package;
			$i++;
		}
		// If we haven't managed to get $num packages with the product in then look and see if it is an upgrade in any packages to 'fill out' the space
		if($i<$num) {
			$packageQtyNeeded = $num - $i;
			$sql = 'SELECT Package_ID FROM tblPackage_Upgrades WHERE Upgrade_ID = '.$this->mProductId.' AND Package_ID IN (SELECT Package_ID FROM tblPackage) LIMIT '.$packageQtyNeeded;
			if(!$result = $this->mDatabase->query($sql)) {
				$error = new Error('Could not fetch the packages for product '.$this->mProductId);
				$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
				throw new Exception($error->GetErrorMsg());
			}
			while($resultObj = $result->fetch(PDO::FETCH_OBJ)) {
				$package = new PackageModel($resultObj->Package_ID);
				$retArr[] = $package;
			}
		}
		return $retArr;
	} // End GetPackages

	//! Gets pending reviews for this product
	function GetPendingReviews() {
		$retArr = array();
		$sql = 'SELECT Review_ID FROM tblreview WHERE Product_ID = \''.$this->mProductId.'\' AND Approved = 0';
		if(!$result = $this->mDatabase->query($sql)) {
			throw new Exception('Unable to fetch pending reviews');
		}
		while($resultObj = $result->fetch(PDO::FETCH_OBJ)) {
			$review = new ReviewModel($resultObj->Review_ID);
			$retArr[] = $review;
		}
		return $retArr;
	} // End GetPendingReviews

	//! Gets approved reviews for this product
	function GetApprovedReviews() {
		$retArr = array();
		$sql = 'SELECT Review_ID FROM tblreview WHERE Product_ID = \''.$this->mProductId.'\' AND Approved = 1 ORDER BY Review_ID DESC';
		if(!$result = $this->mDatabase->query($sql)) {
			throw new Exception('Unable to fetch pending reviews');
		}
		while($resultObj = $result->fetch(PDO::FETCH_OBJ)) {
			$review = new ReviewModel($resultObj->Review_ID);
			$retArr[] = $review;
		}
		return $retArr;
	} // End GetPendingReviews

	//! Returns the number of products similar to this one
	/*!
	* @return Int
	*/
	function NumberOfSimilarProducts() {
		return count ( $this->GetSimilar () );
	}

	//! Returns the number of products related to this one
	/*!
	* @return Int
	*/
	function NumberOfRelatedProducts() {
		return count ( $this->GetRelated () );
	}

	//! Returns the number of product upgrades available
	/*!
	* @return Int
	*/
	function NumberOfProductUpgrades() {
		return count ( $this->GetUpgrades () );
	}

	//! Used by usort to compare product models by product ID
	/*!
	 * @param $a - ProductModel 1
	 * @param $b - ProductModel 2
	 * @return 1 or -1 depending on whether the product ID is less than or greater than the other
	 * Usage: usort($arrayOfProductModels,array("ProductModel","CompareByProductId"))
	 */
	function CompareByProductId($a, $b) {
		if ($a->GetProductId () > $b->GetProductId ()) {
			return - 1;
		} else {
			return 1;
		}
	}

	//! Used by usort to compare product models by (direct) parent category name
	/*!
	 * @param $a - ProductModel 1
	 * @param $b - ProductModel 2
	 * @return 1 or -1 depending on whether the product ID is less than or greater than the other
	 * Usage: usort($arrayOfProductModels,array("ProductModel","CompareByCategory"))
	 */
	function CompareByCategory($a, $b) {
		$categoriesA = $a->GetCategories ();
		$categoriesB = $b->GetCategories ();

		$categoryA = $categoriesA [0];
		$categoryB = $categoriesB [0];

		if (strcmp ( $categoryA->GetDisplayName (), $categoryB->GetDisplayName () ) > 0) {
			return 1;
		} else {
			return - 1;
		}
	}

	//! Used by usort to compare product models by top level parent category name
	/*!
	 * @param $a - ProductModel 1
	 * @param $b - ProductModel 2
	 * @return 1 or -1 depending on whether the product ID is less than or greater than the other
	 * Usage: usort($arrayOfProductModels,array("ProductModel","CompareByTopCategory"))
	 */
	function CompareByTopCategory($a, $b) {
		$categoriesA = $a->GetCategories ();
		$categoriesB = $b->GetCategories ();

		$categoryA = $categoriesA [0];
		$categoryB = $categoriesB [0];

		$parentCategoryA = $categoryA->GetParentCategory ();
		$parentCategoryB = $categoryB->GetParentCategory ();

		if (is_null ( $parentCategoryB ) || is_null ( $parentCategoryA )) {
			return - 1;
		}

		if (strcmp ( $parentCategoryA->GetDisplayName (), $parentCategoryB->GetDisplayName () ) > 0) {
			// A comes first
			return 1;
		} else {
			// B comes first, or equal
			if (strcmp ( $parentCategoryA->GetDisplayName (), $parentCategoryB->GetDisplayName () ) == 0) {
				// Same top level category, so sort on next level
				if (strcmp ( $categoryA->GetDisplayName (), $categoryB->GetDisplayName () ) > 0) {
					return 1;
				} else {
					if (strcmp ( $categoryA->GetDisplayName (), $categoryB->GetDisplayName () ) == 0) {
						// Same next level, sort on product level
						if (strcmp ( $a->GetDisplayName (), $b->GetDisplayName () ) > 0) {
							return 1;
						} else {
							return - 1;
						}
					}
					if (strcmp ( $categoryA->GetDisplayName (), $categoryB->GetDisplayName () ) < 0) {
						return - 1;
					}
				}
			}
		}
	}

	//! Prints all information about a product in a nice manner. Depends on dev.css
	/*!
	 * @return String : HTML display of the product
	 */
	function PrettyPrintAll() {
		$this->GetActualPrice ();
		$this->GetAttributes ();
		$this->GetDescription ();
		$this->GetDisplayName ();
		$this->GetForSale ();
		$this->GetImages ();
		$this->GetInStock ();
		$this->GetLongDescription ();
		$this->GetManufacturer ();
		$this->GetOnClearance ();
		$this->GetOnSale ();
		$this->GetPostage ();
		$this->GetRelated ();
		$this->GetSimilar ();
		$this->GetSkus ();
		$this->GetUpgrades ();
		$this->GetUpgradePrice ();
		$this->GetWasPrice ();
		$this->GetWeight ();
		$display = '<div id="productModelPrettyPrint">';
		$display .= '<h1>Product ID: ' . $this->mProductId . '</h1>';
		$display .= '<div class="productModelPrettyPrintSubHeading">Display Name:</div> <div class="productModelPrettyPrintValue">' . $this->mDisplayName . '</div>';
		$display .= '<div class="productModelPrettyPrintSubHeading">Description:</div> <div class="productModelPrettyPrintValue">' . $this->mDescription . '</div>';
		$display .= '<div class="productModelPrettyPrintSubHeading">Long Description:</div> <div class="productModelPrettyPrintValue">' . $this->mLongDescription . '</div>';
		$display .= '<div class="productModelPrettyPrintSubHeading">Actual Price:</div> <div class="productModelPrettyPrintValue">&pound;' . $this->mActualPrice . '</div>';
		$display .= '<div class="productModelPrettyPrintSubHeading">Was Price:</div> <div class="productModelPrettyPrintValue">&pound;' . $this->mWasPrice . '</div>';
		$display .= '<div class="productModelPrettyPrintSubHeading">Upgrade Price:</div> <div class="productModelPrettyPrintValue">&pound;' . $this->mUpgradePrice . '</div>';
		$display .= '<div class="productModelPrettyPrintSubHeading">For Sale?</div> <div class="productModelPrettyPrintValue">' . $this->mForSale . '</div>';
		$display .= '<div class="productModelPrettyPrintSubHeading">On Sale?</div> <div class="productModelPrettyPrintValue">' . $this->mOnSale . '</div>';
		$display .= '<div class="productModelPrettyPrintSubHeading">On Clearance?</div> <div class="productModelPrettyPrintValue">' . $this->mOnClearance . '</div>';
		$display .= '<div class="productModelPrettyPrintSubHeading">In Stock?</div> <div class="productModelPrettyPrintValue">' . $this->mInStock . '</div>';
		$display .= '<div class="productModelPrettyPrintSubHeading">Postage:</div> <div class="productModelPrettyPrintValue">&pound;' . $this->mPostage . '</div>';
		$display .= '<div class="productModelPrettyPrintSubHeading">Weight:</div> <div class="productModelPrettyPrintValue">' . $this->mWeight . 'g</div>';
		$display .= '----------------------------------------------------------------------';
		foreach ( $this->mAttributes as $attribute ) {
			$display .= '<div class="productModelPrettyPrintSubHeading">Attribute ID:</div> <div class="productModelPrettyPrintValue">' . $attribute->GetProductAttributeId () . '</div>';
			$display .= '<div class="productModelPrettyPrintSubHeading">Name:</div> <div class="productModelPrettyPrintValue">' . $attribute->GetAttributeName () . '</div>';
			$display .= '<div class="productModelPrettyPrintSubHeading">Type:</div> <div class="productModelPrettyPrintValue">' . $attribute->GetType () . '</div>';
			$display .= '----------------------------------------------------------------------';
		}
		foreach ( $this->mImages as $image ) {
			$display .= '<div class="productModelPrettyPrintSubHeading">Image ID:</div> <div class="productModelPrettyPrintValue">' . $image->GetImageId () . '</div>';
			$display .= '<div class="productModelPrettyPrintSubHeading">Alt Text:</div> <div class="productModelPrettyPrintValue">' . $image->GetAltText () . '</div>';
			$display .= '<div class="productModelPrettyPrintSubHeading">Main Image?</div> <div class="productModelPrettyPrintValue">' . $image->GetMainImage () . '</div>';
			$display .= '----------------------------------------------------------------------';
		}
		foreach ( $this->mRelated as $related ) {
			$display .= '<div class="productModelPrettyPrintSubHeading">Related Product ID:</div> <div class="productModelPrettyPrintValue">' . $related->GetProductId () . '</div>';
			$display .= '<div class="productModelPrettyPrintSubHeading">Display Name:</div> <div class="productModelPrettyPrintValue">' . $related->GetDisplayName () . '</div>';
			$display .= '----------------------------------------------------------------------';
		}
		foreach ( $this->mSimilar as $similar ) {
			$display .= '<div class="productModelPrettyPrintSubHeading">Similar Product ID:</div> <div class="productModelPrettyPrintValue">' . $similar->GetProductId () . '</div>';
			$display .= '<div class="productModelPrettyPrintSubHeading">Display Name:</div> <div class="productModelPrettyPrintValue">' . $similar->GetDisplayName () . '</div>';
			$display .= '----------------------------------------------------------------------';
		}
		foreach ( $this->mUpgrades as $upgrade ) {
			$display .= '<div class="productModelPrettyPrintSubHeading">Upgrade Product ID:</div> <div class="productModelPrettyPrintValue">' . $upgrade->GetProductId () . '</div>';
			$display .= '<div class="productModelPrettyPrintSubHeading">Display Name:</div> <div class="productModelPrettyPrintValue">' . $upgrade->GetDisplayName () . '</div>';
			$display .= '----------------------------------------------------------------------';
		}
		foreach ( $this->mSkus as $sku ) {
			$display .= '<div class="productModelPrettyPrintSubHeading">SKU ID:</div> <div class="productModelPrettyPrintValue">' . $sku->GetSkuId () . '</div>';
			$display .= '<div class="productModelPrettyPrintSubHeading">Sage Code:</div> <div class="productModelPrettyPrintValue">' . $sku->GetSageCode () . '</div>';
			$display .= '----------------------------------------------------------------------';
		}
		$display .= '</div>';
		return $display;
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
	} // End GetSaving

} // End ProductModel
/* DEBUG SECTION
$productId = 2;
$productAttribute1 = new ProductAttributeModel(1);
$productAttribute2 = new ProductAttributeModel(2);
$productAtts = array($productAttribute1,$productAttribute2);
$man = new ManufacturerModel(2);
#var_dump($productAtts);
try {
	$product = new ProductModel($productId);
} catch(Exception $e) {
	echo $e->getMessage();
}
try {
	$product->SetManufacturer($man);
} catch(Exception $e) {
	echo $e->getMessage();
}
#print_r($product->GetImages());*/

?>