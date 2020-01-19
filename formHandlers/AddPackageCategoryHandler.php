<?php
require_once ('../autoload.php');

//! Adds a top level category to the database
class AddPackageCategoryHandler {
	
	//! String : Display name of the category to add
	var $mDisplayName;
	//! Obj:CatalogueModel : The catalogue the category will belong to
	var $mCatalogue;
	
	//! Expects the catalogue ID, display name and description
	/*
	 * @param [in] catalogueId : The ID of the catalogue that the category is in
	 * @param [in] displayName : The display name of the category
	 * @return true if successful
	 */
	function ValidateInput($catalogueId, $displayName) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		// Check the catalogue exists 
		$sql = 'SELECT Catalogue_ID FROM tblCatalogue WHERE Catalogue_ID = ' . $catalogueId;
		if (0 === $database->query ( $sql )) {
			$error = new Error ( 'Validation of new top level category failed because the catalogue ID doesn\'t exist.' );
			throw new Exception ( $error->GetErrorMsg () );
		} else {
			$this->mCatalogue = new CatalogueModel ( $catalogueId );
		}
		// Remove HTML
		$this->mDisplayName = strip_tags ( $displayName );
		// Trim
		$this->mDisplayName = trim ( $displayName );
		// Make any (MS)SQL Injection Attack attempts safe
		$this->mDisplayName = str_replace ( "'", "''", $this->mDisplayName );
		return true;
	}
	
	//! Insert the category into the database
	/* 
	 * @return true if successful
	 */
	function InsertCategory() {
		$categoryController = new CategoryController ( );
		$packagesCategory = $this->mCatalogue->GetPackagesCategory ();
		$newCategory = $categoryController->CreateCategory ( $this->mDisplayName, $this->mCatalogue );
		$newCategory->SetPackageCategory ( 1 );
		$newCategory->SetParentCategory ( $packagesCategory );
		return true;
	}

}

try {
	$handler = new AddPackageCategoryHandler ( );
	$handler->ValidateInput ( $_POST ['catalogueId'], $_POST ['displayName'] );
	$handler->InsertCategory ();
	?>
<script language="javascript" type="text/javascript">
		self.parent.window.frames["productMenu"].location.reload();
	</script>
<?php
} catch ( Exception $e ) {
	echo $e->GetMessage ();
}

?>