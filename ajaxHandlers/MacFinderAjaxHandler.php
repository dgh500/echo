<?php
$disableJavascriptAutoload = 1;
include_once ('../autoload.php');

//! Handles requests from the MaxFinder interface
class MacFinderAjaxHandler extends AjaxHandler {

	//! Prefixed to all IDs to avoid clashes
	var $mPrefix;
	//! Style of output, defaults to product form variety, also has orderForm option
	var $mStyle;
	//! The ID of the element to write any output to
	var $mTargetElement;

	//! Sets default header() values
	function __construct() {
		$this->Initialise ();
		$this->SetDataType ( 'xml' );
	}

	//! Handles requests and returns the appropriate response
	function RequestHandler($getArray) {
		$this->mPrefix = $getArray ['prefix'];
		$this->mStyle = $getArray ['style'];
		$this->mTargetElement = $getArray ['targetElement'];
		foreach ( $getArray as $key => $value ) {
			switch ($key) {
				case 'topLevelCategory' :
					$category = $value;
					$this->HandleTopLevelCategory ( $category );
					break;
				case 'subCategory' :
					$subCategory = $value;
					$this->HandleSubLevelCategory ( $subCategory );
					break;
				case 'packageAdd' :
					$packageId = $value;
					$this->HandlePackageAdd ( $packageId );
					break;
				case 'productAdd' :
					$productId = $value;
					$this->HandleProductAdd ( $productId );
					break;
				case 'topLevelPackages' :
					$catalogueId = $value;
					$this->HandleTopLevelPackage ( $catalogueId );
					break;
				case 'productRemove' :
					$productId = $value;
					$this->HandleProductRemove ( $productId );
					break;
				case 'packageRemove' :
					$packageId = $value;
					$this->HandlePackageRemove ( $packageId );
					break;
				case 'addToBasket' :
					$productId = $value;
					$this->HandleAddToBasket ( $productId );
					break;
			} // End switch
		} // End foreach
	} // End RequestHandler()


	function HandleTopLevelCategory($category) {
		$category = new CategoryModel ( $category );
		$categoryController = new CategoryController ( );
		$allSubCategories = $categoryController->GetAllSubCategoriesOf ( $category,true,true );
		$this->mReturn .= '<root>';
		$this->mReturn .= '<who>MacView</who>';
		$this->mReturn .= '<what>topLevel</what>';
		$this->mReturn .= '<prefix>' . $this->mPrefix . '</prefix>';
		$this->mReturn .= '<style>' . $this->mStyle . '</style>';
		$this->mReturn .= '<targetElement>' . $this->mTargetElement . '</targetElement>';
		$this->mReturn .= '<subCategoryList>';
		foreach ( $allSubCategories as $subCategory ) {
			$this->mReturn .= '<subCategory>';
			$this->mReturn .= '<subCategoryId>' . $subCategory->GetCategoryId () . '</subCategoryId>';
			$this->mReturn .= '<subCategoryName>' . htmlspecialchars ( $subCategory->GetDisplayName () ) . '</subCategoryName>';
			$this->mReturn .= '</subCategory>';
		}
		$this->mReturn .= '</subCategoryList><productList>';
		$allProducts = $categoryController->GetAllProductsIn ( $category );
		foreach ( $allProducts as $product ) {
			$this->mReturn .= '<product>';
			$this->mReturn .= '<productId>' . $product->GetProductId () . '</productId>';
			$this->mReturn .= '<productName>' . htmlspecialchars ( $product->GetDisplayName () ) . '</productName>';
			$this->mReturn .= '</product>';
		}
		$this->mReturn .= '</productList></root>';
		$this->ReturnResponse ();
	}

	function HandleSubLevelCategory($subCategory) {
		$category = new CategoryModel ( $subCategory );
		$categoryController = new CategoryController ( );
		$allProducts = $categoryController->GetAllProductsIn ( $category );
		$this->mReturn .= '<root>';
		$this->mReturn .= '<who>MacView</who>';
		$this->mReturn .= '<what>subLevel</what>';
		$this->mReturn .= '<prefix>' . $this->mPrefix . '</prefix>';
		$this->mReturn .= '<style>' . $this->mStyle . '</style>';
		$this->mReturn .= '<targetElement>' . $this->mTargetElement . '</targetElement>';
		$this->mReturn .= '<productList>';
		foreach ( $allProducts as $product ) {
			$this->mReturn .= '<product>';
			$this->mReturn .= '<productId>' . $product->GetProductId () . '</productId>';
			$this->mReturn .= '<productName>' . htmlspecialchars ( $product->GetDisplayName () ) . '</productName>';
			$this->mReturn .= '</product>';
		}
		$this->mReturn .= '</productList><packageList>';
		$allPackages = $categoryController->GetAllPackagesIn ( $category );
		foreach ( $allPackages as $package ) {
			if (! is_null ( $package->GetDisplayName () ) && trim ( $package->GetDisplayName () ) != '') {
				$this->mReturn .= '<package>';
				$this->mReturn .= '<packageId>' . $package->GetPackageId () . '</packageId>';
				$this->mReturn .= '<packageName>' . $package->GetDisplayName () . '</packageName>';
				$this->mReturn .= '</package>';
			}
		}
		$this->mReturn .= '</packageList></root>';
		$this->ReturnResponse ();
	} // End HandleSubLevelCategory


	function HandlePackageAdd($packageId) {
		$package = new PackageModel ( $packageId );
		$this->mReturn .= '<root>';
		$this->mReturn .= '<who>MacView</who>';
		$this->mReturn .= '<what>packageAdd</what>';
		$this->mReturn .= '<style>' . $this->mStyle . '</style>';
		$this->mReturn .= '<prefix>' . $this->mPrefix . '</prefix>';
		$this->mReturn .= '<targetElement>' . $this->mTargetElement . '</targetElement>';
		$this->mReturn .= '<packageId>' . $package->GetPackageId () . '</packageId>';
		$this->mReturn .= '<packageName>' . htmlspecialchars ( str_replace ( '.', '', $package->GetDisplayName () ) ) . '</packageName>';
		$this->mReturn .= '<packagePrice>' . htmlspecialchars ( $package->GetActualPrice () ) . '</packagePrice>';
		$this->mReturn .= '<packagePostage>' . htmlspecialchars ( $package->GetPostage () ) . '</packagePostage>';
		$packageContents = $package->GetContents ();
		// Package Contents
		$this->mReturn .= '<packageContentsList>';
		foreach ( $packageContents as $product ) {
			$this->mReturn .= '<packageContent>';
			$this->mReturn .= '<productId>' . $product->GetProductId () . '</productId>';
			$this->mReturn .= '<productName>' . htmlspecialchars ( $product->GetDisplayName () ) . '</productName>';
			$this->mReturn .= '<productActualPrice>' . htmlspecialchars ( $product->GetActualPrice () ) . '</productActualPrice>';
			$this->mReturn .= '<productPostage>' . htmlspecialchars ( $product->GetPostage () ) . '</productPostage>';
			$this->mReturn .= '<productUpgradePrice>' . htmlspecialchars ( $product->GetUpgradePrice () ) . '</productUpgradePrice>';
			// Product Attributes
			$allAttributes = $product->GetAttributes ();
			foreach ( $allAttributes as $attribute ) {
				$this->mReturn .= '<attribute><attributeName>' . $attribute->GetAttributeName () . '</attributeName><attributeId>' . $attribute->GetProductAttributeId () . '</attributeId>';
				$skuAttributes = $attribute->GetSkuAttributes ();
				$valuesArr = array ();
				foreach ( $skuAttributes as $skuAttribute ) {
					if (! is_null ( $skuAttribute->GetAttributeValue () ) && $skuAttribute->GetAttributeValue () != ' ' && ! in_array ( trim ( $skuAttribute->GetAttributeValue () ), $valuesArr )) {
						$this->mReturn .= '<skuAttribute>';
						$valuesArr [] = trim ( $skuAttribute->GetAttributeValue () );
						$this->mReturn .= '<skuAttributeValue>' . htmlspecialchars ( $skuAttribute->GetAttributeValue () ) . '</skuAttributeValue>';
						$this->mReturn .= '<skuAttributeId>' . $skuAttribute->GetSkuAttributeId () . '</skuAttributeId>';
						$this->mReturn .= '</skuAttribute>';
					}
				}
				$this->mReturn .= '</attribute>';
			} // End product attributes
			// Product Upgrades
			$allUpgrades = $package->GetUpgradesFor ( $product );
			foreach ( $allUpgrades as $upgrade ) {
				if (! is_null ( $upgrade->GetDisplayName () ) && trim ( $upgrade->GetDisplayName () ) != '') {
					$this->mReturn .= '<productUpgrade>';
					$this->mReturn .= '<upgradeId>' . $upgrade->GetProductId () . '</upgradeId>';
					$this->mReturn .= '<upgradeName>' . htmlspecialchars ( $upgrade->GetDisplayName () ) . '</upgradeName>';
					$this->mReturn .= '<upgradePrice>' . $package->GetUpgradePrice ( $product, $upgrade ) . '</upgradePrice>';
					// Upgrade Attributes
					$allAttributes = $upgrade->GetAttributes ();
					foreach ( $allAttributes as $attribute ) {
						$this->mReturn .= '<attribute><attributeName>' . trim ( $attribute->GetAttributeName () ) . '</attributeName><attributeId>' . $attribute->GetProductAttributeId () . '</attributeId>';
						$skuAttributes = $attribute->GetSkuAttributes ();
						$valuesArr = array ();
						foreach ( $skuAttributes as $skuAttribute ) {
							if (! is_null ( $skuAttribute->GetAttributeValue () ) && $skuAttribute->GetAttributeValue () != ' ' && ! in_array ( trim ( $skuAttribute->GetAttributeValue () ), $valuesArr )) {
								$this->mReturn .= '<skuAttribute>';
								$valuesArr [] = trim ( $skuAttribute->GetAttributeValue () );
								$this->mReturn .= '<skuAttributeValue>' . $skuAttribute->GetAttributeValue () . '</skuAttributeValue>';
								$this->mReturn .= '<skuAttributeId>' . $skuAttribute->GetSkuAttributeId () . '</skuAttributeId>';
								$this->mReturn .= '</skuAttribute>';
							}
						}
						$this->mReturn .= '</attribute>';
					} // End product attributes
					$this->mReturn .= '</productUpgrade>';
				}
			} // End product upgrades
			$this->mReturn .= '</packageContent>';
		} // End package contents
		$this->mReturn .= '</packageContentsList>';
		$this->mReturn .= '</root>';
		$this->ReturnResponse ();
	} // End HandlePackageAdd()


	function HandleProductAdd($productId) {
		$product = new ProductModel ( $productId );
		$presentationHelper = new PresentationHelper ( );
		if ($product->GetDescription () == '') {
			$prodDesc = 'No Description';
		} else {
			$prodDesc = $product->GetDescription ();
		}
		if ($product->GetActualPrice () == '') {
			$prodActualPrice = 'No Actual Price';
		} else {
			$prodActualPrice = $product->GetActualPrice ();
		}
		if ($product->GetUpgradePrice () == '') {
			$prodUpgradePrice = 'No Upgrade Price';
		} else {
			$prodUpgradePrice = $product->GetUpgradePrice ();
		}
		$this->mReturn .= '<root>';
		$this->mReturn .= '<who>MacView</who>';
		$this->mReturn .= '<what>productAdd</what>';
		$this->mReturn .= '<prefix>' . $this->mPrefix . '</prefix>';
		$this->mReturn .= '<style>' . $this->mStyle . '</style>';
		$this->mReturn .= '<targetElement>' . $this->mTargetElement . '</targetElement>';
		$this->mReturn .= '<productId>' . $product->GetProductId () . '</productId>';
		$this->mReturn .= '<productPrice>' . $product->GetActualPrice () . '</productPrice>';
		$this->mReturn .= '<productPostage>' . $product->GetPostage () . '</productPostage>';
		$this->mReturn .= '<productName>' . htmlspecialchars ( $presentationHelper->ChopDown ( $product->GetDisplayName (), 25, 1 ) ) . '</productName>';
		$this->mReturn .= '<productDescription>' . $presentationHelper->ChopDown ( htmlspecialchars ( $prodDesc ), 50, 1 ) . '</productDescription>';
		$this->mReturn .= '<productActualPrice>' . htmlspecialchars ( $prodActualPrice ) . '</productActualPrice>';
		$this->mReturn .= '<productUpgradePrice>' . htmlspecialchars ( $prodUpgradePrice ) . '</productUpgradePrice>';
		$allAttributes = $product->GetAttributes ();
		$this->mReturn .= '<attributeList>';
		foreach ( $allAttributes as $attribute ) {
			$this->mReturn .= '<attribute>';
			$this->mReturn .= '<attributeName>' . htmlspecialchars ( trim ( $attribute->GetAttributeName () ) ) . '</attributeName>';
			$this->mReturn .= '<attributeId>' . $attribute->GetProductAttributeId () . '</attributeId>';
			$skuAttributes = $attribute->GetSkuAttributes ();
			$valuesArr = array ();
			$this->mReturn .= '<skuAttributesList>';
			foreach ( $skuAttributes as $skuAttribute ) {
				if (! is_null ( $skuAttribute->GetAttributeValue () ) && $skuAttribute->GetAttributeValue () != ' ' && ! in_array ( trim ( $skuAttribute->GetAttributeValue () ), $valuesArr )) {
					$this->mReturn .= '<skuAttribute>';
					$valuesArr [] = trim ( $skuAttribute->GetAttributeValue () );
					$this->mReturn .= '<skuAttributeValue>' . htmlspecialchars ( $skuAttribute->GetAttributeValue () ) . '</skuAttributeValue>';
					$this->mReturn .= '<skuAttributeId>' . $skuAttribute->GetSkuAttributeId () . '</skuAttributeId>';
					$this->mReturn .= '</skuAttribute>';
				}
			}
			$this->mReturn .= '</skuAttributesList>';
			$this->mReturn .= '</attribute>';
		}
		$this->mReturn .= '</attributeList>';
		$allUpgrades = $product->GetUpgrades ();
		$this->mReturn .= '<upgradeList>';
		foreach ( $allUpgrades as $upgrade ) {
			if (! is_null ( $upgrade->GetDisplayName () ) && trim ( $upgrade->GetDisplayName () ) != '') {
				$this->mReturn .= '<productUpgrade>';
				$this->mReturn .= '<upgradeId>' . $upgrade->GetProductId () . '</upgradeId>';
				$this->mReturn .= '<upgradeName>' . htmlspecialchars ( $upgrade->GetDisplayName () ) . '</upgradeName>';
				$this->mReturn .= '<upgradePrice>' . $upgrade->GetUpgradePrice () . '</upgradePrice>';
				$this->mReturn .= '</productUpgrade>';
			}
		}
		$this->mReturn .= '</upgradeList>';
		$this->mReturn .= '</root>';
		$this->ReturnResponse ();
	} // End HandleProductAdd()


	function HandleTopLevelPackage($catalogueId) {
		$categoryController = new CategoryController ( );
		$catalogue = new CatalogueModel ( $catalogueId );
		$this->mReturn .= '<root>';
		$this->mReturn .= '<who>MacView</who>';
		$this->mReturn .= '<what>topLevelPackages</what>';
		$this->mReturn .= '<style>' . $this->mStyle . '</style>';
		$this->mReturn .= '<targetElement>' . $this->mTargetElement . '</targetElement>';
		$this->mReturn .= '<prefix>' . $this->mPrefix . '</prefix>';
		$this->mReturn .= '<packageCategoryList>';
		$allPackageCategories = $categoryController->GetAllTopLevelPackageCategoriesForCatalogue ( $catalogue );
		foreach ( $allPackageCategories as $packageCategory ) {
			$this->mReturn .= '<packageCategory>';
			$this->mReturn .= '<categoryId>' . htmlspecialchars ( $packageCategory->GetCategoryId () ) . '</categoryId>';
			$this->mReturn .= '<categoryName>' . htmlspecialchars ( $packageCategory->GetDisplayName () ) . '</categoryName>';
			$this->mReturn .= '</packageCategory>';
		}
		$this->mReturn .= '</packageCategoryList>';
		$this->mReturn .= '</root>';
		$this->ReturnResponse ();
	} // End HandleTopLevelPackage()


	function HandleProductRemove($productId) {
		$this->mReturn .= '<root>';
		$this->mReturn .= '<who>MacView</who>';
		$this->mReturn .= '<what>productRemove</what>';
		$this->mReturn .= '<style>' . $this->mStyle . '</style>';
		$this->mReturn .= '<prefix>' . $this->mPrefix . '</prefix>';
		$this->mReturn .= '<targetElement>' . $this->mTargetElement . '</targetElement>';
		$this->mReturn .= '<productId>' . $productId . '</productId>';
		$this->mReturn .= '</root>';
		$this->ReturnResponse ();
	}

	function HandlePackageRemove($packageId) {
		$this->mReturn .= '<root>';
		$this->mReturn .= '<who>MacView</who>';
		$this->mReturn .= '<what>packageRemove</what>';
		$this->mReturn .= '<style>' . $this->mStyle . '</style>';
		$this->mReturn .= '<prefix>' . $this->mPrefix . '</prefix>';
		$this->mReturn .= '<targetElement>' . $this->mTargetElement . '</targetElement>';
		$this->mReturn .= '<packageId>' . $packageId . '</packageId>';
		$this->mReturn .= '</root>';
		$this->ReturnResponse ();
	} // End HandlePackageRemove()


	function HandleAddToBasket($productId) {

	}

} // End MacFinderAjaxHandler class


$page = new MacFinderAjaxHandler ( );
$page->RequestHandler ( $_GET );

?>