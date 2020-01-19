<?php
require_once ('../autoload.php');
require_once ('GoogleCheckoutLibrary/googlerequest.php');

class OrderFormEditHandler extends Handler {

	var $mOrder;
	var $mClean;
	var $mShipList = array ();

	function __construct($orderId) {
		$this->mOrder = new OrderModel ( $orderId );
		$this->mRegistry  = Registry::getInstance ();
		$this->mLocalMode = $this->mRegistry->localMode;
		$this->mDebugMode = $this->mRegistry->debugMode;
		$this->mMoneyHelper = new MoneyHelper ( );
		if ($this->mLocalMode) {
			$this->mOrderPrefix = 'ECHO';
		} else {
			$this->mOrderPrefix = 'ECHO';
		}
	}

	function Validate($postArr) {
		foreach ( $postArr as $key => $value ) {
			switch ($key) {
				case 'orderStatus' :
				case 'courier' :
				case 'dispatchEstimate' :
				case 'trackingNumber' :
				case 'orderId' :
				case 'reasonForCancel' :
				case 'reasonForCancelOther':
					$this->mClean [$key] = $value;
					break;
				default :
					if (strstr ( $key, 'orderItemShip' )) {
						$this->AddToShipList($key);
					}
					break;
			}
		}
	}

	//! Adds an order item to the shipping list - expects string like: orderItemShipID-GOES-HERE
	function AddToShipList($value) {
		$explodeArr = explode('orderItemShip',$value);
		$orderItemId = $explodeArr[1];
		$orderItem = new OrderItemModel($orderItemId);
		$this->mShipList [] = $orderItem;
	}

	function SaveOrder() {
		switch($this->mOrder->GetGoogleCheckout()) {
			case 1:
				$this->SaveGoogleOrder();
			break;
			case 0:
				if($this->mOrder->GetPaypalOrder()) {
					$this->SavePaypalOrder();
				} else {
					$this->SaveProtxOrder();
				}
			break;
		}
	} // End SaveOrder

	function SaveGoogleOrder() {
		// Controllers
		$this->mOrderController 	= new OrderController;
		$this->mOrderItemController	= new OrderItemController;

		// New Google Request Object
		$this->mGrequest = new GoogleRequest($this->mRegistry->GoogleCheckoutMerchantId,$this->mRegistry->GoogleCheckoutMerchantKey,$this->mRegistry->GoogleCheckoutMode,'GBP');

		// Misc Details for the order
		$dispatchDate 	= new DispatchDateModel ( $this->mClean ['dispatchEstimate'] );
		$orderStatus 	= new OrderStatusModel ( $this->mClean ['orderStatus'] );
		$courier 		= new CourierModel ( $this->mClean ['courier'] );
		$this->mCustomer = $this->mOrder->GetCustomer ();

		// Local/Production Mode
		if ($this->mLocalMode) {
			$orderPrefix = 'ECHO';
		} else {
			$orderPrefix = 'ECHO';
		}

		// Initialise
		$errorMsg = '';
		$errorMsg .= 'Running Total: 0<br />';

		// Loop over Items to get actual money to take
		$runningTotal = 0;
		foreach($this->mShipList as $orderItem) {
			if($orderItem->GetPackageId()) {

				// Contents
				foreach($this->mOrderItemController->GetContentsOfPackageItem($orderItem) as $packageProductItem) {
					$runningTotal += $packageProductItem->GetPrice();
				}

				// Upgrades
				foreach($this->mOrderItemController->GetUpgradesOfPackageItem($orderItem) as $packageUpgradeItem) {
					$runningTotal += $packageUpgradeItem->GetPrice();
				}

				// And the package itself
				$runningTotal += $orderItem->GetPrice();

			} elseif(!$orderItem->GetPackageProduct() && !$orderItem->GetPackageUpgrade()) {
				// Just a normal product
				$runningTotal += $orderItem->GetPrice();
			}
		}
		// Don't forget the postage
		$runningTotal += $this->mOrder->GetTotalPostage();

		// Deal with what you're actually being asked to do (Ship/Cancel etc.)
		switch ($orderStatus->GetDescription()) {
			case 'In Transit' :
				// Send Charge Request
				$response = $this->mGrequest->SendChargeOrder($this->mOrder->GetTransactionId(),$runningTotal);

				// If Google didn't receive the order request then definately DONT ship! (200 = HTTP OK reply)
				if($response[0] != 200) { die('ORDER HAS NOT BEEN SHIPPED'.$response[1]); }

				// Set tracking code with Google
				$this->mGrequest->SendTrackingData($this->mOrder->GetTransactionId(),'Other',$this->mClean['trackingNumber']);

				// Ship the correct products
				$googleOrderItems = array();
				foreach($this->mShipList as $orderItem) {
					if($orderItem->GetPackageId()) {

						// Contents
						foreach($this->mOrderItemController->GetContentsOfPackageItem($orderItem) as $packageProductItem) {
							$packageProductItem->SetShipped(1);
						}

						// Upgrades
						foreach($this->mOrderItemController->GetUpgradesOfPackageItem($orderItem) as $packageUpgradeItem) {
							$packageUpgradeItem->SetShipped(1);
						}

						// And the package itself
						$orderItem->SetShipped(1);

					} elseif(!$orderItem->GetPackageProduct() && !$orderItem->GetPackageUpgrade()) {
						// Just a normal product
						$orderItem->SetShipped(1);
					}
					// Update with Google
					$newGoogleItem = new GoogleItem($orderItem->GetDisplayName(),$orderItem->GetDisplayName(),1,$orderItem->GetPrice());
					$googleOrderItems[] = $newGoogleItem;
				}
				// Send the update
				$this->mGrequest->SendShipItems($this->mOrder->GetTransactionId(),$googleOrderItems);

				// Update any other details
				$this->mOrder->SetDispatchDate	($dispatchDate);
				$this->mOrder->SetShippedDate	(time());
				$this->mOrder->SetCourier		($courier);
				$this->mOrder->SetTrackingNumber($this->mClean ['trackingNumber']);

				// Send the emails
				#if(!$this->mLocalMode) {
					$this->PrepareAndSendEmail('In Transit');
				#}
				// Update the order status
				$this->mOrder->SetStatus($orderStatus);

				// Create customer reciept
				echo '<SCRIPT LANGUAGE="javascript">
						<!--
						window.open(\'../view/OrderReceiptView.php?orderId='.$this->mOrder->GetOrderId().'\');
						-->
						</SCRIPT>';
				//echo $reciept->LoadDefault($this->mOrder->GetOrderId());
			break; // End case In Transit
			// ***** Deal with CANCELLING orders *****
			case 'Cancelled By User' :
			case 'Cancelled By Merchant' :

				$reason 	= substr($this->mClean['reasonForCancel'],0,130);	// 140 is Max chars, keep it safe with 130
				$comment 	= substr($this->mClean['reasonForCancel'],0,130);	// 140 is Max chars, keep it safe with 130

				// Send Cancel Request
				$response = $this->mGrequest->SendCancelOrder($this->mOrder->GetTransactionId(),$reason,$comment);

				// If Google didn't receive the cancel request, say so
				if($response[0] != 200) { die('ORDER HAS NOT BEEN CANCELLED'.$response[1]); }

				switch ($this->mClean ['reasonForCancel']) {
					case 'discontinued' :
						if(!$this->PrepareAndSendEmail ( 'CancelledDisc' )) { $emailFailed = true; }
						break;
					case 'tempOutOfStock' :
						if(!$this->PrepareAndSendEmail ( 'CancelledTemp' )) { $emailFailed = true; }
						break;
					case 'dontShipThere' :
						if(!$this->PrepareAndSendEmail ( 'CancelledShip' )) { $emailFailed = true; }
						break;
					case 'noLongerRequired':
						if(!$this->PrepareAndSendEmail('CancelledNoLongerReq')) { $emailFailed = true; }
					break;
					case 'waitedTooLong':
						if(!$this->PrepareAndSendEmail('CancelledTooLong')) { $emailFailed = true; }
					break;
				}
				if($this->SendEmail()) {
					// Display Confirmation
					echo '<h1 style="font-family: Arial; font-size: 12pt;">Email sent.</h1>';
					// Reload the order
					echo '<a href=\'' . $this->mRegistry->viewDir . '/OrdersEditView.php?id=' . $this->mOrder->GetOrderId () . '\' style="font-family: Arial; font-size: 10pt;">Back to order.</a>';
				} else {
					// Display Error
					echo '<h1 style="font-family: Arial; font-size: 12pt; color: #FF0000;">Email was <strong>NOT</strong> sent but the order has been updated.</h1>';
					// Reload the order
					echo '<a href=\'' . $this->mRegistry->viewDir . '/OrdersEditView.php?id=' . $this->mOrder->GetOrderId () . '\' style="font-family: Arial; font-size: 10pt;">Back to order.</a>';
				}
				break; // End Cancelled
			case 'Authorised' :
				// Update the dispatch date
				$dispatchDate = new DispatchDateModel ( $this->mClean ['dispatchEstimate'] );
				$this->mOrder->SetDispatchDate ( $dispatchDate );
				$this->mDispatchDateDescription = $dispatchDate->GetDisplayName ();
				if ($this->PrepareAndSendEmail ('Authorised')) {
					// Display Confirmation
					echo '<h1 style="font-family: Arial; font-size: 12pt;">Email sent.</h1>';
					// Reload the order
					echo '<a href=\'' . $this->mRegistry->viewDir . '/OrdersEditView.php?id=' . $this->mOrder->GetOrderId () . '\' style="font-family: Arial; font-size: 10pt;">Back to order.</a>';
				} else {
					// Display Error
					echo '<h1 style="font-family: Arial; font-size: 12pt; color: #FF0000;">Email was <strong>NOT</strong> sent but the order has been updated.</h1>';
					// Reload the order
					echo '<a href=\'' . $this->mRegistry->viewDir . '/OrdersEditView.php?id=' . $this->mOrder->GetOrderId () . '\' style="font-family: Arial; font-size: 10pt;">Back to order.</a>';
				}
				break;
			case 'Failed' :
				// Do nothing - staff shouldn't
				break;
		}
	} // End SaveGoogleOrder

	function SaveProtxOrder() {
		$this->mOrderController 	= new OrderController;
		$this->mOrderItemController	= new OrderItemController;
		$this->mCourierController	= new CourierController;
		$dispatchDate 				= new DispatchDateModel($this->mClean['dispatchEstimate']);
		$orderStatus 				= new OrderStatusModel($this->mClean['orderStatus']);
		$this->mCustomer 			= $this->mOrder->GetCustomer();

		// A bit smarter - all numberics means DPD, anything else is assumed Royal Mail (BR123... etc)
		$testTrack = $this->mClean['trackingNumber'];
		// Remove Spaces
		$testTrack = str_replace(' ','',$testTrack);
		if(is_numeric($testTrack)) {
			// DPD
			$courier = $this->mCourierController->GetDPD();
		} else {
			// Royal Mail
			$courier = $this->mCourierController->GetRoyalMail();
		}

		// Local/Production Mode
		if ($this->mLocalMode) {
			$orderPrefix = 'ECH0';
		} else {
			$orderPrefix = 'ECH0';
		}

		// Initialise
		$errorMsg = '';
		$errorMsg .= 'Running Total: 0<br />';

		// Loop over Items to get actual money to take
		$runningTotal = 0;
		foreach($this->mShipList as $orderItem) {
			if($orderItem->GetPackageId()) {

				// Contents
				foreach($this->mOrderItemController->GetContentsOfPackageItem($orderItem) as $packageProductItem) {
					$runningTotal += $packageProductItem->GetPrice();
				}

				// Upgrades
				foreach($this->mOrderItemController->GetUpgradesOfPackageItem($orderItem) as $packageUpgradeItem) {
					$runningTotal += $packageUpgradeItem->GetPrice();
				}

				// And the package itself
				$runningTotal += $orderItem->GetPrice();

			} elseif(!$orderItem->GetPackageProduct() && !$orderItem->GetPackageUpgrade()) {
				// Just a normal product
				$runningTotal += $orderItem->GetPrice();
			}
		}
		// Don't forget the postage
		$runningTotal += $this->mOrder->GetTotalPostage();

		// Deal with what you're actually being asked to do (Ship/Cancel etc.)
		switch ($orderStatus->GetDescription ()) {
			case 'In Transit' :
				// Make a request string
				$shipOrderRequest = $this->mOrderController->ConstructShipRequest ( $this->mOrder, $runningTotal, $orderPrefix );
				#cho 'Order Request: '.$shipOrderRequest.'<br /><br />';
				// Perform the request
				$shipOrderResult = $this->mOrderController->SendOrderRequest ( $shipOrderRequest, $this->mRegistry->paymentReleaseUrl );
				#echo 'URL: '.$this->mRegistry->paymentReleaseUrl.'<br />';
				// Format it properly
				$shipOrderResultArr = $this->mOrderController->FormatProtxResponse ( $shipOrderResult );

				if(!isset($shipOrderResultArr['Status'])) {
					die('Did not get a result from Sage Pay.');
				}

				// ***** Deal with SHIPPING orders *****
				switch ($shipOrderResultArr ['Status']) {
					case 'OK' :
						// Ship the correct products
						foreach($this->mShipList as $orderItem) {
							if($orderItem->GetPackageId()) {

								// Contents
								foreach($this->mOrderItemController->GetContentsOfPackageItem($orderItem) as $packageProductItem) {
									$packageProductItem->SetShipped(1);
								}

								// Upgrades
								foreach($this->mOrderItemController->GetUpgradesOfPackageItem($orderItem) as $packageUpgradeItem) {
									$packageUpgradeItem->SetShipped(1);
								}

								// And the package itself
								$orderItem->SetShipped(1);

							} elseif(!$orderItem->GetPackageProduct() && !$orderItem->GetPackageUpgrade()) {
								// Just a normal product
								$orderItem->SetShipped(1);
							}
						}
						// Update any other details
						$this->mOrder->SetDispatchDate ( $dispatchDate );
						$this->mOrder->SetShippedDate ( time () );
						$this->mOrder->SetCourier ( $courier );
						$this->mOrder->SetTrackingNumber ( $this->mClean ['trackingNumber'] );
						// Send the emails
						if(!$this->mLocalMode) {
							$this->PrepareAndSendEmail('In Transit');
						}
						// Update the order status
						$this->mOrder->SetStatus($orderStatus);
						// Create customer reciept
						echo '<SCRIPT LANGUAGE="javascript">
								<!--
								window.open(\'../view/OrderReceiptView.php?orderId='.$this->mOrder->GetOrderId().'\');
								-->
								</SCRIPT>';
						//echo $reciept->LoadDefault($this->mOrder->GetOrderId());
						break;
					case 'INVALID' :
					case 'MALFORMED' :
						echo '<div style="font-family: Arial; font-size: 10pt;"><h2>Error - Order NOT Shipped (Problem with Admin)</h2>';
						echo '<strong>Protx Message:</strong> ' . $shipOrderResultArr ['Status'] . ' - ' . $shipOrderResultArr ['StatusDetail'] . '<br /><br />';
						echo '<strong>Debug Message:</strong> ' . $errorMsg;
						echo '</div>';
						break;
					case 'ERROR' :
						echo '<div style="font-family: Arial; font-size: 10pt;"><h2>Error - Order NOT Shipped (Problem with Sage Pay)</h2>';
						echo '<strong>Protx Message:</strong> ' . $shipOrderResultArr ['Status'] . ' - ' . $shipOrderResultArr ['StatusDetail'] . '<br /><br />';
						echo '</div>';
						break;
					case 'NOTAUTHED' :
						echo '<div style="font-family: Arial; font-size: 10pt;"><h2>Error - Order NOT Shipped (Problem with customer\'s card)</h2>';
						echo '<strong>Protx Message:</strong> ' . $shipOrderResultArr ['Status'] . ' - ' . $shipOrderResultArr ['StatusDetail'] . '<br /><br />';
						echo '<strong>Debug Message:</strong> ' . $errorMsg;
						echo '</div>';
						break;
				}
				break;
			// ***** Deal with CANCELLING orders *****
			case 'Cancelled By User' :
			case 'Cancelled By Merchant' :
				// Make a request string
				$cancelOrderRequest = $this->mOrderController->ConstructCancelRequest ( $this->mOrder, $orderPrefix );
				// Perform the request
				$cancelOrderResult = $this->mOrderController->SendOrderRequest ( $cancelOrderRequest, $this->mRegistry->paymentCancelUrl );
				// Format it properly
				$cancelOrderResultArr = $this->mOrderController->FormatProtxResponse ( $cancelOrderResult );

				switch ($this->mClean ['reasonForCancel']) {
					case 'discontinued' :
						$this->PrepareAndSendEmail ( 'CancelledDisc' );
						break;
					case 'tempOutOfStock' :
						$this->PrepareAndSendEmail ( 'CancelledTemp' );
						break;
					case 'dontShipThere' :
						$this->PrepareAndSendEmail ( 'CancelledShip' );
						break;
					case 'noLongerRequired':
						$this->PrepareAndSendEmail('CancelledNoLongerReq');
					break;
					case 'waitedTooLong':
						$this->PrepareAndSendEmail('CancelledTooLong');
					break;
					case 'unableToContact':
						$this->PrepareAndSendEmail('CancelledUnableToContact');
					break;
					case 'otherCancel':
						$this->PrepareAndSendEmail('CancelledOther',$this->mClean['reasonForCancelOther']);
					break;
				}

				switch ($cancelOrderResultArr ['Status']) {
					case 'OK' :
						$this->SendEmail ();
						echo '<div style="font-family: Arial; font-size: 10pt;"><h1>Order Cancelled</h1>';
						echo '</div>';
						break;
					case 'MALFORMED' :
					case 'INVALID' :
						echo '<div style="font-family: Arial; font-size: 10pt; color: #FF0000;"><h1>Error - Order NOT Cancelled (Problem with Admin)</h1>';
						echo '<strong>Protx Message:</strong> ' . $cancelOrderResultArr ['Status'] . ' - ' . $cancelOrderResultArr ['StatusDetail'] . '<br /><br />';
						echo '</div>';
						break;
					case 'ERROR' :
						echo '<div style="font-family: Arial; font-size: 10pt; color: #FF0000;"><h1>Error - Order NOT Cancelled (Problem with Protx)</h1>';
						echo '<strong>Protx Message:</strong> ' . $cancelOrderResultArr ['Status'] . ' - ' . $cancelOrderResultArr ['StatusDetail'] . '<br /><br />';
						echo '</div>';
						break;
				}
				$this->mOrder->SetStatus ( $orderStatus );
				break;
			case 'Authorised' :
				// Update the dispatch date
				$dispatchDate = new DispatchDateModel ( $this->mClean ['dispatchEstimate'] );
				$this->mOrder->SetDispatchDate ( $dispatchDate );
				$this->mDispatchDateDescription = $dispatchDate->GetDisplayName ();
				if ($this->PrepareAndSendEmail ('Authorised')) {
					// Display Confirmation
					echo '<h1 style="font-family: Arial; font-size: 12pt;">Email sent.</h1>';
					// Reload the order
					echo '<a href=\'' . $this->mRegistry->viewDir . '/OrdersEditView.php?id=' . $this->mOrder->GetOrderId () . '\' style="font-family: Arial; font-size: 10pt;">Back to order.</a>';
				} else {
					// Display Error
					echo '<h1 style="font-family: Arial; font-size: 12pt; color: #FF0000;">Email was <strong>NOT</strong> sent but the order has been updated.</h1>';
					// Reload the order
					echo '<a href=\'' . $this->mRegistry->viewDir . '/OrdersEditView.php?id=' . $this->mOrder->GetOrderId () . '\' style="font-family: Arial; font-size: 10pt;">Back to order.</a>';
				}
				break;
			case 'Failed' :
				// Do nothing - staff shouldn't
				break;
		}
	} // End SaveProtxOrder

	//! Because using Sale rather than Authorize at the moment this just updates the order status when dispatched with a tracking number
	function SavePaypalOrder() {
		$this->mOrderController 	= new OrderController;
		$this->mOrderItemController	= new OrderItemController;
		$this->mCourierController	= new CourierController;
		$dispatchDate 				= new DispatchDateModel($this->mClean['dispatchEstimate']);
		$orderStatus 				= new OrderStatusModel($this->mClean['orderStatus']);
		$this->mCustomer 			= $this->mOrder->GetCustomer();
		$courier = new CourierModel($this->mClean['courier']);

		// Deal with the order
		switch ($orderStatus->GetDescription ()) {
			case 'In Transit':
				// Update any other details
				$this->mOrder->SetDispatchDate ( $dispatchDate );
				$this->mOrder->SetShippedDate ( time () );
				$this->mOrder->SetCourier ( $courier );
				$this->mOrder->SetTrackingNumber ( $this->mClean ['trackingNumber'] );

				// Initialise
				$errorMsg = '';
				$errorMsg .= 'Running Total: 0<br />';

				// Loop over Items to get actual money to take
				$runningTotal = 0;
				foreach($this->mShipList as $orderItem) {
					if($orderItem->GetPackageId()) {

						// Contents
						foreach($this->mOrderItemController->GetContentsOfPackageItem($orderItem) as $packageProductItem) {
							$runningTotal += $packageProductItem->GetPrice();
						}

						// Upgrades
						foreach($this->mOrderItemController->GetUpgradesOfPackageItem($orderItem) as $packageUpgradeItem) {
							$runningTotal += $packageUpgradeItem->GetPrice();
						}

						// And the package itself
						$runningTotal += $orderItem->GetPrice();

					} elseif(!$orderItem->GetPackageProduct() && !$orderItem->GetPackageUpgrade()) {
						// Just a normal product
						$runningTotal += $orderItem->GetPrice();
					}
				}
				// Don't forget the postage
				$runningTotal += $this->mOrder->GetTotalPostage();

				// ***** Deal with SHIPPING orders *****
				foreach($this->mShipList as $orderItem) {
					if($orderItem->GetPackageId()) {

						// Contents
						foreach($this->mOrderItemController->GetContentsOfPackageItem($orderItem) as $packageProductItem) {
							$packageProductItem->SetShipped(1);
						}

						// Upgrades
						foreach($this->mOrderItemController->GetUpgradesOfPackageItem($orderItem) as $packageUpgradeItem) {
							$packageUpgradeItem->SetShipped(1);
						}

						// And the package itself
						$orderItem->SetShipped(1);

					} elseif(!$orderItem->GetPackageProduct() && !$orderItem->GetPackageUpgrade()) {
						// Just a normal product
						$orderItem->SetShipped(1);
					}
				}

				// Send the emails
				$this->PrepareAndSendEmail('In Transit');
				// Update the order status
				$this->mOrder->SetStatus($orderStatus);
				// Create customer reciept
				echo '<SCRIPT LANGUAGE="javascript">
						<!--
						window.open(\'../view/OrderReceiptView.php?orderId='.$this->mOrder->GetOrderId().'\');
						-->
						</SCRIPT>';
			break;
			case 'Authorised' :
				// Update the dispatch date
				$dispatchDate = new DispatchDateModel ( $this->mClean ['dispatchEstimate'] );
				$this->mOrder->SetDispatchDate ( $dispatchDate );
				$this->mDispatchDateDescription = $dispatchDate->GetDisplayName ();
				if ($this->PrepareAndSendEmail ('Authorised')) {
					// Display Confirmation
					echo '<h1 style="font-family: Arial; font-size: 12pt;">Email sent.</h1>';
					// Reload the order
					echo '<a href=\'' . $this->mRegistry->viewDir . '/OrdersEditView.php?id=' . $this->mOrder->GetOrderId () . '\' style="font-family: Arial; font-size: 10pt;">Back to order.</a>';
				} else {
					// Display Error
					echo '<h1 style="font-family: Arial; font-size: 12pt; color: #FF0000;">Email was <strong>NOT</strong> sent but the order has been updated.</h1>';
					// Reload the order
					echo '<a href=\'' . $this->mRegistry->viewDir . '/OrdersEditView.php?id=' . $this->mOrder->GetOrderId () . '\' style="font-family: Arial; font-size: 10pt;">Back to order.</a>';
				}
			break;
			case 'Cancelled By Merchant':
			case 'Cancelled By User':
				$this->mOrder->SetStatus($orderStatus);
			break;
			case 'Failed' :
				// Do nothing - staff shouldn't
			break;
		}
	} // End SavePaypalOrder

	//! Updates the designated IMPORT.TXT file for use by DPD Ship@Ease software
	function UpdateDPD() {
		$feed = 'ECHO'.$this->mOrder->GetOrderId().',';	// Job Reference
		$feed .= '1,';									// Job Type, DPD Outbound = 1
		$feed .= $this->mCustomer->GetCustomerId().',';	// Customer Code
		$feed .= substr($this->mCustomer->GetTitle().$this->mCustomer->GetFirstName().$this->mCustomer->GetLastName(),0,20).',';	// Name
		$feed .= substr($this->mOrder->GetShippingAddress()->GetCompany(),0,35).',';	// Line 1
		$feed .= substr($this->mOrder->GetShippingAddress()->GetLine1(),0,35).',';		// Line 2
		$feed .= substr($this->mOrder->GetShippingAddress()->GetLine2(),0,35).',';		// Town
		$feed .= substr($this->mOrder->GetShippingAddress()->GetCounty(),0,35).',';		// County
		$feed .= substr($this->mOrder->GetShippingAddress()->GetPostcode(),0,8).',';	// Postcode
		$feed .= substr($this->mOrder->GetNotes(),0,25).',';	// Notes / Instructions
		// Service - Worth it?$feed .=
		$feed .= $this->mOrder->GetShippingAddress()->GetCountry()->GetTwoLetter()."\n";	// Country
		$fh = fopen('IMPORT.TXT','w');
		fwrite($fh,$feed);
	}

	//! Prepare the email for emailing to the customer on order status change
	/*!
	 * @param $event - String - The event to prepare the email for, possible values:
	 * Authorised, In Transit, CancelledDisc, CancelledTemp, CancelledShip, CancelledNoLongerReq, CancelledTooLong, CancelledUnableToContact, CancelledOther
	 * @param $reasonForCancellation - String - To be used with 'CancelledOther' - the (user entered) reason for the cancellation
	 */
	function PrepareAndSendEmail($event,$reasonForCancellation='') {

		// Prepare a reason for cancellation
		switch ($event) {
			case 'CancelledDisc':
				$reason = 'The reason your order has been cancelled is that this product has been discontinued.';
			break;
			case 'CancelledTemp':
				$reason = 'The reason your order has been cancelled is that we are unable to guarantee a dispatch date within the next 2 weeks as we are awaiting stock. Please try to re-order after this time.';
			break;
			case 'CancelledShip':
				$reason = 'The reason your order has been cancelled is that we are unable to ship to the address requested.';
			break;
			case 'CancelledNoLongerReq':
				$reason = 'The reason your order has been cancelled is that the item is no longer required';
			break;
			case 'CancelledTooLong':
				$reason = 'The reason your order has been cancelled is that you have waited too long';
			break;
			case 'CancelledUnableToContact':
				$reason = 'The reason your order has been cancelled is that we were unable to contact you';
			break;
			case 'CancelledOther':
				$reason = $reasonForCancellation;
			break;
		}

		// Actually prepare the email
		switch ($event) {
			case 'Authorised' :
				$prefix = 'ECHO';
				$publicOrderView = new PublicOrderView ( );
				$dispatchEstimate = new DispatchDateModel ( $this->mClean ['dispatchEstimate'] );
				$today = date ( 'l jS F Y' );
				$body = 'You Order: ' . $this->mOrderPrefix . $this->mOrder->GetOrderId () . ' has been updated (' . $today . ').<br /><br />';
				$body .= 'Due to unforseen stock levels the dispatch estimate is now: ' . $dispatchEstimate->GetDisplayName () . ' for this order.<br /><br />';
				$body .= $publicOrderView->LoadDefault ( $this->mOrder->GetOrderId () );

				$this->mMail = new PHPMailer;
				$this->mMail->From = "info@echosupplements.com";
				$this->mMail->FromName = "Website Order Update";
				$this->mMail->Subject = "Website Order Update";
				$this->mMail->Host = "smtp.gmail.com";
				$this->mMail->Port = 465;
				$this->mMail->Mailer = "smtp";
				$this->mMail->SMTPAuth = true;
				$this->mMail->Username = "info@echosupplements.com";
				$this->mMail->Password = "bl00dlu5t";
				$this->mMail->Body = $body;
				$this->mMail->SMTPSecure = "ssl"; // option
				$this->mMail->AltBody = $body;
				$this->mMail->AddAddress ( $this->mOrder->GetCustomer()->GetEmail() );
				if($this->mMail->Send()) {
					return true;
				} else {
					return false;
				}
				break;
			case 'In Transit' :$publicOrderView = new PublicOrderView ( );
				$today = date ( 'l jS F Y' );
				$prefix = 'ECHO';
				$body = 'You Order: ' . $this->mOrderPrefix . $this->mOrder->GetOrderId () . ' has been dispatched (' . $today . '). ';
				$body .= '<br />It has been sent using courier: ' . $this->mOrder->GetCourier ()->GetDisplayName () . ' method: ' . $this->mOrder->GetPostageMethod ()->GetDisplayName () . '.';
				$body .= '<br />Delivery guideline: ' . $this->mOrder->GetPostageMethod ()->GetDescription ();
				if ($this->mClean ['trackingNumber'] != '') {
					$body .= ' You can track your order at <a href="' . $this->mOrder->GetCourier ()->GetTrackingUrl () . $this->mClean ['trackingNumber'] . '">this link</a>.<br />';
				}
				if (! $this->mOrder->IsAllItemsShipped ()) {
					$body .= 'Not all items have been shipped and you have <strong>not</strong> been charged for these items. Unshipped Items:<ul>';
					$unshipped = $this->mOrder->GetUnshippedItems();
					foreach ( $unshipped as $orderItem ) {
						$body .= '<li>'.$orderItem->GetDisplayName().'</li>';
					}
					$body .= '</ul><br />The total amount you have been charged is: &pound;' . $this->mOrder->GetActualTaken () . '.<br />';
					$body .= 'If you still require these items you must place a <strong>new</strong> order for them.<br />';
				}
				$body .= '<br />Thank you for shopping with Echo Supplements!<br />';

				// If the order hasn't been delayed and everything is shipped, invite the customer to evaluate us on trustpilot
				$unshipped = $this->mOrder->GetUnshippedItems();
				if(count($unshipped)==0 && $this->mOrder->GetDispatchDate()->GetDispatchDateId()==1) {
					switch($this->mOrder->GetCatalogue()->GetCatalogueId()) {
						// Dive
						case 1:
							$body .= 'Many thanks for your support, it is greatly appreciated. We try to offer the best and most reliable service possible to all of our customers. Would you be willing to evaluate our service? We have made the process as easy and simple as possible. Please click on the link below: <a href="http://www.trustpilot.co.uk/evaluate/www.echosupplements.com">Review Echo Supplements here</a>!<br /><br />';
						break;
						default:
							// null
						break;
					}
				}
				$body .= $publicOrderView->LoadDefault ( $this->mOrder->GetOrderId () );

				$this->mMail = new PHPMailer;
				$this->mMail->From = "info@echosupplements.com";
				$this->mMail->FromName = "Website Order - ".$prefix.$this->mOrder->GetOrderId();
				$this->mMail->Subject = "Website Order - ".$prefix.$this->mOrder->GetOrderId();
				$this->mMail->Host = "smtp.gmail.com";
				$this->mMail->Port = 465;
				$this->mMail->Mailer = "smtp";
				$this->mMail->SMTPAuth = true;
				$this->mMail->Username = "info@echosupplements.com";
				$this->mMail->Password = "bl00dlu5t";
				$this->mMail->Body = $body;
				$this->mMail->SMTPSecure = "ssl"; // option
				$this->mMail->AltBody = $body;
				$this->mMail->AddAddress ( $this->mOrder->GetCustomer()->GetEmail() );
				if($this->mMail->Send()) {
					return true;
				} else {
					return false;
				}
				break;
			case 'CancelledDisc' :
			case 'CancelledTemp' :
			case 'CancelledShip':
			case 'CancelledNoLongerReq':
			case 'CancelledTooLong':
			case 'CancelledUnableToContact':
			case 'CancelledOther':
				$publicOrderView = new PublicOrderView();
				$today = date('l jS F Y');
				$body = 'You Order: '.$this->mOrderPrefix.$this->mOrder->GetOrderId().' has been cancelled ('.$today.').';
				$body .= 'You have <strong>NOT</strong> been charged for this order.<br />'.$reason.'<br /><br />';
				$body .= $publicOrderView->LoadDefault ( $this->mOrder->GetOrderId () );
				$this->mMail = new PHPMailer;
				$this->mMail->From = "info@echosupplements.com";
				$this->mMail->FromName = "Website Order - ".$prefix.$order->GetOrderId();
				$this->mMail->Subject = "Website Order - ".$prefix.$order->GetOrderId();
				$this->mMail->Host = "smtp.gmail.com";
				$this->mMail->Port = 465;
				$this->mMail->Mailer = "smtp";
				$this->mMail->SMTPAuth = true;
				$this->mMail->Username = "info@echosupplements.com";
				$this->mMail->Password = "bl00dlu5t";
				$this->mMail->Body = $body;
				$this->mMail->SMTPSecure = "ssl"; // option
				$this->mMail->AltBody = $body;
				$this->mMail->AddAddress ( $address );
				if($this->mMail->Send()) {
					return true;
				} else {
					return false;
				}
				break;
		}
	} // End PrepareAndSendEmail


	//! Sends the email
	function SendEmail() {
		if (! $this->mLocalMode) {
			if (! $this->mMail->Send ()) {
				return false;
			} else {
				return true;
			}
		} else {
			return true;
		}
	}
}

try {
	$handler = new OrderFormEditHandler ( $_POST ['orderId'] );
	$handler->Validate ( $_POST );
	$handler->SaveOrder ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>