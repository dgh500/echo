<?php

//! Deals with tax code tasks (create, delete etc)
class PackageController {

	function __construct() {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
	}

	//! Creates a new package
	/*!
	 * @param [in] displayName 	: String 				- The name of the package
	 * @param [in] catalogue   	: Obj:CatalogueModel 	- The catalogue the package will be put in
	 * @param [in] category		: Obj:CategoryModel		- The category the package will be put in
	 * @return Obj:PackageModel - The new package. Throws an exception otherwise
	 */
	function CreatePackage($displayName, $catalogue, $category) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$create_package_sql = 'INSERT INTO tblPackage (`Display_Name`,`Catalogue_ID`,`Was_Price`,`Actual_Price`,`Postage`,`Offer_Of_Week`) VALUES (\'' . $displayName . '\',\'' . $catalogue->GetCatalogueId () . '\',\'0\',\'0\',\'0\',\'0\')';
		if ($database->query ( $create_package_sql )) {
			$get_latest_package_sql = 'SELECT Package_ID FROM tblPackage ORDER BY Package_ID DESC LIMIT 1';
			if (! $result = $database->query ( $get_latest_package_sql )) {
				$error = new Error ( 'Could not select new package.' );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$latest_package = $result->fetch ( PDO::FETCH_OBJ );
			$newPackage = new PackageModel ( $latest_package->Package_ID );

			$link_up_package_sql = 'INSERT INTO tblCategory_Packages (`Category_ID`,`Package_ID`) VALUES (\'' . $category->GetCategoryId () . '\',\'' . $newPackage->GetPackageId () . '\')';
			if ($database->query ( $link_up_package_sql )) {
				return $newPackage;
			} else {
				$error = new Error ( 'Could not link new package to its category.' );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		} else {
			$error = new Error ( 'Could not insert package' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

	//! Deletes the package supplied
	/*!
	 * @param [in] package : Obj:PackageModel
	 * @return Boolean true if successful, exception otherwise
	 */
	function DeletePackage($package) {
		$registry = Registry::getInstance ();
		$database = $registry->database;

		//! Remove all links
		$delete_package_sql [] = 'DELETE FROM tblPackage_Products WHERE Package_ID = ' . $package->GetPackageId ();
		$delete_package_sql [] = 'DELETE FROM tblCategory_Packages WHERE Package_ID = ' . $package->GetPackageId ();

		//! Remove package
		$delete_package_sql [] = 'DELETE FROM tblPackage WHERE Package_ID = ' . $package->GetPackageId ();

		foreach ( $delete_package_sql as $sql ) {
			//! This is like this because PDO::Exec returns the number of ROWS affected - if this is zero it would equate to FALSE if normal comparison (==) was used incorrectly
			if (FALSE === $database->query ( $sql )) {
				$error = new Error ( 'Problem deleting package ' . $package->GetPackageId () . ' with SQL:<br /> ' . $sql );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->getErrorMsg () );
			}
		}
		return true;
	}

	//! Given say $category=accessories and $product=din cap, will return $category=accessories tank
	function GetLinkCategory($package, $category) {
		$sql = 'SELECT
					tblCategory_Packages.Category_ID
				FROM
					tblCategory_Packages
				INNER JOIN tblCategory
					ON tblCategory.Category_ID = tblCategory_Packages.Category_ID
				WHERE
					tblCategory_Packages.Package_ID = ' . $package->GetPackageId () . '
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
	* @return Array of PackageModel objects, empty if none
	*/
	function GetOffersOfTheWeek($catalogue, $numberOfOffers = 0) {
		$this->mOffersOfTheWeek = array ();
		if (! isset ( $this->mOffersOfTheWeek ) || 0 == count ( $this->mOffersOfTheWeek )) {
			$registry = Registry::getInstance ();
			$database = $registry->database;
			$sql = 'SELECT Package_ID FROM tblPackage WHERE Offer_Of_Week = \'1\' AND Package_ID IN (SELECT Package_ID FROM tblCategory_Packages) ORDER BY RAND()';
			if (! $result = $database->query ( $sql )) {
				$error = new Error ( 'Could not fetch the offers of the week' );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$packages = $result->fetchAll ( PDO::FETCH_OBJ );
			if ($numberOfOffers == 0) {
				$numberOfOffers = count ( $packages );
			}
			// For each SKU, create a new instance of it and store it in the mSkus member variable
			foreach ( $packages as $resultObj ) {
				$newPack = new PackageModel ( $resultObj->Package_ID );
				if ($catalogue->GetCatalogueId () == $newPack->GetCatalogue ()->GetCatalogueId () && count ( $this->mOffersOfTheWeek ) < $numberOfOffers) {
					$this->mOffersOfTheWeek [] = $newPack;
				}
			}
			if (0 == count ( $packages )) {
				$this->mOffersOfTheWeek = array ();
			}
		}
		return $this->mOffersOfTheWeek;
	}

	//! Gets the stacks listed in order of success - judged by most stacks sold
	/*
	 * @param catalogueList 	- Str - A comma-seperated list of catalogue IDs to be used directly by the SQL - NB. No checking done, eg. 123,154,156,152
	 * @param startTimestamp	- Int - UNIX Timestamp of start date to look at
	 * @param endTimestamp		- Int - UNIX Timestamp of end date to look at
	 * @return Array of format: $retArray['packageId'][], $retArray['saleCount'][]
	 */
	function GetTopPackages($catalogueList,$startTimestamp,$endTimestamp) {
		$retArr = array();
		$sql = 'SELECT
					tblPackage.Package_ID,
					COUNT(tblPackage.Package_ID) AS SaleCount
				FROM
					tblPackage
					INNER JOIN tblBasket_Packages	ON tblBasket_Packages.Package_ID = tblPackage.Package_ID
					INNER JOIN tblOrder				ON tblOrder.Basket_ID = tblBasket_Packages.Basket_ID
					INNER JOIN tblCategory_Packages	ON tblCategory_Packages.Package_ID = tblPackage.Package_ID
					INNER JOIN tblCategory			ON tblCategory_Packages.Category_ID = tblCategory.Category_ID
				WHERE
					tblOrder.Status_ID = 3
					AND tblCategory.Catalogue_ID IN ('.$catalogueList.')
					AND tblOrder.Created_Date BETWEEN '.$startTimestamp.' AND '.$endTimestamp.'
				GROUP BY tblPackage.Package_ID
				ORDER BY SaleCount DESC';
		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$saleCount = $resultObj->SaleCount;
			$packageId = $resultObj->Package_ID;
			$retArr['packageId'][] = $packageId;
			$retArr['saleCount'][] = $saleCount;
		}
		return $retArr;
	}

	//! Gets the package that has sold the most in the category
	function GetBestSellingPackageForCategory($category) {
		$sql = '
				SELECT DISTINCT
					tblPackage.Package_ID,
					COUNT(tblPackage.Package_ID) AS PackageCount,
					tblPackage.Display_Name
				FROM tblPackage
					INNER JOIN tblBasket_Packages ON tblPackage.Package_ID = tblBasket_Packages.Package_ID
					INNER JOIN tblCategory_Packages ON tblCategory_Packages.Package_ID = tblPackage.Package_ID
					INNER JOIN tblCategory ON tblCategory.Category_ID = tblCategory_Packages.Category_ID
				WHERE  tblCategory.Package_Category = \'1\'
					AND (tblCategory.Category_ID = '.$category->GetCategoryId().' OR tblCategory.Parent_Category_ID = '.$category->GetCategoryId().')
				GROUP BY tblPackage.Package_ID, tblPackage.Display_Name
				ORDER BY PackageCount DESC
				LIMIT 1
				';
		// Package Result
		$result = $this->mDatabase->query($sql);
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		if($resultObj) {
			$package = new PackageModel($resultObj->Package_ID);
		} else {
			$package = $this->GetAnyPackageInCategory($category);
		}
		return $package;
	} // End GetBestSellingPackageForCategory

	//! Returns ANY package, for when you just need to display one!
	function GetAnyPackage() {
		$sql = '
				SELECT DISTINCT
					tblPackage.Package_ID
				FROM tblPackage
				LIMIT 1
				';
		// Package Result
		$result = $this->mDatabase->query($sql);
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		$package = new PackageModel($resultObj->Package_ID);
		return $package;
	}

	//! Get all packages, in any order (for feeds)
	function GetAllPackages() {
		$retArr = array();
		$sql = '
				SELECT DISTINCT
					tblPackage.Package_ID
				FROM tblPackage
				';
		$result = $this->mDatabase->query($sql);
		while($resultObj = $result->fetch(PDO::FETCH_OBJ)) {
			if($resultObj) {
				$retArr[] = new PackageModel($resultObj->Package_ID);
			}
		}
 		if(count($retArr) == 0) {
			$retArr[] = $this->GetAnyPackage();
		}
		return $retArr;
	}

	//! Gets the packages that has sold the most
	/*!
	 * @param $num - Int - The number of packages to select
	 */
	function GetBestSellingPackages($num) {
		$retArr = array();
		$sql = '
				SELECT DISTINCT
					tblPackage.Package_ID,
					COUNT(tblPackage.Package_ID) AS PackageCount,
					tblPackage.Display_Name
				FROM tblPackage
					LEFT JOIN tblBasket_Packages ON tblPackage.Package_ID = tblBasket_Packages.Package_ID
					INNER JOIN tblCategory_Packages ON tblCategory_Packages.Package_ID = tblPackage.Package_ID
					INNER JOIN tblCategory ON tblCategory.Category_ID = tblCategory_Packages.Category_ID
				WHERE  tblCategory.Package_Category = \'1\'
				GROUP BY tblPackage.Package_ID, tblPackage.Display_Name
				ORDER BY PackageCount DESC
				LIMIT '.$num.'
				';
		// Package Result
		$result = $this->mDatabase->query($sql);
		while($resultObj = $result->fetch(PDO::FETCH_OBJ)) {
			if($resultObj) {
				$retArr[] = new PackageModel($resultObj->Package_ID);
			}
		}
 		if(count($retArr) == 0) {
			$retArr[] = $this->GetAnyPackage();
		}
		return $retArr;
	} // End GetBestSellingPackages

	//! Gets the newest package in the category
	function GetBrandNewPackageForCategory($category) {
		$sql = '
				SELECT DISTINCT
					tblPackage.Package_ID
				FROM tblPackage
					INNER JOIN tblCategory_Packages ON tblCategory_Packages.Package_ID = tblPackage.Package_ID
					INNER JOIN tblCategory ON tblCategory.Category_ID = tblCategory_Packages.Category_ID
				WHERE (tblCategory.Category_ID = '.$category->GetCategoryId().' OR tblCategory.Parent_Category_ID = '.$category->GetCategoryId().')
					AND tblCategory.Package_Category = \'1\'
				ORDER BY Package_ID DESC
				LIMIT 1
				';
		// Package Result
		$result = $this->mDatabase->query($sql);
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		if($resultObj) {
			$package = new PackageModel($resultObj->Package_ID);
		} else {
			$package = $this->GetAnyPackageInCategory($category);
		}
		return $package;
	} // End GetBrandNewPackageForCategory

	//! Gets ANY package in the category - this is a last resort!
	function GetAnyPackageInCategory($category) {
		$sql = '
				SELECT DISTINCT
					tblPackage.Package_ID
				FROM tblPackage
				INNER JOIN tblCategory_Packages ON tblCategory_Packages.Package_ID = tblPackage.Package_ID
				INNER JOIN tblCategory ON tblCategory.Category_ID = tblCategory_Packages.Category_ID
				WHERE (tblCategory.Category_ID = '.$category->GetCategoryId().' OR tblCategory.Parent_Category_ID = '.$category->GetCategoryId().')
				LIMIT 1
				';
		// Product Result
		$result = $this->mDatabase->query($sql);
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		$package = new PackageModel($resultObj->Package_ID);
		return $package;
	} // End GetBestSellingPackageForCategory

} // End PackageController

?>