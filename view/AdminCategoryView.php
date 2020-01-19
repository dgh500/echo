<?php

//! View for showing/editing a category on the admin side
class AdminCategoryView extends AdminView {

	//! String : Holds the code for the page
	var $mPage;

	function __construct() {
		$jsIncludes = array('jqueryUi.js','jquery.alerts.js','AdminCategoryView.js');
		$cssIncludes = array('AdminCategoryView.css.php','jquery.alerts.css.php');
		parent::__construct(true,$cssIncludes,$jsIncludes);
	}

	//! Loads the default view of the page
	/*!
	 * @param [in] catalogueId : the ID of the catalogue that the category will be entered into
	 * @return String - the code for the page
	 */
	function LoadDefault($categoryId) {
		$category = new CategoryModel($categoryId);
		// See http://www.fckeditor.net for full details
		$oFCKeditor = new FCKeditor('description');
		$oFCKeditor->BasePath = $this->mAdminDir . '/fckeditor/';
		$oFCKeditor->ToolbarSet = 'Basic';
		$oFCKeditor->Value = $category->GetDescription();
		$oFCKeditor->Height = 150;
		$oFCKeditor->Width = 400;

		$this->mPage .= '
				<form method="post" action="' . $this->mFormHandlersDir . '/AdminCategoryHandler.php" id="adminCategoryForm" name="adminCategoryForm">
				<fieldset>
				<legend>Category Name</legend>
				<label for="displayName">Display Name</label><input type="text" name="displayName" id="displayName" value="' . $category->GetDisplayName () . '" />
				<br />
				<label for="description">Description</label>
				';
		$this->mPage .= $oFCKeditor->Create();
		$this->mPage .= '
				<br />
				<input type="hidden" name="categoryId" id="categoryId" value="' . $categoryId . '" />
				<input type="submit" name="saveButton" id="saveButton" value="Save" class="submit" />
				<input type="button" name="deleteButton" id="deleteButton" value="Delete" class="submit" />
				</fieldset>
				<div id="errorBox">
				</div>
				</form>';
		return $this->mPage;
	}
}

$page = new AdminCategoryView ( );
echo $page->LoadDefault ( $_GET ['id'] );

?>