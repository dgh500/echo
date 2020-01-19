<?php

class PostalRatesView extends View {
	
	var $mSystemSettings;
	var $mSessionHelper;
	
	// Init
	function __construct($catalogue) {
		// Params
		$this->mCatalogue = $catalogue;
		
		// Includes
		$cssIncludes = array('PostalRatesView.css.php');
		
		// Construct 
		parent::__construct($this->mCatalogue->GetDisplayName().' Postal Rates',$cssIncludes);
		
		$this->mPricingModel 	= $this->mCatalogue->GetPricingModel();
		$this->mContent 		= $this->mPricingModel->GetContent();
		$this->mSystemSettings 	= new SystemSettingsModel($this->mCatalogue);
		$this->mSessionHelper 	= new SessionHelper();
	}
	
	function LoadDefault() {
		$footerView = new FooterView ( );
		$this->mPage .= $this->mPublicLayoutHelper->OpenBody ();
		$this->mPage .= $this->mPublicLayoutHelper->AddTopRelativeAnchor ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenCentrePageContainer ();
		parent::LoadHeaderSection ($this->mCatalogue);
		$this->mPage .= $this->mPublicLayoutHelper->OpenLayoutContainers ();
		parent::LoadLeftColumn ($this->mCatalogue);
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
		$this->LoadPostalRates ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseMainColumn ();
	} // End LoadMainContentColumn()
	

	function LoadPostalRates() {
		$this->mPage .= $this->mContent->GetLongText ();
	}

	function LoadHeaderSection() {
		#$productSearch = new ProductSearchView ( );
		$this->mPage .= $this->mPublicLayoutHelper->OpenHeader ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenHeaderLeft ();
		$this->mPage .= $this->mPublicLayoutHelper->HeaderLogo ( $this->mCatalogue->GetUrl (), $this->mCatalogue->GetDisplayName () );
		#$this->mPage .= $productSearch->LoadDefault ( $this->mCatalogue );
		$this->mPage .= $this->mPublicLayoutHelper->CloseHeaderLeft ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenHeaderMid ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseHeaderMid ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenHeaderRight ();
		$this->LoadAccountNavigation ();
		$this->mPage .= $this->mPublicLayoutHelper->HeaderRightImages ();
		#$this->LoadOtherSites ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseHeaderRight ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseHeader ();
	} // End LoadHeaderSection()
	

	function LoadLeftColumn() {
		$shopByDeptView = new ShopByDepartmentView ( );
		$this->mPage .= $this->mPublicLayoutHelper->OpenLeftCol ();
		$this->mPage .= $shopByDeptView->LoadDefault ( $this->mCatalogue );
		$this->mPage .= $this->mPublicLayoutHelper->CloseLeftCol ();
	} // End LoadLeftColumn()
	

	//! Loads the right column, including RightColView
	function LoadRightColumn() {
		$rightColView = new RightColView ( $this->mCatalogue, $this->mSessionHelper );
		$this->mPage .= $rightColView->LoadDefault ();
	}
} // End PostalRatesView

?>