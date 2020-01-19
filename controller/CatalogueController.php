<?php

//! Deals with Catalogue tasks (create, delete etc)
class CatalogueController {

	//! Constructor, initialises the database connection
	function __construct() {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
	}

	//! Creates a new catalogue in the database then returns this catalogue as an object of type CatalogueModel
	/*!
	 * @param [in] $displayName - String - The name of the new catalogue
	 * @param [in] $packages - Boolean, whether or not packages should be enabled, optional
	 * @return Obj:CatalogueModel - the new catalogue
	 */
	function CreateCatalogue($displayName, $packages = false) {
		// Initialise category & pricing model controllers
		$categoryController = new CategoryController ( );
		$pricingModelController = new PricingModelController ( );

		// Make sure the packages argument doesn't break anything; defaults to false if cant recognise a standard boolean equivalent
		if (! is_bool ( $packages )) {
			switch ($packages) {
				case 1 :
				case '1' :
					$packages = true;
					break;
				case 0 :
				case '0' :
					$packages = false;
					break;
				default :
					$packages = false;
					break;
			}
		}

		// Create the catalogue SQL
		$sql = 'INSERT INTO tblCatalogue
					(`Display_Name`,`Packages`,`Pricing_Model_ID`)
				VALUES
					(\'' . $displayName . '\',\'' . $packages . '\',\'' . $pricingModelController->GetDefaultPricingModel ()->GetPricingModelId () . '\')';
		// Can use PDO::excc here because if it affects 0 rows then a failure HAS happened
		if ($this->mDatabase->query ( $sql )) {
			$get_latest_catalogue_sql = 'SELECT Catalogue_ID FROM tblCatalogue ORDER BY Catalogue_ID DESC LIMIT 1';
			if (! $result = $this->mDatabase->query ( $get_latest_catalogue_sql )) {
				$error = new Error ( 'Could not select new catalogue' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			// Use the newest catalogue as the current one
			$latest_catalogue = $result->fetch ( PDO::FETCH_OBJ );
			$newCatalogue = new CatalogueModel ( $latest_catalogue->Catalogue_ID );
		} else {
			$error = new Error ( 'Could not insert catalogue' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}

		// Create default catalogue settings
		$sql = 'INSERT INTO tblSystem_Settings (`Catalogue_ID`) VALUES (\'' . $newCatalogue->GetCatalogueId () . '\')';
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not create system settings: ' . $sql . '.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}

		// Add some long text for the front page and index title
		$sql = 'INSERT INTO tblCatalogue_Text
					(`Catalogue_ID`,`Long_Description`,`Index_Title`)
				VALUES
					(\'' . $newCatalogue->GetCatalogueId () . '\',\'' . $displayName . '\',\'' . $displayName . '\')';
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not add catalogue text: ' . $sql . '.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}

		// Create category to be the 'packages' category, so that enabling it later doesn't break anything
		$packagesCategory = $categoryController->CreateCategory ( 'Packages', $newCatalogue );
		$packagesCategory->SetPackageCategory ( 1 );

		// Link up the new catalogue to the packages category
		$newCatalogue->SetPackagesCategory ( $packagesCategory );
		return $newCatalogue;
	}

	//! Attempts to delete a catalogue from the database, throws an exception if this fails
	/*!
	 * @return true if successful
	 * @param [in] catalogue : Obj:CatalogueModel - the catalogue to delete
	 */
	function DeleteCatalogue($catalogue) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$sql = 'DELETE FROM tblManufacturer WHERE Catalogue_ID = ' . $catalogue->GetCatalogueId ();
		$database->query ( $sql );
		$delete_catalogue_sql = 'DELETE FROM tblCatalogue WHERE Catalogue_ID = ' . $catalogue->GetCatalogueId ();
		$database->query ( $delete_catalogue_sql );
		$delete_catalogue_sql = 'DELETE FROM tblSystem_Settings WHERE Catalogue_ID = ' . $catalogue->GetCatalogueId ();
		$database->query ( $delete_catalogue_sql );
		return true;
	}

	//! Gets all catalogues in the database and returns them in an array
	/*!
	 * @return Array of Obj:CatalogueModel
	 */
	function GetAllCatalogues() {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$get_all_catalogues_sql = 'SELECT Catalogue_ID FROM tblCatalogue ORDER BY Display_Name ASC';
		if (! $result = $database->query ( $get_all_catalogues_sql )) {
			$error = new Error ( 'Could not fetch all catalogues.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$catalogues = $result->fetchAll ( PDO::FETCH_OBJ );
		foreach ( $catalogues as $catalogue ) {
			$newCatalogue = new CatalogueModel ( $catalogue->Catalogue_ID );
			$retCatalogues [] = $newCatalogue;
		}
		if (0 == count ( $retCatalogues )) {
			$retCatalogues = array ();
		}
		return $retCatalogues;
	}

	//! Gets the first catalogue that was entered in the database, to be used when a default is needed
	/*!
	* @return Obj:CatalogueModel
	 */
	function GetFirstCatalogue() {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$get_first_catalogue_sql = 'SELECT Catalogue_ID FROM tblCatalogue WHERE Display_Name LIKE \'Echo Supplements\' LIMIT 1';
		if (! $result = $database->query ( $get_first_catalogue_sql )) {
			$error = new Error ( 'Could not fetch first catalogues.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$catalogue_id = $result->fetch ( PDO::FETCH_OBJ );
		$newCatalogue = new CatalogueModel ( $catalogue_id->Catalogue_ID );
		return $newCatalogue;
	}

}

/* DEBUG SECTION
try {
	$catCont = new CatalogueController();
	$toDel = $catCont->CreateCatalogue('Test Catalogue');
	$catCont->DeleteCatalogue($toDel);
} catch(Exception $e) {
	echo $e->GetMessage();
}*/

?>