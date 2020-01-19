<?php

require_once ('../autoload.php');
//! Processes an order
class AddOrderView2Handler {

	//! Initialises the basket
	function __construct($sessionId) {
		$this->mRegistry = Registry::getInstance ();
		$this->mLocalMode = $this->mRegistry->localMode;
		$this->mRequestUrl = $this->mRegistry->paymentProcessingUrl;
	#	($this->mRegistry->debugMode ? $this->mFh = @fopen ( '../' . $this->mRegistry->debugDir . '/NewCustomerOrderHandlerLog.txt', 'w+' ) : NULL);
		$this->mBasket 				= new BasketModel ( $sessionId );
		$this->mSessionHelper 		= new SessionHelper;
		$this->mOrderController 	= new OrderController;
		$this->mCustomerController 	= new CustomerController;
		$this->mValidationHelper 	= new ValidationHelper;
		$this->mAddressController 	= new AddressController;
	}

	//! Validates the input and cleans it into the mClean array
	function Validate($postArr) {
		/******* Customer Tab *******/
	#	try {
			$this->mClean['customer']		= new CustomerModel($postArr['customerId'],'id');
			$this->mClean['deliveryAddress']= new AddressModel($postArr['deliveryId']);
			$this->mClean['billingAddress']	= new AddressModel($postArr['billingId']);
			// Referrer
			$this->mClean['referrer'] 		= $this->mValidationHelper->OnlyNumeric($postArr['referrerId']);
			// Notes
			$this->mClean['notes'] 			= $this->mValidationHelper->OnlyAlphaNumeric($postArr['notes']);
			// Staff
			$this->mClean['staffName'] 		= $this->mValidationHelper->OnlyAlphaNumeric($postArr['staffName']);
			// Catalogue Wanted?
			$this->mClean['catalogueWanted']= $this->mValidationHelper->OnlyAlphaNumeric($postArr['catalogueWanted']);
		/******* Billing Tab *******/
			$this->mClean['cardHoldersName']		= $this->mValidationHelper->OnlyAlphaNumeric($postArr['cardHoldersName']);
			$this->mClean['cardType']				= $this->mValidationHelper->OnlyAlphaNumeric($postArr['cardType']);
			$this->mClean['cardNumber']				= $this->mValidationHelper->OnlyAlphaNumeric($postArr['cardNumber']);
			$this->mClean['validFromMonth']			= $this->mValidationHelper->OnlyAlphaNumeric($postArr['validFromMonth']);
			$this->mClean['validFromYear']			= $this->mValidationHelper->OnlyAlphaNumeric($postArr['validFromYear']);
			$this->mClean['expiryDateMonth']		= $this->mValidationHelper->OnlyAlphaNumeric($postArr['expiryDateMonth']);
			$this->mClean['expiryDateYear']			= $this->mValidationHelper->OnlyAlphaNumeric($postArr['expiryDateYear']);
			$this->mClean['cvn']					= $this->mValidationHelper->OnlyAlphaNumeric($postArr['cardVerificationNumber']);
			$this->mClean['issueNumber']			= $this->mValidationHelper->OnlyAlphaNumeric($postArr['issueNumber']);
		/****** Basket Tab ********/
			$this->mClean['currentPostage'] 		= $postArr['currentPostage'];
			$this->mClean['currentPostageMethod'] 	= $postArr['currentPostageMethod'];
			$this->mClean['currentDeliveryCountry'] = $postArr['countryId'];
	#	} catch(Exception $e) {
			// Do nothing
	#	}
	} // End Validate()

	function SaveOrder() {
		// Create the order
		$this->mOrder = $this->mOrderController->CreateOrder($this->mBasket);
		$this->mOrder->SetGoogleCheckout(false);
		$this->mOrder->SetCatalogue($this->mBasket->GetCatalogue());

		// Create the referrer
		$this->mReferrer = new ReferrerModel($this->mClean['referrer']);

		// Create the postage method
		$this->mPostageMethod = new PostageMethodModel($this->mClean['currentPostageMethod']);
		// And courier
		$this->mCourier = $this->mPostageMethod->GetCourier();

		// Set order details
		$this->mOrder->SetShippingAddress($this->mClean['deliveryAddress']);
		$this->mOrder->SetBillingAddress($this->mClean['billingAddress']);
		$this->mOrder->SetTotalPrice($this->mBasket->GetTotal());
		$this->mOrder->SetTotalPostage($this->mClean['currentPostage']);
		$this->mOrder->SetCustomer($this->mClean['customer']);
		$this->mOrder->SetStaffName($this->mClean['staffName']);
		$this->mOrder->SetNotes($this->mClean['notes']);
		$this->mOrder->SetReferrer($this->mReferrer);
		$this->mOrder->SetBrochure($this->mClean['catalogueWanted']);
		$this->mOrder->SetPostageMethod($this->mPostageMethod);
		$this->mOrder->SetCourier($this->mCourier);

		// Set Delivery Country
		if(isset($this->mClean['currentDeliveryCountry'])) {
			$deliveryCountry = new CountryModel($this->mClean['currentDeliveryCountry']);
			$this->mOrder->GetShippingAddress()->SetCountry($deliveryCountry);
		}

		// Convert the order to an item model
		$this->mOrder->ConvertBasketIntoOrder();

		// Local/Production Mode
		if ($this->mLocalMode) {
			$this->mOrderPrefix = 'ECHOL';
		} else {
			$this->mOrderPrefix = 'ECHO';
		}

		// Construct request
		$this->mRequest = $this->mOrderController->ConstructOrderRequest($this->mOrder,$this->mClean,$this->mOrderPrefix,3,2,'E'); // 2,2 means don't do AVS or 3D-Secure, and use MOTO account

		// Send Request
		$this->mResult = $this->mOrderController->SendOrderRequest($this->mRequest,$this->mRequestUrl);

		// Format result set and process status
		$this->mResultArr = $this->mOrderController->FormatProtxResponse ( $this->mResult );

		// Get the important parts of the result
		$this->mOrderStatus 	= $this->mResultArr['Status'];
		$this->mTransactionId 	= @$this->mResultArr['VPSTxId'];
		$this->mSecurityKey 	= @$this->mResultArr['SecurityKey'];
		$this->mStatusDesc 		= @$this->mResultArr['StatusDetail'];
		$this->mTxAuthNo 		= @$this->mResultArr['TxAuthNo'];

		// Store details in the database
		$this->mOrder->SetTransactionId($this->mTransactionId);
		$this->mOrder->SetSecurityKey($this->mSecurityKey);
		$this->mOrder->SetTxAuthNo($this->mTxAuthNo);
		$this->mOrder->SetTransactionDate(time());

		// Delete any misc products that were created
		$productController = new ProductController;
		if(isset($_SESSION['miscProductsToDelete'])) {
			foreach($_SESSION['miscProductsToDelete'] as $productId) {
				if(is_int($productId)) {
					$product = new ProductModel($productId);
					$productController->DeleteProduct($product);
				}
			}
		}

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
				echo '<span style="font-family: Arial, Sans-Serif;"><strong>Order Number:</strong> ' . $this->mOrderPrefix . $this->mOrder->GetOrderId () . '</span>';
				echo '<h1><a href="javascript: self.close()">Close Window</a></h1>';
				$orderStatusController = new OrderStatusController ( );
				$orderStatus = $orderStatusController->GetAuthorised ();
				$this->mOrder->SetStatus ( $orderStatus );
				$this->mOrderController->PrepareAndSendCustomerEmail( $this->mOrder->GetCustomer ()->GetEmail (), $this->mOrder, $this->mOrderPrefix );
				$this->mOrderController->PrepareAndSendEmail ( "orders@echosupplements.com", $this->mOrder, $this->mOrderPrefix );
				// Update stock levels
				$this->mBasket = $this->mOrder->GetBasket();
				$this->mBasket->UpdateStockLevels();
				// Empty Basket & Reload
				$this->mSessionHelper->Reset ();
				echo '
				<script language="javascript" type="text/javascript">
					top.ordersMenu.document.location.reload();
				</script>';
				break;
			case 'NOTAUTHED' :
			case 'REJECTED' :
				echo '<h2 style="font-family: Arial, Sans-Serif; color: #FF0000;">' . $this->mOrderStatus . ' - Order NOT Taken (Problem with Customer\'s Card)</h2>';
				echo '<span style="font-family: Arial, Sans-Serif;"><strong>Error Message: </strong>' . $this->mStatusDesc . '</span>';
				$orderStatusController = new OrderStatusController ( );
				$orderStatus = $orderStatusController->GetFailed ();
				$this->mOrder->SetStatus ( $orderStatus );
				break;
		} // End switch
	} // End SaveOrder

} // End AddOrderView2Handler

#echo '<pre>'; var_dump($_POST);echo '</pre>';
#echo '<pre>';var_dump($_SESSION);echo '</pre>';
#die();
foreach($_SESSION as $key=>$value) {
	$formVars[$key] = $value;
}
foreach($_POST as $key=>$value) {
	$formVars[$key] = $value;
}
#echo '<pre>';var_dump($formVars);echo '</pre>';

try {
	$handler = new AddOrderView2Handler(session_id());
	$handler->Validate($formVars);
#	die();
	$handler->SaveOrder();
} catch(Exception $e) {
	echo $e->getMessage();
}

?>