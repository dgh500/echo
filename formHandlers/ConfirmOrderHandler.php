<?php
require_once ('../autoload.php');

#header('Content-type: application/xml; charset="UTF-8"');


//! Confirms the orders from the admin side
class ConfirmOrderHandler {

	var $mBasket;

	//! Initialise internal vars
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mSessionHelper = new SessionHelper ( );
		$this->mRegistry = Registry::getInstance ();
		$this->mLocalMode = $this->mRegistry->localMode;
		$this->mRequestUrl = $this->mRegistry->paymentProcessingUrl;
		$this->mOrderController = new OrderController ( );
		#($this->mRegistry->debugMode ? $this->mFh = fopen ( '../' . $this->mRegistry->debugDir . '/ConfirmOrderHandlerLog.txt', 'a' ) : NULL);
		($this->mRegistry->localMode ? $this->mOrderPrefix = 'ECHOL' : $this->mOrderPrefix = 'ECHO');
	}

	//! Make the input values safe
	function Validate($postArr) {
		$this->mClean ['cardNumber'] = $this->mValidationHelper->MakeSafe ( $postArr ['cardNumber'] );
		$this->mClean ['cardType'] = $this->mValidationHelper->MakeSafe ( $postArr ['cardType'] );
		$this->mClean ['validFromMonth'] = $this->mValidationHelper->MakeSafe ( $postArr ['validFromMonth'] );
		$this->mClean ['validFromYear'] = $this->mValidationHelper->MakeSafe ( $postArr ['validFromYear'] );
		$this->mClean ['expiryDateMonth'] = $this->mValidationHelper->MakeSafe ( $postArr ['expiryDateMonth'] );
		$this->mClean ['expiryDateYear'] = $this->mValidationHelper->MakeSafe ( $postArr ['expiryDateYear'] );
		$this->mClean ['issueNumber'] = $this->mValidationHelper->MakeSafe ( $postArr ['issueNumber'] );
		$this->mClean ['cardHoldersName'] = $this->mValidationHelper->MakeSafe ( $postArr ['cardHoldersName'] );
		$this->mClean ['cvn'] = $this->mValidationHelper->MakeSafe ( $postArr ['cardVerificationNumber'] );
	}

	//! Process Order
	function ProcessOrder($basketId) {

		// Initialise basket
		$this->mBasket = new BasketModel ( $basketId );
		$this->mOrder = $this->mBasket->GetOrder ();

		// Local/Production Mode
		if ($this->mLocalMode) {
			$orderPrefix = 'ECHOL';
		} else {
			$orderPrefix = 'ECHO';
		}

		// Construct request
		$this->mRequest = $this->mOrderController->ConstructOrderRequest ( $this->mOrder, $this->mClean, $orderPrefix, 2, 2, 'E' ); // 2,2 means don't do AVS or 3D-Secure, and use MOTO account
			var_dump($this->mRequest);die();
		// Send Request
		$this->mResult = $this->mOrderController->SendOrderRequest ( $this->mRequest, $this->mRequestUrl );

		// Format result set and process status
		$this->mResultArr = $this->mOrderController->FormatProtxResponse ( $this->mResult );

		// Get the important parts of the result
		$this->mOrderStatus = $this->mResultArr ['Status'];
		$this->mTransactionId = @$this->mResultArr ['VPSTxId'];
		$this->mSecurityKey = @$this->mResultArr ['SecurityKey'];
		$this->mStatusDesc = @$this->mResultArr ['StatusDetail'];
		$this->mTxAuthNo = @$this->mResultArr ['TxAuthNo'];

		// Store details in the database
		$this->mOrder->SetTransactionId ( $this->mTransactionId );
		$this->mOrder->SetSecurityKey ( $this->mSecurityKey );
		$this->mOrder->SetTxAuthNo ( $this->mTxAuthNo );
		$this->mOrder->SetTransactionDate ( time () );

		// Debug
		#($this->mRegistry->debugMode ? fwrite ( $this->mFh, "\r\n Request: \r\n" ) : NULL);
		#($this->mRegistry->debugMode ? fwrite ( $this->mFh, $this->mRequest ) : NULL);

		// Handle status response
		switch ($this->mOrderStatus) {
			case 'MALFORMED' :
			case 'INVALID' :
				echo '<h2 style="font-family: Arial, Sans-Serif; color: #FF0000;">Error with request - Probably a typing mistake - check address & card number.</h2>';
				echo '<span style="font-family: Arial, Sans-Serif;"><strong>Error Message: </strong>'.$this->mStatusDesc.'</span>';
				$orderStatusController = new OrderStatusController ( );
				$orderStatus = $orderStatusController->GetFailed ();
				$this->mOrder->SetStatus ( $orderStatus );
				break;
			case 'ERROR' :
				echo '<h2 style="font-family: Arial, Sans-Serif; color: #FF0000;">Error with request (Problem with Protx)</h2>';
				echo '<span style="font-family: Arial, Sans-Serif;"><strong>Error Message: </strong>' . $this->mStatusDesc . '</span>';
				$orderStatusController = new OrderStatusController ( );
				$orderStatus = $orderStatusController->GetFailed ();
				$this->mOrder->SetStatus ( $orderStatus );
				break;
			case 'OK' :
			case 'AUTHENTICATED' :
			case 'REGISTERED' :
				echo '<h2 style="font-family: Arial, Sans-Serif; color: #000000;">Order Taken</h2>';
				echo '<span style="font-family: Arial, Sans-Serif;"><strong>Order Number:</strong> ' . $orderPrefix . $this->mOrder->GetOrderId () . '</span>';
				$orderStatusController = new OrderStatusController ( );
				$orderStatus = $orderStatusController->GetAuthorised ();
				$this->mOrder->SetStatus ( $orderStatus );
				$this->mOrderController->PrepareCustomerEmail ( $this->mOrder->GetCustomer ()->GetEmail (), $this->mOrder, $this->mOrderPrefix );
				$this->mOrderController->SendEmail ();
				$this->mOrderController->PrepareAndSendEmail ( "orders@echosupplements.com", $this->mOrder, $this->mOrderPrefix );
				break;
			case 'NOTAUTHED' :
			case 'REJECTED' :
				echo '<h2 style="font-family: Arial, Sans-Serif; color: #FF0000;">' . $this->mOrderStatus . ' - Order NOT Taken (Problem with Customer\'s Card)</h2>';
				echo '<span style="font-family: Arial, Sans-Serif;"><strong>Error Message: </strong>' . $this->mStatusDesc . '</span>';
				$orderStatusController = new OrderStatusController ( );
				$orderStatus = $orderStatusController->GetFailed ();
				$this->mOrder->SetStatus ( $orderStatus );
				break;
			case '3DAUTH' :
				echo '<h2>3D AUTH...</h2>';
				$orderStatusController = new OrderStatusController ( );
				$orderStatus = $orderStatusController->GetFailed ();
				$this->mOrder->SetStatus ( $orderStatus );
				break;
		} // End switch


		// Empty Basket & Reload
		$this->mSessionHelper->Reset ();
		echo '
		<script language="javascript" type="text/javascript">
			top.ordersMenu.document.location.reload();
		</script>';
	} // ConstructOrderRequest


}

$handler = new ConfirmOrderHandler ( );
$handler->Validate ( $_POST );
$handler->ProcessOrder ( $_POST ['basketId'] );

?>