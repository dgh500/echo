<?php

//! View that defines the main tabs found in the admin navigation - used to provide a central place for changing the navigation
class AdminTabsView extends AdminView {

	function __construct() {
		parent::__construct('',false,false,false);
	}

	//! Generic load function, does all the work itself because a very simple view
	/*!
	 * @return String - The code for the view
	 */
	function LoadDefault() {

		if(isset($_SESSION['showAllTabs']) && $_SESSION['showAllTabs'] == true) {
			$displayStyle = 'style="display: block"';
			$word = 'Less';
		} else {
			$displayStyle = 'style="display: none"';
			$word = 'More';
		}

		$this->mPage .= <<<EOT

<ul>
	<li id="adminMenuNav-home"><a href="{$this->mBaseDir}/wombat7/home">Home</a></li>
	<li id="adminMenuNav-products"><a href="{$this->mBaseDir}/wombat7/products">Products</a></li>
	<li id="adminMenuNav-catalogue"><a href="{$this->mBaseDir}/wombat7/catalogue">Catalogues</a></li>
	<li id="adminMenuNav-orders"><a href="{$this->mSecureBaseDir}/wombat7/orders">Orders</a></li>
	<li id="adminMenuNav-logout"><form action="{$this->mFormHandlersDir}/AdminLogoutHandler.php" method="post" name="logout" id="logout"><a href="#" onclick="javascript:document.logout.submit()">Logout</a></form></li>

	<li id="adminMenuNav-more"><a href="#" id="moreAdminTabsLink">{$word}</a></li>
	<div id="moreAdminTabs" {$displayStyle}>
		<li id="adminMenuNav-tags"><a href="{$this->mBaseDir}/wombat7/tags">Tags</a></li>
		<li id="adminMenuNav-content"><a href="{$this->mBaseDir}/wombat7/content">Content</a></li>
		<li id="adminMenuNav-manufacturers"><a href="{$this->mBaseDir}/wombat7/manufacturers">Manufacturers</a></li>
		<li id="adminMenuNav-settings"><a href="{$this->mBaseDir}/wombat7/settings">Settings</a></li>
		<li id="adminMenuNav-galleries"><a href="{$this->mBaseDir}/wombat7/galleries">Galleries</a></li>
		<li id="adminMenuNav-reports"><a href="{$this->mBaseDir}/wombat7/reports">Reports</a></li>
		<li id="adminMenuNav-salesReports"><a href="{$this->mBaseDir}/wombat7/salesReports">Sales Reports</a></li>
		<li id="adminMenuNav-affiliates"><a href="{$this->mBaseDir}/wombat7/affiliates">Affiliates</a></li>
		<li id="adminMenuNav-help"><a href="{$this->mBaseDir}/wombat7/help">Help</a></li>
	</div>
</ul>

EOT;
		return $this->mPage;
	}
}
?>