<?php

//! View that defines the interface for adding and removing products from different categories, should be called by AdminProductView
class CategoriesView extends View {
	
	//! Int : The catalogue which the view should look in for the upgrades, used by CategorySelectorView. 
	// This is not a catalogue object because it is not used by CategoriesView, rather passed to CategorySelectorView
	var $mCatalogeId;
	//! Obj:ProductModel - The product that is currently being edited
	// This is needed to 'check' the correct box when displaying the list of categories
	var $mProduct;
	
	//! Generic load function
	/*!
	 * @param [in] catalogueId - The catalogue used by the CategorySelectorView interface within the form
	 * @return String - The code for the view
	 */
	function LoadDefault($catalogueId, $product) {
		$this->mCatalogueId = $catalogueId;
		$this->mProduct = $product;
		$this->InitialiseDisplay ();
		$this->LoadCategorySelector ();
		$this->LoadCurrentCategories ();
		$this->CloseDisplay ();
		return $this->mPage;
	}
	
	//! Adds heading and opens a DIV with id categoryViewContainer
	function InitialiseDisplay() {
		$this->mPage .= '<strong>Add Category</strong>
						<div id="categoryViewContainer">';
	}
	
	//! Loads a CategorySelectorView interface, using categoriesList as the DIV to write the output to, and CATEGORY as the prefix
	function LoadCategorySelector() {
		$CategorySelectorView = new CategorySelectorView ( );
		$this->mPage .= $CategorySelectorView->LoadDefault ( $this->mCatalogueId, 'categoriesList', 'CATEGORY', $this->mProduct );
	}
	
	//! Closes the categoryViewContainer DIV (assuming that all DIVs in between are well formed)
	function CloseDisplay() {
		$this->mPage .= '</div>';
	}
	
	//! Writes a heading, assumes categoriesList DIV is below it (should be declared in AdminProductView :: LoadCategoriesDisplay())
	// The DIV is declared in AdminProductView->LoadCategoriesDisplay() because it must contain previously entered products
	function LoadCurrentCategories() {
		$this->mPage .= '<br /><strong>Current Categories</strong><br /><br />';
	}

}

?>
