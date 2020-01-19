<?php

//!
class BestSellerView extends View {

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
		parent::__construct ('Best Sellers');
		$this->mCatalogue = $catalogue;
		$this->mSessionHelper 		= new SessionHelper ( );
		$this->mPublicLayoutHelper 	= new PublicLayoutHelper ( );
		$this->mSystemSettings 		= new SystemSettingsModel ( $this->mCatalogue );
		$this->mBasketId 			= $this->mSessionHelper->GetSessionId ();
		$this->mProductController	= new ProductController;
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
		$topProducts = $this->mProductController->GetBestSellingProducts(15);
		$numOneProduct = array_shift($topProducts);
		$url = $this->mPublicLayoutHelper->LoadLinkHref($numOneProduct);

		// Build HTML
		$this->mPage .= '
			<img src="'.$this->mBaseDir.'/images/headingBestSellers.gif" style="float: none;" />
			<div id="topStackContainer">
				<div id="topStackHeading"><h2><a href="'.$url.'">'.$numOneProduct->GetDisplayName().'</a></h2></div>
				<img src="'.$this->mBaseDir.'/images/no1Seller.jpg" id="leftBanner" />
				<img src="'.$this->mBaseDir.'/images/no1SellerRight.jpg" id="rightBanner" />
				<img src="'.$this->mBaseDir.'/images/echoWatermarkLarge.jpg" id="watermark" />
				<a href="'.$url.'">
					'.$this->mPublicLayoutHelper->LargeProductImage($numOneProduct,'numOneStackImage').'
				</a>
				<div id="numOneProductName">'.$numOneProduct->GetDisplayName().'</div>
				<div id="numOneDescription">'.$numOneProduct->GetDescription().'</div>
				<div id="wasPrice">WAS &pound;'.$numOneProduct->GetWasPrice().'</div>
				<div id="nowPrice">NOW &pound;'.$numOneProduct->GetActualPrice().'</div>
				<img id="secure" src="'.$this->mBaseDir.'/images/100secure.png" />
				<a href="'.$url.'">
					<input type="image" id="button" src="'.$this->mBaseDir.'/images/viewButton.png" />
				</a>
			</div> <!-- End topStackContainer -->
		';

		$secondAndThird = array();
		$secondAndThird[] = array_shift($topProducts);
		$secondAndThird[] = array_shift($topProducts);

		// Display all products
		foreach($secondAndThird as $product) {
			$url = $this->mPublicLayoutHelper->LoadLinkHref($product);
			$this->mPage .= '
			<div class="topStackSubContainer">
				<div class="topStackSubHeading"><h2><a href="'.$url.'">'.$product->GetDisplayName().'</a></h2></div>
				<div class="topStackImage">
				<a href="'.$url.'">
					'.$this->mPublicLayoutHelper->MediumProductImage($product).'
				</a>
				</div>
				<div class="numOneProductName">'.$product->GetDisplayName().'</div>
				<div class="numOneDescription">'.$product->GetDescription().'</div>
				<div class="wasPrice">WAS &pound;'.$product->GetWasPrice().'</div>
				<div class="nowPrice">NOW &pound;'.$product->GetActualPrice().'</div>
				<img class="secure" src="'.$this->mBaseDir.'/images/100secure.png" />
				<a href="'.$url.'">
					<input type="image" class="button" src="'.$this->mBaseDir.'/images/viewButton.png" />
				</a>
			</div> <!-- End topStackSubContainer -->
			';
		}

		foreach($topProducts as $product) {
			$url = $this->mPublicLayoutHelper->LoadLinkHref($product);
			if($product->GetWasPrice() == 0) {
				$wasPrice = '';
				$nowWord = 'ONLY';
			} else {
				$wasPrice = 'WAS &pound;'.$product->GetWasPrice();
				$nowWord = 'NOW';
			}
			$this->mPage .= <<<EOT
			<div class="topProductContainer">
				<a href="{$url}">
					{$this->mPublicLayoutHelper->SmallProductImage($product)}
				</a>
				<a href="{$url}">
					<div style="border: 0px; width: 270px; margin: 5px 0px 0px 5px; font-size: 18px; text-align: right; font-weight: bold;">{$product->GetDisplayName()}</div>
				</a>
				<div class="wasPrice">{$wasPrice}</div>
				<div class="nowPrice">{$nowWord} &pound;{$product->GetActualPrice()}</div>
			</div>
EOT;
		}


	} // End LoadMainContentColumn

} // End BestSellerView


?>