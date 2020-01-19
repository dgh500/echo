<?php

class PackageDetailView extends View {

	function __construct() {
		parent::__construct ();
		$this->IncludeJavascript ( 'validatePackageDetailForm.js' );
	#	$this->IncludeRemoteJavascript('http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js');
		$this->IncludeJavascript('fancyzoom.min.js');
		$this->IncludeJavascript('PackageDetailView.js');
		$this->mPublicLayoutHelper = new PublicLayoutHelper ( );
	}

	function LoadDefault($package, $basketId) {
		// Initialise
		$this->mPackage = $package;
		$this->mBasketId = $basketId;
		$this->mCatalogue = $this->mPackage->GetCatalogue ();
		$this->mContents = $this->mPackage->GetContents ();
		$this->mCategory = $this->mPackage->GetParentCategory ();
		$this->mParentCategory = $this->mCatalogue->GetPackagesCategory ();

		// Display Upgrades
		if (count ( $this->mPackage->GetUpgrades () ) == 0) {
			$this->mShowUpgrades = false;
		} else {
			$this->mShowUpgrades = true;
		}

		// Display the page
		$this->mPage .= $this->mPublicLayoutHelper->OpenPackageDetailsContainer ();
		$this->LoadPackageTitle ();
		// Figure out the min height of the package container - this is needed when the package has loads of contents
		// +25 for an options container, +20 for a short (<25 char) title, +30 for a double line (>25 char) title
		$containerHeight = 180;
		foreach($this->mPackage->GetContents() as $product) {
			if(strlen(trim($product->GetDisplayName())) < 27 ) { $productHeight = 22; } else { $productHeight = 40; }
			if(!$product->HasNoAttributes()) { $productHeight += 32; }
			$containerHeight += $productHeight;
		}
		if($containerHeight < 370) { $containerHeight = false; }

		$this->mPage .= $this->mPublicLayoutHelper->OpenPackageDetailsTopSection($containerHeight);
		$this->LoadPackageImage ();
		$this->LoadPackageText ();
		$this->LoadButtons ();
		if ($this->mShowUpgrades) {
			$this->mPage .= $this->LoadUpgrades ();
		} else {
			$this->LoadPriceMatch();
			$this->LoadSecure();
		}
		$this->mPage .= $this->mPublicLayoutHelper->ClosePackageDetailsTopSection ();
		$this->mPage .= '</form>'; // Close the form
		$this->mPage .= $this->mPublicLayoutHelper->OpenPackageOverviewSection ();
		$this->mPage .= '<div>' . $this->mPackage->GetLongDescription () . '</div>';
		$this->mPage .= $this->mPublicLayoutHelper->ClosePackageOverviewSection ();
		$this->LoadPackageContents ();
		$this->mPage .= $this->mPublicLayoutHelper->ClosePackageDetailsContainer ();
		return $this->mPage;
	}

	function LoadPriceMatch() {
		$this->mPage .= <<<HTMLOUTPUT
				<div id="priceMatchSection">
					<img src="{$this->mBaseDir}/images/priceMatchBanner.jpg" id="priceMatchBanner" width="30" height="145" />
					<img src="{$this->mBaseDir}/images/priceMatchHeader.jpg" id="priceMatchHeader" width="218" height="36" />
					<form action="" method="post">
						<input type="text" name="priceMatchName" id="priceMatchName" value="your name" />
						<input type="text" name="priceMatchPhone" id="priceMatchPhone" value="your phone number" />
						<input type="text" name="priceMatchEmail" id="priceMatchEmail" value="your email address" />
						<input type="text" name="priceMatchWebsite" id="priceMatchWebsite" value="website to beat!" />
						<input type="image" src="{$this->mBaseDir}/images/submitButton.png" id="submitPriceMatchButton" />
					</form>
				</div>
HTMLOUTPUT;
	}

	function LoadSecure() {
		$this->mPage .= '<div id="secureSection">
			<img src="'.$this->mBaseDir.'/images/secureBanner.jpg" id="secureBanner" width="30" height="145" />
			<img src="'.$this->mBaseDir.'/images/secureHeader.jpg" id="secureHeader" width="219" height="36" />
		</div>';
	}

	function LoadUpgrades() {
		$this->mPage .= '<div id="upgradesSection">
		<img src="'.$this->mBaseDir.'/images/packageUpgradesBanner.jpg" id="upgradesBanner" />
		<img src="'.$this->mBaseDir.'/images/upgradesHeader.jpg" id="upgradesHeader" /><div>';
		foreach ( $this->mPackage->GetContents () as $product ) {
			if (count ( $this->mPackage->GetUpgradesFor ( $product ) ) != 0) {
				$this->mPage .= '<strong>' . $product->GetDisplayName () . '</strong><br />';
			}
			foreach ( $this->mPackage->GetUpgradesFor ( $product ) as $upgrade ) {
				$href = $this->mPublicLayoutHelper->LoadLinkHref ( $upgrade );
				$upgradePrice = $this->mPresentationHelper->Money ( $this->mPackage->GetUpgradePrice ( $product, $upgrade ) );
				if ($upgradePrice == 0.00) {
					$upgradePrice = 'FREE';
				}
				$allAttributes = $upgrade->GetAttributes ();
				if (count ( $allAttributes ) == 0) {
					$attributes = '';
				} else {
					foreach ( $allAttributes as $attribute ) {
						$attributes = ' <select
										name="skuAttribute' . $attribute->GetProductAttributeId () . '"
										id="skuAttribute' . $attribute->GetProductAttributeId () . '"
										style="width: 200px; margin-bottom: 5px;">';
						$attributes .= '<option value="NA">' . $attribute->GetAttributeName () . '</option>';
						$allSkuAttributes = $attribute->GetSkuAttributes ();
						foreach ( $allSkuAttributes as $skuAttribute ) {
							$attributes .= '<option value="' . $skuAttribute->GetSkuAttributeId () . '">' . $skuAttribute->GetAttributeValue () . '</option>';
						}
					}
					$attributes .= '</select>';
				}
				$this->mPage .= '<input
				type="radio"
				name="package' . $this->mPackage->GetPackageId () . 'product' . $product->GetProductId () . '"
				id="package' . $this->mPackage->GetPackageId () . 'product' . $product->GetProductId () . '"
				value="upgrade' . $upgrade->GetProductId () . '" />
				&pound;' . $upgradePrice . '
				<a href="' . $href . '">
				<label for="package' . $this->mPackage->GetPackageId () . 'product' . $product->GetProductId () . 'upgrade' . $upgrade->GetProductId () . '">' . $upgrade->GetDisplayName () . '</label></a>
				' . $attributes . '<br />';
			}
			$this->mPage .= '<br />';
		}
		$this->mPage .= '<div id="upgradesExplanation">To apply an upgrade please choose the upgrade THEN add it to your basket</div>';
		$this->mPage .= '</div></div>'; // End upgradesSection (extra div is for padding)
	}

	function LoadButtons() {
		$registry = Registry::getInstance ();
		if ($this->mPackage->GetImage ()) {
			$imageDir = $this->mRootDir . '/' . $registry->originalImageDir;
			$width = $this->mPackage->GetImage ()->GetOriginalWidth ( $imageDir );
			$height = $this->mPackage->GetImage ()->GetOriginalHeight ( $imageDir ) + 100;
		} else {
			$width = 400;
			$height = 400;
		}

		$inStockSection = '<div id="inStock">
								<img src="' . $this->mBaseDir . '/images/inStockButton.png" />
							</div>';
/*		$this->mPage .= '<div id="packageButtons">
							' . $inStockSection . '
							<div id="enlarge">
								<a href="#mainPackageImageContainer" id="mainPackageImageLink2">
									<img src="' . $this->mBaseDir . '/images/enlargeImage.png" />
								</a>
							</div>
						</div>';*/
	}

	function LoadPackageContents() {
		foreach ( $this->mPackage->GetContents () as $product ) {
			$href = $this->mPublicLayoutHelper->LoadLinkHref ( $product );
			$this->mPage .= '	<div id="packageContentsContainer">
									<div class="packageContentsProductContainer">
											<div class="packageContentsProductImageContainer">
												<a href="' . $href . '">';
			$this->mPage .= $this->mPublicLayoutHelper->MediumProductImage ( $product );
			$this->mPage .= '					</a>
											</div>
										<div class="productDetailsContainer">
											<h3><a href="' . $href . '">' . $product->GetDisplayName () . '</a></h3>
											<div class="description">
												' . $product->GetDescription () . '
											</div>
											<div style="border: 0px solid #000; width: 380px; height: 100px; position: absolute; top: 60px; left: 0px;">';
/*			$allImages = $product->GetImages ();
			$image = $allImages[0];
			if (count ( $allImages ) > 1) {
				$i = 0;
				foreach ( $allImages as $image ) {
					if ($i < 1) {
						$this->mPage .= '<div style="width: 100px; height: 100px; text-align: center; float: left;">';
						$this->mPage .= $this->mPublicLayoutHelper->SmallImage ( $image );
						$this->mPage .= '</div>';
						$i ++;
					}
				}
			}*/
			$this->mPage .= '				</div>
										</div>
									</div>
								</div>';
		}
	}

	function LoadPackageText() {
		$this->mPage .= '	<div id="packageText">
									<h1>' . $this->mPackage->GetDisplayName () . '</h1>';
		$this->mPage .= $this->LoadPrices ();
		$this->LoadOptions ();
		$this->mPage .= '</div> <!-- End packageText -->';
	}

	function LoadPackageImage() {
		$this->mPage .= '<div id="packageImage"><a href="#mainPackageImageContainer" id="mainPackageImageLink">';
		$this->mPage .= $this->mPublicLayoutHelper->LargePackageImage ( $this->mPackage );
		$this->mPage .= '</a></div> <!-- End packageImage -->';
		$this->LoadZoomedPackageImage();
	}

	//! Loads the container with the zoomed package image and container
	function LoadZoomedPackageImage() {
		$this->mPage .= '<div id="mainPackageImageContainer">
								<h1 class="zoomedPackageName">'.$this->mPackage->GetDisplayName().'</h1>
								<p class="zoomedPackageText">Click the X above or press Escape to close!</p>
								<br />'.$this->mPublicLayoutHelper->LargestPackageImage ( $this->mPackage, 'XLPackageImage' ).'
						</div> <!-- End mainPackageImageContainer -->';
	}

	function LoadPackageTitle() {
		$breadCrumbs = new PackageBreadCrumbView ( );
		$this->mPage .= '<div id="packageTitle">';
		$this->mPage .= $breadCrumbs->LoadDefault ( $this->mPackage );
		$this->mPage .= '</div><div style="clear: both"></div>';
	}

	function LoadPrices() {
		if ("0.0" == $this->mPackage->GetWasPrice ()) {
			$wasSection = '';
		} else {
			$wasSection = '<div id="packageWasPrice">
								Was &pound;' . $this->mPresentationHelper->Money ( $this->mPackage->GetWasPrice () ) . '
							</div> <!-- End packageWasPrice -->';
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
		if ("0.0" == $this->mPackage->GetPostage () && "0.0" == $this->mPackage->GetWasPrice ()) {
			$nowSection = '<div id="packageNowPrice">
								Only &pound;' . $this->mPresentationHelper->Money ( $this->mPackage->GetActualPrice () ) . ' ' . $freeDelivery . '
							</div> <!-- End packageNowPrice -->';
		} elseif ("0.0" != $this->mPackage->GetPostage () && "0.0" == $this->mPackage->GetWasPrice ()) {
			$nowSection = '<div id="packageNowPrice">
								Only &pound;' . $this->mPresentationHelper->Money ( $this->mPackage->GetActualPrice () ) . '
							</div> <!-- End packageNowPrice -->';
		} elseif ("0.0" == $this->mPackage->GetPostage () && "0.0" != $this->mPackage->GetWasPrice ()) {
			$nowSection = '<div id="packageNowPrice">
								Now &pound;' . $this->mPresentationHelper->Money ( $this->mPackage->GetActualPrice () ) . ' ' . $freeDelivery . '
							</div> <!-- End packageNowPrice -->';
		} elseif ("0.0" != $this->mPackage->GetPostage () && "0.0" != $this->mPackage->GetWasPrice ()) {
			$nowSection = '<div id="packageNowPrice">
								Now &pound;' . $this->mPresentationHelper->Money ( $this->mPackage->GetActualPrice () ) . '
							</div> <!-- End packageNowPrice -->';
		}

		// How much are we saving?
		if($this->mPackage->GetWasPrice() > 0) {
			if($this->mPackage->GetActualPrice() > 45) {
				$savingSection = '
								<div id="packageSaving">
									<span style="text-decoration: underline; text-align: center;">FREE NEXT DAY DELIVERY</span>
								</div>';
			} else {
			$savingSection = '
							<div id="packageSaving">
								Save &pound;'.$this->mPresentationHelper->Money($this->mPackage->GetSaving()).' - '.$this->mPackage->GetSaving(true).'% Off!
							</div> <!-- End packageSaving -->
							';
			}
		} else {
			$savingSection = '';
		}
		$this->mPage .= $nowSection . $wasSection . $savingSection;
	}

	function LoadOptions() {
		$this->mPage .= '
						<form action="' . $this->mBaseDir . '/formHandlers/AddPackageToBasketHandler.php" method="post" onsubmit="return validateForm(this)" />
						<input type="hidden" name="referPage" id="referPage" value="packageDetailView" />
						<input type="hidden" name="addToBasket" id="addToBasket" value="1" />
						<input type="hidden" name="packageId" id="packageId" value="' . $this->mPackage->GetPackageId () . '" />
						<input type="hidden" name="basketId" id="basketId" value="' . $this->mBasketId . '" />
						<input type="hidden" name="categoryId" id="categoryId" value="' . $this->mCategory->GetCategoryId () . '" />';
		if ($this->mCategory->GetParentCategory ()) {
			$this->mPage .= '<input type="hidden" name="parentCategoryId" id="parentCategoryId" value="' . $this->mParentCategory->GetCategoryId () . '" />';
		}
		$this->mPage .= '<div id="packageOptions">
							<h3>Package Contents</h3>
							<div id="optionsContainer">';
		$allContents = $this->mPackage->GetContents ();
		// Don't allow them to buy it if something is out of stock in it
		if($this->mPackage->AllInStock() && $this->mPackage->IsBetterValue()) {
			// Display contents + options
			$this->mPage .= '<ol>';
			foreach ( $allContents as $product ) {
				// Product Name - If there is more than 1 of the product in the package then say so!
				if($this->mPackage->GetProductQty($product)>1) {
					$qty = $this->mPackage->GetProductQty($product).' x ';
				} else {
					$qty = '';
				}
				$this->mPage .= '<li>'.$qty.$product->GetDisplayName () . '</li>';
				$allAttributes = $product->GetAttributes ();

				// If the product has options, display them
	 			foreach ( $allAttributes as $attribute ) {
					$this->mPage .= '<select id="skuAttribute' . $attribute->GetProductAttributeId () . '" name="skuAttribute' . $attribute->GetProductAttributeId () . '"><option value="NA">' . $attribute->GetAttributeName () . '</option>';
					$allSkuAttributes = $attribute->GetSkuAttributes ();
					$values = array ();
					foreach ( $allSkuAttributes as $skuAttribute ) {
						$sku = new SkuModel($skuAttribute->GetSkuId());
						if($sku->GetQty() == 0) {
							$stockMessage = 'SOLD OUT - ';
						} else {
							$stockMessage = '';
						}
						if (! in_array ( trim ( $skuAttribute->GetAttributeValue () ), $values )) {
							$this->mPage .= '<option value="' . $skuAttribute->GetSkuAttributeId () . '">' .$stockMessage . $skuAttribute->GetAttributeValue () . '</option>';
						}
						$values [] = trim ( $skuAttribute->GetAttributeValue () );
					} // End foreach($allSkuAttributes
					$this->mPage .= '</select>';
				} // End foreach($allAttributes
			} // End foreach($allContents
			$this->mPage .= '</ol>';
			$this->mPage .= '<input type="image" src="' . $this->mBaseDir . '/images/buyNowButton2.png" style="position: relative; left: 65px;" />';
		} else {
			// Display not available
			$this->mPage .= '<center>This package is currently unavailable because one of the products is out of stock.</center>';
		}
		$this->mPage .= '	<div id="errorBox"></div>
							</div> <!-- Close optionsContainer -->
						</div> <!-- Close packageOptions -->';
	} // End LoadOptions


}

?>