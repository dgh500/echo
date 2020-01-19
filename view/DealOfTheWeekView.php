<?php

//! Generates the code for the deal of the week box
class DealOfTheWeekView extends View {
	
	//! Initialises the View parent class
	function __construct() {
		parent::__construct ();
	}
	
	//! Loads a default offer of the week (at random) into the deal of the week container
	/*!
	 * @param $catalogue - Obj:CatalogueModel - The catalogue to load from
	 * @param $basketId  - Obj:BasketModel    - The basket ID (for the 'Buy Now' button)
	 * @return - The code for the box
	 */
	function LoadDefault($catalogue, $basketId) {
		// Initialise catalogue and basket
		$this->mCatalogue = $catalogue;
		$this->mBasketId = $basketId;
		$this->mSystemSettings = new SystemSettingsModel($this->mCatalogue);
		// Only allow choice between packages and products (at 50% random) if they are enabled AND there is a package there
		if ($this->mSystemSettings->GetShowPackages () && $this->mCatalogue->HasDealOfTheWeekPackages()) {
			$chooseProduct = rand ( 0, 1 );
		} else {
			$chooseProduct = 1;
		}
		// Load a product or a package
		if ($chooseProduct) {
			$this->LoadProduct ();
		} else {
			$this->LoadPackage ();
		}
		return $this->mPage;
	} // End LoadDefault();
	
	//! Load a product as deal of the week
	function LoadProduct() {
		// Initialise
		$productController = new ProductController ( );
		// Get all of the offers of the week (randomly sorted)
		$dealOfTheWeekProducts = $productController->GetOffersOfTheWeek ( $this->mCatalogue, 1 );
		// If there aren't any offers of the week then say so, otherwise choose one
		if (count ( $dealOfTheWeekProducts ) == 0) {
			$this->LoadNoOffers ();
		} else {
			// Pick the first one
			$dealOfTheWeekProduct = $dealOfTheWeekProducts[0];
			if ($dealOfTheWeekProduct) {
				$linkTo = $this->mPublicLayoutHelper->LoadLinkHref ( $dealOfTheWeekProduct );
				$atts = $dealOfTheWeekProduct->GetAttributes ();
				if (count ( $atts ) == 0) {
					$buyNowBit = '<form action="' . $this->mBaseDir . '/formHandlers/AddProductToBasketHandler.php" method="post" onsubmit="return validateForm(this)">
								<div>
								<input type="hidden" name="referPage" id="referPage" value="dealOfTheWeek" />					
								<input type="hidden" name="addToBasket" id="addToBasket" value="1" />
								<input type="hidden" name="productId" id="productId" value="' . $dealOfTheWeekProduct->GetProductId () . '" />
								<input type="hidden" name="basketId" id="basketId" value="' . $this->mBasketId . '" />
								<input type="image" src="' . $this->mBaseDir . '/images/buyNowButton.gif" id="dealOfTheWeekBuyNowButton" />
								</div>
								</form>';
				} else {
					$buyNowBit = '<a href="' . $linkTo . '"><img src="' . $this->mBaseDir . '/images/viewButton.gif" id="dealOfTheWeekBuyNowButton" alt="View ' . $dealOfTheWeekProduct->GetDisplayName () . '" /></a>';
				}
				if (intval ( $dealOfTheWeekProduct->GetWasPrice () ) != 0) {
					$wasPriceBit = '<h6 id="dealOfTheWeekProductWasPrice">WAS £' . $this->mPresentationHelper->Money ( $dealOfTheWeekProduct->GetWasPrice () ) . '</h6>';
				} else {
					$wasPriceBit = '';
				}
				if (is_object ( $dealOfTheWeekProduct )) {
					$this->mPage .= <<<HTMLOUTPUT
					
			<div id="dealOfTheWeekContainer">
				<div id="dealOfTheWeekTitle">
					<h2>DEAL OF THE WEEK</h2>
				</div> <!-- Close dealOfTheWeekTitle -->
				<div id="dealOfTheWeekContent">
					<h3 id="dealOfTheWeekProductName"><a href="{$linkTo}">{$this->mPresentationHelper->ChopDown($dealOfTheWeekProduct->GetDisplayName(),50)}</a></h3>
					<div id="dealOfTheWeekProductDescription">
						{$this->mPresentationHelper->ChopDown($dealOfTheWeekProduct->GetDescription(),150)}
					</div>
					<a href="{$linkTo}">{$this->mPublicLayoutHelper->MediumProductImage($dealOfTheWeekProduct,'dealOfTheWeekProductImage')}</a>
					{$wasPriceBit}
					<h5 id="dealOfTheWeekProductNowPrice">NOW £{$this->mPresentationHelper->Money($dealOfTheWeekProduct->GetActualPrice())}</h5>
					{$buyNowBit}
				</div> <!-- Close dealOfTheWeekContent -->
			</div> <!-- Close dealOfTheWeekContainer -->
HTMLOUTPUT;
				}
			} // 
		} // End if no products selected for deal of the week
	} // End LoadProduct()
	
	//! Load a package as the deal of the week
	function LoadPackage() {
		// Init
		$packageController 		= new PackageController ( );
		// Get a package
		$dealOfTheWeekPackages 	= $packageController->GetOffersOfTheWeek ( $this->mCatalogue, 1 );
		// Choose the first one (GetOffersOfTheWeek returns an array)
		$dealOfTheWeekPackage 	= $dealOfTheWeekPackages [0];
		// If it is valid (ie. Not == false) then display the page
		if ($dealOfTheWeekPackage) {
			$linkTo = $this->mPublicLayoutHelper->LoadPackageLinkHref ( $dealOfTheWeekPackage );
			$buyNowBit = '<a href="' . $linkTo . '"><img src="' . $this->mBaseDir . '/images/viewButton.gif"  id="dealOfTheWeekBuyNowButton" alt="View ' . $dealOfTheWeekPackage->GetDisplayName () . '" /></a>';
			if (intval ( $dealOfTheWeekPackage->GetWasPrice () ) != 0) {
				$wasPriceBit = '<h6 id="dealOfTheWeekProductWasPrice">WAS £' . $this->mPresentationHelper->Money ( $dealOfTheWeekPackage->GetWasPrice () ) . '</h6>';
			} else {
				$wasPriceBit = '';
			}
			if (is_object ( $dealOfTheWeekPackage )) {
				$packageName = substr ( $dealOfTheWeekPackage->GetDisplayName (), 0, 45 );
				$this->mPage .= <<<HTMLOUTPUT
				
			<div id="dealOfTheWeekContainer">
				<div id="dealOfTheWeekTitle">
					<h2>DEAL OF THE WEEK</h2>
				</div> <!-- Close dealOfTheWeekTitle -->
				<div id="dealOfTheWeekContent">
					<h3 id="dealOfTheWeekProductName"><a href="{$linkTo}">{$packageName}</a></h3>
					<div id="dealOfTheWeekProductDescription">
						{$dealOfTheWeekPackage->GetDescription()}
					</div>
					<a href="{$linkTo}">{$this->mPublicLayoutHelper->MediumPackageImage($dealOfTheWeekPackage,'dealOfTheWeekProductImage')}</a>
					{$wasPriceBit}
					<h5 id="dealOfTheWeekProductNowPrice">NOW £{$this->mPresentationHelper->Money($dealOfTheWeekPackage->GetActualPrice())}</h5>
					{$buyNowBit}
				</div> <!-- Close dealOfTheWeekContent -->
			</div> <!-- Close dealOfTheWeekContainer -->
HTMLOUTPUT;
			}
		}
	} // End LoadPackage()
	
	//! If Deal of the Week is enabled but there are no eligable products or packages..
	function LoadNoOffers() {
		$this->mPage .= <<<HTMLOUTPUT
					
						<div id="dealOfTheWeekContainer">
							<div id="dealOfTheWeekTitle">
								<h2>DEAL OF THE WEEK</h2>
							</div> <!-- Close dealOfTheWeekTitle -->
							<div id="dealOfTheWeekContent">
								<h3 id="dealOfTheWeekProductName">No Deals Yet</h3>
							</div> <!-- Close dealOfTheWeekContent -->
						</div> <!-- Close dealOfTheWeekContainer -->
HTMLOUTPUT;
	} // End LoadNoOffers

} // End DealOfTheWeekView

?>