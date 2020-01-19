<?php

//! View that defines the interface for related products, should be called by AdminProductView
class RelatedView extends View {
	
	//! Int : The catalogue which the view should look in for the related products, used by MacFinderView. 
	// This is not a catalogue object because it is not used by RelatedView, rather passed to MacFinderView
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
		$this->LoadCurrentRelated ();
		$this->CloseDisplay ();
		return $this->mPage;
	}
	
	//! Adds heading and opens a DIV with id relatedViewContainer
	function InitialiseDisplay() {
		$this->mPage .= '<strong>Add Related</strong>
						<div id="relatedViewContainer">';
	}
	
	//! Closes the relatedViewContainer DIV (assuming that all DIVs in between are well formed)
	function CloseDisplay() {
		$this->mPage .= '</div>';
	}
	
	//! Loads a MacFinder interface, using relatedList as the DIV to write the output to, and RELATED as the prefix
	function LoadMacFinder() {
		$relatedFinder = new MacFinderView ( );
		$this->mPage .= $relatedFinder->LoadDefault ( $this->mCatalogueId, 'relatedList', 'RELATED' );
	}
	
	//! Writes a heading, assumes relatedList DIV is below it (should be declared in AdminProductView :: LoadCrossSellDisplay())
	// The DIV is declared in AdminProductView->LoadCrossSellDisplay() because it must contain previously entered products
	function LoadCurrentRelated() {
		$this->mPage .= '<br /><strong>Current Related (Customers who bought this item also bought...)</strong><br /><br />';
	}

}

?>