<?php

//! Generates the code for the brand new section of the category page
class CategoryListBrandNewView extends View {

	//! Initialises the View parent class
	function __construct() {
		parent::__construct ();
	}

	//! Loads the best selling product in the category supplied
	/*!
	 * @param $catalogue - Obj:CategoryModel 	- The category to load from
	 * @param $basketId  - Obj:BasketModel    	- The basket ID (for the 'Buy Now' button)
	 * @return - The code for the box
	 */
	function LoadDefault($category, $basketId) {
		// Initialise catalogue and basket
		$this->mCategory = $category;
		$this->mBasketId = $basketId;
		$this->LoadProduct();
		return $this->mPage;
	} // End LoadDefault();

	//! Load a product as featured
	function LoadProduct() {
		// Initialise
		$productController = new ProductController( );
		$product = $productController->GetBrandNewProductForCategory($this->mCategory);

		// If no products, return nothing
		if(!$product) {
			return 'No Best Selling Products';
		}

		// Do we have free delivery in this catalogue?
		switch ($this->mCategory->GetCatalogue ()->GetPricingModel ()->GetPricingModelId ()) {
			case 1 :
				// Regular - If no manual postage, then free delivery :)
				if("0.0" == $product->GetPostage()) {
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

		// Showing a was price?
		if($product->GetWasPrice() == 0) {
			$wasPriceSection = '';
			$nowWord = 'ONLY';
		} else {
			$wasPriceSection = '<h5 id="brandNewWasPrice">WAS &pound;'.$this->mPresentationHelper->Money($product->GetWasPrice()).' '.$freeDelivery.'</h5>';
			$nowWord = 'NOW';
		}

		// Pick the first one
		if($product) {
			$linkTo = $this->mPublicLayoutHelper->LoadLinkHref($product);
			$atts = $product->GetAttributes();
			if(is_object($product)) {
				// Load HTML code
				$this->mPage .= <<<HTMLOUTPUT
		<div id="brandNewContainer">
			<div id="brandNewContent">
				<h3 id="brandNewName"><a href="{$linkTo}">{$this->mPresentationHelper->ChopDown($product->GetDisplayName(),50)}</a></h3>
					<table><tr><td valign="top" align="center">
						<a href="{$linkTo}">{$this->mPublicLayoutHelper->MediumProductImage($product,'brandNewProductImage')}</a>
					</td></tr></table>
				<p>{$this->mPresentationHelper->ChopDown ( $product->GetDescription (), 120, 1 )}</p>
				{$wasPriceSection}
				<h5 id="brandNewNowPrice">{$nowWord} &pound;{$this->mPresentationHelper->Money($product->GetActualPrice())} {$freeDelivery}</h5>
			</div> <!-- Close brandNewContent -->
		</div> <!-- Close brandNewContainer -->
HTMLOUTPUT;
			}
		} //
	} // End LoadProduct()

} // End CategoryListBestSellerView

?>