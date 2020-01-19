<?php

//! Defines the view for the settings section of the admin area
class AdminManufacturersView extends AdminView {

	//! String - used to style the 'focused' tab
	var $mPageId = 'adminMenuManufacturers';

	function __construct() {
		parent::__construct('Admin > Manufacturers',false,false,false);
		$this->IncludeCss('AdminManufacturersView.css.php');
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
		$this->mPage .= '<div id="adminManufacturersViewContentContainer"><br />';
	}

	// Closes the display
	function CloseDisplay() {
		$this->mPage .= '</div>';
	}

	// Initialises the content section of the page, MUST be matched by $this->CloseContentDisplay()
	function InitialiseContentDisplay() {
		$registry = Registry::GetInstance ();
		$this->mPage .= <<<EOT
			<div id="adminManufacturerViewContainer">
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
		$this->mPage .= '<div style="float: left; width: 300px;">';
		$catalogueSelection = new CatalogueListView ( );
		$this->mPage .= $catalogueSelection->LoadDefault ( $this->mCatalogue, 'manufacturerMenu' );
	}

	function LoadManufacturerMenu() {
		$registry = Registry::GetInstance ();
		$this->mPage .= '<br /><iframe src="' . $registry->viewDir . '/ManufacturerMenuView.php" name="manufacturerMenu" id="manufacturerMenu"></iframe>';
		$this->mPage .= '</div>';
	}

	function LoadEditArea() {
		$registry = Registry::GetInstance ();
		$this->mPage .= '<div id="manufacturerEditIframeContainer">';
		$this->mPage .= '<iframe src="' . $registry->adminDir . '/editArea.php" name="editAreaContainer" id="editAreaContainer" frameborder="0" border="0" scrolling="no"></iframe>';
		$this->mPage .= '</div>';
	}

}
$page = new AdminManufacturersView ( );
echo $page->LoadDefault ();

?>