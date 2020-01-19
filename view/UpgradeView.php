<?php

//! View that defines the interface for product upgrades, should be called by AdminProductView
class UpgradeView extends View {
	
	//! Int : The catalogue which the view should look in for the upgrades, used by MacFinderView. 
	// This is not a catalogue object because it is not used by UpgradeView, rather passed to MacFinderView
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
		$this->LoadCurrentUpgrades ();
		$this->CloseDisplay ();
		return $this->mPage;
	}
	
	//! Adds heading and opens a DIV with id upgradeViewContainer
	function InitialiseDisplay() {
		$this->mPage .= '<strong>Add Upgrade</strong>
						<div id="upgradeViewContainer">';
	}
	
	//! Closes the upgradeViewContainer DIV (assuming that all DIVs in between are well formed)
	function CloseDisplay() {
		$this->mPage .= '</div>';
	}
	
	//! Loads a MacFinder interface, using upgradeList as the DIV to write the output to, and UPGRADE as the prefix
	function LoadMacFinder() {
		$upgradeFinder = new MacFinderView ( );
		$this->mPage .= $upgradeFinder->LoadDefault ( $this->mCatalogueId, 'upgradeList', 'UPGRADE' );
	}
	
	//! Writes a heading, assumes upgradeList DIV is below it (should be declared in AdminProductView :: LoadUpgradesDisplay())
	// The DIV is declared in AdminProductView->LoadUpgradesDisplay() because it must contain previously entered products
	function LoadCurrentUpgrades() {
		$this->mPage .= '<br /><strong>Current Upgrades</strong><br /><br />';
	}

}

?>
