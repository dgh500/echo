<?php

//! Defines the checkout page
class CheckoutView extends View {

	var $mSystemSettings;
	var $mSessionHelper;

	//! Constructor initialises the variables needed and updates the session stage if 3D secure has been set up
	function __construct($catalogue,$threeDSecureComplete = false) {
		// Initialise parameters
		$this->mCatalogue 			 = $catalogue;
		$this->mThreeDSecureComplete = $threeDSecureComplete;

		// Construct
		parent::__construct($this->mCatalogue->GetDisplayName().' > Secure Checkout',false,false,false,FALSE);

		// Loads JS and CSS securely
		parent::IncludeJs('CheckoutView.js',true,true);
	#	parent::IncludeCss('CheckoutView.css.php',true,false,true);

		// Initialise Variables
		$this->mSystemSettings 		= new SystemSettingsModel ( $this->mCatalogue );
		$this->mSessionHelper 		= new SessionHelper ( );
		$this->mReferrerController 	= new ReferrerController ( );
		$this->mOrderController 	= new OrderController ( );
		$this->mBasket 				= $this->mSessionHelper->GetBasket ();

		// Get basket
		if ($this->mBasket->GetOrder()) {
			$this->mOrder = $this->mBasket->GetOrder();
		}

		// Local or Live?
		($this->mRegistry->localMode ? $this->mPrefix = 'ECH0' : $this->mPrefix = 'ECH0');

		// Initialise Debugging
		#if ($this->mRegistry->debugMode) {
		#	$this->DebugInit();
		#	$this->DebugStartLog();
		#}

		// If 3D-Secure is done, update the checkout stage
		if ($threeDSecureComplete) {
			$this->mSessionHelper->SetCheckoutStage ('threeDSecureComplete' );
		}
	} // End __construct()

	//! Opens the file for debugging
	function DebugInit() {
		$this->mDebugFileHandle = fopen('CheckoutViewLog.txt','a+');
	}

	//! Set up debug info (in /debugCheckoutLog.txt)
	function DebugStartLog() {
		if ($this->mRegistry->debugMode) {
			fwrite($this->mDebugFileHandle, "\r\n--------------------------------------------------------------\r\nCheckout Debug Log Started (" . date ( 'r', time () ) . ")" );
			$browser = get_browser ();
			$ip = getenv ( "REMOTE_ADDR" );
			fwrite($this->mDebugFileHandle,
				   "\r\nBrowser: " . $browser->browser .
				   "\r\nVersion: " . $browser->version .
				   "\r\nPlatform: " . $browser->platform .
				   "\r\nIP Address: " . $ip .
				   "\r\nBasket ID: " .$this->mBasket->GetBasketId() .
				   "\r\n"
			);
		}
	} // End DebugStartLog()

	//! Load the page
	function LoadDefault() {
		// Load <head>
		$this->mPage .= $this->mPublicLayoutHelper->OpenBody ();
		// NB Must be immediately after the <body> tag - todo{\Replace this with jQuery or the pop out thing - less buggy}
		$this->IncludeJs ( 'wz_tooltip.js', true, true );
		// Open page container
		$this->mPage .= $this->mPublicLayoutHelper->OpenCentrePageContainer ();
		parent::LoadHeaderSection ($this->mCatalogue,true);
		// Load main content
		$this->LoadMainContentColumn ();
		// Load footer container (empty) to complete page layout
		$this->mPage .= $this->mPublicLayoutHelper->CloseCentrePageContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseBody (true);
		// Close HTML
		$this->mPage .= $this->mPublicLayoutHelper->CloseHtml ();
		// Return the appropriate page dependant on whether 3D Secure has happened
		if (!$this->mThreeDSecureComplete) {
			return $this->mPage;
		} else {
			return $this->mSecureResultPage;
		}
	} // End LoadDefault()

	//! Loads the message users get upon a successful transaction
	function LoadSuccessMessage() {
		if(!isset($this->mOrder)) {
			if($this->mSessionHelper->GetSavedOrderId()) {
				$ORDER_ID = 'ECH0'.$this->mSessionHelper->GetSavedOrderId();
			} else {
				$ORDER_ID = 'NOT AVAILABLE';
			}
		} else {
			$ORDER_ID = 'ECH0'.$this->mOrder->GetOrderId();
		}
		$str = <<<EOT
	<div style="font-family: Arial, Sans-Serif; font-size: 10pt; width: 500px; margin-left: auto; margin-right: auto;">
		<h2>Order successfully placed - Thank You for your order!</h2>
		<strong>Your Order Number: </strong> {$ORDER_ID}<br />
		Please keep this number as it will allow us to reference your order immediately should you call us.<br /><br />
		<strong>What Next?</strong><br />
		You should receive an email with full details of your order soon, and any updates to your order will also be supplied via email. <br /><br />
		<strong>Any Questions?</strong><br />
		If you have any questions please call us on 01753 572741!<br /><br />
		<a target="_top" href="http://www.echosupplements.com"><b>Continue Shopping</b></a>
<!--		<form action="{$this->mSecureBaseDir}/formHandlers/CheckoutContinueShoppingHandler.php" method="post">
			<input type="submit" value="-> Continue Shopping" id="forgotPasswordSubmit" />
		</form> -->
	</div>
EOT;
	$str .= $this->LoadEcommerceTrackingCode();
	return $str;
	} // End LoadSuccessMessage()

	//! If your card gets declined you get this message!
	function LoadDeclinedMessage() {
		$str = <<<EOT
			<h2 style="color: #FF0000;">Error - Order NOT Placed</h2>
			There was a problem with your order and it has not been placed. <br /><strong>Reason:</strong> Card Declined - please try another card.
			<form action="{$this->mSecureBaseDir}/formHandlers/CheckoutReturnToBillingHandler.php" method="post">
			<input type="submit" value="-> Return To Billing Details Entry" id="forgotPasswordSubmit" />
			</form>
			<form action="{$this->mSecureBaseDir}/formHandlers/CheckoutContinueShoppingHandler.php" method="post">
				<input type="submit" value="-> Continue Shopping" id="forgotPasswordSubmit" />
			</form>
EOT;
	return $str;
	} // End LoadDeclinedMessage()

	//! Once you have reset your password you get...
	function LoadForgottenPasswordConfirmationMessage() {
#			<img src="{$this->mSecureBaseDir}/images/chkForgottenPassword.gif" />
		$str = <<<EOT
			<strong>Your password has been reset, please check your email for further instructions!</strong><br /><br />
			<form action="{$this->mSecureBaseDir}/formHandlers/CheckoutReturnToStartHandler.php" method="post">
			<input type="submit" value="-> Return To Login Page" id="forgotPasswordSubmit" />
			</form>
			<br /><br /><br /><br /><br /><br /><br /><br /><br />
EOT;
	return $str;
	} // End LoadForgottenPassword()

	//! If you refresh and break things in the middle of checking out...
	function LoadRefreshMessage() {
		$str = <<<EOT
			<a href="http://www.echosupplements.com">Echo Supplements</a>
EOT;
	return $str;
	} // End LoadRefreshMessage()

	//! If something fucks up with Protx ...
	function LoadErrorMessage() {
		$str = <<<EOT
		<div style="font-family: Arial, Sans-Serif; font-size: 10pt; width: 500px; margin-left: auto; margin-right: auto;">
			<h2 style="color: #FF0000;">Error - Order NOT Placed</h2>
			<strong>Reason</strong>: Error - This is usually either a communications problem, in which case trying again may work, If not please give us a call on 01753 572741 to process your order!
			<form action="{$this->mSecureBaseDir}/formHandlers/CheckoutReturnToBillingHandler.php" method="post">
			<input type="submit" value="-> Return To Billing Details Entry" id="forgotPasswordSubmit" />
			</form>
			<form action="{$this->mSecureBaseDir}/formHandlers/CheckoutContinueShoppingHandler.php" method="post">
				<input type="submit" value="-> Continue Shopping" id="forgotPasswordSubmit" />
			</form>
		</div>
EOT;
	return $str;
	} // End LoadErrorMessage()

	//! If you balls up putting your card details in...
	function LoadInvalidMessage() {
		$str = <<<EOT
			<h2 style="color: #FF0000;">Error - Order NOT Placed</h2>
			<strong>Reason</strong>: Invalid - this means there was a problem with the card details you have entered (probably a typing error) - please try again. If this still does not work please give us a call on 01753 572741 to process your order!<br /><br />
			<form action="{$this->mSecureBaseDir}/formHandlers/CheckoutReturnToBillingHandler.php" method="post">
			<input type="submit" value="-> Return To Billing Details Entry" id="forgotPasswordSubmit" />
			</form>
			<form action="{$this->mSecureBaseDir}/formHandlers/CheckoutContinueShoppingHandler.php" method="post">
				<input type="submit" value="-> Continue Shopping" id="forgotPasswordSubmit" />
			</form>
EOT;
	return $str;
	} // End LoadInvalidMessage

	//! Loads the main content
	function LoadMainContentColumn() {
		$this->mPage .= $this->mPublicLayoutHelper->OpenMainColumn();
		if($this->mSessionHelper->GetCheckoutStage()) {
			switch($this->mSessionHelper->GetCheckoutStage()) {
				// Load the Address Entry Form
				case 'enterAddress' :
					// Debug
					#($this->mRegistry->debugMode ? fwrite($this->mDebugFileHandle, " -> Customer in stage 2 - entering address \r\n") : NULL);
					// Display
				#	$this->mPage .= '<img src="' . $this->mSecureBaseDir . '/images/checkoutStage2.gif" />';
					$this->LoadAddress();
					break;
				case 'forgottenPassword' :
					// Debug
					#($this->mRegistry->debugMode ? fwrite($this->mDebugFileHandle, " -> Customer forgotten password \r\n") : NULL);
					// Display Forgotten Password Form
				#	$this->mPage .= '<img src="' . $this->mSecureBaseDir . '/images/chkForgottenPassword.gif" />';
					$this->LoadForgottenPassword();
					break;
				case 'forgottenPasswordReset' :
					// Debug
					#($this->mRegistry->debugMode ? fwrite($this->mDebugFileHandle, " -> Customer forgotten password (Confirmation) \r\n") : NULL);
					// Display Message
					$this->mPage .= $this->LoadForgottenPasswordConfirmationMessage();
					break;
				case 'registration' :
					// Debug
					#($this->mRegistry->debugMode ? fwrite($this->mDebugFileHandle, " -> Customer in stage 1 - registration \r\n" ) : NULL);
					// Load New Customer Registration Form
				#	$this->mPage .= '<img src="' . $this->mSecureBaseDir . '/images/checkoutStage1.gif" />';
					$this->LoadCustomerRegistrationForm();
					break;
				case 'registrationFailure' :
					// Debug
					#($this->mRegistry->debugMode ? fwrite($this->mDebugFileHandle, " -> Customer in stage 1 - registration failure \r\n" ) : NULL);
					// Show Registration Failure page
				#	$this->mPage .= '<img src="' . $this->mSecureBaseDir . '/images/checkoutStage1.gif" />';
					$this->LoadRegistrationFailure();
					break;
					// Show Login Failure
				case 'loginFailure' :
					// Debug
					#($this->mRegistry->debugMode ? fwrite($this->mDebugFileHandle, " -> Customer in stage 1 - login failure \r\n" ) : NULL);
					// Display
				#	$this->mPage .= '<img src="' . $this->mSecureBaseDir . '/images/checkoutStage1.gif" />';
					$this->LoadLoginFailure ();
					break;
				case 'billingDetails' :
					// Debug
					#($this->mRegistry->debugMode ? fwrite($this->mDebugFileHandle, " -> Customer in stage 3 - entering billing details \r\n") : NULL);
					// Display
				#	$this->mPage .= '<img src="' . $this->mSecureBaseDir . '/images/checkoutStage3.gif" />';
					$this->LoadBilling ();
					break;
				case '3DAuth' :
					// Load 3D Authorisation section if the user's card is enrolled in the scheme
					$this->Load3DAuth ();
					break;
				case 'threeDSecureComplete' :
					// If this IS set then the page HASNT been refreshed, and we can record the AUTH codes and perform the 3D Secure stuff
					if(isset($_REQUEST['PaRes'])) {
						// Make 3D-Secure request
						$request = $this->mOrderController->Construct3DSecureRequest ( $_REQUEST ['PaRes'], $_REQUEST ['MD'] );
						// Send the 3D-Secure request
						$response = $this->mOrderController->SendOrderRequest ( $request, $this->mRegistry->payment3dCallback );

						// Debug - ONLY enable this if needed by Protx. Will show sensitive auth details publicly
						# UNSECURE ($this->mRegistry->debugMode ? fwrite($this->mDebugFileHandle,$response) : NULL);
						#($this->mRegistry->debugMode ? fwrite($this->mDebugFileHandle,"Order ID: ECHO".$this->mOrder->GetOrderId()) : NULL);

						// Format it so it makes sense
						$responseArr = $this->mOrderController->FormatProtxResponse($response);

						// Get the important parts of the result
						$this->mOrderStatus = $responseArr['Status'];
						$this->mTransactionId = @$responseArr['VPSTxId'];
						$this->mSecurityKey = @$responseArr['SecurityKey'];
						$this->mStatusDesc = @$responseArr['StatusDetail'];
						$this->m3DSecureStatus = @$responseArr['3DSecureStatus'];
						$this->mCavv = @$responseArr['CAVV'];
						$this->mTxAuthNo = @$responseArr['TxAuthNo'];

						// Store details in the database
						if (! $this->mOrder->IsTransactionDetailsSet ()) {
							$this->mOrder->SetTransactionId($this->mTransactionId);
							$this->mOrder->SetSecurityKey($this->mSecurityKey);
							$this->mOrder->Set3DSecureStatus($this->m3DSecureStatus);
							$this->mOrder->SetCavv($this->mCavv);
							$this->mOrder->SetTransactionDate(time());
							$this->mOrder->SetTxAuthNo($this->mTxAuthNo);
						}
						#($this->mRegistry->debugMode ? fwrite($this->mDebugFileHandle," -> Checkout is complete - status: ".$this->mOrderStatus." \r\n") : NULL);
					} else {
						// The page has been refreshed
						$this->mOrderStatus = 'REFRESH';
					}
					// Handle the result
					switch ($this->mOrderStatus) {
						case 'OK' :
						case 'AUTHENTICATED' :
						case 'REGISTERED' :
							$this->mSecureResultPage .= $this->LoadSuccessMessage();
							break;
						case 'MALFORMED' :
						case 'INVALID' :
						case 'ERROR' :
							$this->mSecureResultPage .= $this->LoadErrorMessage();
							break;
						case 'NOTAUTHED' :
						case 'REJECTED' :
							$this->mSecureResultPage .= $this->LoadDeclinedMessage();
							break;
						case 'REFRESH' :
							$this->mSecureResultPage .= $this->LoadRefreshMessage();
							break;
					}

					// Send emails and set to authorised ONLY if it came back OK
					if(isset($_REQUEST['PaRes'])) {
						switch ($this->mOrderStatus) {
							case 'OK' :
							case 'AUTHENTICATED' :
							case 'REGISTERED' :
								// Perform final order bits'n'bobs
								$orderStatusController = new OrderStatusController ( );
								$orderStatus = $orderStatusController->GetAuthorised ();
								$this->mOrder->SetStatus ( $orderStatus );
								$customer = $this->mOrder->GetCustomer ();
								$customer->SetFirstOrder ( 0 );

								// Send Emails
								$this->mOrderController->PrepareAndSendCustomerEmail ( $this->mOrder->GetCustomer ()->GetEmail (), $this->mOrder, $this->mPrefix );
								$this->mOrderController->PrepareAndSendEmail ( "orders@echosupplements.com", $this->mOrder, $this->mPrefix );
								// Update Stock Levels
								$this->mBasket = $this->mOrder->GetBasket();
								$this->mBasket->UpdateStockLevels();

								// Reset the basket
								if(!isset($this->mAlreadyRegeneratedTheBasket)) {
									// Reset the basket
									$this->mSessionHelper->RegenerateId();
									// Note that this has already been done
									$this->mAlreadyRegeneratedTheBasket = true;
								}
							break;
						} // End switch()
					} else {
						// Save the order ID even if the checkout has failed in case the customer wants to continue shopping
					# disabled this as causing problems	$this->mSessionHelper->SetSavedOrderId($this->mOrder->GetOrderId());
					} // End if()
					return $this->mSecureResultPage;
					break; // End case(threeDSecureComplete)
					case 'checkoutComplete' :
						// Debug
						#($this->mRegistry->debugMode ? fwrite($this->mDebugFileHandle," -> Checkout is complete - status: ".$this->mSessionHelper->GetCheckoutStatus()." \r\n") : NULL);
						// Image to show where we are
					#	$this->mPage .= '<img src="' . $this->mSecureBaseDir . '/images/checkoutComplete.gif" />';
						switch ($this->mSessionHelper->GetCheckoutStatus()) {
							case 'A' : // Accepted
								$this->mPage .= $this->LoadSuccessMessage();
								// Reset the basket
								if(!isset($this->mAlreadyRegeneratedTheBasket)) {
									// Reset the basket
									$this->mSessionHelper->RegenerateId();
									// Note that this has already been done
									$this->mAlreadyRegeneratedTheBasket = true;
								}
							break;
							case 'D' : // Declined
								$this->mPage .= $this->LoadDeclinedMessage();
								// Save the order ID even if the checkout has failed in case the customer wants to continue shopping
								$this->mSessionHelper->SetSavedOrderId($this->mOrder->GetOrderId());
							break;
							case 'E' : // Error
								$this->mPage .= $this->LoadErrorMessage();
								// Save the order ID even if the checkout has failed in case the customer wants to continue shopping
								$this->mSessionHelper->SetSavedOrderId($this->mOrder->GetOrderId());
							break;
							case 'I' : // Invalid
								$this->mPage .= $this->LoadInvalidMessage();
								// Save the order ID even if the checkout has failed in case the customer wants to continue shopping
								$this->mSessionHelper->SetSavedOrderId($this->mOrder->GetOrderId());
							break;
						} // End checkoutStatus switch
					$this->mPage .= '<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />';
					$this->mPage .= '<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />';
					break; // End checkoutComplete case
			}
	#		$this->mPage .= '<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />';
		} else {
			// Display Checkout Stage
		#	$this->mPage .= '<img src="' . $this->mSecureBaseDir . '/images/checkoutStage1.gif" />';
			// Debug
			#($this->mRegistry->debugMode ? fwrite($this->mDebugFileHandle," -> Customer in stage 1 - choosing new vs returning \r\n") : NULL);
			// Load login/registration forms
			$this->LoadNewCustomerChoice();
		} // End if checkoutStage not set (customer hasn't started the checkout process yet)
		$this->mPage .= '<br style="clear: both" /><img src="'.$this->mSecureBaseDir.'/images/secureCheckout.jpg" style="display: block; margin-left: auto; margin-right: auto;" /><br /><br /><br /><br /><br /><br /><br />';
		$this->mPage .= $this->mPublicLayoutHelper->CloseMainColumn();
	} // End LoadMainContentColumn()

	//! Loads the 3D Authorisation auto-submitting form
	function Load3DAuth() {
	#	echo '3D AUTH:::';
	#	var_dump($_SESSION);
		$PAREQ 		= $_SESSION ['PAReq'];
		$MD 		= $_SESSION ['MD'];
		$ACSURL 	= $_SESSION ['ACSURL'];
		$TERM_URL 	= $_SESSION ['TERM_URL'];

		unset ( $_SESSION ['PAReq'] );
		unset ( $_SESSION ['MD'] );
		unset ( $_SESSION ['ACSURL'] );
		unset ( $_SESSION ['TERM_URL'] );

		$this->mPage .= '
					<IFRAME SRC="'.str_replace('http','https',$this->mFormHandlersDir).'/3DRedirect.php?PAREQ=' . urlencode ( $PAREQ ) . '&MD=' . urlencode ( $MD ) . '&ACSURL=' . urlencode ( $ACSURL ) . '&TERM_URL=' . $TERM_URL . '" NAME="3DIFrame" WIDTH="550" HEIGHT="500" FRAMEBORDER="0">
						<SCRIPT LANGUAGE="Javascript">
							function OnLoadEvent() { document.form.submit(); }
						</SCRIPT>
						<html><head><title>3D Secure Verification</title></head>
						<body OnLoad="OnLoadEvent();">
							<FORM name="form" action="' . $ACSURL . '" method="POST">
								<input type="hidden" name="PaReq" id="PaReq" value="' . $PAREQ . '"/>
								<input type="hidden" name="TermUrl" id="TermUrl" value="' . $TERM_URL . '"/>
								<input type="hidden" name="MD" id="MD" value="' . $MD . '"/>
						<NOSCRIPT>
							<center><p>Please click button below to Authenticate your card</p><input type="submit" value="Go"/></p></center>
						</NOSCRIPT>
							</form></body></html>
						</IFRAME>';
	} // End Load3DAuth()

	//! Loads the billing details form \todo{Tidy this up}
	function LoadBilling() {
		$customer = new CustomerModel ( $this->mSessionHelper->GetCustomer (), 'id' );
		$this->mPage .= <<<EOT
		<form action="{$this->mSecureBaseDir}/formHandlers/CheckoutBillingHandler.php" method="post" name="billingForm" id="billingForm" onsubmit="return validateBilling(this)">
			<h1>{$customer->GetFirstName()} {$customer->GetLastName()} : Card Details</h1>
			<label for="cardHoldersName"><span class="required">*</span> Card Holders Name:</label>
				<input type="text" name="cardHoldersName" id="cardHoldersName" />
				<span onmouseover="Tip('<img src=\'images/chkCardName.jpg\' width=\'200\' height=\'133\'>')" onmouseout="UnTip()">[?]</span><br />
			<label for="cardNumber"><span class="required">*</span> Card Number:</label>
				<input type="text" name="cardNumber" id="cardNumber" />
				<span onmouseover="Tip('<img src=\'images/chkCardNumber.jpg\' width=\'200\' height=\'133\'>')" onmouseout="UnTip()">[?]</span><br />
			<label for="cardType"><span class="required">*</span> Card Type:</label>
				<select name="cardType" id="cardType" />
					<option value="Maestro">Maestro</option>
					<option value="Mastercard">Mastercard</option>
					<option value="Solo">Solo</option>
					<option value="Switch">Switch</option>
					<option value="Visa" selected="selected">Visa</option>
					<option value="Visa Electron">Visa Electron</option>
				</select><br />
			<label for="validFromMonth">Valid From</label>
				<select name="validFromMonth" id="validFromMonth">
				<option value="NA">- -</option>
EOT;
		for($i = 1; $i < 13; $i ++) {
			if ($i < 10) {
				$month = '0' . $i;
			} else {
				$month = $i;
			}
			$this->mPage .= '<option value="' . $month . '">' . $month . '</option>';
		}
		$this->mPage .= <<<EOT
				</select>
				<select name="validFromYear" id="validFromYear">
				<option value="NA">- - - -</option>
EOT;
		$currentTime = time ();
		for($i = 1; $i < 10; $i ++) {
			$this->mPage .= '<option value="' . date ( 'Y', $currentTime ) . '">' . date ( 'Y', $currentTime ) . '</option>';
			$currentTime = $currentTime - 31556926; // Number of seconds in a year
		}
		$this->mPage .= <<<EOT
			</select>
			<span onmouseover="Tip('<img src=\'images/chkCardValidFrom.jpg\' width=\'200\' height=\'133\'>')" onmouseout="UnTip()">[?]</span><br />
		<label for="expiryDateMonth"><span class="required">*</span> Expiry Date</label>
			<select name="expiryDateMonth" id="expiryDateMonth">
EOT;
		for($i = 1; $i < 13; $i ++) {
			if ($i < 10) {
				$month = '0' . $i;
			} else {
				$month = $i;
			}
			$this->mPage .= '<option value="' . $month . '">' . $month . '</option>';
		}
		$this->mPage .= <<<EOT
			</select>
			<select name="expiryDateYear" id="expiryDateYear">
EOT;
		$currentTime = time ();
		for($i = 1; $i < 10; $i ++) {
			$this->mPage .= '<option value="' . date ( 'Y', $currentTime ) . '">' . date ( 'Y', $currentTime ) . '</option>';
			$currentTime = $currentTime + 31556926; // Number of seconds in a year
		}
		$this->mPage .= <<<EOT
			</select>
			<span onmouseover="Tip('<img src=\'images/chkCardExpires.jpg\' width=\'200\' height=\'133\'>')" onmouseout="UnTip()">[?]</span><br />
			<label for="issueNumber">Issue Number:</label>
				<input type="text" name="issueNumber" id="issueNumber" style="width: 15px" maxlength="1" />
				<span onmouseover="Tip('If you have a switch/maestro card you have<br />either an issue number or valid from date<br />the issue number can be found next to the<br />expiry date at the bottom of the card.')" onmouseout="UnTip()">[?]</span><br />
			<label for="cvn">

			<span class="required">*</span> CVN Security Number:</label>
				<input type="text" name="cvn" id="cvn" style="width: 30px;" maxlength="3" />
				<span onmouseover="Tip('<img src=\'images/cvnCard.gif\' width=\'200\' height=\'133\'>')" onmouseout="UnTip()">[?]</span><br />
			<input type="submit" value="Complete Order" class="submit" />
			<br />
			If you do not see a 'Thank you for your order' screen next, your order has not been successful and no goods will be shipped. Please call our hotline on 01753 572741 to place your order!
			<div id="error"></div>
		</form>
EOT;

	} // End LoadBilling()

	//! Loads the forgotten password form
	function LoadForgottenPassword() {
		$forgottenPasswordView = new ForgottenPasswordView ( );
		$this->mPage .= $forgottenPasswordView->LoadDefault ( 'checkout' );
		$this->mPage .= '<form action="' . $this->mSecureBaseDir . '/formHandlers/CheckoutReturnToStartHandler.php" method="post" style="width: 525px; text-align: center;">';
		$this->mPage .= '<input type="image" src="' . $this->mBaseDir . '/images/backButton.png" id="forgotPasswordSubmit" />';
		$this->mPage .= '</form>';
		$this->mPage .= '<br /><br /><br /><br /><br />';
	} // End LoadForgottenPassword()

	//! Loads the address entry form
	function LoadAddress() {
		$customer = new CustomerModel ( $this->mSessionHelper->GetCustomer (), 'id' );
		$allReferrers = $this->mReferrerController->GetAllReferrers ();
		$referrerDropDown = '<select name="referrer" id="referrer"><option style="font-weight: bold; text-align: center;" value="NA"></option>';
		foreach ( $allReferrers as $referrer ) {
			// If returning customer then fill this in for them
			if($customer->GetOrderCount() > 0 && $referrer->GetDescription() == 'Used Before') {
				$default = ' selected="selected" ';
			} else {
				$default = '';
			}
			$referrerDropDown .= '<option value="' . $referrer->GetReferrerId () . '" '.$default.'>' . $referrer->GetDescription () . '</option>';
		}
		$referrerDropDown .= '</select>';
		if (! $customer->GetPreviousDeliveryAddress () || ! $customer->GetPreviousBillingAddress ()) {
			$company = '';
			$delCompany = '';
			$delAddress1 = '';
			$delAddress2 = '';
			$delAddress3 = '';
			$delCounty = '';
			$delPostcode = '';

			$bilAddress1 = '';
			$bilAddress2 = '';
			$bilAddress3 = '';
			$bilCounty = '';
			$bilPostcode = '';
		} else {
			$prevDelivery = $customer->GetPreviousDeliveryAddress ();
			$delCompany = $prevDelivery->GetCompany ();
			$delAddress1 = $prevDelivery->GetLine1 ();
			$delAddress2 = $prevDelivery->GetLine2 ();
			$delAddress3 = $prevDelivery->GetLine3 ();
			$delCounty = $prevDelivery->GetCounty ();
			$delPostcode = $prevDelivery->GetPostcode ();

			$prevBilling = $customer->GetPreviousBillingAddress ();
			$bilAddress1 = $prevBilling->GetLine1 ();
			$bilAddress2 = $prevBilling->GetLine2 ();
			$bilAddress3 = $prevBilling->GetLine3 ();
			$bilCounty = $prevBilling->GetCounty ();
			$bilPostcode = $prevBilling->GetPostcode ();
		}
		$firstOrderSection = '';
		$this->mPage .= <<<EOT
		<form action="{$this->mSecureBaseDir}/formHandlers/CheckoutAddressHandler.php" method="post" name="deliveryForm" id="deliveryForm" onsubmit="return validateDelivery(this)">
			<h1>{$customer->GetFirstName()} {$customer->GetLastName()} : Delivery Address</h1>
			<label for="company">Company:</label>
				<input type="text" name="company" id="company" value="{$delCompany}" /><br />
			<label for="address1"><span class="required">*</span> Address:</label>
				<input type="text" name="address1" id="address1" value="{$delAddress1}" /><br />
			<label for="address2">&nbsp;</label>
				<input type="text" name="address2" id="address2" value="{$delAddress2}" /><br />
			<label for="address3">&nbsp;</label>
				<input type="text" name="address3" id="address3" value="{$delAddress3}" /><br />
			<label for="county">County</label>
				<input type="text" name="county" id="county" value="{$delCounty}" /><br />
			<label for="postCode"><span class="required">*</span> Postcode</label>
				<input type="text" name="postCode" id="postCode" style="width: 70px;" value="{$delPostcode}" /><br />
EOT;
		if(isset($_SESSION['countryId'])) {
			$this->mPage .= '<input type="hidden" name="country" id="country" value="' . $_SESSION ['countryId'] . '" />';
		} else {
			$countryController = new CountryController;
			$this->mPage .= '<input type="hidden" name="country" id="country" value="'.$countryController->GetDefault()->GetCountryId().'" />';
		}
		$this->mPage .= <<<EOT
			<h1>{$customer->GetFirstName()} {$customer->GetLastName()} : Billing Address</h1>
			{$firstOrderSection}<br />
			<label for="sameAsDelivery">Same as delivery:</label>
				<input type="checkbox" name="sameAsDelivery" id="sameAsDelivery" onclick="toggleBillingCopy()" /><br />
			<label for="bAddress1"><span class="required">*</span> Address:</label>
				<input type="text" name="bAddress1" id="bAddress1" value="{$bilAddress1}" /><br />
			<label for="bAddress2">&nbsp;</label>
				<input type="text" name="bAddress2" id="bAddress2" value="{$bilAddress2}" /><br />
			<label for="bAddress3">&nbsp;</label>
				<input type="text" name="bAddress3" id="bAddress3" value="{$bilAddress3}" /><br />
			<label for="bCounty">County</label>
				<input type="text" name="bCounty" id="bCounty" value="{$bilCounty}" /><br />
			<label for="bPostCode"><span class="required">*</span> Postcode</label>
				<input type="text" name="bPostCode" id="bPostCode" style="width: 70px;" value="{$bilPostcode}" /><br />
			<label for="catalogueReq"><span class="required">*</span> Free Brochure?</label>
				<input type="checkbox" name="catalogueReq" id="catalogueReq" checked="checked" /><br />
			<label for="referrer"><span class="required">*</span> How did you hear about us?</label>
				{$referrerDropDown}<br />
			<label for="notes">Any other notes:</label>
				<textarea name="notes" id="notes" cols="20" rows="3"></textarea>
			<input type="submit" value="Submit Address Details" class="submit" />
			<div id="error"></div>
		</form>
EOT;
	} // End LoadAddress()

	//! Loads the registration failure message and back button
	function LoadRegistrationFailure() {
		$this->mPage .= '<div id="registrationError">Your registration failed because ' . $this->mSessionHelper->GetRegistrationError () . '.</div>';
		$this->mPage .= '<form action="' . $this->mSecureBaseDir . '/formHandlers/BackHandler.php" method="post" id="checkoutBackForm" name="checkoutBackForm">';
		$this->mPage .= '<input type="image" src="' . $this->mBaseDir . '/images/loginButton.png" />';
		$this->mPage .= '</form>';
		$this->LoadCustomerRegistrationForm ();
	} //! End LoadRegistrationFailure()

	//! Loads the login failure form \todo{tidy up}
	function LoadLoginFailure() {
		$postageMethod = '<input type="hidden" name="postageMethodId" id="postageMethodId" value="' . $this->mSessionHelper->GetPostageMethod () . '" />';
		$this->mPage .= '<div id="failedLoginContainer">';
		$this->mPage .= '<h1>Login Failure</h1>';
		$this->mPage .= '<div id="returningCustomerContainer">';
		$this->mPage .= '<h2>Returning Customer</h2>';
		$this->mPage .= '<form action="' . $this->mSecureBaseDir . '/formHandlers/ReturningCustomerHandler.php" method="post" name="returningCustomerForm" id="returningCustomerForm" onsubmit="return validateLoginForm(this)">';
		$this->mPage .= '<label for="loginEmail">Email:</label><input type="text" name="loginEmail" id="loginEmail" /><br />';
		$this->mPage .= '<label for="loginPassword">Password:</label><input type="password" name="loginPassword" id="loginPassword" />';
		$this->mPage .= '<input type="image" src="' . $this->mBaseDir . '/images/loginButton.png" />';
		$this->mPage .= '<div id="error"></div>';
		$this->mPage .= $postageMethod;
		$this->mPage .= '</form>';
		$this->mPage .= '<form action="' . $this->mSecureBaseDir . '/formHandlers/CheckoutForgotPasswordHandler.php" method="post">';
		$this->mPage .= '<input type="submit" value="Forgotten Your Password?" id="forgotPasswordSubmit" />';
		$this->mPage .= '</form>';
		$this->mPage .= '</div><!-- Close returningCustomerContainer -->';
		$this->mPage .= '</div><!-- Close failedLoginContainer -->';
		$this->mPage .= '<form action="' . $this->mSecureBaseDir . '/formHandlers/BackHandler.php" method="post" id="checkoutBackForm" name="checkoutBackForm">';
		$this->mPage .= '<input type="image" src="' . $this->mBaseDir . '/images/backButton.png" />';
		$this->mPage .= '</form>';
	} // End LoadLoginFailure()

	function LoadCustomerRegistrationForm() {
		$postageMethod = '<input type="hidden" name="postageMethodId" id="postageMethodId" value="' . $this->mSessionHelper->GetPostageMethod () . '" />';
		$this->mPage .= <<<EOT
		<form action="{$this->mSecureBaseDir}/formHandlers/NewCustomerRegistrationHandler.php" method="post" name="newCustomerRegistrationForm" id="newCustomerRegistrationForm" onsubmit="return validateForm(this)">
			{$postageMethod}
			<h1>Registration</h1>
			<label for="title"><span class="required">*</span> Title:</label>
				<select name="title" id="title"><option value="Mr">Mr</option><option value="Mrs">Mrs</option><option value="Miss">Miss</option><option value="Ms">Ms</option><option value="Dr">Dr</option><option value="Prof">Prof</option><option value="Rev">Rev</option><option value="Lord">Lord</option></select><br />
			<label for="firstName"><span class="required">*</span> First Name:</label>
				<input type="text" name="firstName" id="firstName" autocomplete="off" /><br />
			<label for="lastName"><span class="required">*</span> Last Name:</label>
				<input type="text" name="lastName" id="lastName" autocomplete="off" /><br />
			<label for="email"><span class="required">*</span> Email Address:</label>
				<input type="text" name="email" id="email" autocomplete="off" /><br />
			<label for="telNo"><span class="required">*</span> Telephone Number:</label>
				<input type="text" name="telNo" id="telNo" autocomplete="off" /><br />
			<label for="mobNo">Mobile Number:</label>
				<input type="text" name="mobNo" id="mobNo" autocomplete="off" /><br />
			<label for="password"><span class="required">*</span> Password:</label>
				<input type="password" name="password" id="password" autocomplete="off" /><br />
			<label for="passwordCheck"><span class="required">*</span> Re-Type Password:</label>
				<input type="password" name="passwordCheck" id="passwordCheck" autocomplete="off" /><br />
			<label for="acceptTerms"><span class="required">*</span> I accept the <a href="{$this->mBaseDir}/content/7/terms-and-conditions" target="_blank">Terms & Conditions</a></label>
				<input type="checkbox" name="acceptTerms" id="acceptTerms" /><br />
			<input type="submit" value="Submit My Details" class="submit" />
			<div id="error"></div>
		</form>
EOT;
		$this->mPage .= '<form action="' . $this->mSecureBaseDir . '/formHandlers/CheckoutReturnToStartHandler.php" method="post" style="width: 540px; margin-left: auto; margin-right: auto; text-align: center;">';
		$this->mPage .= '<input type="image" src="' . $this->mBaseDir . '/images/backButton.png" id="forgotPasswordSubmit" />';
		$this->mPage .= '</form>';
	}

	//! Loads the E-Commerce tracking
	function LoadEcommerceTrackingCode() {
		$value = $this->mOrder->GetTotalPrice();
		$trackingCode = '<!-- Google Code for Echo Checkout Complete Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 994440035;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "8RwOCNWM5ggQ4-aX2gM";
var google_conversion_value = '.$value.';
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/994440035/?value=0&amp;label=8RwOCNWM5ggQ4-aX2gM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>';
		$msnCode = '
					<script type="text/javascript">
						if (!window.mstag) mstag = {loadTag : function(){},time : (new Date()).getTime()};
					</script>
					<script id="mstag_tops" type="text/javascript" src="//flex.atdmt.com/mstag/site/080a6090-bee8-4fb5-9655-63bd16e04c0d/mstag.js"></script>
					<script type="text/javascript">
						mstag.loadTag("analytics", {dedup:"1",domainId:"950200",type:"1",revenue:"",actionid:"1583"})
					</script>
					<noscript>
						<iframe src="//flex.atdmt.com/mstag/tag/080a6090-bee8-4fb5-9655-63bd16e04c0d/analytics.html?dedup=1&domainId=950200&type=1&revenue=&actionid=1583" frameborder="0" scrolling="no" width="1" height="1" style="visibility:hidden; display:none"> </iframe>
					</noscript>';

		return $trackingCode;
	} // Close LoadEcommerceTrackingCode()

	function LoadNewCustomerChoice() {
		// Log the fact that someone has entered checkout stage
		$now = date('r',time());
		$fh = fopen("checkoutEntered.php","a+");
		fwrite($fh,"Checkout Entered - ".$now."\n");
		fclose($fh);
		// Load Form
		$postageMethod = '<input type="hidden" name="postageMethodId" id="postageMethodId" value="' . $this->mSessionHelper->GetPostageMethod () . '" />';
		$this->mPage .= '<div id="loginContainer">';
		$this->mPage .= '<h1>Login/Register</h1>';
		$this->mPage .= '<div id="newCustomerContainer">';
		$this->mPage .= '<h2>New Customer</h2>';
		$this->mPage .= '<p>Not a customer yet? Create an account today!</p>';
		$this->mPage .= '<ul><li>FREE product catalogue</li><li>Track Deliveries</li><li>Flexible delivery options</li></ul>';
		$this->mPage .= '<form action="' . $this->mSecureBaseDir . '/formHandlers/NewCustomerHandler.php" method="post" name="newCustomerForm" id="newCustomerForm">';
		$this->mPage .= '<input type="hidden" name="newCustomerSubmitted" id="newCustomerSubmitted" value="1" />';
		$this->mPage .= '<input type="image" src="' . $this->mBaseDir . '/images/createAccountButton.png" />';
		$this->mPage .= '</form>';
		$this->mPage .= '</div><!-- Close newCustomerContainer -->';
		$this->mPage .= '<div id="returningCustomerContainer">';
		$this->mPage .= '<h2>Returning Customer</h2>';
		$this->mPage .= '<form action="' . $this->mSecureBaseDir . '/formHandlers/ReturningCustomerHandler.php" method="post" name="returningCustomerForm" id="returningCustomerForm" onsubmit="return validateLoginForm(this)">';
		$this->mPage .= $postageMethod;
		$this->mPage .= '<label for="loginEmail">Email:</label><input type="text" name="loginEmail" id="loginEmail" /><br />';
		$this->mPage .= '<label for="loginPassword">Password:</label><input type="password" name="loginPassword" id="loginPassword" />';
		$this->mPage .= '<input type="image" src="' . $this->mBaseDir . '/images/loginButton.png" />';
		$this->mPage .= '<div id="error"></div>';
		$this->mPage .= '</form>';
		$this->mPage .= '<form action="' . $this->mSecureBaseDir . '/formHandlers/CheckoutForgotPasswordHandler.php" method="post">';
		$this->mPage .= '<input type="submit" value="Forgotten Your Password?" id="forgotPasswordSubmit" />';
		$this->mPage .= '</form>';
		$this->mPage .= '</div><!-- Close returningCustomerContainer -->';
		$this->mPage .= '<div style="clear: both"></div><br /><strong>Having problems?</strong><br />Call our customer service team on: <strong>01753 572741</strong><br />';
		$this->mPage .= '</div><!-- Close loginContainer -->';
	}
	function LoadLeftColumn() {
		$leftColView = new CheckoutLeftColView ( );
		$this->mPage .= $this->mPublicLayoutHelper->OpenLeftCol ();
		$this->mPage .= $leftColView->LoadDefault ( $this->mCatalogue );
		$this->mPage .= $this->mPublicLayoutHelper->CloseLeftCol ();
	} // End LoadLeftColumn()

	//! Loads the right column, including RightColView
	function LoadRightColumn() {
		$rightColView = new CheckoutRightColView ( $this->mCatalogue, $this->mSessionHelper, true );
		$this->mPage .= $rightColView->LoadDefault ();
	}

	//! Just here to make sure the layout doesn't break - doesn't actually do anything (no links displayed)
	function LoadAccountNavigation() {
		$accountNavigation = new AccountNavigationView ( );
		$this->mPage .= $accountNavigation->LoadDefault ( false );
	}

	//! Just here to not break the layout
	function LoadOtherSites() {
		$otherSites = new OtherSitesView ( );
		$this->mPage .= $otherSites->LoadDefault ( false );
	}
} // End CheckoutView

?>