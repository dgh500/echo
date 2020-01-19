<?php
@include_once('../autoload.php');
class BasketContentsView extends AdminView {

	//! Consructor, does some setup
	/*!
	 * @param $catalogue - CatalogueModel - Which catalogue are we in?
	 */
	function __construct($catalogue) {
		// Params
		$this->mCatalogue = $catalogue;

		// Construct
		parent::__construct();

		// Get some member vars set up
		$this->mSessionHelper 		= new SessionHelper();
		$this->mSystemSettings 		= new SystemSettingsModel($this->mCatalogue);
		$this->mPublicLayoutHelper 	= new PublicLayoutHelper();
		$this->mPricingModel 		= $this->mCatalogue->GetPricingModel();
		$this->mBasket 				= $this->mSessionHelper->GetBasket();
	} // End __construct()

	//! Load the products in the basket
	/*************
	 VAT-Free Note
	 If the country has been changed to a VAT-Free one (eg. BFPO) then there are 2 possibilities:
	 	For a Package/Stack - Depends entirely on the $registry->packageVatFreeAllowed setting - if true then the VAT will be removed from the package
		For a SKU			- The VAT is only removed if the product does not have a 0% tax rate (ie. is exempt)
	 *************/
	function LoadDefault() {
		// Do we have packages?
		if(count($this->mBasket->GetPackages())==0) {
			$this->mShowPackage = false;
		} else {
			$this->mShowPackage = true;
		}

		// Load a country if none already set
		if(!isset($_SESSION ['countryId'])) {
			$countryController = new CountryController ( );
			$this->mDefaultCountry = $countryController->GetDefault ();
			$this->mSessionHelper->SetCountry ( $this->mDefaultCountry->GetCountryId () );
		} else {
			$this->mDefaultCountry = new CountryModel ( $_SESSION ['countryId'] );
		}

		// Load a postage method if none already set
		if(!isset($_SESSION['postageMethodId'])) {
			$postageMethodController = new PostageMethodController();
			$this->mDefaultMethod = $this->mBasket->GetDefaultPostageMethod();
			$this->mSessionHelper->SetPostageMethod($this->mDefaultMethod->GetPostageMethodId());
		} else {
			$this->mDefaultMethod = new PostageMethodModel($_SESSION['postageMethodId']);
		}

		// Do we have anything in the basket? If not then display a message saying so!
		if(count($this->mBasket->GetSkus()) == 0 && count($this->mBasket->GetPackages())==0) {
			$this->mPage .= 'Your basket is empty.';
		} else {
			$this->OpenTable();
			$skusPrepArr = array();
			$upgradesArr = array();
			$prevUpgrades = array();
			$this->mCurrentPostage = 0;
			$this->mRunningTotal = 0;

			// Make a 'prepped' array so its easier to loop over
			foreach ( $this->mBasket->GetSkus () as $sku ) {
				if (isset ( $skusPrepArr [$sku->GetSkuId ()] )) {
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
			if($this->mShowPackage) {
				$allPackages = $this->mBasket->GetPackages();
				$packageSkuBucket = $this->mBasket->GetSkus(false,false,true,true); // Include both package products and upgrades, and not normal SKUs
				#echo 'x<pre>'; var_dump($packageSkuBucket); echo '</pre>x';
				foreach($packagesPrepArr as $key=>$preppedPack) {
					$package = new PackageModel($key);
					$packageContents = $package->GetContents();
					$packageContentsDisplay = '';

					// Load the products in each package
					foreach($packageContents as $product) {

						if($package->GetProductQty($product)>1) {
							// Add the quantity if greater than 1 (so it will display as '3xWhatever' on the page)
							$qty = $package->GetProductQty($product).' x ';
						} else {
							$qty = '';
						}
						$attrString = ' ';
						foreach($product->GetSkus() as $sku) {
							if(in_array($sku,$packageSkuBucket)) {
								$key = array_search($sku,$packageSkuBucket);
								$attrString .= $sku->GetSkuAttributesList();
								#echo 'Found '.$sku->GetSkuAttributesList().' <br />';
								array_splice($packageSkuBucket,$key,1);
								#echo '<pre>'; var_dump($packageSkuBucket); echo '</pre>';
							}
						}
						$packageContentsDisplay .= ' - '.$qty.' '.$this->mPresentationHelper->ChopDown($product->GetDisplayName(),75).$attrString.'<br />';
					}

					// Add the information to the page
					$this->mPage .= '<tr>	<td id="productColumn"><strong>' . $package->GetDisplayName () . '</strong><br />' . $packageContentsDisplay . '</td>
											<td id="qtyColumn">' . $packagesPrepArr [$package->GetPackageId ()] ['qty'] . '</td>
											<td id="unitPriceColumn">

												<form action="' . $this->mBaseDir . '/formHandlers/AdminPackageUnitPriceChangeHandler.php" method="post">
													<input type="hidden" name="packageId" id="packageId" value="'.$package->GetPackageId().'" />
														&pound;
				<input type="text" value="'.$this->mPresentationHelper->Money($packagesPrepArr[$package->GetPackageId()]['unitPrice']).'" name="packageUnitPrice" id="packageUnitPrice" onChange="this.form.submit()" />
				<input type="hidden" value="' . $this->mPresentationHelper->Money($packagesPrepArr[$package->GetPackageId()]['unitPrice']).'" name="prevPackageUnitPrice" id="prevPackageUnitPrice" />
				<input type="hidden" value="'.$packagesPrepArr[$package->GetPackageId()]['qty'].'" name="unitPricePackageQty" id="unitPricePackageQty" />
												</form>

											</td>
											<td id="totalPriceColumn">&pound;' . $this->mPresentationHelper->Money ( $packagesPrepArr [$package->GetPackageId ()] ['totalPrice'] ) . '</td>
											<td id="removeColumn"><form action="' . $this->mBaseDir . '/formHandlers/AdminRemovePackageHandler.php" method="post">
											<input type="hidden" name="packageToRemove" id="packageToRemove" value="' . $package->GetPackageId () . '" />
											<input type="image" src="' . $this->mBaseDir . '/images/shoppingBagRemoveItemAdmin.gif" />
										</form></td>
									</tr>';
				}

				// Show Upgrades
				foreach($upgradesArr as $key=>$preppedUpgrade) {
					$sku = new SkuModel($key);
					$skuAttributes = $sku->GetSkuAttributes();
					if(count($skuAttributes) != 0) {
						$options = '(';
						foreach($skuAttributes as $skuAttribute) {
							$options .= $skuAttribute->GetAttributeValue();
						}
						$options .= ')';
					} else {
						$options = '';
					}
					$href = $this->mPublicLayoutHelper->LoadLinkHref($sku->GetParentProduct());
					$this->mPage .= '<tr>	<td id="productColumn"><strong>Upgrade: </strong><a href="' . $href . '">' . $sku->GetParentProduct ()->GetDisplayName () . '</a> ' . $options . '</td>
											<td id="qtyColumn">' . $upgradesArr [$sku->GetSkuId ()] ['qty'] . '</td>
											<td id="unitPriceColumn">
											<form action="' . $this->mBaseDir . '/formHandlers/AdminPackageUpgradeUnitPriceChangeHandler.php" method="post">
													<input type="hidden" name="upgradeSkuId" id="upgradeSkuId" value="'.$sku->GetSkuId().'" />
														&pound;
				<input type="text" value="'.$this->mPresentationHelper->Money($upgradesArr[$sku->GetSkuId()]['unitPrice']).'" name="upgradeSkuUnitPrice" id="upgradeSkuUnitPrice" onChange="this.form.submit()" />
				<input type="hidden" value="' . $this->mPresentationHelper->Money($upgradesArr[$sku->GetSkuId()]['unitPrice']).'" name="prevUpgradeSkuUnitPrice" id="prevUpgradeSkuUnitPrice" />
				<input type="hidden" value="'.$upgradesArr[$sku->GetSkuId()]['qty'].'" name="unitPriceUpgradeSkuQty" id="unitPriceUpgradeSkuQty" />
												</form>
											</td>
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

				// A misc product has no category => it can't have a link
				if(isset($parentCategories[0])) {
					$parentCategory = $parentCategories [0];
					$categoryPart = $this->mValidationHelper->MakeLinkSafe ( $parentCategory->GetDisplayName () );
					if ($parentCategory->GetParentCategory ()) {
						$categoryPart .= '/' . $this->mValidationHelper->MakeLinkSafe ( $parentCategory->GetParentCategory ()->GetDisplayName () ) . '/';
					}
					$urlToProduct = $categoryPart . 'product/' . $this->mValidationHelper->MakeLinkSafe ( $parentProduct->GetDisplayName () ) . '/' . $parentProduct->GetProductId ();
				} else {
					// Misc Product
					$urlToProduct = '#';
				}

				$this->mPage .= '<input type="hidden" name="skuId" id="skuId" value="' . $sku->GetSkuId () . '" />
								 <input type="hidden" name="basketId" id="basketId" value="' . $this->mBasket->GetBasketId () . '" />';
				$this->mPage .= '
								<tr>
										<td id="productColumn"><a href="' . $this->mBaseDir . '/department/' . $urlToProduct . '">' . $sku->GetParentProductName () . '</a>' . $skuAttributesList . '</td>
										<td id="qtyColumn">';
				$this->mPage .= '		<form action="' . $this->mBaseDir . '/formHandlers/AdminQuantityChangeHandler.php" method="post">
											<input type="hidden" name="skuId" id="skuId" value="' . $sku->GetSkuId () . '" />
												<input type="text" value="' . $preppedRow ['qty'] . '" name="skuQty" id="skuQty" onChange="this.form.submit()" />
											</form>';
				$this->mPage .= '		</td>
										<td id="unitPriceColumn">
											<form action="' . $this->mBaseDir . '/formHandlers/AdminUnitPriceChangeHandler.php" method="post">
												<input type="hidden" name="skuId" id="skuId" value="' . $sku->GetSkuId () . '" />
												&pound;
			<input type="text" value="' . $this->mPresentationHelper->Money ( $preppedRow ['unitPrice'] ) . '" name="skuUnitPrice" id="skuUnitPrice" onChange="this.form.submit()" />
			<input type="hidden" value="' . $this->mPresentationHelper->Money ( $preppedRow ['unitPrice'] ) . '" name="prevSkuUnitPrice" id="prevSkuUnitPrice" />
			<input type="hidden" value="'.$preppedRow['qty'].'" name="unitPriceSkuQty" id="unitPriceSkuQty" />
											</form>
										</td>
										<td id="totalPriceColumn">&pound;' . $this->mPresentationHelper->Money ( $preppedRow ['totalPrice'] ) . '</td>
										<td id="removeColumn">';
				$this->mPage .= '<form action="' . $this->mBaseDir . '/formHandlers/AdminRemoveSkuHandler.php" method="post">
										<input type="hidden" name="skuToRemove" id="skuToRemove" value="' . $sku->GetSkuId () . '" />
										<input type="image" src="' . $this->mBaseDir . '/images/shoppingBagRemoveItemAdmin.gif" />
									</form></td>';
				$this->mPage .= '</tr>';
			}

			// Sort out postage
			if($this->mBasket->GetPostageUpgrade() == 0 && !isset($_SESSION['postageChanged'])) {
				$this->WorkOutPostage();
			} else {
				$this->mCurrentPostage = $this->mBasket->GetPostageUpgrade();
			}
			$this->AddUpgradesToPostage();
			$this->mSessionHelper->SetPostage($this->mCurrentPostage);

			// Load totals
			$this->LoadTotalsSection ();

			// Close the table
			$this->CloseTable();
		}
		return $this->mPage;
	} // End LoadBasketContents

	//! Add upgrades to the postage if applicable
	function AddUpgradesToPostage() {
		// Only apply the postage upgrade price if the person HAS upgradfed
		if ($this->mBasket->GetDefaultPostageMethod ()->GetPostageMethodId () != $this->mDefaultMethod->GetPostageMethodId ()) {
			$this->mCurrentPostage += $this->mDefaultMethod->GetUpgradeCost ();
		}
	} // End AddUpgradesToPostage

	//! Work out the postage depending on where it is going and how much it weighs
	function WorkOutPostage() {
		if ($this->mBasket->GetTotal () > 45) {
			$this->mCurrentPostage = 0;
		} else {
			$this->mCurrentPostage = 2.95;
		}
	} // End WorkOutPostage

	//! Loads the last row of the table with postage, total, and drop down country/methods
	function LoadTotalsSection() {
		if ($this->mDefaultCountry->IsVatFree() && $this->mRegistry->vatFreeAllowed) {
			$displayTotal = $this->mMoneyHelper->RemoveVAT ( $this->mBasket->GetTotal () );
		} else {
			$displayTotal = $this->mBasket->GetTotal ();
		}

		$countryDropDown = new CountryDropDownView;
		$postageDropDown = new PostageMethodDropDownView;
		$this->mPage .= '<tr>
							<td colspan="2" id="shippingOptions">';
		$this->mPage .= $countryDropDown->LoadDefault ( $this->mDefaultCountry,'Ship To: ',true,true );
		$this->mPage .= $postageDropDown->LoadDefault ( $this->mBasket->GetWeight (), $this->mDefaultCountry, $this->mDefaultMethod, true );
		$this->mPage .= '	</td>
							<td colspan="2" id="totalsTitles">
								Sub-Total: <br />
								Postage: <br />
								Total:
							</td>
							<td id="totalsValues">
								&pound;' . $this->mPresentationHelper->Money ( $displayTotal ) . '<br />
							<form id="addOrderPostageChangeForm" name="addOrderPostageChangeForm" method="post" action="' . $this->mSecureBaseDir . '/formHandlers/AdminPostageChangeHandler.php">
									<input type="hidden" name="currentPostage" id="currentPostage" value="'.$this->mCurrentPostage.'" />
									&pound;<input type="text" name="editableCurrentPostage" id="editableCurrentPostage"
													value="'.$this->mPresentationHelper->Money($this->mCurrentPostage).'" onChange="this.form.submit()" />
							</form>
								&pound;' . $this->mPresentationHelper->Money ( $this->mCurrentPostage + $displayTotal ) . '<br />
							</td>
						</tr>';
	} // End LoadTotalsSection

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


}
if(isset($_GET['LOAD'])) {
	$catalogue = new CatalogueModel($_GET['catalogueIdentifier']);
	$page = new BasketContentsView($catalogue);
	echo $page->LoadDefault();
}
?>