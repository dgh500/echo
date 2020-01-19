<?php
require_once('../autoload.php');

//! View for the admin(edit/delete) view of a product
class AdminProductView extends AdminView {

	//! Obj:ProductModel : The product that is being edited by the administrator
	var $mProduct;

	//! Constructor
	function __construct() {
		$jsIncludes = array('jqueryUi.js','jquery.alerts.js','AdminProductView.js','validateProductForm.js','InputListView.js','Tabs.js');
		$cssIncludes = array('jqueryUI.css','admin.css.php','AdminProductView.css.php','jquery.alerts.css.php');
		parent::__construct(true,$cssIncludes,$jsIncludes);
	}

	//! Standard load function - call this on first load. Initialises and loads everything
	function LoadDefault($productId) {
		$this->InitialiseProduct($productId);
		$this->InitialiseDisplay();
		$this->LoadTabs();
		$this->InitialiseContentDisplay();
		$this->LoadDescriptionDisplay();
		$this->LoadPricingDisplay();
		$this->LoadPromotionsDisplay();
		$this->LoadOptionsDisplay();
		$this->LoadUpgradesDisplay();
		$this->LoadCrossSellDisplay();
		$this->LoadImagesDisplay();
		$this->LoadCategoriesDisplay();
		$this->CloseContentDisplay();
		$this->CloseDisplay();
		return $this->mPage;
	}

	//! Initialises the product to be edited
	function InitialiseProduct($productId) {
		$this->mProduct = new ProductModel($productId);
	}

	// Initialise the display - MUST be matched by $this->CloseDisplay()
	function InitialiseDisplay() {
		$this->mPage .= '<div id="adminProductViewContainer">';
	}

	// Closes the display
	function CloseDisplay() {
		$this->mPage .= '</div>';
	}
	// Loads the tab navigation - description, pricing etc.
	function LoadTabs() {
		$this->mPage .= <<<EOT
		<div id="adminProductViewTabContainer">
			<ul>
				<li id="adminProductViewTabContainer-description"><a href="#"  id="descriptionLink">Description</a></li>
				<li id="adminProductViewTabContainer-pricing"><a href="#" id="pricingLink">Pricing</a></li>
				<li id="adminProductViewTabContainer-promotions"><a href="#" id="promotionsLink">Promotions</a></li>
				<li id="adminProductViewTabContainer-optionsz"><a href="#" id="optionszLink">Options</a></li>
				<li id="adminProductViewTabContainer-upgrades"><a href="#" id="upgradesLink">Upgrades</a></li>
				<li id="adminProductViewTabContainer-crossSell"><a href="#" id="crossSellLink">Cross&nbsp;Sell</a></li>
				<li id="adminProductViewTabContainer-images"><a href="#" id="imagesLink">Images</a></li>
				<li id="adminProductViewTabContainer-categories"><a href="#" id="categoriesLink">Categories</a></li>
			</ul>
		</div>
EOT;
	}

	// Initialises the content section of the page, MUST be matched by $this->CloseContentDisplay()
	function InitialiseContentDisplay() {
		$registry = Registry::GetInstance();
		$this->mPage .= <<<EOT
			<div id="adminProductViewContentContainer">
			<form id="adminProductForm" name="adminProductForm" method="post" action="{$registry->formHandlersDir}/AdminProductViewHandler.php" onsubmit="return validateForm(this)">
EOT;
	}

	// Closes the content display
	function CloseContentDisplay() {
		$this->mPage .= '</div><div id="adminProductFormButtons">
							<input type="submit" value="Save" name="saveProduct" id="saveProduct" />
							<input type="button" value="Delete" name="deleteProduct" id="deleteProduct" />
						</div><br /><br />
						<div id="errorBox"></div>
					</form>';
	}

	//! Loads the description section
	function LoadDescriptionDisplay() {
		$registry = Registry::getInstance();
		$adminPath = $registry->adminDir;
		$skus = $this->mProduct->GetSkus();
		$buildQtyField = false; $qtyField = '';
		if(1 == count($skus)) {
			// No Attributes => One sage code
			$singleSku = $skus [0]->GetSageCode();
			// One quantity as well..
			if(count($this->mProduct->GetAttributes()) == 0){
				$buildQtyField = true;
			}
		} else {
			// Multiple Attributes
			$singleSku = 'Multiple Sage Codes';
		}

		// If we need to build a quantity field then do so.
		if($buildQtyField) {
			$qtySku = $skus [0];
			$qtyField = <<<EOT
			<label for="skuQuantity">			Quantity:			</label>
				<input type="text" name="skuQuantity" id="skuQuantity" value="{$qtySku->GetQty()}" /><br />
EOT;
		}

		// See http://www.fckeditor.net for full details
		$oFCKeditor = new FCKeditor('longDescription');
		$oFCKeditor->BasePath = $adminPath . '/fckeditor/';
		$oFCKeditor->ToolbarSet = 'DeepBlue08';
		$oFCKeditor->Value = $this->mProduct->GetLongDescription();
		$oFCKeditor->Height = 350;

		$oFCKeditor2 = new FCKeditor('echoDescription');
		$oFCKeditor2->BasePath = $adminPath . '/fckeditor/';
		$oFCKeditor2->ToolbarSet = 'DeepBlue08';
		$oFCKeditor2->Value = $this->mProduct->GetEchoDescription();
		$oFCKeditor2->Height = 250;

		$this->mPage .= <<<EOT
		<div id="descriptionContentArea">
			<input type="hidden" name="productId" id="productId" value="{$this->mProduct->GetProductId()}" />
			<label for="displayName">		Display Name:		</label>
				<input type="text" name="displayName" id="displayName" value="{$this->mProduct->GetDisplayName()}" /><br />
			<label for="sageCode">			Sage Code:			</label>
				<input type="text" name="sageCode" id="sageCode" value="{$singleSku}" /><br />
			{$qtyField}
			<label for="description">		Description:		</label>
				<input type="text" name="description" id="description" value="{$this->mProduct->GetDescription()}" /><br />
			<label for="longDescription">	Long Description:	</label><br />
EOT;
		$this->mPage .= $oFCKeditor->Create();
		$this->mPage .= <<<EOT
			<label for="longDescription">	ECHO Description:	</label><br />
EOT;
		$this->mPage .= $oFCKeditor2->Create();
		$this->mPage .= <<<EOT
		</div>
EOT;
	}

	//! Loads the pricing section
	function LoadPricingDisplay() {
		$manufacturer = $this->mProduct->GetManufacturer();
		$manufacturerController = new ManufacturerController();
		$taxCodeController = new TaxCodeController();
		$dispatchDateController = new DispatchDateController();
		$catalogue = $this->mProduct->GetCatalogue();
		if(NULL === $manufacturer) {
			$manufacturerName = 'NO MANUFACTURER';
		} else {
			$manufacturerName = $manufacturer->GetDisplayName();
		}
		if($this->mProduct->GetInStock()) {
			$inStock = 'checked';
		} else {
			$inStock = '';
		}
		if($this->mProduct->GetForSale()) {
			$forSale = 'checked';
		} else {
			$forSale = '';
		}
		$this->mPage .= <<<EOT
		<div id="pricingContentArea">
			<label for="actualPrice">		Actual Price:		</label>
				<input type="text" name="actualPrice" id="actualPrice" value="{$this->mProduct->GetActualPrice()}" />
				&nbsp;[ <a href="#" id="addVat" name"addVat">Add VAT</a> ]
				<br />
			<label for="upgradePrice">		Upgrade Price:		</label>
				<input type="text" name="upgradePrice" id="upgradePrice" value="{$this->mProduct->GetUpgradePrice()}" /><br />
			<label for="wasPrice">			Was Price:			</label>
				<input type="text" name="wasPrice" id="wasPrice" value="{$this->mProduct->GetWasPrice()}" /><br />
			<label for="taxCode">			Tax Code:			</label>
				<select name="taxCode" id="taxCode">
EOT;
		$allTaxCodes = $taxCodeController->GetAllTaxCodes();
		foreach($allTaxCodes as $taxCode) {
			if($taxCode->GetTaxCodeId() == $this->mProduct->GetTaxCode()->GetTaxCodeId()) {
				$this->mPage .= '<option value="' . $taxCode->GetTaxCodeId() . '" selected>' . $taxCode->GetDisplayName() . '</option>';
			} else {
				$this->mPage .= '<option value="' . $taxCode->GetTaxCodeId() . '">' . $taxCode->GetDisplayName() . '</option>';
			}
		}
		$this->mPage .= <<<EOT
				</select><br />
			<label for="postage">			Postage:			</label>
				<input type="text" name="postage" id="postage" value="{$this->mProduct->GetPostage()}" /><br />
			<label for="manufacturer">		Manufacturer:		</label>
				<select name="manufacturer" id="manufacturer">
EOT;
		foreach($manufacturerController->GetAllManufacturersFor($catalogue) as $tempManufacturer) {
			if(NULL === $manufacturer) {
				$this->mPage .= '<option value="' . $tempManufacturer->GetManufacturerId() . '">' . $tempManufacturer->GetDisplayName() . '</option>';
			} else {
				if($manufacturer->GetManufacturerId() == $tempManufacturer->GetManufacturerId()) {
					$this->mPage .= '<option value="' . $tempManufacturer->GetManufacturerId() . '" selected>' . $tempManufacturer->GetDisplayName() . '</option>';
				} else {
					$this->mPage .= '<option value="' . $tempManufacturer->GetManufacturerId() . '">' . $tempManufacturer->GetDisplayName() . '</option>';
				}
			}
		}

		$this->mPage .= <<<EOT
				</select><br />
			<label for="inStock">			In Stock:			</label>
				<input type="checkbox" name="inStock" id="inStock" {$inStock} /><br />
			<label for="forSale">			For Sale:			</label>
				<input type="checkbox" name="forSale" id="forSale" {$forSale} /><br />
			<label for="productUrl">			Product URL:			</label>
				{$this->mPublicLayoutHelper->LoadLinkHref($this->mProduct)}<br /><br />
			<label for="weight">			Weight:				</label>
				<input type="text" name="weight" id="weight" value="{$this->mProduct->GetWeight()}" />&nbsp;grams<br />
			<label></label>Set weight for...<br />
			<label></label><input type="checkbox" name="WEIGHTINTHIS" id="WEIGHTINTHIS" style="width: auto;" checked />&nbsp;this product only<br />
EOT;
		$allCategories = $this->mProduct->GetCategories();
		$alreadyBeen = array();
		foreach($allCategories as $category) {
			$parentCategory = $category->GetParentCategory();
			if(NULL !== $parentCategory) {
				if(! in_array($parentCategory->GetCategoryId(), $alreadyBeen)) {
					// The product is in a sub category
					$this->mPage .= '<label></label>
						<input type="checkbox" style="width: auto;" name="WEIGHTIN' . $parentCategory->GetCategoryId() . '" id="WEIGHTIN' . $parentCategory->GetCategoryId() . '"  />
						&nbsp;all products in ' . $parentCategory->GetDisplayName() . '<br />';
				}
				$this->mPage .= '<label></label>
					<input type="checkbox" style="width: auto;" name="WEIGHTIN' . $category->GetCategoryId() . '" id="WEIGHTIN' . $category->GetCategoryId() . '" />
					&nbsp;all products in ' . $parentCategory->GetDisplayName() . ' > ' . $category->GetDisplayName() . '<br />';
				$alreadyBeen [] = $parentCategory->GetCategoryId();
			} else {
				// The product is in a top level category
				$this->mPage .= '<label></label>
						<input type="checkbox" style="width: auto;" name="WEIGHTIN' . $category->GetCategoryId() . '" id="WEIGHTIN' . $category->GetCategoryId() . '"  />
						&nbsp;all products in ' . $category->GetDisplayName() . '<br />';
			}
		}
		$this->mPage .= <<<EOT
		</div>
EOT;
	}

	//! Loads the promotions section
	function LoadPromotionsDisplay() {
		$this->mCatalogue = $this->mProduct->GetCatalogue();
		$this->mSystemSettings = new SystemSettingsModel($this->mCatalogue);
		if($this->mProduct->GetOnSale()) {
			$onSale = 'checked';
		} else {
			$onSale = '';
		}
		if($this->mProduct->GetOfferOfWeek()) {
			$offerOfWeek = 'checked';
		} else {
			$offerOfWeek = '';
		}
		if($this->mProduct->GetOnClearance()) {
			$onClearance = 'checked';
		} else {
			$onClearance = '';
		}
		if($this->mProduct->GetFeatured()) {
			$featured = 'checked';
		} else {
			$featured = '';
		}
		if($this->mProduct->GetHidden()) {
			$hidden = 'checked';
		} else {
			$hidden = '';
		}
		if($this->mProduct->GetMultibuy() && $this->mSystemSettings->GetShowMultibuy()) {
			$multibuy = 'checked';
			$multibuyShowStyle = 'style="display: block;"';
		} else {
			$multibuy = '';
			$multibuyShowStyle = 'style="display: none;"';
		}

		$this->mPage .= <<<EOT
		<div id="promotionsContentArea">
			<div id="promotionsLeftContentArea">
				<label for="onSale">			On Sale:			</label>
					<input type="checkbox" name="onSale" id="onSale" {$onSale} /><br />
				<label for="offerOfWeek">		Offer of the week:	</label>
					<input type="checkbox" name="offerOfWeek" id="offerOfWeek" {$offerOfWeek} /><br />
				<label for="onClearance">		Non-Stock Item?:		</label>
					<input type="checkbox" name="onClearance" id="onClearance" {$onClearance} /><br />
				<label for="featured">		Featured:		</label>
					<input type="checkbox" name="featured" id="featured" {$featured} /><br />
				<label for="hidden">		Hidden:		</label>
					<input type="checkbox" name="hidden" id="hidden" {$hidden} /><br />
				<label for="multibuy"> 			Multibuy Enabled:		</label>
					<input type="checkbox" name="multibuy" id="multibuy" {$multibuy} /><br />
				<table {$multibuyShowStyle}>
					<tbody id="multibuyTable">
					<tr>
						<th>Quantity</th>
						<th>Unit Price</th>
						<th>Delete</th>
					</tr>
EOT;
		$allMultibuy = $this->mProduct->GetMultibuyDetails();
		$i = 0;
		foreach($allMultibuy as $multibuy) {
			$this->mPage .= <<<EOT
				<tr>
					<td><input type="hidden" name="existingQuantityInput{$i}" id="existingQuantityInput{$i}" value="{$multibuy['quantity']}" />{$multibuy['quantity']}</td>
					<td><input type="text" name="existingUnitPriceInput{$i}" id="existingUnitPriceInput{$i}" value="{$this->mPresentationHelper->Money($multibuy['unitPrice'])}" /></td>
					<td><a href="#" onClick="toggleDeleteMultibuyRow({$i},'existing')" id="existingMultibuyDeleteButton" name="existingMultibuyDeleteButton">Delete</a></td>
				</tr>
EOT;
			$i ++;
		}
		$this->mPage .= <<<EOT
					<tr id="multibuyTableLastRow">
						<td colspan="4" id="multibuyAddRow"><a href="#" onclick="addMultibuyRow()">Add</td>
					</tr>
					</tbody>
				</table>
			</div> <!-- End promotionsLeftContentArea -->
			<div id="promotionsRightContentArea">
EOT;

		// Add product attribute section
		$this->mPage .= <<<EOT
		<strong><a href="#" id="addReviewLink">Add a Review</a></strong>
		<div id="addReviewFormContainer">
			<iframe src="{$this->mRegistry->formHandlersDir}/AdminAddReviewHandler.php?productId={$this->mProduct->GetProductId()}"
				id="productAddReviewIframe"
				name="productAddReviewIframe"
				scrolling="no"
				frameborder="0"
				/></iframe>
		</div>
EOT;

			// Pending Reviews
			$this->mPage .= '<br /><strong>Reviews - Pending</strong>';
			$pendingReviews = $this->mProduct->GetPendingReviews();
			$this->mPage .= '<ul id="pendingReviewsList">';
			foreach($pendingReviews as $review) {
				$this->mPage .= '<li id="'.$review->GetReviewId().'">
									<a id="'.$review->GetReviewId().'" name="'.$review->GetReviewId().'">'.$review->GetName().' - '.$review->GetDateAdded(true).'</a>

								</li>';
			}
			$this->mPage .= '</ul>';
			$this->mPage .= '<hr />';
			/// Approved Reviews
			$this->mPage .= '<strong>Approved Reviews</strong>';
			$approvedReviews = $this->mProduct->GetApprovedReviews();
			foreach($approvedReviews as $review) {
				$this->mPage .= '- '.$review->GetName().' - '.$review->GetDateAdded(true).'<br />';
			}

			$this->mPage .= '
				<!-- The reviews approval form -->
				<div id="pendingReviewDialog" class="dialog" style="display: none">
					<div id="pendingReviewText"></div>
					<label for="reviewConfirmName" style="float: left; width: 100px;">Name: </label>
						<input type="text" name="reviewConfirmName" id="reviewConfirmName" style="float: left" /><br />
					<label for="reviewConfirmRating"  style="float: left; width: 100px;">Rating: </label>
						<input type="text" name="reviewConfirmRating" id="reviewConfirmRating"  style="float: left" /><br />
					<label for="reviewConfirmName"  style="float: left; width: 100px;">IP: </label>
						<input type="text" name="reviewConfirmIP" id="reviewConfirmIP"  style="float: left" /><br />
					<label for="reviewConfirmName"  style="float: left; width: 100px;">Text: </label>
						<textarea name="reviewConfirmText" id="reviewConfirmText" style="float: left" cols="40" rows="5"></textarea><br />
				</div>';
/*
			SHOP BY GOAL DISPLAY
			// All the possible tags
			$allTags = $this->mProduct->GetCatalogue()->GetTags();
			// Create an array to store the IDs - so can compare IDs, not objects
			$productTagIds = array();
			// The tags related to this product
			$productTags = $this->mProduct->GetTags();
			foreach($productTags as $tag) {
				$productTagIds[] = $tag->GetTagId();
			}
			// Display the checkboxes
			foreach($allTags as $tag) {
				if(in_array($tag->GetTagId(),$productTagIds)) {
					$checked = 'checked="checked"';
				} else {
					$checked = '';
				}
				$this->mPage .= '<input type="checkbox" id="shopByTag'.$tag->GetTagId().'" name="shopByTag'.$tag->GetTagId().'" '.$checked.'> <label for="shopByTag'.$tag->GetTagId().'">'.$tag->GetDisplayName().'</label><br />';
			}*/
		$this->mPage .= <<<EOT
			</div>
		</div> <!-- End promotionsContentArea -->
EOT;
	}

	//! Loads the options section
	function LoadOptionsDisplay() {
		$registry = Registry::getInstance();
		$this->mPage .= '<div id="optionsContentArea">';
		$productAttributes = $this->mProduct->GetAttributes();
		$this->mPage .= '<strong>Product Attributes</strong><br />';
		// Start Looping over product attributes
		foreach($productAttributes as $productAttribute) {
			$attributeName = trim($productAttribute->GetAttributeName());
			$this->mPage .= <<<EOT
				<input 	type="text"
						name="PRODUCTATTRIBUTEEDIT{$productAttribute->GetProductAttributeId()}"
						id="PRODUCTATTRIBUTEEDIT{$productAttribute->GetProductAttributeId()}"
						value="{$attributeName}"
						disabled />
						&nbsp;
				<a href="#" onClick="toggleTextInputEditable('{$productAttribute->GetProductAttributeId()}','PRODUCTATTRIBUTEEDIT','adminProductForm',true)" id="PRODUCTATTRIBUTEEDIT{$productAttribute->GetProductAttributeId()}Edit">Edit</a>
				|
				<a href="#" onClick="toggleDeleteField('{$productAttribute->GetProductAttributeId()}','PRODUCTATTRIBUTEEDIT','adminProductForm',true);" id="PRODUCTATTRIBUTEEDIT{$productAttribute->GetProductAttributeId()}Delete">Delete</a><br />
EOT;
		} // End Looping over product attributes


		// Add product attribute section
		$this->mPage .= <<<EOT
			<iframe src="{$registry->formHandlersDir}/ProductAttributeHandler.php?productId={$this->mProduct->GetProductId()}"
				id="productAttributeIframe"
				name="productAttributeIframe"
				scrolling="no"
				frameborder="0"
				/></iframe>


EOT;

		// Start SKUS table section
		$skus = $this->mProduct->GetSkus();
		// Only do something if there are attributes...
		if(0 != count($productAttributes)) {
			// This is used to have the bottom row span the whole table. Seeded to 4 for the 4 mandatory columns
			$columns = 4;
			$this->mPage .= '<table><tbody id="attributesTable">';

			// ****** Start header row ******
			$this->mPage .= '<tr>';
			// Start looping over product attributes
			foreach($productAttributes as $productAttribute) {
				// This array is used to make sure that when looping over the SKUs they are displayed in the correct order
				$attributeOrderArray [] = $productAttribute->GetProductAttributeId();
				$columns ++;
				$productAttributeIdArray [] = $productAttribute->GetProductAttributeId();
				$this->mPage .= <<<EOT
				<th>
					<span id="tableHeading{$productAttribute->GetProductAttributeId()}">{$productAttribute->GetAttributeName()}</span>
					<a href="#" onClick="copyDown('{$productAttribute->GetProductAttributeId()}')"><img src="{$registry->adminDir}/images/down.gif" /></a>
				</th>
EOT;
			} // End looping over product attributes


			// Sage and Actual price table headings
			$this->mPage .= <<<EOT
		<th>
			Actual Price<a href="#" onClick="copyDownPrice()"><img src="{$registry->adminDir}/images/down.gif" /></a>
		</th>
		<th>
			Sage Code<a href="#" onClick="copyDownSageCode()"><img src="{$registry->adminDir}/images/down.gif" /></a>
		</th>
		<th>
			Qty<a href="#" onClick="copyDownQty()"><img src="{$registry->adminDir}/images/down.gif" /></a>
		</th>
		<th>
			Delete
		</th>
		</tr>
EOT;
			// ****** End header row ******


			// This is used by the javascript linked to the page
			$this->mPage .= '<input type="hidden" name="productAttributeIds" id="productAttributeIds" value="' . implode(",", $productAttributeIdArray) . '" /> ';

			// Loop over SKUs
			// ****** Start SKU rows ******
			foreach($skus as $sku) {
				$this->mPage .= '<tr>';
				$skuAttributes = $sku->GetSkuAttributes();
				// Loop over the ordering array - this introduces a nasty triple nested loop, but because almost all products will have 3 or less attributes this shouldnt be an issue
				foreach($attributeOrderArray as $key => $index) {
					// Loop over SKU attributes
					foreach($skuAttributes as $skuAttribute) {
						// Only show the attribute if it corresponds properly to the product attribute in the column
						if($skuAttribute->GetProductAttributeId() == $index) {
							$this->mPage .= <<<EOT
						<td>
							<input 	type="text"
									name="SKU{$sku->GetSkuId()}PRODUCTATTRIBUTE{$index}"
									id="SKU{$sku->GetSkuId()}PRODUCTATTRIBUTE{$index}"
									value="{$skuAttribute->GetAttributeValue()}" />
						</td>
EOT;
						} // End if
					} // End foreach($skuAttributes...
				} // End foreach($attributeOrderArray...


				// Sage code and actual price per-SKU section
				$this->mPage .= <<<EOT
				<td>
					<input 	type="text"
							name="SKU{$sku->GetSkuId()}PRICE"
							id="SKU{$sku->GetSkuId()}PRICE"
							value="{$sku->GetSkuPrice()}" />
				</td>
				<td>
					<input type="text" name="SKU{$sku->GetSkuId()}SAGECODE" id="SKU{$sku->GetSkuId()}SAGECODE" value="{$sku->GetSageCode()}" />
				</td>
				<td>
					<input type="text" name="SKU{$sku->GetSkuId()}QTY" id="SKU{$sku->GetSkuId()}QTY" value="{$sku->GetQty()}" style="width: 30px" maxlength="2" />
				</td>
				<td>
					<a href="#" id="SKUDELETE{$sku->GetSkuId()}" name="SKUDELETE{$sku->GetSkuId()}" onClick="toggleDeleteSkuRow('{$sku->GetSkuId()}')">Delete</a>
				</td>
				</tr>
EOT;
			} // End foreach($skus...
			// ****** End SKU rows ******

			$columnsMinusOne = $columns - 1;

			$this->mPage .= <<<EOT
			<tr id="lastRow">
				<td colspan="{$columns}">
					<a href="#" onClick="addSkuRow({$columnsMinusOne});">Add</a>
				</td>
			</tr>
			</tbody></table>
EOT;
		} // End 'if any attributes' condition
		$this->mPage .= '</div>';
	} // End function


	function LoadUpgradesDisplay() {
		$upgradeView = new UpgradeView();
		$presentationHelper = new PresentationHelper();
		$this->mPage .= '<div id="upgradesContentArea">';
		$this->mPage .= $upgradeView->LoadDefault($this->mProduct->GetCatalogue()->GetCatalogueId());
		$this->mPage .= '<div id="upgradeList">';
		foreach($this->mProduct->GetUpgrades() as $upgrade) {
			if($upgrade->GetDescription() == '') {
				$prodDesc = 'No Description';
			} else {
				$prodDesc = $presentationHelper->ChopDown($upgrade->GetDescription(), 50, 1);
			}
			$this->mPage .= <<<EOT
					<input type="hidden" value="UPGRADE{$upgrade->GetProductId()}" name="UPGRADE{$upgrade->GetProductId()}" id="UPGRADE{$upgrade->GetProductId()}" />
					<div class="upgradesProductContainer" id="UPGRADEproductContainer{$upgrade->GetProductId()}">
						<img src="" />
						<div>
							<strong>{$upgrade->GetDisplayName()}</strong><br />
							{$prodDesc}<br />
						</div>
					</div>
EOT;
		}
		$this->mPage .= '</div>'; // End upgradeList
		$this->mPage .= '</div>'; // End upgradesContentArea
	}

	function LoadCrossSellDisplay() {
		$registry = Registry::getInstance();
		$relatedView = new RelatedView();
		$similarView = new SimilarView();
		$presentationHelper = new PresentationHelper();
		$this->mPage .= <<<EOT
			<div id="crossSellContentArea">
			{$relatedView->LoadDefault($this->mProduct->GetCatalogue()->GetCatalogueId())}
				<div id="relatedList">
EOT;
		foreach($this->mProduct->GetRelated() as $related) {
			$prodName = $presentationHelper->ChopDown($related->GetDisplayName(), 25, 1);
			if($related->GetDescription() == '') {
				$prodDesc = 'No Description';
			} else {
				$prodDesc = $presentationHelper->ChopDown($related->GetDescription(), 50, 1);
			}
			$image = $related->GetMainImage();
			$this->mPage .= <<<EOT
					<input type="hidden" value="RELATED{$related->GetProductId()}" name="RELATED{$related->GetProductId()}" id="RELATED{$related->GetProductId()}" />
					<div class="relatedProductContainer" id="RELATEDproductContainer{$related->GetProductId()}">
						<img src="{$registry->rootDir}/{$registry->smallImageDir}{$image->GetFilename()}" />
						<div>
							<strong>{$prodName}</strong><br />
							{$prodDesc}<br />
						</div>
					</div>
EOT;
		} // End foreach


		$this->mPage .= <<<EOT
				</div><br /><br />
				{$similarView->LoadDefault($this->mProduct->GetCatalogue()->GetCatalogueId())}
				<div id="similarList">
EOT;
		foreach($this->mProduct->GetSimilar() as $similar) {
			$prodName = $presentationHelper->ChopDown($similar->GetDisplayName(), 25, 1);
			if($similar->GetDescription() == '') {
				$prodDesc = 'No Description';
			} else {
				$prodDesc = $presentationHelper->ChopDown($similar->GetDescription(), 50, 1);
			}
			$image = $similar->GetMainImage();
			$this->mPage .= <<<EOT
					<input type="hidden" value="SIMILAR{$similar->GetProductId()}" name="SIMILAR{$similar->GetProductId()}" id="SIMILAR{$similar->GetProductId()}" />
					<div class="similarProductContainer" id="SIMILARproductContainer{$similar->GetProductId()}">
						<img src="{$registry->rootDir}/{$registry->smallImageDir}{$image->GetFilename()}" />
						<div>
							<strong>{$prodName}</strong><br />
							{$prodDesc}<br />
						</div>
					</div>
EOT;
		} // End foreach
		$this->mPage .= <<<EOT
				</div>
			</div>
EOT;
	} // End LoadCrossSellDisplay


	//! Loads the Images section
	function LoadImagesDisplay() {
		$registry = Registry::GetInstance();
		$this->mPage .= <<<EOT
			<div id="imagesContentArea">
				<strong>Add an Image</strong><br />

				<iframe src="{$registry->formHandlersDir}/ImageUploadHandler.php?productId={$this->mProduct->GetProductId()}"
						id="uploadImageIframe"
						name="uploadImageIframe"
						scrolling="no"
						frameborder="0"
						/></iframe><br />
				<strong>Current Images</strong><br /><br />
				<div class="imageListContainer" id="imageListContainer">
EOT;
		$allImages = $this->mProduct->GetImages();
		foreach($allImages as $image) {
			if($image->GetMainImage()) {
				$imageMain = 'checked';
			} else {
				$imageMain = '';
			}
			$altText = trim($image->GetAltText());
			$this->mPage .= <<<EOT
					<div class="imageContainer">
						<img src="{$registry->rootDir}/{$registry->smallImageDir}{$image->GetFilename()}" alt="{$image->GetAltText()}" />
						<div>
							<label for="AltText{$image->GetImageId()}" style="width: 100px;">Alt Text:</label><input type="text" name="AltText{$image->GetImageId()}" id="AltText{$image->GetImageId()}" value="{$altText}" /><br />
							<label for="MainImage" style="width: 100px;">Main Image:</label><input type="radio" name="MainImage" id="MainImage" value="{$image->GetImageId()}" style="width: auto;" {$imageMain} /><br />
							<label for="DeleteImage{$image->GetImageId()}" style="width: 100px;">Delete:</label><input type="checkbox" name="DeleteImage{$image->GetImageId()}" id="DeleteImage{$image->GetImageId()}" style="width: auto;" /><br />
							<input type="hidden" name="image{$image->GetImageId()}" id="image{$image->GetImageId()}" value="{$image->GetImageId()}" />
						</div>
					</div>
EOT;
		}

		$this->mPage .= <<<EOT
				</div>
			</div>
EOT;
	} // End LoadImages()


	//! Loads the categories section
	function LoadCategoriesDisplay() {
		$categoriesView = new CategoriesView();
		$presentationHelper = new PresentationHelper();
		$this->mPage .= '<div id="categoriesContentArea">';
		$this->mPage .= $categoriesView->LoadDefault($this->mProduct->GetCatalogue()->GetCatalogueId(), $this->mProduct);
		$this->mPage .= '<div id="categoriesList">';
		foreach($this->mProduct->GetCategories() as $category) {
			if($category->GetDescription() == '') {
				$categoryDescription = 'No Description';
			} else {
				$categoryDescription = $presentationHelper->ChopDown($category->GetDescription(), 50, 1);
			}
			$parentCategory = $category->GetParentCategory();
			if(NULL !== $parentCategory) {
				$parent = $parentCategory->GetDisplayName() . ' > ';
			} else {
				$parent = '';
			}
			$this->mPage .= <<<EOT
					<input type="hidden" value="CATEGORY{$category->GetCategoryId()}" name="CATEGORY{$category->GetCategoryId()}" id="CATEGORY{$category->GetCategoryId()}" />
					<div class="categoriesCategoryContainer" id="CATEGORYcategoryContainer{$category->GetCategoryId()}" name="CATEGORYcategoryContainer{$category->GetCategoryId()}">
						<img src="" />
						<div>
							<strong>{$parent}{$category->GetDisplayName()}</strong><br />
							{$categoryDescription}<br />
						</div>
					</div>
EOT;
		}
		$this->mPage .= '</div>'; // End upgradeList
		$this->mPage .= '</div>'; // End upgradesContentArea
	} // End LoadCategories()


} // End AdminProductView class


$page = new AdminProductView();
if(isset($_GET ['id'])) {
	echo $page->LoadDefault($_GET ['id']);
}
if(isset($_GET ['tab'])) {
	echo '<script language="javascript" type="text/javascript">
			var tabsArray = new Array();
			showTab(tabsArray,\'' . $_GET ['tab'] . '\',\'adminProductViewTabContainer\')
			</script>';
}

?>