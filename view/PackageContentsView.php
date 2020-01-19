<?php

//! View that defines the interface for package contents, should be called by AdminPackageView
class PackageContentsView extends View {
	
	//! Int : The catalogue which the view should look in for the package contents, used by MacFinderView. 
	// This is not a catalogue object because it is not used by PackageContentsView, rather passed to MacFinderView
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
	
	//! Adds heading and opens a DIV with id packageContentsViewContainer
	function InitialiseDisplay() {
		$this->mPage .= '<strong>Add Product</strong>
						<div id="packageContentsViewContainer">';
	}
	
	//! Closes the packageContentsViewContainer DIV (assuming that all DIVs in between are well formed)
	function CloseDisplay() {
		$this->mPage .= '</div>';
	}
	
	//! Loads a MacFinder interface, using packageContentsList as the DIV to write the output to, and PACKAGECONTENTS as the prefix
	function LoadMacFinder() {
		$upgradeFinder = new MacFinderView ( );
		$this->mPage .= $upgradeFinder->LoadDefault ( $this->mCatalogueId, 'packageContentsList', 'PACKAGECONTENTS' );
	}
	
	//! Writes a heading, assumes packageContentsList DIV is below it (should be declared in AdminPackageView :: LoadContentsDisplay())
	// The DIV is declared in AdminPackageView->LoadContentsDisplay() because it must contain previously entered products
	function LoadCurrentContents() {
		$this->mPage .= '<br /><strong>Current Contents</strong><br /><br />';
	}

}

?>
