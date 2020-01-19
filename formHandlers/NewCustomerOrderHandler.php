<?php

/*foreach ( $_POST as $key => $value ) {
	echo '<strong>' . $key . ':</strong> ' . $value . '<br />';
}*/
#die ();
require_once ('../autoload.php');
//! Processes an order
class NewCustomerOrderHandler {
	
	//! A "cleaned" array with only safe values in it
	var $mClean;
	var $mBasket;
	var $mOrder;
	var $mBasketController;
	
	//! Initialises the basket
	function __construct($sessionId) {
		$this->mRegistry = Registry::getInstance ();
		#($this->mRegistry->debugMode ? $this->mFh = fopen ( '../' . $this->mRegistry->debugDir . '/adminOrderLog.txt', 'w+' ) : NULL);
		$this->mBasket = new BasketModel ( $sessionId );
	}
	
	//! Validates the input and cleans it into the mClean array	
	function Validate($postArr) {
		$validator = new ValidationHelper ( );
		foreach ( $postArr as $key => $value ) {
			switch ($key) {
				case 'customerName' :
				case 'delivery1' :
				case 'delivery2' :
				case 'delivery3' :
				case 'county' :
				case 'deliveryPostcode' :
				case 'billing1' :
				case 'billingPostcode' :
				case 'orderPostageTotal' :
				case 'orderTotalPrice' :
				case 'cardType' :
				case 'cardNumber' :
				case 'validFromYear' :
				case 'validFromMonth' :
				case 'expiryDateMonth' :
				case 'expiryDateYear' :
				case 'issueNumber' :
				case 'telephoneNumber' :
				case 'emailAddress' :
				case 'brochure' :
				case 'staffName' :
				case 'notes' :
				case 'staffNotes' :
					#case 'orderAdjustmentTotal':				
					$this->mClean [$key] = $validator->MakeSafe ( $value );
					break;
				case 'cardVerificationNumber' :
					if (strlen ( trim ( $value ) ) != 3) {
						throw new Exception ( 'CVN must have 3 characters: ' . $value . '.' );
					} else {
						$this->mClean [$key] = $validator->MakeSafe ( $value );
					}
					break;
				default :
					if (strpos ( $key, 'productName' )) {
						$this->mClean ['products'] [$this->ExtractProductId ( $key )] = new ProductModel ( $this->ExtractProductId ( $key ) );
					} elseif (strpos ( $key, 'productQuantity' )) {
						$this->SetProductQuantity ( $key, $value );
					} elseif (strpos ( $key, 'packageName' )) {
						$this->mClean ['packages'] [$this->ExtractPackageId ( $key )] = new PackageModel ( $this->ExtractPackageId ( $key ) );
					} elseif (strpos ( $key, 'packageQuantity' )) {
						$this->SetPackageQuantity ( $key, $value );
					} elseif (strpos ( $key, 'productPrice' )) {
						$this->SetProductPrice ( $key, $value );
					} else {
						$this->mClean [$key] = $value;
					}
					
					break;
			} // End switch
		} // End foreach
		

		// See if product SKUS are OK
		if (isset ( $this->mClean ['products'] )) {
			foreach ( $this->mClean ['products'] as $product ) {
				if ($product->HasNoAttributes ()) {
					$quantity = $this->mClean ['quantity'] [$product->GetProductId ()];
					for($i = 0; $i < $quantity; $i ++) {
						$skus = $product->GetSkus ();
						$this->mClean ['basketSkus'] [] = $skus [0]->GetSkuId ();
						#($this->mRegistry->debugMode ? fwrite ( $this->mFh, "Adding sku " . $skus [0]->GetSkuId () . " to array line " . __LINE__ . ". \r\n" ) : NULL);
					}
				} else {
					$this->AddSkusToBasketForProduct ( $product );
				}
			}
		}
		
		// See if package SKUS are OK
		if (isset ( $this->mClean ['packages'] )) {
			foreach ( $this->mClean ['packages'] as $package ) {
				// How many of these packages have been added?
				for($packageCounter = 1; $packageCounter < ($this->mClean ['packageQuantity'] [$package->GetPackageId ()] + 1); $packageCounter ++) {
					// Needs to check upgrades etc...
					$contents = $package->GetContents ();
					foreach ( $contents as $product ) {
						// check if upgraded and add the upgrade instead
						$upgradeLock = false;
						foreach ( $package->GetUpgradesFor ( $product ) as $upgrade ) {
							if (isset ( $postArr ['packageContentUgradeCheckbox' . $package->GetPackageId () . 'packageId' . $packageCounter . 'packageCounter' . $product->GetProductId () . 'productId' . $upgrade->GetProductId () . 'upgradeId'] )) {
								#($this->mRegistry->debugMode ? fwrite ( $this->mFh, "Adding upgrade " . $upgrade->GetProductId () . " to basket line " . __LINE__ . ". \r\n" ) : NULL);
								$this->AddSkusToBasketForPackageProduct ( $package, $upgrade, $packageCounter );
								$upgradeLock = true;
							} // end if
						} // end foreach
						// If the upgrade HASNT been added, add the original product
						if (! $upgradeLock) {
							$this->AddSkusToBasketForPackageProduct ( $package, $product, $packageCounter );
							#($this->mRegistry->debugMode ? fwrite ( $this->mFh, "Adding package product " . $product->GetDisplayName () . " to basket line " . __LINE__ . ". \r\n" ) : NULL);
						}
					}
				}
			}
		}
		
		$this->CheckValidFromDate ();
		$this->CheckExpiryDate ();
	} // End Validate()
	

	//! Adds the correct SKU to the basket for the $product in $package (black magic.. see comments for real explanation)
	/* Does this by looking for a field in the submitted form with the correct ID combination. Makes sure it gets the correct SKU and doesnt add the item if there isnt a correct combination.
	For example if the user picked Red/Large and this DID exist then the correct SKU is added, if it doesn't then no SKU is added, but no error is given 
	(adds to an intrernal $problemSkus array which is currently ignored) */
	// Specifically, looks for: packageContentAttributePACK_IDpackageIdPACK_COUNTERpackageCounterPROD_IDproductIdATT_IDattributeId combination
	/*!
	 * @param $package [in] Obj:PackageModel - The package to look in
	 * @param $product [in] Obk:ProductModel - The product to add
	 * @return Void
	 */
	function AddSkusToBasketForPackageProduct($package, $product, $counter) {
		$productAttributes = $product->GetAttributes ();
		// If there are no attributes then just add the product
		if (count ( $productAttributes ) == 0) {
			$skus = $product->GetSkus ();
			$this->mClean ['basketSkus'] [] = $skus [0]->GetSkuId ();
			#($this->mRegistry->debugMode ? fwrite ( $this->mFh, "Adding sku " . $skus [0]->GetSkuId () . " to array line " . __LINE__ . ". \r\n" ) : NULL);
		} else {
			$currentArr = array ();
			$skuAttributeIdArr = array ();
			// Loop over each product attribute and
			foreach ( $productAttributes as $attribute ) {
				// Get SKU Attribute ID
				$skuAttributeId = $this->mClean ['packageContentAttribute' . $package->GetPackageId () . 'packageId' . $counter . 'packageCounter' . $product->GetProductId () . 'productId' . $attribute->GetProductAttributeId () . 'attributeId'];
				// Make an SKU attribute model (To get the SKU ID)
				$skuAttribute = new SkuAttributeModel ( $skuAttributeId );
				// Add the SKU ID to the $currentArr
				$currentArr [] = $skuAttribute->GetSkuId ();
				// Add the SKU Attribute ID to an array (in case the SKU isn't set)
				$skuAttributeIdArr [] = $skuAttributeId;
			}
			// If all entries are the same then add to basketSkus
			if ($this->CheckAllEntriesTheSame ( $currentArr )) {
				$skuId = array_pop ( $currentArr );
				$this->mClean ['basketSkus'] [] = $skuId;
				#($this->mRegistry->debugMode ? fwrite ( $this->mFh, "Adding sku " . $skuId . " to array line " . __LINE__ . ". \r\n" ) : NULL);
			} else {
				$skuError = $this->ConstructSkuErrorMessage ( $skuAttributeIdArr );
				$this->mClean ['problemSkus'] [] = $skuError;
			}
		}
	} // End AddSkusToBasketForProduct()	
	

	//! Adds the correct SKU to the basket for the $product (see NewCustomerOrderHandler::AddSkusToBasketForPackageProduct for explanation on inner workings)
	/*!
	 * @param $product [in] Obk:ProductModel - The product to add
	 * @return Void
	 */
	function AddSkusToBasketForProduct($product) {
		$quantity = $this->mClean ['quantity'] [$product->GetProductId ()];
		// Loop QUANTITY times for each product
		for($i = 0; $i < $quantity; $i ++) {
			$productAttributes = $product->GetAttributes ();
			$currentArr = array ();
			$currentValueArr = array ();
			$skuAttributeIdArr = array ();
			// Loop over each product attribute and
			foreach ( $productAttributes as $attribute ) {
				// Get SKU Attribute ID
				$skuAttributeId = $this->mClean ['productAttribute' . ($i + 1) . 'productCounter' . $product->GetProductId () . 'productId' . $attribute->GetProductAttributeId () . 'attributeId'];
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
				#($this->mRegistry->debugMode ? fwrite ( $this->mFh, "Adding product " . $product->GetDisplayName () . " to basket line " . __LINE__ . ". \r\n" ) : NULL);
				$skuId = array_pop ( $currentArr );
				$this->mClean ['basketSkus'] [] = $skuId;
				#($this->mRegistry->debugMode ? fwrite ( $this->mFh, "Adding sku " . $skuId . " to array line " . __LINE__ . ". \r\n" ) : NULL);
			} else {
				// If there is an alternate but equivalent combination then use that (occurs because only 1 'black' would be displayed if every size came in black)
				$sku = $this->AlternateSkuCombination ( $currentValueArr, $product );
				if ($sku) {
					#($this->mRegistry->debugMode ? fwrite ( $this->mFh, "Adding product " . $product->GetDisplayName () . " to array line " . __LINE__ . ". \r\n" ) : NULL);
					$this->mClean ['basketSkus'] [] = $sku->GetSkuId ();
					#($this->mRegistry->debugMode ? fwrite ( $this->mFh, "Adding sku " . $sku->GetSkuId () . " to array line " . __LINE__ . ". \r\n" ) : NULL);
				} else {
					$skuError = $this->ConstructSkuErrorMessage ( $skuAttributeIdArr );
					$this->mClean ['problemSkus'] [] = $skuError;
				}
			}
		}
	} // End AddSkusToBasketForProduct()
	

	//! Used internally to help when the user has picked for example 'Black/Large' however the SKU Attribute ID matching 'Black' is the first black that occurs, where several may exist
	/*
	 * So for example all of the following are combinations...
	 * Black(1) - Large(4)
	 * Black(2) - X-Large(5)
	 * Black(3) - XX-Large(6)
	 * And only one of the 'Black' options is displayed - the user could pick Black (1) and XX-Large (6), and the SKU superficially wouldnt match up, but using this function
	 * we can find the Black(3) - XX-Large(6) SKU anyway
	 */
	/*!
	 * @param $attributeValueArray [in] - Array(String) - The attributes (Eg. Black, XX-Large) as Strings
	 * @param $product [in] - Obj:ProductModel - The product whose SKUs need checking
	 * @return Obj:SkuModel or Boolean False
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
	}
	
	//! Checks all of the values in an array are the same
	// Actually a general purpose function, but used by NewCustomerOrderHandler::AddSkusToBasketForPackageProduct (And ...Product) to check SKU values are the same
	/*
	 * $param $array - The array to scan
	 * @return Boolean - True if all entries ARE the same
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
	}
	
	//! Sets the quantity of the product whose ID is contained in $str as "productQuantityPID"
	/*!
	 * @param $str [in] String - The "productQuantityPID" string
	 * @param $quantity [in] Int - The quanity
	 * @return Void
	 */
	function SetProductQuantity($str, $quantity) {
		$quantityArr = explode ( 'productQuantity', $str );
		$productId = $quantityArr [1];
		$this->mClean ['quantity'] [$productId] = $quantity;
	}
	
	//! Sets the price of the product whose ID is contained in $str as "productPricePID"
	/*!
	 * @param $str [in] String - The "productPricePID" string
	 * @param $quantity [in] Decimal - The price
	 * @return Void
	 */
	function SetProductPrice($str, $price) {
		$priceArr = explode ( 'productPrice', $str );
		$productId = $priceArr [1];
		$this->mClean ['productPrice'] [$productId] = $price;
	}
	
	//! Sets the quantity of the package whose ID is contained in $str as "packageQuantityPID"
	/*!
	 * @param $str [in] String - The "packageQuantityPID" string
	 * @param $quantity [in] Int - The quanity
	 * @return Void
	 */
	function SetPackageQuantity($str, $quantity) {
		$quantityArr = explode ( 'packageQuantity', $str );
		$packageId = $quantityArr [1];
		$this->mClean ['packageQuantity'] [$packageId] = $quantity;
	}
	
	//! Constructs an error message detailing why a given SKU is invalid
	/*!
	 * @param $skuAttributeIdArray Array(Int) - An array of SKU Attribute IDs
	 * @return String - the error message
	 */
	function ConstructSkuErrorMessage($skuAttributeIdArray) {
		$str = 'The combination: ';
		foreach ( $skuAttributeIdArray as $skuAttributeId ) {
			$skuAttribute = new SkuAttributeModel ( $skuAttributeId );
			$str .= $skuAttribute->GetAttributeValue () . ', ';
		}
		// Remove the last comma
		$str = substr ( $str, 0, (strlen ( $str ) - 2) );
		$str .= ' doesn\'t exist for product: ';
		$skuAttribute = new SkuAttributeModel ( $skuAttributeIdArray [0] );
		$sku = new SkuModel ( $skuAttribute->GetSkuId () );
		$product = $sku->GetParentProduct ();
		$str .= $product->GetDisplayName () . '.';
		return $str;
	}
	
	//! Expects PREFIXproductName{ID}, returns ID
	/*!
	 * @param $str - the PREFIXproductName{ID} string
	 * @return ID - String/Int
	 */
	function ExtractProductId($str) {
		$productIdArr = explode ( 'productName', $str );
		return $productIdArr [1];
	}
	
	//! Expects PREFIXpackageName{ID} returns ID
	/*!
	 * @param $str - the PREFIXpackageName{ID} string
	 * @return ID - String/Int
	 */
	function ExtractPackageId($str) {
		$packageIdArr = explode ( 'packageName', $str );
		return $packageIdArr [1];
	}
	
	//! Checks that the expiry date is in the future - throws exception if it isnt
	function CheckExpiryDate() {
		$thisMonth = date ( 'm', time () );
		$thisYear = date ( 'Y', time () );
		if ($thisYear > $this->mClean ['expiryDateYear']) {
			throw new Exception ( 'Card has expired.' );
		} elseif ($thisYear == $this->mClean ['expiryDateYear']) {
			if ($thisMonth > $this->mClean ['expiryDateMonth']) {
				throw new Exception ( 'Card has expired.' );
			}
		}
	}
	
	//! Checks that the valid from date is in the past - throws exception if it isnt
	function CheckValidFromDate() {
		$thisMonth = date ( 'm', time () );
		$thisYear = date ( 'Y', time () );
		if ($thisYear < $this->mClean ['validFromYear']) {
			throw new Exception ( 'Valid from date must be in the past!' );
		} elseif ($thisYear == $this->mClean ['validFromYear']) {
			if ($thisMonth < $this->mClean ['validFromMonth']) {
				throw new Exception ( 'Valid from date must be in the past!' );
			}
		}
	}
	
	//! Actually saves the order
	/* Details
	 * 1) Figures out the total Price, first adding package prices, then product ones
	 * 2) Adds the correct SKUs to the basket (at same time) as 1)
	 * 3) Over-ride the total if someone has done so
	 * 4) Create an order
	 * 5) Creates & initialises the customer
	 * 6) Creates & initialises the billing & shipping addresses
	 * 7) Sets the order details (Customer, Addresses, Totals, Basket etc.)
	 * 8) Confirm the order screen
	 */
	/*!
	 * @return Void
	 */
	function SaveOrder() {
		$orderController = new OrderController ( );
		$addressController = new AddressController ( );
		$customerController = new CustomerController ( );
		
		$runningTotal = 0;
		if (isset ( $this->mClean ['packages'] )) {
			foreach ( $this->mClean ['packages'] as $package ) {
				for($i = 0; $i < $this->mClean ['packageQuantity'] [$package->GetPackageId ()]; $i ++) {
					#($this->mRegistry->debugMode ? fwrite ( $this->mFh, "Adding package to basket line " . __LINE__ . ". \r\n" ) : NULL);
					$this->mBasket->AddPackageToBasket ( $package );
					$runningTotal += $package->GetActualPrice ();
				}
			}
		}
		if (isset ( $this->mClean ['basketSkus'] )) {
			#($this->mRegistry->debugMode ? fwrite ( $this->mFh, "SKUs to add: " . count ( $this->mClean ['basketSkus'] ) . " \r\n" ) : NULL);
			// To stop the problems associated with a product IN a package being ordered in the same order AS the package itself
			$productsAlreadyAddedToPackages = array ();
			// To stop a product's price being added when it is IN the package as well as separately
			$productsAlreadyPriced = array ();
			foreach ( $this->mClean ['basketSkus'] as $skuId ) {
				$sku = new SkuModel ( $skuId );
				$product = $sku->GetParentProduct ();
				// Handle packages
				if (isset ( $this->mClean ['packages'] )) {
					foreach ( $this->mClean ['packages'] as $package ) {
						if ($package->IsUpgrade ( $product )) {
							$upgrade = $product;
							$product = $package->GetProductForUpgrade ( $upgrade );
							$upgradePrice = $package->GetUpgradePrice ( $product, $upgrade );
							$this->mBasket->AddToBasket ( $sku, true, $upgradePrice );
							#($this->mRegistry->debugMode ? fwrite ( $this->mFh, "Adding SKU " . $sku->GetSkuId () . " to basket line " . __LINE__ . ". \r\n" ) : NULL);
							$this->mBasket->ChangePriceForSku ( $sku, $upgradePrice );
						} else {
							if ($package->IsPart ( $product ) && ! in_array ( $product->GetProductId (), $productsAlreadyAddedToPackages )) {
								#($this->mRegistry->debugMode ? fwrite ( $this->mFh, "Adding SKU " . $sku->GetSkuId () . " to basket line " . __LINE__ . ". \r\n" ) : NULL);
								$this->mBasket->AddToBasket ( $sku, true );
								$this->mBasket->ChangePriceForSku ( $sku, '0.0' );
								$productsAlreadyAddedToPackages [] = $product->GetProductId ();
							} elseif ($package->IsPart ( $product ) && in_array ( $product->GetProductId (), $productsAlreadyAddedToPackages )) {
								#($this->mRegistry->debugMode ? fwrite ( $this->mFh, "Adding SKU " . $sku->GetSkuId () . " to basket line " . __LINE__ . ". \r\n" ) : NULL);
								$this->mBasket->AddToBasket ( $sku );
							}
						}
					} // End foreach
				}
				// If not part of a package, just add the sku
				if (! $this->mBasket->IsPackage ( $sku ) && ! $this->mBasket->IsPackageUpgrade ( $sku )) {
					#($this->mRegistry->debugMode ? fwrite ( $this->mFh, "Adding SKU " . $sku->GetSkuId () . " to basket line " . __LINE__ . ". \r\n" ) : NULL);
					$this->mBasket->AddToBasket ( $sku );
				}
				
				/* prices section */
				// Change prices if needed
				//***** BUG - If price 2 x Pharma Whey @ 32.15 then once the first one has been priced at 32.15 the second one is already in the $productsAlreadyPriced array it is full priced
				if (isset ( $this->mClean ['productPrice'] [$product->GetProductId ()] ) && ! in_array ( $product->GetProductId (), $productsAlreadyPriced )) {
					$this->mBasket->ChangePriceForSku ( $sku, $this->mClean ['productPrice'] [$product->GetProductId ()] );
					#($this->mRegistry->debugMode ? fwrite ( $this->mFh, "Running total ++ " . $this->mClean ['productPrice'] [$product->GetProductId ()] . " " . __LINE__ . ". \r\n" ) : NULL);
					$runningTotal += $this->mClean ['productPrice'] [$product->GetProductId ()];
					$productsAlreadyPriced [] = $product->GetProductId ();
				} else {
					// If not in a package, add the regular SKU price
					if (! $this->mBasket->IsPackage ( $sku )) {
						if ($this->mBasket->IsPackageUpgrade ( $sku )) {
							$runningTotal += $this->mBasket->GetOverruledSkuPrice ( $sku );
							#($this->mRegistry->debugMode ? fwrite ( $this->mFh, "Running total ++  " . __LINE__ . ". \r\n" ) : NULL);
						} else {
							#for($i=0;$i<$this->mClean['quantity'][$product->GetProductId()];$i++) {
							$runningTotal += $sku->GetSkuPrice ();
							#($this->mRegistry->debugMode ? fwrite ( $this->mFh, "Running total ++ " . __LINE__ . ". \r\n" ) : NULL);
							#}
						}
					}
				} // end if
			} // end foreach
		} // end if
		#$runningTotal += $this->mClean['orderPostageTotal'];
		

		// Override all previous totals if the total has been adjusted
		#if(isset($this->mClean['orderAdjustmentTotal']) && $this->mClean['orderAdjustmentTotal']!=0) {
		#	$runningTotal = $this->mClean['orderAdjustmentTotal'];
		#}
		

		// Create the order
		$order = $orderController->CreateOrder ( $this->mBasket );
		// Create the customer
		$customer = $customerController->CreateCustomer ();
		
		// Set customer details
		$customer->SetFirstName ( $this->mClean ['customerName'] );
		$customer->SetDaytimeTelephone ( $this->mClean ['telephoneNumber'] );
		$customer->SetEmail ( $this->mClean ['emailAddress'] );
		
		// Create Shipping/Billing Addresses
		$shippingAddress = $addressController->CreateAddress ();
		$billingAddress = $addressController->CreateAddress ();
		
		// Set shipping details
		$shippingAddress->SetLine1 ( $this->mClean ['delivery1'] );
		$shippingAddress->SetLine2 ( $this->mClean ['delivery2'] );
		$shippingAddress->SetLine3 ( $this->mClean ['delivery3'] );
		$shippingAddress->SetCounty ( $this->mClean ['county'] );
		$shippingAddress->SetPostcode ( $this->mClean ['deliveryPostcode'] );
		
		// Set billing details
		$billingAddress->SetLine1 ( $this->mClean ['billing1'] );
		$billingAddress->SetPostcode ( $this->mClean ['billingPostcode'] );
		
		// Set order details
		$order->SetShippingAddress ( $shippingAddress );
		$order->SetBillingAddress ( $billingAddress );
		$order->SetTotalPrice ( $runningTotal );
		$order->SetTotalPostage ( $this->mClean ['orderPostageTotal'] );
		$order->SetCustomer ( $customer );
		$order->SetStaffName ( $this->mClean ['staffName'] );
		$order->SetNotes ( $this->mClean ['notes'] );
		$order->SetStaffNotes ( $this->mClean ['staffNotes'] );
		if (isset ( $this->mClean ['brochure'] )) {
			$order->SetBrochure ( 1 );
		} else {
			$order->SetBrochure ( 0 );
		}
		
		// Set affiliate details
		$affiliateHelper = new AffiliateHelper ( );
		if ($affiliateHelper->GetAffiliate()) {
			$affiliate = $affiliateHelper->GetAffiliate();
			$order->SetAffiliate($affiliate);
		}
		
		// This actually doesnt do anything as nothing gets added to the problemSkus array at the minute...
		if (isset ( $this->mClean ['problemSkus'] )) {
			foreach ( $this->mClean ['problemSkus'] as $errorMsg ) {
				echo $errorMsg . '<br />';
			}
		} else {
			// Confirm the order
			$this->LoadConfirmBar ();
			$confirmView = new OrderView ( );
			$this->mPage .= $confirmView->LoadDefault ( $order->GetOrderId (), true );
			echo $this->mPage;
		}
	} // End SaveOrder()
	

	//! Loads the confirm order section, allowing the customer to be read the order details over 
	function LoadConfirmBar() {
		$registry = Registry::getInstance ();
		$secureDir = str_replace ( 'http', 'https', $registry->formHandlersDir );
		$this->mPage .= '
						<div style="width: 550px; border-bottom: 1px solid #000; text-align: center; margin-bottom: 10px; padding-bottom: 10px;">
						<h2>Confirm Order</h2>
							<form action="' . $secureDir . '/ConfirmOrderHandler.php" name="confirmOrderForm" id="confirmOrderForm" method="post" target="ordersEdit">
								<input type="hidden" name="basketId" id="basketId" value="' . $this->mBasket->GetBasketId () . '" />';
		// Pass on payment details
		$this->mPage .= '<input type="hidden" name="billing1" id="billing1" value="' . $this->mClean ['billing1'] . '" />';
		$this->mPage .= '<input type="hidden" name="billingPostcode" id="billingPostcode" value="' . $this->mClean ['billingPostcode'] . '" />';
		$this->mPage .= '<input type="hidden" name="cardType" id="cardType" value="' . $this->mClean ['cardType'] . '" />';
		$this->mPage .= '<input type="hidden" name="cardNumber" id="cardNumber" value="' . $this->mClean ['cardNumber'] . '" />';
		$this->mPage .= '<input type="hidden" name="validFromYear" id="validFromYear" value="' . $this->mClean ['validFromYear'] . '" />';
		$this->mPage .= '<input type="hidden" name="validFromMonth" id="validFromMonth" value="' . $this->mClean ['validFromMonth'] . '" />';
		$this->mPage .= '<input type="hidden" name="issueNumber" id="issueNumber" value="' . $this->mClean ['issueNumber'] . '" />';
		$this->mPage .= '<input type="hidden" name="expiryDateMonth" id="expiryDateMonth" value="' . $this->mClean ['expiryDateMonth'] . '" />';
		$this->mPage .= '<input type="hidden" name="expiryDateYear" id="expiryDateYear" value="' . $this->mClean ['expiryDateYear'] . '" />';
		$this->mPage .= '<input type="hidden" name="cardVerificationNumber" id="cardVerificationNumber" value="' . $this->mClean ['cardVerificationNumber'] . '" />';
		$this->mPage .= '<input type="hidden" name="orderTotalPrice" id="orderTotalPrice" value="' . $this->mClean ['orderTotalPrice'] . '" />';
		$this->mPage .= '<input type="hidden" name="customerName" id="customerName" value="' . $this->mClean ['customerName'] . '" />';
		$this->mPage .= '<input type="submit" value="Confirm Order" name="orderConfirmButton" id="orderConfirmButton" />
							</form>
						</div>';
	}

} // End NewCustomerOrderHandler


try {
	$handler = new NewCustomerOrderHandler ( $_GET ['sessionId'] );
	$handler->Validate ( $_POST );
	$handler->SaveOrder ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>