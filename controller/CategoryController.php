<?php

//! Deals with Category tasks (create, delete etc)
class CategoryController {

	function __construct() {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
		($registry->debugMode ? $this->mFh = @fopen ( './' . $registry->debugDir . '/categoryController.txt', 'a' ) : NULL);
	}

	//! Creates a new category in the database then returns this category as an object of type CategoryModel
	/*!
	 * @return Obj:CategoryModel - the new category
	 */
	function CreateCategory($displayName, $catalogue) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$create_category_sql = 'INSERT INTO tblCategory (`Display_Name`,`Catalogue_ID`,`Package_Category`) VALUES (\'' . $displayName . '\',\'' . $catalogue->GetCatalogueId () . '\',\'0\')';
		if ($database->query ( $create_category_sql )) {
			$get_latest_category_sql = 'SELECT Category_ID FROM tblCategory ORDER BY Category_ID DESC LIMIT 1';
			if (! $result = $database->query ( $get_latest_category_sql )) {
				$error = new Error ( 'Could not select new category' );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$latest_category = $result->fetch ( PDO::FETCH_OBJ );
			$newCategory = new CategoryModel ( $latest_category->Category_ID );
			return $newCategory;
		} else {
			$error = new Error ( 'Could not insert category: ' . $create_category_sql );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

	//! Attempts to delete a category from the database, throws an exception if this fails
	/*!
	 * @return true if successful
	 * @param [in] category : Obj:CategoryModel - the category to delete
	 */
	function DeleteCategory($category, $deep = true) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$productController = new ProductController ( );
		$packageController = new PackageController ( );

		// If a package category, delete the packages in it - otherwise the products
		if ($category->GetPackageCategory ()) {
			// Delete the packages from within the category
			$sql = 'SELECT Package_ID FROM tblCategory_Packages WHERE Category_ID = ' . $category->GetCategoryId ();
			if (! $result = $database->query ( $sql )) {
				$error = new Error ( 'Could not fetch all categories.' );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultSet = $result->fetchAll ( PDO::FETCH_OBJ );
			foreach ( $resultSet as $resultObj ) {
				try {
					$package = new PackageModel ( $resultObj->Package_ID );
					$packageController->DeletePackage ( $package );
				} catch ( Exception $e ) {
					// Do nothing but log it
					fwrite ( $this->mFh, 'Package could not be deleted: ' . $package->GetDisplayName () . ' | ' );
				}
			}
		} else {
			// Delete the products from within the category
			$sql = 'SELECT Product_ID FROM tblCategory_Products WHERE Category_ID = ' . $category->GetCategoryId ();
			if (! $result = $database->query ( $sql )) {
				$error = new Error ( 'Could not fetch all categories.' );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultSet = $result->fetchAll ( PDO::FETCH_OBJ );
			foreach ( $resultSet as $resultObj ) {
				try {
					$product = new ProductModel ( $resultObj->Product_ID );
					$productController->DeleteProduct ( $product );
				} catch ( Exception $e ) {
					// Do nothing but log it
#					fwrite ( $this->mFh, 'Product could not be deleted: ' . $product->GetDisplayName () . ' | ' );
				}
			}
		} // End package/product choice


		// Delete any product links
		$delete_products_links = 'DELETE FROM tblCategory_Products WHERE Category_ID = ' . $category->GetCategoryID ();
		if (FALSE === $database->query ( $delete_products_links )) {
			$error = new Error ( 'Could not delete category-product link ' . $category->GetCategoryId () );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->getErrorMsg () );
		}

		// Delete any package links
		$delete_package_links = 'DELETE FROM tblCategory_Packages WHERE Category_ID = ' . $category->GetCategoryID ();
		if (FALSE === $database->query ( $delete_package_links )) {
			$error = new Error ( 'Could not delete category-package link ' . $category->GetCategoryId () );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->getErrorMsg () );
		}

		// Delete its children
		if ($deep) {
			$children = $this->GetAllSubCategoriesOf ( $category );
			foreach ( $children as $child ) {
				$this->DeleteCategory ( $child );
			}
		}

		// Kamikaze
		$delete_category_sql = 'DELETE FROM tblCategory WHERE Category_ID = ' . $category->GetCategoryId ();
		if (FALSE === $database->query ( $delete_category_sql )) {
			$error = new Error ( 'Could not delete category ' . $category->GetCategoryId () );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->getErrorMsg () );
		}
		return true;
	}

	//! Retrieves all the top level categories (those without parents) for a catalogue, in ASCending order
	/*!
	 * @param [in] catalogue : Obj:CatalogueModel - the catalogue to fetch the categories from
	 * @return Array of Obj:CategoryModel (possibly empty) or an exception may be thrown
	 */
	function GetAllTopLevelCategoriesForCatalogue($catalogue) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$get_all_categories_sql = 'SELECT Category_ID
									FROM tblCategory
									WHERE Catalogue_ID = ' . $catalogue->GetCatalogueId () . '
									AND Parent_Category_ID IS NULL
									AND Package_Category = 0
									ORDER BY Display_Name ASC
									';
		if (! $result = $database->query ( $get_all_categories_sql )) {
			$error = new Error ( 'Could not fetch all categories.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$categories = $result->fetchAll ( PDO::FETCH_OBJ );
		foreach ( $categories as $category ) {
			$newCategory = new CategoryModel ( $category->Category_ID );
			$retCategories [] = $newCategory;
		}
		if (0 == count ( $categories )) {
			$retCategories = array ();
		}
		return $retCategories;
	}

	//! Retrieves all the top level categories (those without parents) for a catalogue list, in ASCending order
	/*!
	 * @param [in] catalogueList : String - the catalogues to fetch the categories from - comma seperated
	 * @return Array of Obj:CategoryModel (possibly empty) or an exception may be thrown
	 */
	function GetAllTopLevelCategoriesForCatalogueList($catalogueList) {
		$sql = 'SELECT Category_ID
									FROM tblCategory
									WHERE Catalogue_ID IN ('.$catalogueList.')
									AND Category_ID NOT IN
										(
										SELECT Category_ID FROM tblCategory WHERE
										Parent_Category_ID != NULL
										)
									AND Package_Category = 0
									ORDER BY Display_Name ASC
									';
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not fetch all categories.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$categories = $result->fetchAll ( PDO::FETCH_OBJ );
		foreach ( $categories as $category ) {
			$newCategory = new CategoryModel ( $category->Category_ID );
			$retCategories [] = $newCategory;
		}
		if (0 == count ( $categories )) {
			$retCategories = array ();
		}
		return $retCategories;
	}

	//! Given a catalogue, returns all the sub level categories for it - IE those with parents (Used in generating the XML sitemap)
	/*!
	 * @param $catalogue - Obj:CatalogueModel - The catalogue to look at
	 * @return Array of Obj:CategoryModel - Those categories in the catalogue with parents
	 */
	function GetAllSubLevelCategoriesForCatalogue($catalogue) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$get_all_categories_sql = 'SELECT Category_ID
									FROM tblCategory
									WHERE Catalogue_ID = ' . $catalogue->GetCatalogueId () . '
									AND Category_ID IS NOT NULL
									AND Package_Category = 0
									ORDER BY Display_Name ASC
									';
		if (! $result = $database->query ( $get_all_categories_sql )) {
			$error = new Error ( 'Could not fetch all categories.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$categories = $result->fetchAll ( PDO::FETCH_OBJ );
		foreach ( $categories as $category ) {
			$newCategory = new CategoryModel ( $category->Category_ID );
			$retCategories [] = $newCategory;
		}
		if (0 == count ( $categories )) {
			$retCategories = array ();
		}
		return $retCategories;
	}

	//! Retrieves all the top level package categories (those without parents) for a catalogue, in ASCending order
	/*!
	 * @param [in] catalogue : Obj:CatalogueModel - the catalogue to fetch the package categories from
	 * @return Array of Obj:CategoryModel (possibly empty) or an exception may be thrown
	 */
	function GetAllTopLevelPackageCategoriesForCatalogue($catalogue) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$get_all_categories_sql = 'SELECT Category_ID
									FROM tblCategory
									WHERE Catalogue_ID = ' . $catalogue->GetCatalogueId () . '
									AND Package_Category = 1
									AND Category_ID NOT IN
										(
										SELECT Packages_Category FROM tblCatalogue
										)
									ORDER BY Display_Name ASC
									';
		if (! $result = $database->query ( $get_all_categories_sql )) {
			$error = new Error ( 'Could not fetch all categories.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$categories = $result->fetchAll ( PDO::FETCH_OBJ );
		foreach ( $categories as $category ) {
			$newCategory = new CategoryModel ( $category->Category_ID );
			$retCategories [] = $newCategory;
		}
		if (0 == count ( $categories )) {
			$retCategories = array ();
		}
		return $retCategories;
	}

	//! Retrieves all the sub categories of a given parent category
	/*!
	 * @param [in] category : Obj:CategoryModel - the category to fetch the subcategories of
	 * @return Array of Obj:CategoryModel (possibly empty) or an exception may be thrown
	 */
	function GetAllSubCategoriesOf($category,$includeHidden=false,$includeEmpty=false) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$get_all_subCategories_sql = 'SELECT Category_ID
									FROM tblCategory
									WHERE Parent_Category_ID = ' . $category->GetCategoryId () . '
									ORDER BY Display_Name ASC
									';
		if (! $result = $database->query ( $get_all_subCategories_sql )) {
			$error = new Error ( 'Could not fetch all sub categories.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$categories = $result->fetchAll ( PDO::FETCH_OBJ );
		$retCategories = array();
		foreach ( $categories as $resultObj ) {
			$newCategory = new CategoryModel ( $resultObj->Category_ID );
			if(($this->CountProductsIn($newCategory,$includeHidden) > 0 || $category->GetPackageCategory()) || $includeEmpty) {
				$retCategories [] = $newCategory;
			}
		}
		if (0 == count ( $categories )) {
			$retCategories = array ();
		}
		return $retCategories;
	}

	//! Gets those products that are in the given category, or in subcategories of that category
	/*!
	 * @param $category [in] Obj:CategoryModel - The category to scan
	 * @param $numberOfProducts [in] : Int : The number of products to retrieve (default 1) (product per page)
	 * @param $sortBy [in] : String - Which field to sort the data by
	 * @param $sortDirection [in] : String - ASC(ending) or DESC(ending)
	 * @param $pageNumber : Which page is required - IE. From 0..X or X..Y etc.
	 * @return Array of Obj:ProductModel - those products that satisfy the requirements
	 */
	function GetTopLevelCategoryProducts($category, $numberOfProducts = 1, $sortBy = 'Actual_Price', $sortDirection = 'ASC', $pageNumber = 1) {
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
		$totalProducts = $this->CountProductsIn ( $category );
		if ($numberOfProducts * $pageNumber > $totalProducts) {
			$numberOfProducts = $numberOfProducts - (($numberOfProducts * $pageNumber) - $totalProducts);
		}
		$sql = '
				SELECT * FROM (
					SELECT  * FROM (
							SELECT
								tblCategory_Products.Product_ID,
								tblProduct_Text.Display_Name,
								tblProduct.Actual_Price
							FROM
								tblCategory_Products
							INNER JOIN tblProduct
								ON tblProduct.Product_ID = tblCategory_Products.Product_ID
							INNER JOIN tblProduct_Text
								ON tblProduct.Product_ID = tblProduct_Text.Product_ID
							WHERE
							(
								tblCategory_Products.Category_ID = ' . $category->GetCategoryId () . '
							OR
								tblCategory_Products.Category_ID IN
								(SELECT tblCategory.Category_ID FROM tblCategory WHERE tblCategory.Parent_Category_ID = ' . $category->GetCategoryId () . ')
							)
							AND tblProduct.Hidden = \'0\'
							ORDER BY ' . $firstSortBySql . ' tblProduct_Text.Display_Name ' . $sortDirection . '
							LIMIT '.$endLimit.'
						) AS foo ORDER BY ' . $secondSortBySql . ' Display_Name ' . $opposite . ' LIMIT '.$numberOfProducts.'
					) AS bar ORDER BY ' . $firstSortBySql . ' Display_Name ' . $sortDirection . '
					';
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

	//! Gets those packages that have been (dubiously) placed in a top level category
	/*!
	 * @param $category [in] Obj:CategoryModel - The category to scan
	 * @param $numberOfPackages [in] : Int : The number of packages to retrieve (default 1) (product per page)
	 * @param $sortBy [in] : String - Which field to sort the data by
	 * @param $sortDirection [in] : String - ASC(ending) or DESC(ending)
	 * @param $pageNumber : Which page is required - IE. From 0..X or X..Y etc.
	 * @return Array of Obj:PackageModel - those packages that satisfy the requirements
	 */
	function GetTopLevelCategoryPackages($category, $numberOfPackages = 1, $sortBy = 'Actual_Price', $sortDirection = 'ASC', $pageNumber = 1) {
		if ($sortDirection == 'ASC') {
			$opposite = 'DESC';
		} else {
			$opposite = 'ASC';
		}
		$endLimit = $pageNumber * $numberOfPackages;
		$totalPackages = $this->CountPackagesIn ( $category );
		if ($numberOfPackages * $pageNumber > $totalPackages) {
			$numberOfPackages = $numberOfPackages - (($numberOfPackages * $pageNumber) - $totalPackages);
		}
		$sql = '
				SELECT * FROM (
					SELECT  * FROM (
							SELECT
								tblCategory_Packages.Package_ID,
								tblPackage.Display_Name,
								tblPackage.Actual_Price
							FROM
								tblCategory_Packages
							INNER JOIN tblPackage
								ON tblPackage.Package_ID = tblCategory_Packages.Package_ID
							WHERE tblCategory_Packages.Category_ID = ' . $category->GetCategoryId () . '
							OR
							tblCategory_Packages.Category_ID IN
								(SELECT tblCategory.Category_ID FROM tblCategory WHERE tblCategory.Parent_Category_ID = ' . $category->GetCategoryId () . ')
							ORDER BY ' . $sortBy . ' ' . $sortDirection . ' LIMIT ' . $endLimit . '
						) AS foo ORDER BY ' . $sortBy . ' ' . $opposite . ' LIMIT ' . $numberOfPackages . '
					) AS bar ORDER BY ' . $sortBy . ' ' . $sortDirection . '
					';
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not sort packages: ' . $sql . '.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception($error->GetErrorMsg());
		}
		$packages = $result->fetchAll ( PDO::FETCH_OBJ );
		foreach ( $packages as $package_id ) {
			$newPackage = new PackageModel ( $package_id->Package_ID );
			$retPackages [] = $newPackage;
		}
		if (0 == count ( $packages )) {
			$retPackages = array ();
		}
		return $retPackages;
	}

	//! Gets the number of packages in a given category
	/*!
	 * @param $category [in] : Obj:CategoryModel - The category to scan
	 * @return Int - the number of packages in the category
	 */
	function CountPackagesIn($category) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$sql = 'SELECT COUNT(Package_ID) AS packageCount FROM tblCategory_Packages
				WHERE tblCategory_Packages.Category_ID = ' . $category->GetCategoryId () . '
				OR
				tblCategory_Packages.Category_ID IN
				(SELECT Category_ID FROM tblCategory WHERE Parent_Category_ID = ' . $category->GetCategoryId () . ')
				';
		if (! $result = $database->query ( $sql )) {
			$error = new Error ( 'Could not count products.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$packageIds = $result->fetch ( PDO::FETCH_OBJ );
		return $packageIds->packageCount;
	}

	//! Gets the number of products in a given category
	/*!
	 * @param $category [in] : Obj:CategoryModel - The category to scan
	 * @return Int - the number of products in the category
	 */
	function CountProductsIn($category,$includeHidden=false) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		if($includeHidden) {
			$andClause = '';
		} else {
			$andClause = ' AND tblProduct.Hidden = \'0\' ';
		}
		$sql = 'SELECT COUNT(tblCategory_Products.Product_ID) AS productCount
				FROM tblCategory_Products
				INNER JOIN tblProduct ON tblProduct.Product_ID = tblCategory_Products.Product_ID
				WHERE
				(
					tblCategory_Products.Category_ID = ' . $category->GetCategoryId () . '
						OR
					tblCategory_Products.Category_ID IN
					(SELECT Category_ID FROM tblCategory WHERE Parent_Category_ID = ' . $category->GetCategoryId () . ')
				)
				'.$andClause.'
				';
		if (! $result = $database->query ( $sql )) {
			$error = new Error ( 'Could not count products.' . $sql);
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$productIds = $result->fetch ( PDO::FETCH_OBJ );
		return $productIds->productCount;
	}

	//! Retrieves all the products of a given category
	/*!
	 * @param [in] category : Obj:CategoryModel - the category to fetch the products of
	 * @return Array of Obj:ProductModel (possibly empty) or an exception may be thrown
	 */
	function GetAllProductsIn($category) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$get_all_products_sql = 'SELECT tblProduct_Text.Product_ID, tblProduct_Text.Display_Name
									FROM tblCategory_Products
									INNER JOIN tblProduct_Text
										ON tblCategory_Products.Product_ID = tblProduct_Text.Product_ID
									WHERE Category_ID = ' . $category->GetCategoryId () . '
									ORDER BY tblProduct_Text.Display_Name ASC';
		if (! $result = $database->query ( $get_all_products_sql )) {
			$error = new Error ( 'Could not fetch all products.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
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

	//! Retrieves all the packages of a given category
	/*!
	 * @param [in] category : Obj:CategoryModel - the category to fetch the products of
	 * @return Array of Obj:PackageModel (possibly empty) or an exception may be thrown
	 */
	function GetAllPackagesIn($category) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$get_all_packages_sql = 'SELECT tblPackage.Package_ID, tblPackage.Display_Name
									FROM tblCategory_Packages
									INNER JOIN tblPackage
										ON tblPackage.Package_ID = tblCategory_Packages.Package_ID
									WHERE Category_ID = ' . $category->GetCategoryId () . '
									ORDER BY tblPackage.Display_Name ASC';
		if (! $result = $database->query ( $get_all_packages_sql )) {
			$error = new Error ( 'Could not fetch all packages.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$packages = $result->fetchAll ( PDO::FETCH_OBJ );
		foreach ( $packages as $package_id ) {
			$newPackage = new PackageModel ( $package_id->Package_ID );
			$retPackages [] = $newPackage;
		}
		if (0 == count ( $packages )) {
			$retPackages = array ();
		}
		return $retPackages;
	}

	function GetAProductIn($category) {
		$sql = 'SELECT Product_ID FROM tblCategory_Products WHERE Category_ID = ' . $category->GetCategoryId ().' LIMIT 1';
		$result = $this->mDatabase->query ( $sql );
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		return new ProductModel ( $resultObj->Product_ID );
	}

	function GetEmptyCategories($catalogueList) {
		$retArr = array ();
		$sql = 'SELECT DISTINCT tblCategory.Category_ID FROM tblCategory
					WHERE tblCategory.Category_ID NOT IN
					(SELECT DISTINCT Category_ID FROM tblCategory_Products)
					AND tblCategory.Catalogue_ID IN (' . $catalogueList . ')
					AND tblCategory.Package_Category = 0
					AND tblCategory.Parent_Category_ID IS NOT NULL';
		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$category = new CategoryModel ( $resultObj->Category_ID );
			$retArr [] = $category;
		}
		return $retArr;
	}

	//! Gets those manufacturers that have products in the categories in the categoryList
	/*!
	 * @param $newCategoryList - String - Comma separated list of categories
	 * @return Array of Manufacturer objects
	 */
	function GetManufacturersInCategoryList($newCategoryList) {
		$retArr = array();
		$sql = '
				SELECT DISTINCT tblProduct.Manufacturer_ID
				FROM tblProduct
				INNER JOIN tblCategory_Products ON tblCategory_Products.Product_ID = tblProduct.Product_ID
				WHERE tblCategory_Products.Category_ID IN 	(
																SELECT tblCategory.Category_ID FROM tblCategory WHERE tblCategory.Parent_Category_ID = '.$newCategoryList.'
															)
				';
		if(!$result = $this->mDatabase->query($sql)) {
			return $retArr;
		} else {
			while($resultObj = $result->fetch(PDO::FETCH_OBJ)) {
				$manufacturer = new ManufacturerModel($resultObj->Manufacturer_ID);
				$retArr[] = $manufacturer;
			}
			return $retArr;
		}
	}

} // End CategoryController

?>