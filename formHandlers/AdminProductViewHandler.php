<?php

/*foreach($_POST as $key=>$value) {
	echo $key.' : '.$value.'<br />';
}die();*/

require_once ('../autoload.php');

class AdminProductHandler extends Handler {

	var $mProduct;
	var $mClean;

	function ValidateInput($submittedArray,$product) {
		$this->mProduct = $product;
		foreach($submittedArray as $key=>$value) {
			switch ($key) {
				case 'displayName' :
				case 'description' :
				case 'sageCode' :
				case 'skuQuantity':
					$this->mClean[$key] = $value;
					break;
				case 'longDescription' :
				case 'echoDescription':
					$this->mClean[$key] = $this->mValidationHelper->RemoveWhitespace($value);
					break;
				case 'actualPrice' :
				case 'upgradePrice' :
				case 'wasPrice' :
				case 'weight' :
				case 'postage' :
					if (! $this->mValidationHelper->IsNumeric ( $value )) {
						$error = new Error ( 'Validation failed because the ' . $key . ' must be numeric.' );
						throw new Exception ( $error->GetErrorMsg () );
					}
					$this->mClean[$key] = $this->mValidationHelper->MakeSafe ( $value );
					break;
				case 'manufacturer' :
					$this->mClean[$key] = $value;
					break;
				default :
					$existingSkuTest = explode ( "SKU", $key );
					if ($this->IsCorrectKey ( $existingSkuTest )) {
						if (strpos ( $existingSkuTest [1], "PRICE" )) {
							// SKU Price
							if (! $this->mValidationHelper->IsNumeric ( $value )) {
								$error = new Error ( 'Validation failed because the ' . $key . ' must be numeric.' );
								throw new Exception ( $error->GetErrorMsg () );
							} else {
								$this->mClean[$key] = $value;
							}
						} elseif (strpos ( $existingSkuTest [1], "SAGECODE" )) {
							// SKU Sage Code
							$this->mClean[$key] = $this->mValidationHelper->MakeSafe ( $value );
						} elseif (strpos ( $existingSkuTest[1], "QTY")) {
							// SKU Quantity
							$this->mClean[$key] = $this->mValidationHelper->MakeSafe($value);
						} else {
							// A SKU Attribute value
							$this->mClean[$key] = $this->mValidationHelper->MakeSafe ( $value );
						}
					}
					$this->mClean[$key] = $submittedArray [$key];
					break;
			}
		}
	}

	//! Returns whether a key in an encoded field scan is correct, working under the assumption that if there are 2 values in the test array then the key is correct
	/*!
	 * Eg. Field has ID of SKU1393560PRODUCTATTRIBUTE104657 - where 104657 is the product attribute ID in the database, so doing an
	 *     explode('PRODUCTATTRIBUTE','SKU1393560PRODUCTATTRIBUTE104657'); will yield $arr[0] = 'SKU1393560' $arr[1] = '104657' (the ID)
	 *     If the field DIDN'T have PRODUCTATTRIBUTE in its ID then the array would be $arr[0] = 'ORIGINAL STRING' and this function would return false
	 */
	function IsCorrectKey($test) {
		if(2 == count($test)) {
			return true;
		} else {
			return false;
		}
	}

	function DeleteProduct($product) {
		$productController = new ProductController ( );
		#if ($productController->IsSafeToDelete ( $product )) {
			$productController->DeleteProduct ( $product );
			return true;
		#} else {
		#	return false;
		#}
	}

	// ValidateInput MUST be called before this!
	function SaveProduct() {

		$productController = new ProductController ( );
		$i = 0;
		$newSkuArray = array ();

		// Text
		$this->mProduct->SetDisplayName ( $this->mClean ['displayName'] );
		$this->mProduct->SetDescription ( $this->mClean ['description'] );
		$this->mProduct->SetLongDescription ( $this->mClean ['longDescription'] );
		$this->mProduct->SetEchoDescription ( $this->mClean ['echoDescription'] );
		$this->mProduct->SetActualPrice ( $this->mClean ['actualPrice'] );
		$this->mProduct->SetUpgradePrice ( $this->mClean ['upgradePrice'] );
		$this->mProduct->SetWasPrice ( $this->mClean ['wasPrice'] );
		$this->mProduct->SetPostage ( $this->mClean ['postage'] );
		$this->mProduct->SetWeight ( $this->mClean ['weight'] );

		// Select
		$taxCode = new TaxCodeModel ( $this->mClean ['taxCode'] );
		$this->mProduct->SetTaxCode ( $taxCode );
		if (isset ( $this->mClean ['manufacturer'] )) {
			$manufacturer = new ManufacturerModel ( $this->mClean ['manufacturer'] );
			$this->mProduct->SetManufacturer ( $manufacturer );
		}

		// Checkbox
		(isset ( $this->mClean ['forSale'] ) ? $this->mProduct->SetForSale ( 1 ) : $this->mProduct->SetForSale ( 0 ));
		(isset ( $this->mClean ['inStock'] ) ? $this->mProduct->SetInStock ( 1 ) : $this->mProduct->SetInStock ( 0 ));
		(isset ( $this->mClean ['onSale'] ) ? $this->mProduct->SetOnSale ( 1 ) : $this->mProduct->SetOnSale ( 0 ));
		(isset ( $this->mClean ['offerOfWeek'] ) ? $this->mProduct->SetOfferOfWeek ( 1 ) : $this->mProduct->SetOfferOfWeek ( 0 ));
		(isset ( $this->mClean ['onClearance'] ) ? $this->mProduct->SetOnClearance ( 1 ) : $this->mProduct->SetOnClearance ( 0 ));
		(isset ( $this->mClean ['featured'] ) ? $this->mProduct->SetFeatured ( 1 ) : $this->mProduct->SetFeatured ( 0 ));
		(isset ( $this->mClean ['hidden'] ) ? $this->mProduct->SetHidden ( 1 ) : $this->mProduct->SetHidden ( 0 ));
		(isset ( $this->mClean ['multibuy'] ) ? $this->mProduct->SetMultibuy ( 1 ) : $this->mProduct->SetMultibuy ( 0 ));

		$allFormSkus = array ();
		// Basic SKU
		$skus = $this->mProduct->GetSkus ();
		if (1 == count ( $skus )) {
			$skus [0]->SetSageCode 	( $this->mClean ['sageCode'] );
			$skus [0]->SetSkuPrice 	( $this->mClean ['actualPrice'] );
			$skus [0]->SetQty 		( $this->mClean ['skuQuantity'] );
			$allFormSkus [] = $skus [0]->GetSkuId ();
		}

		// Any that have to be matched by their IDs (non-static)
		foreach ( $this->mClean as $key => $value ) {

			// Weight in other categories
			if ($categoryToSetWeight = substr ( strstr ( $key, 'WEIGHTIN' ), 8 )) {
				if ('THIS' != $categoryToSetWeight) {
					// Set the weight of every product in this category
					$categoryController = new CategoryController ( );
					$category = new CategoryModel ( $categoryToSetWeight );
					$allProducts = $categoryController->GetAllProductsIn ( $category );
					foreach ( $allProducts as $product ) {
						$product->SetWeight ( $this->mClean ['weight'] );
					}
				}
			}

			// Tags - Add
			$tagArr = explode("shopByTag",$key);
			#echo '<pre>'; var_dump($tagArr); echo '</pre>';
			if($this->IsCorrectKey($tagArr)) {
				$tag = new TagModel($tagArr[1]);
				$tagController = new TagController();
				$tagController->CreateProductTagLink($this->mProduct,$tag);
			}

			// Upgrades - Add
			$upgradeArr = explode ( "UPGRADE", $key );
			if ($this->IsCorrectKey ( $upgradeArr ) && $upgradeArr [1] [0] != 'C') {
				$upgrade = new ProductModel ( $upgradeArr [1] );
				$productController = new ProductController ( );
				$productController->CreateUpgradeLink ( $this->mProduct, $upgrade );
			}

			// Related - Add
			$relatedArr = explode ( "RELATED", $key );
			if ($this->IsCorrectKey ( $relatedArr ) && $relatedArr [1] [0] != 'C') {
				$related = new ProductModel ( $relatedArr [1] );
				$productController = new ProductController ( );
				$productController->CreateRelatedLink ( $this->mProduct, $related );
			}

			// Similar - Add
			$similarArr = explode ( "SIMILAR", $key );
			if ($this->IsCorrectKey ( $similarArr ) && $similarArr [1] [0] != 'C') {
				$similar = new ProductModel ( $similarArr [1] );
				$productController = new ProductController ( );
				$productController->CreateSimilarLink ( $this->mProduct, $similar );
			}

			// Categories - Add (Top Level)
			$categoriesArr = explode ( "CATEGORYtopLevelCheckbox", $key );
			if ($this->IsCorrectKey ( $categoriesArr )) {
				$currentCategories = $this->mProduct->GetCategories ();
				$category = new CategoryModel ( $categoriesArr [1] );
				$productController = new ProductController ( );
				if (! in_array ( $category, $currentCategories )) {
					$productController->CreateCategoryLink ( $this->mProduct, $category );
				}
			}

			// Categories - Add (Sub Level)
			$categoriesArr = explode ( "CATEGORYCHECK", $key );
			if ($this->IsCorrectKey ( $categoriesArr )) {
				$currentCategories = $this->mProduct->GetCategories ();
				$category = new CategoryModel ( $categoriesArr [1] );
				$productController = new ProductController ( );
				if (! in_array ( $category, $currentCategories )) {
					$productController->CreateCategoryLink ( $this->mProduct, $category );
				}
			}

			// Product Attribute - Edit
			$attrArr = explode ( "PRODUCTATTRIBUTEEDIThidden", $key );
			if ($this->IsCorrectKey ( $attrArr )) {
				$prodAtt = new ProductAttributeModel ( $attrArr [1] );
				$prodAtt->SetAttributeName ( $value );
			}

			// Images - Edit only (Add is done seperately)
			$imageController = new ImageController ( );
			// Alt Text
			$altTextArr = explode ( "AltText", $key );
			if ($this->IsCorrectKey ( $altTextArr )) {
				$image = new ImageModel ( $altTextArr [1] );
				$image->SetAltText($this->mValidationHelper->MakeLinkSafe($value,true,false));
			}
			// Main Image
			$mainImageArr = explode ( "MainImage", $key );
			if ($this->IsCorrectKey ( $mainImageArr )) {
				$image = new ImageModel ( $value );
				$currentMainImage = $imageController->GetMainImageFor ( $this->mProduct );
				if ($currentMainImage) {
					if ($image != $currentMainImage) {
						$currentMainImage->SetMainImage ( 0 );
					}
				}
				$image->SetMainImage ( 1 );
			}
			// Delete Image
			$deleteImageArr = explode ( "DeleteImage", $key );
			if ($this->IsCorrectKey ( $deleteImageArr )) {
				$image = new ImageModel ( $deleteImageArr [1] );
				$productController->RemoveImageLink ( $this->mProduct, $image );
			}

			// Multibuy - Add
			$multibuyArr = explode ( "QuantityInput", $key );
			if ($this->IsCorrectKey ( $multibuyArr )) {
				$quantity = $this->mClean [$multibuyArr [0] . 'QuantityInput' . $multibuyArr [1]];
				$unitPrice = $this->mClean [$multibuyArr [0] . 'UnitPriceInput' . $multibuyArr [1]];
				if ($this->mProduct->IsSafeToAddMultibuy ( $quantity )) {
					$this->mProduct->InsertMultibuy ( $quantity, $unitPrice );
				} else {
					$this->mProduct->AmendMultibuy ( $quantity, $unitPrice );
				}
			}

			// Options
			// Look for strings with SKU in - either current or new SKUs
			// New SKU Profile: NEWSKU-XXXX-PRODUCTATTRIBUTE-YYYY
			$skuArr = explode ( "SKU", $key );
			// $skuArr[0] = 'NEW'
			// $skuArr[1] = '-XXXX-PRODUCTATTRIBUTE-YYYY'
			// Ignore any POST values without SKU in them
			if ($this->IsCorrectKey ( $skuArr )) {
				$nextLevel = explode ( "PRODUCTATTRIBUTE", $skuArr [1] );
				// $nextLevel[0] = -XXXX-
				// $nextLevel[1] = -YYYY-
				if (! strpos ( $nextLevel [0], 'PRICE' ) && ! strpos ( $nextLevel [0], 'SAGECODE' )) {
					$allFormSkus [] = $nextLevel [0];
				}
				if ('NEW' != $skuArr [0]) {
					// Current SKU
					$productAttributeArr = explode ( "PRODUCTATTRIBUTE", $skuArr [1] );
					// This will only be set if looking at an SKU Attribute value field (eg. blue/large...)
					if (isset ( $productAttributeArr [1] )) {
						$skuId = $productAttributeArr [0];
						$sku = new SkuModel ( $skuId );
						$productAttributeId = $productAttributeArr [1];
						// Process SKU Attribute changes
						$productAttribute = new ProductAttributeModel ( $productAttributeId );
						$skuAttributeController = new SkuAttributeController ( );
						$skuAttributeModel = $skuAttributeController->GetSkuAttributeFor ( $sku, $productAttribute );
						$skuAttributeModel->SetAttributeValue ( $value );

					} else {
						#var_dump($productAttributeArr [0]); echo '<br />';
						// SKU Price
						$priceCheck = explode ( "PRICE", $productAttributeArr [0] );
						if($this->IsCorrectKey($priceCheck)) {
							// Set Price
							$skuId = $priceCheck[0];
							$sku = new SkuModel($skuId);
							$sku->SetSkuPrice($value);
						}
						// SKU Sage Code
						$sageCodeCheck = explode ( "SAGECODE", $productAttributeArr [0] );
						if($this->IsCorrectKey($sageCodeCheck)) {
							// Set Sage Code
							$skuId = $sageCodeCheck[0];
							$sku = new SkuModel($skuId);
							$sku->SetSageCode($value);
						}
						// SKU Quantity
						$qtyCheck = explode ( "QTY", $productAttributeArr [0] );
						if($this->IsCorrectKey($qtyCheck)) {
							// Set Quantity
							$skuId = $qtyCheck[0];
							$sku = new SkuModel($skuId);
							$sku->SetQty($value);
						}
						/*// Because of the way explode() works, if there is NO match then the first element of the return array will be the same as the haystack
						if ($priceCheck [0] == $productAttributeArr [0]) {
							// Similarly for sage code
							$sageCheck = explode ( "SAGECODE", $productAttributeArr [0] );
							// Opposite of above check - don't do anything (no else) if neither match!
							if ($sageCheck [0] != $productAttributeArr [0]) {
								// Process sage code
								$skuId = $sageCheck [0];
								$sku = new SkuModel ( $skuId );
								$sku->SetSageCode ( $value );
							}
						} else {
							// Process Price
							$skuId = $priceCheck [0];
							$sku = new SkuModel ( $skuId );
							$sku->SetSkuPrice ( $value );
						}*/
					}

				} else {
					// New SKU
					$skuAttributeController = new SkuAttributeController ( );
					$productController = new ProductController ( );
					$skuController = new SkuController ( );
					$productAttributeArr = explode ( "PRODUCTATTRIBUTE", $skuArr [1] );

					// The strlen check here was introduced because price/sagecode will have at LEAST 5 characters, while a user is unlikey to add over 999 attributes at a time
					if (! in_array ( $productAttributeArr [0], $newSkuArray ) && strlen ( $productAttributeArr [0] ) < 4) {
						$newSku = $skuController->CreateSku ();
						$productController->CreateSkuLink ( $newSku, $this->mProduct );
						$newSkuArray [] = $productAttributeArr [0];

						$productAttribute = new ProductAttributeModel ( $productAttributeArr [1] );
						$newSkuAttribute = $skuAttributeController->CreateSkuAttribute ( $newSku, $productAttribute );
						$newSkuAttribute->SetAttributeValue ( $value );
					} else {
						if ($productAttributeArr [0] != $skuArr [1]) {
							// Is a product attribute
							$productAttribute = new ProductAttributeModel ( $productAttributeArr [1] );
							$newSkuAttribute = $skuAttributeController->CreateSkuAttribute ( $newSku, $productAttribute );
							$newSkuAttribute->SetAttributeValue ( $value );
						} else {
							// Is sage code...
							$sageCheck = explode ( "SAGECODE", $productAttributeArr [0] );
							if ($sageCheck [0] != $productAttributeArr [0]) {
								$newSku->SetSageCode ( $value );
							}
							// ... or price ...
							$priceCheck = explode ( "PRICE", $productAttributeArr [0] );
							if ($priceCheck [0] != $productAttributeArr [0]) {
								$newSku->SetSkuPrice ( $value );
							}
							// ... or quantity
							$qtyCheck = explode ( "QTY", $productAttributeArr [0] );
							if ($qtyCheck [0] != $productAttributeArr [0]) {
								$newSku->SetQty ( $value );
							}
						}
					}
				}
			}
			$i ++;

		} // End foreach

		// SKUS - Remove. If a SKU is in the DB but not in the form, then delete it.
		$skus = $this->mProduct->GetSkus ();
		$skuController = new SkuController ( );
		foreach ( $skus as $sku ) {
			if (! in_array ( $sku->GetSkuId (), $allFormSkus ) && ! $sku->IsFinalSku ()) {
				$skuController->DeleteSku ( $sku );
			}
		}

		// Multibuy Remove
		$allMultibuy = $this->mProduct->GetMultibuyDetails ();
		foreach ( $allMultibuy as $multibuy ) {
			if (! in_array ( $multibuy ['quantity'], $this->mClean )) {
				// There is no value in the array .. could there be another field with same number...
				$this->mProduct->RemoveMultibuy ( $multibuy ['quantity'] );
			}
		}

		// Upgrades - Remove
		$allUpgrades = $this->mProduct->GetUpgrades ();
		foreach ( $allUpgrades as $upgrade ) {
			if (! in_array ( 'UPGRADE' . $upgrade->GetProductId (), $this->mClean )) {
				$productController = new ProductController ( );
				$productController->RemoveUpgradeLink ( $this->mProduct, $upgrade );
			}
		}

		// Tags - Remove - NOTE: This works because a checkbox ISN'T submitted unless it is checked.
		$allTags = $this->mProduct->GetTags();
		foreach($allTags as $tag) {
			if(!in_array('shopByTag'.$tag->GetTagId(),array_keys($this->mClean))) {
				$tagController = new TagController();
				$tagController->RemoveProductTagLink($this->mProduct,$tag);
			}
		}

		// Related - Remove
		$allRelated = $this->mProduct->GetRelated ();
		foreach ( $allRelated as $related ) {
			if (! in_array ( 'RELATED' . $related->GetProductId (), $this->mClean )) {
				$productController = new ProductController ( );
				$productController->RemoveRelatedLink ( $this->mProduct, $related );
			}
		}

		// Similar - Remove
		$allSimilar = $this->mProduct->GetSimilar ();
		foreach ( $allSimilar as $similar ) {
			if (! in_array ( 'SIMILAR' . $similar->GetProductId (), $this->mClean )) {
				$productController = new ProductController ( );
				$productController->RemoveSimilarLink ( $this->mProduct, $similar );
			}
		}

		// Categories - Remove
		$allCategories = $this->mProduct->GetCategories ();
		foreach ( $allCategories as $category ) {
			if (! in_array ( 'CATEGORY' . $category->GetCategoryId (), $this->mClean )) {
				$productController = new ProductController ( );
				$productController->RemoveCategoryLink ( $this->mProduct, $category );
			}
		}

		// Product Attribute -  Remove
		$allAttributes = $this->mProduct->GetAttributes ();
		foreach ( $allAttributes as $productAttribute ) {
			$deleteStr = 'DELETEPRODUCTATTRIBUTEEDIT' . $productAttribute->GetProductAttributeId ();
			if (in_array ( $deleteStr, $_POST )) {
				// Delete this product attribute
				$productAttributeController = new ProductAttributeController ( );
				$productAttributeController->DeleteProductAttribute ( $productAttribute );
			}
		}

	} // End function
} // End class


try {
	$product = new ProductModel ( $_POST ['productId'] );
	$handler = new AdminProductHandler ( );
	if (isset ( $_POST ['saveProduct'] )) {
		$registry = Registry::getInstance ();
		$handler->ValidateInput ( $_POST, $product );
		$handler->SaveProduct ();
		echo '<h1 style="font-family: Arial; font-size: 12pt;">' . $product->GetDisplayName () . ' Saved.</h1>';
		echo '<script language="javascript" type="text/javascript">
				self.location.href=\'' . $registry->viewDir . '/AdminProductView.php?id=' . $_POST ['productId'] . '\'
		</script>';
	} elseif (isset ( $_POST ['deleteProductInput'] )) {
		$name = $product->GetDisplayName ();
		if ($handler->DeleteProduct ( $product )) {
			echo '<h1 style="font-family: Arial; font-size: 12pt;">' . $name . ' Deleted.</h1>';
		} else {
			echo '<h1 style="font-family: Arial; font-size: 12pt;">' . $name . ' could not be deleted - either it is in an authorised order, or hasn\'t been downloaded to Sage yet.</h1>';
			$orderController = new OrderController ( );
			$registry = Registry::getInstance ();
			$orders = $orderController->GetAuthorisedOrdersWithProduct ( $product );
			echo '<div style="font-family: Arial; font-size: 10pt;">Authorised Orders with ' . $product->GetDisplayName () . ' in them:<br /><ul>';
			foreach ( $orders as $order ) {
				echo '<li><a href="' . $registry->adminDir . '/orders/' . $order->GetOrderId () . '" target="_top" style="text-decoration: none; color: #000;">ECHO' . $order->GetOrderId () . '</a></li>';
			}
			echo '</ul></div>';
		}
	} else {
		var_dump ( $_POST );
		die ( '<strong>Something has gone seriously wrong!</strong>' );
	}
} catch ( Exception $e ) {
	echo $e->GetMessage ();
}

?>