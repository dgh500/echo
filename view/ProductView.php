<?php
//! Defines the product detail page
class ProductView extends View {

	var $mSystemSettings;
	var $mSessionHelper;
	var $mProduct;
	var $mCatalogue;

	//! Constructor, initialises the page
	function __construct($productId, $addToBasket = 0) {

		// Which product are we showing?
		try {
			$this->mProduct = new ProductModel($productId);
		} catch(Exception $e) {
			echo '<img src="http://www.echosupplements.com/images/echoWatermarkLarge.jpg" /><br />';
			echo '<p style="font-family: Arial, Sans-Serif; font-size: 14pt;">Sorry this product does not exist, redirecting you to www.echosupplements.com please wait...</p>';
			echo '<script type="text/javascript">
			<!--
			setTimeout("top.location.href = \'http://www.echosupplements.com\'",4000);
			//-->
			</script>';
			die();

		}
		// And which catalogue is it in?
		$this->mCatalogue = $this->mProduct->GetCatalogue();

		// Build the title tag
		$categories = $this->mProduct->GetCategories();
		$category = $categories [0];

		// Load the head tags
		// <title>
		$pHelper = new PresentationHelper;	// This gets declared as a member var mPresentationHelper in the View construct but we haven't constructed yet
		if($this->mProduct->GetMultibuy()) {
			$lowestPrice = $pHelper->Money ($this->mProduct->GetCheapestMultibuy());
			$fromWord = ' From';
		} else {
			$lowestPrice = $pHelper->Money ($this->mProduct->GetActualPrice());
			$fromWord = '';
		}
		unset($pHelper); // Done with it now
		// Make sure not lying..
		if($this->mProduct->GetMultibuy()) {
			if(5*$lowestPrice > 45) {
				$freeDelivery = ' - FREE Delivery';
			} else {
				$freeDelivery = '';
			}
		} else {
			if($lowestPrice > 45) {
				$freeDelivery = ' - FREE Delivery';
			} else {
				$freeDelivery = '';
			}
		} // End if multibuy


		$title = $this->mProduct->GetDisplayName().$fromWord.' Only &pound;'.$lowestPrice.$freeDelivery;



		// Meta Description
		$this->mPresentationHelper = new PresentationHelper;
		if($this->mProduct->GetWasPrice() == 0) {
			$rrpSaving = '';
		} else {
			$rrpSaving = 'Save a HUGE £'.$this->mPresentationHelper->Money($this->mProduct->GetSaving()).' - '.$this->mProduct->GetSaving(true).'% Off RRP';
		}
		($category->GetParentCategory() ? $categoryText = $category->GetParentCategory()->GetDisplayName() : $categoryText = $category->GetDisplayName());
		($this->mProduct->GetManufacturer() ? $metaDesc = ' Manufacturer: '.$this->mProduct->GetManufacturer()->GetDisplayName().',' : $metaDesc = '' );
		$metaDescription = 'Product: '.$this->mProduct->GetDisplayName().','.$metaDesc.' Price: £'.$this->mProduct->GetActualPrice().', Category: '.$categoryText.', In Stock: Yes, '.$rrpSaving;

		// Experimental..
	#	if($this->mProduct->GetManufacturer()->GetManufacturerId() != 25) {
			if($rrpSaving != '') {
				$metaDescription = $this->mProduct->GetDisplayName().': '.$rrpSaving.' on '.$this->mProduct->GetDisplayName().' at Echo Supplements!';
			}
	#	}

		// Constructor
		$cssIncludes = array('jquery.rating.css');
		$jsIncludes = array('fancyzoom.min.js','jqueryUi.js','jquery.rating.js','jquery.qtip.min.js','ProductDetailView.js','validateProductDetailForm.js');

		// Set the canonical URL
		$plh = new PublicLayoutHelper;
		$canonicalUrl = $plh->LoadLinkHref($this->mProduct);
		unset($plh); // No need to have 2 of these, another is created in the constructor below

		// Construct head section
		parent::__construct($title,$cssIncludes,$jsIncludes,$metaDescription,false,$canonicalUrl);

		// System settings
		$this->mSystemSettings = new SystemSettingsModel($this->mCatalogue);

		// Initialise session
		$this->mSessionHelper = new SessionHelper();
		// Update the recently viewed product
		$this->mSessionHelper->SetRecentlyViewedProduct($productId);
	} // End __construct()

	//! Default page load
	function LoadDefault() {
		// HTML page bits
		$footerView = new FooterView();
		$this->mPage .= $this->mPublicLayoutHelper->OpenBody();
		$this->mPage .= $this->mPublicLayoutHelper->OpenCentrePageContainer();
		parent::LoadHeaderSection($this->mCatalogue);
		parent::LoadNavigation($this->mCatalogue);
		parent::LoadLeftColumn ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenRightCol();
		$this->LoadMainContentColumn();
		$this->mPage .= $this->mPublicLayoutHelper->CloseRightCol();
		$this->mPage .= $this->mPublicLayoutHelper->OpenFooterContainer();
		$this->mPage .= $footerView->LoadDefault();
		$this->mPage .= $this->mPublicLayoutHelper->CloseFooterContainer();
		$this->mPage .= $this->mPublicLayoutHelper->CloseCentrePageContainer();
		$this->mPage .= $this->mPublicLayoutHelper->CloseBody();
		$this->mPage .= $this->mPublicLayoutHelper->CloseHtml();
		return $this->mPage;
	} // End LoadDefault()

	//! Loads all of the main content (delegated to ProductDetailView)
	function LoadMainContentColumn() {
		$productDetailView = new ProductDetailView();
		$this->mPage .= $productDetailView->LoadDefault($this->mProduct,$this->mSessionHelper->GetBasket()->GetBasketId());
	} // End LoadMainContentColumn()
}

?>