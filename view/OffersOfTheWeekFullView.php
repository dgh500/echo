<?php

class OffersOfTheWeekFullView extends View {
	
	var $mSessionHelper;
	
	function __construct($catalogue) {
		// Params
		$this->mCatalogue = $catalogue;
		
		// Includes
		$cssIncludes = array('OffersOfTheWeekFullView.css.php','Category.css.php');
		
		// Construct
		parent::__construct($this->mCatalogue->GetDisplayName().' > Offers of the Week!',$cssIncludes);
		
		// Member vars
		$this->mSessionHelper = new SessionHelper ( );
	}
	
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
	}
	
	function LoadMainContentColumn() {
		$this->mPage .= $this->mPublicLayoutHelper->OpenMainColumn ();
		$this->mPage .= $this->LoadOffers ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseMainColumn ();
	} // End LoadMainContentColumn()
	

	function LoadOffers() {
		// Initialise
		$productController = new ProductController ( );
		$productsOfTheWeek = $productController->GetOffersOfTheWeek ( $this->mCatalogue );
		
		$packageController = new PackageController ( );
		$packagesOfTheWeek = $packageController->GetOffersOfTheWeek ( $this->mCatalogue );
		$publicLayoutHelper = new PublicLayoutHelper ( );
		
		// Start Display
		$this->mPage .= '<div id="offersOfTheWeekFullContainer">';
		$this->mPage .= '<h1 id="offersOfTheWeekLogo"><span></span>Offers of the Week</h1>';
		
		foreach ( $productsOfTheWeek as $product ) {
			$categoryListProductView = new CategoryListProductView ( );
			$categories = $product->GetCategories ();
			$category = $categories [0];
			$this->mPage .= $categoryListProductView->LoadDefault ( $product, $category, $this->mSessionHelper->GetBasket ()->GetBasketId () );
		}
		
		foreach ( $packagesOfTheWeek as $package ) {
			$categoryListPackageView = new CategoryListPackageView ( );
			$category = $package->GetParentCategory ();
			$this->mPage .= $categoryListPackageView->LoadDefault ( $package, $category, $this->mSessionHelper->GetBasket ()->GetBasketId () );
		}
		$this->mPage .= '</div> <!-- Close offersOfTheWeekFullContainer -->
		';
	}
	
	//! Loads the right column, including RightColView
	function LoadRightColumn() {
		$rightColView = new RightColView ( $this->mCatalogue, $this->mSessionHelper );
		$this->mPage .= $rightColView->LoadDefault ();
	}
} // End OffersOfTheWeekFullView

?>