<?php

class TagView extends View {
	
	var $mTag;
	var $mSystemSettings;
	var $mSessionHelper;
	var $mSortBy;
	var $mSortDirection;
	var $mCategory;
	
	//! Loads the initial values
	/*! 
	 * @param $tagId			- Int - The tag ID
	 * @param $sortBy			- Str - What to sort the products by (price/name)
	 * @param $sortDirection	- Str - The direction of the sort (ASC/DESC)
	 * @param $page				- Int - The page number we are on
	 * @param $categoryId		- Int - The category ID, if the user has clicked on one (Path 'By Brand' -> 'Category'). If zero (false) then assumed none
	 * @param $showAll			- Boo - Whether or not we are showing 'all' in the current page or doing an X-per-page result set
	 * @return 					- Void
	 */
	function __construct($tagId, $sortBy, $sortDirection, $page, $categoryId = false, $showAll = false) {		
		$this->mTag 			= new TagModel($tagId); // The tag to load the display for
		$this->mTagController 	= new TagController(); // Controller to get the products in the tag
		$this->mCatalogue 		= $this->mTag->GetCatalogue (); // The catalogue the tag is in
		$this->mSystemSettings 	= new SystemSettingsModel ( $this->mCatalogue ); // A system settings object for the catalogue
		$this->mSessionHelper 	= new SessionHelper ( ); // Session helper class
		$this->mPublicLayoutHelper = new PublicLayoutHelper ( ); // Public layout class to ensure consistent design
		$this->mPageNum 		= $page; // Which page we are currently on
		$this->mSortByString 	= '/sortBy/' . $sortBy . '/' . $sortDirection; // The 'sort by' string which is built up to pass on to the pagination view
		// Includes
		$cssIncludes = array('Category.css.php','Manufacturer.css.php');
		$jsIncludes  = array('ManufacturerView.js');
	
		parent::__construct($this->mCatalogue->GetDisplayName().' > Shop By '.$this->mSystemSettings->GetShopByTagDescription().' > '.$this->mTag->GetDisplayName(),$cssIncludes,$jsIncludes);		
		// If a category ID is supplied then the user has clicked a subcategory of the tag => Only display those products in this category
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
			// all of the products that tag makes
			if ($this->mDisplayCategory) {
				$this->mProductsPerPage = $this->mTagController->CountProductsIn ( $this->mTag, $this->mCategory );
			} else {
				$this->mProductsPerPage = $this->mTagController->CountProductsIn ( $this->mTag );
			}
		} else {
			$this->mProductsPerPage = 9;
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
				$this->mSortDirection = 'ASC';
				break;
		}
	}
	
	//! Loads the page for the shop-by-brand view
	function LoadDefault() {
		$footerView = new FooterView ( );
		$this->mPage .= $this->mPublicLayoutHelper->OpenBody ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenCentrePageContainer ();
		parent::LoadHeaderSection($this->mCatalogue);
		// Three col container
		$this->mPage .= $this->mPublicLayoutHelper->OpenLayoutContainers ();
		// Left Col
		parent::LoadLeftColumn($this->mCatalogue);
		// Centre Col
		$this->mPage .= $this->mPublicLayoutHelper->OpenCentreColumn ();
		$this->LoadMainContentColumn ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseCentreColumn ();
		// Right Col
		$this->LoadRightColumn ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseLayoutContainers ();
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
		$breadCrumbView = new TagBreadCrumbView ( );
		$this->mPage .= $this->mPublicLayoutHelper->OpenMainColumn ();
		$this->LoadTagDescriptionSection ();
		$this->mPage .= '	<div id="manufacturerViewListContainer">
								<div id="manufacturerViewListContainerTitle">';
		$this->mPage .= $breadCrumbView->LoadDefault ( $this->mTag );
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
											<option value="sortByPriceLowest" ' . $sortByPriceAscSelected . '>Sort By Price - Lowest First</option>
											<option value="sortByPriceHighest" ' . $sortByPriceDescSelected . '>Sort By Price - Highest First</option>
											<option value="sortByNameAsc" ' . $sortByNameAscSelected . '>Sort By Name - A-Z</option>
											<option value="sortByNameDesc" ' . $sortByNameDescSelected . '>Sort By Name - Z-A</option>
											<option value="showAll" ' . $showAllSelected . '>Show All</option>
										</select>
									</div>
								</div>';
		if ($this->mDisplayCategory) {
			$paginationView = new TagPaginationView ( $this->mTag, $this->mPageNum, $this->mProductsPerPage, $this->mCategory, $this->mSortByString );
			$paginationViewBottom = new TagPaginationView ( $this->mTag, $this->mPageNum, $this->mProductsPerPage, $this->mCategory, $this->mSortByString );
			$this->mPage .= $paginationView->LoadDefault ();
			$this->LoadProducts ();
			$this->mPage .= $paginationViewBottom->LoadDefault ();
		} else {
			$paginationView = new TagPaginationView ( $this->mTag, $this->mPageNum, $this->mProductsPerPage, false, $this->mSortByString );
			$paginationViewBottom = new TagPaginationView ( $this->mTag, $this->mPageNum, $this->mProductsPerPage, false, $this->mSortByString );
			$this->mPage .= $paginationView->LoadDefault ();
			#$this->LoadSubCategoryList ();
			$this->LoadAllProducts ();
			$this->mPage .= $paginationViewBottom->LoadDefault ();
		}
		$this->mPage .= '</div>';
		$this->mPage .= $this->mPublicLayoutHelper->CloseMainColumn ();
	} // End LoadMainContentColumn()
	

	function LoadTagDescriptionSection() {
		$this->mPage .= '<div id="manufacturerDescriptionContainer">';
		$this->mPage .= $this->mPublicLayoutHelper->TagImage($this->mTag);
		$this->mPage .= '<div id="manufacturerDescriptionText">';
		$this->mPage .= $this->mTag->GetDescription ();
		$this->mPage .= '</div><!-- Close manufacturerDescriptionText -->';
		$this->mPage .= '</div><!-- Close manufacturerDescriptionContainer -->';
	}
	
	function LoadProducts() {
		$allProducts = $this->mTagController->GetProductsIn ( $this->mTag, $this->mProductsPerPage, $this->mSortBy, $this->mSortDirection, $this->mPageNum, $this->mCategory );
		foreach ( $allProducts as $product ) {
			$categoryListProductView = new CategoryListProductView ( );
			$this->mPage .= $categoryListProductView->LoadDefault ( $product, $this->mCategory, $this->mSessionHelper->GetBasket ()->GetBasketId () );
		}
	}
	
	function LoadAllProducts() {
		$allProducts = $this->mTagController->GetProductsIn ( $this->mTag, $this->mProductsPerPage, $this->mSortBy, $this->mSortDirection, $this->mPageNum );
		foreach ( $allProducts as $product ) {
			$categoryListProductView = new CategoryListProductView ( );
			$categories = $product->GetCategories ();
			$category = $categories [0];
			$this->mPage .= $categoryListProductView->LoadDefault ( $product, $category, $this->mSessionHelper->GetBasket ()->GetBasketId () );
		}
	}
	
	function LoadSubCategoryList() {
		$tagCategoryListView = new TagCategoryListView ( );
		$this->mPage .= $tagCategoryListView->LoadDefault ( $this->mTag );
	}

	//! Loads the right column, including RightColView
	function LoadRightColumn() {
		$rightColView = new RightColView ( $this->mCatalogue, $this->mSessionHelper );
		$this->mPage .= $rightColView->LoadDefault ();
	}
} // End TagView

?>