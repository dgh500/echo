<?php
set_time_limit ( 60 );
require_once ('../autoload.php');

/*foreach($_POST as $key=>$value) {
	echo '<strong>'.$key.':</strong> '.$value.'<br />';
}*/

//! The final stage of the checkout - processes the card details
class CheckoutBillingHandler extends Handler {

	var $mClean;

	//! Initialise needed variables
	function __construct() {
		parent::__construct ();
		$this->mSessionHelper = new SessionHelper ( );
		$this->mOrderController = new OrderController ( );
		$this->mRegistry = Registry::getInstance ();
		$this->mBasket = $this->mSessionHelper->GetBasket ();
		$this->mRequestUrl = $this->mRegistry->paymentProcessingUrl;

		// A little debugging
		#($this->mRegistry->debugMode ? $this->mFh = fopen ( '../' . $this->mRegistry->debugDir . '/CheckoutBillingHandlerLog.txt', 'a' ) : NULL);

		// So that local test orders don't interfere with live ones
		($this->mRegistry->localMode ? $this->mOrderPrefix = 'ECHO' : $this->mOrderPrefix = 'ECHO');
	}

	//! Make the inputted values safe
	function Validate($postArr) {
		$this->mClean['cardHoldersName'] = $this->mValidationHelper->RemoveAllSpaces ( $this->mValidationHelper->MakeSafe ( $postArr ['cardHoldersName'] ) );
		$this->mClean['cardNumber'] 	 = $this->mValidationHelper->RemoveAllSpaces ( $this->mValidationHelper->MakeSafe ( $postArr ['cardNumber'] ) );
		$this->mClean['cardType'] 		 = $this->mValidationHelper->MakeSafe ( $postArr ['cardType'] );
		$this->mClean['validFromMonth']  = $this->mValidationHelper->MakeSafe ( $postArr ['validFromMonth'] );
		$this->mClean['validFromYear'] 	 = $this->mValidationHelper->MakeSafe ( $postArr ['validFromYear'] );
		$this->mClean['expiryDateMonth'] = $this->mValidationHelper->MakeSafe ( $postArr ['expiryDateMonth'] );
		$this->mClean['expiryDateYear']  = $this->mValidationHelper->MakeSafe ( $postArr ['expiryDateYear'] );
		$this->mClean['issueNumber'] 	 = $this->mValidationHelper->MakeSafe ( $postArr ['issueNumber'] );
		$this->mClean['cvn'] 			 = $this->mValidationHelper->MakeSafe ( $postArr ['cvn'] );
	}

	//! Process the order
	function ProcessOrder() {
		// Initialise basket
		$this->mOrder = $this->mBasket->GetOrder();
		$this->mOrder->SetCatalogue($this->mOrder->GetBasket()->GetCatalogue());

		// Set Order Total
		#$deliveryCountry = $this->mOrder->GetShippingAddress()->GetCountry();
		#if ($deliveryCountry->IsVatFree()) {
		#	$vat = $this->mMoneyHelper->VAT($this->mBasket->GetTotal());
		#	$noVat = $this->mBasket->GetTotal() - $vat;
		#	$this->mOrder->SetTotalPrice($noVat);
		#} else {
		$this->mOrder->SetTotalPrice($this->mBasket->GetTotal());
		#}

		// Set Postage
		if($this->mSessionHelper->GetPostage()) {
			$this->mOrder->SetTotalPostage($this->mSessionHelper->GetPostage());
		} else {
			$this->mOrder->SetTotalPostage(0);
		}

		// Convert the basket into order items for saving
		$this->mOrder->ConvertBasketIntoOrder();

		// Set the dispatch estimate
		if($this->mBasket->ContainsNonStockProducts()) {
			// 3-5 Day
			$dispatchDate = new DispatchDateModel(3);
			$this->mOrder->SetDispatchDate($dispatchDate);
		} // Defaults to same day anyway

		// Local/Production Mode
		if ($this->mLocalMode) {
			$orderPrefix = 'ECHOL';
		} else {
			$orderPrefix = 'ECHO';
		}
		// SagePay or PayPal
		if($this->mRegistry->paymentGateway == 'sagepay') {
			// Construct request
			$this->mRequest = $this->mOrderController->ConstructOrderRequest($this->mOrder,$this->mClean,$orderPrefix,3,0,'E'); // 2,1 means don't do AVS, but DO do 3D-Secure, and use E-Commerce merchant number
#var_dump($this->mRequest);die();
			// Send Request
			$this->mResult = $this->mOrderController->SendOrderRequest($this->mRequest, $this->mRequestUrl);

			// Format result set and process status
			$this->mResultArr = $this->mOrderController->FormatProtxResponse ( $this->mResult );
#var_dump($this->mResultArr);die();
			// Get the important parts of the result
# TEST			$this->mOrderStatus 	= 'AUTHENTICATED';
			$this->mOrderStatus 	= $this->mResultArr ['Status'];
			$this->mTransactionId 	= @$this->mResultArr ['VPSTxId'];
			$this->mSecurityKey 	= @$this->mResultArr ['SecurityKey'];
			$this->mStatusDesc 		= @$this->mResultArr ['StatusDetail'];
			$this->mTxAuthNo 		= @$this->mResultArr ['TxAuthNo'];
			$this->mStatusDetail 	= @$this->mResultArr['StatusDetail'];

			// Debug
			#($this->mRegistry->debugMode ? fwrite ( $this->mFh, "\r\n---------------\r\nECHO".$this->mOrder->GetOrderId()." - Protx Response: \r\n".$this->mStatusDetail ) : NULL);

			// Store details in the database
			$this->mOrder->SetTransactionId ( $this->mTransactionId );
			$this->mOrder->SetSecurityKey ( $this->mSecurityKey );
			$this->mOrder->SetTxAuthNo ( $this->mTxAuthNo );
			$this->mOrder->SetTransactionDate ( time () );
		} else {
			// Use Paypal as the payment gateway
			// Construct request
			$this->mRequest = $this->mOrderController->ConstructPaypalOrderRequest($this->mOrder,$this->mClean,$orderPrefix); // 2,1 means don't do AVS, but DO do 3D-Secure, and use E-Commerce merchant number

			// Send Request
			$this->mResult = $this->mOrderController->SendPaypalOrderRequest($this->mRequest, $this->mRequestUrl);
	#		var_dump($this->mResult);die();
			// Format result set and process status
			parse_str ( urldecode($this->mResult),$this->mResultArr );

			// Get the important parts of the result
			$this->mOrderStatus 	= $this->mResultArr['ACK'];
			$this->mTransactionId 	= @$this->mResultArr['TRANSACTIONID'];
			$this->mTimestamp 		= @$this->mResultArr['TIMESTAMP'];
			$this->mCorrelationId	= @$this->mResultArr['CORRELATIONID'];
			$this->mAvsCode			= @$this->mResultArr['AVSCODE'];
			$this->mCvvMatch		= @$this->mResultArr['CVV2MATCH'];

			// Store details in the database
			$this->mOrder->SetTransactionId ( $this->mTransactionId );
			$this->mOrder->SetTxAuthNo		( $this->mCorrelationId ); // Use TxAuthNo == CorrelationId
			$this->mOrder->SetTransactionDate (time());
			$this->mOrder->SetPaypalOrder(1);
		}

		// SAGE PAY - DEAL WITH RESULT
		if($this->mRegistry->paymentGateway == 'sagepay') {

			// Handle status response
			switch ($this->mOrderStatus) {
				case 'ERROR' :
					$orderStatusController = new OrderStatusController ( );
					$orderStatus = $orderStatusController->GetFailed ();
					$this->mOrder->SetStatus ( $orderStatus );
					$this->mSessionHelper->SetCheckoutStage ( 'checkoutComplete' );
					$this->mSessionHelper->SetCheckoutStatus ( 'E' );
					$this->CheckoutStatus = 'failure';
					break;
				case 'MALFORMED' :
				case 'INVALID' :
					$orderStatusController = new OrderStatusController ( );
					$orderStatus = $orderStatusController->GetFailed ();
					$this->mOrder->SetStatus ( $orderStatus );
					$this->mSessionHelper->SetCheckoutStage ( 'checkoutComplete' );
					$this->mSessionHelper->SetCheckoutStatus ( 'I' );
					$this->CheckoutStatus = 'failure';
					// Send US an email saying that the order has failed
					$this->mOrderController->PrepareAndSendFailureEmail("orders@echosupplements.com", $this->mOrder, $this->mOrderPrefix );
					break;
				case 'OK' :
				case 'AUTHENTICATED' :
				case 'REGISTERED' :
					$orderStatusController = new OrderStatusController ( );
					$orderStatus = $orderStatusController->GetAuthorised ();
					$this->mOrder->SetStatus ( $orderStatus );
					$this->mSessionHelper->SetCheckoutStage ( 'checkoutComplete' );
					$this->mSessionHelper->SetCheckoutStatus ( 'A' );
					$customer = $this->mOrder->GetCustomer ();
					$customer->SetFirstOrder ( 0 );
					$this->mOrderController->PrepareAndSendCustomerEmail ( $this->mOrder->GetCustomer ()->GetEmail (), $this->mOrder, $this->mOrderPrefix );
					$this->mOrderController->PrepareAndSendEmail ( "orders@echosupplements.com", $this->mOrder, $this->mOrderPrefix );
					$this->CheckoutStatus = 'success';
					$this->mBasket->UpdateStockLevels();
					break;
				case 'NOTAUTHED' :
				case 'REJECTED' :
					$orderStatusController = new OrderStatusController ( );
					$orderStatus = $orderStatusController->GetFailed ();
					$this->mOrder->SetStatus ( $orderStatus );
					$this->mSessionHelper->SetCheckoutStage ( 'checkoutComplete' );
					$this->mSessionHelper->SetCheckoutStatus ( 'D' );
					$this->CheckoutStatus = 'failure';
					break;
				case '3DAUTH' :
					$this->mSessionHelper->SetCheckoutStage ( '3DAuth' );
					$this->CheckoutStatus = '3DAuth';
					$_SESSION ['PAReq'] = $this->mResultArr ['PAReq'];
					$_SESSION ['MD'] = $this->mResultArr ['MD'];
					$_SESSION ['ACSURL'] = $this->mResultArr ['ACSURL'];
					$_SESSION ['TERM_URL'] = $this->mRegistry->secureBaseDir . '/checkout.php?threeDSecureComplete=1';
					break;
			} // End switch

			// If the request was unsuccessful to Protx (timeout usually) then update the checkout stages as usual
			if(!$this->mResult) {
				$orderStatusController = new OrderStatusController( );
				$orderStatus = $orderStatusController->GetFailed();
				$this->mOrder->SetStatus($orderStatus);
				$this->mSessionHelper->SetCheckoutStage('checkoutComplete');
				$this->mSessionHelper->SetCheckoutStatus('E');
				$this->CheckoutStatus = 'failure';
			}
	} else {
		// PAYPAL DEAL WITH RESULT SECTION
		switch($this->mOrderStatus) {
			case 'Success' :
				$orderStatusController = new OrderStatusController ( );
				$orderStatus = $orderStatusController->GetAuthorised ();
				$this->mOrder->SetStatus ( $orderStatus );
				$this->mSessionHelper->SetCheckoutStage ( 'checkoutComplete' );
				$this->mSessionHelper->SetCheckoutStatus ( 'A' );
				$customer = $this->mOrder->GetCustomer ();
				$customer->SetFirstOrder ( 0 );
				$this->mOrderController->PrepareAndSendCustomerEmail ( $this->mOrder->GetCustomer ()->GetEmail (), $this->mOrder, $this->mOrderPrefix );
				$this->mOrderController->PrepareAndSendEmail ( "orders@echosupplements.com", $this->mOrder, $this->mOrderPrefix );
				$this->CheckoutStatus = 'success';
				break;
			default:
				var_dump($this->mRequestUrl);die();
				$orderStatusController = new OrderStatusController( );
				$orderStatus = $orderStatusController->GetFailed();
				$this->mOrder->SetStatus($orderStatus);
				$this->mSessionHelper->SetCheckoutStage('checkoutComplete');
				$this->mSessionHelper->SetCheckoutStatus('E');
				$this->CheckoutStatus = 'failure';
			break;
		} // End switch
	} // End if SagePay

#	die();

	$secureBaseDir = $this->mRegistry->secureBaseDir;
	$sendTo = $secureBaseDir . '/checkout.php';
	header ( 'Location: ' . $sendTo );

	} // End ProcessOrder

} // End CheckoutBillingHandler

try {
	$handler = new CheckoutBillingHandler ( );
	$handler->Validate ( $_POST );
	$handler->ProcessOrder ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>