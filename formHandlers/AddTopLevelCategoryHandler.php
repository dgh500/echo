<?php
require_once ('../autoload.php');

//! Adds a top level category to the database
class AddTopLevelCategoryHandler {
	
	//! String : Display name of the category to add
	var $mDisplayName;
	//! String : Description of the category to add
	var $mDescription;
	//! Obj:CatalogueModel : The catalogue the category will belong to
	var $mCatalogue;
	
	//! Expects the catalogue ID, display name and description
	/*
	 * @param [in] catalogueId : The ID of the catalogue that the category is in
	 * @param [in] displayName : The display name of the category
	 * @param [in] description : The desctiption of the category
	 * @return true if successful
	 */
	function ValidateInput($catalogueId, $displayName, $description) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		// Check the catalogue exists 
		$sql = 'SELECT COUNT(Catalogue_ID) AS CatalogueCount FROM tblCatalogue WHERE Catalogue_ID = ' . $catalogueId;
		$result = $database->query($sql);
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		if(0 === $resultObj->CatalogueCount) {
			var_dump($database->query($sql));
			$error = new Error ( 'Validation of new top level category failed because the catalogue ID doesn\'t exist.' );
			throw new Exception ( $error->GetErrorMsg () );
		} else {
			$this->mCatalogue = new CatalogueModel ( $catalogueId );
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
		$newCategory = $categoryController->CreateCategory ( $this->mDisplayName, $this->mCatalogue );
		$newCategory->SetDescription ( $this->mDescription );
		return true;
	}

}

try {
	$handler = new AddTopLevelCategoryHandler ( );
	$handler->ValidateInput ( $_POST ['catalogueId'], $_POST ['displayName'], $_POST ['description'] );
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