<?php

//! View that defines the interface for order contents, should be called by AddOrderView.php
class OrderContentsView extends View {
	
	//! Int : The catalogue which the view should look in for the order contents, used by MacFinderView. 
	// This is not a catalogue object because it is not used by OrderContentsView, rather passed to MacFinderView
	var $mCatalogeId;
	
	//! Generic load function
	/*!
	 * @param [in] catalogueId - The catalogue used by the MacFinder interface within the form
	 * @return String - The code for the view
	 */
	function LoadDefault($catalogueId) {
		$this->mCatalogueId = $catalogueId;
		$this->InitialiseDisplay ();
		$this->LoadMacFinder ( $this->mCatalogueId );
		$this->LoadCurrentContents ();
		$this->CloseDisplay ();
		return $this->mPage;
	}
	
	//! Adds heading and opens a DIV with id orderContentsViewContainer
	function InitialiseDisplay() {
		$this->mPage .= '<strong>Add Product</strong>
						<div id="orderContentsViewContainer">';
	}
	
	//! Closes the orderContentsViewContainer DIV (assuming that all DIVs in between are well formed)
	function CloseDisplay() {
		$this->mPage .= '</div>';
	}
	
	//! Loads a MacFinder interface, using orderContentsList as the DIV to write the output to, and ORDERCONTENTS as the prefix
	function LoadMacFinder() {
		$productFinder = new MacFinderView ( );
		$this->mPage .= $productFinder->LoadDefault ( $this->mCatalogueId, 'orderContentsList', 'ORDERCONTENTS', 'orderForm', true );
	}
	
	//! Writes a heading, assumes orderContentsList DIV is below it (should be declared in AddOrderView :: LoadContentsDisplay())
	// The DIV is declared in AddOrderView->LoadContentsDisplay() because it must contain previously entered products
	function LoadCurrentContents() {
		$this->mPage .= '<br /><strong>Current Basket</strong>
		<div id="orderFormHeading">
			<div id="productName">Product Name</div>
			<div id="productQuantity">Quantity</div>
			<div id="productPrice">Price</div>
		</div>';
	}

}

?>
