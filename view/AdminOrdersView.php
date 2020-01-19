<?php

//! Defines the view for the orders section of the admin area
class AdminOrdersView extends AdminView {

	//! String - used to style the 'focused' tab
	var $mPageId = 'adminMenuOrders';

	function __construct() {
		parent::__construct('Admin > Orders',false,false,false);
		$this->IncludeCss('wombat7/css/AdminOrdersView.css.php',false);
	}

	//! Generic load function
	/*!
	 * @param orderId - If supplied loads the order
	 * @return String - Code for the page
	 */
	function LoadDefault($orderId = false) {
		$adminHelper = new AdminHelper ( );
		if ($adminHelper->LoginCheck ()) {
			$this->InitialisePage ();
			$this->InitialiseDisplay ();
			$this->LoadOrdersList ();
			if ($orderId) {
				$this->LoadOrdersEdit ( $orderId );
			} else {
				$this->LoadOrdersEdit ();
			}
			$this->CloseDisplay ();
		} else {
			$adminLoginView = new AdminLoginView ( );
			$this->mPage .= $adminLoginView->LoadDefault ('secure');
		}
		return $this->mPage;
	}

	//! Loads the admin <head> section and the tab navigation section
	function InitialisePage() {
		$adminTabsView = new AdminTabsView ( );
		$adminHeaderView = new AdminHeaderView ( );
		$this->mPage .= $adminHeaderView->OpenHeader ( $this->mPageId, true );
		$this->mPage .= $adminTabsView->LoadDefault ();
		$this->mPage .= $adminHeaderView->CloseHeader ( $this->mPageId );
	}

	// Initialise the display - MUST be matched by $this->CloseDisplay()
	function InitialiseDisplay() {
		$this->mPage .= '<div id="adminOrdersViewContainer"><br />';
	}

	// Closes the display
	function CloseDisplay() {
		$this->mPage .= '</div>';
	}

	//! Loads an iframe with OrdersMenuView in it; basically loads a list of all catalogues
	function LoadOrdersList() {
		$registry = Registry::getInstance();
		$dir = $registry->secureBaseDir;
		$this->mPage .= '
		<div style="width: 200px; margin-bottom: 8px; margin-top: -6px; text-align: center; border: 0px solid #000;">
			<a href="' . str_replace('http','https',$registry->viewDir) . '/OrdersMenuView.php?method=allOrders" target="ordersMenu" style="font-weight: bold; text-decoration: none; color: #000;">ALL ORDERS</a> |
			<a href="' . str_replace('http','https',$registry->viewDir) . '/OrdersMenuView.php?method=undispatched" target="ordersMenu" style="font-weight: bold; text-decoration: none; color: #000;">UNDISPATCHED</a>
		</div>';
		$this->mPage .= '<div style="float: left;"><iframe src="' . $dir . '/view/OrdersMenuView.php" name="ordersMenu" id="ordersMenu" frameborder="0" border="0"></iframe></div>';
	}

	//! Loads an iframe with OrdersEditView in it, allowing orders to be printed etc.
	function LoadOrdersEdit($orderId = false) {
		$registry = Registry::getInstance();
		$dir = $registry->secureBaseDir;
		if ($orderId) {
			$this->mPage .= '<div style="float: left;"><iframe src="' . $dir . '/view/OrdersEditView.php?id=' . $orderId . '" name="ordersEdit" id="ordersEdit" frameborder="0" border="0"></iframe></div>';
		} else {
			$this->mPage .= '<div style="float: left;"><iframe src="' . $dir . '/view/OrdersEditView.php" name="ordersEdit" id="ordersEdit" frameborder="0" border="0"></iframe></div>';
		}
	}

}

$page = new AdminOrdersView ( );
if (isset ( $_GET ['id'] )) {
	echo $page->LoadDefault ( $_GET ['id'] );
} else {
	echo $page->LoadDefault ();
}
?>