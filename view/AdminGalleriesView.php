<?php

//! Defines the view for the galleries section of the admin area
class AdminGalleriesView extends AdminView {
	
	//! String - used to style the 'focused' tab
	var $mPageId = 'adminMenuGalleries';
	
	//! Initialise needed member variables
	function __construct() {
		// Includes
		$cssIncludes = array('AdminGalleriesView.css.php');
		$jsIncludes  = array('AdminGalleriesView.js');
		// Construct
		parent::__construct('Admin > Galleries',$cssIncludes,$jsIncludes);
	}
	
	//! Generic load function
	/*!
	 * @return String - Code for the page
	 */
	function LoadDefault() {
		$this->InitialisePage();		
		$this->LoadGalleryList();
		$this->LoadEditArea();
		return $this->mPage;
	} // End LoadDefault

	//! Loads the admin <head> section and the tab navigation section
	function InitialisePage() {
		$adminTabsView 		= new AdminTabsView;
		$adminHeaderView 	= new AdminHeaderView;
		$this->mPage .= $adminHeaderView->OpenHeader($this->mPageId);
		$this->mPage .= $adminTabsView->LoadDefault();
		$this->mPage .= $adminHeaderView->CloseHeader($this->mPageId);
	} // End InitialisePage

	//! Loads an iframe with CatalogueMenuView in it; basically loads a list of all catalogues
	function LoadGalleryList() {
		$this->mPage .= '
	<div style="float: left;">
		<iframe src="'.$this->mViewDir.'/GalleryMenuView.php" name="galleryMenu" id="galleryMenu">
		</iframe>
	</div>';
	}
	
	//! Loads an iframe with editArea in it, which handles any links to it and directs them to the correct target
	function LoadEditArea() {
		$this->mPage .= '						
	<div style="float: left;">
		<iframe src="'.$this->mRegistry->adminDir.'/editArea.php" name="galleryEditAreaContainer" id="galleryEditAreaContainer">
		</iframe>
	</div>';
	}

} // End AdminGalleriesView

set_time_limit(300);
$page = new AdminGalleriesView();
echo $page->LoadDefault();

?>