<?php

class PaginationView extends View {
	
	function __construct() {
		parent::__construct ();
		$this->mCategoryController = new CategoryController ( );
	}
	
	//! Loads the pagination part of the category page
	/*! 
	 * @param $category 		- Obj : CategoryModel - The category page
	 * @param $pageNum 			- Int - The current page number
	 * @param $productsPerPage 	- Int - The number of products per page
	 * @return - The xhtml for the page as a string
	 */
	function LoadDefault($category, $pageNum, $productsPerPage) {
		// The end number of the products (So can show $start...$end of $totalProds)
		$end = $pageNum * $productsPerPage;
		// The start number (So can show $start...$end of $totalProds)
		$start = $end - $productsPerPage + 1;
		// Load either the products or packages in the category
		if ($category->GetPackageCategory ()) {
			$totalProds = $this->mCategoryController->CountPackagesIn ( $category );
		} else {
			$totalProds = $this->mCategoryController->CountProductsIn ( $category );
		}
		// For example if there are 14 products, and 9 per page, then the end would be 18 when only 14 exist - this chops it off
		if ($end > $totalProds) {
			$end = $totalProds;
		}
		$numberOfPages = ceil ( $totalProds / $productsPerPage );
		$pageSelection = 'Page: ';
		$numberOfPages ++;
		// Build up the link
		if ($category->GetParentCategory ()) {
			$parentCategoryPart = '/' . $this->mValidationHelper->MakeLinkSafe ( $category->GetParentCategory ()->GetDisplayName () ) . '/' . $category->GetParentCategory ()->GetCategoryId () . '';
		} else {
			$parentCategoryPart = '';
		}
		$categoryPart = '/' . $this->mValidationHelper->MakeLinkSafe ( $category->GetDisplayName () ) . '/' . $category->GetCategoryId () . '';
		
		for($i = 1; $i < $numberOfPages; $i ++) {
			if ($pageNum == $i) {
				$pageSelection .= '<a href="' . $this->mBaseDir . '/department' . $parentCategoryPart . $categoryPart . '/page/' . $i . '" class="currentPageNumber">' . $i . '</a> ';
			} else {
				$pageSelection .= '<a href="' . $this->mBaseDir . '/department' . $parentCategoryPart . $categoryPart . '/page/' . $i . '">' . $i . '</a> ';
			}
		}
		
		$this->mPage .= <<<HTMLOUTPUT
			<div id="pageNumbersContainer">
				<span style="float: left;">Showing {$start}..{$end} of {$totalProds} products</span>
				<span style="float: right;">{$pageSelection}</span>
			</div>	
HTMLOUTPUT;
		return $this->mPage;
	}

}

?>