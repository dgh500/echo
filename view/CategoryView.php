<?php
//! Defines the category listing view
class CategoryView extends View {

	var $mCategory;
	var $mSystemSettings;
	var $mSessionHelper;
	var $mSortBy;
	var $mSortDirection;

	//! Loads up the setup variables
	/*!
	 * @param $categoryId - Int -  The category to display a list of (from query string)
	 * @param $sortBy - String 	- What to sort by (price,name)
	 * @param $sortDirection 	- String - How to sort (asc,desc)
	 * @param $page	- Int
	 * @param $showAll			- Boolean - Whether or not to override the products per page and just show them all
	 * @return Void
	 */
	function __construct($categoryId, $sortBy, $sortDirection, $page, $showAll) {
		// Initialise variables
		$this->mCategory 			= new CategoryModel($categoryId);
		$this->mPageNum 			= $page;
		$this->mCategoryController 	= new CategoryController();
		$this->mProductController	= new ProductController();
		$this->mCatalogue 			= $this->mCategory->GetCatalogue();

		// Member Variables
		$this->mSystemSettings = new SystemSettingsModel ( $this->mCatalogue );
		$this->mSessionHelper 	= new SessionHelper ( );

		// CSS Extra
		$cssIncludes = array();
		$jsIncludes = array('CategoryView.js');

		// META Info
		$title = $this->mCategory->GetDisplayName().' | '.$this->mCatalogue->GetDisplayName();
		$metaDescription = '';
		$metaDescription = $this->mCategory->GetDisplayName().' : '.strip_tags($this->mCategory->GetDescription());

		// Construct!
		parent::__construct($title,$cssIncludes,$jsIncludes,$metaDescription);

		// Are we showing all of the products/packages
		if ($showAll) {
			if ($this->mCategory->GetPackageCategory ()) {
				$this->mProductsPerPage = $this->mCategoryController->CountPackagesIn ( $this->mCategory );
			} else {
				$this->mProductsPerPage = $this->mCategoryController->CountProductsIn ( $this->mCategory );
			}
		} else {
			$this->mProductsPerPage = 12;
		}

		// Sorting by what?
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

		// And in which direction?
		switch ($sortDirection) {
			case 'asc' :
				$this->mSortDirection = 'ASC';
				break;
			case 'desc' :
				$this->mSortDirection = 'DESC';
				break;
			default :
				if($this->mCategory->GetDisplayName() == 'CLEARANCE') {
					$this->mSortDirection = 'DESC';
				} else {
					$this->mSortDirection = 'DESC';
				}
				break;
		}
	} // End __construct

	//! Generic loader
	function LoadDefault() {
		$footerView = new FooterView();
		$this->mPage .= $this->mPublicLayoutHelper->OpenBody ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenCentrePageContainer ();
		parent::LoadHeaderSection($this->mCatalogue);
		parent::LoadNavigation();
		parent::LoadLeftColumn ($this->mCatalogue);
		$this->mPage .= $this->mPublicLayoutHelper->OpenRightCol ();
		$this->LoadMainContentColumn ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseRightCol ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenFooterContainer ();
		$this->mPage .= $footerView->LoadDefault ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseFooterContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseCentrePageContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseBody ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseHtml ();
		return $this->mPage;
	} // End LoadDefault()

	//! Load the central column
	function LoadMainContentColumn() {
		$breadCrumbView = new CategoryBreadCrumbView ( );
		$paginationView = new PaginationView ( );
		$paginationViewBottom = new PaginationView ( );
		// Category Description
		$this->LoadCategoryDescriptionSection();
		// Category Contents
		$this->mPage .= '	<div id="categoryViewListContainer">
								<div id="categoryViewListContainerTitle">';
		$this->mPage .= $breadCrumbView->LoadDefault ( $this->mCategory );
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

		// Show page
		$this->mPage .= '
									<div id="sortByContainer">
										<select name="sortBy" id="sortBy" onChange="changeSortBy(this)">
											<option value="sortByPriceHighest" ' . $sortByPriceDescSelected . '>Sort By Price - Highest First</option>
											<option value="sortByPriceLowest" ' . $sortByPriceAscSelected . '>Sort By Price - Lowest First</option>
											<option value="sortByNameAsc" ' . $sortByNameAscSelected . '>Sort By Name - A-Z</option>
											<option value="sortByNameDesc" ' . $sortByNameDescSelected . '>Sort By Name - Z-A</option>
											<option value="showAll" ' . $showAllSelected . '>Show All</option>
										</select>
									</div>
								</div>';
#		$this->mPage .= $paginationView->LoadDefault ( $this->mCategory, $this->mPageNum, $this->mProductsPerPage );
		$this->LoadSubCategoryList ();

		// Load product/package best seller and featured product
		if(!$this->mCategory->GetPackageCategory()) {
			// Load Best Seller & Brand New Boxes if a top level category
			if($this->mCategory->GetParentCategory() === NULL) {
				if($this->mProductController->GetAnyProductInCategory($this->mCategory)) {
					$this->LoadBestSellerAndBrandNew();
				}
			}
			// If a parent category and there are products to display
			if($this->mCategory->GetParentCategory() == NULL && $this->mCategoryController->CountProductsIn($this->mCategory) > 0) {
				// Load featured product
				$this->LoadFeaturedProduct();
			}
		} else {
			$this->LoadPackageBestSellerAndBrandNew();
		}

		// Load category contents
		$this->LoadProducts ();
		$this->mPage .= $paginationViewBottom->LoadDefault ( $this->mCategory, $this->mPageNum, $this->mProductsPerPage );
		$this->mPage .= '</div>';
	} // End LoadMainContentColumn()

	//! Load the brand new and best seller sections for a product category
	function LoadBestSellerAndBrandNew() {
		$categoryListBestSellerView = new CategoryListBestSellerView();
		$this->mPage .= $categoryListBestSellerView->LoadDefault($this->mCategory,$this->mSessionHelper->GetBasket()->GetBasketId());
		$categoryListBestSellerView = new CategoryListBrandNewView();
		$this->mPage .= $categoryListBestSellerView->LoadDefault($this->mCategory,$this->mSessionHelper->GetBasket()->GetBasketId());
	} // End LoadBestSellerAndBrandNew

	//! Load the brand new and best seller sections for a package category
	function LoadPackageBestSellerAndBrandNew() {
		$categoryListBestSellerView = new CategoryPackageListBestSellerView();
		$this->mPage .= $categoryListBestSellerView->LoadDefault($this->mCategory,$this->mSessionHelper->GetBasket()->GetBasketId());

		$categoryListBestSellerView = new CategoryPackageListBrandNewView();
		$this->mPage .= $categoryListBestSellerView->LoadDefault($this->mCategory,$this->mSessionHelper->GetBasket()->GetBasketId());
	} // End LoadPackageBestSellerAndBrandNew


	//! Loads the featured product for this category (if there is one)
	function LoadFeaturedProduct() {
		$categoryListFeaturedProductView = new CategoryListFeaturedProductView();
		$this->mPage .= $categoryListFeaturedProductView->LoadDefault($this->mCategory,$this->mSessionHelper->GetBasket()->GetBasketId());
	} // End LoadFeaturedProduct

	function LoadCategoryDescriptionSection() {
		$this->mPage .= '<div id="categoryDescriptionContainer">';
		$this->mPage .= '<div id="categoryDescriptionText">';
		$this->mPage .= $this->mCategory->GetDescription();
		$this->mPage .= '</div><!-- Close categoryDescriptionText -->';
		$this->mPage .= '</div><!-- Close categoryDescriptionContainer -->';
	}

	//! Load products in the category
	function LoadProducts() {
		if ($this->mCategory->GetPackageCategory ()) {
			$allPackages = $this->mCategoryController->GetTopLevelCategoryPackages ( $this->mCategory, $this->mProductsPerPage, $this->mSortBy, $this->mSortDirection, $this->mPageNum );
			foreach ( $allPackages as $package ) {
				$categoryListPackageView = new CategoryListPackageView ( );
				$this->mPage .= $categoryListPackageView->LoadDefault ( $package, $this->mCategory, $this->mSessionHelper->GetBasket ()->GetBasketId () );
			}
		} else {
			$allProducts = $this->mCategoryController->GetTopLevelCategoryProducts ( $this->mCategory, $this->mProductsPerPage, $this->mSortBy, $this->mSortDirection, $this->mPageNum );
			foreach ( $allProducts as $product ) {
				$categoryListProductView = new CategoryListProductView ( );
				$this->mPage .= $categoryListProductView->LoadDefault ( $product, $this->mCategory, $this->mSessionHelper->GetBasket ()->GetBasketId () );
			}
		}
	} // End LoadProducts

	//! Loads the possible sub categories
	function LoadSubCategoryList() {
		$subCategoryListView = new SubCategoryListView ( );
		$this->mPage .= $subCategoryListView->LoadDefault ( $this->mCategory );
	} // End LoadSubCategoryList

} // End CategoryView

?>