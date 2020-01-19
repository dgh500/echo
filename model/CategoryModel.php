<?php

//! Models a single Category (Eg. Regs)
class CategoryModel {
	//! Int : Unique Category ID
	var $mCategoryId;
	//! Obj:CatalogueModel : Catalogue the category belongs to
	var $mCatalogue;
	//! String : Category name (Eg. Regs)
	var $mDisplayName;
	//! String : Category description (Eg. You breathe through them...)
	var $mDescription;
	//! Obj:CategoryModel : The parent category of this category (possibly null)
	var $mParentCategory;
	//! Obj:ImageModel : The image for this category (possibly null)
	var $mImageId;
	//! Boolean : Whether the category is a package category or not
	var $mPackageCategory;

	//! Constructor, initialises the Category ID
	function __construct($categoryId) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$does_this_category_exist_sql = 'SELECT COUNT(Category_ID) AS CategoryCount FROM tblCategory WHERE Category_ID = ' . $categoryId;
		$result = $database->query ( $does_this_category_exist_sql );
		if ($result) {
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			if ($resultObj->CategoryCount != 0) {
				$this->mCategoryId = $categoryId;
			} else {
				$error = new Error ( 'Could not initialise category ' . $categoryId . ' because it does not exist in the database - count ' . $resultObj->CategoryCount . '.' );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		} else {
			$error = new Error ( 'Could not initialise category ' . $categoryId . ' because the query didnt work.' );
			#die(var_dump($database->errorInfo ()));
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

	function __toString() {
		return $this->GetDisplayName ();
	}

	//! Returns the name of the category
	/*!
	* @return String
	*/
	function GetDisplayName() {
		if (! isset ( $this->mDisplayName )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_display_name_sql = 'SELECT Display_Name FROM tblCategory WHERE Category_ID = ' . $this->mCategoryId.' LIMIT 1';
			if (! $result = $database->query ( $get_display_name_sql )) {
				$error = new Error ( 'Could not fetch the display name for category ' . $this->mCategoryId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$display_name = $result->fetch ( PDO::FETCH_OBJ );
			$this->mDisplayName = trim ( $display_name->Display_Name );
		}
		return $this->mDisplayName;
	}

	//! Sets the name of the category
	/*!
	* @param [in] newDisplayName : String
	* @return Bool : true if successful
	*/
	function SetDisplayName($newDisplayName) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_display_name_sql = 'UPDATE tblCategory SET Display_Name = \'' . $newDisplayName . '\' WHERE Category_ID = ' . $this->mCategoryId;
		if (! $database->query ( $set_display_name_sql )) {
			$error = new Error ( 'Could not update the display name for category ' . $this->mCategoryId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDisplayName = $newDisplayName;
		return true;
	}

	//! Returns the description of the category
	/*!
	* @return String
	*/
	function GetDescription() {
		$registry = Registry::getInstance ();
		if (! isset ( $this->mDescription )) {
			mysql_connect ( $registry->host, $registry->username, $registry->password );
			mysql_select_db ( $registry->dbName );

			$get_description_sql = 'SELECT Description FROM tblCategory WHERE Category_ID = ' . $this->mCategoryId.' LIMIT 1';
			if (! $result = mysql_query( $get_description_sql )) {
				$error = new Error ( 'Could not fetch the description for category ' . $this->mCategoryId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = mysql_fetch_object( $result );
			$this->mDescription = trim ( $resultObj->Description );
		}
		return $this->mDescription;
	}

	//! Sets the description of the category
	/*!
	* @param [in] newDescription : String
	* @return Bool : true if successful
	*/
	function SetDescription($newDescription) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_description_sql = 'UPDATE tblCategory SET Description = \'' . $newDescription . '\' WHERE Category_ID = ' . $this->mCategoryId;
		if (! $database->query( $set_description_sql )) {
			$error = new Error ( 'Could not update the description for category ' . $this->mCategoryId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDescription = $newDescription;
		return true;
	}

	//! Returns the unique Category ID (Set in the constructor)
	/*!
	* @return Int
	*/
	function GetCategoryId() {
		return $this->mCategoryId;
	}

	//! Returns the Image associated with this category
	/*!
	* @return Obj:ImageModel : The image associated with the category
	*/
	function GetImage() {
		if (! isset ( $this->mImage )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_image_sql = 'SELECT Image_ID FROM tblCategory WHERE Category_ID = ' . $this->mCategoryId.' LIMIT 1';
			if (! $result = $database->query ( $get_image_sql )) {
				$error = new Error ( 'Could not fetch the image ID for category ' . $this->mCategoryId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$image_id = $result->fetch ( PDO::FETCH_OBJ );
			$newImage = new ImageModel ( $image_id->Image_ID );
			$this->mImage = $newImage;
		}
		return $this->mImage;
	}

	//! Sets the image of the category
	/*!
	* @param [in] newImage Obj:ImageModel : The new image
	* @return Bool : true if successful
	*/
	function SetImage($newImage) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_image_sql = 'UPDATE tblCategory SET Image_ID = \'' . $newImage->GetImageId () . '\' WHERE Category_ID = ' . $this->mCategoryId;
		if (! $database->query ( $set_image_sql )) {
			$error = new Error ( 'Could not update the category image for category ' . $this->mCategoryId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mImage = $newImage;
		return true;
	}

	//! Returns the catalogue associated with this category
	/*!
	* @return Obj:CatalogueModel : The catalogue associated with the category
	*/
	function GetCatalogue() {
		if (! isset ( $this->mCatalogue )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_catalogue_sql = 'SELECT Catalogue_ID FROM tblCategory WHERE Category_ID = ' . $this->mCategoryId.' LIMIT 1';
			if (! $result = $database->query ( $get_catalogue_sql )) {
				$error = new Error ( 'Could not fetch the catalogue ID for category ' . $this->mCategoryId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$catalogue_id = $result->fetch ( PDO::FETCH_OBJ );
			$newCatalogue = new CatalogueModel ( $catalogue_id->Catalogue_ID );
			$this->mCatalogue = $newCatalogue;
		}
		return $this->mCatalogue;
	}

	//! Sets the catalogue of the category
	/*!
	* @param [in] newCatalogue Obj:CatalogueModel : The new catalogue
	* @return Bool : true if successful
	*/
	function SetCatalogue($newCatalogue) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_catalogue_sql = 'UPDATE tblCategory SET Catalogue_ID = \'' . $newCatalogue->GetCatalogueId () . '\' WHERE Category_ID = ' . $this->mCategoryId;
		if (! $database->query ( $set_catalogue_sql )) {
			$error = new Error ( 'Could not update the catalogue for category ' . $this->mCategoryId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mCatalogue = $newCatalogue;
		return true;
	}

	//! Sets the best selling product in this category
	function SetBestSellingProduct($product) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$sql = 'UPDATE tblCategory SET BS_Product_ID = \'' . $product->GetProductId() . '\' WHERE Category_ID = ' . $this->mCategoryId;
		if (! $database->query ( $sql )) {
			$error = new Error ( 'Could not update the best selling product for category ' . $this->mCategoryId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mBsProduct = $product;
		return true;
	} // End SetBestSellingProduct

	//! Returns a ProductModel - the best selling product in this category
	function GetBestSellingProduct() {
		if (! isset ( $this->mBsProduct )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$sql = 'SELECT BS_Product_ID FROM tblCategory WHERE Category_ID = ' . $this->mCategoryId.' LIMIT 1';
			if (! $result = $database->query ( $sql )) {
				$error = new Error ( 'Could not fetch the best selling product ID for category ' . $this->mCategoryId );
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
		if (! isset ( $this->mBsProduct )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$sql = '
					SELECT DISTINCT Product_ID FROM tblCategory_Products
					WHERE
						Category_ID = '.$this->mCategoryId.'
					OR
						Category_ID IN (SELECT Category_ID FROM tblCategory WHERE Parent_Category_ID = '.$this->mCategoryId.')
					ORDER BY Product_ID ASC LIMIT 1';
			if (! $result = $database->query ( $sql )) {
				$error = new Error ( 'Could not fetch any product ID for category ' . $this->mCategoryId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$newProduct = new ProductModel ( $resultObj->Product_ID );
		}
		return $newProduct;
	} // End GetAnyProduct

	//! Sets whether the category contains packages or not
	/*!
	* @param [in] Str(1) - 0 or 1 : Bool whether the category is a package category
	* @return Bool : true if successful
	*/
	function SetPackageCategory($newPackageCategory) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$set_package_sql = 'UPDATE tblCategory SET Package_Category = \'' . $newPackageCategory . '\' WHERE Category_ID = ' . $this->mCategoryId;
		if (! $database->query ( $set_package_sql )) {
			$error = new Error ( 'Could not update the package for category ' . $this->mCategoryId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mPackageCategory = $newPackageCategory;
		return true;
	}

	//! Returns whether the category is a category for packages
	/*!
	* @return String(1) - Either 0 or 1 (False or True)
	*/
	function GetPackageCategory() {
		if (! isset ( $this->mPackageCategory )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_package_category_sql = 'SELECT Package_Category FROM tblCategory WHERE Category_ID = ' . $this->mCategoryId.' LIMIT 1';
			if (! $result = $database->query ( $get_package_category_sql )) {
				$error = new Error ( 'Could not fetch the package category information for category ' . $this->mCategoryId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$package_category = $result->fetch ( PDO::FETCH_OBJ );
			$this->mPackageCategory = $package_category->Package_Category;
		}
		return $this->mPackageCategory;
	}

	//! Returns the parent category (if any) associated with this category
	/*!
	* @return Obj:CategoryModel or NULL : The parent category associated with the category
	*/
	function GetParentCategory() {
		if (! isset ( $this->mParentCategory )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$get_parent_category_sql = 'SELECT Parent_Category_ID FROM tblCategory WHERE Category_ID = ' . $this->mCategoryId.' LIMIT 1';
			if (! $result = $database->query ( $get_parent_category_sql )) {
				$error = new Error ( 'Could not fetch the parent category ID for category ' . $this->mCategoryId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$parent_category_id = $result->fetch ( PDO::FETCH_OBJ );
			if (is_null ( $parent_category_id->Parent_Category_ID )) {
				$this->mParentCategory = NULL;
			} else {
				$newParentCategory = new CategoryModel ( $parent_category_id->Parent_Category_ID );
				$this->mParentCategory = $newParentCategory;
			}
		}
		return $this->mParentCategory;
	}

	//! Sets the parent category of the category
	/*!
	* @param [in] newParentCategory Obj:CategoryModel : The new parent category
	* @return Bool : true if successful
	*/
	function SetParentCategory($newParentCategory) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		// Check we're not trying to make something its own parent - no cycles allowed!
		if ($newParentCategory->GetCategoryId () == $this->GetCategoryId ()) {
			$error = new Error ( 'Can\'t make something its own parent!' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}

		// Set the parent-child relationship up
		$set_parent_category_sql = 'UPDATE tblCategory SET Parent_Category_ID = \'' . $newParentCategory->GetCategoryId () . '\' WHERE Category_ID = ' . $this->mCategoryId;
		if (! $database->query ( $set_parent_category_sql )) {
			$error = new Error ( 'Could not update the parent category for category ' . $this->mCategoryId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mParentCategory = $newParentCategory;
		return true;
	}

	//! Returns the category name if a top-level, or a TOPLEVEL > CURRENT string otherwise
	/*!
	 * @return String
	 */
	function GetDirectoryPath() {
		if($this->GetParentCategory()) {
			return $this->GetParentCategory()->GetDisplayName().' > '.$this->GetDisplayName();
		} else {
			return $this->GetDisplayName();
		}
	} // End GetDirectoryPath

	//! Tells you whether this category contains $item
	/*!
	 * @param [in] item Obj:ProductModel or PackageModel : The item to check
	 * @return Bool : true if the product is in this category
	 */
	function Contains($item) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		if (method_exists ( $item, 'GetProductId' )) {
			$sql = 'SELECT COUNT(Product_ID) Product_ID FROM tblCategory_Products WHERE Category_ID = ' . $this->mCategoryId . ' AND Product_ID = ' . $item->GetProductId () . '';
		} else {
			$sql = 'SELECT COUNT(Package_ID) Package_ID FROM tblCategory_Packages WHERE Category_ID = ' . $this->mCategoryId . ' AND Package_ID = ' . $item->GetPackageId () . '';
		}
		if (! $result = $database->query ( $sql )) {
			$error = new Error ( 'Could not check category ' . $this->mCategoryId . ' against item.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}

		if (0 == $result->fetchColumn ()) {
			return false;
		} else {
			return true;
		}
	} // End Contains

	//! Returns an Object with 2 properties - SalesCount and ProductId, used by bestSeller.php to keep updated the best selling products in categories
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
INNER JOIN tblCategory_Products ON tblCategory_Products.Product_ID = tblProduct.Product_ID
LEFT JOIN tblBasket_Skus ON tblProduct_SKUs.SKU_ID = tblBasket_Skus.SKU_ID
WHERE tblProduct.Hidden = '0'
AND tblCategory_Products.Category_ID = $this->mCategoryId
GROUP BY tblProduct.Product_ID, tblProduct_Text.Display_Name
ORDER BY ProductCount DESC
LIMIT 1
				";
		// Product Result
	#	echo $sql; die();
		if (! $result = $database->query ( $sql )) {
			return false;	// Let bestSeller.php deal with this - occurs when no sales in a category
		}
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		return $resultObj;
	} // End CalculateBestSellingProduct

} // End CategoryModel

?>