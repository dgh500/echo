<?php

//! Defines the view for the misc section of the admin area
class AdminMiscView extends View {
	
	//! String - used to style the 'focused' tab
	var $mPageId = 'adminMenuMisc';
	
	//! Generic load function
	/*!
	 * @return String - Code for the page
	 */
	function LoadDefault() {
		$this->InitialisePage ();
		$this->InitialiseDisplay ();
		$this->InitialiseContentDisplay ();
		$this->mPage .= 'as';
		$this->CloseContentDisplay ();
		$this->CloseDisplay ();
		return $this->mPage;
	}
	
	// Initialise the display - MUST be matched by $this->CloseDisplay()	
	function InitialiseDisplay() {
		$this->mPage .= '<div id="adminMiscViewContainer"><br />';
	}
	
	// Closes the display	
	function CloseDisplay() {
		$this->mPage .= '</div>';
	}
	
	// Initialises the content section of the page, MUST be matched by $this->CloseContentDisplay()
	function InitialiseContentDisplay() {
		$registry = Registry::GetInstance ();
		$this->mPage .= <<<EOT
			<div id="adminMiscViewContentContainer">
EOT;
	}
	
	// Closes the content display	
	function CloseContentDisplay() {
		$this->mPage .= '</div>';
	}
	
	//! Loads the admin <head> section and the tab navigation section
	function InitialisePage() {
		$adminTabsView = new AdminTabsView ( );
		$adminHeadView = new AdminHeadView ( );
		$adminHeaderView = new AdminHeaderView ( );
		$this->mPage .= $adminHeadView->LoadDefault ();
		$this->mPage .= $adminHeaderView->OpenHeader ( $this->mPageId );
		$this->mPage .= $adminTabsView->LoadDefault ();
		$this->mPage .= $adminHeaderView->CloseHeader ( $this->mPageId );
	}

}
$page = new AdminMiscView ( );
$page->IncludeCss ( 'admin.css.php' );
$page->IncludeCss ( 'adminForms.css.php' );

echo $page->LoadDefault ();

?>