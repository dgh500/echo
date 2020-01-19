<?php

//! View for the add a new sub level category form
class AddSubLevelCategoryView extends View {
	
	//! String : Holds the code for the page
	var $mPage;

	function __construct() {
		parent::__construct();
		$this->IncludeCss('admin/css/AddSubLevelCategoryView.css.php',false);
		$this->IncludeJs('jquery.js');
		$this->IncludeJs('AddSubLevelCategoryView.js');
	}
	
	//! Loads the default view of the page
	/*!
	 * @param [in] catalogueId 	: the ID of the catalogue that the category will be entered into
	 * @param [in] parentId		: the ID of the category that will be the parent of this new sub category
	 * @return String - the code for the page
	 */
	function LoadDefault($catalogueId, $parentId) {
		$this->mPage .= '<link rel="stylesheet" type="text/css" href="' . $this->mBaseDir . '/css/adminForms.css" />
				<form method="post" action="' . $this->mFormHandlersDir . '/AddSubLevelCategoryHandler.php" id="addSubLevelCategoryForm" name="addSubLevelCategoryForm">
				<fieldset>
				<legend>Add Category</legend>
				<label for="displayName">Display Name</label><input type="text" name="displayName" id="displayName" />
				<br />
				<label for="description">Description</label><input type="text" name="description" id="description" />
				<input type="hidden" name="catalogueId" id="catalogueId" value="' . $catalogueId . '" />
				<input type="hidden" name="parentId" id="parentId" value="' . $parentId . '" />		
				<input type="submit" value="Add Category" class="submit" />
				</fieldset>
				<div id="errorBox">
				</div>
				</form>';
		return $this->mPage;
	}
}

$page = new AddSubLevelCategoryView ( );
echo $page->LoadDefault ( $_GET ['currentCatalogueId'], $_GET ['id'] );

?>