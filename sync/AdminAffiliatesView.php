<?php

//! Defines the view for the missing section of the admin area
class AdminAffiliates extends View {
	
	//! String - used to style the 'focused' tab
	var $mPageId = 'adminMenuAffiliates';
	
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
			$this->LoadAffiliatesDisplay ();
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
		$this->mPage .= '<div id="adminMissingViewContainer"><br />';
	}
	
	// Closes the display	
	function CloseDisplay() {
		$this->mPage .= '</div>';
	}
	
	// Initialises the content section of the page, MUST be matched by $this->CloseContentDisplay()
	function InitialiseContentDisplay() {
		$registry = Registry::GetInstance ();
		$this->mPage .= <<<EOT
			<div id="adminMissingViewContentContainer">
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
		$this->mPage .= $adminHeadView->LoadDefault ( ' > Missing' );
		$this->mPage .= $adminHeaderView->OpenHeader ( $this->mPageId );
		$this->mPage .= $adminTabsView->LoadDefault ();
		$this->mPage .= $adminHeaderView->CloseHeader ( $this->mPageId );
	}
	
	function LoadAffiliatesDisplay() {
		$this->mPage .= <<<EOT
		aff
EOT;
	}
}
set_time_limit ( 300 );
$page = new AdminAffiliates ( );
$page->IncludeCss ( 'admin.css.php' );
$page->IncludeCss ( 'adminForms.css.php' );

echo $page->LoadDefault ();

?>