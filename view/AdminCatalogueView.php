<?php

//! View for showing/editing a catalogue on the admin side
class AdminCatalogueView extends AdminView {
	
	//! String - used to style the 'focused' tab
	var $mPageId = 'adminMenuCatalogue';
	
	function __construct() {
		parent::__construct('Admin > Catalogue',false,false,false);
		$this->IncludeCss('CatalogueMenuView.css');
		$this->IncludeCss('AdminCatalogueView.css.php');
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
			$this->LoadCatalogueList ();
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
		$this->mPage .= '
<div id="adminCatalogueViewContainer"><br />';
	}
	
	// Closes the display	
	function CloseDisplay() {
		$this->mPage .= '
</div>';
	}
	
	// Initialises the content section of the page, MUST be matched by $this->CloseContentDisplay()
	function InitialiseContentDisplay() {
		$registry = Registry::GetInstance ();
		$this->mPage .= <<<EOT

	<div id="adminCatalogueViewContentContainer">
EOT;
	}
	
	// Closes the content display	
	function CloseContentDisplay() {
		$this->mPage .= '
	</div>';
	}
	
	//! Loads the admin header section and the tab navigation section
	function InitialisePage() {
		$adminTabsView = new AdminTabsView ( );
		$adminHeaderView = new AdminHeaderView ( );
		$this->mPage .= $adminHeaderView->OpenHeader ( $this->mPageId);
		$this->mPage .= $adminTabsView->LoadDefault();
		$this->mPage .= $adminHeaderView->CloseHeader($this->mPageId);
	}
	
	//! Loads an iframe with CatalogueMenuView in it; basically loads a list of all catalogues
	function LoadCatalogueList() {
		$this->mPage .= '
	<div style="float: left;">
		<iframe src="'.$this->mViewDir.'/CatalogueMenuView.php" name="catalogueMenu" id="catalogueMenu">
		</iframe>
	</div>';
	}
	
	//! Loads an iframe with editArea in it, which handles any links to it and directs them to the correct target
	function LoadEditArea() {
		$this->mPage .= '						
	<div style="float: left;">
		<iframe src="'.$this->mRegistry->adminDir.'/editArea.php" name="catalogueEditAreaContainer" id="catalogueEditAreaContainer">
		</iframe>
	</div>';
	}

}
$page = new AdminCatalogueView();
echo $page->LoadDefault ();

?>