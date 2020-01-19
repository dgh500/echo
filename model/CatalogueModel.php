<?php

//! Models a single Catalogue (Eg. Dive)
class CatalogueModel {
	//! Int : Unique Catalogue ID
	var $mCatalogueId;
	//! String : The catalogue name (Eg. Dive/Shooting etc.)
	var $mDisplayName;
	//! Obj:ImageModel : The image for this catalogue (Logo)
	var $mImage;
	//! Boolean : Whether this catalogue has packages turned on or off
	var $mPackages;
	//! Link to the homepage of this catalogue
	var $mUrl;
	//! Array of Obj:ManufacturerModel - All the manufacturers that are associated with this catalogue
	var $mManufacturers;
	//! Int - Pricing Model To Use
	var $mPricingModel;
	//! String : Long description of the catalogue, used on the front page
	var $mLongDescription;
	//! String : The public (info@...) email address of the catalogue
	var $mEmail;
	//! Array of TagModel objects
	var $mTags;

	//! Constructor, initialises the Catalogue ID
	function __construct($catalogueId) {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
		$sql = 'SELECT COUNT(Catalogue_ID) FROM tblCatalogue WHERE Catalogue_ID = ' . $catalogueId;
		if ($result = $this->mDatabase->query ( $sql )) {
			if ($result->fetchColumn () > 0) {
				$this->mCatalogueId = $catalogueId;
			} else {
				$error = new Error ( 'Could not initialise catalogue ' . $catalogueId . ' because it does not exist in the database 1.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		} else {
			$error = new Error ( 'Could not initialise catalogue ' . $catalogueId . ' because it does not exist in the database 2.' . $sql );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	#	echo('test - catalogue model');
	}

	//! Returns the name of the catalog
	/*!
	* @return String
	*/
	function GetDisplayName() {
		if (! isset ( $this->mDisplayName )) {
			$get_display_name_sql = 'SELECT Display_Name FROM tblCatalogue WHERE Catalogue_ID = ' . $this->mCatalogueId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $get_display_name_sql )) {
				$error = new Error ( 'Could not fetch the display name for catalogue ' . $this->mCatalogueId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$display_name = $result->fetch ( PDO::FETCH_OBJ );
			$this->mDisplayName = $display_name->Display_Name;
		}
		return $this->mDisplayName;
	}

	//! Returns the long decsription for the catalogue
	/*!
	* @return String
	*/
	function GetLongDescription() {
		$registry = Registry::getInstance ();
		if (! isset ( $this->mLongDescription )) {
			mysql_connect ( $registry->host, $registry->username, $registry->password );
			mysql_select_db ( $registry->dbName );
			$sql = 'SELECT Long_Description FROM tblCatalogue_Text WHERE Catalogue_ID = ' . $this->mCatalogueId.' LIMIT 1';
			if (! $result = mysql_query ( $sql )) {
				$error = new Error ( 'Could not fetch the long description for catalogue ' . $this->mCatalogueId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = mysql_fetch_object( $result );
			$this->mLongDescription = $resultObj->Long_Description;
		}
		return $this->mLongDescription;
	}

	//! Sets the long description
	/*!
	* @param [in] newLongDescription : String
	* @return Bool : true if successful
	*/
	function SetLongDescription($newLongDescription) {
		$sql = 'UPDATE tblCatalogue_Text SET Long_Description = \'' . mysql_escape_string($newLongDescription) . '\' WHERE Catalogue_ID = ' . $this->mCatalogueId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the long description for catalogue ' . $this->mCatalogueId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
		#	die(var_dump($this->mDatabase->errorInfo()));
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mLongDescription = $newLongDescription;
		return true;
	}

	//! Returns the index title for the catalogue
	/*!
	* @return String
	*/
	function GetIndexTitle() {
		if (! isset ( $this->mIndexTitle )) {
			$sql = 'SELECT Index_Title FROM tblCatalogue_Text WHERE Catalogue_ID = ' . $this->mCatalogueId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the index title for catalogue ' . $this->mCatalogueId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mIndexTitle = $resultObj->Index_Title;
		}
		return $this->mIndexTitle;
	}

	//! Sets the index title (for the <title> on the home page)
	/*!
	* @param [in] newIndexTitle : String
	* @return Bool : true if successful
	*/
	function SetIndexTitle($newIndexTitle) {
		$sql = 'UPDATE tblCatalogue_Text SET Index_Title = \'' . $newIndexTitle . '\' WHERE Catalogue_ID = ' . $this->mCatalogueId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the index title for catalogue ' . $this->mCatalogueId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mIndexTitle = $newIndexTitle;
		return true;
	}

	//! Returns the URL of the catalog
	/*!
	* @return String
	*/
	function GetUrl() {
		if (! isset ( $this->mUrl )) {
			$get_url_sql = 'SELECT URL FROM tblCatalogue WHERE Catalogue_ID = ' . $this->mCatalogueId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $get_url_sql )) {
				$error = new Error ( 'Could not fetch the URL for catalogue ' . $this->mCatalogueId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$url = $result->fetch ( PDO::FETCH_OBJ );
			$this->mUrl = $url->URL;
		}
		return $this->mUrl;
	}

	//! Sets the name of the catalog
	/*!
	* @param [in] newDisplayName : String
	* @return Bool : true if successful
	*/
	function SetDisplayName($newDisplayName) {
		$set_display_name_sql = 'UPDATE tblCatalogue SET Display_Name = \'' . $newDisplayName . '\' WHERE Catalogue_ID = ' . $this->mCatalogueId;
		if (! $this->mDatabase->query ( $set_display_name_sql )) {
			$error = new Error ( 'Could not update the display name for catalogue ' . $this->mCatalogueId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDisplayName = $newDisplayName;
		return true;
	}

	//! Returns the image instance associated with this catalogue
	/*!
	* @return Obj:ImageModel
	*/
	function GetImage() {
		if (! isset ( $this->mImage )) {
			$get_image_sql = 'SELECT Image_ID FROM tblCatalogue WHERE Catalogue_ID = ' . $this->mCatalogueId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $get_image_sql )) {
				$error = new Error ( 'Could not fetch the image for catalogue ' . $this->mCatalogueId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$image = $result->fetch ( PDO::FETCH_OBJ );
			if (NULL === $image->Image_ID) {
				return NULL;
			} else {
				$image_id = $image->Image_ID;
				$this->mImage = new ImageModel ( $image_id );
			}
		}
		return $this->mImage;
	}

	//! Returns the name of the catalog
	/*!
	* @param [in] newImage : Obj:ImageModel
	* @return Bool : true if successful
	*/
	function SetImage($newImage) {
		$set_image_sql = 'UPDATE tblCatalogue SET Image_ID = \'' . $newImage->GetImageId () . '\' WHERE Catalogue_ID = ' . $this->mCatalogueId;
		if (! $this->mDatabase->query ( $set_image_sql )) {
			$error = new Error ( 'Could not update the image for catalogue ' . $this->mCatalogueId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mImage = $newImage;
		return true;
	}

	/*! Returns whether the catalogue allows packages /todo{THIS SHOULD BE IN SYSTEM SETTINGS}
	/!
	* @return String(1) - Either 0 or 1 (False or True)
	/
	function GetPackages() {
		if (! isset ( $this->mPackages )) {
			$get_package_sql = 'SELECT TOP 1 Packages FROM tblCatalogue WHERE Catalogue_ID = ' . $this->mCatalogueId;
			if (! $result = $this->mDatabase->query ( $get_package_sql )) {
				$error = new Error ( 'Could not fetch the packages information for catalogue ' . $this->mCatalogueId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$packages = $result->fetch ( PDO::FETCH_OBJ );
			$this->mPackages = $packages->Packages;
		}
		return $this->mPackages;
	}
	//! Sets the packages option of the catalogue
	/*!
	* @param [in] mPackages String(1) : Either 0 or 1
	* @return Bool : true if successful
	*
	function SetPackages($newPackages) {
		$set_packages_sql = 'UPDATE tblCatalogue SET Packages = \'' . $newPackages . '\' WHERE Catalogue_ID = ' . $this->mCatalogueId;
		if (! $this->mDatabase->query ( $set_packages_sql )) {
			$error = new Error ( 'Could not update the packages option for catalogue ' . $this->mCatalogueId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mPackages = $newPackages;
		return true;
	}

	*/

	function HasActivePackages() {
		$sql = 'SELECT COUNT(*) AS PackageCount FROM tblCategory_Packages
					INNER JOIN tblCategory ON tblCategory_Packages.Category_ID = tblCategory.Category_ID
					WHERE tblCategory.Catalogue_ID = ' . $this->mCatalogueId;
		$result = $this->mDatabase->query ( $sql );
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		if ($resultObj->PackageCount == 0) {
			return false;
		} else {
			return true;
		}
	}

	function HasDealOfTheWeekPackages() {
		$sql = 'SELECT COUNT(*) AS PackageCount FROM tblCategory_Packages
					INNER JOIN tblCategory ON tblCategory_Packages.Category_ID = tblCategory.Category_ID
					INNER JOIN tblPackage ON tblCategory_Packages.Package_ID = tblPackage.Package_ID
					WHERE tblCategory.Catalogue_ID = ' . $this->mCatalogueId.'
					AND tblPackage.Offer_Of_Week = \'1\'
					';
		$result = $this->mDatabase->query ( $sql );
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		if ($resultObj->PackageCount == 0) {
			return false;
		} else {
			return true;
		}
	}

	//! Gets the unique packages category for this catalogue
	/*!
	 * @return Obj:CategoryModel
	 */
	function GetPackagesCategory() {
		if (! isset ( $this->mPackagesCategory )) {
			$sql = 'SELECT Packages_Category FROM tblCatalogue WHERE Catalogue_ID = ' . $this->mCatalogueId;
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the packages category for catalogue ' . $this->mCatalogueId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$packagesCat = $result->fetch ( PDO::FETCH_OBJ );
			$this->mPackagesCategory = new CategoryModel ( $packagesCat->Packages_Category );
		}
		return $this->mPackagesCategory;
	}

	//! Sets the unique packages category for this catalogue
	/*!
	 * @param Obj:CategoryModel
	 * @return Obj:CategoryModel
	 */
	function SetPackagesCategory($category) {
		$sql = 'UPDATE tblCatalogue SET Packages_Category = \'' . $category->GetCategoryId () . '\' WHERE Catalogue_ID = ' . $this->mCatalogueId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the packages category for catalogue ' . $this->mCatalogueId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mPackagesCategory = $category;
		return true;
	}

	//! Returns the pricing model used by the catalogue
	/*!
	* @return Obj:PricingModel
	*/
	function GetPricingModel() {
		if (! isset ( $this->mPricingModel )) {
			$sql = 'SELECT Pricing_Model_ID FROM tblCatalogue WHERE Catalogue_ID = ' . $this->mCatalogueId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the pricing model information for catalogue ' . $this->mCatalogueId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$pricingModelId = $resultObj->Pricing_Model_ID;
			$this->mPricingModel = new PricingModel ( $pricingModelId );
		}
		return $this->mPricingModel;
	}

	//! Returns all manufacturers linked to this catalogue
	/*!
	* @return Array of ManufacturerModel objects, empty if none
	*/
	function GetManufacturers() {
		if (! isset ( $this->mManufacturers )) {
			$get_manufacturers_sql = 'SELECT Manufacturer_ID FROM tblManufacturer WHERE Catalogue_ID = ' . $this->mCatalogueId . ' ORDER BY Display_Name ASC';
			if (! $result = $this->mDatabase->query ( $get_manufacturers_sql )) {
				$error = new Error ( 'Could not fetch the manufacturers for catalogue ' . $this->mCatalogueId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$manufacturers = $result->fetchAll ( PDO::FETCH_OBJ );
			// For each manufacturer, create a new instance of it and store it in the mManufacturers member variable
			foreach ( $manufacturers as $manufacturer ) {
				$newManufacturer = new ManufacturerModel ( $manufacturer->Manufacturer_ID );
				$this->mManufacturers [] = $newManufacturer;
			}
			if (0 == count ( $manufacturers )) {
				$this->mManufacturers = array ();
			}
		}
		return $this->mManufacturers;
	}

	//! Returns the email of the catalog
	/*!
	* @return String
	*/
	function GetEmail() {
		if (! isset ( $this->mEmail )) {
			$sql = 'SELECT Email FROM tblCatalogue WHERE Catalogue_ID = ' . $this->mCatalogueId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the email address for catalogue ' . $this->mCatalogueId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mEmail = $resultObj->Email;
		}
		return $this->mEmail;
	}

	//! Returns associated tags
	/*!
	* @param $onlyActive - Boolean - Whether to get only those tags to which products are assigned (IE. Empty tags)
	* @return Array of TagModel objects, empty if none
	*/
	function GetTags($onlyActive=false) {
		if(!isset($this->mTags)) {
			// Just consider active tags?
			if($onlyActive) {
				$onlyActiveSql = 'AND tblCatalogue_Tags.Tag_ID IN
								(SELECT Tag_ID FROM tblProduct_Tags INNER JOIN tblProduct ON tblProduct.Product_ID = tblProduct_Tags.Product_ID)';
			} else { $onlyActiveSql = ''; }
			// Full SQL including above clause
			$sql = 'SELECT tblCatalogue_Tags.Tag_ID
						FROM tblCatalogue_Tags
							INNER JOIN tblCatalogue
								ON tblCatalogue_Tags.Catalogue_ID = tblCatalogue.Catalogue_ID
							INNER JOIN tblTag
								ON tblCatalogue_Tags.Tag_ID = tblTag.Tag_ID
						WHERE tblCatalogue_Tags.Catalogue_ID = '.$this->mCatalogueId.'
						'.$onlyActiveSql.'
						ORDER BY tblTag.Display_Name ASC';
			if(!$result = $this->mDatabase->query($sql)) {
				$error = new Error('Could not fetch the tags for catalogue '.$this->mCatalogueId);
				$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
				throw new Exception($error->GetErrorMsg());
			}
			$catalogue_tags = $result->fetchAll(PDO::FETCH_OBJ);
			// For each tag, create a new instance of it and store it in the mTags member variable
			foreach($catalogue_tags as $value) {
				$newTag = new TagModel($value->Tag_ID);
				$this->mTags[] = $newTag;
			}
			if (0 == count ( $catalogue_tags )) {
				$this->mTags = array ();
			}
		}
		return $this->mTags;
	} // End GetTags()

	//! Returns an Object with 2 properties - SalesCount and ProductId, used by bestSeller.php to keep updated the best selling products in catalogues
	//! NB - Use GetBestSellingProduct for the public pages :)
	function CalculateBestSellingProduct() {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		// Best selling RECENTLY!
		$endTime = time();
		$startTime = strtotime("-2 month");
		// Select: Product_ID, SalesCount
		// Conditions: Order is shipped, Product in Order, Product in Category
		$sql = "
SELECT DISTINCT tblProduct.Product_ID, COUNT( tblProduct.Product_ID ) AS ProductCount, tblProduct_Text.Display_Name
FROM tblProduct
INNER JOIN tblProduct_SKUs ON tblProduct_SKUs.Product_ID = tblProduct.Product_ID
INNER JOIN tblProduct_Text ON tblProduct.Product_ID = tblProduct_Text.Product_ID
LEFT JOIN tblBasket_Skus ON tblProduct_SKUs.SKU_ID = tblBasket_Skus.SKU_ID
WHERE tblProduct.Hidden = '0'
GROUP BY tblProduct.Product_ID, tblProduct_Text.Display_Name
ORDER BY ProductCount DESC
LIMIT 1
				";
		// Product Result
	#	echo $sql; die();
		if (! $result = $database->query ( $sql )) {
			return false;	// Let bestSeller.php deal with this - occurs when no sales in a catalogue
		}
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		return $resultObj;
	} // End CalculateBestSellingProduct

	//! Sets the best selling product in this catalogue
	function SetBestSellingProduct($product) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$sql = 'UPDATE tblCatalogue SET BS_Product_ID = \'' . $product->GetProductId() . '\' WHERE Catalogue_ID = ' . $this->mCatalogueId;
		if (! $database->query ( $sql )) {
			$error = new Error ( 'Could not update the best selling product for catalogue ' . $this->mCatalogueId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mBsProduct = $product;
		return true;
	} // End SetBestSellingProduct

	//! Returns a ProductModel - the best selling product in this catalogue
	function GetBestSellingProduct() {
		if (! isset ( $this->mBsProduct )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$sql = 'SELECT BS_Product_ID FROM tblCatalogue WHERE Catalogue_ID = ' . $this->mCatalogueId.' LIMIT 1';
			if (! $result = $database->query ( $sql )) {
				$error = new Error ( 'Could not fetch the best selling product ID for catalogue ' . $this->mCatalogueId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			if($resultObj->BS_Product_ID == 0) {
				return $this->GetAnyProduct();
			} else {
				try {
					$newProduct = new ProductModel ( $resultObj->BS_Product_ID );
				} catch(Exception $e) {
					return $this->GetAnyProduct();
				}
			}
			$this->mBsProduct = $newProduct;
		}
		return $this->mBsProduct;
	} // End GetBestSellingProduct

	//! Returns a ProductModel - any product in a subcategory from this parent category
	function GetAnyProduct() {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$sql = '
				SELECT DISTINCT Product_ID FROM tblProduct LIMIT 1';
		if (! $result = $database->query ( $sql )) {
			$error = new Error ( 'Could not fetch any product ID for catalogue ' . $this->mCatalogueId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		$newProduct = new ProductModel ( $resultObj->Product_ID );
		return $newProduct;
	} // End GetAnyProduct


	//! Returns the unique Catalogue ID (Set in the constructor)
	/*!
	* @return Int
	*/
	function GetCatalogueId() {
		return $this->mCatalogueId;
	}

}

?>