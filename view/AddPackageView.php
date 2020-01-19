<?php

//! View for the add a new package form
class AddPackageView extends AdminView {
	
	//! String : Holds the code for the page
	var $mPage;
	
	function __construct() {
		parent::__construct(true);	
		$this->IncludeCss('admin/css/AddPackageView.css.php',false);
		$this->IncludeJs('AddPackageView.js');
	}
	
	//! Loads the default view of the page
	/*!
	 * @param [in] catalogueId : the ID of the catalogue that the package will be entered into
	 * @return String - the code for the page
	 */
	function LoadDefault($catalogueId, $categoryId) {
		$this->mPage .= '<form method="post" action="' . $this->mFormHandlersDir . '/AddPackageHandler.php" id="addPackageForm" name="addPackageForm">
				<fieldset>
				<legend>Add Package</legend>
				<label for="displayName">Display Name</label><input type="text" name="displayName" id="displayName" />
				<br />
				<input type="hidden" name="catalogueId" id="catalogueId" value="' . $catalogueId . '" />
				<input type="hidden" name="categoryId" id="categoryId" value="' . $categoryId . '" />
				<input type="submit" value="Add Package" class="submit" />
				</fieldset>
				<div id="errorBox">
				</div>
				</form>';
		return $this->mPage;
	}
}

$page = new AddPackageView ( );
echo $page->LoadDefault ( $_GET ['currentCatalogueId'], $_GET ['id'] );

?>