<?php

//! View that defines the interface for similar products, should be called by AdminProductView
class SimilarView extends View {
	
	//! Int : The catalogue which the view should look in for the simlar products, used by MacFinderView. 
	// This is not a catalogue object because it is not used by SimilarView, rather passed to MacFinderView
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
		$this->LoadCurrentSimilar ();
		$this->CloseDisplay ();
		return $this->mPage;
	}
	
	//! Adds heading and opens a DIV with id similarViewContainer
	function InitialiseDisplay() {
		$this->mPage .= '<strong>Add Similar</strong>
						<div id="similarViewContainer">';
	}
	
	//! Closes the similarViewContainer DIV (assuming that all DIVs in between are well formed)
	function CloseDisplay() {
		$this->mPage .= '</div>';
	}
	
	//! Loads a MacFinder interface, using similarList as the DIV to write the output to, and SIMILAR as the prefix
	function LoadMacFinder() {
		$similarFinder = new MacFinderView ( );
		$this->mPage .= $similarFinder->LoadDefault ( $this->mCatalogueId, 'similarList', 'SIMILAR' );
	}
	
	//! Writes a heading, assumes similarList DIV is below it (should be declared in AdminProductView :: LoadCrossSellDisplay())
	// The DIV is declared in AdminProductView->LoadCrossSellDisplay() because it must contain previously entered products
	function LoadCurrentSimilar() {
		$this->mPage .= '<br /><strong>Current Similar (You may also be interested in...)</strong><br /><br />';
	}

}

?>
