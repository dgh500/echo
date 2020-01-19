<?php

//! Defines the view for the logout section of the admin area
class AdminLogoutView extends View {
	
	//! String - used to style the 'focused' tab
	var $mPageId = 'adminMenuLogout';
	
	//! Generic load function
	/*!
	 * @return String - Code for the page
	 */
	function LoadDefault() {
		$this->InitialisePage ();
		return $this->mPage;
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

$page = new AdminLogoutView ( );
$page->IncludeCss ( 'admin.css.php' );

echo $page->LoadDefault ();

?>