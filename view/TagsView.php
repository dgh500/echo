<?php

class TagsView extends View {
	
	var $mSystemSettings;
	var $mSessionHelper;
	
	function __construct($catalogue) {
		// Params
		$this->mCatalogue = $catalogue;
		$this->mSystemSettings = new SystemSettingsModel ( $this->mCatalogue );
		
		// Includes
		$cssIncludes = array('Category.css');
		
		// Constructor
		parent::__construct ($this->mCatalogue->GetDisplayName () . ' > All '.$this->mSystemSettings->GetShopByTagDescription(),$cssIncludes);
		
		// Member Vars
		$this->mSessionHelper  = new SessionHelper ( );
		$this->mTagController  = new TagController ( );
	}
	
	function LoadDefault() {
		$footerView = new FooterView ( );
		$this->mPage .= $this->mPublicLayoutHelper->OpenBody ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenCentrePageContainer ();
		parent::LoadHeaderSection($this->mCatalogue);
		$this->mPage .= $this->mPublicLayoutHelper->OpenLayoutContainers ();
		parent::LoadLeftColumn($this->mCatalogue);
		$this->mPage .= $this->mPublicLayoutHelper->OpenCentreColumn ();
		$this->LoadMainContentColumn ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseCentreColumn ();
		$this->LoadRightColumn ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseLayoutContainers ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenFooterContainer ();
		$this->mPage .= $footerView->LoadDefault ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseFooterContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseCentrePageContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseBody ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseHtml ();
		return $this->mPage;
	}
	
	function LoadMainContentColumn() {
		$this->mPage .= $this->mPublicLayoutHelper->OpenMainColumn ();
		$this->LoadTags ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseMainColumn ();
	} // End LoadMainContentColumn()
	

	function LoadTags() {
		$publicLayoutHelper = new PublicLayoutHelper ( );
		$this->mPage .= '<h1>Shop By '.$this->mSystemSettings->GetShopByTagDescription().'</h1>';
		$allTags = $this->mTagController->GetAllTagsFor ( $this->mCatalogue, false );
		foreach ( $allTags as $tag ) {
			$linkTo = $this->mBaseDir . '/tag/' . $this->mValidationHelper->MakeLinkSafe ( trim ( $tag->GetDisplayName () ) ) . '/' . $tag->GetTagId ();
			$this->mPage .= '<div class="brandContainer">
								<a href="' . $linkTo . '">';
			$this->mPage .= $publicLayoutHelper->TagImage( $tag );
			$this->mPage .= '</a>
								<h3><a href="' . $linkTo . '">' . $tag->GetDisplayName () . '</a></h3>
							</div> <!-- Close brandContainer -->';
		}
	}

	//! Loads the right column, including RightColView
	function LoadRightColumn() {
		$rightColView = new RightColView ( $this->mCatalogue, $this->mSessionHelper );
		$this->mPage .= $rightColView->LoadDefault ();
	}
} // End TagsView

?>