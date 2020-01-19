<?php
require_once('../autoload.php');

//! Form handler to add a product to a basket
class AddProductToBasketHandler {
	
	//! Cleaned (validated) array of posted variables
	var $mClean;
	
	//! Constructor, initialises validation and session helper
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mSessionHelper = new SessionHelper ( );
		$this->mErrorDoc = 'AddProductToBasketHandlerLog.txt';
	}
	
	//! Validation function - takes the $_POST array and processes it. By the end of this function call there will be a member mClean variable with the following structure:
	/*
		$this->mClean	['product']  : Obj : ProductModel	- The product to add to the basket
						['category'] : Obj : CategoryModel	- The category the product is in
						['basket']	 : Obj : BasketModel 	- The basket to add the product to
						['referPage']: Str - The page that referred the basket
						['parentCategory'] 		: Obj : CategoryModel - 
						['multibuyQuantity']	: Int - The quantity of the product being ordered (1 if multibuy isn't enabled)
						['basketSkus'][]	: Obj : SkuModel - A collection of SKUs to add to the basket (Eg. flavours/sizes)
	 */
	/*! 
	 * @param $postArr - The $_POST array, expects $_POST['productId'],['basketId'],['referPage'], optional - ['categoryId'],['parentCategoryId']
	 */
	function Validate($postArr) {
		$this->mClean ['product'] = new ProductModel ( $postArr ['productId'] );
		if (isset ( $postArr ['categoryId'] )) {
			$this->mClean ['category'] = new CategoryModel ( $postArr ['categoryId'] );
		}
		$this->mClean ['basket'] = new BasketModel ( $postArr ['basketId'] );
		$this->mClean ['referPage'] = $postArr ['referPage'];
		if (isset ( $postArr ['parentCategoryId'] )) {
			$this->mClean ['parentCategory'] = new CategoryModel ( $postArr ['parentCategoryId'] );
		}
		
		// Multibuy
		if ($this->mClean ['product']->GetMultibuy ()) {
			$this->mClean ['multibuyQuantity'] = $postArr ['multibuyQuantity'];
		} else {
			$this->mClean ['multibuyQuantity'] = 1;
		}
		
		// Options
		$allProductAttributes = $this->mClean ['product']->GetAttributes ();
		if (count ( $allProductAttributes ) > 0) {
			foreach ( $allProductAttributes as $attribute ) {
				$this->mClean ['skuAttribute' . $attribute->GetProductAttributeId ()] = $postArr ['skuAttribute' . $attribute->GetProductAttributeId ()];
			}
			$this->AddSkusToBasketForProduct ( $this->mClean ['product'] );
		} else {
			$allSkus = $this->mClean ['product']->GetSkus ();
			for($i = 0; $i < $this->mClean ['multibuyQuantity']; $i ++) {
				$this->mClean ['basketSkus'] [] = $allSkus [0]->GetSkuId ();
			}
		}
	} // End Validate
	

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
			// If multibuy, then add X to the basket, otherwise dont
			if ($this->mClean ['product']->GetMultibuy ()) {
				$skuId = array_pop ( $currentArr );
				for($i = 0; $i < $this->mClean ['multibuyQuantity']; $i ++) {
					$this->mClean ['basketSkus'] [] = $skuId;
				}
			} else {
				$this->mClean ['basketSkus'] [] = array_pop ( $currentArr );
			}
		} else {
			$sku = $this->AlternateSkuCombination ( $currentValueArr, $product );
			if ($sku) {
				$this->mClean ['basketSkus'] [] = $sku->GetSkuId ();
			} else {
				$skuError = 'Failed to add product: ' . $this->mClean ['product']->GetDisplayName () . ' to basket at ' . date ( 'r' ) . ',';
				$this->LogError ( $skuError );
			}
		}
	} // End AddSkusToBasketForProduct()
	

	//! Logs the error message to the file for this file
	function LogError($errorMsg) {
		$fh = @fopen ( $this->mErrorDoc, 'a' );
		@fwrite ( $fh, $skuError );
		@fclose ( $fh );
	} // End LogError
	

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
		// Add each SKU to the basket, at their displayed price
		foreach ( $this->mClean ['basketSkus'] as $skuId ) {
			$sku = new SkuModel ( $skuId );
			// If the SKU is being added from the basket page then it is a freebie!
			if($this->mClean ['referPage'] == 'basket') {
				$this->mClean['basket']->SetFreeOfferApplied(1);
				$this->mClean ['basket']->AddToBasket ( $sku, false, 0.0, false );				
			} else {
				$this->mClean ['basket']->AddToBasket ( $sku, false, $sku->GetSkuPrice (), false );
			}
		}
		
		// Depending on where the visitor came from, send them back to the correct page
		$registry = Registry::getInstance ();
		$baseDir = $registry->baseDir;
		switch ($this->mClean ['referPage']) {
			case 'productDetailView' :
				if (isset ( $this->Clean ['parentCategory'] )) {
					$redirectTo = $baseDir . '/department/' . $this->mValidationHelper->MakeLinkSafe ( $this->mClean ['category']->GetDisplayName () ) . '/' . $this->mValidationHelper->MakeLinkSafe ( $this->mClean ['parentCategory']->GetDisplayName () ) . '/product/' . $this->mValidationHelper->MakeLinkSafe ( $this->mClean ['product']->GetDisplayName () ) . '/' . $this->mClean ['product']->GetProductId ();
				} else {
					if ($this->mClean ['category']->GetParentCategory ()) {
						$redirectTo = $baseDir . '/department/' . $this->mValidationHelper->MakeLinkSafe ( $this->mClean ['category']->GetDisplayName () ) . '/' . $this->mValidationHelper->MakeLinkSafe ( $this->mClean ['category']->GetParentCategory ()->GetDisplayName () ) . '/product/' . $this->mValidationHelper->MakeLinkSafe ( $this->mClean ['product']->GetDisplayName () ) . '/' . $this->mClean ['product']->GetProductId ();
					} else {
						$redirectTo = $baseDir . '/department/' . $this->mValidationHelper->MakeLinkSafe ( $this->mClean ['category']->GetDisplayName () ) . '/product/' . $this->mValidationHelper->MakeLinkSafe ( $this->mClean ['product']->GetDisplayName () ) . '/' . $this->mClean ['product']->GetProductId ();
					}
				}
				break;
			case 'dealOfTheWeek' :
				$redirectTo = $baseDir . '/index.php';
				break;
			case 'basket' :
				$redirectTo = $baseDir . '/basket.php';
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
	$handler = new AddProductToBasketHandler ( );
	$handler->Validate ( $_POST );
	$handler->AddToBasket ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>