<?php
#session_start(); // Really needed?


require_once ('../autoload.php');

//! Adds a package to the current basket
class AddPackageToBasketHandler {
	
	//! A cleaned (validated) array of the submitted variables
	var $mClean;
	
	//! Initialises the validation helper
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mSessionHelper = new SessionHelper ( );
	}
	
	//! Validation function - takes the $_POST array and processes it. By the end of this function call there will be a member mClean variable with the following structure:
	/*
		$this->mClean	['package']  : Obj : PackageModel	- The package to add to the basket
						['category'] : Obj : CategoryModel	- The category the product is in
						['basket']	 : Obj : BasketModel 	- The basket to add the product to
						['referPage']: Str - The page that referred the basket
						['parentCategory'] 		: Obj : CategoryModel - 
						['multibuyQuantity']	: Int - The quantity of the product being ordered (1 if multibuy isn't enabled)
						['basketSkus'][]	: Obj : SkuModel - A collection of SKUs to add to the basket (Eg. flavours/sizes)
	 */
	/*! 
	 * @param $postArr - The $_POST array, expects $_POST['packageId'],['basketId'],['referPage'], optional - ['categoryId'],['parentCategoryId']
	 */
	function Validate($postArr) {
		// Required
		$this->mClean ['package'] = new PackageModel ( $postArr ['packageId'] );
		$this->mClean ['basket'] = new BasketModel ( $postArr ['basketId'] );
		$this->mClean ['referPage'] = $postArr ['referPage'];
		
		// Optional
		if (isset ( $postArr ['categoryId'] )) {
			$this->mClean ['category'] = new CategoryModel ( $postArr ['categoryId'] );
		}
		if (isset ( $postArr ['parentCategoryId'] )) {
			$this->mClean ['parentCategory'] = new CategoryModel ( $postArr ['parentCategoryId'] );
		}
		
		// Add SKUs to basket
		$products = $this->mClean ['package']->GetContents ();
		foreach ( $products as $product ) {
			$allProductAttributes = $product->GetAttributes ();
			if (count ( $allProductAttributes ) > 0) {
				// The product has attributes
				if (! isset ( $postArr ['package' . $this->mClean ['package']->GetPackageId () . 'product' . $product->GetProductId ()] )) {
					// The product has not been upgraded
					$allProductAttributes = $product->GetAttributes ();
					foreach ( $allProductAttributes as $attribute ) {
						$this->mClean ['skuAttribute' . $attribute->GetProductAttributeId ()] = $postArr ['skuAttribute' . $attribute->GetProductAttributeId ()];
					}
					$this->AddSkusToBasketForProduct ( $product );
				} else {
					// The $product has been upgraded, and the value can be found in the value of the radio input
					$value = $postArr ['package' . $this->mClean ['package']->GetPackageId () . 'product' . $product->GetProductId ()];
					$upgradeArr = explode ( 'upgrade', $value );
					$upgrade = new ProductModel ( $upgradeArr [1] );
					$allProductAttributes = $upgrade->GetAttributes ();
					foreach ( $allProductAttributes as $attribute ) {
						$this->mClean ['skuAttribute' . $attribute->GetProductAttributeId ()] = $postArr ['skuAttribute' . $attribute->GetProductAttributeId ()];
					}
					$this->AddSkusToBasketForProduct ( $upgrade );
				}
			} else {
				// No Attributes
				if (isset ( $postArr ['package' . $this->mClean ['package']->GetPackageId () . 'product' . $product->GetProductId ()] )) {
					// The $product has been upgraded, and the value can be found in the value of the radio input
					$value = $postArr ['package' . $this->mClean ['package']->GetPackageId () . 'product' . $product->GetProductId ()];
					$upgradeArr = explode ( 'upgrade', $value );
					$upgrade = new ProductModel ( $upgradeArr [1] );
					$allSkus = $upgrade->GetSkus ();
					$this->mClean ['basketSkus'] [] = $allSkus [0]->GetSkuId (); // If the upgrade has no attributes then the first SKU is the only one
				} else {
					$allSkus = $product->GetSkus ();
					$this->mClean ['basketSkus'] [] = $allSkus [0]->GetSkuId (); // If a product has no attributes then the first SKU is the only one
				}
			} // end if
		} // End foreach
	} // End Validate()
	

	//! Analyses the submitted options and adds the correct SKU for the product
	/*!
	 * @param $productModel : Obj : ProductModel - The product to add to the basket
	 * @return Void
	 */
	function AddSkusToBasketForProduct($product) {
		$productAttributes = $product->GetAttributes ();
		$currentArr = array ();
		$currentValueArr = array ();
		$skuAttributeIdArr = array ();
		// Loop over each product attribute and
		foreach ( $productAttributes as $attribute ) {
			// Get SKU Attribute ID
			$skuAttributeId = $this->mClean ['skuAttribute' . $attribute->GetProductAttributeId ()];
			// Make an SKU attribute model (To get the SKU ID)
			$skuAttribute = new SkuAttributeModel ( $skuAttributeId );
			// Add the SKU ID to the $currentArr
			$currentArr [] = $skuAttribute->GetSkuId ();
			$currentValueArr [] = $skuAttribute->GetAttributeValue ();
			// Add the SKU Attribute ID to an array (in case the SKU isn't set)
			$skuAttributeIdArr [] = $skuAttributeId;
		}
		// If all entries are the same then add to basketSkus
		if ($this->CheckAllEntriesTheSame ( $currentArr )) {
			$this->mClean ['basketSkus'] [] = array_pop ( $currentArr );
		} else {
			$sku = $this->AlternateSkuCombination ( $currentValueArr, $product );
			if ($sku) {
				$this->mClean ['basketSkus'] [] = $sku->GetSkuId ();
			} else {
				#$skuError = $this->ConstructSkuErrorMessage($skuAttributeIdArr);
				$this->mClean ['problemSkus'] [] = $skuError;
			}
		}
	} // End AddSkusToBasketForProduct()
	

	//! Looks at the text of the attribute value (not the ID) because 'Black' would only be displayed once for example and returns the SKU if one is found, False otherwise
	/*! 
	 * @param $attributeValueArray : Array of structure: 
	 *
	 * array(2) {
	 * 		[0]=>
  	 *		string(6) "Yellow"
  	 *		[1]=>
  	 *		string(8) "9.5/10.5"
	 *	}
	 *
	 *	For example for the Yellow, 9.5/10.5 size Mares Volo Powers
	 *
	 * @param $product : Obj : ProductModel - The product to look at
	 * @return Either Obj : SkuModel if there is a matching SKU, or Bool : False otherwise
	 */
	function AlternateSkuCombination($attributeValueArray, $product) {
		$skus = $product->GetSkus ();
		foreach ( $skus as $sku ) {
			$lock = false;
			foreach ( $attributeValueArray as $value ) {
				if (! $sku->HasAttributeValue ( $value )) {
					$lock = true;
				}
			}
			// If the values array has 'survived' unlocked then a SKU exists for them - return it.
			if (! $lock) {
				return $sku;
			}
		}
		// Return false on failure to find the combination
		return false;
	} // End AlternateSkuCombination
	

	//! Checks whether all the entries in an array are the same
	/*!
	 * @param $array - The array to check
	 * @return Boolean; True if they are
	 */
	function CheckAllEntriesTheSame($array) {
		foreach ( $array as $value ) {
			if (! isset ( $runningArr )) {
				$runningArr [] = $value;
			}
			if (! in_array ( $value, $runningArr )) {
				return false;
			}
		}
		return true;
	} // End CheckAllEntriesTheSame
	

	//! Adds the SKUs determined in the validation function to the basket
	function AddToBasket() {
		
		// Add all of the submitted SKUs for this package
		foreach ( $this->mClean ['basketSkus'] as $skuId ) {
			$sku = new SkuModel ( $skuId );
			$product = $sku->GetParentProduct ();
			
			// Handle upgrades
			if ($this->mClean ['package']->IsUpgrade ( $product )) {
				$upgrade = $product;
				$product = $this->mClean ['package']->GetProductForUpgrade ( $upgrade );
				$upgradePrice = $this->mClean ['package']->GetUpgradePrice ( $product, $upgrade );
				$this->mClean ['basket']->AddToBasket ( $sku, false, $upgradePrice, true );
				$this->mClean ['basket']->ChangePriceForSku ( $sku, $upgradePrice, false, true );
			} else {
				$this->mClean ['basket']->AddToBasket ( $sku, true, 0.0, false );
				$this->mClean ['basket']->ChangePriceForSku ( $sku, '0.0', true, false );
			}
		}
		
		// Add the package to the basket
		$this->mClean ['basket']->AddPackageToBasket ( $this->mClean ['package'] );
		
		// Redirect the user, depending on where they came from
		$registry = Registry::getInstance ();
		$baseDir = $registry->baseDir;
		switch ($this->mClean ['referPage']) {
			case 'packageDetailView' :
				$redirectTo = $baseDir . '/packages/' . $this->mValidationHelper->MakeLinkSafe ( $this->mClean ['category']->GetDisplayName () ) . '/package/' . $this->mValidationHelper->MakeLinkSafe ( $this->mClean ['package']->GetDisplayName () ) . '/' . $this->mClean ['package']->GetPackageId ();
				break;
			case 'dealOfTheWeek' :
				$redirectTo = $baseDir . '/index.php';
				break;
			case 'categoryListProductView' :
				$redirectTo = $baseDir . '/department/' . $this->mValidationHelper->MakeLinkSafe ( $this->mClean ['category']->GetDisplayName () ) . '/' . $this->mClean ['category']->GetCategoryId ();
				break;
		}
		// Redirect the user back to the correct place
		header ( 'Location: ' . $redirectTo );
	} // End AddToBasket
} // End class


try {
	$handler = new AddPackageToBasketHandler ( );
	$handler->Validate ( $_POST );
	$handler->AddToBasket ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>