<?php

//! Generates the code for the featured product section of the category page
class CategoryListFeaturedProductView extends View {

	//! Initialises the View parent class
	function __construct() {
		parent::__construct ();
	}

	//! Loads a featured product in the category supplied
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
		$product = $productController->GetFeaturedProductForCategory($this->mCategory);
		// Pick the first one
		if($product) {
			$linkTo = $this->mPublicLayoutHelper->LoadLinkHref($product);
			$atts = $product->GetAttributes();
			if(count($atts) == 0 && $product->GetForSale() && !$product->GetMultibuy()) {
				$buyNowBit = '<form action="' . $this->mBaseDir . '/formHandlers/AddProductToBasketHandler.php" method="post" onsubmit="return validateForm(this)">
							<div>
							<input type="hidden" name="referPage" id="referPage" value="categoryListProductView" />
							<input type="hidden" name="addToBasket" id="addToBasket" value="1" />
							<input type="hidden" name="productId" id="productId" value="'.$product->GetProductId().'" />
							<input type="hidden" name="basketId" id="basketId" value="'.$this->mBasketId.'" />
							<input type="hidden" name="categoryId" id="categoryId" value="' . $this->mCategory->GetCategoryId () . '" />
							<input type="image" src="'.$this->mBaseDir.'/images/buyNowButton.png" id="featuredProductBuyNowButton" />
							</div>
							</form>';
			} else {
				$buyNowBit = '
							<a href="'.$linkTo.'">
								<img src="'.$this->mBaseDir.'/images/viewButton.png" id="featuredProductBuyNowButton" alt="View '.$product->GetDisplayName().'" />
							</a>';
			}
			if(intval($product->GetWasPrice()) != 0) {
				$wasPriceBit = '<h6 id="featuredProductWasPrice">WAS &pound;'.$this->mPresentationHelper->Money($product->GetWasPrice()).'</h6>';
			} else {
				$wasPriceBit = '';
			}
			if(is_object($product)) {
				// Load HTML code
				$this->mPage .= <<<HTMLOUTPUT
		<div id="featuredProductContainer">
			<div id="featuredProductTitle">
				<h2>FEATURED PRODUCT - {$this->mCategory->GetDisplayName()}</h2>
			</div> <!-- Close dealOfTheWeekTitle -->
			<div id="featuredProductContent">
				<h3 id="featuredProductName"><a href="{$linkTo}">{$this->mPresentationHelper->ChopDown($product->GetDisplayName(),50)}</a></h3>
				<div id="featuredProductDescription">
					{$this->mPresentationHelper->ChopDown($product->GetDescription(),150)}
				</div>
				<a href="{$linkTo}">{$this->mPublicLayoutHelper->MediumProductImage($product,'featuredProductImage')}</a>
				{$wasPriceBit}
				<h5 id="featuredProductNowPrice">NOW &pound;{$this->mPresentationHelper->Money($product->GetActualPrice())}</h5>
				{$buyNowBit}
			</div> <!-- Close featuredProductContent -->
		</div> <!-- Close featuredProductContainer -->
HTMLOUTPUT;
			}
		} //
	} // End LoadProduct()

} // End CategoryListFeaturedProductView

?>