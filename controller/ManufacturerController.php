<?php

//! Deals with Manufacturer tasks (create, delete etc) \todo{get all manufacturers}
class ManufacturerController {

	//! Initialises the database connection
	function __construct() {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
	}

	//! Creates a new manufacturer in the database then returns this manufacturer as an object of type ManufacturerModel
	/*!
	 * @param [in] catalogue : Obj:CatalogueModel - the catalogue the manufacturer will be in
	 * @return Obj:ManufacturerModel - the new manufacturer
	 */
	function CreateManufacturer($catalogue) {
		$create_manufacturer_sql = 'INSERT INTO tblManufacturer (`Catalogue_ID`,`Display_Name`,`Description`,`Image_ID`) VALUES (\'' . $catalogue->GetCatalogueId () . '\',\'\',\'\',\'\')';
		if ($this->mDatabase->query ( $create_manufacturer_sql )) {
			$get_latest_manufacturer_sql = 'SELECT Manufacturer_ID FROM tblManufacturer ORDER BY Manufacturer_ID DESC LIMIT 1';
			if (! $result = $this->mDatabase->query ( $get_latest_manufacturer_sql )) {
				$error = new Error ( 'Could not select new manufacturer' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$latest_manufacturer = $result->fetch ( PDO::FETCH_OBJ );
			$newManufacturer = new ManufacturerModel ( $latest_manufacturer->Manufacturer_ID );
			return $newManufacturer;
		} else {
			$error = new Error ( 'Could not insert manufacturer' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

	//! Attempts to delete a manufacturer from the database, throws an exception if this fails
	/*!
	 * @return true if successful
	 * @param [in] manufacturer : Obj:ManufacturerModel - the manufacturer to delete
	 */
	function DeleteManufacturer($manufacturer) {
		$remove_link_sql = 'SELECT Product_ID FROM tblProduct WHERE Manufacturer_ID = ' . $manufacturer->GetManufacturerId ();
		$result = $this->mDatabase->query ( $remove_link_sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$tempProd = new ProductModel ( $resultObj->Product_ID );
			$tempProd->RemoveManufacturer ();
		}
		$delete_manufacturer_sql = 'DELETE FROM tblManufacturer WHERE Manufacturer_ID = ' . $manufacturer->GetManufacturerId ();
		if (! $this->mDatabase->query ( $delete_manufacturer_sql )) {
			$error = new Error ( 'Could not delete manufacturer ' . $manufacturer->GetManufacturerId () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->getErrorMsg () );
		} else {
			return true;
		}
	}

	//! Does the manufacturer exist, boolean response
	/*!
	 * @param $manufacturerId Obj:ManufacturerModel [in] The manufacturer to check
	 * @return Boolean - True if it does, false otherwise
	 */
	function ManufacturerExists($manufacturer) {
		$check_sql = '	SELECT COUNT(Manufacturer_ID) AS ManufacturerCount
						FROM tblManufacturer
						WHERE Manufacturer_ID = ' . $manufacturer->GetManufacturerId ();
		if ($result = $this->mDatabase->query ( $check_sql )) {
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			if ($resultObj->ManufacturerCount () > 0) {
				return true;
			} else {
				return false;
			}
		} else {
			$error = new Error ( 'Could not check manufacturer ' . $manufacturer->GetManufacturerId () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->getErrorMsg () );
		}
	}

	//! Gets all manufacturers that are in the supplied $catalogue
	/*!
	 * @param [in] catalogue : Obj:CatalogueModel - the catalogue to fetch the manufacturers for
	 * @param [in] empty : Bool - Whether or not to include empty manufacturers - true if you do
	 * @return Array of Obj:Manufacturers or an exception
	 */
	function GetAllManufacturersFor($catalogue, $empty = true) {
		if ($empty) {
			$get_all_manufacturers_sql = 'SELECT Manufacturer_ID
									FROM tblManufacturer
									WHERE Catalogue_ID = ' . $catalogue->GetCatalogueId () . '
									ORDER BY Display_Name ASC
									';
		} else {
			$get_all_manufacturers_sql = 'SELECT Manufacturer_ID
									FROM tblManufacturer
									WHERE Catalogue_ID = ' . $catalogue->GetCatalogueId () . '
									AND Manufacturer_ID IN (SELECT Manufacturer_ID FROM tblProduct)
									ORDER BY Display_Name ASC
									';
		}
		if (! $result = $this->mDatabase->query ( $get_all_manufacturers_sql )) {
			$error = new Error ( 'Could not fetch all manufacturers.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$manufacturers = $result->fetchAll ( PDO::FETCH_OBJ );
		$retManufacturers = array ();
		foreach ( $manufacturers as $manufacturer ) {
			$newManufacturer = new ManufacturerModel ( $manufacturer->Manufacturer_ID );
			if(count($this->GetProductsIn($newManufacturer)) > 0 || $empty) {
				$retManufacturers [] = $newManufacturer;
			}
		}
		if (0 == count ( $manufacturers )) {
			$retManufacturers = array ();
		}
		return $retManufacturers;
	}

	//! Gets all manufacturers that are in the supplied $catalogue
	/*!
	 * @param [in] catalogue : Obj:CatalogueModel - the catalogue to fetch the manufacturers for
	 * @return Array of Obj:Manufacturers or an exception
	 */
	function GetAllManufacturersForCatalogueList($catalogueList, $empty = true) {
		if ($empty) {
			$sql = 'SELECT Manufacturer_ID
									FROM tblManufacturer
									WHERE Catalogue_ID IN ('.$catalogueList.')
									ORDER BY Display_Name ASC
									';
		} else {
			$sql = 'SELECT Manufacturer_ID
									FROM tblManufacturer
									WHERE Catalogue_ID IN ('.$catalogueList.')
									AND Manufacturer_ID IN (SELECT Manufacturer_ID FROM tblProduct)
									ORDER BY Display_Name ASC
									';
		}
		if(!$result = $this->mDatabase->query($sql)) {
			$error = new Error('Could not fetch all manufacturers.');
			$error->PdoErrorHelper($this->mDatabase->errorInfo(), __LINE__, __FILE__ );
			throw new Exception($error->GetErrorMsg());
		}
		$manufacturers = $result->fetchAll(PDO::FETCH_OBJ);
		foreach($manufacturers as $manufacturer) {
			$newManufacturer = new ManufacturerModel ( $manufacturer->Manufacturer_ID );
			$retManufacturers [] = $newManufacturer;
		}
		if (0 == count ( $manufacturers )) {
			$retManufacturers = array ();
		}
		return $retManufacturers;
	}

	//! Gets n manufacturers that are in the supplied $catalogue
	/*!
	 * @param [in] catalogue : Obj:CatalogueModel - the catalogue to fetch the manufacturers for
	 * @param [in] n : The number of manufacturers to return
	 * @return Array of Obj:Manufacturers or an exception
	 */
	function GetNManufacturersFor($catalogue, $n) {
		$get_all_manufacturers_sql = 'SELECT Manufacturer_ID
									FROM tblManufacturer
									WHERE Catalogue_ID = ' . $catalogue->GetCatalogueId () . '
									AND Display = \'1\'
									ORDER BY Display_Name ASC
									LIMIT '.$n.'
									';
		if (! $result = $this->mDatabase->query ( $get_all_manufacturers_sql )) {
			$error = new Error ( 'Could not fetch all manufacturers.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$manufacturers = $result->fetchAll ( PDO::FETCH_OBJ );
		foreach ( $manufacturers as $manufacturer ) {
			$newManufacturer = new ManufacturerModel ( $manufacturer->Manufacturer_ID );
			$retManufacturers [] = $newManufacturer;
		}
		if (0 == count ( $manufacturers )) {
			$retManufacturers = array ();
		}
		return $retManufacturers;
	}

	//! Gets the TOP n manufacturers that are in the supplied $catalogue
	/*!
	 * @param [in] catalogue : Obj:CatalogueModel - the catalogue to fetch the manufacturers for
	 * @param [in] n : The number of manufacturers to return
	 * @return Array of Obj:Manufacturers or an exception
	 */
	function GetTopNManufacturersFor($catalogue, $n) {
		$retManufacturersIds = array();
		$get_all_manufacturers_sql = '
			SELECT
				DISTINCT tblManufacturer.Manufacturer_ID,
				COUNT(tblBasket_Skus.SKU_ID) AS OrderCount
				FROM tblManufacturer
				INNER JOIN tblProduct ON tblProduct.Manufacturer_ID = tblManufacturer.Manufacturer_ID
				INNER JOIN tblProduct_SKUs on tblProduct_SKUs.Product_ID = tblProduct.Product_ID
				INNER JOIN tblBasket_Skus ON tblBasket_Skus.SKU_ID = tblProduct_SKUs.SKU_ID
				WHERE tblManufacturer.Catalogue_ID = '.$catalogue->GetCatalogueId().'
					GROUP BY tblManufacturer.Manufacturer_ID
				ORDER BY OrderCount DESC
				LIMIT '.$n.'
									';
	#	echo $get_all_manufacturers_sql;
		if (! $result = $this->mDatabase->query ( $get_all_manufacturers_sql )) {
			$error = new Error ( 'Could not fetch top manufacturers.' .$get_all_manufacturers_sql);
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$manufacturers = $result->fetchAll ( PDO::FETCH_OBJ );
		foreach ( $manufacturers as $manufacturer ) {
			if(!in_array($manufacturer->Manufacturer_ID,$retManufacturersIds)) {
				$newManufacturer = new ManufacturerModel ( $manufacturer->Manufacturer_ID );
				$retManufacturers [] = $newManufacturer;
				$retManufacturersIds[] = $manufacturer->Manufacturer_ID;
			}
		}
		if (0 == count ( $manufacturers )) {
			$retManufacturers = array ();
		}
		return $retManufacturers;
	}


	//! Gets all of the categories with products made by $manufacturer
	/*!
	 * @param $manufacturer Obj:ManufacturerModel - The manufacturer to check
	 * @return Array of CategoryModel objects
	 */
	function GetAllCategoriesIn($manufacturer) {
		$sql = 'SELECT DISTINCT
					tblCategory.Category_ID,
					tblCategory.Display_Name
				FROM
					tblCategory
				INNER JOIN tblCategory_Products
					ON tblCategory_Products.Category_ID = tblCategory.Category_ID
				INNER JOIN tblProduct
					ON tblCategory_Products.Product_ID = tblProduct.Product_ID
				WHERE tblProduct.Manufacturer_ID = ' . $manufacturer->GetManufacturerId () . '
				ORDER BY tblCategory.Display_Name ASC';
		$result = $this->mDatabase->query ( $sql );
		$retCats = array ();
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$tempCat = new CategoryModel ( $resultObj->Category_ID );
			$retCats [] = $tempCat;
		}
		return $retCats;
	}

	//! Gets those products that are made by this manufacturer
	/*!
	 * @param $manufacturer [in] Obj:ManufacturerModel - The manufacturer to scan
	 * @param $numberOfProducts [in] : Int : The number of products to retrieve (default 1) (product per page)
	 * @param $sortBy [in] : String - Which field to sort the data by (Defaults Actual_Price)
	 * @param $sortDirection [in] : String - ASC(ending) or DESC(ending)
	 * @param $pageNumber : Which page is required - IE. From 0..X or X..Y etc.
	 * @param $category [in] : Obj : CategoryModel Optional - if present then constrains the function to the given category (and required manufacturer)
	 * @return Array of Obj:ProductModel - those products that satisfy the requirements
	 */
	function GetProductsIn($manufacturer, $numberOfProducts = 1, $sortBy = 'Actual_Price', $sortDirection = 'ASC', $pageNumber = 1, $category = false) {
		if ($sortDirection == 'ASC') {
			$opposite = 'DESC';
		} else {
			$opposite = 'ASC';
		}
		if ($sortBy == 'Display_Name') {
			$firstSortBySql = ' ';
			$secondSortBySql = ' ';
		} else {
			$firstSortBySql = ' ' . $sortBy . ' ' . $sortDirection . ', ';
			$secondSortBySql = ' ' . $sortBy . ' ' . $opposite . ', ';
		}

		$endLimit = $pageNumber * $numberOfProducts;
		$totalProducts = $this->CountProductsIn ( $manufacturer );
		if ($numberOfProducts * $pageNumber > $totalProducts) {
			$numberOfProducts = $numberOfProducts - (($numberOfProducts * $pageNumber) - $totalProducts);
		}

		if ($category) {
			$categoryConstraintSql = ' AND tblCategory_Products.Category_ID = ' . $category->GetCategoryId ();
		} else {
			$categoryConstraintSql = ' ';
		}
		$sql = '
				SELECT * FROM (
					SELECT DISTINCT * FROM (
							SELECT DISTINCT
								tblCategory_Products.Product_ID,
								tblProduct_Text.Display_Name,
								tblProduct.Actual_Price
							FROM
								tblCategory_Products
							INNER JOIN tblProduct
								ON tblProduct.Product_ID = tblCategory_Products.Product_ID
							INNER JOIN tblProduct_Text
								ON tblProduct.Product_ID = tblProduct_Text.Product_ID
							WHERE tblProduct.Manufacturer_ID = ' . $manufacturer->GetManufacturerId () . '
							' . $categoryConstraintSql . '
							AND tblProduct.Hidden = \'0\'
							ORDER BY ' . $sortBy . ' ' . $sortDirection . ' LIMIT ' . $endLimit . '
						) AS foo ORDER BY ' . $sortBy . ' ' . $opposite . ' LIMIT ' . $numberOfProducts . '
					) AS bar ORDER BY ' . $sortBy . ' ' . $sortDirection . '
					';
		#echo $sql;
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not sort products: ' . $sql . '.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$products = $result->fetchAll ( PDO::FETCH_OBJ );
		foreach ( $products as $product_id ) {
			$newProduct = new ProductModel ( $product_id->Product_ID );
			$retProducts [] = $newProduct;
		}
		if (0 == count ( $products )) {
			$retProducts = array ();
		}
		return $retProducts;
	}

	//! Returns all SKUs for the manufacturer supplied
	function GetAllSkusIn($manufacturer) {
		$retArr = array();
		$sql = '
				SELECT tblProduct_SKUs.SKU_ID from tblProduct_SKUs
				INNER JOIN tblProduct ON tblProduct.Product_ID = tblProduct_SKUs.Product_ID
				INNER JOIN tblProduct_Text ON tblProduct_Text.Product_ID = tblProduct.Product_ID
				WHERE tblProduct.Manufacturer_ID = '.$manufacturer->GetManufacturerId().'
				ORDER BY tblProduct_Text.Display_Name ASC
				';
		if(!$result = $this->mDatabase->query($sql)) {
			$error = new Error ( 'Could not get SKUs for manufacturer: ' . $sql . '.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$skusArr = $result->fetchAll(PDO::FETCH_OBJ);
		foreach($skusArr as $resultObj) {
			$sku = new SkuModel($resultObj->SKU_ID);
			$retArr[] = $sku;
		}
		return $retArr;
	}

	//! Returns the number of products that a manufacturer makes
	/*!
	 * @param $manufacturer Obj : ManufacturerModel - The manufacturer to check
	 * @param $category Obj : CategoryModel - Constrain by a category - defaults to false
	 * @return Int - The number of products they make
	 */
	function CountProductsIn($manufacturer, $category = false) {
		if ($category) {
			$categoryConstraintSql = '
			INNER JOIN tblCategory_Products ON tblCategory_Products.Product_ID = tblProduct.Product_ID
			INNER JOIN tblCategory ON tblCategory_Products.Category_ID = tblCategory.Category_ID
			WHERE tblCategory.Category_ID = ' . $category->GetCategoryId () . ' AND ';
		} else {
			$categoryConstraintSql = ' WHERE ';
		}
		$sql = 'SELECT COUNT(DISTINCT tblProduct.Product_ID) AS productCount FROM tblProduct
				' . $categoryConstraintSql . ' tblProduct.Manufacturer_ID = ' . $manufacturer->GetManufacturerId () . '
				AND tblProduct.Hidden = \'0\'';
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not count products.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$productIds = $result->fetch ( PDO::FETCH_OBJ );
		return $productIds->productCount;
	}

	//! Gets the manufacturers listed in order of success - judged by most products sold
	/*
	 * @param catalogueList 	- Str - A comma-seperated list of catalogue IDs to be used directly by the SQL - NB. No checking done, eg. 123,154,156,152
	 * @param startTimestamp	- Int - UNIX Timestamp of start date to look at
	 * @param endTimestamp		- Int - UNIX Timestamp of end date to look at
	 * @param $excludeSmallItems- Bool - Whether or not to exclude protein bars and RTDs (which inflate numbers) Defaults to false
	 * @return Array of format: $retArray['manufacturer'][], $retArray['orderValue'][] and $retArray['productCount'][]
	 */
	function GetTopBrands($catalogueList,$startTimestamp,$endTimestamp,$excludeSmallItems=false) {
		$retArr = array();
		if($excludeSmallItems) {
			$exclSql = ' AND tblCategory.Parent_Category_ID NOT IN (50680) ';
		} else {
			$exclSql = ' ';
		}
		$sql = 'SELECT
					tblManufacturer.Display_Name,
					COUNT(tblManufacturer.Display_Name) AS ProductCount,
					SUM(tblProduct.Actual_Price) AS OrderValue
				FROM tblManufacturer
					INNER JOIN tblProduct			ON tblProduct.Manufacturer_ID		= tblManufacturer.Manufacturer_ID
					INNER JOIN tblProduct_SKUs		ON tblProduct_SKUs.Product_ID		= tblProduct.Product_ID
					INNER JOIN tblBasket_Skus		ON tblBasket_Skus.SKU_ID			= tblProduct_SKUs.SKU_ID
					INNER JOIN tblOrder				ON tblBasket_Skus.Basket_ID			= tblOrder.Basket_ID
					INNER JOIN tblCategory_Products	ON tblCategory_Products.Product_ID	= tblProduct.Product_ID
					INNER JOIN tblCategory			ON tblCategory_Products.Category_ID	= tblCategory.Category_ID
					WHERE tblOrder.Status_ID = 6
					AND tblCategory.Catalogue_ID IN ('.$catalogueList.')
					'.$exclSql.'
					AND tblOrder.Created_Date BETWEEN '.$startTimestamp.' AND '.$endTimestamp.'
					GROUP BY tblManufacturer.Display_Name
					ORDER BY ProductCount DESC';
		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$productCount = $resultObj->ProductCount;
			$manufacturer = $resultObj->Display_Name;
			$orderValue   = $resultObj->OrderValue;
			$retArr['manufacturer'][] = $manufacturer;
			$retArr['productCount'][] = $productCount;
			$retArr['orderValue'][]	  = $orderValue;
		}
		return $retArr;
	} // End GetTopBrands

	//! Gets those categories that have products in the manufacturers in the newManufacturerList
	/*!
	 * @param $newManufacturerList - String - Comma separated list of manufacturers
	 * @return Array of CategoryModel objects
	 */
	function GetCategoriesInManufacturerList($newManufacturerList) {
		$retArr = array();
		$sql = '
				SELECT DISTINCT tblCategory.Parent_Category_ID
				FROM tblCategory
				INNER JOIN tblCategory_Products ON tblCategory_Products.Category_ID = tblCategory.Category_ID
				INNER JOIN tblProduct ON tblCategory_Products.Product_ID = tblProduct.Product_ID
				WHERE tblProduct.Manufacturer_ID IN ('.$newManufacturerList.')
				';
		if(!$result = $this->mDatabase->query($sql)) {
			return $retArr;
		} else {
			while($resultObj = $result->fetch(PDO::FETCH_OBJ)) {
				if(!is_null($resultObj->Parent_Category_ID)) {
					$category = new CategoryModel($resultObj->Parent_Category_ID);
					$retArr[] = $category;
				}
			}

			return $retArr;
		}
	} // End GetCategoriesInManufacturerList

} // End ManufacturerController

?>