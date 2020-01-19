<?php
require_once ('../autoload.php');

class OrderFormEditHandler extends Handler {

	var $mOrder;
	var $mClean;
	var $mShipList = array ();
	var $mPackageShipList = array ();

	function __construct($orderId) {
		$this->mOrder = new OrderModel ( $orderId );
		$registry = Registry::getInstance ();
		$this->mLocalMode = $registry->localMode;
		$this->mDebugMode = $registry->debugMode;
		$this->mMoneyHelper = new MoneyHelper ( );
		if ($this->mLocalMode) {
			$this->mOrderPrefix = 'DBDL0';
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
					$this->mClean [$key] = $value;
					break;
				default :
					if (strstr ( $key, 'packageShip' )) {
						$this->AddPackageToShipList ( $key );
					}
					if (strstr ( $key, 'skuShip' )) {
						$this->AddSkuToShipList ( $key );
					}
					break;
			}
		}
	}

	function AddPackageToShipList($value) {
		$packageIdArr = explode ( 'packageShip', $value );
		$packageId = $packageIdArr [1];
		$package = new PackageModel ( $packageId );
		for($i = 0; $i < $this->mOrder->GetBasket ()->PackagesInBasket ( $package ); $i ++) {
			$this->mPackageShipList [] = $package;
		}
	}

	function AddSkuToShipList($value) {
		$skuIdArr = explode ( 'skuShip', $value );
		$skuId = $skuIdArr [1];
		$sku = new SkuModel ( $skuId );
		for($i = 0; $i < $this->mOrder->GetBasket ()->SkusInBasket ( $sku, true ); $i ++) {
			$this->mShipList [] = $sku;
		}
	}

	function SaveOrder() {
		$registry = Registry::getInstance ();
		$this->mOrderController = new OrderController ( );
		$dispatchDate = new DispatchDateModel ( $this->mClean ['dispatchEstimate'] );
		$orderStatus = new OrderStatusModel ( $this->mClean ['orderStatus'] );
		$courier = new CourierModel ( $this->mClean ['courier'] );
		$this->mCustomer = $this->mOrder->GetCustomer ();

		// Local/Production Mode
		if ($this->mLocalMode) {
			$orderPrefix = 'DBDL0';
		} else {
			$orderPrefix = 'ECHO';
		}

		// Initialise
		$runningTotal = 0;
		$errorMsg = '';
		$errorMsg .= 'Running Total: 0<br />';

		// Loop over SKUs
		foreach ( $this->mShipList as $sku ) {
			if ($this->mOrder->GetBasket ()->IsPackageUpgrade ( $sku )) {
				$packageUpgrade = true;
			} else {
				$packageUpgrade = false;
			}

			if ($this->mOrder->GetBasket ()->HasOverruledSku ( $sku, $packageUpgrade )) {
				$newTotal = $this->mOrder->GetBasket ()->GetOverruledSkuPrice ( $sku, false, $packageUpgrade );
			} else {
				$newTotal = $sku->GetSkuPrice ();
			}
			$runningTotal += $newTotal;
			$errorMsg .= 'Running Total: + ' . $newTotal . ' = ' . $runningTotal . ' (' . $sku->GetParentProduct ()->GetDisplayName () . ')<br />';
		}

		foreach ( $this->mPackageShipList as $package ) {
			$runningTotal += $this->mOrder->GetBasket ()->GetOverruledPackagePrice ( $package );
			$errorMsg .= 'Running Total: + ' . $this->mOrder->GetBasket ()->GetOverruledPackagePrice ( $package ) . ' = ' . $runningTotal . ' (' . $package->GetDisplayName () . ')<br />';
			#echo 'Package: '.$runningTotal.'<br>';
		}

		if ($this->mOrder->GetShippingAddress ()->GetCountry ()->IsVatFree ()) {
			$beforeVat = $runningTotal;
			$runningTotal = $this->mMoneyHelper->RemoveVAT ( $runningTotal );
			$errorMsg .= 'Running Total: = ' . $runningTotal . ' = ' . $beforeVat . ' - ' . $this->mMoneyHelper->VAT ( $runningTotal ) . ' (' . $this->mOrder->GetShippingAddress ()->GetCountry ()->GetDescription () . ')<br />';
		}

		$beforePostage = $runningTotal;
		$runningTotal += $this->mOrder->GetTotalPostage ();
		$errorMsg .= 'Running Total: = ' . $runningTotal . ' = ' . $beforePostage . ' + ' . $this->mOrder->GetTotalPostage () . '<br />';

		// Deal with what you're actually being asked to do (Ship/Cancel etc.)
		switch ($orderStatus->GetDescription ()) {
			case 'In Transit' :
				// Make a request string
				$shipOrderRequest = $this->mOrderController->ConstructShipRequest ( $this->mOrder, $runningTotal, $orderPrefix );
				#echo 'Order Request: '.$shipOrderRequest.'<br /><br />';
				// Perform the request
				$shipOrderResult = $this->mOrderController->SendOrderRequest ( $shipOrderRequest, $registry->paymentReleaseUrl );
				#echo 'URL: '.$registry->paymentReleaseUrl.'<br />';
				// Format it properly
				$shipOrderResultArr = $this->mOrderController->FormatProtxResponse ( $shipOrderResult );

				// ***** Deal with SHIPPING orders *****
				switch ($shipOrderResultArr ['Status']) {
					case 'OK' :
						// Ship the correct products
						$basket = $this->mOrder->GetBasket ();
						foreach ( $this->mShipList as $sku ) {
							$basket->SetShipped ( $sku );
						}
						// Ship the correct packages
						foreach ( $this->mPackageShipList as $package ) {
							$basket->SetPackageShipped ( $package );
						}
						// Update any other details
						$this->mOrder->SetDispatchDate ( $dispatchDate );
						$this->mOrder->SetShippedDate ( time () );
						$this->mOrder->SetCourier ( $courier );
						$this->mOrder->SetTrackingNumber ( $this->mClean ['trackingNumber'] );
						// Send the emails
						if (! $this->mLocalMode) {
							$this->PrepareEmail ( 'In Transit' );
							$this->SendEmail ();
						}
						// Update the order status
						$this->mOrder->SetStatus ( $orderStatus );
						// Create customer reciept
						$reciept = new AdminOrderRecieptView ( );
						echo $reciept->LoadDefault ( $this->mOrder->GetOrderId () );
						break;
					case 'INVALID' :
					case 'MALFORMED' :
						echo '<div style="font-family: Arial; font-size: 10pt;"><h2>Error - Order NOT Shipped (Problem with Deep Blue Admin)</h2>';
						echo '<strong>Protx Message:</strong> ' . $shipOrderResultArr ['Status'] . ' - ' . $shipOrderResultArr ['StatusDetail'] . '<br /><br />';
						echo '<strong>Debug Message:</strong> ' . $errorMsg;
						echo '</div>';
						break;
					case 'ERROR' :
						echo '<div style="font-family: Arial; font-size: 10pt;"><h2>Error - Order NOT Shipped (Problem with Protx)</h2>';
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
				$cancelOrderResult = $this->mOrderController->SendOrderRequest ( $cancelOrderRequest, $registry->paymentCancelUrl );
				// Format it properly
				$cancelOrderResultArr = $this->mOrderController->FormatProtxResponse ( $cancelOrderResult );

				switch ($this->mClean ['reasonForCancel']) {
					case 'discontinued' :
						$this->PrepareEmail ( 'CancelledDisc' );
						break;
					case 'tempOutOfStock' :
						$this->PrepareEmail ( 'CancelledTemp' );
						break;
					case 'dontShipThere' :
						$this->PrepareEmail ( 'CancelledShip' );
						break;
					case 'noLongerRequired':
						$this->PrepareEmail('CancelledNoLongerReq');
					break;
					case 'waitedTooLong':
						$this->PrepareEmail('CancelledTooLong');
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
						echo '<div style="font-family: Arial; font-size: 10pt; color: #FF0000;"><h1>Error - Order NOT Cancelled (Problem with Deep Blue Admin)</h1>';
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
				$this->PrepareEmail ( 'Authorised' );
				if ($this->SendEmail ()) {
					// Display Confirmation
					echo '<h1 style="font-family: Arial; font-size: 12pt;">Email sent.</h1>';
					// Reload the order
					echo '<a href=\'' . $registry->viewDir . '/OrdersEditView.php?id=' . $this->mOrder->GetOrderId () . '\' style="font-family: Arial; font-size: 10pt;">Back to order.</a>';
				} else {
					// Display Error
					echo '<h1 style="font-family: Arial; font-size: 12pt; color: #FF0000;">Email was <strong>NOT</strong> sent but the order has been updated.</h1>';
					// Reload the order
					echo '<a href=\'' . $registry->viewDir . '/OrdersEditView.php?id=' . $this->mOrder->GetOrderId () . '\' style="font-family: Arial; font-size: 10pt;">Back to order.</a>';
				}
				break;
			case 'Failed' :
				// Do nothing - staff shouldn't
				break;
		}
	}

	function PrepareEmail($event) {
		switch ($event) {
			case 'Authorised' :$publicOrderView = new PublicOrderView ( );
				$dispatchEstimate = new DispatchDateModel ( $this->mClean ['dispatchEstimate'] );
				$today = date ( 'l jS F Y' );
				$body = 'You Order: ' . $this->mOrderPrefix . $this->mOrder->GetOrderId () . ' has been updated (' . $today . ').<br /><br />';
				$body .= 'Due to unforseen stock levels the dispatch estimate is now: ' . $dispatchEstimate->GetDisplayName () . ' for this order.<br /><br />';
				$body .= $publicOrderView->LoadDefault ( $this->mOrder->GetOrderId () );
				$text_body = 'Your Order ' . $this->mOrderPrefix . $this->mOrder->GetOrderId () . ' has been updated (' . $today . '). \r\n Due to unforseen stock levels the dispatch estimate is now: ' . $dispatchEstimate->GetDisplayName () . ' for this order.';
				$text_body .= 'Due to unforseen stock levels the dispatch estimate is now: ' . $dispatchEstimate->GetDisplayName () . ' for this order.<br /><br /><br />';
				$this->mMail->From = "orders@echosupplements.com";
				$this->mMail->FromName = "Web Order";
				$this->mMail->Subject = "Website Order - " . $this->mOrderPrefix . $this->mOrder->GetOrderId ();
				$this->mMail->Host = "smtp.gmail.com";
				$this->mMail->Port = 465;
				$this->mMail->Mailer = "smtp";
				$this->mMail->SMTPAuth = true;
				$this->mMail->Username = "info@echosupplements.com";
				$this->mMail->Password = "c00piesway";
				$this->mMail->Body = $body;
				$this->mMail->SMTPSecure = "ssl"; // option
				$this->mMail->AltBody = $text_body;
				$this->mMail->AddAddress ( $this->mCustomer->GetEmail () );
				break;
			case 'In Transit' :$publicOrderView = new PublicOrderView ( );
				$today = date ( 'l jS F Y' );
				$body = 'You Order: ' . $this->mOrderPrefix . $this->mOrder->GetOrderId () . ' has been dispatched (' . $today . '). ';
				$body .= '<br />It has been sent using courier: ' . $this->mOrder->GetCourier ()->GetDisplayName () . ' method: ' . $this->mOrder->GetPostageMethod ()->GetDisplayName () . '.';
				$body .= '<br />Delivery guideline: ' . $this->mOrder->GetPostageMethod ()->GetDescription ();
				if ($this->mClean ['trackingNumber'] != '') {
					$body .= ' You can track your order at <a href="' . $this->mOrder->GetCourier ()->GetTrackingUrl () . $this->mClean ['trackingNumber'] . '">this link</a>.<br />';
				}
				if (! $this->mOrder->IsAllItemsShipped ()) {
					$body .= 'Not all items have been shipped and you have <strong>not</strong> been charged for these items. Unshipped Items:<ul>';
					$unshipped = $this->mOrder->GetUnshippedItems ();
					foreach ( $unshipped as $sku ) {
						$body .= '<li>' . $sku->GetParentProduct ()->GetDisplayName () . '</li>';
					}
					$body .= '</ul><br />The total amount you have been charged is: &pound;' . $this->mOrder->GetActualTaken () . '.<br />';
					$body .= 'If you still require these items you must place a <strong>new</strong> order for them.<br />';
				}
				$body .= '<br />Thank you for shopping with Deep Blue!<br /><br />';
				$body .= $publicOrderView->LoadDefault ( $this->mOrder->GetOrderId () );
				$text_body = 'Your Order ' . $this->mOrderPrefix . $this->mOrder->GetOrderId () . ' has been dispatched (' . $today . '). \r\n';
				$this->mMail->From = "orders@echosupplements.com";
				$this->mMail->FromName = "Web Order";
				$this->mMail->Subject = "Website Order - " . $this->mOrderPrefix . $this->mOrder->GetOrderId ();
				$this->mMail->Host = "smtp.gmail.com";
				$this->mMail->Port = 465;
				$this->mMail->Mailer = "smtp";
				$this->mMail->SMTPAuth = true;
				$this->mMail->Username = "info@echosupplements.com";
				$this->mMail->Password = "c00piesway";
				$this->mMail->Body = $body;
				$this->mMail->SMTPSecure = "ssl"; // option
				$this->mMail->AltBody = $text_body;
				$this->mMail->AddAddress ( $this->mCustomer->GetEmail () );
				break;
			case 'CancelledDisc' :$publicOrderView = new PublicOrderView ( );
				$today = date ( 'l jS F Y' );
				$body = 'You Order: ' . $this->mOrderPrefix . $this->mOrder->GetOrderId () . ' has been cancelled (' . $today . '). You have <strong>NOT</strong> been charged for this order.<br />The reason your order has been cancelled is that this product has been discontinued.<br /><br />';
				$body .= $publicOrderView->LoadDefault ( $this->mOrder->GetOrderId () );
				$text_body = 'Your Order ' . $this->mOrderPrefix . $this->mOrder->GetOrderId () . ' has been cancelled (' . $today . '). \r\n You have not been charged for this order. The reason your order has been cancelled is that this product has been discontinued..';
				$this->mMail->From = "orders@echosupplements.com";
				$this->mMail->FromName = "Web Order";
				$this->mMail->Subject = "Website Order - " . $this->mOrderPrefix . $this->mOrder->GetOrderId ();
				$this->mMail->Host = "smtp.gmail.com";
				$this->mMail->Port = 465;
				$this->mMail->Mailer = "smtp";
				$this->mMail->SMTPAuth = true;
				$this->mMail->Username = "info@echosupplements.com";
				$this->mMail->Password = "c00piesway";
				$this->mMail->Body = $body;
				$this->mMail->SMTPSecure = "ssl"; // option
				$this->mMail->AltBody = $text_body;
				$this->mMail->AddAddress ( $this->mCustomer->GetEmail () );
				break;
			case 'CancelledTemp' :$publicOrderView = new PublicOrderView ( );
				$today = date ( 'l jS F Y' );
				$body = 'You Order: ' . $this->mOrderPrefix . $this->mOrder->GetOrderId () . ' has been cancelled (' . $today . '). You have <strong>NOT</strong> been charged for this order.<br />The reason your order has been cancelled is that we are unable to guarantee a dispatch date within the next 2 weeks as we are awaiting stock. Please try to re-order after this time.<br /><br />';
				$body .= $publicOrderView->LoadDefault ( $this->mOrder->GetOrderId () );
				$text_body = 'Your Order ' . $this->mOrderPrefix . $this->mOrder->GetOrderId () . ' has been cancelled (' . $today . '). \r\n You have not been charged for this order. The reason your order has been cancelled is that we are unable to guarantee a dispatch date within the next 2 weeks as we are awaiting stock. Please try to re-order after this time.';
				$this->mMail->From = "orders@echosupplements.com";
				$this->mMail->FromName = "Web Order";
				$this->mMail->Subject = "Website Order - " . $this->mOrderPrefix . $this->mOrder->GetOrderId ().' - Cancelled';
				$this->mMail->Host = "smtp.gmail.com";
				$this->mMail->Port = 465;
				$this->mMail->Mailer = "smtp";
				$this->mMail->SMTPAuth = true;
				$this->mMail->Username = "info@echosupplements.com";
				$this->mMail->Password = "c00piesway";
				$this->mMail->Body = $body;
				$this->mMail->SMTPSecure = "ssl"; // option
				$this->mMail->AltBody = $text_body;
				$this->mMail->AddAddress ( $this->mCustomer->GetEmail () );
				break;
			case 'CancelledShip' :$publicOrderView = new PublicOrderView ( );
				$today = date ( 'l jS F Y' );
				$body = 'You Order: ' . $this->mOrderPrefix . $this->mOrder->GetOrderId () . ' has been cancelled (' . $today . '). You have <strong>NOT</strong> been charged for this order.<br />The reason your order has been cancelled is that we are unable to ship to the address requested.<br /><br />';
				$body .= $publicOrderView->LoadDefault ( $this->mOrder->GetOrderId () );
				$text_body = 'Your Order ' . $this->mOrderPrefix . $this->mOrder->GetOrderId () . ' has been cancelled (' . $today . '). \r\n You have not been charged for this order. The reason your order has been cancelled is that we are unable to ship to the address requested.';
				$this->mMail->From = "orders@echosupplements.com";
				$this->mMail->FromName = "Web Order";
				$this->mMail->Subject = "Website Order - " . $this->mOrderPrefix . $this->mOrder->GetOrderId ().' - Cancelled';
				$this->mMail->Host = "smtp.gmail.com";
				$this->mMail->Port = 465;
				$this->mMail->Mailer = "smtp";
				$this->mMail->SMTPAuth = true;
				$this->mMail->Username = "info@echosupplements.com";
				$this->mMail->Password = "c00piesway";
				$this->mMail->Body = $body;
				$this->mMail->SMTPSecure = "ssl"; // option
				$this->mMail->AltBody = $text_body;
				$this->mMail->AddAddress ( $this->mCustomer->GetEmail () );
				break;
			case 'CancelledNoLongerReq' :$publicOrderView = new PublicOrderView ( );
				$today = date ( 'l jS F Y' );
				$body = 'You Order: ' . $this->mOrderPrefix . $this->mOrder->GetOrderId () . ' has been cancelled (' . $today . '). You have <strong>NOT</strong> been charged for this order.<br />The reason your order has been cancelled is that the item is no longer required.<br /><br />';
				$body .= $publicOrderView->LoadDefault ( $this->mOrder->GetOrderId () );
				$text_body = 'Your Order ' . $this->mOrderPrefix . $this->mOrder->GetOrderId () . ' has been cancelled (' . $today . '). \r\n You have not been charged for this order. The reason your order has been cancelled is that the item is no longer required.';
				$this->mMail->From = "orders@echosupplements.com";
				$this->mMail->FromName = "Web Order";
				$this->mMail->Subject = "Website Order - " . $this->mOrderPrefix . $this->mOrder->GetOrderId ().' - Cancelled';
				$this->mMail->Host = "smtp.gmail.com";
				$this->mMail->Port = 465;
				$this->mMail->Mailer = "smtp";
				$this->mMail->SMTPAuth = true;
				$this->mMail->Username = "info@echosupplements.com";
				$this->mMail->Password = "c00piesway";
				$this->mMail->Body = $body;
				$this->mMail->SMTPSecure = "ssl"; // option
				$this->mMail->AltBody = $text_body;
				$this->mMail->AddAddress ( $this->mCustomer->GetEmail () );
				break;
			case 'waitedTooLong' :$publicOrderView = new PublicOrderView ( );
				$today = date ( 'l jS F Y' );
				$body = 'You Order: ' . $this->mOrderPrefix . $this->mOrder->GetOrderId () . ' has been cancelled (' . $today . '). You have <strong>NOT</strong> been charged for this order.<br />The reason your order has been cancelled is that you have waited too long.<br /><br />';
				$body .= $publicOrderView->LoadDefault ( $this->mOrder->GetOrderId () );
				$text_body = 'Your Order ' . $this->mOrderPrefix . $this->mOrder->GetOrderId () . ' has been cancelled (' . $today . '). \r\n You have not been charged for this order. The reason your order has been cancelled is that you have waited too long.';
				$this->mMail->From = "orders@echosupplements.com";
				$this->mMail->FromName = "Web Order";
				$this->mMail->Subject = "Website Order - " . $this->mOrderPrefix . $this->mOrder->GetOrderId ();
				$this->mMail->Host = "smtp.gmail.com";
				$this->mMail->Port = 465;
				$this->mMail->Mailer = "smtp";
				$this->mMail->SMTPAuth = true;
				$this->mMail->Username = "info@echosupplements.com";
				$this->mMail->Password = "c00piesway";
				$this->mMail->Body = $body;
				$this->mMail->SMTPSecure = "ssl"; // option
				$this->mMail->AltBody = $text_body;
				$this->mMail->AddAddress ( $this->mCustomer->GetEmail () );
				break;
		}
	} // End PrepareEmail


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