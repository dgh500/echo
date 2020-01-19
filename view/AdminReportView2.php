<?php

//! Defines the view for the missing section of the admin area
class AdminReportView2 extends AdminView {
	
	//! String - used to style the 'focused' tab
	var $mPageId = 'adminMenuSalesReports';
	
	//! Initialise needed member variables
	function __construct() {
		// Includes
		$cssIncludes = array('AdminReportView2.css.php','jqueryUI.css');
		$jsIncludes  = array('jquery.js','jquery.flot.js','jqueryUi.js','AdminReportView2.js');
		// Construct
		parent::__construct('Admin > Reports',$cssIncludes,$jsIncludes);
		// Print Style Sheet
		$this->IncludeCss('adminPrint.css.php',true,'print');
		// To generate the initial options container
		$this->mCatalogueController = new CatalogueController;
	}
	
	//! Generic load function
	/*!
	 * @return String - Code for the page
	 */
	function LoadDefault() {
		$this->InitialisePage();
		$this->LoadReportChoice();
		return $this->mPage;
	}
			
	//! Loads the admin <head> section and the tab navigation section
	function InitialisePage() {
		$adminTabsView 		= new AdminTabsView;
		$adminHeaderView 	= new AdminHeaderView;
		$this->mPage .= $adminHeaderView->OpenHeader($this->mPageId);
		$this->mPage .= $adminTabsView->LoadDefault();
		$this->mPage .= $adminHeaderView->CloseHeader($this->mPageId);
	}
	
	function LoadReportChoice() {
		$this->mPage .= '<div id="adminReportView2Container">';
			// Loading...
			$this->mPage .= '<div id="catalogueContainer">';
				$this->mPage .= '<div id="loading"><img src="'.$this->mBaseDir.'/admin/images/reportAjaxLoadingComplete.gif" /></div>';			
				$this->LoadCatalogueChoice();
			$this->mPage .= '</div>';
			$this->mPage .= '<div id="categoryManufacturerContainer">';
				// For the category/manufacturer choice
			$this->mPage .= '</div>';
			$this->mPage .= '<div id="productContainer">';
				// For the product choice
			$this->mPage .= '</div>';
			$this->mPage .= '<div id="dateRangeContainer">';
				// For the date range choice
			$this->mPage .= '</div>';
			$this->mPage .= '<div id="generateReportContainer">';
				// For the generate report button
			$this->mPage .= '</div>';
			// Seperator
			$this->mPage .= '<div id="seperator"></div>';
			$this->mPage .= '<div id="reportGraphContainer">';
				// For the graph
				$this->mPage .= '<div id="placeholder" name="placeholder" style="height: 500px; width: 850px;"></div>';
			$this->mPage .= '</div>';
			$this->mPage .= '<div id="legendContainer"></div>';
			$this->mPage .= '<div id="reportTotalContainer">';
				// For the graph totals underneath
			$this->mPage .= '</div>';
			
			
		$this->mPage .= '</div>';
	}
	
	//! Loads a dropdown with a dropdown list of catalogues
	function LoadCatalogueChoice() {
		$allCatalogues = $this->mCatalogueController->GetAllCatalogues ();
		$this->mPage .= '
		<form id="catalogueChoiceForm" name="catalogueChoiceForm" method="post" style="float: left; height: 100%;">
		<div class="clickableBg" id="catalogueGo" name="catalogueGo"></div>
		<div style="float: left">
		<strong>Select Catalogue:</strong><br />
			<select id="catalogueChoice" name="catalogueChoice">
			<option value="ALL">All Catalogues</option>
		';
		foreach ( $allCatalogues as $catalogue ) {
			$this->mPage .= '<option value="' . $catalogue->GetCatalogueId () . '">' . $catalogue->GetDisplayName () . '</option>';
		}
		$this->mPage .= '</select></div></form>';
		
	} // End CatalogueChoice
	
	function LoadDateRange() {
		$this->mPage .= <<<EOT
EOT;
	} // End DateRange


}

set_time_limit(300);
$page = new AdminReportView2();
echo $page->LoadDefault();

?>