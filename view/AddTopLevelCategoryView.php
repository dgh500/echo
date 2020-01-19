<?php

//! View for the add a new top level category form
class AddTopLevelCategoryView extends View {
	
	//! String : Holds the code for the page
	var $mPage;
	
	function __construct() {
		parent::__construct();
		$this->IncludeCss('admin/css/AddTopLevelCategoryView.css.php',false);
		$this->IncludeJs('jquery.js');
		$this->IncludeJs('AddTopLevelCategoryView.js');
	}
	
	//! Loads the default view of the page
	/*!
	 * @param [in] catalogueId : the ID of the catalogue that the category will be entered into
	 * @return String - the code for the page
	 */
	function LoadDefault($catalogueId) {
		$this->mPage .= '
<form method="post" action="' . $this->mFormHandlersDir . '/AddTopLevelCategoryHandler.php" id="addTopLevelCategoryForm" name="addTopLevelCategoryForm">
<fieldset>
<legend>Add Category</legend>
<label for="displayName">Display Name</label><input type="text" name="displayName" id="displayName" />
<br />
<label for="description">Description</label><input type="text" name="description" id="description" />
<input type="hidden" name="catalogueId" id="catalogueId" value="' . $catalogueId . '" />
<input type="submit" value="Add Category" class="submit" />
</fieldset>
<div id="errorBox">
</div>
</form>';
		return $this->mPage;
	}
}

$page = new AddTopLevelCategoryView ( );
echo $page->LoadDefault ( $_GET ['currentCatalogueId'] );

?>