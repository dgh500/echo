<?php

class SearchView extends View {

	var $mSessionHelper;

	function __construct($catalogue,$q) {
		// Params
		$this->mCatalogue 	= $catalogue;
		$this->mQ			= $q;

		// Construct
		parent::__construct($this->mCatalogue->GetDisplayName().' Search');
		$this->mSessionHelper 	= new SessionHelper ();
	}

	function LoadDefault() {
		$footerView = new FooterView ( );
		$this->mPage .= $this->mPublicLayoutHelper->OpenBody ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenCentrePageContainer ();
		parent::LoadHeaderSection($this->mCatalogue);
		parent::LoadNavigation();
		parent::LoadLeftColumn ();
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
	}

	function LoadMainContentColumn() {
		$this->LoadSearchResults();
	} // End LoadMainContentColumn()


	function LoadSearchResults() {
		$this->mPage .= '<h1>Search Results For "'.$this->mQ.'"</h1>';
		$pController = new ProductController;
		foreach($pController->SearchByName($this->mQ) as $product) {
			$url = $this->mPublicLayoutHelper->LoadLinkHref($product);
			$this->mPage .= '<div style="float: left;"><a href="'.$url.'">'.$this->mPublicLayoutHelper->SmallProductImage($product).'</a></div>';
			$this->mPage .= '<h2 style="margin-bottom: 0px; padding-bottom: 0px;"><a href="'.$url.'">'.$product->GetDisplayName().'</a></h2>';
			$this->mPage .= '<p style="margin-top: 0px; padding-top: 0px;">'.substr(strip_tags($product->GetDescription()),0,150).'...</p>';
			$this->mPage .= '<br style="clear: both" />';
		}
	}

	//! Loads the right column, including RightColView
	function LoadRightColumn() {
		$rightColView = new RightColView ( $this->mCatalogue, $this->mSessionHelper );
		$this->mPage .= $rightColView->LoadDefault ();
	}
} // End SearchView

?>