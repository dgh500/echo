<?php

//! Deals with Product tasks (create, delete etc)
class ProductController {

	function __construct() {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
	}

	//! Creates a new product in the database then returns this product as an object of type ProductModel
	/*!
	 * @return Obj:ProductModel - the new product
	 ***Explanation***
	 * A product MUST have a SKU associated with it when it is created, similarly a record must exist in the Product_Text table, because
	 * ProductModel::SetDisplayName and ProductModel::SetDescription rely on there being such a record. A new SKU is created for each new product
	 * and it is linked using ProductController::LinkProductToSku
	 */
	function CreateProduct() {
		$skuController 			= new SkuController ( );
		$taxCodeController 		= new TaxCodeController ( );
		$dispatchDateController = new DispatchDateController ( );
		$defaultTaxCode 		= $taxCodeController->GetDefaultTaxCode ();
		$defaultDispatchDate 	= $dispatchDateController->GetDefaultDispatchDate ();
		$sql = 'INSERT INTO tblProduct (`In_Stock`,`On_Sale`,`For_Sale`,`On_Clearance`,`Actual_Price`,`Offer_Of_Week`,`Tax_Code_ID`,`Dispatch_Date_ID`,`Was_Price`,`Upgrade_Price`,`Postage`,`Weight`) VALUES (\'1\',\'0\',\'1\',\'0\',\'0\',\'0\',\'' . $defaultTaxCode->GetTaxCodeId () . '\',\'' . $defaultDispatchDate->GetDispatchDateId () . '\',\'0\',\'0\',\'0\',\'0\')';
		if ($this->mDatabase->query ( $sql )) {
			$sql = 'SELECT Product_ID FROM tblProduct ORDER BY Product_ID DESC LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not select new product' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$latest_product = $result->fetch ( PDO::FETCH_OBJ );
			$newProduct = new ProductModel ( $latest_product->Product_ID );
			$sql = 'INSERT INTO tblProduct_Text (`Product_ID`,`Display_Name`) VALUES (\'' . $latest_product->Product_ID . '\',\'\')';
			if (! $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not insert product text' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$newSku = $skuController->CreateSku ();
			if ($this->CreateSkuLink ( $newSku, $newProduct )) {
				return $newProduct;
			} else {
				$error = new Error ( 'Could not link Product and SKU.' );
				throw new Exception ( $error->GetErrorMsg () );
			}
		} else {
			$error = new Error ( 'Could not insert product' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

	//! Links a product to a SKU
	/*!
	 * @param [in] sku : Obj:SkuModel
	 * @param [in] product : Obj:ProductModel
	 * @return Bool true if successful
	 */
	function CreateSkuLink($sku, $product) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		if (! in_array ( $sku, $product->GetSkus () )) {
			$link_sku_to_product_sql = 'INSERT INTO tblProduct_SKUs (`Product_ID`,`SKU_ID`) VALUES (\'' . $product->GetProductId () . '\',\'' . $sku->GetSkuId () . '\')';
			if (! $result = $database->query ( $link_sku_to_product_sql )) {
				$error = new Error ( 'Could not link Product ' . $product->GetProductId () . ' to SKU' . $sku->GetSkuId () );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return true;
	}

	//! Attempts to delete a product from the database, throws an exception if this fails
	/*!
	 * @return true if successful
	 * @param [in] product : Obj:ProductModel - the product  to delete
	*/
	function DeleteProduct($product) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$skuController = new SkuController ( );
		$imageController = new ImageController ( );
		$productAttributeController = new ProductAttributeController ( );
		//! Remove product text
		$delete_product_sql [] = 'DELETE FROM tblProduct_Text WHERE Product_ID = ' . $product->GetProductId ();

		//! Remove all of the 'dangling' Attributes and SKUs
		foreach ( $product->GetAttributes () as $prodAtt ) {
			$productAttributeController->DeleteProductAttribute ( $prodAtt );
		}
		foreach ( $product->GetSkus () as $sku ) {
			$skuController->DeleteSku ( $sku );
		}

		//! Remove all links
		$delete_product_sql [] = 'DELETE FROM tblPackage_Products WHERE Product_ID = ' . $product->GetProductId ();
		$delete_product_sql [] = 'DELETE FROM tblCategory_Products WHERE Product_ID = ' . $product->GetProductId ();
		#$delete_product_sql[] = 'DELETE FROM tblProduct_Images WHERE Product_ID = '.$product->GetProductId();
		$delete_product_sql [] = 'DELETE FROM tblProduct_Attributes WHERE Product_ID = ' . $product->GetProductId ();
		#$delete_product_sql[] = 'DELETE FROM tblProduct_Upgrade WHERE Product_ID = '.$product->GetProductId();
		#$delete_product_sql[] = 'DELETE FROM tblProduct_Similar WHERE Product_ID = '.$product->GetProductId();
		#$delete_product_sql[] = 'DELETE FROM tblProduct_Related WHERE Product_ID = '.$product->GetProductId();
		#$delete_product_sql[] = 'DELETE FROM tblProduct_Similar WHERE Similar_Product_ID = '.$product->GetProductId();
		#$delete_product_sql[] = 'DELETE FROM tblProduct_Related WHERE Related_Product_ID = '.$product->GetProductId();
		#$delete_product_sql[] = 'DELETE FROM tblProduct_Skus WHERE Product_ID = '.$product->GetProductId();


		//! Remove Links
		foreach ( $product->GetSimilar () as $similar ) {
			$this->RemoveSimilarLink ( $product, $similar );
			$this->RemoveSimilarLink ( $similar, $product );
		}
		foreach ( $product->GetRelated () as $related ) {
			$this->RemoveRelatedLink ( $product, $related );
			$this->RemoveRelatedLink ( $related, $product );
		}
		foreach ( $product->GetUpgrades () as $upgrade ) {
			$this->RemoveUpgradeLink ( $product, $upgrade );
			$this->RemoveUpgradeLink ( $upgrade, $product );
		}
		foreach ( $product->GetImages () as $image ) {
			$this->RemoveImageLink ( $product, $image );
		}

		//! Finally, remove the actual product - this has to be last because everything else requires the product to exist
		$delete_product_sql [] = 'DELETE FROM tblProduct WHERE Product_ID = ' . $product->GetProductId ();
		foreach ( $delete_product_sql as $sql ) {
			//! This is like this because PDO::Exec returns the number of ROWS affected - if this is zero it would equate to FALSE if normal comparison (==) was used incorrectly
			if (FALSE === $database->query ( $sql )) {
				$error = new Error ( 'Problem deleting product ' . $product->GetProductId () . ' with SQL:<br /> ' . $sql );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->getErrorMsg () );
			}
		}
		return true;
	}

	//! Attempts to create a link between a product and a similar product, throws an exception if this fails
	/*!
	 * @return true if successful
	 * @param [in] product : Obj:ProductModel
	 * @param [in] similarProduct : Obj:ProductModel
	*/
	function CreateSimilarLink($product, $similarProduct) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		//! Checks not already a link here. Not throwing an exception here makes this non-destructive; adding more links between 2 products has no effect.
		if (! in_array ( $similarProduct, $product->GetSimilar () )) {
			$sql = 'INSERT INTO tblProduct_Similar (`Product_ID`,`Similar_Product_ID`) VALUES (' . $product->GetProductId () . ',' . $similarProduct->GetProductId () . ')';
			if (FALSE === $database->query ( $sql )) {
				$error = new Error ( 'Problem creating link between product ' . $product->GetProductId () . ' and similar product ' . $similarProduct->GetProductId () . ' with SQL:<br /> ' . $sql );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->getErrorMsg () );
			}
		}
		return true;
	}

	//! Attempts to remove a link between a product and a similar product, throws an exception if this fails
	/*!
	 * @return true if successful
	 * @param [in] product : Obj:ProductModel
	 * @param [in] similarProduct : Obj:ProductModel
	*/
	function RemoveSimilarLink($product, $similarProduct) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$delete_product_similar_sql = 'DELETE FROM tblProduct_Similar WHERE Similar_Product_ID = ' . $similarProduct->GetProductId () . ' AND Product_ID = ' . $product->GetProductId ();
		if (FALSE === $database->query ( $delete_product_similar_sql )) {
			$error = new Error ( 'Problem removing link between product ' . $product->GetProductId () . ' and similar product ' . $similarProduct->GetProductId () . ' with SQL:<br /> ' . $delete_product_similar_sql );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->getErrorMsg () );
		}
		return true;
	}

	//! Attempts to create a link between a product and a related product, throws an exception if this fails
	/*!
	 * @return true if successful
	 * @param [in] product : Obj:ProductModel
	 * @param [in] relatedProduct : Obj:ProductModel
	*/
	function CreateRelatedLink($product, $relatedProduct) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		//! Checks not already a link here. Not throwing an exception here makes this non-destructive; adding more links between 2 products has no effect.
		if (! in_array ( $relatedProduct, $product->GetRelated () )) {
			$create_product_related_sql = 'INSERT INTO tblProduct_Related (`Product_ID`,`Related_Product_ID`) VALUES (' . $product->GetProductId () . ',' . $relatedProduct->GetProductId () . ')';
			if (FALSE === $database->query ( $create_product_related_sql )) {
				$error = new Error ( 'Problem creating link between product ' . $product->GetProductId () . ' and related product ' . $relatedProduct->GetProductId () . ' with SQL:<br /> ' . $create_product_related_sql );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->getErrorMsg () );
			}
		}
		return true;
	}

	//! Attempts to remove a link between a product and a related product, throws an exception if this fails
	/*!
	 * @return true if successful
	 * @param [in] product : Obj:ProductModel
	 * @param [in] relatedProduct : Obj:ProductModel
	*/
	function RemoveRelatedLink($product, $relatedProduct) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$delete_product_related_sql = 'DELETE FROM tblProduct_Related WHERE Related_Product_ID = ' . $relatedProduct->GetProductId () . ' AND Product_ID = ' . $product->GetProductId ();
		if (FALSE === $database->query ( $delete_product_related_sql )) {
			$error = new Error ( 'Problem removing link between product ' . $product->GetProductId () . ' and related product ' . $relatedProduct->GetProductId () . ' with SQL:<br /> ' . $delete_product_similar_sql );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->getErrorMsg () );
		}
		return true;
	}

	//! Attempts to create a link between a product and a upgrade product, throws an exception if this fails
	/*!
	 * @return true if successful
	 * @param [in] product : Obj:ProductModel
	 * @param [in] upgradeProduct : Obj:ProductModel
	*/
	function CreateUpgradeLink($product, $upgradeProduct) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		//! Checks not already a link here. Not throwing an exception here makes this non-destructive; adding more links between 2 products has no effect.
		if (! in_array ( $upgradeProduct, $product->GetUpgrades () )) {
			$create_product_upgrade_sql = 'INSERT INTO tblProduct_Upgrade (`Product_ID`,`Product_Upgrade_ID`) VALUES (' . $product->GetProductId () . ',' . $upgradeProduct->GetProductId () . ')';
			if (FALSE === $database->query ( $create_product_upgrade_sql )) {
				$error = new Error ( 'Problem creating link between product ' . $product->GetProductId () . ' and related product ' . $relatedProduct->GetProductId () . ' with SQL:<br /> ' . $create_product_upgrade_sql );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->getErrorMsg () );
			}
		}
		return true;
	}

	//! Attempts to remove a link between a product and a upgrade product, throws an exception if this fails
	/*!
	 * @return true if successful
	 * @param [in] product : Obj:ProductModel
	 * @param [in] upgradeProduct : Obj:ProductModel
	*/
	function RemoveUpgradeLink($product, $upgradeProduct) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$delete_product_upgrade_sql = 'DELETE FROM tblProduct_Upgrade WHERE Product_Upgrade_ID = ' . $upgradeProduct->GetProductId () . ' AND Product_ID = ' . $product->GetProductId ();
		if (FALSE === $database->query ( $delete_product_upgrade_sql )) {
			$error = new Error ( 'Problem removing link between product ' . $product->GetProductId () . ' and upgrade product ' . $relatedProduct->GetProductId () . ' with SQL:<br /> ' . $delete_product_similar_sql );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->getErrorMsg () );
		}
		return true;
	}

	//! Attempts to create a link between a product and an image, throws an exception if this fails
	/*!
	 * @return true if successful
	 * @param [in] product : Obj:ProductModel
	 * @param [in] image : Obj:ImageModel
	*/
	function CreateImageLink($product, $image) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		//! Checks not already a link here. Not throwing an exception here makes this non-destructive; adding more links between 2 products has no effect.
		if (! in_array ( $image, $product->GetImages () )) {
			$create_product_image_sql = 'INSERT INTO tblProduct_Images (`Product_ID`,`Image_ID`) VALUES (' . $product->GetProductId () . ',' . $image->GetImageId () . ')';
			if (FALSE === $database->query ( $create_product_image_sql )) {
				$error = new Error ( 'Problem creating link between product ' . $product->GetProductId () . ' and image ' . $image->GetImageId () . ' with SQL:<br /> ' . $create_product_image_sql );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->getErrorMsg () );
			}
		}
		//! If this is the first image, then make it the main image.
		if (0 == count ( $product->GetImages () )) {
			$image->SetMainImage ( 1 );
		}
		return true;
	}

	//! Attempts to remove a link between a product and an image, throws an exception if this fails. This is destructive to the image.
	/*!
	 * @return true if successful
	 * @param [in] product : Obj:ProductModel
	 * @param [in] image : Obj:ImageModel
	*/
	function RemoveImageLink($product, $image) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$imageController = new ImageController ( );
		//!  Remove the link
		$delete_product_image_sql = 'DELETE FROM tblProduct_Images WHERE Image_ID = ' . $image->GetImageId () . ' AND Product_ID = ' . $product->GetProductId ();
		if (FALSE === $database->query ( $delete_product_image_sql )) {
			$error = new Error ( 'Problem removing link between product ' . $product->GetProductId () . ' and image ' . $image->GetImageId () . ' with SQL:<br /> ' . $delete_product_image_sql );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->getErrorMsg () );
		}

		//! Remove the image
		$imageController->DeleteImage ( $image );
		return true;
	}

	//! Attempts to create a link between a product and a category, throws an exception if this fails
	/*!
	 * @return true if successful
	 * @param [in] product : Obj:ProductModel
	 * @param [in] category : Obj:CategoryModel
	*/
	function CreateCategoryLink($product, $category) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$check_sql = 'SELECT COUNT(*) FROM tblCategory_Products WHERE Product_ID = ' . $product->GetProductId () . ' AND Category_ID = ' . $category->GetCategoryId ();
		$check_result = $database->query ( $check_sql );
		if ($check_result) {
			if ($check_result->fetchColumn () == 0) {
				//! Checks not already a link here. Not throwing an exception here makes this non-destructive; adding more links between 2 products has no effect.
				$create_product_category_sql = 'INSERT INTO tblCategory_Products (`Product_ID`,`Category_ID`) VALUES (' . $product->GetProductId () . ',' . $category->GetCategoryId () . ')';
				if (FALSE === $database->query ( $create_product_category_sql )) {
					$error = new Error ( 'Problem creating link between product ' . $product->GetProductId () . ' and category ' . $category->GetCategoryId () . ' with SQL:<br /> ' . $create_product_category_sql );
					$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
					throw new Exception ( $error->getErrorMsg () );
				}
			}
		}
		return true;
	}

	//! Attempts to remove a link between a product and a category, throws an exception if this fails
	/*!
	 * @return true if successful
	 * @param [in] product : Obj:ProductModel
	 * @param [in] category : Obj:CategoryModel
	*/
	function RemoveCategoryLink($product, $category) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$delete_product_category_sql = 'DELETE FROM tblCategory_Products WHERE Product_ID = ' . $product->GetProductId () . ' AND Category_ID = ' . $category->GetCategoryId ();
		if (FALSE === $database->query ( $delete_product_category_sql )) {
			$error = new Error ( 'Problem removing link between product ' . $product->GetProductId () . ' and category ' . $category->GetCategoryId () . ' with SQL:<br /> ' . $delete_product_category_sql );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->getErrorMsg () );
		}
		return true;
	}

	//! Retrieves the deal of the week for a catalogue
	/*!
	 * @return true if successful
	 * @param [in] catalogue : Obj:CatalogueModel
	*/
	function GetDealOfTheWeek($catalogue) {
		$sql = 'SELECT Product_ID FROM tblProduct WHERE Offer_Of_Week = \'1\' LIMIT 1';
		if ($result = $this->mDatabase->query ( $sql )) {
			while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
				$newProduct = new ProductModel ( $resultObj->Product_ID );
				if ($newProduct->GetCatalogue ()->GetCatalogueId () == $catalogue->GetCatalogueId ()) {
					return $newProduct;
				}
			}
		} else {
			$error = new Error ( 'Problem getting deal of the week with SQL:<br /> ' . $delete_product_category_sql );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->getErrorMsg () );
		}
		// Just return ANY product if needed!
		return $this->GetAnyProduct($catalogue);
	}

	//! Retrieves the newest product in a catalogue (that is NOT on sale - so if you don't want something to show up, tick the sale box!)
	/*!
	 * @return true if successful
	*/
	function GetNewestProduct() {
		$sql = 'SELECT Product_ID FROM tblProduct WHERE On_Sale = \'0\' ORDER BY Product_ID DESC LIMIT 1';
		if ($result = $this->mDatabase->query ( $sql )) {
			while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
				$newProduct = new ProductModel ( $resultObj->Product_ID );
				return $newProduct;
			}
		} else {
			$error = new Error ( 'Problem getting newest product with SQL:<br /> ' . $delete_product_category_sql );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->getErrorMsg () );
		}
		// Just return ANY product if needed!
		return $this->GetAnyProduct($catalogue);
	}

	//! Retrieves any product for a catalogue
	/*!
	 * @return true if successful
	 * @param [in] catalogue : Obj:CatalogueModel
	*/
	function GetAnyProduct($catalogue=false) {
		$sql = 'SELECT Product_ID FROM tblProduct ORDER BY RAND() LIMIT 1';
		if ($result = $this->mDatabase->query ( $sql )) {
			while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
				$newProduct = new ProductModel ( $resultObj->Product_ID );
				return $newProduct;
			}
		} else {
			$error = new Error ( 'Problem getting any product with SQL:<br /> ' . $delete_product_category_sql );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->getErrorMsg () );
		}
		return false;
	}

	//! Given say $category=accessories and $product=din cap, will return $category=accessories tank (the (sub) category that links the two)
	function GetLinkCategory($product, $category) {
		$sql = 'SELECT
					tblCategory_Products.Category_ID
				FROM
					tblCategory_Products
				INNER JOIN tblCategory
					ON tblCategory.Category_ID = tblCategory_Products.Category_ID
				WHERE
					tblCategory_Products.Product_ID = ' . $product->GetProductId () . '
				AND tblCategory.Parent_Category_ID = ' . $category->GetCategoryId () . '
				LIMIT 1
				';
		if ($result = $this->mDatabase->query ( $sql )) {
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$linkCat = new CategoryModel ( $resultObj->Category_ID );
			return $linkCat;
		} else {
			$error = new Error ( 'Problem getting link category with SQL:<br /> ' . $delete_product_category_sql );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->getErrorMsg () );
		}
		return false;
	}

	//! Returns the offers of the week for a catalogue
	/*!
	* @param $catalogue - Obj:CatalogueModel - The catalogue to load the offers from
	* @param $numberOfOffers - The number of offers to fetch
	* @return Array of ProductModel objects, empty if none
	*/
	function GetOffersOfTheWeek($catalogue, $numberOfOffers = 0) {
		$this->mOffersOfTheWeek = array ();
		if (! isset ( $this->mOffersOfTheWeek ) || 0 == count ( $this->mOffersOfTheWeek )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$sql = 'SELECT tblProduct.Product_ID FROM tblProduct
						INNER JOIN tblCategory_Products ON tblCategory_Products.Product_ID = tblProduct.Product_ID
						INNER JOIN tblCategory ON tblCategory_Products.Category_ID = tblCategory.Category_ID
						WHERE Offer_Of_Week = \'1\' AND tblProduct.Product_ID IN
						(SELECT Product_ID FROM tblCategory_Products)
						AND tblCategory.Catalogue_ID = '.$catalogue->GetCatalogueId().'
						ORDER BY RAND()';
			if (! $result = $database->query ( $sql )) {
				$error = new Error ( 'Could not fetch the offers of the week' );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$products = $result->fetchAll ( PDO::FETCH_OBJ );
			if ($numberOfOffers == 0) {
				$numberOfOffers = count ( $products );
			}
			// For each SKU, create a new instance of it and store it in the mSkus member variable
			foreach ( $products as $product ) {
				$newProd = new ProductModel ( $product->Product_ID );
				if ($catalogue->GetCatalogueId () == $newProd->GetCatalogue ()->GetCatalogueId () && count ( $this->mOffersOfTheWeek ) < $numberOfOffers) {
					$this->mOffersOfTheWeek [] = $newProd;
				}
			}
			if (0 == count ( $products )) {
				$this->mOffersOfTheWeek = array ();
			}
		}
		return $this->mOffersOfTheWeek;
	}

	function GetClearance($catalogue) {
		$sql = 'SELECT tblproduct.Product_ID FROM tblproduct
					INNER JOIN tblcategory_products
						ON tblcategory_products.product_id = tblproduct.product_id
					INNER JOIN tblcategory
						ON tblcategory.category_id = tblcategory_products.category_id
					WHERE on_clearance = \'1\' AND tblcategory.catalogue_id = ' . $catalogue->GetCatalogueId ();
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not fetch the clearance list' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$clearanceList = array ();
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$newProd = new ProductModel ( $resultObj->Product_ID );
			$clearanceList [] = $newProd;
		}
		return $clearanceList;
	}

	//! Returns all of the products in the catalogue(s) supplied
	/*!
	 * @param $catalogueList	- Str - A comma-seperated list of catalogue IDs to be used directly by the SQL - NB. No checking done, eg. 123,154,156,152
	 * @return Array of ProductModel objects
	 */
	function GetAllProductsInCatalogue($catalogueList) {
		if (is_object ( $catalogueList )) {
			$catalogueList = $catalogueList->GetCatalogueId ();
		}
		$sql = '
				SELECT DISTINCT tblProduct.Product_ID
				FROM tblProduct
				INNER JOIN tblCategory_Products
					ON tblCategory_Products.Product_ID = tblProduct.Product_ID
				INNER JOIN tblCategory
					ON tblCategory_Products.Category_ID = tblCategory.Category_ID
				WHERE tblCategory.Catalogue_ID IN (' . $catalogueList . ')
				AND tblProduct.Hidden = \'0\'
				';
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not fetch the all products' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$products = $result->fetchAll ( PDO::FETCH_OBJ );
		// For each SKU, create a new instance of it and store it in the mSkus member variable
		foreach ( $products as $product ) {
			$newProd = new ProductModel ( $product->Product_ID );
			$retProds [] = $newProd;
		}
		if (0 == count ( $products )) {
			$retProds = array ();
		}
		return $retProds;
	}

	//! Checks that this product is not in any current (authorised) orders, and that it isn't waiting to be downloaded (meaning downloaded is 0 and status is in transit)
	/*!
	 * @param $product - The product to check
	 * @return Boolean - True if the product can be deleted, false otherwise
	 */
	function IsSafeToDelete($product) {
		$skus = $product->GetSkus ();
		$skuList = '';
		// Build a list of SKUs for a SQL 'IN' clause
		foreach ( $skus as $sku ) {
			$skuList .= $sku->GetSkuId () . ', ';
		}
		// Remove the last 2 characters (, )
		$skuList = substr ( $skuList, 0, (strlen ( $skuList ) - 2) );
		// Get orders that are authorised with these skus in
		$sql = 'SELECT
					COUNT(tblOrder.Order_ID) AS OrderCount
				FROM tblOrder
					INNER JOIN tblBasket_Skus
						ON tblOrder.Basket_ID = tblBasket_Skus.Basket_ID
				WHERE tblOrder.Status_ID = 10
				AND tblBasket_Skus.SKU_ID IN (' . $skuList . ')';
		$result = $this->mDatabase->query ( $sql );
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		if ($resultObj->OrderCount > 0) {
			return false;
		}
		// Now check that there aren't any orders waiting to be downloaded
		$sql = 'SELECT
					COUNT(tblOrder.Order_ID) AS OrderCount
				FROM tblOrder
					INNER JOIN tblBasket_Skus
						ON tblOrder.Basket_ID = tblBasket_Skus.Basket_ID
				WHERE tblOrder.Status_ID = 3
				AND tblOrder.Downloaded = \'0\'
				AND tblBasket_Skus.SKU_ID IN (' . $skuList . ')';
		$result = $this->mDatabase->query ( $sql );
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		if ($resultObj->OrderCount > 0) {
			return false;
		} else {
			return true;
		}
	}

	function GetMissingSageCodes($catalogueList) {
		$retArr = array ();
		$sql = 'SELECT DISTINCT tblSku.SKU_ID, tblCategory.Display_Name FROM tblSku
				INNER JOIN tblProduct_SKUs
					ON tblProduct_SKUs.SKU_ID = tblSku.SKU_ID
				INNER JOIN tblCategory_Products
					ON tblProduct_SKUs.Product_ID = tblCategory_Products.Product_ID
				INNER JOIN tblCategory
					ON tblCategory_Products.Category_ID = tblCategory.Category_ID
				INNER JOIN tblProduct
					ON tblCategory_Products.Product_ID = tblProduct.Product_ID
				WHERE tblCategory.Catalogue_ID IN (' . $catalogueList . ') AND tblSku.Sage_Code IS NULL AND tblProduct.For_Sale = \'1\'
				OR tblCategory.Catalogue_ID IN (' . $catalogueList . ') AND tblSku.Sage_Code = \'\' AND tblProduct.For_Sale = \'1\'
				ORDER BY tblCategory.Display_Name';
			#	echo $sql;
		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$sku = new SkuModel ( $resultObj->SKU_ID );
			$product = $sku->GetParentProduct ();
			$retArr [] = $product;
		}
		usort ( $retArr, array ("ProductModel", "CompareByTopCategory" ) );
		return $retArr;
	}

	function GetMissingDescriptions($catalogueList) {
		$retArr = array ();
		$sql = 'SELECT tblProduct_Text.Product_ID, tblProduct_Text.Display_Name
					FROM tblProduct_Text
					INNER JOIN tblCategory_Products
						ON tblCategory_Products.Product_ID = tblProduct_Text.Product_ID
					INNER JOIN tblCategory
						ON tblCategory_Products.Category_ID = tblCategory.Category_ID
					WHERE tblProduct_Text.Long_Description = \'\'
					AND tblCategory.Catalogue_ID IN (' . $catalogueList . ')';
		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$product = new ProductModel ( $resultObj->Product_ID );
			$retArr [] = $product;
		}
		usort ( $retArr, array ("ProductModel", "CompareByTopCategory" ) );
		return $retArr;
	}

	function GetMissingPrices($catalogueList) {
		$retArr = array ();
		$sql = 'SELECT tblProduct.Product_ID
					FROM tblProduct
					INNER JOIN tblCategory_Products
						ON tblCategory_Products.Product_ID = tblProduct.Product_ID
					INNER JOIN tblCategory
						ON tblCategory_Products.Category_ID = tblCategory.Category_ID
					WHERE tblProduct.Actual_Price = 0 AND tblCategory.Catalogue_ID IN (' . $catalogueList . ')';
		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$product = new ProductModel ( $resultObj->Product_ID );
			$retArr [] = $product;
		}
		usort ( $retArr, array ("ProductModel", "CompareByTopCategory" ) );
		return $retArr;
	}

	///////// DOESN'T DO POSTAGES!!
	function GetMissingPostages($catalogueList) {
		$retArr = array ();
		$sql = 'SELECT tblProduct.Product_ID
					FROM tblProduct
					INNER JOIN tblCategory_Products
						ON tblCategory_Products.Product_ID = tblProduct.Product_ID
					INNER JOIN tblCategory
						ON tblCategory_Products.Category_ID = tblCategory.Category_ID
					WHERE tblProduct.For_Sale = 0 AND tblCategory.Catalogue_ID IN (' . $catalogueList . ')';
		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$product = new ProductModel ( $resultObj->Product_ID );
			$retArr [] = $product;
		}
		usort ( $retArr, array ("ProductModel", "CompareByTopCategory" ) );
		return $retArr;
	}

	//! Returns all products with zero weights
	/*!
	 * @param $catalogueList	- Str - A comma-seperated list of catalogue IDs to be used directly by the SQL - NB. No checking done, eg. 123,154,156,152
	 * @return Array of ProductModel objects
	 */
	function GetMissingWeights($catalogueList) {
		$retArr = array ();
		$sql = 'SELECT tblProduct.Product_ID
					FROM tblProduct
					INNER JOIN tblCategory_Products
						ON tblCategory_Products.Product_ID = tblProduct.Product_ID
					INNER JOIN tblCategory
						ON tblCategory_Products.Category_ID = tblCategory.Category_ID
					WHERE tblProduct.Weight = 0 AND tblCategory.Catalogue_ID IN (' . $catalogueList . ')';
		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$product = new ProductModel ( $resultObj->Product_ID );
			$retArr [] = $product;
		}
		usort ( $retArr, array ("ProductModel", "CompareByTopCategory" ) );
		return $retArr;
	}

	function GetDodgyOptions($catalogueList) {
		$retArr = array ();
		$sql = 'SELECT tblProduct_Attributes.Product_ID
					FROM tblProduct_Attributes
					INNER JOIN tblCategory_Products ON tblCategory_Products.Product_ID = tblProduct_Attributes.Product_ID
					INNER JOIN tblCategory ON tblCategory.Category_ID = tblCategory_Products.Category_ID
					WHERE tblCategory.Catalogue_ID IN (' . $catalogueList . ')
					GROUP BY tblProduct_Attributes.Product_ID
					HAVING count(*) > 2
					';
		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$product = new ProductModel ( $resultObj->Product_ID );
			$retArr [] = $product;
		}
		usort ( $retArr, array ("ProductModel", "CompareByTopCategory" ) );
		return $retArr;
	}

	function GetDodgySage($catalogueList) {
		$retArr = array ();
		$sql = 'SELECT DISTINCT tblSku.SKU_ID FROM tblSku
				INNER JOIN tblProduct_SKUs
					ON tblProduct_SKUs.SKU_ID = tblSku.SKU_ID
				INNER JOIN tblCategory_Products
					ON tblProduct_SKUs.Product_ID = tblCategory_Products.Product_ID
				INNER JOIN tblCategory
					ON tblCategory_Products.Category_ID = tblCategory.Category_ID
				WHERE tblCategory.Catalogue_ID IN (' . $catalogueList . ')';
		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$sku = new SkuModel ( $resultObj->SKU_ID );
			$sageCode = $sku->GetSageCode ();
			if (@is_numeric ( $sageCode [0] )) {
				$retArr [] = $sku;
			}
		}
		return $retArr;
	}

	function GetPoorStock($catalogueList,$startTimestamp,$endTimestamp) {
		$retArr = array ();
		$sql = 'SELECT tblProduct_Text.Display_Name, tblProduct_SKUs.SKU_ID FROM
				tblProduct_Text inner join tblProduct_SKUs ON tblProduct_Text.Product_ID = tblProduct_SKUs.Product_ID
				INNER JOIN tblBasket_Skus ON tblBasket_Skus.SKU_ID = tblProduct_SKUs.SKU_ID
				INNER JOIN tblOrder ON tblOrder.Basket_ID = tblBasket_Skus.Basket_ID
				INNER JOIN tblCategory_Products ON tblCategory_Products.Product_ID = tblProduct_Text.Product_ID
				INNER JOIN tblCategory ON tblCategory_Products.Category_ID = tblCategory.Category_ID
				WHERE tblOrder.Dispatch_Date_ID <> 1
				AND tblCategory.Catalogue_ID IN ('.$catalogueList.')
				AND tblOrder.Created_Date BETWEEN ' . $startTimestamp . ' AND ' . $endTimestamp . '
				ORDER BY Display_Name, SKU_ID ASC';
		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$sku = new SkuModel ( $resultObj->SKU_ID );
				$retArr [] = $sku;
		}
		return $retArr;
	}

	function GetMostCancelled($catalogueList,$startTimestamp,$endTimestamp) {
		$retArr = array ();
		$sql = 'SELECT tblProduct_Text.Display_Name, tblProduct_SKUs.SKU_ID FROM
				tblProduct_Text inner join tblProduct_SKUs ON tblProduct_Text.Product_ID = tblProduct_SKUs.Product_ID
				INNER JOIN tblBasket_Skus ON tblBasket_Skus.SKU_ID = tblProduct_SKUs.SKU_ID
				INNER JOIN tblOrder ON tblOrder.Basket_ID = tblBasket_Skus.Basket_ID
				INNER JOIN tblCategory_Products ON tblCategory_Products.Product_ID = tblProduct_Text.Product_ID
				INNER JOIN tblCategory ON tblCategory_Products.Category_ID = tblCategory.Category_ID
				WHERE tblOrder.Status_ID IN (6,7)
				AND tblCategory.Catalogue_ID IN ('.$catalogueList.')
				AND tblOrder.Created_Date BETWEEN ' . $startTimestamp . ' AND ' . $endTimestamp . '
				ORDER BY Display_Name, SKU_ID ASC';
		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$sku = new SkuModel ( $resultObj->SKU_ID );
				$retArr [] = $sku;
		}
		return $retArr;
	}

	function GetNotInStacks($catalogueList) {
		$retArr = array ();
		$sql = 'SELECT tblProduct_Text.Product_ID FROM tblProduct_Text
					INNER JOIN tblCategory_Products ON tblCategory_Products.Product_ID = tblProduct_Text.Product_ID
					INNER JOIN tblCategory ON tblCategory_Products.Category_ID = tblCategory.Category_ID
				WHERE tblCategory.Catalogue_ID IN ('.$catalogueList.')
				AND tblProduct_Text.Product_ID NOT IN
					(SELECT tblProduct_Text.Product_ID FROM tblProduct_Text INNER JOIN tblPackage_Products ON tblProduct_Text.Product_ID = tblPackage_Products.Product_ID)
				ORDER BY tblCategory.Display_Name ASC';
		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$product = new ProductModel( $resultObj->Product_ID );
			$retArr [] = $product;
		}
		return $retArr;
	}
	// Now defunct - see SkuController->GetOutOfStockSkus
	function GetOutOfStock($catalogueList) {
		$retArr = array ();
		$sql = 'SELECT tblProduct.Product_ID FROM tblProduct
					INNER JOIN tblCategory_Products ON tblCategory_Products.Product_ID = tblProduct.Product_ID
					INNER JOIN tblCategory ON tblCategory_Products.Category_ID = tblCategory.Category_ID
				WHERE tblCategory.Catalogue_ID IN ('.$catalogueList.')
				AND tblProduct.In_Stock = \'0\'
				OR tblProduct.Product_ID IN
				(
					SELECT tblProduct_SKUs.Product_ID
					FROM tblProduct_SKUs
					WHERE SKU_ID IN
						(SELECT tblSku_Attributes.SKU_ID FROM tblSku_Attributes WHERE Attribute_Value LIKE \'%OUT OF STOCK%\' OR Attribute_Value LIKE \'%OUT OF STOCK%\' )
				)
				ORDER BY tblCategory.Display_Name ASC';
	#	echo $sql;
		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$product = new ProductModel( $resultObj->Product_ID );
			$retArr [] = $product;
		}
		return $retArr;
	}

	function GetZeroSkuProducts($catalogueList) {
		$retArr = array ();
		$sql = 'SELECT tblProduct_Text.Product_ID, tblProduct_Text.Display_Name
					FROM tblProduct
				INNER JOIN tblProduct_Text
					ON tblProduct_Text.Product_ID = tblProduct.Product_ID
				INNER JOIN tblCategory_Products
					ON tblProduct.Product_ID = tblCategory_Products.Product_ID
				INNER JOIN tblCategory
					ON tblCategory_Products.Category_ID = tblCategory.Category_ID
				WHERE tblCategory.Catalogue_ID IN (' . $catalogueList . ')
				AND tblProduct.Product_ID NOT IN
					(
					SELECT tblProduct_SKUs.Product_ID
					FROM tblProduct_SKUs
					)';
		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$product = new ProductModel ( $resultObj->Product_ID );
			$retArr [] = $product;
		}
		return $retArr;
	}

	function GetMissingSizes($catalogueList) {
		$retArr = array ();
		$sql = 'SELECT tblProduct.Product_ID, tblProduct_Text.Display_Name, tblCategory.Display_Name
				FROM tblProduct
					INNER JOIN tblProduct_Text
						ON tblProduct_Text.Product_ID = tblProduct.Product_ID
					INNER JOIN tblCategory_Products
						ON tblProduct.Product_ID = tblCategory_Products.Product_ID
					INNER JOIN tblCategory
						ON tblCategory_Products.Category_ID = tblCategory.Category_ID
					WHERE tblCategory.Catalogue_ID IN (' . $catalogueList . ')
					AND tblProduct.Product_ID NOT IN(
						SELECT tblProduct_Attributes.Product_ID
							FROM tblProduct_Attributes
							INNER JOIN tblCategory_Products
								ON tblProduct_Attributes.Product_ID = tblCategory_Products.Product_ID
							INNER JOIN tblCategory
								ON tblCategory_Products.Category_ID = tblCategory.Category_ID
							WHERE tblCategory.Catalogue_ID IN (' . $catalogueList . ')
								GROUP BY tblProduct_Attributes.Product_ID, tblCategory.Parent_Category_ID
							HAVING ( COUNT(tblProduct_Attributes.Product_ID) = 1 )
						)
				ORDER BY tblCategory.Display_Name';
		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$product = new ProductModel ( $resultObj->Product_ID );
			$retArr [] = $product;
		}
		usort ( $retArr, array ("ProductModel", "CompareByTopCategory" ) );
		return $retArr;
	}

	function GetMissingRelatedSimilar($catalogueList) {
		$relatedArr = $this->GetMissingRelated ( $catalogueList );
		$similarArr = $this->GetMissingSimilar ( $catalogueList );
		$relatedAndSimilar = array_merge ( $relatedArr, $similarArr );
		usort ( $relatedAndSimilar, array ("ProductModel", "CompareByTopCategory" ) );
		return array_unique ( $relatedAndSimilar );
	}

	//! Returns the products that have no/not enough related products
	function GetMissingRelated($catalogueList) {
		$retArr = array ();
		// Products with none
		$sql = 'SELECT tblProduct.Product_ID FROM tblProduct
				INNER JOIN tblCategory_Products
					ON tblProduct.Product_ID = tblCategory_Products.Product_ID
				INNER JOIN tblCategory
					ON tblCategory_Products.Category_ID = tblCategory.Category_ID
				WHERE tblCategory.Catalogue_ID IN (' . $catalogueList . ')
					AND
				tblProduct.Product_ID NOT IN
				(SELECT tblProduct_Related.Product_ID FROM tblProduct_Related)';
		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$product = new ProductModel ( $resultObj->Product_ID );
			$retArr [] = $product;
		}
		// Products with less than 3
		$sql = 'SELECT tblProduct_Related.Product_ID FROM tblProduct_Related
				INNER JOIN tblCategory_Products
					ON tblProduct_Related.Product_ID = tblCategory_Products.Product_ID
				INNER JOIN tblCategory
					ON tblCategory_Products.Category_ID = tblCategory.Category_ID
				WHERE tblCategory.Catalogue_ID IN (' . $catalogueList . ')
				GROUP BY (tblProduct_Related.Product_ID)
				HAVING COUNT(tblProduct_Related.Product_ID) < 3';
		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$product = new ProductModel ( $resultObj->Product_ID );
			$retArr [] = $product;
		}
		return $retArr;
	}

	//! Returns the products that have no/not enough similar products
	function GetMissingSimilar($catalogueList) {
		$retArr = array ();
		// Products with none
		$sql = 'SELECT tblProduct.Product_ID FROM tblProduct
				INNER JOIN tblCategory_Products
					ON tblProduct.Product_ID = tblCategory_Products.Product_ID
				INNER JOIN tblCategory
					ON tblCategory_Products.Category_ID = tblCategory.Category_ID
				WHERE tblCategory.Catalogue_ID IN (' . $catalogueList . ')
					AND
				tblProduct.Product_ID NOT IN
				(SELECT tblProduct_Similar.Product_ID FROM tblProduct_Similar)';
		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$product = new ProductModel ( $resultObj->Product_ID );
			$retArr [] = $product;
		}
		// Products with less than 3
		$sql = 'SELECT tblProduct_Similar.Product_ID FROM tblProduct_Similar
				INNER JOIN tblCategory_Products
					ON tblProduct_Similar.Product_ID = tblCategory_Products.Product_ID
				INNER JOIN tblCategory
					ON tblCategory_Products.Category_ID = tblCategory.Category_ID
				WHERE tblCategory.Catalogue_ID IN (' . $catalogueList . ')
				GROUP BY (tblProduct_Similar.Product_ID)
				HAVING COUNT(tblProduct_Similar.Product_ID) < 3';
		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$product = new ProductModel ( $resultObj->Product_ID );
			$retArr [] = $product;
		}
		return $retArr;
	}

	function GetAllProducts($catalogueList) {
		$retArr = array ();
		$sql = 'SELECT tblProduct.Product_ID, tblProduct_Text.Display_Name, tblCategory.Display_Name
				FROM tblProduct
				INNER JOIN tblCategory_Products
					ON tblProduct.Product_ID = tblCategory_Products.Product_ID
				INNER JOIN tblProduct_Text
					ON tblProduct_Text.Product_ID = tblProduct.Product_ID
				INNER JOIN tblCategory
					ON tblCategory_Products.Category_ID = tblCategory.Category_ID
				WHERE tblCategory.Catalogue_ID IN (' . $catalogueList . ')
				ORDER BY tblCategory.Display_Name';
		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$product = new ProductModel ( $resultObj->Product_ID );
			$retArr [] = $product;
		}
		usort ( $retArr, array ("ProductModel", "CompareByTopCategory" ) );
		return $retArr;
	}

	function GetMissingImages($catalogueList) {
		$retArr = array ();
		$sql = 'SELECT tblProduct.Product_ID
				FROM tblProduct
				INNER JOIN tblCategory_Products
					ON tblProduct.Product_ID = tblCategory_Products.Product_ID
				INNER JOIN tblCategory
					ON tblCategory_Products.Category_ID = tblCategory.Category_ID
				WHERE tblCategory.Catalogue_ID IN (' . $catalogueList . ')
				AND tblProduct.Product_ID NOT IN
				(SELECT tblProduct_Images.Product_ID FROM tblProduct_Images)';
		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$product = new ProductModel ( $resultObj->Product_ID );
			$retArr [] = $product;
		}
		usort ( $retArr, array ("ProductModel", "CompareByTopCategory" ) );
		return $retArr;
	}

	//! Gets the products in the categories and manufacturers indicated
	/*!
	 * @param $categoryList - String - Comma seperated list of category IDs
	 * @param $manufacturerList - String - Comma seperated list of manufacturer IDs
	 * @return Array of ProductModel objects
	 */
	function GetProductsForCategoryListManufacturerList($categoryList,$manufacturerList) {
		$retArr = array();
		$sql = '
				SELECT DISTINCT tblProduct.Product_ID, tblProduct_Text.Display_Name
				FROM tblProduct
				INNER JOIN tblCategory_Products ON tblCategory_Products.Product_ID = tblProduct.Product_ID
				INNER JOIN tblProduct_Text 		ON tblProduct_Text.Product_ID = tblProduct.Product_ID
				WHERE tblCategory_Products.Category_ID IN (
														   SELECT tblCategory.Category_ID FROM tblCategory WHERE Parent_Category_ID IN ('.$categoryList.')
														   )
				AND tblProduct.Manufacturer_ID IN ('.$manufacturerList.')
				ORDER BY tblProduct_Text.Display_Name
				';
		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$product = new ProductModel ( $resultObj->Product_ID );
			$retArr [] = $product;
		}
		return $retArr;
	}

	//! Given a product ID and timestamp, gets the amount of sales within 24 hours (86400 seconds) before that timestamp
	/*
	 * @param $productId - Int - The product ID
	 * @param $timestamp - Int - A UNIX timestamp
	 * @return Int - The number of sales
	 */
	function GetSalesForProductIdForTimestamp($productId,$timestamp,$sampleRate=86400,$excludePackages=false) {
		$startTime = $timestamp-$sampleRate;
		#echo ' BETWEEN '.date('r',$startTime).' AND '.date('r',$timestamp).' <br>  ';
		$sql = '
				SELECT COUNT(tblBasket_Skus.SKU_ID) AS SKUSalesCount
				FROM tblBasket_Skus
				INNER JOIN tblOrder ON tblBasket_Skus.Basket_ID = tblOrder.Basket_ID
				INNER JOIN tblProduct_SKUs ON tblBasket_Skus.SKU_ID = tblProduct_SKUs.SKU_ID
				WHERE tblOrder.Created_Date BETWEEN '.$startTime.' AND '.$timestamp.'
				AND tblProduct_SKUs.Product_ID = '.$productId.'
				AND tblOrder.Status_ID = 6
				';
		$packageSql = '
				SELECT COUNT(tblBasket_Packages.Package_ID) AS PackageSalesCount
				FROM tblBasket_Packages
				INNER JOIN tblOrder ON tblBasket_Packages.Basket_ID = tblOrder.Basket_ID
				INNER JOIN tblPackage_Products ON tblPackage_Products.Package_ID = tblBasket_Packages.Package_ID
				WHERE tblOrder.Created_Date BETWEEN '.$startTime.' AND '.$timestamp.'
				AND tblPackage_Products.Product_ID = '.$productId.'
				AND tblOrder.Status_ID = 6
						';
		// Product Result
		$result = $this->mDatabase->query($sql);
		$resultObj = $result->fetch(PDO::FETCH_OBJ);

		// Package Result
		if(!$excludePackages) {
			$packageResult = $this->mDatabase->query($packageSql);
			$packageResultObj = $packageResult->fetch(PDO::FETCH_OBJ);
			$totalSalesCount = $resultObj->SKUSalesCount + $packageResultObj->PackageSalesCount;
		} else {
			$totalSalesCount = $resultObj->SKUSalesCount;
		}

	/*	$fh = fopen('A.txt','a+');
		fwrite($fh,"Sales += ".$totalSalesCount." between ".date('r',$startTime)." and ".date('r',$timestamp)." \n");*/
		return $totalSalesCount;
	} // End GetSalesForProductIdForTimestamp

	//! Gets the product that is set to 'featured' in the given category (used by CategoryView)
	function GetBestSellingProductForCategory($category) {
		// while I fix the bug.... now using CategoryModel->GetBestSellingProduct so this is now not needed
		$sql = '
				SELECT DISTINCT
					tblProduct.Product_ID
				FROM tblProduct
					INNER JOIN tblCategory_Products ON tblCategory_Products.Product_ID = tblProduct.Product_ID
					INNER JOIN tblCategory ON tblCategory.Category_ID = tblCategory_Products.Category_ID
				WHERE
					tblCategory.Category_ID = '.$category->GetCategoryId().'
					OR tblCategory.Parent_Category_ID = '.$category->GetCategoryId().'
					AND tblProduct.Hidden = \'0\'
				ORDER BY tblProduct.Product_ID ASC
				LIMIT 1
				';
		// Product Result
		$result = $this->mDatabase->query($sql);
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		if($resultObj) {
			$product = new ProductModel($resultObj->Product_ID);
		} else {
			$product = $this->GetAnyProductInCategory($category);
		}
		return $product;
	} // End GetBestSellingProductForCategory

	//! Gets ANY product in the category - this is a last resort!
	function GetAnyProductInCategory($category) {
		$sql = '
				SELECT DISTINCT
					tblProduct.Product_ID
				FROM tblProduct
				INNER JOIN tblCategory_Products ON tblCategory_Products.Product_ID = tblProduct.Product_ID
				INNER JOIN tblCategory ON tblCategory.Category_ID = tblCategory_Products.Category_ID
				WHERE (tblCategory.Category_ID = '.$category->GetCategoryId().' OR tblCategory.Parent_Category_ID = '.$category->GetCategoryId().')
				AND tblProduct.Hidden = \'0\'
				LIMIT 1
				';
		// Product Result
		$result = $this->mDatabase->query($sql);
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		if($resultObj) {
			$product = new ProductModel($resultObj->Product_ID);
			return $product;
		} else {
			return false;
		}
	} // End GetBestSellingProductForCategory

	//! Gets the 'next best' selling product in a category - this is because the 'featured' box defaults to this product, which can't be the same as the 'best seller' because that
	// wouldn't look good ---- TOO SLOW!!!! Because if none have sold??
	function GetNextBestSellingProductForCategory($category) {
		$sql = '
				SELECT Product_ID FROM (
					SELECT DISTINCT
						tblProduct.Product_ID,
						COUNT(tblProduct.Product_ID) AS ProductCount,
						tblProduct_Text.Display_Name
					FROM tblProduct
					INNER JOIN tblProduct_Text ON tblProduct_Text.Product_ID = tblProduct.Product_ID
					INNER JOIN tblProduct_SKUs ON tblProduct_SKUs.Product_ID = tblProduct.Product_ID
					INNER JOIN tblBasket_Skus ON tblProduct_SKUs.SKU_ID = tblBasket_Skus.SKU_ID
					INNER JOIN tblOrder ON tblBasket_Skus.Basket_ID = tblOrder.Basket_ID
					INNER JOIN tblCategory_Products ON tblCategory_Products.Product_ID = tblProduct.Product_ID
					INNER JOIN tblCategory ON tblCategory.Category_ID = tblCategory_Products.Category_ID
					WHERE tblOrder.Status_ID = 3
					AND (tblCategory.Category_ID = '.$category->GetCategoryId().' OR tblCategory.Parent_Category_ID = '.$category->GetCategoryId().')
					AND tblProduct.Hidden = \'0\'
					GROUP BY tblProduct.Product_ID, tblProduct_Text.Display_Name
					ORDER BY ProductCount DESC
					LIMIT 2
				) TopTwoProducts
				ORDER BY ProductCount ASC
				LIMIT 1
				';
		// Product Result
		$result = $this->mDatabase->query($sql);
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		if($resultObj) {
			$product = new ProductModel($resultObj->Product_ID);
		} else {
			$product = $this->GetAnyProductInCategory($category);
		}
		return $product;
	} // End GetBestSellingProductForCategory

	//! Gets the 'third best' selling product in a category - this is used if the BRAND NEW and NEXT BEST are the same
	function GetThirdBestSellingProductForCategory($category) {
		$sql = '
				SELECT Product_ID FROM (
					SELECT DISTINCT
						tblProduct.Product_ID,
						COUNT(tblProduct.Product_ID) AS ProductCount,
						tblProduct_Text.Display_Name
					FROM tblProduct
					INNER JOIN tblProduct_Text ON tblProduct_Text.Product_ID = tblProduct.Product_ID
					INNER JOIN tblProduct_SKUs ON tblProduct_SKUs.Product_ID = tblProduct.Product_ID
					INNER JOIN tblBasket_Skus ON tblProduct_SKUs.SKU_ID = tblBasket_Skus.SKU_ID
					INNER JOIN tblOrder ON tblBasket_Skus.Basket_ID = tblOrder.Basket_ID
					INNER JOIN tblCategory_Products ON tblCategory_Products.Product_ID = tblProduct.Product_ID
					INNER JOIN tblCategory ON tblCategory.Category_ID = tblCategory_Products.Category_ID
					WHERE tblOrder.Status_ID = 3
					AND (tblCategory.Category_ID = '.$category->GetCategoryId().' OR tblCategory.Parent_Category_ID = '.$category->GetCategoryId().')
					AND tblProduct.Hidden = \'0\'
					GROUP BY tblProduct.Product_ID, tblProduct_Text.Display_Name
					ORDER BY ProductCount DESC
					LIMIT 3
				) TopThreeProducts
				ORDER BY ProductCount ASC
				LIMIT 1
				';
		// Product Result
		$result = $this->mDatabase->query($sql);
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		if($resultObj) {
			$product = new ProductModel($resultObj->Product_ID);
		} else {
			$product = $this->GetAnyProductInCategory($category);
		}
		return $product;
	} // End GetBestSellingProductForCategory

	//! Gets the product that is set to 'featured' in the given category (used by CategoryView)
	function GetBrandNewProductForCategory($category) {
		$sql = '
				SELECT DISTINCT
					tblProduct.Product_ID
				FROM tblProduct
				INNER JOIN tblProduct_SKUs ON tblProduct_SKUs.Product_ID = tblProduct.Product_ID
				INNER JOIN tblCategory_Products ON tblCategory_Products.Product_ID = tblProduct.Product_ID
				INNER JOIN tblCategory ON tblCategory.Category_ID = tblCategory_Products.Category_ID
				AND (tblCategory.Category_ID = '.$category->GetCategoryId().' OR tblCategory.Parent_Category_ID = '.$category->GetCategoryId().')
				AND tblProduct.Hidden = \'0\'
				ORDER BY Product_ID DESC
				LIMIT 1
				';
		// Product Result
		$result = $this->mDatabase->query($sql);
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		if($resultObj) {
			$product = new ProductModel($resultObj->Product_ID);
		} else {
			$product = $this->GetAnyProductInCategory($category);
		}
		return $product;
	} // End GetBrandNewProductForCategory

	//! Gets the products that has sold the most
	/*!
	 * @param $num - Int - The number of products to select
	 * @param $duration - String (Optional) - One of 'day', 'week', 'month'
	 */
	function GetBestSellingProducts($num,$duration=false) {
		$retArr = array();

		// If a duration is asked for to constrain the query then apply it.
		if($duration) {
			$endTime = time();

			// Sort out date range
			switch($duration) {
				case 'month':
					$startTime = strtotime("-1 month",$endTime);
				break;
				case 'day':
					$startTime = strtotime("-1 day",$endTime);
				break;
				case 'week':
				default:
					$startTime = strtotime("-1 week",$endTime);
				break;
			}
			// Additional SQL
			$additionalSql = '
					INNER JOIN tblOrder on tblBasket_Skus.Basket_ID = tblOrder.Basket_ID
				WHERE tblOrder.Created_Date BETWEEN '.$startTime.' AND '.$endTime.'
				AND tblProduct.Hidden = \'0\' ';
		} else {
			$additionalSql = 'WHERE tblProduct.Hidden = \'0\' ';
		}

		// Start SQL
		$sql = '
				SELECT DISTINCT
					tblProduct.Product_ID,
					COUNT(tblProduct.Product_ID) AS ProductCount,
					tblProduct_Text.Display_Name
				FROM tblProduct
					INNER JOIN tblProduct_SKUs ON tblProduct_SKUs.Product_ID = tblProduct.Product_ID
					INNER JOIN tblProduct_Text ON tblProduct.Product_ID = tblProduct_Text.Product_ID
					INNER JOIN tblCategory_Products ON tblCategory_Products.Product_ID = tblProduct.Product_ID
					LEFT JOIN tblBasket_Skus ON tblProduct_SKUs.SKU_ID = tblBasket_Skus.SKU_ID
					'.$additionalSql.'
				GROUP BY tblProduct_Text.Product_ID, tblProduct_Text.Display_Name
				ORDER BY ProductCount DESC
				LIMIT '.$num.'
				';#echo $sql;
		// Package Result
		$result = $this->mDatabase->query($sql);
		while($resultObj = $result->fetch(PDO::FETCH_OBJ)) {
			if($resultObj) {
				$retArr[] = new ProductModel($resultObj->Product_ID);
			}
		}
 		if(count($retArr) == 0) {
			$retArr[] = $this->GetAnyProduct();
		}
		return $retArr;
	} // End GetBestSellingProducts

	//! Gets the product that is set to 'featured' in the given category (used by CategoryView)
	function GetFeaturedProductForCategory($category) {
		$sql = '
				SELECT
					tblProduct.Product_ID
				FROM tblProduct
				INNER JOIN tblCategory_Products ON tblCategory_Products.Product_ID = tblProduct.Product_ID
				INNER JOIN tblCategory ON tblCategory.Category_ID = tblCategory_Products.Category_ID
				WHERE tblProduct.Featured = \'1\'
				AND (tblCategory.Category_ID = '.$category->GetCategoryId().' OR tblCategory.Parent_Category_ID = '.$category->GetCategoryId().')
				AND tblProduct.Hidden = \'0\'
				LIMIT 1
				';
		// Product Result
		$result = $this->mDatabase->query($sql);
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		if($resultObj) {
			$product = new ProductModel($resultObj->Product_ID);
			return $product;
		} else {
			if($this->GetBrandNewProductForCategory($category)->GetProductId() == $this->GetNextBestSellingProductForCategory($category)->GetProductId()) {
				return $this->GetThirdBestSellingProductForCategory($category);
			} else {
				return $this->GetAnyProductInCategory($category);
			}
		}
	} // End GetFeaturedProductForCategory

	//! Searches the products by name
	/*!
	 * @param $q - The string to search for
	 * @return Array of ProductModel objects
	 */
	function SearchByName($q) {
		$retArr = array();
		$sqlCondition = 'LIKE \'%'.$q.'%\'';
		// If the $q has spaces or dashes, try them both
		// Spaces
		if(strpos($q,' ')) {
			$sqlCondition .= ' OR Display_Name LIKE \'%'.str_replace(' ','-',$q).'%\'';
		}
		// Split up the words and try them separately
		$words = explode('-',$q);
		foreach($words as $word) {
			$sqlCondition .= ' OR Display_Name LIKE \'%'.$word.'%\'';
		}
		$sql = 'SELECT Product_ID FROM tblProduct_Text WHERE Display_Name '.$sqlCondition.' ORDER BY Display_Name';
		if(!$result = $this->mDatabase->query($sql)) {
			$error = new Error('Could not fetch the products!' .$sql);
			$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
			throw new Exception($error->GetErrorMsg());
		}
		while($resultObj = $result->fetch(PDO::FETCH_OBJ)) {
			$product = new ProductModel($resultObj->Product_ID);
			$retArr[] = $product;
		}
	return $retArr;
	}

} // End ProductController

/* DEBUG SECTION
try {
	$prodCont = new ProductController();
	#$product = $prodCont->CreateProduct();
	#var_dump($product->GetSkus());
	#$prod = $prodCont->CreateProduct();
	$prod2 = new ProductModel(1458);
	#$prod3 = new ProductModel(13);
	#$prodCont->CreateUpgradeLink($prod3,$prod2);
	$prodCont->DeleteProduct($prod2);
	#echo $prod2->PrettyPrintAll();
} catch(Exception $e) {
	echo $e->GetMessage();
}*/

?>