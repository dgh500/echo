<?php
//! Defines the display for a single package on the product listing view
class CategoryListProductView extends View {

	function __construct() {
		parent::__construct ();
	}

	//! Generic load function
	/*!
	 * @param $product  - ProductModel	- Which product to display
	 * @param $category - CategoryModel	- The category being displayed
	 * @param $basketId - Int - 		- The current basket ID
	 */
	function LoadDefault($product, $category, $basketId) {
		// Initialise
		$this->mProduct = $product;
		$this->mBasketId = $basketId;
		$this->mCategory = $category;

		// Top-level or sub category?
		if ($this->mCategory->GetParentCategory ()) {
			$this->mParentCategory = $this->mCategory->GetParentCategory ();
		}

		// Needed for the GetLinkCategory method
		$productController = new ProductController ( );

		// Was Price Section
		$this->LoadWasPrice ();

		// URL for the product page
		$href = $this->mPublicLayoutHelper->LoadLinkHref ( $this->mProduct );

		$this->mPage .= $this->mPublicLayoutHelper->OpenCategoryViewProductContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenCategoryViewProductImageContainer ();
		$this->mPage .= '<table><tr><td style="width: 140px; height: 140px; vertical-align: middle; text-align: center;">';
		$this->mPage .= '<a href="' . $href . '">';
		$this->mPage .= $this->mPublicLayoutHelper->MediumProductImage ( $product );
		$this->mPage .= '</a>';
		$this->mPage .= '</td></tr></table>';
		$this->mPage .= $this->mPublicLayoutHelper->CloseCategoryViewProductImageContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenCatProductDetailsContainer ();
		$this->mPage .= '<h3><a href="' . $href . '">' . $product->GetDisplayName () . '</a></h3>';
		$this->LoadPrices ();
		$this->LoadDescription ();
	#	$this->mPage .= $this->mPublicLayoutHelper->OpenCategoryViewButtonsContainer ();

		// If a product has no attributes and is for sale then display a 'buy now' button, else display a 'view' button
		$atts = $product->GetAttributes();
		if(count($atts) == 0 && $product->GetForSale() && !$product->GetMultibuy()) {
			$this->mPage .= '<form action="' . $this->mFormHandlersDir . '/AddProductToBasketHandler.php" method="post" />
							<input type="hidden" name="addToBasket" id="addToBasket" value="1" />
							<input type="hidden" name="referPage" id="referPage" value="categoryListProductView" />
							<input type="hidden" name="productId" id="productId" value="' . $this->mProduct->GetProductId () . '" />
							<input type="hidden" name="basketId" id="basketId" value="' . $this->mBasketId . '" />
							<input type="hidden" name="categoryId" id="categoryId" value="' . $this->mCategory->GetCategoryId () . '" />
							';
			if ($this->mCategory->GetParentCategory ()) {
				$this->mPage .= '<input type="hidden" name="parentCategoryId" id="parentCategoryId" value="' . $this->mParentCategory->GetCategoryId () . '" />';
			}
			$this->mPage .= '<input type="image" src="' . $this->mBaseDir . '/images/buyNowButton2.png" id="buyNowButton" alt="Buy ' . $this->mProduct->GetDisplayName () . ' Now" width="75" height="40" />';
			$this->mPage .= '</form>';
		} else {
			$this->mPage .= '<a href="' . $href . '"><img src="' . $this->mBaseDir . '/images/viewButton2.png" id="viewButton" alt="View ' . $this->mProduct->GetDisplayName () . '" width="75" height="40" /></a>';
		}

		// Load secure button
	#	$this->mPage .= '<img src="' . $this->mBaseDir . '/images/100secure.png" id="oneHundredSecureButton" alt="One Hundred Percent Secure" width="89" height="24" />';
	#	$this->mPage .= $this->mPublicLayoutHelper->CloseCategoryViewButtonsContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseCatProductDetailsContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseCategoryViewProductContainer ();
		return $this->mPage;
	} // End LoadDefault

	function LoadDescription() {
		$this->mPage .= '<div class="description">
							' . $this->mPresentationHelper->ChopDown ( $this->mProduct->GetDescription (), 120, 1 ) . '
						</div>';
	}

	function LoadPrices() {
	#	$this->mPage .= '<div class="prices">';
		if ("0.0" == $this->mProduct->GetWasPrice ()) {
			$wasSection = '';
		} else {
			$wasSection = '<div class="wasPrice">
								Was &pound;' . $this->mPresentationHelper->Money ( $this->mProduct->GetWasPrice () ) . '
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
		if (floatval ( $this->mProduct->GetActualPrice () ) == 0) {
			$nowSection = '<div class="nowPrice">
									Call for Price
								</div>';
		} else {
			if ("0.0" == $this->mProduct->GetPostage () && "0.0" == $this->mProduct->GetWasPrice ()) {
				$nowSection = '<div class="nowPrice">
									Only &pound;' . $this->mPresentationHelper->Money ( $this->mProduct->GetActualPrice () ) . ' ' . $freeDelivery . '
								</div>';
			} elseif ("0.0" != $this->mProduct->GetPostage () && "0.0" == $this->mProduct->GetWasPrice ()) {
				$nowSection = '<div class="nowPrice">
									Only &pound;' . $this->mPresentationHelper->Money ( $this->mProduct->GetActualPrice () ) . '
								</div>';
			} elseif ("0.0" == $this->mProduct->GetPostage () && "0.0" != $this->mProduct->GetWasPrice ()) {
				$nowSection = '<div class="nowPrice">
									Now &pound;' . $this->mPresentationHelper->Money ( $this->mProduct->GetActualPrice () ) . ' ' . $freeDelivery . '
								</div>';
			} elseif ("0.0" != $this->mProduct->GetPostage () && "0.0" != $this->mProduct->GetWasPrice ()) {
				$nowSection = '<div class="nowPrice">
									Now &pound;' . $this->mPresentationHelper->Money ( $this->mProduct->GetActualPrice () ) . '
								</div>';
			}
		}
		$this->mPage .= $nowSection . $wasSection;
		if($this->mProduct->GetMultibuy()) {
			$this->mPage .= '<div class="multibuyPrice">Multibuy Available From &pound;'.$this->mPresentationHelper->Money ($this->mProduct->GetCheapestMultibuy()).'</div>';
		}
	#	$this->mPage .= '</div>';
	}

	function LoadWasPrice() {
		if ($this->mProduct->GetWasPrice () == 0) {
			$wasPriceSection = '';
		} else {
			$wasPriceSection = '<div class="wasPrice">Was &pound;' . $this->mPresentationHelper->Money ( $this->mProduct->GetWasPrice () ) . '</div>';
		}
	}

}

?>