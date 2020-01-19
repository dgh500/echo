<?php

class TagPaginationView extends View {
	
	//! Sets up the internal variables
	/*! 
	 * @param $tag				- Obj : TagModel
	 * @param $pageNum 			- Int - The current page number
	 * @param $productsPerPage 	- Int - The number of products per page
	 * @param $category 		- Obj : CategoryModel - If the tag view has been constrained by category then Obj : CategoryModel
	 * @param $sortByString		- Str - The existing sort by string, if it exists
	 * @return - The xhtml for the page as a string
	 */
	function __construct($tag, $pageNum, $productsPerPage, $category = false, $sortByString = '') {
		parent::__construct ();
		$this->mTag = $tag;
		$this->mCategory = $category;
		$this->mPageNum = $pageNum;
		$this->mProductsPerPage = $productsPerPage;
		$this->mSortByString = $sortByString;
		$this->mTagController = new TagController ( );
	}
	
	//! Loads the pagination part of the category page
	function LoadDefault() {
		// The end number of the products (So can show $start...$end of $totalProds)
		$this->mEndNumber = $this->mPageNum * $this->mProductsPerPage;
		// The start number (So can show $start...$end of $totalProds)
		$this->mStartNumber = $this->mEndNumber - $this->mProductsPerPage + 1;
		// Load either the drilled down catgeory/tag combo, or all products the tagged
		if ($this->mCategory) {
			$this->mTotalProducts = $this->mTagController->CountProductsIn ( $this->mTag, $this->mCategory );
		} else {
			$this->mTotalProducts = $this->mTagController->CountProductsIn ( $this->mTag );
		}
		// For example if there are 14 products, and 9 per page, then the end would be 18 when only 14 exist - this chops it off
		if ($this->mEndNumber > $this->mTotalProducts) {
			$this->mEndNumber = $this->mTotalProducts;
		}
		$this->mNumberOfPages = ceil ( $this->mTotalProducts / $this->mProductsPerPage );
		$this->mPageSelection = 'Page: ';
		$this->mNumberOfPages ++;
		
		$parentCategoryPart = '';
		$categoryPart = '';
		$totalCategoryPart = '';
		
		if ($this->mCategory) {
			// Build up the link
			if ($this->mCategory->GetParentCategory ()) {
				$parentCategoryPart = '/' . $this->mValidationHelper->MakeLinkSafe ( $this->mCategory->GetParentCategory ()->GetDisplayName () ) . '/' . $this->mCategory->GetParentCategory ()->GetCategoryId () . '';
			}
			$categoryPart = '/' . $this->mValidationHelper->MakeLinkSafe ( $this->mCategory->GetDisplayName () ) . '/' . $this->mCategory->GetCategoryId () . '';
		}
		$tagPart = $this->mValidationHelper->MakeLinkSafe ( trim ( $this->mTag->GetDisplayName () ) ) . '/' . $this->mTag->GetTagId ();
		
		if ($categoryPart != '') {
			$totalCategoryPart = '/department' . $parentCategoryPart . $categoryPart;
		}
		
		for($i = 1; $i < $this->mNumberOfPages; $i ++) {
			if ($this->mPageNum == $i) {
				$this->mPageSelection .= '<a href="' . $this->mBaseDir . '/tag/' . $tagPart . $totalCategoryPart . '/page/' . $i . $this->mSortByString . '" class="currentPageNumber">' . $i . '</a> ';
			} else {
				$this->mPageSelection .= '<a href="' . $this->mBaseDir . '/tag/' . $tagPart . $totalCategoryPart . '/page/' . $i . $this->mSortByString . '">' . $i . '</a> ';
			}
		}
		
		$this->mPage .= <<<HTMLOUTPUT
			<div id="pageNumbersContainer">
				<span style="float: left;">Showing {$this->mStartNumber}..{$this->mEndNumber} of {$this->mTotalProducts} products</span>
				<span style="float: right;">{$this->mPageSelection}</span>
			</div>	
HTMLOUTPUT;
		return $this->mPage;
	}

}

?>