<?php

//! Generates the code for the best seller section of the category page
class CategoryPackageListBestSellerView extends View {

	//! Initialises the View parent class
	function __construct() {
		parent::__construct ();
	}

	//! Loads the best selling package in the category supplied
	/*!
	 * @param $catalogue - Obj:CategoryModel 	- The category to load from
	 * @param $basketId  - Obj:BasketModel    	- The basket ID (for the 'Buy Now' button)
	 * @return - The code for the box
	 */
	function LoadDefault($category, $basketId) {
		// Initialise catalogue and basket
		$this->mCategory = $category;
		$this->mBasketId = $basketId;
		$this->LoadPackage();
		return $this->mPage;
	} // End LoadDefault();

	//! Load a package as best seller
	function LoadPackage() {
		// Initialise
		$packageController = new PackageController( );
		$package = $packageController->GetBestSellingPackageForCategory($this->mCategory);

		// Do we have free delivery in this catalogue?
		switch ($this->mCategory->GetCatalogue ()->GetPricingModel ()->GetPricingModelId ()) {
			case 1 :
				// Regular - If no manual postage, then free delivery :)
				if("0.0" == $package->GetPostage()) {
					$freeDelivery = ' FREE DELIVERY!';
				} else {
					$freeDelivery = '';
				}
				break;
			case 2 :
				// Shooting
				$freeDelivery = '';
				break;
		}

		// Pick the first one
		if($package) {
			$linkTo = $this->mPublicLayoutHelper->LoadPackageLinkHref($package);
			if(is_object($package)) {
				// Load HTML code
				$this->mPage .= <<<HTMLOUTPUT
		<div id="bestSellerContainer">
			<div id="bestSellerContent">
				<h3 id="bestSellerName"><a href="{$linkTo}">{$this->mPresentationHelper->ChopDown($package->GetDisplayName(),50)}</a></h3>
					<table><tr><td valign="top" align="center">
						<a href="{$linkTo}">{$this->mPublicLayoutHelper->MediumPackageImage($package,'bestSellerProductImage')}</a>
					</td></tr></table>
				<p>{$this->mPresentationHelper->ChopDown ( $package->GetDescription (), 120, 1 )}</p>
				<h5 id="bestSellerWasPrice">WAS &pound;{$this->mPresentationHelper->Money($package->GetWasPrice())} {$freeDelivery}</h5>
				<h5 id="bestSellerNowPrice">NOW &pound;{$this->mPresentationHelper->Money($package->GetActualPrice())} {$freeDelivery}</h5>
			</div> <!-- Close bestSellerContent -->
		</div> <!-- Close bestSellerContainer -->
HTMLOUTPUT;
			}
		} //
	} // End LoadProduct()

} // End CategoryPackageListBestSellerView

?>