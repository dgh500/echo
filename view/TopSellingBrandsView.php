<?php

//! Loads the home page
class TopSellingBrandsView extends View {

	//! The catalogue to load for
	var $mCatalogue;
	//! Settings to do with the catalogue such as whether to display different components
	var $mSystemSettings;
	//! Deals with managing the basket and any session variables
	var $mSessionHelper;
	//! Holds HTML code for public viewing
	var $mPublicLayoutHelper;
	//! ID of the current basket
	var $mBasketId;

	//! Constructor, sets some member variables based on the catalogue
	function __construct($catalogue) {
		parent::__construct ('Top Brands | Echo Supplements');
		$this->mCatalogue = $catalogue;
		$this->mSessionHelper 		= new SessionHelper ( );
		$this->mPublicLayoutHelper 	= new PublicLayoutHelper ( );
		$this->mSystemSettings 		= new SystemSettingsModel ( $this->mCatalogue );
		$this->mBasketId 			= $this->mSessionHelper->GetSessionId ();
		$this->mManufacturerController	= new ManufacturerController;
	}

	//! Main page load function
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

	//! Loads the horizontal navigation bar
	function LoadTopBrands() {
		$topBrandsView = new TopBrandsView;
		$this->mPage .= $topBrandsView->LoadDefault($this->mCatalogue);
	}

	//! Loads the centre column
	function LoadMainContentColumn() {
		$topBrands = $this->mManufacturerController->GetTopNManufacturersFor($this->mCatalogue,20);
		$numOneBrand = array_shift($topBrands);

		// Short top bit
		$this->mPage .= <<<EOT
			<br />
			<img src="{$this->mBaseDir}/images/headingTopBrands.gif" style="float: none;" />
			<p>The following brands are our top selling brands and at Echo Supplements we recommend them to anyone looking for great results!</p>
			<hr />
EOT;
		// Num.1 Brand
		$url = $this->mPublicLayoutHelper->LoadManufacturerHref($numOneBrand);
		$this->LoadManufacturerDescriptionSection($numOneBrand);

		// Display all packages
		foreach($topBrands as $brand) {
			if($this->mManufacturerController->CountProductsIn($brand) > 1) {
				$url = $this->mPublicLayoutHelper->LoadManufacturerHref($brand);
				$this->LoadManufacturerDescriptionSection($brand);
			}
		}
	} // End LoadMainContentColumn

	//! Load a single manufacturer
	function LoadManufacturerDescriptionSection($manufacturer) {
		if ($manufacturer && $manufacturer->GetSizeChart ()) {
			$sizeChart = $manufacturer->GetSizeChart ();
			$href = $this->mBaseDir . '/content/' . $sizeChart->GetContentId () . '/' . $this->mValidationHelper->MakeLinkSafe ( $manufacturer->GetDisplayName () );
		} else {
			$href = $this->mBaseDir . '/brand/' . $this->mValidationHelper->MakeLinkSafe ( $manufacturer->GetDisplayName () ) . '/' . $manufacturer->GetManufacturerId ();
		}
		$this->mPage .= '<div id="manufacturerDescriptionContainer">';
		$this->mPage .= '<a href="' . $href . '">';
		$this->mPage .= $this->mPublicLayoutHelper->ManufacturerImage ( $manufacturer );
		$this->mPage .= '</a>';
		$this->mPage .= '<div id="manufacturerDescriptionText">';
		$this->mPage .= nl2br(substr($manufacturer->GetDescription(),0,200));
		$this->mPage .= '...[<a href="'.$href.'">more '.$manufacturer->GetDisplayName().'</a>]</div><!-- Close manufacturerDescriptionText -->';
		$this->mPage .= '</div><!-- Close manufacturerDescriptionContainer --><hr />';
	} // End LoadManufacturerDescriptionSection

} // End TopSellingBrandsView


?>