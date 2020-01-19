<?php

// Defines the clearance section
class ClearanceView extends View {
	
	var $mSystemSettings;
	var $mSessionHelper;
	
	//! Constructor
	function __construct($catalogue) {
		// Params
		$this->mCatalogue = $catalogue;
		
		// CSS Extras 
		$cssIncludes = array('ClearanceView.css.php','Category.css.php');	

		// Constructor
		parent::__construct($this->mCatalogue->GetDisplayName () . ' > Clearance - All Stock Must Go!',$cssIncludes);
		
		// Init
		$this->mSystemSettings 		= new SystemSettingsModel ( $this->mCatalogue );
		$this->mSessionHelper 		= new SessionHelper ( );
	} // End __construct();
	
	//! Generic load function
	function LoadDefault() {
		$footerView = new FooterView ( );
		$this->mPage .= $this->mPublicLayoutHelper->OpenBody ();
		$this->mPage .= $this->mPublicLayoutHelper->AddTopRelativeAnchor ();
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
	} // End LoadDefault
	
	//! Loads main column
	function LoadMainContentColumn() {
		$this->mPage .= $this->mPublicLayoutHelper->OpenMainColumn ();
		$this->mPage .= $this->LoadClearance ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseMainColumn ();
	} // End LoadMainContentColumn()
	
	//! Load the clearance list
	function LoadClearance() {
		// Initialise
		$productController = new ProductController ( );
		$clearanceProducts = $productController->GetClearance ( $this->mCatalogue );
				
		// Start Display
		$this->mPage .= '<div id="clearanceContainer">';
		$this->mPage .= '<br /><img border="0" src="'.$this->mBaseDir.'/images/clearanceBanner.jpg" alt="Clearance Offer" /><br />';
		
		foreach ( $clearanceProducts as $product ) {
			$categoryListProductView = new CategoryListProductView ( );
			$categories = $product->GetCategories ();
			$category = $categories [0];
			$this->mPage .= $categoryListProductView->LoadDefault ( $product, $category, $this->mSessionHelper->GetBasket ()->GetBasketId () );
		}
		$this->mPage .= '</div> <!-- Close clearanceContainer -->
		';
	} // End LoadClearance
	
	//! Loads the right column, including RightColView
	function LoadRightColumn() {
		$rightColView = new RightColView ( $this->mCatalogue, $this->mSessionHelper );
		$this->mPage .= $rightColView->LoadDefault ();
	}
} // End ClearanceView

?>