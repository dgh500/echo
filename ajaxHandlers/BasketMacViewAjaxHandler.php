<?php
session_start();
include_once('../autoload.php');

if(isset($_POST['catalogueId'])) {$catalogue = new CatalogueModel($_POST['catalogueId']);}
$str = '';
$basket = new BasketModel(session_id());
$handler = new BasketMacViewAjaxHandler;

switch($_POST['what']) {
	
	// Clicked a top level category..
	case 'GetProductCategories':
		// The top level category to load the sub categories of
		$category = new CategoryModel($_POST['categoryIdentifier']);
		// To get the subcategories
		$categoryController = new CategoryController;
		$allSubCategories = $categoryController->GetAllSubCategoriesOf($category);
		foreach($allSubCategories as $subCategory) {
			$str .= '	<div id="subLevelCategory'.$subCategory->GetCategoryId().'" class="macViewMenuItem">
							<a href="#" id="'.$subCategory->GetCategoryId().'" name="'.$subCategory->GetCategoryId().'">'.ucwords(strtolower($subCategory->GetDisplayName())).'</a>
						</div>';
		}
		$allProducts = $categoryController->GetAllProductsIn($category);
		foreach($allProducts as $product) {
			$str .= '	<div id="subLevelProduct'.$product->GetProductId().'" class="macViewProductMenuItem">
							<a href="#" id="subLevelProductItem'.$product->GetProductId().'" name="'.$product->GetProductId().'">'.ucwords(strtolower($product->GetDisplayName())).'</a>
						</div>';		
		} // End looping over products
		if($str == '') { $str = 'No Products'; }
	break;
	
	// Clicked a product - if it has attributes then return them, otherwise just add the product to the basket
	case 'ProductClick':
		// Which product has been clicked?
		$product = new ProductModel($_POST['productId']);
		// Does the product have options?
		$productHasAttributes = !$product->HasNoAttributes();
		
		// If the product has options, then return a div with the option display in it
		if($productHasAttributes) {
			$str .= '<div id="productOptionsDialogContainer">';
			$str .= ''.$product->GetDisplayName().' - ';
			$str .= $productOptions = $handler->LoadProductOptions($product);
			$str .= $productOptions = $handler->LoadAllSkus($product);
			$str .= '</div>';
			$str .= '<input type="hidden" id="showProductOptions" value="1" />';
			$str .= '<input type="hidden" id="productName" value="'.$product->GetDisplayName().'" />';
			$str .= '<input type="hidden" id="productPrice" value="'.$product->GetActualPrice().'" />';
			$str .= '<input type="hidden" id="productId" value="'.$product->GetProductId().'" />';
		}
		
		// Otherwise add the (only) SKU to the basket
		if(!$productHasAttributes) {
			$skus = $product->GetSkus();
			$basket->AddToBasket($skus[0],false,$skus[0]->GetSkuPrice(),false);
			$str .= '<input type="hidden" id="showProductOptions" value="0" />';
			$str .= '<input type="hidden" id="productName" value="'.$product->GetDisplayName().'" />';
			$str .= '<input type="hidden" id="productPrice" value="'.$product->GetActualPrice().'" />';
			$str .= '<input type="hidden" id="productId" value="'.$product->GetProductId().'" />';			
		}
	break; // End ProductClick
	
	// TopLevelPackage (Ie. Packages/Stacks)
	case 'GetPackageCategories':
		$categoryController = new CategoryController;
		$allPackageCategories = $categoryController->GetAllTopLevelPackageCategoriesForCatalogue($catalogue);
		foreach ( $allPackageCategories as $packageCategory) {
			$str .= '	<div id="subLevelCategory'.$packageCategory->GetCategoryId().'" class="macViewMenuItem">
							<a href="#" id="subLevelCategoryItem'.$packageCategory->GetCategoryId().'" name="'.$packageCategory->GetCategoryId().'">'.ucwords(strtolower($packageCategory->GetDisplayName())).'</a>
						</div>';
		}
	break;
	
	// Sub Level Package (Eg. Mask Packages)
	case 'GetPackages':
		$category = new CategoryModel($_POST['categoryId']);
		$categoryController = new CategoryController ( );
		$allPackages = $categoryController->GetAllPackagesIn ( $category );
		foreach ( $allPackages as $package ) {
			if (! is_null ( $package->GetDisplayName () ) && trim ( $package->GetDisplayName () ) != '') {
				$str .= '
				<div class="macViewProductMenuItem">
					<a href="#" name="'.$package->GetPackageId().'">'.ucwords(strtolower($package->GetDisplayName())).'</a>
					<input type="hidden" id="packagePrice'.$package->GetPackageId().'" value="'.number_format($package->GetActualPrice(),2).'" />
				</div>';
			}
		}
	break;
	
	// Gets the list of package items
	case 'GetPackageItems':
		// The package we're interested in
		$package = new PackageModel($_POST['packageId']);
		// Make the form with upgrades etc. in
		$str .= '<div id="packageContentsDialogContainer">';
		$str .= '<div id="optionsContainer">';
		$str .= '<h3>Package Contents</h3><ol>';	
		// For each package item display it as a list of links, which when clicked bring up the upgrade options
		/*
			The ID is the product ID, the name is the package ID, and the title is there to help
		 */
		$str .= '<input type="hidden" id="packageIdentifier" value="'.$package->GetPackageId().'" />';	 
		foreach($package->GetContents() as $product) {
			//***** Load Attributes
			$productOptions = $handler->LoadProductOptions($product);
			// Hide the SKU for the product in an <input> if there are no attributes (otherwise it is figured out 'on demand')
			if($product->HasNoAttributes()) {
				$skus = $product->GetSkus();
				$str .= '<input type="hidden" id="productSku'.$product->GetProductId().'" value="'.$skus[0]->GetSkuId().'" />';	
			}
			
			$str .= '<li>
						<a 	href="#" 
							id="'.$product->GetProductId().'" 
							name="'.$package->GetPackageId().'" 
							title="Click For Upgrades">
							'.ucwords(strtolower($product->GetDisplayName())).'
						</a><br />
						<input type="hidden" id="packageProductUpgradePrice'.$product->GetProductId().'" value="0" />
						<input type="hidden" id="packageProductId'.$product->GetProductId().'" value="'.$product->GetProductId().'" />
						<div id="productOptionsFor'.$product->GetProductId().'">'.$productOptions.'</div>
					</li>';
		}
		$str .= '</ol></div>';	
		$str .= '<div id="upgradesContainer"></div>';
		$str .= '</div>';
	break;
	
	// Gets the package upgrade list 
	case 'GetPackageUpgrades':
		$registry = Registry::getInstance();						// To get the base directory
		$package = new PackageModel($_POST['packageId']);			// The package we're interested in
		$packageItem = new ProductModel($_POST['packageProductId']);// The package item that is being upgraded
		$upgrades = $package->GetUpgradesFor($packageItem);			// All possible upgrades
		// Initialise the display with a heading and a 'close upgrades' image
		$str .= '<h3>
						Package Upgrades
						<a id="packageCloseLink" href="#">
							<img id="packageClose" src="'.$registry->baseDir.'/images/closePackage.gif" />
						</a>
				</h3>';	
				
		// Display all possible upgrades if possible, otherwise say no upgrades
		if(count($upgrades)>0) {
			$str .= '<ol>';
			// This is the package item, so the user can revert back from an upgrade if the customer wants
			$str .= '
					<li>
						<a 	href="#" 
							id="'.$packageItem->GetProductId().'" 
							name="'.$packageItem->GetProductId().'" 
							class="packageUpgradeItem"
							>
							'.$packageItem->GetDisplayName().'
						</a>
					</li>
					<input type="hidden" id="upgradePrice'.$packageItem->GetProductId().'" value="0.00" />';
			// For each possible upgrade add it to the list.
			/*
			 The id holds the upgrade ID, the name holds the package item ID, the class is so that the 'close' button ^ doesn't activate the JS for these links
			 The price of the upgrade is held in the hidden form field
			 */
			foreach($upgrades as $upgrade) {
				// Hide the SKU for the product in an <input> if there are no attributes (otherwise it is figured out 'on demand')
				if($upgrade->HasNoAttributes()) {
					$skus = $upgrade->GetSkus();
					$str .= '<input type="hidden" id="productSku'.$upgrade->GetProductId().'" value="'.$skus[0]->GetSkuId().'" />';	
				}				
				$str .= '<li>
							&pound;'.$package->GetUpgradePrice($packageItem,$upgrade).' 
							<a 	href="#" 
								id="'.$upgrade->GetProductId().'" 
								name="'.$packageItem->GetProductId().'" 
								class="packageUpgradeItem">
								'.$upgrade->GetDisplayName().'
							</a>
						</li>
						<input type="hidden" id="upgradeId'.$packageItem->GetProductId().'" value="'.$upgrade->GetProductId().'" />
						<input 	type="hidden" 
								id="upgradePrice'.$upgrade->GetProductId().'" 
								value="'.number_format($package->GetUpgradePrice($packageItem,$upgrade),2).'" />';
			}
			// Close list
			$str .= '</ol>';
		// If no upgrades, say so!
		} else {
			$str .= '<ol>No Upgrades</ol>';	
		}
	break;
	
	// Get the attributes for the upgrade item
	case 'GetUpgradeOptions':
		$upgrade = new ProductModel($_POST['upgradeIdentifier']);
		$str .= $handler->LoadProductOptions($upgrade);
	break;
	
	case 'GetSKU':
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
	break;
	
	case 'AddSku':
		$sku = new SkuModel($_POST['skuIdentifier']);
		$basket->AddToBasket($sku,false,$sku->GetSkuPrice());
	break;
	
	case 'AddPackage':
		// Add Package
		$package = new PackageModel($_POST['packageIdentifier']);
		$basket->AddPackageToBasket($package);
	
		// Add package contents
		$skuArr = explode(',',$_POST['skuCSVList']);
		foreach($skuArr as $skuId) {
			$sku = new SKUModel($skuId);
			// If the product is an upgrade to the package then say so
			if($package->IsUpgrade($sku->GetParentProduct())) {
				$upgradeItem = $sku->GetParentProduct();
				$upgrade = true;
				// Get the upgrade price that should be paid
				$price = $package->GetUpgradePrice($package->GetProductForUpgrade($upgradeItem),$upgradeItem);
			} else {
				$upgrade = false;
				$price = '0.00';
			}
			$packageBool = !$upgrade;			
			$basket->AddToBasket($sku,$packageBool,$price,$upgrade);
		}		
	break;
}

class BasketMacViewAjaxHandler {
	
	//! Loads the <select> list of product attributes 
	function LoadProductOptions($product) {
		$i=0;
		$str = '';
		$allAttributes = $product->GetAttributes();
		$str .= '<input type="hidden" id="attributeCount'.$product->GetProductId().'" value="'.count($allAttributes).'" />';
		// If the product has options, display them
		foreach($allAttributes as $attribute) {
			$str .= '<select id="productAttributes'.$product->GetProductId().'LOOP'.$i.'"><optgroup label="'.trim(ucfirst($attribute->GetAttributeName())).'">';
			$allSkuAttributes = $attribute->GetSkuAttributes();
			$values = array();
			foreach($allSkuAttributes as $skuAttribute) {
				if(!in_array(trim($skuAttribute->GetAttributeValue()),$values)) {
					$str .= '<option value="'.$skuAttribute->GetSkuAttributeId().'">'.ucfirst($skuAttribute->GetAttributeValue()).'</option>';
				}
				$values[] = trim($skuAttribute->GetAttributeValue());
			} // End foreach($allSkuAttributes
			$str .= '</optgroup></select>';
			$i++;
		} // End foreach($allAttributes	
		return $str;
	}
	
	//! Loads a div with all the SKUs for a product - this is hidden by default
	function LoadAllSkus($product) {
		$str = '<div id="allProductSkus" style="display: none">';
		$str .= '<h3>Possible Options Are..</h3>';
		// Get all SKUs 
		$skus = $product->GetSkus();
		foreach($skus as $sku) {
			$str .= ' - '.$sku->GetSkuAttributesList(false).'<br />';
		}
		$str .= '</div>';
		return $str;
	} // End LoadAllSkus
	
}

echo $str;
?>