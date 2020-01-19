<?php
require_once ('../autoload.php');

//! Adds a package to the database
class AddPackageHandler {
	
	//! String : Display name of the package to add
	var $mDisplayName;
	//! Obj:CatalogueModel : The catalogue the package will belong to
	var $mCatalogue;
	//! Obj:CategoryModel : The category the package will be in
	var $mCategory;
	
	//! Expects the catalogue ID, display name and description
	/*
	 * @param [in] catalogueId : The ID of the catalogue that the category is in
	 * @param [in] displayName : The display name of the category
	 * @return true if successful
	 */
	function ValidateInput($catalogueId, $displayName, $categoryId) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		// Check the catalogue exists 
		$sql = 'SELECT Catalogue_ID FROM tblCatalogue WHERE Catalogue_ID = ' . $catalogueId;
		if (0 === $database->query ( $sql )) {
			$error = new Error ( 'Validation of new package failed because the catalogue ID doesn\'t exist.' );
			throw new Exception ( $error->GetErrorMsg () );
		} else {
			$this->mCatalogue = new CatalogueModel ( $catalogueId );
		}
		// Check the category exists 
		$sql = 'SELECT Category_ID FROM tblCategory WHERE Category_ID = ' . $categoryId;
		if (0 === $database->query ( $sql )) {
			$error = new Error ( 'Validation of new package failed because the category ID doesn\'t exist.' );
			throw new Exception ( $error->GetErrorMsg () );
		} else {
			$this->mCategory = new CategoryModel ( $categoryId );
		}
		// Remove HTML
		$this->mDisplayName = strip_tags ( $displayName );
		// Trim
		$this->mDisplayName = trim ( $displayName );
		// Make any (MS)SQL Injection Attack attempts safe
		$this->mDisplayName = str_replace ( "'", "''", $this->mDisplayName );
		return true;
	}
	
	//! Insert the package into the database
	/* 
	 * @return true if successful
	 */
	function InsertPackage() {
		$packageController = new PackageController ( );
		$packageController->CreatePackage ( $this->mDisplayName, $this->mCatalogue, $this->mCategory );
		return true;
	}
}

try {
	$handler = new AddPackageHandler ( );
	$handler->ValidateInput ( $_POST ['catalogueId'], $_POST ['displayName'], $_POST ['categoryId'] );
	$handler->InsertPackage ();
	?>
<script language="javascript" type="text/javascript">
		self.parent.window.frames["productMenu"].location.reload();
	</script>
<?php
} catch ( Exception $e ) {
	echo $e->GetMessage ();
}

?>