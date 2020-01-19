<?php

//! Defines the view for the missing section of the admin area
class AdminMissingView extends AdminView {

	//! String - used to style the 'focused' tab
	var $mPageId = 'adminMenuReports';

	//! Initialise needed member variables
	function __construct() {

		$cssIncludes = array('jqueryUI.css','AdminReportView.css.php');
		$jsIncludes  = array('jquery.js','jqueryUi.js','AdminReportView.js');

		parent::__construct('Admin > Reports',$cssIncludes,$jsIncludes,false);
		$this->IncludeCss('adminPrint.css.php', true, 'print' );
		$this->mCatalogueController = new CatalogueController ( );
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
			$this->LoadMissingDisplay ();
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
		$this->mPage .= '<div id="adminReportsViewContainer"><br />';
	}

	// Closes the display
	function CloseDisplay() {
		$this->mPage .= '</div>';
	}

	// Initialises the content section of the page, MUST be matched by $this->CloseContentDisplay()
	function InitialiseContentDisplay() {
		$registry = Registry::GetInstance ();
		$this->mPage .= <<<EOT
			<div id="adminReportsViewContentContainer">
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
		$this->mPage .= $adminHeaderView->OpenHeader ( $this->mPageId );
		$this->mPage .= $adminTabsView->LoadDefault ();
		$this->mPage .= $adminHeaderView->CloseHeader ( $this->mPageId );
	}

	function LoadMissingDisplay() {
		$this->mPage .= '<div id="reportChoice">';
		$this->LoadReportChoice ();
		$this->LoadCatalogueChoice ();
		$this->mPage .= '</div>';
		$this->mPage .= '<div id="reportDateRange">';
		$this->LoadDateRange ();
		$this->mPage .= '</div>';
		$this->mPage .= '<br /><input type="submit" value="Generate Report" id="generateReport" style="margin-left: 5px; margin-top: 10px;" />
		<hr />';
		$this->mPage .= '<div id="contentLoading"></div><div id="reportResults"></div>';
	}

	//! Loads a dropdown with a dropdown list of catalogues
	function LoadCatalogueChoice() {
		$allCatalogues = $this->mCatalogueController->GetAllCatalogues ();
		$this->mPage .= '
		<strong>In Catalogue:</strong><br />
			<select id="catalogue">
			<option value="ALL">All Catalogues</option>
		';
		foreach ( $allCatalogues as $catalogue ) {
			$this->mPage .= '<option value="' . $catalogue->GetCatalogueId () . '">' . $catalogue->GetDisplayName () . '</option>';
		}
		$this->mPage .= '</select><br />';
	} // End CatalogueChoice


	//! Loads the choice of reports drop down
	function LoadReportChoice() {
		$this->mPage .= '
		<strong>Select Report:</strong><br />
			<select id="reportType">
				<option value="allProducts">	All Products</option>
				<option value="allSkus">		All SKUs</option>
				<option value="dodgyOptions">	Dodgy Options</option>
				<option value="dodgySage">		Dodgy Sage</option>
				<option value="emptyCategories">Empty Categories</option>
				<option value="descriptions">	Missing Descriptions</option>
				<option value="images">			Missing Images</option>
				<option value="price">			Missing Prices</option>
				<option value="postage">		Unable to Buy</option>
				<option value="relatedSimilar">	Missing Related/Similar</option>
				<option value="sageCodes">		Missing Sage Codes</option>
				<option value="missingSizes">	Missing Sizes (No Options)</option>
				<option value="weight">			Missing Weight</option>
				<option value="mostCancelled">	Most Cancelled</option>
				<option value="notInStacks">	Not In Stacks</option>
				<option value="authorisedOrders">On Order</option>
				<option value="ordersFromCatalogue">Orders</option>
				<option value="outOfStock">		Out of Stock</option>
				<option value="poorStockLevels">Poor Stock Levels</option>
				<option value="referrer">		Referrer Report</option>
				<option value="topBrandsAll">	Top Brands (All Sales)</option>
				<option value="topBrandsExcl">	Top Brands (Excl Small)</option>
				<option value="topProducts">	Top Performing Products</option>
				<option value="topStacks">		Top Stacks</option>
				<option value="zeroSkuProducts">Zero SKU Products</option>
			</select><br />';
	} // End LoadReportChoice


	function LoadDateRange() {
		// Construct options for days 1-31
		$dayOptions = '';
		for($i = 1; $i < 32; $i ++) {
			if ($i < 10) {
				$day = '0' . $i;
			} else {
				$day = $i;
			}
			$dayOptions .= '<option>' . $day . '</option>';
		}
		// Construct options for months 1-12
		$monthOptions = '';
		for($i = 1; $i < 13; $i ++) {
			if ($i < 10) {
				$month = '0' . $i;
			} else {
				$month = $i;
			}
			$monthOptions .= '<option>' . $month . '</option>';
		}
		// Construct options for years for the past 10 years
		$yearOptions = '';
		$currentTime = time ();
		for($i = 1; $i < 10; $i ++) {
			$yearOptions .= '<option>' . date ( 'Y', $currentTime ) . '</option>';
			$currentTime = $currentTime - 31556926; // Number of seconds in a year = 31556926, use 32556926 to overshoot it a bit (11 days ish) to compensate on new years eve
		}

		$this->mPage .= <<<EOT
			<strong>Date Range</strong><br />
			<label for="startDate">From: </label><input type="text" id="startDate" name="startDate" /><br />
			<label for="endDate">To: </label><input type="text" id="endDate" name="endDate" /><br /><br />

			<a href="#" onClick="switchDate('lastWeek')">Last Week</a>
			| <a href="#" onClick="switchDate('lastMonth')">Last Month</a>
			| <a href="#" onClick="switchDate('lastYear')">Last Year</a>
			| <a href="#" onClick="switchDate('allTime')">All Time</a>
EOT;
	} // End DateRange


}

set_time_limit ( 300 );
$page = new AdminMissingView ( );
echo $page->LoadDefault ();

/*
	<link type="text/css" href="css/themename/jquery-ui-1.7.custom.css" rel="Stylesheet" />
	<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
	<script type="text/javascript" src="js/jquery-ui-1.7.custom.min.js"></script>
*/

?>