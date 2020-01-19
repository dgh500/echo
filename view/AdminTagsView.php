<?php

//! Defines the view for the settings section of the admin area
class AdminTagsView extends AdminView {
	
	//! String - used to style the 'focused' tab
	var $mPageId = 'adminMenuTags';
	
	function __construct() {
		parent::__construct('Admin > Tags',false,false,false);
		$this->IncludeCss('AdminTagsView.css.php');
		$this->IncludeCss('AdminTagsView.js');		
	}
	
	//! Generic load function
	/*!
	 * @return String - Code for the page
	 */
	function LoadDefault() {
		$adminHelper = new AdminHelper ( );
		if ($adminHelper->LoginCheck ()) {
			$this->InitialisePage ();
			$this->InitialiseDisplay ();
			$this->InitialiseContentDisplay ();
			$this->LoadCatalogueSelection ();
			$this->LoadManufacturerMenu ();
			$this->LoadEditArea ();
			$this->CloseContentDisplay ();
			$this->CloseDisplay ();
		} else {
			$adminLoginView = new AdminLoginView ( );
			$this->mPage .= $adminLoginView->LoadDefault ();
		}
		return $this->mPage;
	}
	
	// Initialise the display - MUST be matched by $this->CloseDisplay()	
	function InitialiseDisplay() {
		$this->mPage .= '<div id="adminTagsViewContainer"><br />';
	}
	
	// Closes the display	
	function CloseDisplay() {
		$this->mPage .= '</div>';
	}
	
	// Initialises the content section of the page, MUST be matched by $this->CloseContentDisplay()
	function InitialiseContentDisplay() {
		$registry = Registry::GetInstance ();
		$this->mPage .= <<<EOT
			<div id="adminTagsViewTagContainer">
EOT;
	}
	
	// Closes the content display	
	function CloseContentDisplay() {
		$this->mPage .= '</div>';
	}
	
	//! Loads the admin <head> section and the tab navigation section
	function InitialisePage() {
		$adminTabsView = new AdminTabsView ( );
		$adminHeaderView = new AdminHeaderView ( );
		$this->mCatalogue = $this->mRegistry->catalogue;
		$this->mPage .= $adminHeaderView->OpenHeader ( $this->mPageId );
		$this->mPage .= $adminTabsView->LoadDefault ();
		$this->mPage .= $adminHeaderView->CloseHeader ( $this->mPageId );
	}
	
	function LoadCatalogueSelection() {
		$this->mPage .= '<div id="tagMenuContainer">';
		$catalogueSelection = new CatalogueListView ( );
		$this->mPage .= $catalogueSelection->LoadDefault ( $this->mCatalogue, 'tagMenu' );
	}
	
	function LoadManufacturerMenu() {
		$registry = Registry::GetInstance ();
		$this->mPage .= '<br /><iframe src="' . $registry->viewDir . '/TagMenuView.php" name="tagMenu" id="tagMenu"></iframe>';
		$this->mPage .= '</div>';
	}
	
	function LoadEditArea() {
		$registry = Registry::GetInstance ();
		$this->mPage .= '<div id="editTagArea">';
		$this->mPage .= '<iframe src="' . $registry->adminDir . '/editArea.php" name="editAreaContainer" id="editAreaContainer" frameborder="0" border="0"></iframe>';
		$this->mPage .= '</div>';
	}

}
$page = new AdminTagsView ( );
echo $page->LoadDefault ();

?>