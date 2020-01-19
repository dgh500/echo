<?php

//! Defines the order view seen on the admin side - see PublicOrderView for the public's view
class OrderView extends AdminView {
	
	//! The order - Obj:OrderModel
	var $mOrder;
	//! The basket for the order - Obj:BasketModel
	var $mBasket;
	
	//! Constructor - loads parent constructor and makes decisions on whether local or production
	function __construct() {
		parent::__construct (true);
		if ($this->mRegistry->localMode) {
			$this->mOrderPrefix = 'DBDL0';
		} else {
			$this->mOrderPrefix = 'ECHO';
		}
		if($this->mRegistry->debugMode) {
			$this->mDebugMode = true;
			$this->mFh = fopen('../debug/OrderView.txt','a+');
		} else {
			$this->mDebugMode = false;	
		}		
	}
	
	//! Loads an order form view, given an order ID
	/*!
	 * @param [in] $orderId : Int - The order ID
	 * @param [in] $secure  : Boolean - Whether or not this should be a secure view
	 */
	function LoadDefault($orderId, $secure = false) {
		// Initialise the order (and basket, for convenience)
		$this->mOrder = new OrderModel ( $orderId );
		$this->mBasket = $this->mOrder->GetBasket ();
		
		// If in secure mode then load the CSS & JS securely to stop annoying IE warnings
		if ($secure) {
			$this->IncludeCss ( 'OrderView.css.php', true, false, true );
			$this->IncludeJavascript ( 'OrderView.js', true, true );
		} else {
			$this->IncludeCss ( 'OrderView.css.php' );
			$this->IncludeJavascript ( 'OrderView.js' );
		}
		
		// Try and load the order form, if there is a problem then display a message
		try {
			$this->OpenOrderForm ();
			$this->LoadDeepBlueSection ();
			$this->LoadOrderDetails ();
			$this->LoadBasketDetails ();
			$this->LoadMisc ();
			$this->LoadAddresses ();
			$this->CloseOrderForm ();
		} catch ( Exception $e ) {
			echo '<span style="font-family: Arial, Sans-Serif; font-size: 12pt;">There was a problem loading order: ' . $orderId . ' - this is probably because the product has been taken off the web.</span>';
			die ($e->getMessage());
		}
		
		// Return the view to be displayed
		return $this->mPage;
	}
	
	//! Does some prelim work setting up secure/non-secure and whether to allow updates
	function OpenOrderForm() {
		$dir = str_replace ( 'http', 'https', $this->mFormHandlersDir );
		$this->mPage .= <<<EOT
		<div id="orderForm">
			<form id="orderForm" name="orderForm" method="post" action="{$dir}/orderFormEditHandler.php" />
			<input type="hidden" id="orderId" name="orderId" value="{$this->mOrder->GetOrderId()}" />
EOT;
		if ($this->mOrder->GetStatus ()->GetDescription () == 'Failed' || $this->mOrder->GetStatus ()->GetDescription () == 'Cancelled By User' || $this->mOrder->GetStatus ()->GetDescription () == 'Cancelled By Merchant') {
			$this->mPage .= '<div id="cancelledOrderBg"></div>';
			$this->mShowUpdateForm = false;
		} else {
			if ($this->mOrder->GetStatus ()->GetDescription () == 'In Transit') {
				$this->mShowUpdateForm = false;
			} else {
				$this->mShowUpdateForm = true;
			}
		}
	}
	
	//! Cleans up after the form, closes <form> and <div> elements
	function CloseOrderForm() {
		$this->mPage .= <<<EOT
			</form>
		</div>
EOT;
	}
	
	//! Loads the address and contact details
	function LoadDeepBlueSection() {
		$this->mPage .= <<<EOT
			<h1>Sales Order</h1>	
			{$this->mOrder->GetCatalogue()->GetDisplayName()}<br />
			55 Marden Road<br />
			Whitley Bay<br />
			Tyne and Wear<br />
			NE26 2JW<br />
			<br />
			Tel: (0191) 2536220<br />
			Fax: (0191) 2895714<br />
			<br />
EOT;
	}
	
	//! Loads the Order No, Date, Status, Delivery Method, Weight, Courier, Tracking No & Dispatch Estimate
	function LoadOrderDetails() {
		$this->mOrderStatusController = new OrderStatusController ( );
		$this->mCourierController = new CourierController ( );
		$this->mDispatchDateController = new DispatchDateController ( );
		
		// Construct order statuses options
		$allStatuses = $this->mOrderStatusController->GetAll ();
		$orderStatusOptions = '';
		foreach ( $allStatuses as $orderStatus ) {
			// The option is selected if it matches the current order's status
			if ($orderStatus->GetStatusId () == $this->mOrder->GetStatus ()->GetStatusId ()) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			$orderStatusOptions .= '<option value="' . $orderStatus->GetStatusId () . '" ' . $selected . '>' . $orderStatus->GetDescription () . '</option>';
		}
		
		// Construct couriers options
		$allCouriers = $this->mCourierController->GetAll ();
		$orderCourierOptions = '';
		foreach ( $allCouriers as $courier ) {
			if ($courier->GetCourierId () == $this->mOrder->GetCourier ()->GetCourierId ()) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			$orderCourierOptions .= '<option value="' . $courier->GetCourierId () . '" ' . $selected . ' >' . $courier->GetDisplayName () . '</option>';
		}
		
		// Construct dispatch estimate options
		$allDispatchDates = $this->mDispatchDateController->GetAllDispatchDates ();
		$dispatchEstimateOptions = '';
		foreach ( $allDispatchDates as $dispatchDate ) {
			if ($dispatchDate->GetDispatchDateId () == $this->mOrder->GetDispatchDate ()->GetDispatchDateId ()) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			$dispatchEstimateOptions .= '<option value="' . $dispatchDate->GetDispatchDateId () . '" ' . $selected . ' >' . $dispatchDate->GetDisplayName () . '</option>';
		}
		
		// Construct the options for reasons an order might be cancelled
		$reasonForCancelOptions = '
			<option value="NULL">Choose a Reason</option>
			<option value="discontinued">Discontinued</option>
			<option value="tempOutOfStock">Temp Out Of Stock</option>
			<option value="waitedTooLong">Waited Too Long</option>
			<option value="noLongerRequired">No Longer Required</option>
			<option value="dontShipThere">Don\'t Ship There</option>';
		
		// Only display the affiliate part if an affiliate was used
		if (NULL != $this->mOrder->GetAffiliate ()) {
			$affiliateSection = '<label for="affiliate">Affiliate</label>
				<span class="falseInput">' . $this->mOrder->GetAffiliate ()->GetName () . '</span>';
		} else {
			$affiliateSection = '';
		}
		
		$this->mPage .= <<<EOT
			<strong>Order No:</strong> {$this->mOrderPrefix}{$this->mOrder->GetOrderId()}<br />
			<strong>Order Date:</strong> {$this->mTimeHelper->TimestampToDateAndTime($this->mOrder->GetCreatedDate())}<br /><br />
			<label for="orderStatus">Status</label>
				<select name="orderStatus" onchange="OrderStatusEventHandler(this.form.orderStatus[this.selectedIndex].value)">{$orderStatusOptions}</select>
EOT;
		// Only show the update button if it makes sense
		if ($this->mShowUpdateForm) {
			$this->mPage .= '<input type="submit" value="Update" id="saveChangesOrderFormButton" onclick="return UpdateOrderButtonHandler()" />';
		} else {
			$this->mPage .= '';
		}
		$this->mPage .= <<<EOT
			<br />
			<div id="reasonForCancelContainer" style="display: none;">
				<label for="reasonForCancel">Reason</label>
					<select name="reasonForCancel" id="reasonForCancel">{$reasonForCancelOptions}</select>
			</div>
			<label for="deliveryMethod">Delivery Method</label>
				<span class="falseInput">{$this->mOrder->GetPostageMethod()->GetDisplayName()}</span>
			<br />
			<label for="weight">Weight</label>
				<span class="falseInput">{$this->mOrder->GetBasket()->GetWeight()}g</span>
			<br />
			{$affiliateSection}
			<label for="courier">Courier</label>
				<select name="courier">{$orderCourierOptions}</select>
			<br />
			<label for="trackingNumber">Tracking Number</label>
				<input type="text" name="trackingNumber" id="trackingNumber" value="{$this->mOrder->GetTrackingNumber()}" />
			<br />
			<label for="dispatchEstimate">Dispatch Estimate</label>
				<select name="dispatchEstimate">{$dispatchEstimateOptions}</select>
			<br />
EOT;
	} // End LoadOrderDetails
	

	//! Loads Staff Name, Brochure and Notes section
	function LoadMisc() {
		$this->mPage .= (trim ( $this->mOrder->GetStaffName () ) ? '<strong>Staff</strong>: ' . $this->mOrder->GetStaffName () . '' : '<strong>Staff</strong>: Web Order');
		$this->mPage .= ($this->mOrder->GetBrochure () ? ' | <strong>Brochure</strong>: Yes' : ' | <strong>Brochure</strong>: No');
		$this->mPage .= (trim ( $this->mOrder->GetNotes () ) ? ' | <strong>Notes</strong>: ' . $this->mValidationHelper->MakeSafe ( $this->mOrder->GetNotes () ) : ' | <strong>Notes</strong>: None');
		$this->mPage .= '<br /><br />';
	} // End LoadMisc
	

	//! Load the customer's billing and shipping details - including phone numbers and email
	function LoadAddresses() {
		
		// Billing Address
		$this->mPage .= <<<EOT
			<strong>Billing Details</strong><br />
EOT;
		$this->mPage .= (trim ( $this->mOrder->GetBillingAddress ()->GetLine1 () ) ? $this->mOrder->GetBillingAddress ()->GetLine1 () . '<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetBillingAddress ()->GetLine2 () ) ? $this->mOrder->GetBillingAddress ()->GetLine2 () . '<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetBillingAddress ()->GetLine3 () ) ? $this->mOrder->GetBillingAddress ()->GetLine3 () . '<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetBillingAddress ()->GetCounty () ) ? $this->mOrder->GetBillingAddress ()->GetCounty () . '<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetBillingAddress ()->GetPostcode () ) ? $this->mOrder->GetBillingAddress ()->GetPostcode () . '<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetBillingAddress ()->GetCountry ()->GetDescription () ) ? $this->mOrder->GetBillingAddress ()->GetCountry ()->GetDescription () . '<br />' : '');
		
		// Phone numbers and email
		$this->mPage .= <<<EOT
			<br />
			<strong>Shipping Details</strong><br />
			<strong>Daytime Tel: </strong> {$this->mOrder->GetCustomer()->GetDaytimeTelephone()}<br />
			<strong>Mobile Tel: </strong> {$this->mOrder->GetCustomer()->GetMobileTelephone()}<br />
			<strong>Email: </strong> {$this->mOrder->GetCustomer()->GetEmail()}<br />
			<br />
EOT;
		// Shipping Address
		$this->mPage .= (trim ( $this->mOrder->GetCustomer ()->GetTitle () ) ? $this->mOrder->GetCustomer ()->GetTitle () . ' ' : '');
		$this->mPage .= (trim ( $this->mOrder->GetCustomer ()->GetFirstName () ) ? $this->mOrder->GetCustomer ()->GetFirstName () . ' ' : '');
		$this->mPage .= (trim ( $this->mOrder->GetCustomer ()->GetLastName () ) ? $this->mOrder->GetCustomer ()->GetLastName () : '');
		$this->mPage .= (trim ( $this->mOrder->GetShippingAddress ()->GetLine1 () ) ? '<br />' . $this->mOrder->GetShippingAddress ()->GetLine1 () . '<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetShippingAddress ()->GetLine2 () ) ? $this->mOrder->GetShippingAddress ()->GetLine2 () . '<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetShippingAddress ()->GetLine3 () ) ? $this->mOrder->GetShippingAddress ()->GetLine3 () . '<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetShippingAddress ()->GetCounty () ) ? $this->mOrder->GetShippingAddress ()->GetCounty () . '<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetShippingAddress ()->GetPostcode () ) ? $this->mOrder->GetShippingAddress ()->GetPostcode () . '<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetShippingAddress ()->GetCountry ()->GetDescription () ) ? $this->mOrder->GetShippingAddress ()->GetCountry ()->GetDescription () . '<br />' : '');
		
		// Order number
		$this->mPage .= '<br />ECHO' . $this->mOrder->GetOrderId ();
	} // End LoadAddresses
	

	//! Loads the table displaying the basket for the order form
	function LoadBasketDetails() {
		// Decide whether the order has been completed (In Transit/Cancelled/Failed)
		if (! $this->mOrder->GetStatus ()->IsComplete ()) {
			$shipHeading = 'Ship?';
		} else {
			$shipHeading = 'Shipped';
		}
		// Display the first row of the table with headings
		$this->mPage .= <<<EOT
			<table>
				<tr>
					<th class="left" width="60%">Product</th><th class="center" width="20%">Qty</th><th width="10%">Price</th><th width="10%">{$shipHeading}</th>
				</tr>
EOT;
		// Initialise the arrays
		$skusPrepArr = array ();
		$upgradesArr = array ();
		$prevUpgrades = array ();
		
		// Initialise postage
		$this->mCurrentPostage = 0;
		
		$packagesPrepArr = array ();
		
		// Make a 'prepped' array so its easier to loop over
		// The rationale is that the prepped array is indexed by the package ID
		/*
		 * $packagesPrepArr[_PACKAGE_ID_]['qty']
		 * $packagesPrepArr[_PACKAGE_ID_]['totalPrice']
		 * $packagesPrepArr[_PACKAGE_ID_]['unitPrice']
		 * $packagesPrepArr[_PACKAGE_ID_]['totalPrice']		 
		 */
		foreach ( $this->mOrder->GetBasket ()->GetPackages () as $package ) {
			if (isset ( $packagesPrepArr [$package->GetPackageId ()] )) {
				$packagesPrepArr [$package->GetPackageId ()] ['qty'] ++;
				$packagesPrepArr [$package->GetPackageId ()] ['totalPrice'] = $packagesPrepArr [$package->GetPackageId ()] ['qty'] * $packagesPrepArr [$package->GetPackageId ()] ['unitPrice'];
			} else {
				$packagesPrepArr [$package->GetPackageId ()] ['qty'] = 1;
				$packagesPrepArr [$package->GetPackageId ()] ['unitPrice'] = $this->mOrder->GetBasket ()->GetOverruledPackagePrice ( $package );
				$packagesPrepArr [$package->GetPackageId ()] ['totalPrice'] = $this->mOrder->GetBasket ()->GetOverruledPackagePrice ( $package );
			}
			if ($package->GetPostage () > $this->mCurrentPostage) {
				$this->mCurrentPostage = $package->GetPostage ();
			}
		} // End prepping packages
		

		// Make a 'prepped' array so its easier to loop over
		// The rationale is that the prepped array is indexed by the SKU ID
		/*
		 * $packagesPrepArr[_SKU_ID_]['qty']
		 * $packagesPrepArr[_SKU_ID_]['totalPrice']
		 * $packagesPrepArr[_SKU_ID_]['unitPrice']
		 * $packagesPrepArr[_SKU_ID_]['totalPrice']	
		 */
		$skuQtyCheckArray = array ();
		foreach ( $this->mOrder->GetBasket ()->GetSkus () as $sku ) {
			$skuQtyCheckArray [$sku->GetSkuId ()] = 0;
		}
		
		($this->mDebugMode ? fwrite($this->mFh,count($this->mOrder->GetBasket ()->GetSkus ())) : NULL);
		
		foreach ( $this->mOrder->GetBasket ()->GetSkus () as $sku ) {
			// Max times this sku is used as an upgrade
			$maxQtyForSku = $this->mBasket->GetNumberOfUpgradesFor ( $sku );
			
			// If this SKU is already in the array, just increment the quantity and adjust the total
			if (isset ( $skusPrepArr [$sku->GetSkuId ()] )) {
				$skusPrepArr [$sku->GetSkuId ()] ['qty'] ++;
				$skusPrepArr [$sku->GetSkuId ()] ['totalPrice'] = $skusPrepArr [$sku->GetSkuId ()] ['qty'] * $skusPrepArr [$sku->GetSkuId ()] ['unitPrice'];
			} else {
				// If the SKU is a package upgrade then either change the quantity for it or add it to the upgrades array
				if ($this->mOrder->GetBasket ()->IsPackageUpgrade ( $sku ) && $skuQtyCheckArray [$sku->GetSkuId ()] < $maxQtyForSku) {
					if (in_array ( $sku->GetSkuId (), $prevUpgrades )) {
						$upgradesArr [$sku->GetSkuId ()] ['qty'] ++;
						$skuQtyCheckArray [$sku->GetSkuId ()] ++;
					} else {
						$upgradesArr [$sku->GetSkuId ()] ['qty'] = 1;
						$upgradesArr [$sku->GetSkuId ()] ['unitPrice'] = $this->mOrder->GetBasket ()->GetOverruledSkuPrice ( $sku, false, true );
						$upgradesArr [$sku->GetSkuId ()] ['totalPrice'] = $this->mOrder->GetBasket ()->GetOverruledSkuPrice ( $sku, false, true );
						$prevUpgrades [] = $sku->GetSkuId ();
						$skuQtyCheckArray [$sku->GetSkuId ()] ++;
					}
				} else {
					// At this stage the SKU is definately a new (not in the array) SKU and just need to check if its price has been over-ruled
					if ($this->mOrder->GetBasket ()->HasOverruledSku ( $sku )) {
						$skusPrepArr [$sku->GetSkuId ()] ['qty'] = 1;
						$skusPrepArr [$sku->GetSkuId ()] ['unitPrice'] = $this->mOrder->GetBasket ()->GetOverruledSkuPrice ( $sku );
						$skusPrepArr [$sku->GetSkuId ()] ['totalPrice'] = $this->mOrder->GetBasket ()->GetOverruledSkuPrice ( $sku );
					} else {
						$skusPrepArr [$sku->GetSkuId ()] ['qty'] = 1;
						$skusPrepArr [$sku->GetSkuId ()] ['unitPrice'] = $sku->GetSkuPrice ();
						$skusPrepArr [$sku->GetSkuId ()] ['totalPrice'] = $sku->GetSkuPrice ();
					} // End if the SKU has an over-ruled price
				} // End if the SKU is a package upgrade
			} // End if the SKU is already in the array
			

			$product = $sku->GetParentProduct ();
			if ($product->GetPostage () > $this->mCurrentPostage) {
				$this->mCurrentPostage = $product->GetPostage ();
			}
		} // End prepping
		

		// Show list of packages
		$allPackages = $this->mOrder->GetBasket ()->GetPackages ();
		foreach ( $packagesPrepArr as $key => $preppedPack ) {
			$package = new PackageModel ( $key );
			$packageContents = $package->GetContents ();
			$packageContentsDisplay = '';
			
			// Make shipped section - Whether to say Yes/No for each SKU. 
			// If cancelled then always 'No'. If a SKU isnt shipped, then if the order is in transit it says 'No' otherwise it displays a checkbox (assumed Authorised)
			if ($this->mOrder->GetStatus ()->IsCancelled ()) {
				$shippedSection = 'No';
			} else {
				if (! $this->mBasket->IsShippedPackage ( $package )) {
					if ($this->mOrder->GetStatus ()->IsInTransit ()) {
						$shippedSection = 'No';
					} else {
						$shippedSection = '<input type="checkbox" name="packageShip' . $package->GetPackageId () . '" id="packageShip' . $package->GetPackageId () . '" checked="checked" />';
					}
				} else {
					$shippedSection = 'Yes';
				}
			}
			
			// Make a display list of attributes
			foreach ( $packageContents as $product ) {
				$attributeInfo = '';
				foreach ( $product->GetSkus () as $sku ) {
					if ($this->mOrder->GetBasket ()->InBasket ( $sku ) && count ( $sku->GetSkuAttributes () ) > 0) {
						$attributeInfo .= '(';
						foreach ( $sku->GetSkuAttributes () as $skuAttribute ) {
							$attributeInfo .= $skuAttribute->GetAttributeValue () . ', ';
						}
						$attributeInfo = substr ( $attributeInfo, 0, (strlen ( $attributeInfo ) - 2) );
						$attributeInfo .= ')';
					} else {
						#$attributeInfo .= $sku->GetSkuId(); null?
					}
				}
				$packageContentsDisplay .= ' - ' . $product->GetDisplayName () . ' ' . $attributeInfo . '<br />';
			}
			
			// Show VAT-Free display if not being shipped to Europe
			if ($this->mOrder->GetShippingAddress ()->GetCountry ()->IsVatFree ()) {
				$packagePrice = $this->mPresentationHelper->Money ( $this->mMoneyHelper->RemoveVAT ( $packagesPrepArr [$package->GetPackageId ()] ['unitPrice'] ) );
			} else {
				$packagePrice = $this->mPresentationHelper->Money ( $packagesPrepArr [$package->GetPackageId ()] ['unitPrice'] );
			}
			
			$this->mPage .= '<tr>	
								<td id="productColumn" style="text-align: left"><strong>' . $package->GetDisplayName () . '</strong><br />' . $packageContentsDisplay . '</td>
								<td id="qtyColumn">' . $packagesPrepArr [$package->GetPackageId ()] ['qty'] . '</td>
								<td id="unitPriceColumn">&pound;' . $this->mPresentationHelper->Money ( $packagePrice ) . '</td>
								<td id="totalPriceColumn">' . $shippedSection . '</td>
								</form>
							</tr>';
		}
		
		// Show list of upgrades (by SKU) for the package(s)
		foreach ( $upgradesArr as $key => $preppedUpgrade ) {
			$sku = new SkuModel ( $key );
			$skuAttributes = $sku->GetSkuAttributes ();
			
			// Make a display list of attributes
			if (count ( $skuAttributes ) != 0) {
				$options = '(';
				foreach ( $skuAttributes as $skuAttribute ) {
					$options .= $skuAttribute->GetAttributeValue ();
				}
				$options .= ')';
			} else {
				$options = '';
			}
			
			// Make shipped section - Whether to say Yes/No for each SKU. 
			// If cancelled then always 'No'. If a SKU isnt shipped, then if the order is in transit it says 'No' otherwise it displays a checkbox (assumed Authorised)
			if ($this->mOrder->GetStatus ()->IsCancelled ()) {
				$shippedSection = 'No';
			} else {
				if (! $this->mBasket->IsShipped ( $sku )) {
					if ($this->mOrder->GetStatus ()->IsInTransit ()) {
						$shippedSection = 'No';
					} else {
						$shippedSection = '<input type="checkbox" name="skuShip' . $sku->GetSkuId () . '" id="skuShip' . $sku->GetSkuId () . '" checked="checked" />';
					}
				} else {
					$shippedSection = 'Yes';
				}
			}
			
			// Show VAT-Free display if not being shipped to Europe
			if ($this->mOrder->GetShippingAddress ()->GetCountry ()->IsVatFree ()) {
				$skuPrice = $this->mPresentationHelper->Money ( $this->mMoneyHelper->RemoveVAT ( $upgradesArr [$sku->GetSkuId ()] ['unitPrice'] ) );
			} else {
				$skuPrice = $this->mPresentationHelper->Money ( $upgradesArr [$sku->GetSkuId ()] ['unitPrice'] );
			}
			
			// Display a row of the table for each SKU
			$this->mPage .= '<tr>	
								<td id="productColumn" style="text-align: left"><strong>Upgrade: </strong>' . $sku->GetParentProduct ()->GetDisplayName () . ' ' . $options . '</td>
								<td id="qtyColumn">' . $upgradesArr [$sku->GetSkuId ()] ['qty'] . '</td>
								<td id="unitPriceColumn">&pound;' . $this->mPresentationHelper->Money ( $skuPrice ) . '</td>
								<td id="totalPriceColumn">' . $shippedSection . '</td>
							</tr>';
		} // End foreach upgrades
		

		// Show list of products (by SKU) in the order
		foreach ( $skusPrepArr as $key => $preppedRow ) {
			$sku = new SkuModel ( $key );
			$skuAttributes = $sku->GetSkuAttributes ();
			
			// Make display list of attributes
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
			
			// Make shipped section - Whether to say Yes/No for each SKU. 
			// If cancelled then always 'No'. If a SKU isnt shipped, then if the order is in transit it says 'No' otherwise it displays a checkbox (assumed Authorised)
			if ($this->mOrder->GetStatus ()->IsCancelled ()) {
				$shippedSection = 'No';
			} else {
				if (! $this->mBasket->IsShipped ( $sku )) {
					if ($this->mOrder->GetStatus ()->IsInTransit ()) {
						$shippedSection = 'No';
					} else {
						$shippedSection = '<input type="checkbox" name="skuShip' . $sku->GetSkuId () . '" id="skuShip' . $sku->GetSkuId () . '" checked="checked" />';
					}
				} else {
					$shippedSection = 'Yes';
				}
			}
			
			// Show VAT-Free display if not being shipped to Europe
			if ($this->mOrder->GetShippingAddress ()->GetCountry ()->IsVatFree ()) {
				$skuPrice = $this->mPresentationHelper->Money ( $this->mMoneyHelper->RemoveVAT ( $preppedRow ['unitPrice'] ) );
			} else {
				$skuPrice = $this->mPresentationHelper->Money ( $preppedRow ['unitPrice'] );
			}
			
			// Display a row of the table for each SKU
			$this->mPage .= '<tr>
									<td id="productColumn" style="text-align: left">' . $sku->GetParentProduct ()->GetDisplayName () . $skuAttributesList . '</td>
									<td id="qtyColumn">' . $preppedRow ['qty'] . '</td>
									<td id="unitPriceColumn">&pound;' . $skuPrice . '</td>
									<td id="totalPriceColumn">' . $shippedSection . '</td>
							</tr>';
		} // End foreach SKU
		

		//! Absolute total - The total displayed as Sub-Total + Postage
		$absTotal = number_format ( $this->mOrder->GetTotalPrice () + $this->mOrder->GetTotalPostage (), 2 );
		
		// Display the bottom (totals) row of the table
		$this->mPage .= <<<EOT
				<tr>
					<td class="left"></td>
					<td class="right"><strong>Sub-total:</strong><br /><strong>Delivery:</strong><br /><strong>Total:</strong></td>
					<td class="right">&pound;{$this->mPresentationHelper->Money($this->mOrder->GetTotalPrice())}<br />&pound;{$this->mPresentationHelper->Money($this->mOrder->GetTotalPostage())}<br />&pound;{$absTotal}</td>
					<td></td>
				</tr>
			</table>
EOT;
	} // End LoadBasketDetails


} // End OrderEditView


?>