<?php
//! Defines the display for a single package on the package listing view
class CategoryListPackageView extends View {

	function __construct() {
		parent::__construct();
	}

	//! Generic load function
	/*!
	 * @param $package  - PackageModel	- Which package to display
	 * @param $category - CategoryModel	- The category being displayed
	 * @param $basketId - Int - 		- The current basket ID
	 */
	function LoadDefault($package,$category,$basketId) {
		// Member vars
		$this->mPackage  = $package;
		$this->mBasketId = $basketId;
		$this->mCategory = $category;

		// Top level or not?
		if($this->mCategory->GetParentCategory()) {
			$this->mParentCategory = $this->mCategory->GetParentCategory ();
		}
		// Does it have a WAS price?
		$packageController = new PackageController ( );
		if ($this->mPackage->GetWasPrice () == 0) {
			$wasPriceSection = '';
		} else {
			$wasPriceSection = '<div class="wasPrice">Was &pound;' . $this->mPresentationHelper->Money ( $this->mPackage->GetWasPrice () ) . '</div>';
		}
		// If top level, need to include it in the link
		// Format: BASE_DIR/packages/CATEGORY_NAME/package/PACKAGE_NAME/PACKAGE_ID
		if ($this->mCategory->GetParentCategory ()) {
			$parentCategoryPart = '/'.$this->mValidationHelper->MakeLinkSafe ( $this->mCategory->GetParentCategory ()->GetDisplayName () );
		} else {
			$parentCategoryPart = '';
		}
		// Build up the link to include the package
		if(!$this->mCategory->Contains($this->mPackage)) {
			$childCategoryPart = '/'. $this->mValidationHelper->MakeLinkSafe ( $packageController->GetLinkCategory ( $this->mPackage, $this->mCategory )->GetDisplayName () );
		} else {
			$childCategoryPart = '';
		}

		// Display the page
		$this->mPage .= '
		<div class="categoryViewProductContainer">
			<div class="categoryViewProductImageContainer">
			<a href="
				'.$this->mBaseDir.
				$parentCategoryPart.'
				/'.$this->mValidationHelper->MakeLinkSafe($category->GetDisplayName()).
				$childCategoryPart.'
				/package/'.$this->mValidationHelper->MakeLinkSafe($package->GetDisplayName()).'
				/'.$package->GetPackageId().'
			">';
		$this->mPage .= $this->mPublicLayoutHelper->MediumPackageImage($package );
		$this->mPage .= '
			</a>
			</div>
			<div class="productDetailsContainer">
				<h3><a href="' . $this->mBaseDir . '' . $parentCategoryPart . '/' . $this->mValidationHelper->MakeLinkSafe ( $category->GetDisplayName () ) . '' . $childCategoryPart . '/package/' . $this->mValidationHelper->MakeLinkSafe ( $package->GetDisplayName () ) . '/' . $package->GetPackageId () . '">' . $package->GetDisplayName () . '</a></h3>
				<div class="prices">';
		$this->mPage .= $this->LoadPrices ();
		$this->mPage .= '
				</div>
				<div class="description">
					' . $this->mPresentationHelper->ChopDown ( $package->GetDescription (), 130, 1 ) . '
				</div>
				<div class="categoryViewButtonsContainer">';
		$this->mPage .= '<a href="' . $this->mBaseDir . '' . $parentCategoryPart . '/' . $this->mValidationHelper->MakeLinkSafe ( $category->GetDisplayName () ) . '' . $childCategoryPart . '/package/' . $this->mValidationHelper->MakeLinkSafe ( $package->GetDisplayName () ) . '/' . $package->GetPackageId () . '"><img src="' . $this->mBaseDir . '/images/viewButton2.png" width="75" height="40" id="buyNowButton" /></a>';
		$this->mPage .= '
				<!--	<img src="' . $this->mBaseDir . '/images/100secure.png" id="oneHundredSecureButton" width="89" height="24" /> -->
				</div>
			</div>
		</div>';
		return $this->mPage;
	} // End LoadDefault

	//! Load the prices section
	function LoadPrices() {
		if ("0.0" == $this->mPackage->GetWasPrice ()) {
			$wasSection = '';
		} else {
			$wasSection = '<div class="wasPrice">
								Was &pound;' . $this->mPresentationHelper->Money ( $this->mPackage->GetWasPrice () ) . '
							</div>';
		}
		switch ($this->mCategory->GetCatalogue ()->GetPricingModel ()->GetPricingModelId ()) {
			case 1 :
				// Regular
				$freeDelivery = 'Free Delivery';
				break;
			case 2 :
				// Shooting
				$freeDelivery = '';
				break;
		}

		// Display the WAS/NOW price appropriately depending on which it has
		if ("0.0" == $this->mPackage->GetPostage () && "0.0" == $this->mPackage->GetWasPrice ()) {
			$nowSection = '<div class="nowPrice">
								Only &pound;' . $this->mPresentationHelper->Money ( $this->mPackage->GetActualPrice () ) . ' ' . $freeDelivery . '
							</div>';
		} elseif ("0.0" != $this->mPackage->GetPostage () && "0.0" == $this->mPackage->GetWasPrice ()) {
			$nowSection = '<div class="nowPrice">
								Only &pound;' . $this->mPresentationHelper->Money ( $this->mPackage->GetActualPrice () ) . '
							</div>';
		} elseif ("0.0" == $this->mPackage->GetPostage () && "0.0" != $this->mPackage->GetWasPrice ()) {
			$nowSection = '<div class="nowPrice">
								Now &pound;' . $this->mPresentationHelper->Money ( $this->mPackage->GetActualPrice () ) . ' ' . $freeDelivery . '
							</div>';
		} elseif ("0.0" != $this->mPackage->GetPostage () && "0.0" != $this->mPackage->GetWasPrice ()) {
			$nowSection = '<div class="nowPrice">
								Now &pound;' . $this->mPresentationHelper->Money ( $this->mPackage->GetActualPrice () ) . '
							</div>';
		}
		$this->mPage .= $nowSection.$wasSection;
		$this->mPage .= '<div class="multibuyPrice">Save a Huge '.$this->mPackage->GetSaving(true).'% on RRP!</div>';
	} // End LoadPrices
} // End CategoryListPackageView

?>