<?php

//! View for the add a new product form
class AddProductView extends View {
	
	//! String : Holds the code for the page
	var $mPage;
	
	function __construct() {
		parent::__construct();
		$this->IncludeJs('jquery.js');
		$this->IncludeJs('AddProductView.js');
		$this->IncludeCss('admin/css/AddProductView.css.php',false);
	}
	
	//! Loads the default view of the page
	/*!
	 * @param [in] categoryId : the ID of the category that the product will be entered into
	 * @return String - the code for the page
	 */
	function LoadDefault($categoryId) {
		$registry = Registry::getInstance ();
		$baseDir = $registry->baseDir;
		$formHandlersDir = $registry->formHandlersDir;
		$this->mPage .= '
<form method="post" action="' . $formHandlersDir . '/AddProductHandler.php" id="addProductForm" name="addProductForm">
	<fieldset>
		<legend>Add Product</legend>
		<label for="displayName">Display Name</label><input type="text" name="displayName" id="displayName" />
		<br />
		<input type="hidden" name="categoryId" id="categoryId" value="' . $categoryId . '" />
		<input type="submit" value="Add Product" class="submit" />
	</fieldset>
	<div id="errorBox"></div>
</form>';
		return $this->mPage;
	}
}

$page = new AddProductView ( );
echo $page->LoadDefault ( $_GET ['id'] );

?>