<?php
//! Defines the page for a given brand
class ManufacturerView extends View {

	var $mManufacturer;
	var $mSessionHelper;
	var $mSortBy;
	var $mSortDirection;
	var $mCategory;

	//! Loads the initial values
	/*!
	 * @param $manufacturerId	- Int - The manufacturer ID
	 * @param $sortBy			- Str - What to sort the products by (price/name)
	 * @param $sortDirection	- Str - The direction of the sort (ASC/DESC)
	 * @param $page				- Int - The page number we are on
	 * @param $categoryId		- Int - The category ID, if the user has clicked on one (Path 'By Brand' -> 'Category'). If zero (false) then assumed none
	 * @param $showAll			- Boo - Whether or not we are showing 'all' in the current page or doing an X-per-page result set
	 * @return 					- Void
	 */
	function __construct($manufacturerId, $sortBy, $sortDirection, $page, $categoryId = false, $showAll = false) {
		try {
			$this->mManufacturer 			= new ManufacturerModel ( $manufacturerId ); // The manufacturer to load the display for
		} catch(Exception $e) {
			echo '<img src="http://www.echosupplements.com/images/echoWatermarkLarge.jpg" /><br />';
			echo '<p style="font-family: Arial, Sans-Serif; font-size: 14pt;">Sorry this page does not exist, redirecting you to www.echosupplements.com please wait...</p>';
			echo '<script type="text/javascript">
			<!--
			setTimeout("top.location.href = \'http://www.echosupplements.com\'",4000);
			//-->
			</script>';
			die();

		}
		$this->mManufacturerController 	= new ManufacturerController ( ); // Controller to get the products in the manufacturer
		$this->mCatalogue 				= $this->mManufacturer->GetCatalogue (); // The catalogue the manufacturer is in
		$this->mSessionHelper 			= new SessionHelper ( ); // Session helper class
		$this->mPageNum 				= $page; // Which page we are currently on
		$this->mSortByString 			= '/sortBy/' . $sortBy . '/' . $sortDirection; // The 'sort by' string which is built up to pass on to the pagination view

		// CSS/JS Includes
		#$cssIncludes = array('Category.css.php','Manufacturer.css.php');
		$cssIncludes = array();
		$jsIncludes  = array('ManufacturerView.js');

		// META Info
		$title = $this->mManufacturer->GetDisplayName().' | '.$this->mCatalogue->GetDisplayName();
		$metaDescription = $this->mManufacturer->GetDisplayName().' : '.$this->mManufacturer->GetDescription();

		// Construct!
		parent::__construct($title,$cssIncludes,$jsIncludes,$metaDescription);
		// If a category ID is supplied then the user has clicked a subcategory of the manufacturer => Only display those products in this category
		if ($categoryId) {
			$this->mDisplayCategory = true;
			$this->mCategory = new CategoryModel ( $categoryId );
			$this->mCategoryController = new CategoryController ( );
		} else {
			$this->mDisplayCategory = false;
		}
		// Show either the default (9) number of products per page or the maximum amount in the brand/subcategory
		if ($showAll) {
			// If displaying a category (user has clicked a subcategory of the brand) then get the number of products in this category with the brand, otherwise just get
			// all of the products that manufacturer makes
			if ($this->mDisplayCategory) {
				$this->mProductsPerPage = $this->mManufacturerController->CountProductsIn ( $this->mManufacturer, $this->mCategory );
			} else {
				$this->mProductsPerPage = $this->mManufacturerController->CountProductsIn ( $this->mManufacturer );
			}
		} else {
			$this->mProductsPerPage = 12;
		}
		// Convert the accepted sort by inputs to database field names (defaults to price)
		switch ($sortBy) {
			case 'price' :
				$this->mSortBy = 'Actual_Price';
				break;
			case 'name' :
				$this->mSortBy = 'Display_Name';
				break;
			default :
				$this->mSortBy = 'Actual_Price';
				break;
		}
		// Convert the accepted sort by directions to SQL (defaults to ASC)
		switch ($sortDirection) {
			case 'asc' :
				$this->mSortDirection = 'ASC';
				break;
			case 'desc' :
				$this->mSortDirection = 'DESC';
				break;
			default :
				$this->mSortDirection = 'DESC';
				break;
		}
	}

	//! Loads the page for the shop-by-brand view
	function LoadDefault() {
		$footerView = new FooterView ( );
		$this->mPage .= $this->mPublicLayoutHelper->OpenBody ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenCentrePageContainer ();
		parent::LoadHeaderSection($this->mCatalogue);
		parent::LoadNavigation();
		parent::LoadLeftColumn ($this->mCatalogue);
		$this->mPage .= $this->mPublicLayoutHelper->OpenRightCol ();
		$this->LoadMainContentColumn ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseRightCol ();
		// Close three col container
		$this->mPage .= $this->mPublicLayoutHelper->OpenFooterContainer ();
		$this->mPage .= $footerView->LoadDefault ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseFooterContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseCentrePageContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseBody ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseHtml ();
		return $this->mPage;
	} // End LoadDefault()


	function LoadMainContentColumn() {
		$breadCrumbView = new ManufacturerBreadCrumbView ( );
		$this->mPage .= $this->mPublicLayoutHelper->OpenMainColumn ();
		$this->LoadManufacturerDescriptionSection ();
		$this->mPage .= '	<div id="manufacturerViewListContainer">
								<div id="manufacturerViewListContainerTitle">';
		$this->mPage .= $breadCrumbView->LoadDefault ( $this->mManufacturer );
		$queryString = $_SERVER ['QUERY_STRING'];
		// Sort by name desc
		if (strpos ( $queryString, 'name' ) && strpos ( $queryString, 'desc' )) {
			$sortByNameDescSelected = 'selected';
		} else {
			$sortByNameDescSelected = '';
		}
		// Sort by name asc
		if (strpos ( $queryString, 'name' ) && strpos ( $queryString, 'asc' )) {
			$sortByNameAscSelected = 'selected';
		} else {
			$sortByNameAscSelected = '';
		}
		// Sort by price desc
		if (strpos ( $queryString, 'price' ) && strpos ( $queryString, 'desc' )) {
			$sortByPriceDescSelected = 'selected';
		} else {
			$sortByPriceDescSelected = '';
		}
		// Sort by price asc
		if (strpos ( $queryString, 'price' ) && strpos ( $queryString, 'asc' )) {
			$sortByPriceAscSelected = 'selected';
		} else {
			$sortByPriceAscSelected = '';
		}
		// Show All
		if (strpos ( $queryString, 'showAll' )) {
			$showAllSelected = 'selected';
		} else {
			$showAllSelected = '';
		}
		$this->mPage .= '		<div id="sortByContainer">
										<select name="sortBy" id="sortBy" onChange="changeSortBy(this)">
											<option value="sortByPriceHighest" ' . $sortByPriceDescSelected . '>Sort By Price - Highest First</option>
											<option value="sortByPriceLowest" ' . $sortByPriceAscSelected . '>Sort By Price - Lowest First</option>
											<option value="sortByNameAsc" ' . $sortByNameAscSelected . '>Sort By Name - A-Z</option>
											<option value="sortByNameDesc" ' . $sortByNameDescSelected . '>Sort By Name - Z-A</option>
											<option value="showAll" ' . $showAllSelected . '>Show All</option>
										</select>
									</div>
								</div> <!-- End manufacturerViewListContainerTitle -->';
		if ($this->mDisplayCategory) {
			$paginationViewBottom = new ManufacturerPaginationView ( $this->mManufacturer, $this->mPageNum, $this->mProductsPerPage, $this->mCategory, $this->mSortByString );
			$this->LoadProducts ();
			$this->mPage .= $paginationViewBottom->LoadDefault ();
		} else {
			$paginationViewBottom = new ManufacturerPaginationView ( $this->mManufacturer, $this->mPageNum, $this->mProductsPerPage, false, $this->mSortByString );
			$this->LoadSubCategoryList ();
			$this->LoadAllProducts ();
			$this->mPage .= $paginationViewBottom->LoadDefault ();
			$this->LoadManufacturerFooterSection();
		}
		$this->mPage .= '</div> <!-- End manufacturerViewListContainer -->';
		$this->mPage .= $this->mPublicLayoutHelper->CloseMainColumn ();
	} // End LoadMainContentColumn()

	function LoadManufacturerFooterSection() {
		$this->mPage .= '<div id="manufacturerFooterContainer">';
		$this->mPage .= $this->mPublicLayoutHelper->ManufacturerImage ( $this->mManufacturer );
		$this->mPage .= '<div id="manufacturerDescriptionText">';
		$this->mPage .= '<h1>'.$this->mManufacturer->GetDisplayName().'</h1>';
		$this->mPage .= $this->mManufacturer->GetDescription($stripTags=false);
		$this->mPage .= '</div><!-- Close manufacturerDescriptionText -->';
		$this->mPage .= '</div><!-- Close manufacturerFooterContainer -->';
		$this->mPage .= '<br style="clear: both" />';
	}

	function LoadManufacturerDescriptionSection() {
		// If they have a banner set, then show it!
		if (!$this->mDisplayCategory) {
			if($this->mManufacturer->GetBannerUrl() != '') {
				$this->mPage .= '<div id="manufacturerDescriptionContainer">';
				$this->mPage .= '<img src="'.$this->mBaseDir.'/manufacturerImages/'.$this->mManufacturer->GetBannerUrl().'" />';
				$this->mPage .= '</div><!-- Close manufacturerDescriptionContainer -->';
			}
		}
	}

	function LoadProducts() {
		$allProducts = $this->mManufacturerController->GetProductsIn ( $this->mManufacturer, $this->mProductsPerPage, $this->mSortBy, $this->mSortDirection, $this->mPageNum, $this->mCategory );
		foreach ( $allProducts as $product ) {
			$categoryListProductView = new CategoryListProductView ( );
			$this->mPage .= $categoryListProductView->LoadDefault ( $product, $this->mCategory, $this->mSessionHelper->GetBasket ()->GetBasketId () );
		}
	}

	function LoadAllProducts() {
		$allProducts = $this->mManufacturerController->GetProductsIn ( $this->mManufacturer, $this->mProductsPerPage, $this->mSortBy, $this->mSortDirection, $this->mPageNum );
		foreach ( $allProducts as $product ) {
			$categoryListProductView = new CategoryListProductView ( );
			$categories = $product->GetCategories ();
			$category = $categories [0];
			$this->mPage .= $categoryListProductView->LoadDefault ( $product, $category, $this->mSessionHelper->GetBasket ()->GetBasketId () );
		}
	}

	function LoadSubCategoryList() {
		$manufacturerCategoryListView = new ManufacturerCategoryListView ( );
		$this->mPage .= $manufacturerCategoryListView->LoadDefault ( $this->mManufacturer );
	}
} // End ManufacturerView

?>