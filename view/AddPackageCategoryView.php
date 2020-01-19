<?php

//! View for the add a new top level category form
class AddPackageCategoryView extends AdminView {
	
	//! String : Holds the code for the page
	var $mPage;

	function __construct() {
		parent::__construct();
		$this->IncludeCss('admin/css/AddPackageCategoryView.css.php',false);
		$this->IncludeJs('jquery.js');
		$this->IncludeJs('AddPackageCategoryView.js');
	}

	//! Loads the default view of the page
	/*!
	 * @param [in] catalogueId : the ID of the catalogue that the category will be entered into
	 * @return String - the code for the page
	 */
	function LoadDefault($catalogueId) {
		$registry = Registry::getInstance ();
		$baseDir = $registry->baseDir;
		$formHandlersDir = $registry->formHandlersDir;
		$this->mPage .= '<form method="post" action="' . $formHandlersDir . '/AddPackageCategoryHandler.php" id="addPackageCategoryForm" name="addPackageCategoryForm">
				<fieldset>
				<legend>Add Package Category</legend>
				<label for="displayName">Display Name</label><input type="text" name="displayName" id="displayName" />
				<br />
				<input type="hidden" name="catalogueId" id="catalogueId" value="' . $catalogueId . '" />
				<input type="submit" value="Add Category" class="submit" />
				</fieldset>
				<div id="errorBox">
				</div>
				</form>';
		return $this->mPage;
	}
}

$page = new AddPackageCategoryView ( );
echo $page->LoadDefault ( $_GET ['currentCatalogueId'] );

?>