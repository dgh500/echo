<?php

//! Defines the order view seen on the admin side - see PublicOrderView for the public's view
class OrderView extends AdminView {

	//! The order - Obj:OrderModel
	var $mOrder;
	//! The basket for the order - Obj:BasketModel
	var $mBasket;

	//! Constructor - loads parent constructor and makes decisions on whether local or production
	function __construct() {

		$cssIncludes = array('OrderView.css.php','jqueryUI.css');
		$jsIncludes = array('OrderView.js','jqueryUi.js');

		parent::__construct(true,$cssIncludes,$jsIncludes);

		$this->mOrderItemController = new OrderItemController;
		if ($this->mRegistry->localMode) {
			$this->mOrderPrefix = 'ECHOL';
		} else {
			$this->mOrderPrefix = 'ECHO';
		}
		if($this->mRegistry->debugMode) {
			$this->mDebugMode = true;
			$this->mFh = @fopen('../debug/OrderView.txt','a+');
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
#		$this->mBasket = $this->mOrder->GetBasket ();

		// Try and load the order form, if there is a problem then display a message
		try {
			$this->OpenOrderForm ();
			$this->LoadDeepBlueSection ();
			if($this->mOrder->GetStatus()->IsAuthorised() || $this->mOrder->GetStatus()->IsInTransit()) {
				$this->LoadOrderDetails();
			}
			$this->LoadBasketDetails();
			$this->LoadEditOrderItemsForm();
			$this->LoadMisc ();
			if($this->mOrder->GetStatus()->IsInTransit()) {
				$this->LoadContactDetails();
			#	$this->LoadAddresses();
			} else {
				$this->LoadContactDetails();
			}
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
		// Set up the form
		$this->mPage .= <<<EOT
		<div id="orderForm">
			<form id="orderForm" name="orderForm" method="post" action="{$this->mSecureBaseDir}/formHandlers/OrderFormEditHandler.php" />
			<input type="hidden" id="orderId" name="orderId" value="{$this->mOrder->GetOrderId()}" />
EOT;
		// If failed/cancelled then diplay the large background image, and do not allow updates
		if($this->mOrder->GetStatus()->IsFailed() || $this->mOrder->GetStatus()->IsCancelled()) {
			$this->mPage .= '<div id="cancelledOrderBg"></div>';
			$this->mShowUpdateForm = false;
		} else {
			// Allow updates if the order is not in transit
			if($this->mOrder->GetStatus()->IsInTransit()) {
				$this->mShowUpdateForm = false;
			} else {
				$this->mShowUpdateForm = true;
			}
		}
	} // End OpenOrderForm

	//! Cleans up after the form, closes <form> and <div> elements
	function CloseOrderForm() {
		$this->mPage .= <<<EOT
			</form>
		</div>
EOT;
	} // End CloseOrderForm

	//! Loads the address and contact details
	/*!
	 * @return Void - Adds to the $this->mPage variable
	 */
	function LoadDeepBlueSection() {
		// Load the receipt link if in transit
		if($this->mOrder->GetStatus()->IsInTransit()) {
		$receipt = ' - [<a href="'.$this->mBaseDir.'/view/OrderReceiptView.php?orderId='.$this->mOrder->GetOrderId().'" target="_blank" style="text-decoration: none;">View Receipt</a>]';
		} else {
			$receipt = '';
		}

		// Load the heading for the order
		$this->mPage .= <<<EOT
			<h1>Sales Order - ECHO{$this->mOrder->GetOrderId()} - {$this->mOrder->GetStatus()->GetDescription()}{$receipt}</h1>
EOT;
	} // End LoadDeepBlueSection

	//! Generates the <select> part of the order form with the correct status selected
	/*!
	 * @return - String - The HTML code for the select
	 */
	function LoadOrderStatusSection() {
		// You can only change the status if the order is authorised
		if($this->mOrder->GetStatus()->IsAuthorised()) {
			// Construct order statuses options
			$allStatuses = $this->mOrderStatusController->GetAll();
			$orderStatusOptions = '<select name="orderStatus" onchange="OrderStatusEventHandler(this.form.orderStatus[this.selectedIndex].value)">';
			foreach($allStatuses as $orderStatus) {
				// The option is selected if it matches the current order's status
				if($orderStatus->GetStatusId() == $this->mOrder->GetStatus()->GetStatusId()) {
					$selected = 'selected';
				} else {
					$selected = '';
				}
				// Disable those that shouldn't be allowed (Eg. Can't change to failed)
				if($orderStatus->GetDescription() == 'In Transit - Charged' || $orderStatus->GetDescription() == 'Failed' || $orderStatus->GetDescription() == 'Awaiting Authorisation') {
					$disabled = 'disabled="disabled"';
				} else {
					$disabled = '';
				}
				// Load the <option>
				$orderStatusOptions .= '<option value="'.$orderStatus->GetStatusId().'" '.$selected.' '.$disabled.'>'.$orderStatus->GetDescription().'</option>';
			}
			$orderStatusOptions .= '</select>';
		} else {
			// If the order has been shipped/cancelled/failed then just display the description
			$orderStatusOptions = $this->mOrder->GetStatus()->GetDescription();
		}
		return $orderStatusOptions;
	} // End LoadOrderStatusSection

	function LoadTrackingSection() {
		if($this->mOrder->GetStatus()->IsInTransit()) {
			if(trim($this->mOrder->GetTrackingNumber())) {
				if(trim($this->mOrder->GetCourier()->GetDisplayName()) == 'Royal Mail') {
					$trackUrl = $this->mOrder->GetCourier()->GetTrackingUrl();
				} else {
					$trackUrl = $this->mOrder->GetCourier()->GetTrackingUrl().$this->mOrder->GetTrackingNumber();
				}
				$retStr = $this->mOrder->GetTrackingNumber();
				$retStr .= ' - [<a href="'.$trackUrl.'" target="_blank">Track Order</a>]';
				return $retStr;
			} else {
				return 'NO  ';
			}
		} elseif($this->mOrder->GetStatus()->IsAuthorised()) {
			$retStr = '<input type="text" name="trackingNumber" id="trackingNumber" />';
			return $retStr;
		}
	}

	function LoadCourierSection() {
		$orderCourierOptions = '';
		if($this->mOrder->GetStatus()->IsAuthorised()) {
			// Construct couriers options
			$allCouriers = $this->mCourierController->GetAll ();
			$orderCourierOptions = '<select name="courier">';
			foreach ( $allCouriers as $courier ) {
				if ($courier->GetCourierId () == $this->mOrder->GetCourier ()->GetCourierId ()) {
					$selected = 'selected';
				} else {
					$selected = '';
				}
				$orderCourierOptions .= '<option value="' . $courier->GetCourierId () . '" ' . $selected . ' >' . $courier->GetDisplayName () . '</option>';
			}
			$orderCourierOptions .= '</select>';
		} else {
			$orderCourierOptions .= $this->mOrder->GetCourier()->GetDisplayName();
		}
		return $orderCourierOptions;
	}

	function LoadDispatchSection() {
		$dispatchEstimateOptions = '';
		if($this->mOrder->GetStatus()->IsAuthorised()) {
			// Construct dispatch estimate options
			$allDispatchDates = $this->mDispatchDateController->GetAllDispatchDates ();
			$dispatchEstimateOptions .= '<select name="dispatchEstimate">';
			foreach ( $allDispatchDates as $dispatchDate ) {
				if ($dispatchDate->GetDispatchDateId () == $this->mOrder->GetDispatchDate ()->GetDispatchDateId ()) {
					$selected = 'selected';
				} else {
					$selected = '';
				}
				$dispatchEstimateOptions .= '<option value="' . $dispatchDate->GetDispatchDateId () . '" ' . $selected . ' >' . $dispatchDate->GetDisplayName () . '</option>';
			}
			$dispatchEstimateOptions .= '</select>';
		} else {
			$dispatchEstimateOptions .= $this->mOrder->GetDispatchDate()->GetDisplayName();
		}
		return $dispatchEstimateOptions;
	}

	//! Loads the Order No, Date, Status, Delivery Method, Weight, Courier, Tracking No & Dispatch Estimate
	function LoadOrderDetails() {
		$this->mOrderStatusController = new OrderStatusController ( );
		$this->mCourierController = new CourierController ( );
		$this->mDispatchDateController = new DispatchDateController ( );

		// Make the order date readable
		$orderDate = date('D jS M Y \a\t G:ia',$this->mOrder->GetCreatedDate());

		// Order Weight
		try {
			$weight = $this->mOrder->GetBasket()->GetWeight();
		} catch(Exception $e) {
			$weight = 0;
		}

		// If the order has been shipped, make the order shipped section
		if(NULL != $this->mOrder->GetStatus()->IsInTransit()) {
			$orderShipped = '<label for="orderDate">Shipped Date</label><span class="falseInput">'.date('D jS M Y \a\t G:ia',$this->mOrder->GetShippedDate()).'</span><br />';
		} else {
			$orderShipped = '';
		}

		// Construct the options for reasons an order might be cancelled
		$reasonForCancelOptions = '
			<option value="NULL">Choose a Reason</option>
			<option value="discontinued">Discontinued</option>
			<option value="tempOutOfStock">Temp Out Of Stock</option>
			<option value="waitedTooLong">Waited Too Long</option>
			<option value="noLongerRequired">No Longer Required</option>
			<option value="dontShipThere">Don\'t Ship There</option>
			<option value="unableToContact">Unable To Contact</option>
			<option value="otherCancel">Other</option>';

		// Only display the affiliate part if an affiliate was used
		$affiliateSection = '';
		$this->mPage .= <<<EOT
			<label for="orderDate">Order Date</label>
				<span class="falseInput">{$orderDate}</span><br />
			<label for="orderDate">Order Weight</label>
				<span class="falseInput">{$weight}g</span><br />
			{$orderShipped}
			<label for="deliveryMethod">Delivery Method</label>
				<span class="falseInput">{$this->mOrder->GetPostageMethod()->GetDisplayName()}</span>
			<br />
			<label for="orderStatus">Status</label> {$this->LoadOrderStatusSection()}
EOT;
		// Only show the update button if it makes sense
		if ($this->mShowUpdateForm) {
			$this->mPage .= '<input type="submit" value="Update" id="saveChangesOrderFormButton" onclick="return UpdateOrderButtonHandler()" />';
		} else {
			$this->mPage .= '';
		}
		$this->mPage .= <<<EOT
			<br />
			<!-- Reason For Cancelling DropDown -->
			<div id="reasonForCancelContainer" style="display: none;">
				<label for="reasonForCancel">Reason</label>
					<select name="reasonForCancel" id="reasonForCancel" onchange="OrderStatusEventOtherHandler(this.form.reasonForCancel[this.selectedIndex].value)">
						{$reasonForCancelOptions}
					</select>
			</div>
			<!-- The "other" text entry when none of the cancel options is appropriate -->
			<div id="reasonForCancelOtherContainer" style="display: none;">
				<label for="reasonForCancelOther">Other</label>
					<input name="reasonForCancelOther" id="reasonForCancelOther" type="text" value="Enter Reason For Cancellation" />
			</div>
			{$affiliateSection}
			<label for="trackingNumber">Tracking Number</label>{$this->LoadTrackingSection()}

			<br />
			<label for="courier">Courier</label>
				{$this->LoadCourierSection()}
			<br />
			<label for="dispatchEstimate">Dispatch Estimate</label>{$this->LoadDispatchSection()}
			<br />
EOT;
	} // End LoadOrderDetails


	//! Loads Staff Name, Brochure and Notes section
	function LoadMisc() {
		$this->mPage .= (trim ( $this->mOrder->GetStaffName () ) ? '<strong>Staff</strong>: ' . $this->mOrder->GetStaffName () . '' : '<strong>Staff</strong>: Web Order');
		#$this->mPage .= ($this->mOrder->GetBrochure () ? ' | <strong>Brochure</strong>: Yes' : ' | <strong>Brochure</strong>: No');
		#$this->mPage .= ($this->mOrder->GetCustomer()->GetFirstOrder() ? ' | <strong>First Order</strong>: Yes' : ' | <strong>First Order</strong>: No');
		$this->mPage .= (trim ( $this->mOrder->GetReferrer()->GetDescription()) ? ' | <strong>Referrer: </strong>'.$this->mOrder->GetReferrer()->GetDescription() : '');
		if($this->mOrder->GetCustomer()->GetEmail() == '') {
			$this->mPage .= ' | <strong>Shipped Orders:</strong> UNKNOWN';
		} else {
			$this->mPage .= ' | <strong>Shipped Orders:</strong> '.$this->mOrder->GetCustomer()->GetOrderCount();
		}
		$this->mPage .= (trim ( $this->mOrder->GetNotes () ) ? ' <br><strong>Notes</strong>: ' . $this->mValidationHelper->MakeSafe ( $this->mOrder->GetNotes () ) : ' | <strong>Notes</strong>: None');
		$this->mPage .= '<br /><br />';
		// TEMP - BODITRONICS OFFER
		if($this->mOrder->GetCatalogue()->GetCatalogueId()== 120 && $this->mOrder->GetTotalPrice() > 30 && $this->mOrder->GetTotalPrice() < 40) {
			$this->mPage .= '<h2>-----------| FREE BODITRONICS SHAKER |-----------</h2><br>';
		}

	} // End LoadMisc

	//! Loads the contact details needed (eg. email, telephone no, name etc.) needed for non-in-transit orders
	function LoadContactDetails() {
		$emailHref = 'mailto:'.$this->mOrder->GetCustomer()->GetEmail().'?subject='.$this->mOrder->GetCatalogue()->GetDisplayName().' Order ECHO'.$this->mOrder->GetOrderId();
		// Phone numbers and email
		$this->mPage .= '<strong>Contact Details</strong><br />';
		$this->mPage .= '<label><strong>Customer Name: </strong></label>';
		$this->mPage .= '<a href="'.$this->mBaseDir.'/view/CustomerHistoryView.php?id='.$this->mOrder->GetCustomer()->GetEmail().'" target="_blank">';
		$this->mPage .= (trim($this->mOrder->GetCustomer()->GetTitle()) 	? ucwords($this->mOrder->GetCustomer()->GetTitle()).' '		: '');
		$this->mPage .= (trim($this->mOrder->GetCustomer()->GetFirstName())	? ucwords($this->mOrder->GetCustomer()->GetFirstName()).' ' : '');
		$this->mPage .= (trim($this->mOrder->GetCustomer()->GetLastName()) 	? ucwords($this->mOrder->GetCustomer()->GetLastName()) 		: '');
		$this->mPage .= '</a>';
		$this->mPage .= '<br />';
		$this->mPage .= (trim ( $this->mOrder->GetCustomer()->GetDaytimeTelephone()) ? '<label><strong>Daytime Tel: </strong></label>'.$this->mOrder->GetCustomer()->GetDaytimeTelephone().'<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetCustomer()->GetMobileTelephone()) ? '<label><strong>Mobile Tel: </strong></label>'.$this->mOrder->GetCustomer ()->GetMobileTelephone () . '<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetCustomer()->GetEmail()) ? '<label><strong>Email: </strong></label><a href="'.$emailHref.'">'.$this->mOrder->GetCustomer()->GetEmail().'</a><br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetShippingAddress()->GetCountry()->GetDescription()) ? '<label><strong>Country: </strong></label>'.$this->mOrder->GetShippingAddress()->GetCountry()->GetDescription().'<br />' : '');
		$this->mPage .= '<br />';
		$this->LoadAddresses(true);
		$this->mPage .= '</div>';
	}

	//! Load the customer's billing and shipping details - including phone numbers and email
	function LoadAddresses($screenOnly=false) {

//		if(!$screenOnly) {
		// Billing Address
		$this->mPage .= <<<EOT
			<strong>Billing Details</strong><br />
EOT;
		$this->mPage .= (trim ( $this->mOrder->GetBillingAddress ()->GetLine1 () ) ? ucwords($this->mOrder->GetBillingAddress()->GetLine1()).'<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetBillingAddress ()->GetLine2 () ) ? ucwords($this->mOrder->GetBillingAddress()->GetLine2()).'<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetBillingAddress ()->GetLine3 () ) ? ucwords($this->mOrder->GetBillingAddress()->GetLine3()).'<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetBillingAddress ()->GetCounty () ) ? ucwords($this->mOrder->GetBillingAddress()->GetCounty()).'<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetBillingAddress ()->GetPostcode () ) ? strtoupper($this->mOrder->GetBillingAddress()->GetPostcode()).'<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetBillingAddress ()->GetCountry ()->GetDescription () ) ? ucwords($this->mOrder->GetBillingAddress()->GetCountry()->GetDescription()).'<br />' : '');

		// Phone numbers and email
	#	$this->mPage .= '<br /><strong>Shipping Details</strong><br />';
	#	$this->mPage .= (trim ( $this->mOrder->GetCustomer()->GetDaytimeTelephone()) ? '<strong>Daytime Tel</strong>: '.$this->mOrder->GetCustomer()->GetDaytimeTelephone().'<br />' : '');
	#	$this->mPage .= (trim ( $this->mOrder->GetCustomer()->GetMobileTelephone()) ? '<strong>Mobile Tel</strong>: '.$this->mOrder->GetCustomer ()->GetMobileTelephone () . '<br />' : '');
	#	$this->mPage .= (trim ( $this->mOrder->GetCustomer()->GetEmail()) ? '<strong>Email: </strong>'.$this->mOrder->GetCustomer ()->GetEmail () . '<br />' : '');
	#	$this->mPage .= '<br />';
//		}

		// Shipping Address
		$this->mPage .= '<br /><div class="screenOnly"><strong>Delivery Address</strong><br />';
		$this->mPage .= (trim ( $this->mOrder->GetCustomer ()->GetTitle () ) ? ucwords($this->mOrder->GetCustomer()->GetTitle()).' ' : '');
		$this->mPage .= (trim ( $this->mOrder->GetCustomer ()->GetFirstName () ) ? ucwords($this->mOrder->GetCustomer()->GetFirstName()).' ' : '');
		$this->mPage .= (trim ( $this->mOrder->GetCustomer ()->GetLastName () ) ? ucwords($this->mOrder->GetCustomer()->GetLastName()) : '');
		$this->mPage .= (trim ( $this->mOrder->GetShippingAddress ()->GetCompany () ) ? '<br />'.ucwords($this->mOrder->GetShippingAddress()->GetCompany()).'<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetShippingAddress ()->GetLine1 () ) ? '<br />'.ucwords($this->mOrder->GetShippingAddress()->GetLine1()).'<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetShippingAddress ()->GetLine2 () ) ? ucwords($this->mOrder->GetShippingAddress()->GetLine2()).'<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetShippingAddress ()->GetLine3 () ) ? ucwords($this->mOrder->GetShippingAddress()->GetLine3()).'<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetShippingAddress ()->GetCounty () ) ? ucwords($this->mOrder->GetShippingAddress()->GetCounty()).'<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetShippingAddress ()->GetPostcode () ) ? strtoupper($this->mOrder->GetShippingAddress()->GetPostcode()).'<br />' : '');
		$this->mPage .= (trim ( $this->mOrder->GetShippingAddress ()->GetCountry ()->GetDescription () ) ? ucwords($this->mOrder->GetShippingAddress()->GetCountry()->GetDescription()).'<br />' : '');

		// Order number
		#$this->mPage .= '<br />ECHO' . $this->mOrder->GetOrderId ();
	} // End LoadAddresses


	//! Loads the table displaying the basket for the order form
	function LoadBasketDetails() {
		// Decide whether the order has been completed (In Transit/Cancelled/Failed)
		if (! $this->mOrder->GetStatus()->IsComplete()) {
			$shipHeading = 'Ship?';
		} else {
			$shipHeading = 'Shipped';
		}
		// Display the first row of the table with headings
		$this->mPage .= <<<EOT
			<table>
				<tr>
					<th class="left" colspan="2">Product</th><th>Price</th><th>{$shipHeading}</th>
				</tr>
EOT;
		// Get the packages & loop over them
		foreach($this->mOrderItemController->GetPackagesForOrder($this->mOrder) as $packageItem) {

			// Display the row
			$this->mPage .= '<tr>';
			$this->mPage .= '<td id="productColumn" colspan="2"><strong>'.$packageItem->GetDisplayName().'</strong><br />';

			// Get the contents of this package
			foreach($this->mOrderItemController->GetContentsOfPackageItem($packageItem) as $packageProductItem) {
				$this->mPage .= '- '.$packageProductItem->GetDisplayName().'<br />';
			}
			// Upgrades
			foreach($this->mOrderItemController->GetUpgradesOfPackageItem($packageItem) as $packageUpgradeItem) {
				$this->mPage .= '- <strong>Upgrade</strong>: '.$packageUpgradeItem->GetDisplayName().'<br />';
			}

			$this->mPage .= '</td>';
			$this->mPage .= '<td id="unitPriceColumn">&pound;'.$packageItem->GetPackagePrice().'</td>';
			$this->mPage .= '<td id="shippedColumn">'.$this->LoadShippingSection($packageItem).'</td>';

			$this->mPage .= '</tr>';
		} // End Looping Over Packages

		// Get the products and loop over them
		foreach($this->mOrderItemController->GetProductsForOrder($this->mOrder) as $productItem) {
			$this->mPage .= '<tr>';
			if($this->mOrder->GetStatus()->IsAuthorised()) {
			$this->mPage .= '<td id="editOrderItemColumn">
								<a id="'.$productItem->GetOrderItemId().'" class="editOrderItemIcon">
									EDIT
								</a>
							</td>';
			} else {
				$this->mPage .= '<td id="editOrderItemColumn"></td>';
			}
			// Indicate if its a non stock item
			if($productItem->IsNonStockItem()) {
				$this->mPage .= '<td class="orderItem" 		 id="orderItem'.$productItem->GetOrderItemId().'"><i><b>3-5 Day:</b> '.$productItem->GetDisplayName().'</i></td>';
			} else {
				$this->mPage .= '<td class="orderItem" 		 id="orderItem'.$productItem->GetOrderItemId().'">'.$productItem->GetDisplayName().'</td>';
			}
			$this->mPage .= '<td class="unitPriceColumn" id="unitPriceColumn'.$productItem->GetOrderItemId().'">&pound;'.$productItem->GetPrice().'</td>';
			$this->mPage .= '<td class="shippedColumn"   id="shippedColumn'.$productItem->GetOrderItemId().'">'.$this->LoadShippingSection($productItem).'</td>';
			$this->mPage .= '</tr>';
		}

		// Absolute total - The total displayed as Sub-Total + Postage
		$absTotal = number_format ( $this->mOrder->GetTotalPrice () + $this->mOrder->GetTotalPostage (), 2 );

		// If the order is in transit, we can display the total actually taken
		if($this->mOrder->GetStatus()->IsInTransit()) {
			$totalTakenHeading 	= '<br /><strong>Total Actually Taken:</strong>';
			$totalTaken			= '<strong>&pound;'.$this->mPresentationHelper->Money($this->mOrder->GetActualTaken()).'</strong>';
		} else {
			$totalTakenHeading  = '';
			$totalTaken			= '';
		}

		// Display the bottom (totals) row of the table
		$this->mPage .= <<<EOT
				<tr>
					<td class="right" colspan="2"><strong>Sub-total:</strong><br /><strong>Delivery:</strong><br /><strong>Total:</strong>{$totalTakenHeading}</td>
					<td class="right">	&pound;{$this->mPresentationHelper->Money($this->mOrder->GetTotalPrice())}<br />
										&pound;<span id="totalPostageContainer">{$this->mPresentationHelper->Money($this->mOrder->GetTotalPostage())}</span><br />
										&pound;{$absTotal}<br />
										{$totalTaken}
										</td>
					<td>[<a href="#" id="removePostage">Remove</a>]</td>
				</tr>
			</table>
EOT;
	} // End LoadBasketDetails

	function LoadEditOrderItemsForm() {
		$this->mPage .= <<<EOT
			<div id="editOrderItemsContainer" title="Change Order Item">
				<form id="editOrderItemsForm">
					<label for="name">Sage Code: </label>
					<input type="text" name="sageCodeChange" id="sageCodeChange" />
					<input type="hidden" name="sageCodeChangeHidden" id="sageCodeChangeHidden" />
					<input type="hidden" name="sageCodeChangeDisplayHidden" id="sageCodeChangeDisplayHidden" />
					<input type="hidden" name="sageCodePriceChangeHidden" id="sageCodePriceChangeHidden" />
					<div id="sageCodeLookupArea"></div>
				</form>
			</div>
EOT;
	}

	function LoadShippingSection($orderItem) {
		// Make shipped section - Whether to say Yes/No for each product item
		// If cancelled then always 'No'. If a product item isnt shipped, then if the order is in transit it says 'No' otherwise it displays a checkbox (assumed Authorised)
		if ($this->mOrder->GetStatus()->IsCancelled() || $this->mOrder->GetStatus()->IsFailed()) {
			$shippedSection = 'No';
		} else {
			if (!$orderItem->GetShipped()) {
				if ($this->mOrder->GetStatus()->IsInTransit()) {
					$shippedSection = 'No';
				} else {
					$shippedSection = '<input type="checkbox" name="orderItemShip'.$orderItem->GetOrderItemId().'" id="orderItemShip'.$orderItem->GetOrderItemId().'" checked="checked" />';
				}
			} else {
				$shippedSection = 'Yes';
			}
		}
		return $shippedSection;
	} // End LoadShippingSection

} // End OrderEditView


?>