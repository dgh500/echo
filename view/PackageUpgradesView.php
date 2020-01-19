<?php

//! View that defines the interface for package upgrades, should be called by AdminPackageView
class PackageUpgradesView extends View {
	
	//! Int : The catalogue which the view should look in for the package upgrades, used by MacFinderView. 
	// This is not a catalogue object because it is not used by PackageUpgradesView, rather passed to MacFinderView
	var $mCatalogueId;
	
	//! Generic load function
	/*!
	 * @param [in] catalogueId - The catalogue used by the MacFinder interface within the form
	 * @return String - The code for the view
	 */
	function LoadDefault($catalogueId, $prefix) {
		$this->mCatalogueId = $catalogueId;
		$this->mPrefix = $prefix;
		$this->InitialiseDisplay ();
		$this->LoadMacFinder ( $this->mCatalogueId );
		$this->LoadCurrentUpgrades ();
		$this->CloseDisplay ();
		return $this->mPage;
	}
	
	//! Adds heading and opens a DIV with id packageUpgradesViewContainer
	function InitialiseDisplay() {
		$this->mPage .= '<strong>Add Product</strong>
						<div id="' . $this->mPrefix . 'packageUpgradesViewContainer">';
	}
	
	//! Closes the packageUpgradesViewContainer DIV (assuming that all DIVs in between are well formed)
	function CloseDisplay() {
		$this->mPage .= '</div>';
	}
	
	//! Loads a MacFinder interface, using packageUpgradesList as the DIV to write the output to, and PACKAGEUPGRADES as the prefix
	function LoadMacFinder() {
		$upgradeFinder = new MacFinderView ( );
		$this->mPage .= $upgradeFinder->LoadDefault ( $this->mCatalogueId, $this->mPrefix . 'packageUpgradesList', $this->mPrefix . 'PACKAGEUPGRADES', 'packageUpgrade' );
	}
	
	//! Writes a heading, assumes packageUpgradesList DIV is below it (should be declared in AdminPackageView :: LoadUpgradesDisplay())
	// The DIV is declared in AdminPackageView->LoadUpgradesDisplay() because it must contain previously entered products
	function LoadCurrentUpgrades() {
		$this->mPage .= '';
	}

}

?>
