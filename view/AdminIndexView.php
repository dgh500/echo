<?php

//! Defines the view for the index section of the admin area
class AdminIndexView extends AdminView {

	//! String - used to style the 'focused' tab
	var $mPageId = 'adminMenuHome';

	function __construct() {
		parent::__construct('Admin > Home',false,false,false);
		$this->IncludeCss('AdminIndexView.css.php');
	}

	//! Generic load function
	/*!
	 * @return String - Code for the page
	 */
	function LoadDefault() {
		$adminHelper = new AdminHelper ( );
		if ($adminHelper->LoginCheck ()) {
			$this->InitialisePage ();
			$this->mPage .= '
			<div id="adminIndexViewContainer">
				<h2>'.$this->mCompanyName.' Admin</h2>
				<ul>
		<li><strong>Products</strong>
			<ul><li>Add/Edit/Remove categories, packages and products for any catalogue</li></ul>
		</li>
		<li><strong>Catalogues</strong>
			<ul><li>Add/Edit/Remove catalogues, add manufacturers & dispatch estimates</li>
			<li>Change the front page display, enable/disable packages for any catalogue</li></ul>
		</li>
		<li><strong>Orders</strong>
			<ul><li>Add/Ship/Cancel orders, update staff notes, search for orders</li></ul>
		</li>
		<li><strong>Manufacturers</strong>
			<ul><li>Edit manufacturers and add images, brand pages and manufacturer descriptions</li></ul>
		</li>
		<li><strong>Content</strong>
			<ul><li>Add/Edit/Delete content pages (eg. About Us) for each catalogue</li></ul>
		</li>
		<li><strong>Logout</strong>
			<ul><li>Click to log out of the system</li></ul>
		</li>
				</ul>
			</div>
							';
		} else {
			$adminLoginView = new AdminLoginView ( );
			$this->mPage .= $adminLoginView->LoadDefault ();
		}
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

$page = new AdminIndexView ( );
echo $page->LoadDefault ();

?>