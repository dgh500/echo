<?php

//! Loads the markup for the details - image, description etc. - for the product page
class ProductDetailView extends View {

	function __construct() {
		parent::__construct();
	#	$this->IncludeJavascript ( 'validateProductDetailForm.js' );
	#	$this->IncludeRemoteJavascript('http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js');
	#	$this->IncludeJavascript('fancyzoom.min.js');
	#	$this->IncludeJavascript('ProductDetailView.js');
	}

	function LoadDefault($product, $basketId) {
		// Initialise
		$this->mProduct 	= $product;
		$this->mCatalogue 	= $this->mProduct->GetCatalogue();
		$this->mBasketId 	= $basketId;
		$allCategories 		= $this->mProduct->GetCategories ();
		$this->mCategory 	= $allCategories [0];
		$this->mManufacturer 	= $this->mProduct->GetManufacturer ();
		$this->mAllAttributes 	= $this->mProduct->GetAttributes ();
		$this->mReviewController= new ReviewController;
		if ($this->mCategory->GetParentCategory ()) {
			$this->mParentCategory = $this->mCategory->GetParentCategory ();
		}
		if ($this->mProduct->GetMultibuy ()) {
			$this->mShowMultibuy = 1;
		} else {
			$this->mShowMultibuy = 0;
		}
		$this->mAllSimilar = $this->mProduct->GetSimilar ();
		$this->mAllRelated = $this->mProduct->GetRelated ();
		(count ( $this->mAllSimilar ) > 0 ? $this->mShowSimilar = 1 : $this->mShowSimilar = 0);
		(count ( $this->mAllRelated ) > 0 ? $this->mShowRelated = 1 : $this->mShowRelated = 0);
		(count ( $this->mAllSimilar ) >= 4 ? $this->mMaxSimilar = 4 : $this->mMaxSimilar = count ( $this->mAllSimilar ));
		(count ( $this->mAllRelated ) >= 4 ? $this->mMaxRelated = 4 : $this->mMaxRelated = count ( $this->mAllRelated ));

		// Display the page
		$this->mPage .= $this->mPublicLayoutHelper->OpenProductDetailsContainer ();
		$this->LoadProductTitle ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenProductDetailsTopSection ();
		$this->LoadProductImage ();
		$this->LoadProductText ();
		$this->LoadButtons ();
		// Multibuy
		if ($this->mShowMultibuy) {
			$this->LoadMultibuy();
			$this->LoadBestDeal();
		} else {
			$this->LoadSecure();
			$this->LoadBestDeal();
		}
		$this->mPage .= $this->mPublicLayoutHelper->CloseProductDetailsTopSection ();

		// Additional Images
		$this->LoadAdditionalImages ();

		// Social Networking
		$this->mPage .= $this->mPublicLayoutHelper->OpenProductSocialNetworking();

		// Facebook
		$pinitUrl 	= $this->mPublicLayoutHelper->LoadLinkHref($this->mProduct);
				$image 		= $this->mProduct->GetMainImage();
		if(is_object($image)) {
			$pinitImage = urlencode($this->mBaseDir.'/'.$this->mRegistry->largeImageDir.'/'.$image->GetFilename());
			$pinitDesc 	= urlencode($this->mProduct->GetDisplayName());
		} else {
			$pinitImage = '';
			$pinitDesc = '';
		}
		$this->mPage .= '
		<div id="facebookLikeBox">

		<iframe src="//www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2Fechosupplements&amp;width=270&amp;height=62&amp;colorscheme=light&amp;show_faces=false&amp;border_color&amp;stream=false&amp;header=false&amp;appId=156304131110243" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:270px; height:62px; border-bottom: 1px solid #CCC;" allowTransparency="true"></iframe>

		<a href="https://plus.google.com/101746486729690416875?prsrc=3" style="text-decoration:none;"><img src="https://ssl.gstatic.com/images/icons/gplus-16.png" alt="" style="border:0;width:16px;height:16px;float:none; margin-left: 2px;margin-right: 30px;"/></a>

		<a href="https://twitter.com/share" class="twitter-share-button" data-via="EchoSupplements">Tweet</a>
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>

<!--		<a href="http://pinterest.com/pin/create/button/?url='.$pinitUrl.'&media='.$pinitImage.'&description='.$pinitDesc.'" class="pin-it-button" count-layout="horizontal">Pin It</a>
		<script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"></script>
-->

		</div>';

		// Trustpilot
		$this->mPage .= '
		<div id="trustPilotBox">
			<img src="'.$this->mBaseDir.'/images/productPageTrust.jpg" />
		</div>';

		$this->mPage .= $this->mPublicLayoutHelper->CloseProductSocialNetworking();

		// If we have an ECHO description then the other one is called the MANUFACTURERS desc, otherwise its just the description
		$otherDescriptionTitle = 'Product Description';
		// If present, an Echo description
		if($this->mProduct->GetEchoDescription()) {
			$this->mPage .= $this->mPublicLayoutHelper->OpenEchoDescriptionSection ($this->mProduct->GetDisplayName());
			$this->mPage .= '<div>'.$this->mProduct->GetEchoDescription().'</div>';
			$this->mPage .= $this->mPublicLayoutHelper->CloseEchoDescriptionSection ();
			$otherDescriptionTitle = 'Manufacturers Description';
		}

		// Product Overview
		$this->mPage .= $this->mPublicLayoutHelper->OpenProductOverviewSection ($this->mProduct->GetDisplayName(),$otherDescriptionTitle);
		$this->LoadProductOverview ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseProductOverviewSection ();

		// Product Reviews
		$this->mPage .= $this->mPublicLayoutHelper->OpenProductReviewSection ($this->mProduct->GetDisplayName().' - '.$this->mReviewController->CountReviewsForProduct($this->mProduct).'');
		$this->LoadProductReviews();
		$this->mPage .= $this->mPublicLayoutHelper->CloseProductReviewSection ();

		// Package Cross-Selling
		$this->mPage .= $this->LoadPackageCrossSell();

		// Related
		if ($this->mShowRelated) {
			$this->mPage .= $this->mPublicLayoutHelper->OpenProductRelatedSection ($this->mProduct->GetDisplayName());
			$this->mPage .= $this->LoadRelated ();
			$this->mPage .= $this->mPublicLayoutHelper->CloseProductRelatedSection ();
		}

		// Similar
		if ($this->mShowSimilar) {
			$this->mPage .= $this->mPublicLayoutHelper->OpenProductSimilarSection ($this->mProduct->GetDisplayName().' : ');
			$this->mPage .= $this->LoadSimilar ();
			$this->mPage .= $this->mPublicLayoutHelper->CloseProductSimilarSection ();
		}

		$this->mPage .= $this->mPublicLayoutHelper->CloseProductDetailsContainer ();
		return $this->mPage;
	}

	//! Gives the customer the 'best deal' (replaces price match section) which is either a package with one other
	// item or a complimentary product.
	function LoadBestDeal() {
		/********* in simple package ************/
		if($this->mProduct->IsInSimplePackage(true)) {
			// Get a package
			$package = $this->mProduct->IsInSimplePackage(true);
			// Loop over the package and remove the prices of products until we hit the 'active' product
			$hitActiveProduct = false;
			$effectivePrice = $package->GetActualPrice();
			foreach($package->GetContents() as $packageProduct) {
				if($packageProduct->GetProductId() != $this->mProduct->GetProductId()) {
					// Not the active product, remove its price from the effective price
	 				$effectivePrice = $effectivePrice - ($package->GetProductQty($packageProduct) * $packageProduct->GetActualPrice());
					$secondProduct = $packageProduct->GetDisplayName();
				} // End if
			} // End foreach

			// Link to the package
			$href = $this->mPublicLayoutHelper->LoadPackageLinkHref($package);
			// Image Code
			$image = $this->mPublicLayoutHelper->SmallPackageImage($package,'bestDealPackage');
			// Pretty it up
			$effectivePrice = $this->mPresentationHelper->Money($effectivePrice);
			// Generate output
			$this->mPage .= <<<HTMLOUTPUT
	<div id="bestDealSection">
		<img src="{$this->mBaseDir}/images/bestDealHeader.jpg" id="bestDealHeader" width="248" height="37" />
		<table style="border: 0px !important" width="100%" height="115">
		<tr><td align="center" valign="middle" style="border: 0px !important">
		<a href="{$href}">{$image}</a>
		</td><td align="left" valign="middle" style="border: 0px !important">
		<span id="bestDealText">
			<a href="{$href}">
				<strong><span style="color: #F00">Buy This For <br />Only &pound;{$effectivePrice}</span></strong><br />When bought with {$secondProduct}
			</a>
		</span>
		</td></tr></table>
	</div>
HTMLOUTPUT;
		} else {
			if($this->mShowRelated) {
			/********* has 'also bought' option ************/
				// Get an 'also bought' product to try and cross sell
				$product = $this->mAllRelated[0];
				$href 	= $this->mPublicLayoutHelper->LoadLinkHref($product);
				$image 	= $this->mPublicLayoutHelper->SmallProductImage($product,'bestDealProduct');

				// Generate output
				$this->mPage .= <<<HTMLOUTPUT
		<div id="bestDealSection">
			<img src="{$this->mBaseDir}/images/howAboutHeader.jpg" id="bestDealHeader" width="248" height="37" />
			<table style="border: 0px !important" width="100%" height="115">
			<tr><td align="center" valign="middle" style="border: 0px !important">
			<a href="{$href}">{$image}</a>
			</td><td align="left" valign="middle" style="border: 0px !important">
			<span id="bestDealText">
				<a href="{$href}">
					<strong>Customers Who Bought This Item Also Bought<br /></strong>{$product->GetDisplayName()}
				</a>
			</span>
			</td></tr></table>
		</div>
HTMLOUTPUT;
			} else {
				// A bit screwed here, just 'approved retailer' and the company logo
				$image = $this->mPublicLayoutHelper->ManufacturerImage($this->mManufacturer,'bestDealManufacturerLogo');
				$href = $this->mPublicLayoutHelper->LoadManufacturerHref($this->mManufacturer);
				// Generate output
				$this->mPage .= <<<HTMLOUTPUT
		<div id="bestDealSection">
			<img src="{$this->mBaseDir}/images/approvedRetailerHeader.jpg" id="bestDealHeader" width="248" height="37" />
			<table style="border: 0px !important" width="100%" height="100%">
			<tr><td align="center" valign="middle" style="border: 0px !important">
				<a href="{$href}">{$image}</a>
			</td></tr></table>
		</div>
HTMLOUTPUT;
			} // End if has a related product
		} // End if in simple package
	} // End LoadBestDeal

	//! Loads the 'why not try in a package' section
	function LoadPackageCrossSell() {
		if($this->mProduct->IsInSomePackage(true)) {
			$packages = $this->mProduct->GetPackages(4);

			// What do we call it - packages, stacks, specials...
			$packagesCategory = $this->mCatalogue->GetPackagesCategory();
			$packagesDescription = $packagesCategory->GetDisplayName();

			// Open Stack container
			$this->mPage .= $this->mPublicLayoutHelper->OpenProductPackageCrossSellSection($this->mProduct->GetDisplayName(),$packagesDescription);

			// Anonymous - handles margin/padding
			$this->mPage .= '<div>';

			// Loop and display!
			foreach($packages as $package) {
				// Link to the package
				$href = $this->mPublicLayoutHelper->LoadPackageLinkHref($package);
				// Image Code
				$image = $this->mPublicLayoutHelper->SmallPackageImage($package);

				// Do we have free delivery in this catalogue?
				switch ($this->mCategory->GetCatalogue ()->GetPricingModel ()->GetPricingModelId ()) {
					case 1 :
						// Regular - If no manual postage, then free delivery :)
						if("0.0" == $package->GetPostage()) {
							$freeDelivery = '';
						} else {
							$freeDelivery = '';
						}
						break;
					case 2 :
						// Shooting
						$freeDelivery = '';
						break;
				}

				// How much are we saving?
				$saving = ' - Save £'.$this->mPresentationHelper->Money($package->GetSaving()).' - '.$package->GetSaving(true).'% Off!';

				// Display the stack/package
				$this->mPage .= '
								<div class="stackContainer">
										<div class="imageContainer">
											<a href="'.$href.'">'.$image.'</a>
										</div>
										<div class="stackDescription">
											<strong><a href="'.$href.'">'.$package->GetDisplayName().'</a></strong><br />';
				foreach($package->GetContents() as $product) {
					$this->mPage .= ' - '.$product->GetDisplayName().'<br />';
				}
				$this->mPage .= '
											<span class="wasPrice">Was £'.$this->mPresentationHelper->Money($package->GetWasPrice()).'</span><br />
											<span class="nowPrice">Now £'.$this->mPresentationHelper->Money($package->GetActualPrice()).$saving.$freeDelivery.'</span>
										</div>
									</div>
								';

			}
			// Close anonymous
			$this->mPage .= '</div>';
			// Close stack container
			$this->mPage .= $this->mPublicLayoutHelper->CloseProductPackageCrossSellSection();
		}
	}

	//! Loads the product description, incorporating a cut-off at 500 characters or the nearest convenient point (end of sentence etc)
	function LoadProductOverView() {
		$length = strlen ( $this->mProduct->GetLongDescription () );
		$fullDesc = $this->mProduct->GetLongDescription ();

		// Remove tabs, newlines etc. so that the below function works properly (ie. no double spaces...)
		$fullDesc = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $fullDesc);

		$i = 2000;
		$found = false;
		// String is less than 2000
		if (strlen ( $fullDesc ) < 2000) {
			$i = strlen ( $fullDesc );
			$cutoffLength = $i;
		} else {
			// Look for something nice to end on
			while ( $i < strlen ( $fullDesc ) && ! $found ) {
				// Full stop followed by a space
				// The is_numberic stops the end coming on a decimal point (3.5 etc.)
				if ($fullDesc [$i] == '.' && ! is_numeric($fullDesc[$i - 1])) {
					// To check it isn't a link we need to check the previous 3 characters werent www or ://
					$linkStartCheck = substr($fullDesc,$i-3,3);
					// ... And make sure we arent in the middle of a '.com'
					$linkEndCheck = substr($fullDesc,$i+1,3);
					// ... And it isn't a full stop at the end of a <li>
					$liCheck = substr($fullDesc,$i+1,5);
					// Check both ends to make sure they aren't link segments
					if(!$this->IsLinkSegment($linkStartCheck) && !$this->IsLinkSegment($linkEndCheck) && !$this->IsListItemEnd($liCheck)) {
						$cutoffLength = $i+1;
						$found = true;
					}
				// Close Paragraph
				// Sometimes </p></td> occurs - need to cover for this as well - also allow <p> </td> - with one space inbetween, as the preg_replace
				// earlier means there should only be a max of 1 space between things. Won't work if you have <p>  </td> (2 spaces)
				} elseif ($fullDesc [$i] == '<' && $fullDesc [$i + 1] == '/' && $fullDesc [$i + 2] == 'p' && $fullDesc [$i + 3] == '>' &&
							// To avoid notice warnings..
							(isset($fullDesc[$i + 4]) && isset($fullDesc[$i + 5]) && isset($fullDesc[$i + 6]) && isset($fullDesc[$i + 7]))
							&&
							// Not </t
							($fullDesc[$i + 4] != '<' && $fullDesc[$i + 5] != '/' && $fullDesc[$i + 6] != 't')
								&&
							// Not _</t
							($fullDesc[$i + 4] != ' ' && $fullDesc[$i + 5] != '<' && $fullDesc[$i + 6] != '/' && $fullDesc[$i + 7] != 't')
						  ) {
				#	var_dump($fullDesc[$i].$fullDesc[$i+1].$fullDesc[$i+2].$fullDesc[$i+3].$fullDesc[$i+4].$fullDesc[$i+5].$fullDesc[$i+6].$fullDesc[$i+7]);die();
				#	print_r($fullDesc);die();
					$cutoffLength = $i + 4;
					$found = true;
				// Close UL
				} elseif ($fullDesc [$i] == '<' && $fullDesc [$i + 1] == '/' && $fullDesc [$i + 2] == 'u' && $fullDesc [$i + 3] == 'l' && $fullDesc [$i + 4] == '>') {
					$cutoffLength = $i + 5;
					$found = true;
				}
				$i ++;
			}
			// No convenient point found - just cut off after the end (so the read more is useless, but doesnt break the design or show errors)
			if (! $found) {
				$cutoffLength = strlen ( $fullDesc );
			}
		}

		$descArrTeaser = substr ( $fullDesc, 0, $cutoffLength );
		$descArrRemainder = substr ( $fullDesc, $cutoffLength, $length );

		// If the length is shorter than the length to cut off from then don't display the 'read more' part at all
		if (!$found) {
			$this->mPage .= '<div>' . $descArrTeaser . '</div>';
		} else {
			$this->mPage .= '<div>' . $descArrTeaser . '<div id="readMoreBar"><a onclick="toggleProductOverview()" id="readMoreLink">READ FULL DESCRIPTION</a></div>';
			$this->mPage .= '<div id="productOverviewOverflow" style="display: none; margin-top: 10px;">' . $descArrRemainder . '</div></div>';
		}
	}


	//! Expects a string length 5 and tests it for </li>
	function IsListItemEnd($string) {
		if($string == '</li>') { return true; } else { return false; }
	}

	//! Expects a string length 3, and tests it for ://, www, com, co.uk
	function IsLinkSegment($string) {
		switch($string) {
			case 'www':
			case '://':
			case 'com':
			case 'co.':
				return true;
			break;
		}
		return false;
	}

	function LoadAdditionalImages() {
		$images = $this->mProduct->GetImages ();
		$i = 0;
		if (count ( $images ) > 1) {
			$this->mPage .= $this->mPublicLayoutHelper->OpenAdditionalImagesSection ();
			foreach ( $images as $image ) {
				if ($i < 4) {
					$this->mPage .= '
					<div id="additionalImageContainer'.$i.'" class="zoomedImageContainer">
					<h1 class="zoomedProductName">'.$this->mProduct->GetDisplayName().'</h1>
					<p class="zoomedProductText">Click the X above or press Escape to close!</p>
						<div class="zoomedImageInnerContainer">
							<table><tr><td style="vertical-align: middle; height: 400px; width: 400px; text-align: center;">
							'.$this->mPublicLayoutHelper->GetImageCode($image).'
							</td></tr></table>
						</div>
					</div>
					<div>
						<a href="#additionalImageContainer'.$i.'" id="additionalImageLink'.$i.'">
							' . $this->mPublicLayoutHelper->SmallImage ( $image ) . '
						</a>
					</div>
					';
					$i ++;
				}
			}
			$this->mPage .= $this->mPublicLayoutHelper->CloseAdditionalImagesSection ();
		}
	}

	function LoadMultibuy() {
		$allMultibuy = $this->mProduct->GetMultibuyDetails ();
		$quantityArr = array ();
		$this->mPage .= '<div id="multibuySection">
		<img src="'.$this->mBaseDir.'/images/multibuyBanner.jpg" id="multibuyBanner" width="30" height="150" />
		<img src="'.$this->mBaseDir.'/images/multibuyHeader.jpg" id="multibuyHeader" width="218" height="36" />';
		$this->mPage .= '<table id="multibuyTable">';
		$i = 0;
		foreach ( $allMultibuy as $multibuy ) {
			$this->mPage .= '<tr>';
			if ($i % 2) {
				$this->mPage .= '<td><strong>Buy ' . $multibuy ['quantity'] . '+</strong></td>';
				$this->mPage .= '<td>£' . $this->mPresentationHelper->Money ( $multibuy ['unitPrice'] ) . '</td>';
			} else {
				$this->mPage .= '<td class="altCell"><strong>Buy ' . $multibuy ['quantity'] . '+</strong></td>';
				$this->mPage .= '<td class="altCell">£' . $this->mPresentationHelper->Money ( $multibuy ['unitPrice'] ) . '</td>';
			}
			$i ++;
			$this->mPage .= '</tr>';
		}
		$this->mPage .= '<tr><td colspan="4">You Can Mix & Match Flavours!</td></tr>';
		$this->mPage .= '</table>';
		$this->mPage .= '</div>';
	}

	function LoadPriceMatch() {
		$this->mPage .= <<<HTMLOUTPUT
	<div id="priceMatchSection">
		<img src="{$this->mBaseDir}/images/priceMatchBanner.jpg" id="priceMatchBanner" width="30" height="145" />
		<img src="{$this->mBaseDir}/images/priceMatchHeader.jpg" id="priceMatchHeader" width="218" height="36" />
		<div id="priceMatchLoading" style="display: none; position: absolute; left: 30px; bottom: 50px; text-align: center;" >
			<h2>Submitting Request</h2>
			<img src="{$this->mBaseDir}/images/ajax-loader.gif" id="priceMatchLoadingImage" />
		</div>
		<div id="priceMatchSuccess" style="display: none; width: 150px; margin-left: 60px; text-align: center;">
			<h2>Success!</h2>
			We have received your request and will get back to you soon!
		</div>
		<form action="{$this->mFormHandlersDir}/PriceMatchHandler.php" method="post">
			<input type="text" name="priceMatchName" id="priceMatchName" value="your name" />
			<input type="text" name="priceMatchPhone" id="priceMatchPhone" value="your phone number" />
			<input type="text" name="priceMatchEmail" id="priceMatchEmail" value="your email address" />
			<input type="text" name="priceMatchWebsite" id="priceMatchWebsite" value="website to beat!" />
			<input type="hidden" name="productId" id="productId" value="{$this->mProduct->GetProductId()}" />
			<input type="image" src="{$this->mBaseDir}/images/submitButton.png" id="submitPriceMatchButton" />
		</form>
	</div>
HTMLOUTPUT;
	}

	//! Loads the in stock and enlarge image buttons
	function LoadButtons() {
	/*	// Get all SKUs for the product - if all of them are zero, then display out of stock button
		$displayOutOfStockImage = true;
		foreach($this->mProduct->GetSkus() as $sku) {
			// If one of the SKUs has stock then set the flag to false
			if($sku->GetQty() > 0) {
				$displayOutOfStockImage = false;
			}
		} // End foreach

		// Load in stock / out of stock images
		if ($this->mProduct->GetInStock() && !$displayOutOfStockImage) {
			$inStockSection = '<div id="inStock">
								<img src="' . $this->mBaseDir . '/images/inStockButton.png" width="110" height="24" />
							</div>';
		} else {
			$inStockSection = '<div id="inStock">
								<img src="' . $this->mBaseDir . '/images/notInStockButton.png" />
							</div>';
		}*/
		/******* now using 'next day delivery' or '3-5 day delivery' instead - old code above ******/
		$skus = $this->mProduct->GetSkus();

		// If a 'non stock' item (NOTE: USING THE CLEARANCE FLAG!!) then display 3-5 days regardless
		if($this->mProduct->GetOnClearance()) {
			$altText = 'Please allow 3-5 days for delivery.';
				$inStockSection = '<div id="inStock">
									<img src="' . $this->mBaseDir . '/images/buttonThreeFiveDayDelivery.gif" alt="'.$altText.'" title="'.$altText.'" />
								</div>';
		} else {
			// If a 'single SKU' product then if qty = 0 its 3-5 days, otherwise next day
			if(count($skus) == 1) {
				if($skus[0]->GetQty() > 0) {
				$altText = 'This product will be dispatched on a Next Day delivery!';
				$inStockSection = '<div id="inStock">
									<img src="' . $this->mBaseDir . '/images/buttonNextDayDelivery.gif" alt="'.$altText.'" title="'.$altText.'" />
								</div>';
				} else {
				$altText = 'Please allow 3-5 days for delivery.';
				$inStockSection = '<div id="inStock">
									<img src="' . $this->mBaseDir . '/images/buttonSoldOut.gif" alt="'.$altText.'" title="'.$altText.'" />
								</div>';
				}
			} else {
			// If a multiple SKU product then if one SKU is in stock then display next day, otherwise (all out) then 3-5 days
				$allOutOfStock = true;
				$oneOutOfStock = false; // Is ONE out of stock (for image alt text)
				// Assume all are out and if one ISNT then set it the other way
				foreach($skus as $sku) {
					if($sku->GetQty() > 0) {
						$allOutOfStock = false;
					} else {
						// This SKU has zero left
						$oneOutOfStock = true;
					}
				} // End foreach

				// Build correct button for stock
				if($allOutOfStock) {
					$altText = 'Please allow 3-5 days for delivery.';
					$inStockSection = '<div id="inStock">
									<img src="' . $this->mBaseDir . '/images/buttonSoldOut.gif" alt="'.$altText.'" title="'.$altText.'" />
								</div>';
				} else {
					// If one is out of stock give a message on the image alt text
					if($oneOutOfStock) {
						$altText = 'Sold Out items will take 3-5 days for delivery.';
					} else {
						$altText = 'This product will be dispatched on a Next Day delivery!';
					}
					// Build HTML
					$inStockSection = '<div id="inStock">
									<img src="' . $this->mBaseDir . '/images/buttonNextDayDelivery.gif" alt="'.$altText.'" title="'.$altText.'" />
								</div>';
				}
			} // End if single SKU product
		} // End if non stock item

		// Enlarge Button
		$registry = Registry::getInstance ();
		if ($this->mProduct->GetMainImage ()) {
			$imageDir = $this->mRootDir . '/' . $registry->originalImageDir;
			$width = $this->mProduct->GetMainImage ()->GetOriginalWidth ( $imageDir );
			$height = $this->mProduct->GetMainImage ()->GetOriginalHeight ( $imageDir ) + 100;
		} else {
			$width = 400;
			$height = 400;
		}
		$this->mPage .= '<div id="productButtons">
							<div id="enlarge">
								<a href="#mainProductImageContainer" id="mainProductImageLink2"><img src="' . $this->mBaseDir . '/images/enlargeImage.png" width="119" height="24" /></a>
							</div>
							' . $inStockSection . '
						</div>';
	}

	function LoadSecure() {
		$this->mPage .= '<div id="secureSection">
			<img src="'.$this->mBaseDir.'/images/secureBanner.jpg" id="secureBanner" />
			<img src="'.$this->mBaseDir.'/images/secureHeader.jpg" id="secureHeader" />
		</div>';
	}

	//! Load similar products
	function LoadSimilar() {
		$this->mPage .= '<div id="similarProductContainer">';
		for($i = 0; $i < $this->mMaxSimilar; $i ++) {
			$this->mPage .= '<div class="similarProduct">';
			$this->mPage .= '<div class="imageContainer">';
			$this->mPage .= '<a href="' . $this->mPublicLayoutHelper->LoadLinkHref ( $this->mAllSimilar [$i] ) . '">';
			$this->mPage .= $this->mPublicLayoutHelper->MediumProductImage ( $this->mAllSimilar [$i] );
			$this->mPage .= '</a>';
			$this->mPage .= '</div>';
			$this->mPage .= '<div class="titleContainer">';
			$this->mPage .= '<strong><a href="' . $this->mPublicLayoutHelper->LoadLinkHref ( $this->mAllSimilar [$i] ) . '">' . $this->mAllSimilar [$i]->GetDisplayName () . '</a></strong>';
			$this->mPage .= '</div>';
			$this->mPage .= '<div class="pricesContainer">';
			$this->LoadPrices( $this->mAllSimilar[$i]);
			$this->mPage .= '</div>';
			$this->mPage .= '</div>';
		}
		$this->mPage .= '</div>';
	}

	//! Load related products
	function LoadRelated() {
		$this->mPage .= '<div id="relatedProductContainer">';
		for($i = 0; $i < $this->mMaxRelated; $i ++) {
			$this->mPage .= '<div class="relatedProduct">';
			$this->mPage .= '<div class="imageContainer">';
			$this->mPage .= '<a href="' . $this->mPublicLayoutHelper->LoadLinkHref ( $this->mAllRelated [$i] ) . '">';
			$this->mPage .= $this->mPublicLayoutHelper->MediumProductImage ( $this->mAllRelated [$i] );
			$this->mPage .= '</a>';
			$this->mPage .= '</div>';
			$this->mPage .= '<div class="titleContainer">';
			$this->mPage .= '<strong><a href="' . $this->mPublicLayoutHelper->LoadLinkHref ( $this->mAllRelated [$i] ) . '">' . $this->mAllRelated [$i]->GetDisplayName () . '</a></strong>';
			$this->mPage .= '</div>';
			$this->mPage .= '<div class="pricesContainer">';
			$this->LoadPrices( $this->mAllRelated[$i]);
			$this->mPage .= '</div>';
			$this->mPage .= '</div>';
		}
		$this->mPage .= '</div>';
	}

	//! Loads the product reviews section
	function LoadProductReviews() {
		//*** Existing Reviews Will Go Here ***//
		$reviews = $this->mProduct->GetApprovedReviews();
		$totalStars = 0;
		foreach($reviews as $review) {

			// Stars Code
			$litStars = $review->GetRating();
			$litCode = '';
			$unlitStars = 5 - $litStars;
			$unlitCode = '';
			$totalStars += $review->GetRating();

			// Correct Number of stars
			for($i=0;$i<$litStars;$i++) {
				$litCode .= '<img src="'.$this->mBaseDir.'/images/shinyStar.jpg" />';
			}
			for($i=0;$i<$unlitStars;$i++) {
				$unlitCode .= '<img src="'.$this->mBaseDir.'/images/shinyStarOut.jpg" />';
			}

			// Generate Page
			$this->mPage .= <<<EOT
			<h2 class="reviewerName">{$review->GetName()}</h2> <div class="starsBox">{$litCode}{$unlitCode}</div>
			<br style="clear: both" />
			<blockquote class="speechBubble">
				<p>"{$review->GetText()}"</p>
			</blockquote>

EOT;
		}

		//***** Load Microformat Data *****//
		if(count($reviews) != 0) {
			$reviewCount = count($reviews);
			$averageRating = ceil($totalStars / $reviewCount);
			$this->mPage .= '<div class="hreview-aggregate" style="text-align: center;">
							   <span class="item">
								  <span class="fn">'.$this->mProduct->GetDisplayName().'</span>
							   </span>
							   <span class="rating">
								  <span class="average">'.$averageRating.'</span> out of
								  <span class="best">5</span>
							   </span>
							   based on
							   <span class="count">'.$reviewCount.'</span> ratings.
							</div>';
		}

		$this->mPage .= '<br style="clear: both" />';
		$this->mPage .= '<a name="addReview"></a>';
		/** Add Review Form **/
		$this->mPage .= '
						<form name="addReviewForm" id="addReviewForm" method="post" action="'.$this->mFormHandlersDir.'/AddReviewHandler.php">
							<input type="hidden" name="reviewProduct" id="reviewProduct" value="'.$this->mProduct->GetProductId().'" />
							<input type="hidden" name="reviewIP" id="reviewIP" value="'.$_SERVER['REMOTE_ADDR'].'" />
							<h4>ADD REVIEW</h4>
								<div class="falseLabel"><label for="reviewName">Your Name: </label></div>
								<div class="falseInput"><input type="text" name="reviewName" id="reviewName" maxlength="50" /></div>
							<br style="clear: both" />
								<div class="falseLabel"><label for="reviewRating" id="productRatingLabel">Product Rating: </label></div>
								<div class="falseInput">
					            	<input type="radio" name="reviewRating" id="reviewRating" value="1" class="star" />
									<input type="radio" name="reviewRating" id="reviewRating" value="2" class="star" />
									<input type="radio" name="reviewRating" id="reviewRating" value="3" class="star" />
									<input type="radio" name="reviewRating" id="reviewRating" value="4" class="star" />
									<input type="radio" name="reviewRating" id="reviewRating" value="5" class="star" />
								</div>
								<textarea id="reviewText" name="reviewText" rows="5" cols="80">Your Review</textarea>
								<div id="reviewButtonContainer">
									<input type="image" src="' . $this->mBaseDir . '/images/addReviewButton.png" id="addReviewButton" name="addReviewButton" />
								</div>
						</form>
						<div id="reviewLoading" style="display: none; text-align: center; margin: 10px; margin-bottom: 30px;" >
							<h2 style="margin: 3px">Submitting Review...</h2>
							<img src="'.$this->mBaseDir.'/images/ajax-loader.gif" id="priceMatchLoadingImage" style="float: none" />
						</div>
						<div id="reviewSuccess" style="display: none; text-align: center; margin: 10px; margin-bottom: 30px;">
							<h2 style="margin: 3px">Success!</h2>
							We have received your review and it should appear live soon!
						</div>

						';
	} // End LoadProductReviews

	//! Load the product title, options and prices
	function LoadProductText() {
		$this->mPage .= '	<div id="productText">
									<h1>' . $this->mProduct->GetDisplayName () . '</h1>';
		$this->LoadPrices($this->mProduct,true);
		if ($this->mProduct->GetForSale () && !$this->mProduct->GetHidden()) {
			if (count ( $this->mAllAttributes ) > 0) {
				$this->mPage .= $this->LoadOptions();
			} else {
				$this->mPage .= $this->LoadSimpleBuyNow();
			}
		} else {
			$this->mPage .= $this->LoadCallToBuy ();
		}
		$this->LoadSlogan();
		if ($this->mManufacturer && $this->mManufacturer->GetSizeChart ()) {
			$this->LoadSizeChart();
		}
		$this->mPage .= '</div> <!-- End productText -->';
	}

	function LoadSlogan() {
#		$this->mPage .= '<img src="'.$this->mBaseDir.'/images/slogan.gif" />';
	}

	//! Load the large product image
	function LoadProductImage() {
		$this->mPage .= '<div id="productImage"><a href="#mainProductImageContainer" id="mainProductImageLink">';
		$this->mPage .= '<table><tr><td style="width: 300px; height: 300px; vertical-align: middle; text-align: center;">';
		$this->mPage .= $this->mPublicLayoutHelper->LargeProductImage ( $this->mProduct, 'mainProductImage' );
		$this->mPage .= '</td></tr></table>';
		$this->mPage .= '</div></a>';
		$this->LoadZoomedProductImage();
	}

	//! Loads the container with the zoomed product image and container
	function LoadZoomedProductImage() {
		$this->mPage .= '<div id="mainProductImageContainer">
								<h1 class="zoomedProductName">'.$this->mProduct->GetDisplayName().'</h1>
								<p class="zoomedProductText">Click the X above or press Escape to close!</p>
								<table style="width: 100%; height: 100%;"><tr><td style="vertical-align: middle; text-align: center;">
								<br />'.$this->mPublicLayoutHelper->LargestProductImage ( $this->mProduct, 'XLProductImage' ).'
								</td></tr></table>
						</div>';
	}

	//! Load the product name with breadcrumb nav in the bar
	function LoadProductTitle() {
		$breadCrumbs = new ProductBreadCrumbView ( );
		$this->mPage .= '<div id="productTitle">';
		$this->mPage .= $breadCrumbs->LoadDefault ( $this->mProduct );
		$this->mPage .= '</div><div style="clear: both"></div>';
	}

	//! Buy now / add to basket button
	function LoadSimpleBuyNow() {
		$this->mPage .= '<form action="' . $this->mBaseDir . '/formHandlers/AddProductToBasketHandler.php" method="post" onsubmit="return validateForm(this)" />
						<input type="hidden" name="referPage" id="referPage" value="productDetailView" />
						<input type="hidden" name="addToBasket" id="addToBasket" value="1" />
						<input type="hidden" name="productId" id="productId" value="' . $this->mProduct->GetProductId () . '" />
						<input type="hidden" name="basketId" id="basketId" value="' . $this->mBasketId . '" />
						<input type="hidden" name="categoryId" id="categoryId" value="' . $this->mCategory->GetCategoryId () . '" />';
		$this->mPage .= '<div id="productOptions"><h3>Buy Now</h3><div id="optionsContainer">';
		$multibuyEnabled = $this->mProduct->GetMultibuy ();
		if ($multibuyEnabled) {
			$this->mPage .= '<select id="multibuyQuantity" name="multibuyQuantity"><option style="font-weight: bold; text-align: center;" value="NA">Multibuy Discount</option>';
			$multibuyDetails = $this->mProduct->GetMultibuyDetails ();

			// Get some max/min values
			foreach ( $multibuyDetails as $multibuy ) {
				$quantityArr [$multibuy ['quantity']] = $multibuy ['quantity'];
				$priceArr [$multibuy ['quantity']] = $multibuy ['unitPrice'];
			}

			// Limits
			$upTo = max ( $quantityArr );
			$downTo = min ( $quantityArr );

			// Make the select list
			$runningPrice = $this->mProduct->GetActualPrice ();
			for($i = 1; $i < $upTo + 1; $i ++) {
				$this->mPage .= '<option value="' . $i . '">Buy ' . $i;
				if (in_array ( $i, $quantityArr )) {
					$runningPrice = $priceArr [$i];
				}
				$this->mPage .= ' - £' . $this->mPresentationHelper->Money($runningPrice) . ' Each';
				$this->mPage .= '</option>';
			}

			$this->mPage .= '</select>';
		}
		$this->mPage .= '<input type="image" src="' . $this->mBaseDir . '/images/buyNowButton2.png" /></div><div id="errorBox"></div></div>';
		$this->mPage .= '</form>';
	}

	//! Load attributes
	function LoadOptions() {
		$this->mPage .= '
						<form action="' . $this->mBaseDir . '/formHandlers/AddProductToBasketHandler.php" method="post" onsubmit="return validateForm(this)" />
						<input type="hidden" name="referPage" id="referPage" value="productDetailView" />
						<input type="hidden" name="addToBasket" id="addToBasket" value="1" />
						<input type="hidden" name="productId" id="productId" value="' . $this->mProduct->GetProductId () . '" />
						<input type="hidden" name="basketId" id="basketId" value="' . $this->mBasketId . '" />
						<input type="hidden" name="categoryId" id="categoryId" value="' . $this->mCategory->GetCategoryId () . '" />';
		if ($this->mCategory->GetParentCategory ()) {
			$this->mPage .= '<input type="hidden" name="parentCategoryId" id="parentCategoryId" value="' . $this->mParentCategory->GetCategoryId () . '" />';
		}
		$this->mPage .= '<div id="productOptions">
							<h3>PRODUCT OPTIONS</h3>
							<div id="optionsContainer">';
		//<span>Please select an option:</span>';
		foreach ( $this->mAllAttributes as $attribute ) {
			$this->mPage .= '<select id="skuAttribute' . $attribute->GetProductAttributeId () . '" name="skuAttribute' . $attribute->GetProductAttributeId () . '"><option value="NA" style="font-weight: bold; text-align: center;" value="NA">' . $attribute->GetAttributeName () . '</option>';
			$allSkuAttributes = $attribute->GetSkuAttributes ();
			$values = array ();
			// Loop over all SKU attributes for this product attribute
			foreach ( $allSkuAttributes as $skuAttribute ) {
				$sku = new SkuModel($skuAttribute->GetSkuId());
				if($sku->GetQty() == 0) {
					$stockMessage = 'SOLD OUT - ';
				} else {
					$stockMessage = '';
				}
				// Build Options Menu
				if (! in_array ( trim ( $skuAttribute->GetAttributeValue () ), $values )) {
					$this->mPage .= '<option value="' . $skuAttribute->GetSkuAttributeId () . '">'.$stockMessage . $skuAttribute->GetAttributeValue () . '</option>';
				}
				$values [] = trim ( $skuAttribute->GetAttributeValue () );
			}
			$this->mPage .= '</select>';
		}
		$multibuyEnabled = $this->mProduct->GetMultibuy ();
		if ($multibuyEnabled) {
			$this->mPage .= '<select id="multibuyQuantity" name="multibuyQuantity"><option style="font-weight: bold; text-align: center;" value="NA">Multibuy Discount</option>';
			$multibuyDetails = $this->mProduct->GetMultibuyDetails ();

			// Get some max/min values
			foreach ( $multibuyDetails as $multibuy ) {
				$quantityArr [$multibuy ['quantity']] = $multibuy ['quantity'];
				$priceArr [$multibuy ['quantity']] = $multibuy ['unitPrice'];
			}

			// Limits
			$upTo = max ( $quantityArr );
			$downTo = min ( $quantityArr );

			// Make the select list
			$runningPrice = $this->mProduct->GetActualPrice ();
			for($i = 1; $i < $upTo + 1; $i ++) {
				$this->mPage .= '<option value="' . $i . '">Buy ' . $i;
				if (in_array ( $i, $quantityArr )) {
					$runningPrice = $priceArr [$i];
				}
				$this->mPage .= ' - £' . $runningPrice . ' Each';
				$this->mPage .= '</option>';


			}

			#foreach($multibuyDetails as $multibuy) {
			#	$this->mPage .= '<option value="'.$multibuy['quantity'].'">Buy '.$multibuy['quantity'].' - £'.$multibuy['unitPrice'].' Each</option>';
			#}


			$this->mPage .= '</select>';
		}
		$this->mPage .= '	<input type="image" src="' . $this->mBaseDir . '/images/buyNowButton2.png" />
							<div id="errorBox"></div>
							</div> <!-- Close optionsContainer -->
						</div> <!-- Close productOptions -->
						</form>';
	}

	//! If the product is not for sale (online) display the call to buy button
	function LoadCallToBuy() {
		$this->mPage .= '<div id="productOptions"><h3>Currently Not Available</h3><div id="optionsContainer">
		This product is currently unavailable.
		<!-- <img src="' . $this->mBaseDir . '/images/callToBuyButton.png" style="float: none;" /> -->
		</div></div>';
	}

	//! "Size Charts" button links to the manufacturer content
	function LoadSizeChart() {
		$sizeChart = $this->mManufacturer->GetSizeChart ();
		$this->mPage .= '<div id="sizeChart" style="z-index: 9;"><a href="' . $this->mBaseDir . '/content.php?id=' . $sizeChart->GetContentId () . '"><img src="' . $this->mBaseDir . '/images/sizeChart.gif" /></a></div>';
	}

	//! Load the product prices
	/*!
	 * @param $product - The product to look at
	 * @param $savingSection - Boolean - Whether or not to include the savings section
	 */
	function LoadPrices($product,$enableSavingSection=false) {
		// Load the VAT section so as not to display VAT-Free prices if the product is exempt
		if (intval ( $this->mProduct->GetTaxCode ()->GetRate () ) != 0) {
			$vatSection = ''; //$vatSection = '<span style="font-size: 10pt; text-decoration: none;">(&pound;' . $this->mPresentationHelper->Money ( $this->mMoneyHelper->RemoveVAT ( $product->GetActualPrice () ) ) . ' ex VAT)</span>';
		} else {
			$vatSection = '';
		}

		// Load the 'Was' section so only if a was price exists (ie. was isnt = 0)
		if(0 == intval($product->GetWasPrice())) {
			if (intval ( $this->mProduct->GetActualPrice () ) == 0) {
				$wasSection = '';
			} else {
				$wasSection = '<div id="productWasPrice">' . $vatSection . '</div>';
			}
		} else {
			$wasSection = '<div id="productWasPrice">
							<span style="text-decoration: line-through">Was &pound;' . $this->mPresentationHelper->Money ( $product->GetWasPrice () ) . '</span>
							' . $vatSection . '
							</div>';
		}

		switch ($this->mCategory->GetCatalogue ()->GetPricingModel ()->GetPricingModelId ()) {
			case 1 :
				// Regular
				$freeDelivery = '';
				break;
			case 2 :
				// Shooting
				$freeDelivery = '';
				break;
		}
		if (floatval ( $this->mProduct->GetActualPrice () ) == 0) {
			$nowSection = '<div class="productNowPrice">
									Call for Price
								</div>';
		} else {
			if ("0.0" == $product->GetPostage () && "0.0" == $product->GetWasPrice ()) {
				$nowSection = '<div id="productNowPrice">
									Only &pound;' . $this->mPresentationHelper->Money ( $product->GetActualPrice () ) . ' ' . $freeDelivery . '
								</div>';
			} elseif ("0.0" != $product->GetPostage () && "0.0" == $product->GetWasPrice ()) {
				$nowSection = '<div id="productNowPrice">
									Only &pound;' . $this->mPresentationHelper->Money ( $product->GetActualPrice () ) . '
								</div>';
			} elseif ("0.0" == $product->GetPostage () && "0.0" != $product->GetWasPrice ()) {
				$nowSection = '<div id="productNowPrice">
									Now &pound;' . $this->mPresentationHelper->Money ( $product->GetActualPrice () ) . ' ' . $freeDelivery . '
								</div>';
			} elseif ("0.0" != $product->GetPostage () && "0.0" != $product->GetWasPrice ()) {
				$nowSection = '<div id="productNowPrice">
									Now &pound;' . $this->mPresentationHelper->Money ( $product->GetActualPrice () ) . '
								</div>';
			}
		}

		// Load the 'saving' between was/now prices
		if($this->mProduct->GetWasPrice() > 0) {
			$savingSection = '
							<div id="productSaving">
								Save &pound;'.$this->mPresentationHelper->Money($product->GetSaving()).' - '.$product->GetSaving(true).'% Off!
							</div>
							';
		} else {
			$savingSection = '';
		}

		// Overwrite $savingSection if over £45 for free delivery
		if($this->mProduct->GetActualPrice() >= 45 && !$this->mProduct->GetOnClearance()) {
			$savingSection = '
							<div id="productSaving">
								<span style="text-decoration: underline">FREE NEXT DAY DELIVERY</span>
							</div>';
		}

		// Only add the saving section if req.
		if($enableSavingSection) {
			$this->mPage .= $nowSection.$wasSection.$savingSection;
		} else {
			$this->mPage .= $nowSection.$wasSection;
		}
	} // End LoadPrices

} // End ProductDetailView

?>