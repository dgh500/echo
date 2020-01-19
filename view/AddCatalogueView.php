<?php

//! View for the add a new top level category form
class AddCatalogueView extends AdminView {
	
	//! String : Holds the code for the page
	var $mPage;
	
	function __construct() {
		parent::__construct();
		$this->IncludeJs('jquery.js');		
		$this->IncludeJs('AddCatalogueView.js');
		$this->IncludeCss('admin/css/AddCatalogueView.css.php',false);
	}
	
	//! Loads the default view of the page
	/*!
	 * @return String - the code for the page
	 */
	function LoadDefault() {
		$this->mPage .= '
<form method="post" action="'.$this->mFormHandlersDir.'/AddCatalogueHandler.php" id="addCatalogueForm" name="addCatalogueForm">
	<fieldset>
	<legend>Add Catalogue</legend>
	<label for="displayName">Display Name</label>
		<input type="text" name="displayName" id="displayName" /><br />
	<input type="submit" value="Add Catalogue" class="submit" />
	</fieldset>
	<div id="errorBox"></div>
</form>';
		return $this->mPage;
	}
}

$page = new AddCatalogueView ( );
echo $page->LoadDefault ();

?>