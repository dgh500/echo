<?php

//! View for the add a new gallery
class AddGalleryView extends AdminView {
	
	//! String : Holds the code for the page
	var $mPage;
	
	function __construct() {
		parent::__construct();
		$this->IncludeJs('jquery.js');		
		$this->IncludeJs('AddGalleryView.js');
		$this->IncludeCss('admin/css/AddGalleryView.css.php',false);
	}
	
	//! Loads the default view of the page
	/*!
	 * @return String - the code for the page
	 */
	function LoadDefault() {
		$this->mPage .= '
<form method="post" action="'.$this->mFormHandlersDir.'/AddGalleryHandler.php" id="addGalleryForm" name="addGalleryForm">
	<fieldset>
	<legend>Add Gallery</legend>
	<label for="displayName">Display Name</label>
		<input type="text" name="displayName" id="displayName" /><br />
	<input type="submit" value="Add Gallery" class="submit" />
	</fieldset>
	<div id="errorBox"></div>
</form>';
		return $this->mPage;
	}
}

$page = new AddGalleryView;
echo $page->LoadDefault ();

?>