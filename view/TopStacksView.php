<?php

//! Loads the home page
class TopStacksView extends View {

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
		parent::__construct ('Echo Supplements | Top Supplement Packages');
		$this->mCatalogue = $catalogue;
		$this->mSessionHelper 		= new SessionHelper ( );
		$this->mPublicLayoutHelper 	= new PublicLayoutHelper ( );
		$this->mSystemSettings 		= new SystemSettingsModel ( $this->mCatalogue );
		$this->mBasketId 			= $this->mSessionHelper->GetSessionId ();
		$this->mPackageController	= new PackageController;
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
		$topPackages = $this->mPackageController->GetBestSellingPackages(3);
		$numOnePackage = array_shift($topPackages);
		$url = $this->mPublicLayoutHelper->LoadPackageLinkHref($numOnePackage);

		// Build HTML
		$this->mPage .= '
			<div id="topStackContainer">
				<div id="topStackHeading"><h2><a href="'.$url.'">'.$numOnePackage->GetDisplayName().'</a></h2></div>
				<img src="'.$this->mBaseDir.'/images/no1Stack.jpg" id="leftBanner" />
				<img src="'.$this->mBaseDir.'/images/no1StackRight.jpg" id="rightBanner" />
				<img src="'.$this->mBaseDir.'/images/echoWatermarkLarge.jpg" id="watermark" />
				<a href="'.$url.'">
					'.$this->mPublicLayoutHelper->LargePackageImage($numOnePackage,'numOneStackImage').'
				</a>
				<div id="numOneProductName">'.$numOnePackage->GetDisplayName().'</div>
				<div id="numOneDescription">'.$this->mPresentationHelper->Chopdown(strip_tags($numOnePackage->GetLongDescription()),400).'</div>
				<div id="wasPrice">WAS &pound;'.$numOnePackage->GetWasPrice().'</div>
				<div id="nowPrice">NOW &pound;'.$numOnePackage->GetActualPrice().'</div>
				<img id="secure" src="'.$this->mBaseDir.'/images/100secure.png" />
				<a href="'.$url.'">
					<input type="image" id="button" src="'.$this->mBaseDir.'/images/viewButton.png" />
				</a>
			</div> <!-- End topStackContainer -->
		';

		// Display all packages
		foreach($topPackages as $package) {
			$url = $this->mPublicLayoutHelper->LoadPackageLinkHref($package);
			$this->mPage .= '
			<div class="topStackSubContainer">
				<div class="topStackSubHeading"><h2><a href="'.$url.'">'.$package->GetDisplayName().'</a></h2></div>
				<div class="topStackImage">
				<a href="'.$url.'">
					'.$this->mPublicLayoutHelper->MediumPackageImage($package).'
				</a>
				</div>
				<div class="numOneProductName">'.$package->GetDisplayName().'</div>
				<div class="numOneDescription">'.$this->mPresentationHelper->Chopdown(strip_tags($package->GetLongDescription()),200).'</div>
				<div class="wasPrice">WAS &pound;'.$package->GetWasPrice().'</div>
				<div class="nowPrice">NOW &pound;'.$package->GetActualPrice().'</div>
				<img class="secure" src="'.$this->mBaseDir.'/images/100secure.png" />
				<a href="'.$url.'">
					<input type="image" class="button" src="'.$this->mBaseDir.'/images/viewButton.png" />
				</a>
			</div> <!-- End topStackSubContainer -->
			';
		}


	} // End LoadMainContentColumn

} // End TopStacksView


?>