<?php
//! Defines the basket page
class BasketView extends View {

	var $mCategory;
	var $mSystemSettings;
	var $mSessionHelper;

	//! Consructor, does some setup
	/*!
	 * @param $catalogue - CatalogueModel - Which catalogue are we in?
	 */
	function __construct($catalogue) {
		// Params
		$this->mCatalogue = $catalogue;

		// CSS Extras
		#$cssIncludes = array('BasketView.css.php','jquery.alerts.css.php','jqueryUi.css');
		$cssIncludes = array();
		// JS Extras
		$jsIncludes = array('jquery.alerts.js','BasketView.js','jqueryUi.js');

		// Construct
		parent::__construct($this->mCatalogue->GetDisplayName().' > Shopping Basket',$cssIncludes,$jsIncludes);

		// Get some member vars set up
		$this->mSessionHelper 		= new SessionHelper();
		$this->mSystemSettings 		= new SystemSettingsModel($this->mCatalogue);
		$this->mPublicLayoutHelper 	= new PublicLayoutHelper();
		$this->mPricingModel 		= $this->mCatalogue->GetPricingModel();
		$this->mBasket 				= $this->mSessionHelper->GetBasket();
	} // End __construct()

	//! Generic loader
	function LoadDefault() {
		$footerView = new FooterView ( );
		$this->mPage .= $this->mPublicLayoutHelper->OpenBody ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenCentrePageContainer ();
		parent::LoadHeaderSection($this->mCatalogue, true);
		parent::LoadNavigation();
		parent::LoadLeftColumn ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenRightCol ();
		$this->LoadMainContentColumn ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseRightCol ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenFooterContainer ();
		$this->mPage .= $footerView->LoadDefault ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseFooterContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseCentrePageContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseBody (true);
		$this->mPage .= $this->mPublicLayoutHelper->CloseHtml ();
		return $this->mPage;
	} // End LoadDefault()

	// Loads the center column
	function LoadMainContentColumn() {
		$this->mPage .= $this->mPublicLayoutHelper->OpenMainColumn ();
		$this->mPage .= $this->mPublicLayoutHelper->LoadBasketHeader ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenBasket ();

		// Load a country if none already set
		if(!isset($_SESSION ['countryId'])) {
			$countryController = new CountryController ( );
			$this->mDefaultCountry = $countryController->GetDefault ();
			$this->mSessionHelper->SetCountry ( $this->mDefaultCountry->GetCountryId () );
		} else {
			$this->mDefaultCountry = new CountryModel ( $_SESSION ['countryId'] );
		}

		// Load a postage method if none already set
		if (!$this->mSessionHelper->GetPostageMethod()) {
		#echo 'def';
			$postageMethodController = new PostageMethodController ( );
			$this->mDefaultMethod = $this->mBasket->GetDefaultPostageMethod ();
	#		$this->mSessionHelper->SetPostageMethod ( $this->mDefaultMethod->GetPostageMethodId () );
		} else {
		#echo 'proper';
			$this->mDefaultMethod = new PostageMethodModel ( $this->mSessionHelper->GetPostageMethod() );
		}
	#		echo $this->mDefaultMethod->GetPostageMethodId();

		// Display a message if anything is 3-5 day dispatch
		$productList = $this->mBasket->ContainsNonStockProducts(true);
		if($productList !== false) {
			$this->mPage .= '<h3>Please be aware that the dispatch estimate for this order is: <span style="color: #F00;">3-5 days</span></h3>';
			$this->mPage .= 'This is due to the following products: '.$productList.'<br />';
			$this->mPage .= '(These products are not yet stock items so take longer to be delivered)<br />';
		} else {
			// V Fest warning..
		#	if(date('j/n/Y') == '19/8/2011' || date('j/n/Y') == '18/8/2011') {
		#		$this->mPage .= '<h3>Please be aware that orders placed after 4pm Thursday 18th or on Friday 19th will be dispatched on Monday 22nd August</h3>';
		#	}
		}

		// Christmas Times
	#	$this->mPage .= '<h3>Echo Supplements will be closed from <u>22<sup>nd</sup> December until 2<sup>nd</sup> January</u>. <br>Therefore the last shipping day before Christmas will be Friday 21st December - please choose courier delivery for guaranteed delivery before Christmas.</h3>';


		// Display a message if anything is 3-5 day dispatch
		$productList = $this->mBasket->ContainsSoldOutProducts(true);
		if($productList !== false) {
			$this->mPage .= '<h3>Please be aware that the dispatch estimate for this order is: <span style="color: #F00;">3-5 days</span></h3>';
			$this->mPage .= 'This is due to the following products: '.$productList.'<br />';
			$this->mPage .= '(These products are currently <strong>sold out</strong> so take longer to be delivered)<br />';
		}

		// Load the products in the basket
		$this->LoadBasketContents();

		// Close the page
		$this->mPage .= $this->mPublicLayoutHelper->CloseBasket();
		$this->mPage .= $this->mPublicLayoutHelper->LoadBasketFooter();
		$this->mPage .= $this->mPublicLayoutHelper->CloseMainColumn ();
	} // End LoadMainContentColumn()

	//! Load the products in the basket
	/*************
	 VAT-Free Note
	 If the country has been changed to a VAT-Free one (eg. BFPO) then there are 2 possibilities:
	 	For a Package/Stack - Depends entirely on the $registry->packageVatFreeAllowed setting - if true then the VAT will be removed from the package
		For a SKU			- The VAT is only removed if the product does not have a 0% tax rate (ie. is exempt)
	 *************/
	function LoadBasketContents() {
		// Do we have packages?
		if(count($this->mBasket->GetPackages())==0) {
			$this->mShowPackage = false;
		} else {
			$this->mShowPackage = true;
		}
		// Do we have ANYTHING?
		if(count($this->mBasket->GetSkus()) == 0 && count($this->mBasket->GetPackages())==0) {
			$this->mPage .= 'Your basket is empty.';
		} else {
			$this->OpenTable();
			$skusPrepArr = array();
			$upgradesArr = array();
			$prevUpgrades = array();
			$this->mCurrentPostage = 0;
			$this->mRunningTotal = 0;
#		var_dump($this->mBasket->GetSkus (false,true,false,true));die();
			// Make a 'prepped' array so its easier to loop over
			foreach ( $this->mBasket->GetSkus (false,true,false,true) as $sku ) {
				if(isset($skusPrepArr[$sku->GetSkuId()]) && !$this->mBasket->IsPackage($sku) && !$this->mBasket->IsPackageUpgrade($sku)) {
					// Just increment and adjust things
					$skusPrepArr [$sku->GetSkuId ()] ['qty'] ++;
					$skusPrepArr [$sku->GetSkuId ()] ['totalPrice'] = $skusPrepArr [$sku->GetSkuId ()] ['qty'] * $skusPrepArr [$sku->GetSkuId ()] ['unitPrice'];
				} else {
					// Add to the prepped array
					if ($this->mBasket->IsPackageUpgrade ( $sku ) && ! in_array ( $sku->GetSkuId (), $prevUpgrades )) {
						$upgradesArr [$sku->GetSkuId ()] ['qty'] = 1;
						$upgradesArr [$sku->GetSkuId ()] ['unitPrice'] = $this->mBasket->GetOverruledSkuPrice ( $sku, false, true );
						$upgradesArr [$sku->GetSkuId ()] ['totalPrice'] = $this->mBasket->GetOverruledSkuPrice ( $sku, false, true );
						$prevUpgrades [] = $sku->GetSkuId ();
					} else {
						$skusPrepArr [$sku->GetSkuId ()] ['qty'] = 1;
						$skusPrepArr [$sku->GetSkuId ()] ['unitPrice'] = $this->mBasket->GetOverruledSkuPrice ( $sku, false, false );
						$skusPrepArr [$sku->GetSkuId ()] ['totalPrice'] = $this->mBasket->GetOverruledSkuPrice ( $sku, false, false );
					}
				}
				$product = $sku->GetParentProduct ();
				if ($product->GetPostage () > $this->mCurrentPostage) {
					$this->mCurrentPostage = $product->GetPostage ();
				}
			} // End prepping


			$packagesPrepArr = array ();
			// Make a 'prepped' array so its easier to loop over
			foreach($this->mBasket->GetPackages() as $package ) {
				if (isset($packagesPrepArr[$package->GetPackageId()])) {
					$packagesPrepArr[$package->GetPackageId()]['qty'] ++;
					$packagesPrepArr[$package->GetPackageId()]['totalPrice'] = $packagesPrepArr[$package->GetPackageId()]['qty'] * $packagesPrepArr[$package->GetPackageId()]['unitPrice'];
				} else {
					$packagesPrepArr [$package->GetPackageId ()] ['qty'] = 1;
					$packagesPrepArr[$package->GetPackageId()]['unitPrice']  = $this->mBasket->GetOverruledPackagePrice($package);
					$packagesPrepArr[$package->GetPackageId()]['totalPrice'] = $this->mBasket->GetOverruledPackagePrice($package);
				}
				if ($package->GetPostage() > $this->mCurrentPostage) {
					$this->mCurrentPostage = $package->GetPostage();
				}
			} // End prepping


			// Show Packages
			if ($this->mShowPackage) {
				$allPackages = $this->mBasket->GetPackages ();
				foreach ( $packagesPrepArr as $key => $preppedPack ) {
					$package = new PackageModel ( $key );
					$packageContents = $package->GetContents ();
					$packageContentsDisplay = '';
					foreach ( $packageContents as $product ) {
						if($package->GetProductQty($product)>1) {
							$qty = $package->GetProductQty($product).' x ';
						} else {
							$qty = '';
						}
						$packageContentsDisplay .= ' - '.$qty.' ' . $this->mPresentationHelper->ChopDown ( $product->GetDisplayName (), 25, true ) . '<br />';
					}
					$this->mPage .= '<tr>	<td id="productColumn"><strong>' . $package->GetDisplayName () . '</strong><br />' . $packageContentsDisplay . '</td>
											<td id="qtyColumn">' . $packagesPrepArr [$package->GetPackageId ()] ['qty'] . '</td>
											<td id="unitPriceColumn">&pound;' . $this->mPresentationHelper->Money ( $packagesPrepArr [$package->GetPackageId ()] ['unitPrice'] ) . '</td>
											<td id="totalPriceColumn">&pound;' . $this->mPresentationHelper->Money ( $packagesPrepArr [$package->GetPackageId ()] ['totalPrice'] ) . '</td>
											<td id="removeColumn"><form action="' . $this->mBaseDir . '/formHandlers/RemovePackageHandler.php" method="post">
											<input type="hidden" name="packageToRemove" id="packageToRemove" value="' . $package->GetPackageId () . '" />
											<input type="image" src="' . $this->mBaseDir . '/images/shoppingBagRemoveItem.gif" />
										</form></td>
									</tr>';
				}

				// Show Upgrades
				foreach ( $upgradesArr as $key => $preppedUpgrade ) {
					$sku = new SkuModel ( $key );
					$skuAttributes = $sku->GetSkuAttributes ();
					if (count ( $skuAttributes ) != 0) {
						$options = '(';
						foreach ( $skuAttributes as $skuAttribute ) {
							$options .= $skuAttribute->GetAttributeValue ();
						}
						$options .= ')';
					} else {
						$options = '';
					}
					$href = $this->mPublicLayoutHelper->LoadLinkHref ( $sku->GetParentProduct () );
					$this->mPage .= '<tr>	<td id="productColumn"><strong>Upgrade: </strong><a href="' . $href . '">' . $sku->GetParentProduct ()->GetDisplayName () . '</a> ' . $options . '</td>
											<td id="qtyColumn">' . $upgradesArr [$sku->GetSkuId ()] ['qty'] . '</td>
											<td id="unitPriceColumn">&pound;' . $this->mPresentationHelper->Money ( $upgradesArr [$sku->GetSkuId ()] ['unitPrice'] ) . '</td>
											<td id="totalPriceColumn">&pound;' . $this->mPresentationHelper->Money ( $upgradesArr [$sku->GetSkuId ()] ['totalPrice'] ) . '</td>
											<td id="removeColumn"></td>
									</tr>';
				}
			}

			// Show SKUs
			foreach ( $skusPrepArr as $key => $preppedRow ) {
				$sku = new SkuModel ( $key );
				$skuAttributes = $sku->GetSkuAttributes ();
				$skuAttributesList = '';
				if (count ( $skuAttributes ) > 0) {
					$skuAttributesList .= '<br />(';
				}
				foreach ( $skuAttributes as $skuAttribute ) {
					$skuAttributesList .= $skuAttribute->GetAttributeValue () . ', ';
				}
				if (count ( $skuAttributes ) > 0) {
					$skuAttributesList = substr ( $skuAttributesList, 0, (count ( $skuAttributesList ) - 3) );
					$skuAttributesList .= ')';
				}
				$parentProduct = $sku->GetParentProduct ();
				$parentCategories = $parentProduct->GetCategories ();
				$parentCategory = $parentCategories [0];
				$categoryPart = $this->mValidationHelper->MakeLinkSafe ( $parentCategory->GetDisplayName () );
				if ($parentCategory->GetParentCategory ()) {
					$categoryPart .= '/' . $this->mValidationHelper->MakeLinkSafe ( $parentCategory->GetParentCategory ()->GetDisplayName () ) . '/';
				}
				$urlToProduct = $categoryPart . '/product/' . $this->mValidationHelper->MakeLinkSafe ( $parentProduct->GetDisplayName () ) . '/' . $parentProduct->GetProductId ();
				$this->mPage .= '<input type="hidden" name="skuId" id="skuId" value="' . $sku->GetSkuId () . '" />
								 <input type="hidden" name="basketId" id="basketId" value="' . $this->mBasket->GetBasketId () . '" />';
				$this->mPage .= '
								<tr>
										<td id="productColumn"><a href="' . $this->mBaseDir . '/department/' . $urlToProduct . '">' . $sku->GetParentProductName () . '</a>' . $skuAttributesList . '</td>
										<td id="qtyColumn">';
				if($this->mBasket->GetFreeOfferApplied() && $sku->GetParentProduct()->GetProductId() == $this->mRegistry->freeOfferProductId) {
					$this->mPage .= '1';
				} else {
					$this->mPage .= '		<form action="' . $this->mBaseDir . '/formHandlers/QuantityChangeHandler.php" method="post">
											<input type="hidden" name="skuId" id="skuId" value="' . $sku->GetSkuId () . '" />
												<input type="text" value="' . $preppedRow ['qty'] . '" name="skuQty" id="skuQty" onChange="this.form.submit()" />
											</form>';
				}
				$this->mPage .= '		</td>
										<td id="unitPriceColumn">&pound;' . $this->mPresentationHelper->Money ( $preppedRow ['unitPrice'] ) . '</td>
										<td id="totalPriceColumn">&pound;' . $this->mPresentationHelper->Money ( $preppedRow ['totalPrice'] ) . '</td>
										<td id="removeColumn">';
				if($this->mBasket->GetFreeOfferApplied() && $sku->GetParentProduct()->GetProductId() == $this->mRegistry->freeOfferProductId) {
					$this->mPage .= '';
				} else {
					$this->mPage .= '<form action="' . $this->mBaseDir . '/formHandlers/RemoveSkuHandler.php" method="post">
										<input type="hidden" name="skuToRemove" id="skuToRemove" value="' . $sku->GetSkuId () . '" />
										<input type="image" src="' . $this->mBaseDir . '/images/shoppingBagRemoveItem.gif" />
									</form>';
				}
				$this->mPage .= '</td></tr>';
			}

			// Sort out postage
			$this->WorkOutPostage();
			$this->AddUpgradesToPostage();
			$this->mSessionHelper->SetPostage($this->mCurrentPostage);

			// Load totals
			$this->LoadTotalsSection ();

			// Close the table
			$this->CloseTable();
		/*	$this->mPage .= '<h3>Christmas Delivery Times</h3>';
			$this->mPage .= '<strong>Please be aware that due to the high volume of parcels being sent at this time of year Interlink &amp; Royal Mail have advised us of
			possible delays. Orders will be processed as normal but please bear this in mind!</strong>';*/
			$this->LoadCheckoutButton();
			$this->LoadWarningMessage();

			// Load freebie offers
			if($this->mRegistry->hasFreeOffer && $this->mBasket->GetTotal() < $this->mRegistry->freeOfferLimit) {
				$this->LoadFreeOffer();
			} else {
				// If the product IS added, do nothing otherwise add the product
				if($this->mRegistry->hasFreeOffer && !$this->mBasket->GetFreeOfferApplied()) {
					$this->mFreebieProduct 	= new ProductModel($this->mRegistry->freeOfferProductId);
					// Let them claim the t-shirt
					$this->LoadClaimFreeOffer();
				}
				// What if they remove something, thereby reducing below £75 once the tshirt has already been added?
			}
		}
	} // End LoadBasketContents

	//! Load a message telling people that they must get it right here!
	function LoadWarningMessage() {
		$this->mPage .= '
		<div id="warningMessage">
			'.$this->mRegistry->basketMessage.'
		</div>';
	} // End LoadWarningMessage

	//! Load a message telling people about a free offer (if it exists)
	function LoadClaimFreeOffer() {
		$this->mPage .= '
		<div id="claimFreeOfferContainer">
			'.$this->mRegistry->freeOfferClaimMessage.'
			<div id="freeOfferDialog">
			<table>
				<tr>
					<td rowspan="2" id="smallImageCell">'.$this->mPublicLayoutHelper->SmallProductImage($this->mFreebieProduct).'</td>
					<td id="productName"><strong>'.$this->mFreebieProduct->GetDisplayName().'</strong></td>
				</tr>
				<tr>
					<td>'.$this->LoadProductOptions($this->mFreebieProduct).'</td>
				</tr>
			</table>
			</div>
		</div>
		';
	} // End LoadWarningMessage

	//! Load attributes
	function LoadProductOptions() {
		$str = '<form action="'.$this->mBaseDir.'/formHandlers/AddProductToBasketHandler.php" id="claimFreebieForm" method="post">

					<input type="hidden" name="referPage" id="referPage" value="basket" />
					<input type="hidden" name="addToBasket" id="addToBasket" value="1" />
					<input type="hidden" name="multibuyQuantity" id="multibuyQuantity" value="1" />
					<input type="hidden" name="basketId" id="basketId" value="' . $this->mBasket->GetBasketId() . '" />
					<input type="hidden" name="productId" id="productId" value="'.$this->mFreebieProduct->GetProductId().'" />
					<div id="productOptions">
							<h3>Product Options</h3>
							<div id="optionsContainer">';
		//<span>Please select an option:</span>';
		foreach ( $this->mFreebieProduct->GetAttributes() as $attribute ) {
			$str .= '<select id="skuAttribute' . $attribute->GetProductAttributeId () . '" name="skuAttribute' . $attribute->GetProductAttributeId () . '"><option value="NA">' . $attribute->GetAttributeName () . '</option>';
			$allSkuAttributes = $attribute->GetSkuAttributes ();
			$values = array ();
			foreach ( $allSkuAttributes as $skuAttribute ) {
				if (! in_array ( trim ( $skuAttribute->GetAttributeValue () ), $values )) {
					$str .= '<option value="' . $skuAttribute->GetSkuAttributeId () . '">' . $skuAttribute->GetAttributeValue () . '</option>';
				}
				$values [] = trim ( $skuAttribute->GetAttributeValue () );
			}
			$str .= '</select>';
		}
		$str .= '</div> <!-- Close optionsContainer -->
						</div> <!-- Close productOptions -->
						</form>
						<div id="errorBox"></div>';
		return $str;
	}

	function GetSKU() {
		$attributeIds = explode(',',$_POST['skuAttrId']);
		$skuController = new SkuController;
		$product = new ProductModel($_POST['productIdentifier']);
		// If a SKU exists for the attribute values then return the SKU ID
		if($skuController->RetrieveSKUFromAttributes($attributeIds,$product)) {
			$str .= $skuController->RetrieveSKUFromAttributes($attributeIds,$product);
		} else {
			// There is no SKU with this combination of attribute values
			$str .= 'NO_SKU_EXISTS';
		}
	}

	//! Load a message telling people about a free offer (if it exists)
	function LoadFreeOffer() {
		$this->mPage .= '
		<div id="freeOfferContainer">
			<div id="freeOfferIncentive">
				Deep Blue T-Shirt ideal for training worth <strong>£14.95</strong> - <strong><u>FREE</u></strong> with orders over £'.$this->mRegistry->freeOfferLimit.'
				 - only <strong>&pound;'.$this->mPresentationHelper->Money(($this->mRegistry->freeOfferLimit - $this->mBasket->GetTotal())).'</strong> more!!
			</div>
			<div id="freeOfferSmallprint">
				Make your basket over £'.$this->mRegistry->freeOfferLimit.' (not incl postage) and choose your T-Shirt!
			</div>
			'.$this->mRegistry->freeOfferMessage.'
		</div>
		';
	} // End LoadWarningMessage

	//! Add upgrades to the postage if applicable
	function AddUpgradesToPostage() {
		// Only apply the postage upgrade price if the person HAS upgraded
		if ($this->mBasket->GetDefaultPostageMethod()->GetPostageMethodId () != $this->mDefaultMethod->GetPostageMethodId()) {
			$this->mCurrentPostage += $this->mDefaultMethod->GetUpgradeCost();
		}
	} // End AddUpgradesToPostage

	//! Work out the postage depending on where it is going and how much it weighs
	function WorkOutPostage() {
		if ($this->mBasket->GetTotal() >= 45) {
			if($this->mDefaultCountry->GetShortDescription() == 'United Kingdom Mainland' || $this->mDefaultCountry->GetShortDescription() == 'N.Ireland') {
				$this->mCurrentPostage = 0;
			} else {
				// REPUBLIC OF IRELAND
				if($this->mBasket->GetTotal() > 100) {
					$this->mCurrentPostage = 0;
				} else {
					$this->mCurrentPostage = 9.95;
				}
			}
		} else {
			// Work out ...
			if($this->mDefaultCountry->GetShortDescription() == 'United Kingdom Mainland') {
				// UK
				if($this->mDefaultMethod->GetDescription() == 'Next Working Day' && $this->mBasket->GetWeight() <= 1500) {
					$this->mCurrentPostage = 4.95;
				} else {
					$this->mCurrentPostage = 2.95;
				}
			} elseif($this->mDefaultCountry->GetShortDescription() == 'N.Ireland') {
				// N.Ireland
				$this->mCurrentPostage = 9.95;
			} else {
				// Default
				$this->mCurrentPostage = 9.95;
			}
		}
	} // End WorkOutPostage

	//! Loads the last row of the table with postage, total, and drop down country/methods
	function LoadTotalsSection() {
		$displayTotal = $this->mBasket->GetTotal();
		$exclVatTotal = $this->mBasket->GetExcVatTotal();
		$vatableTotal = $this->mBasket->GetVatableTotal();

		$countryDropDown = new CountryDropDownView ( );
		$postageDropDown = new PostageMethodDropDownView ( );
		$this->mPage .= '<tr>
							<td colspan="2" id="shippingOptions">';
		$this->mPage .= $countryDropDown->LoadDefault ( $this->mDefaultCountry );
		$this->mPage .= $postageDropDown->LoadDefault ( $this->mBasket->GetWeight (), $this->mDefaultCountry, $this->mDefaultMethod );
		$this->mPage .= '</td>
							<td colspan="2" id="totalsTitles">
								Goods: <br />
								Delivery: <br />
								Total:
							</td>
							<td id="totalsValues">
								&pound;' . $this->mPresentationHelper->Money ( $displayTotal) . '<br />
								&pound;' . $this->mPresentationHelper->Money ( $this->mCurrentPostage ) . '<br />
								&pound;' . $this->mPresentationHelper->Money ( $this->mCurrentPostage + $displayTotal ) . '<br />
							</td>
						</tr>';
	} // End LoadTotalsSection

	//! Loads the 'proceed to checkout' button/form
	/*
	 To have 3 buttons use a 3row/2col table and put the buttons in if required
	 */
	function LoadCheckoutButton() {
		// Initialise button vars
		$paypalButton 	= '';
		$googleButton 	= '';
		$echoButton 	= '';
		// Initialise button text
	#	$paypalText = '';
		$paypalText 	= 'Please <b style="text-decoration: underline">ONLY</b> choose the PayPal checkout if you have a <b style="color: red; font-size: 12pt; text-decoration: underline"><a onclick="javascript: window.open(\'' . $this->mBaseDir . '/confirmPayPal.php\',\'Confirmed PayPal Address\',\'toolbar=0,location=0,status=0,menubar=0,width=450,height=450\')">CONFIRMED</a></b> PayPal address!<br />
		If you order with an unconfirmed address your order will be automatically cancelled. <br />';
		$googleText		= 'If you already have a Google Checkout account this may be convenient for you.';
		$echoText		= 'We recommend our own checkout for a secure and simple checkout <br />Benefit from order tracking, order history, speedy re-ordering and free gifts!
		<a onclick="javascript: window.open(\'' . $this->mBaseDir . '/register.php\',\'Register\',\'toolbar=0,location=0,status=0,menubar=0,width=400,height=400\')" style="text-decoration: underline">
		Read More
		</a>';
/*		$echoText		= '
							<strong>PAYMENT BY DEBIT/CREDIT CARD</strong><br />
							We are currently upgrading our servers - payment by debit/credit card has been disabled while we do this.
							You can still use PayPal or Google Checkout below, or call to pay by card.
							';*/

		// Prep Echo Button
		$echoButton = '<form 	action="' . $this->mBaseDir . '/formHandlers/ProceedToCheckoutHandler.php"
								method="post"
								id="proceedToCheckoutForm"
								name="proceedToCheckoutForm">';
		// Close Form
		$echoButton .= '<input type="hidden" name="deliveryCountry" id="deliveryCountry" value="' . $this->mDefaultCountry->GetCountryId () . '" />';
		$echoButton .= '<input type="hidden" name="postageMethodId" id="postageMethodId" value="' . $this->mDefaultMethod->GetPostageMethodId () . '" />';
	#	echo $this->mDefaultMethod->GetPostageMethodId ();
		if($this->mRegistry->hasFreeOffer) {
			$echoButton .= '<input type="image" src="' . $this->mBaseDir . '/images/checkoutButton.gif" id="proceedToCheckoutButtonOffer" name="proceedToCheckoutButtonOffer" />';
		} else {
			$echoButton .= '<input type="image" src="' . $this->mBaseDir . '/images/checkoutButton.gif" id="proceedToCheckoutButton" name="proceedToCheckoutButton" />';
		}
		$echoButton .= '</form><br style="clear: both" />';
#		$echoButton = '';

		// Google checkout button
		if($this->mRegistry->GoogleCheckoutEnabled) {
		$googleButton = '<div id="proceedToGoogleCheckoutButtonContainer">
						<form action="'.$this->mFormHandlersDir.'/ProceedToGoogleCheckoutHandler.php" method="post" id="googleCheckoutForm" name="googleCheckoutForm" onsubmit="setUrchinInputCode(pageTracker);">
						<input type="hidden" name="basket_id" id="basket_id" value="'.$this->mBasket->GetBasketId().'" />
						<input type="hidden" name="postagePrice" id="postagePrice" value="'.$this->mCurrentPostage.'" />
						<input type="hidden" name="postageText" id="postageText" value="'.trim($this->mDefaultMethod->GetDisplayName()).'" />
						<input type="hidden" name="postageId" id="postageId" value="'.trim($this->mDefaultMethod->GetPostageMethodId()).'" />
						<input type="hidden" name="analyticsdata" value="">
						<input 	type="image"
								name="Google Checkout"
								id="GoogleCheckout"
								alt="Fast checkout through Google"
								src="https://checkout.google.com/buttons/checkout.gif?merchant_id=327933992030665&w=180&h=46&style=trans&variant=text&loc=en_GB"
								height="46"
								width="180" />
						</form>

						</div>
						<br style="clear: both" />
						';
		}
		if($this->mRegistry->PaypalCheckoutEnabled) {
			$paypalButton = '
					<div id="proceedToPaypalCheckoutButtonContainer">
						<form action="'.$this->mFormHandlersDir.'/ProceedToPaypalCheckoutHandler.php" method="post" id="paypalCheckoutForm" name="paypalCheckoutForm">
						<input type="hidden" name="basket_id" id="basket_id" value="'.$this->mBasket->GetBasketId().'" />
						<input type="hidden" name="postagePrice" id="postagePrice" value="'.$this->mCurrentPostage.'" />
						<input 	type="image"
								name="PaypalCheckout"
								id="PaypalCheckout"
								alt="Pay with PayPal"
								src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" />
						</form>

					</div><br style="clear: both" />';
		}
		// **** END GENERATING BUTTONS

		// Start Table with options for checkout
	#	$this->mPage .= '<h2>We are currently transferring to a new host and are unable to take web orders during this time - please call us on 01753 572741 to place any orders!</h2>';
		$this->mPage .= '
					<br style="clear: both" />
						<table id="checkoutOptionsTable">
					<!--	<tr>
							<td style="text-align: center; font-weight: bold">We are currently upgrading our server - please call on 01753 572741 to place an order
							by credit/debit card, otherwise you can use either PayPal and Google Checkout below.</td>
						</tr>-->
						<tr>
						        <td>'.$echoText.'</td>
						        <td class="button">'.$echoButton.'</td>
						    </tr>
							<tr>
								<td colspan="2" class="basketDivider"></td>
							</tr>
						 <!--   <tr>
						        <td>'.$googleText.'</td>
						        <td class="button"><br />'.$googleButton.'</td>
						    </tr> -->
						    <tr>
						        <td>'.$paypalText.'</td>
						        <td class="button">'.$paypalButton.'</td>
						    </tr>
						</table>
					<br style="clear: both" /><br />
						';

		// **** END TABLE WITH OPTIONS FOR CHECKOUT

		// Add a free delivery teaser...
		$currentSpend = $this->mPresentationHelper->Money($this->mBasket->GetTotal());

		// Rep. Ireland needs to spend £100 - unlucky!
		if($this->mDefaultCountry->GetShortDescription() == 'Rep. Ireland') {
			$stillToSpend = $this->mPresentationHelper->Money(100 - $currentSpend);
		} else {
			$stillToSpend = $this->mPresentationHelper->Money(45 - $currentSpend);
		}

		// Royal Mail or courier?
		if($this->mBasket->GetWeight() > 2000) {
			if($this->mDefaultCountry->GetShortDescription() == 'United Kingdom Mainland') {
				$nextDayWord = 'Next Day ';
			} else {
				$nextDayWord = '48 HOUR ';
			}
		} else {
			$nextDayWord = 'First Class ';
		}

		// If something is 3-5 day delivery in basket then don't say next day at all
		if($this->mBasket->ContainsNonStockProducts()) {
			$nextDayWord = '';
		}

		if($stillToSpend > 0) {
			$this->mPage .= '
				<div style="border: 2px solid #EC6665; margin-bottom: 5px; font-size: 14pt; padding: 10px; 0px 10px 0px">
					You have spent &pound;'.$currentSpend.' - spend an extra &pound;'.$stillToSpend.' to qualify for '.$nextDayWord.'FREE Delivery!
				</div>';
		} else {
			$this->mPage .= '
				<div style="border: 2px solid #EC6665; margin-bottom: 5px; font-size: 14pt; padding: 10px; 0px 10px 0px">
					You have spent &pound;'.$currentSpend.' - You qualify for '.$nextDayWord.'FREE Delivery!
				</div>';
		}

	} // End LoadCheckoutButton

	//! Close the basket table
	function CloseTable() {
		$this->mPage .= '</table>';
	} // End CloseTable

	//! Open the basket table
	function OpenTable() {
		$this->mPage .= '<br /><table id="basketTable">
							<tr>
								<th id="productColumn">Product</th>
								<th id="qtyColumn">Qty</th>
								<th id="unitPriceColumn">Unit Price</th>
								<th id="totalPriceColumn">Total Price</th>
								<th>Remove</th>
							</tr>';
	} // End OpenTable

	//! Loads the right column, including RightColView
	function LoadRightColumn() {
		$rightColView = new RightColView ( $this->mCatalogue, $this->mSessionHelper );
		$this->mPage .= $rightColView->LoadDefault ();
	}

} // End BasketView

?>