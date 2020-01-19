<?php

//! Loads the paypal receipt view
class PaypalOrderReceiptView extends View {

	//! The catalogue to load for
	var $mCatalogue;
	//! Settings to do with the catalogue such as whether to display different components
	var $mSystemSettings;
	//! Deals with managing the basket and any session variables
	var $mSessionHelper;
	//! Holds HTML code for public viewing
	var $mPublicLayoutHelper;
	//! ID of the current basket
	var $mBasketId;

	//! Constructor, sets some member variables based on the catalogue
	function __construct($catalogue) {
		parent::__construct ('Paypal Order Receipt View');
		$this->mCatalogue = $catalogue;
		$this->mSessionHelper 		= new SessionHelper ( );
		$this->mPublicLayoutHelper 	= new PublicLayoutHelper ( );
		$this->mSystemSettings 		= new SystemSettingsModel ( $this->mCatalogue );
		$this->mOrderController		= new OrderController;
		$this->mBasketId 			= $this->mSessionHelper->GetSessionId ();
	}

	//! Main page load function
	function LoadDefault($result) {
		$this->mResult = $result;
		$footerView = new FooterView ( );
		$this->mPage .= $this->mPublicLayoutHelper->OpenBody ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenCentrePageContainer ();
		parent::LoadHeaderSection($this->mCatalogue);
		parent::LoadNavigation();
		parent::LoadLeftColumn ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenRightCol ();
		$this->LoadMainContentColumn ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseRightCol ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenFooterContainer ();
		$this->mPage .= $footerView->LoadDefault ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseFooterContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseCentrePageContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseBody ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseHtml ();
		return $this->mPage;
	}

	//! Loads the centre column
	function LoadMainContentColumn() {
		if($this->mResult == 'fail') {
			$this->mPage .= 'PAYPAL CHECKOUT FAILED';
		} else {
			$this->LoadOrderReceipt();
		}
	} // End LoadMainContentColumn

	//! Perform the WS request and display a receipt
	function LoadOrderReceipt() {
		$registry = Registry::getInstance();
		// Set request-specific fields.
		$token = urlencode(htmlspecialchars($this->mResult));
		$paypalCheckout = new PaypalCheckoutHelper;

		// Add request-specific fields to the request string.
		$this->mRequest = 'VERSION='.urlencode('56.0');
		$this->mRequest .= '&SIGNATURE='.urlencode($registry->paypalApiSignature);
		$this->mRequest .= '&USER='.urlencode($registry->paypalApiUsername);
		$this->mRequest .= '&PWD='.urlencode($registry->paypalApiPassword);
		$this->mRequest .= "&METHOD=GetExpressCheckoutDetails";
		$this->mRequest .= "&TOKEN=$token";

		// Execute the API operation
		$this->mResult = $this->mOrderController->SendPaypalOrderRequest($this->mRequest,$registry->paypalPaymentProcessingUrl);
		parse_str(urldecode($this->mResult),$this->mResultArr);
	#	var_dump($this->mResultArr);die();
		if("SUCCESS" == strtoupper($this->mResultArr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($this->mResultArr["ACK"])) {
			// Extract the response details.
			$payerID = $this->mResultArr['PAYERID'];
			$street1 = urldecode($this->mResultArr["SHIPTOSTREET"]);
			if(array_key_exists("SHIPTOSTREET2", $this->mResultArr)) {
				$street2 = urldecode($this->mResultArr["SHIPTOSTREET2"]).'<br />';
			} else {
				$street2 = '';
			}
			$city_name 		= urldecode($this->mResultArr["SHIPTOCITY"]);
			$state_province = urldecode($this->mResultArr["SHIPTOSTATE"]);
			$postal_code 	= urldecode($this->mResultArr["SHIPTOZIP"]);
			$country_code 	= urldecode($this->mResultArr["SHIPTOCOUNTRYCODE"]);
			$pHelper		= new PresentationHelper;
			$orderTotal 	= $_REQUEST['amount'];
			$this->mPage .= '
							<h2>Paypal Checkout</h2>
							<b>Please confirm the following information:</b><br /><br />
								<b>Your Paypal Email</b>: '.urldecode($this->mResultArr['EMAIL']).'<br />
								<b>Order Total</b>: &pound;'.$pHelper->Money(urldecode($orderTotal)).'<br />
								<b>Delivery Address</b>:<br />
								'.$street1.'<br />
								'.$street2.'
								'.$city_name.'<br />
								'.$state_province.'<br />
								'.$postal_code.'<br />
								'.$country_code.'<br /><br />
							';
			$this->mPage .= '
					<div id="cofirmPaypalFormContainer">
						<form action="'.$this->mFormHandlersDir.'/ConfirmPaypalOrderHandler.php" method="post" id="cofirmPaypalForm" name="cofirmPaypalForm">
						<input type="hidden" name="tokenId" id="tokenId" value="'.urldecode($this->mResultArr['TOKEN']).'" />
						<input type="hidden" name="payerId" id="payerId" value="'.urldecode($this->mResultArr['PAYERID']).'" />
						<input type="hidden" name="amount" id="amount" value="'.$_REQUEST['amount'].'" />
						<input 	type="submit" value="Confirm Order" />
						</form>

						</div>
						';

		} else  {
			$this->mPage .= 'PAYPAL CHECKOUT FAILED';
		}
	} // End LoadOrderReceipt

} // End PaypalOrderReceiptView

			/*	[TOKEN] => EC%2d50142558HL821294W
				[TIMESTAMP] => 2010%2d05%2d29T18%3a18%3a47Z
				[CORRELATIONID] => e4daacd04f724
				[ACK] => Success
				[VERSION] => 51%2e0
				[BUILD] => 1322101
				[EMAIL] => dgh500_1275156098_per%40gmail%2ecom
				[PAYERID] => 7QLS5DBM7UPWG
				[PAYERSTATUS] => verified
				[FIRSTNAME] => Test
				[LASTNAME] => User
				[COUNTRYCODE] => GB
				[SHIPTONAME] => Test%20User
				[SHIPTOSTREET] => 1%20Main%20Terrace
				[SHIPTOCITY] => Wolverhampton
				[SHIPTOSTATE] => West%20Midlands
				[SHIPTOZIP] => W12%204LQ
				[SHIPTOCOUNTRYCODE] => GB
				[SHIPTOCOUNTRYNAME] => United%20Kingdom
				[ADDRESSSTATUS] => Confirmed ) */

?>