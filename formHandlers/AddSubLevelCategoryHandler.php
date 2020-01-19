<?php
require_once ('../autoload.php');

//! Adds a top level category to the database
class AddSubLevelCategoryHandler {
	
	//! String : Display name of the category to add
	var $mDisplayName;
	//! String : Description of the category to add
	var $mDescription;
	//! Obj:CatalogueModel : The catalogue the category will belong to
	var $mCatalogue;
	//! Obj:CategoryModel : The parent of the category
	var $mParent;
	
	//! Expects the catalogue ID, display name and description
	/*
	 * @param [in] catalogueId : The ID of the catalogue that the category is in
	 * @param [in] displayName : The display name of the category
	 * @param [in] description : The desctiption of the category
	 * @return true if successful
	 */
	function ValidateInput($catalogueId, $displayName, $description, $parentId) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		// Check the catalogue exists 
		$sql = 'SELECT COUNT(Catalogue_ID) AS CatalogueCount FROM tblCatalogue WHERE Catalogue_ID = ' . $catalogueId;
		$result = $database->query($sql);
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		if(0 === $resultObj->CatalogueCount) {
			$error = new Error ( 'Validation of new top level category failed because the catalogue ID doesn\'t exist.' );
			throw new Exception ( $error->GetErrorMsg () );
		} else {
			$this->mCatalogue = new CatalogueModel ( $catalogueId );
		}
		// Check the parent category exists
		$sql = 'SELECT Category_ID FROM tblCategory WHERE Category_ID = ' . $parentId;
		if (0 === $database->query ( $sql )) {
			$error = new Error ( 'Validation of new top level category failed because the parent category ID doesn\'t exist.' );
			throw new Exception ( $error->GetErrorMsg () );
		} else {
			$this->mParent = new CategoryModel ( $parentId );
		}
		// Remove HTML
		$this->mDisplayName = strip_tags ( $displayName );
		$this->mDescription = strip_tags ( $description );
		// Trim
		$this->mDisplayName = trim ( $displayName );
		$this->mDescription = trim ( $this->mDescription );
		// Make any (MS)SQL Injection Attack attempts safe
		$this->mDisplayName = str_replace ( "'", "''", $this->mDisplayName );
		$this->mDescription = str_replace ( "'", "''", $this->mDescription );
		return true;
	}
	
	//! Insert the category into the database
	/* 
	 * @return true if successful
	 */
	function InsertCategory() {
		$categoryController = new CategoryController ( );
		$parentCategory = new CategoryModel ( $this->mParent->GetCategoryId () );
		$newCategory = $categoryController->CreateCategory ( $this->mDisplayName, $this->mCatalogue );
		$newCategory->SetDescription ( $this->mDescription );
		$newCategory->SetParentCategory ( $parentCategory );
		return true;
	}

}

try {
	$handler = new AddSubLevelCategoryHandler ( );
	$handler->ValidateInput ( $_POST ['catalogueId'], $_POST ['displayName'], $_POST ['description'], $_POST ['parentId'] );
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