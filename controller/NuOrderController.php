<?php

//! Deals with tasks to do with orders such as creating, deleting etc.
class OrderController {
	
	//! Obj:PDO : Database used to access the underlying SQL
	var $mDatabase;
	//! Email to be sent
	var $mMail;
	
	//! Constructor, initiates the database access
	function __construct() {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
		$this->mLocalMode = $registry->localMode;
		$this->mValidationHelper = new ValidationHelper ( );
		if ($this->mLocalMode) {
			$this->mOrderPrefix = 'DBDL0';
		} else {
			$this->mOrderPrefix = 'ECHO';
		}
	}
	
	//! Creates an order and returns it as Obj:OrderModel
	/*!
	 * @param [in] basket : Obj:BasketModel - the basket with the items sold in this order
	 * @return Obj:OrderModel - the order created
	 */
	function CreateOrder($basket) {
		$orderStatusController = new OrderStatusController ( );
		$currencyController = new CurrencyController ( );
		$courierController = new CourierController ( );
		$dispatchDateController = new DispatchDateController ( );
		$postageMethodController = new PostageMethodController ( );
		
		$create_order_sql = '
		INSERT INTO tblOrder
			(`Created_Date`,`Basket_ID`,`Status_ID`,`Notes`,`Currency_ID`,`Downloaded`,`Brochure`,`Courier_ID`,`Dispatch_Date_ID`,`Postage_Method_ID`)
			VALUES
			(\'' . time () . '\',\'' . $basket->GetBasketId () . '\',\'' . $orderStatusController->GetDefault ()->GetStatusId () . '\',\'\',\'' . $currencyController->GetDefault ()->GetCurrencyId () . '\',\'0\',\'0\',\'' . $courierController->GetDefault ()->GetCourierId () . '\',\'' . $dispatchDateController->GetDefaultDispatchDate ()->GetDispatchDateId () . '\',\'' . $postageMethodController->GetDefault ()->GetPostageMethodId () . '\')
		';
		if ($this->mDatabase->query ( $create_order_sql )) {
			$sql = 'SELECT Order_ID FROM tblOrder ORDER BY Order_ID DESC LIMIT 1';
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				return new OrderModel ( $resultObj->Order_ID );
			} else {
				$error = new Error ( 'Could not select the order just created.' );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		} else {
			$error = new Error ( 'Could not create new order.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	
	} // End CreateOrder
	

	//! Sends an order request to the correct URL and returns the response (encapsules the CURL details)
	/*!
	 * @param $request : Str - The request
	 * @param $requestUrl : Str - The payment processing URL
	 * @return Either Bool False on failure, or the result (raw)
	 */
	function SendOrderRequest($request, $requestUrl) {
		$ch = curl_init ();
		// Set the URL
		curl_setopt ( $ch, CURLOPT_URL, $requestUrl );
		// No headers
		curl_setopt ( $ch, CURLOPT_HEADER, false );
		// It's a POST request
		curl_setopt ( $ch, CURLOPT_POST, true );
		// Set the fields for the POST
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $request );
		// Return it direct, don't print it out
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		// Timeout in 30 seconds
		curl_setopt ( $ch, CURLOPT_TIMEOUT, 30 );
		// The next two lines must be present for the kit to work with newer version of cURL
		// You should remove them if you have any problems in earlier versions of cURL
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, true );
		
		// Raw response
		$response = curl_exec ( $ch );
		if (curl_errno ( $ch )) {
			// Log Error
			$errorMsg = 'ERROR -> ' . curl_errno ( $ch ) . ': ' . curl_error ( $ch );
			$fh = @fopen ( 'OrderControllerLog.txt', 'a' );
			@fwrite ( $fh, $errorMsg );
			@fclose ( $fh );
			// Return indication of error
			return false;
		} else {
			return $response;
		}
	}
	
	//! Converts the Name=Value pairs in the Protx response and returns XX
	/*! 
	 * @param $rawResponse - A Name=Value string pairing
	 * @return Array[Name] = Value
	 */
	function FormatProtxResponse($rawResponse) {
		$response = split ( chr ( 10 ), $rawResponse );
		// Tokenise the response
		for($i = 0; $i < count ( $response ); $i ++) {
			// Find position of first "=" character
			$splitAt = strpos ( $response [$i], "=" );
			// Create an associative (hash) array with key/value pairs ('trim' strips excess whitespace)
			$output [trim ( substr ( $response [$i], 0, $splitAt ) )] = trim ( substr ( $response [$i], ($splitAt + 1) ) );
		} // END for ($i=0; $i<count($response); $i++)
		return $output;
	}
	
	//! Builds and returns the request for the order supplied
	/*!
	 * @param $order : Obj : OrderModel - The order to construct the request for
	 * @param $billingDetails : Array - The card numbers etc. in format: 
	 *		['cardNumber'],['expiryDateMonth'],['expiryDateYear'],['cvn'],['cardHoldersName'],['validFromMonth'],['validFromYear'],['issueNumber']
	 * @param $prefix : Str - Either 'ECHO' or 'DBDL0' for production/testing purposes
	 * @param $avsCheck - Int - Fine tune the AVS check (0 = Admin Panel rules by default)
	 * 		0 = If Enabled, use them (in admin panel)
	 * 		1 = Force Checks
	 *		2 = Force NO Check
	 * 		3 = Force Checks even if not enabled for the account, but DON'T apply rules
	 * @param $3dSecure - Int - Fine tune 3D Secure rules
	 *		0 = If enabled, use them (in admin panel)
	 *		1 = Force 3D Secure (and apply rules)
	 *		2 = Do not perform 3D Secure checks, only and always authorise
	 *		3 = Force 3D secure checks but always obtain auth code, irrespective of rule base
	 * @return Str - The XML to send
	 */
	function ConstructOrderRequest($order, $billingDetails, $prefix = 'ECHO', $avsCheck = 0, $ThreeDSecure = 0) {
		// Fraud Checks
		$APPLY_AVS_CHECK = $avsCheck;
		$APPLY_3D_SECURE = $ThreeDSecure;
		
		// Initialise card details
		$ORDER_ID = $prefix . $order->GetOrderId ();
		$CARD_NUMBER = $billingDetails ['cardNumber'];
		$CARD_TYPE = $this->CardType ( $CARD_NUMBER );
		$EXPIRY_DATE = $billingDetails ['expiryDateMonth'] . substr ( $billingDetails ['expiryDateYear'], 2, 2 ); // MMYY Format 
		$CVV_NUMBER = $billingDetails ['cvn'];
		$CARD_HOLDERS_NAME = $billingDetails ['cardHoldersName'];
		$ORDER_TOTAL = trim ( number_format ( $order->GetTotalPrice () + $order->GetTotalPostage (), 2 ) ); // No whitespace => Amount in pence
		$ORDER_POSTAGE = trim ( number_format ( $order->GetTotalPostage (), 2 ) ); // No whitespace => Amount in pence
		

		// Valid from decision
		if ($billingDetails ['validFromMonth'] != 'NA' && $billingDetails ['validFromYear'] != 'NA') {
			$includeValidFrom = true;
			$VALID_FROM_DATE = $billingDetails ['validFromMonth'] . substr ( $billingDetails ['validFromYear'], 2, 2 ); // MMYY Format
		} else {
			$includeValidFrom = false;
		}
		
		// Issue number decision
		if ($billingDetails ['issueNumber'] != '') {
			$includeIssue = true;
			$ISSUE_NUMBER = $billingDetails ['issueNumber'];
		} else {
			$includeIssue = false;
		}
		
		// Construct Request
		$request = 'VPSProtocol=2.23';
		$request .= '&TxType=AUTHENTICATE';
		$request .= '&Vendor=dgh500';
		$request .= '&VendorTxCode=' . $ORDER_ID;
		$request .= '&Amount=' . $ORDER_TOTAL;
		$request .= '&Currency=GBP';
		$request .= '&Description=DESCRIPTION';
		$request .= '&CardHolder=' . $CARD_HOLDERS_NAME;
		$request .= '&CardNumber=' . $CARD_NUMBER;
		if ($includeValidFrom) {
			$request .= '&StartDate=' . $VALID_FROM_DATE;
		}
		$request .= '&ExpiryDate=' . $EXPIRY_DATE;
		if ($includeIssue) {
			$request .= '&IssueNumber=' . $ISSUE_NUMBER;
		}
		$request .= '&CV2=' . $CVV_NUMBER;
		$request .= '&CardType=' . $CARD_TYPE;
		$request .= '&ApplyAVSCV2=' . $APPLY_AVS_CHECK;
		$request .= '&Apply3DSecure=' . $APPLY_3D_SECURE;
		return $request;
	} // End ConstructOrderRequest
	

	//! Constructs and returns a ship-order request
	/*!
	 * @param $order	 - Obj : OrderModel - The order to construct the request for	
	 * @param $shipTotal - Dec - The amount to take
	 * @param $prefix 	 - Str - The prefix for the order (defaults to ECHO)
	 */
	function ConstructShipRequest($order, $shipTotal, $prefix = 'ECHO') {
		// Update DB
		$order->SetActualTaken ( $shipTotal );
		// Initialise Values
		$AMOUNT = trim ( number_format ( $shipTotal, 2 ) );
		$ORDER_ID = $prefix . $order->GetOrderId ();
		$TRANSACTION_ID = $order->GetTransactionId ();
		$SECURITY_KEY = $order->GetSecurityKey ();
		$AUTH_REFERENCE = $order->GetNextAuthRef ();
		
		// Set the authorisation reference for the order
		$order->SetAuthRef ( $AUTH_REFERENCE );
		
		// Construct Request
		$request = 'VPSProtocol=2.23';
		$request .= '&TxType=AUTHORISE';
		$request .= '&Vendor=dgh500';
		$request .= '&VendorTxCode=' . $AUTH_REFERENCE;
		$request .= '&Amount=' . $AMOUNT;
		$request .= '&Description=DESCRIPTION';
		$request .= '&RelatedVPSTxId=' . $TRANSACTION_ID;
		$request .= '&RelatedVendorTxCode=' . $ORDER_ID;
		$request .= '&RelatedSecurityKey=' . $SECURITY_KEY;
		return $request;
	} // End ConstructShipRequest
	

	//! Constructs and returns a cancel-order request
	/*!
	 * @param $order	 - Obj : OrderModel - The order to construct the request for	
	 * @param $prefix 	 - Str - The prefix for the order (defaults to ECHO)
	 */
	function ConstructCancelRequest($order, $prefix = 'ECHO') {
		// Initialise Values
		$ORDER_ID = $prefix . $order->GetOrderId ();
		$TRANSACTION_ID = $order->GetTransactionId ();
		$SECURITY_KEY = $order->GetSecurityKey ();
		
		// Construct Request
		$request = 'VPSProtocol=2.23';
		$request .= '&TxType=CANCEL';
		$request .= '&Vendor=dgh500';
		$request .= '&VendorTxCode=' . $ORDER_ID;
		$request .= '&VPSTxId=' . $TRANSACTION_ID;
		$request .= '&SecurityKey=' . $SECURITY_KEY;
		return $request;
	} // End ConstructShipRequest
	

	//! Constructs and returns a 3D-secure auth request
	/*!
	 * @param $PARES	 - Str - The PA-Result back from Protx
	 * @param $MD	 	 - Str - The MD back from Protx
	 */
	function Construct3DSecureRequest($PARES, $MD) {
		$request = 'VPSProtocol=2.23';
		$request .= '&MD=' . $MD;
		$request .= '&PARes=' . $PARES;
		return $request;
	}
	
	function PrepareCustomerEmail($address, $order, $prefix = 'ECHO') {$registry = Registry::getInstance ();
		$orderView = new PublicOrderView ( );
		$body = 'Your order: ' . $prefix . $order->GetOrderId () . ' has been received. Your bank has reserved the payment for this order, and no money is taken by Deep Blue until goods are dispatched. If there is any delay with your order you will receive an email with an estimated delivery time. Once your order has been dispatched you will receive a final email detailing goods that have been shipped and if applicable a tracking link and number with which you can track the status of your delivery.<br />';
		$body .= $orderView->LoadDefault ( $order->GetOrderId () );
		$text_body = 'Order ECHO' . $order->GetOrderId () . ' received. Your bank has reserved the payment for this order, and no money is taken by Deep Blue until goods are dispatched. If there is any delay with your order you will receive an email with an estimated delivery time. Once your order has been dispatched you will receive a final email detailing goods that have been shipped and if applicable a tracking link and number with which you can track the status of your delivery. \r\n';
		$this->mMail->From = "orders@deepbluedive.com";
		$this->mMail->FromName = "Web Order";
		$this->mMail->Subject = "Website Order - " . $prefix . $order->GetOrderId ();
		$this->mMail->Host = "smtp.gmail.com";
		$this->mMail->Port = 465;
		$this->mMail->Mailer = "smtp";
		$this->mMail->SMTPAuth = true;
		$this->mMail->Username = "info@deepbluedive.com";
		$this->mMail->Password = "d33pblu3";
		$this->mMail->Body = $body;
		$this->mMail->SMTPSecure = "ssl"; // option
		$this->mMail->AltBody = $text_body;
		$this->mMail->AddAddress ( $address );
	}
	
	function PrepareEmail($address, $order, $prefix = 'ECHO') {$registry = Registry::getInstance ();
		$orderView = new PublicOrderView ( );
		$body = $orderView->LoadDefault ( $order->GetOrderId () );
		$text_body = 'Order ' . $order->GetOrderId () . ' received. \r\n';
		$this->mMail->From = "orders@deepbluedive.com";
		$this->mMail->FromName = "Web Order";
		$this->mMail->Subject = "Website Order - " . $prefix . $order->GetOrderId ();
		$this->mMail->Host = "smtp.gmail.com";
		$this->mMail->Port = 465;
		$this->mMail->Mailer = "smtp";
		$this->mMail->SMTPAuth = true;
		$this->mMail->Username = "info@deepbluedive.com";
		$this->mMail->Password = "d33pblu3";
		$this->mMail->Body = $body;
		$this->mMail->SMTPSecure = "ssl"; // option
		$this->mMail->AltBody = $text_body;
		$this->mMail->AddAddress ( $address );
	}
	
	//! Sends the email
	function SendEmail($overrideLocalCheck = false) {
		if (! $this->mLocalMode || $overrideLocalCheck) {
			if (! $this->mMail->Send ()) {
				return false;
			} else {
				return true;
			}
		} else {
			return true;
		}
	}
	
	function GetAffiliatesOrders($affiliate) {
		$sql = 'SELECT Order_ID FROM tblOrder WHERE Affiliate_ID = ' . $affiliate->GetAffiliateId () . ' AND Status_ID = 3';
		$result = $this->mDatabase->query ( $sql );
		$retArr = array ();
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$retArr [] = new OrderModel ( $resultObj->Order_ID );
		}
		return $retArr;
	}
	
	//! Looks at a card number and returns the type of card it is
	/*! 
	 * @param $cardNumber : Str - A string that is the card number, either with or without spaces
	 * @return Str - Either VISA, MC, DELTA, SOLO, MAESTRO, UKE
	 */
	function CardType($cardNumber) {
		// Switch/Maestro start with these numbers
		$switchMaestro = array (4903, 4905, 4911, 4936, 6759, 6333, 564182, 633110, 5020, 5038, 6304, 6759, 6761 );
		$visa = array (4 );
		$visaElectron = array (4917, 4913, 4508, 4844, 417500 );
		$masterCard = array (51, 52, 53, 54, 55 );
		
		// Remove spaces from the card number
		$cardNumber = str_replace ( ' ', '', $cardNumber );
		
		// First Six Check
		$firstSixNumbers = substr ( $cardNumber, 0, 6 );
		switch ($firstSixNumbers) {
			case in_array ( $firstSixNumbers, $switchMaestro ) :
				return 'MAESTRO';
				break;
			case in_array ( $firstSixNumbers, $visaElectron ) :
				return 'UKE';
				break;
		}
		
		// First four numbers check
		$firstFourNumbers = substr ( $cardNumber, 0, 4 );
		switch ($firstFourNumbers) {
			case in_array ( $firstFourNumbers, $switchMaestro ) :
				return 'MAESTRO';
				break;
			case in_array ( $firstFourNumbers, $visaElectron ) :
				return 'UKE';
				break;
		}
		
		// First two Check
		$firstTwoNumbers = substr ( $cardNumber, 0, 2 );
		switch ($firstTwoNumbers) {
			case in_array ( $firstTwoNumbers, $masterCard ) :
				return 'MC';
				break;
		}
		
		// Visa check
		if (substr ( $cardNumber, 0, 1 ) == 4) {
			return 'VISA';
		}
		
		// No idea!
		return false;
	} // End CardType()
	

	//! Gets all orders placed within the past 24 hours
	function GetTodaysOrders() {
		$last24hours = time () - 86400;
		$sql = 'SELECT Order_ID FROM tblOrder WHERE Created_Date > ' . $last24hours . '';
		if ($result = $this->mDatabase->query ( $sql )) {
			$orders = $result->fetchAll ( PDO::FETCH_OBJ );
			// For each order, create a new instance of it and store it in the todaysOrders variable
			foreach ( $orders as $order ) {
				$newOrder = new OrderModel ( $order->Order_ID );
				$todaysOrders [] = $newOrder;
			}
			if (0 == count ( $orders )) {
				$todaysOrders = array ();
			}
			return $todaysOrders;
		} else {
			$error = new Error ( 'Could not get todays orders.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	} // End GetTodaysOrders
	

	//! Gets all orders placed within the past $n days in catalogue $catalogue
	function GetNDaysOrders($n, $catalogue) {
		$endRange = time () - ($n * 86400);
		$sql = 'SELECT DISTINCT Order_ID 
				FROM tblOrder 
				INNER JOIN tblBasket_Skus ON tblOrder.Basket_ID = tblBasket_Skus.Basket_ID
				INNER JOIN tblProduct_SKUs ON tblProduct_SKUs.SKU_ID = tblBasket_Skus.SKU_ID
				INNER JOIN tblCategory_Products ON tblProduct_SKUs.Product_ID = tblCategory_Products.Product_ID
				INNER JOIN tblCategory ON tblCategory_Products.Category_ID = tblCategory.Category_ID
				WHERE Created_Date > ' . $endRange . ' AND tblCategory.Catalogue_ID = ' . $catalogue->GetCatalogueId () . ' AND tblOrder.Status_ID IN (3,6,7,10)
				ORDER BY Order_ID DESC';
		if ($result = $this->mDatabase->query ( $sql )) {
			$orders = $result->fetchAll ( PDO::FETCH_OBJ );
			// For each order, create a new instance of it and store it in the todaysOrders variable
			foreach ( $orders as $order ) {
				$newOrder = new OrderModel ( $order->Order_ID );
				$nDaysOrders [] = $newOrder;
			}
			if (0 == count ( $orders )) {
				$nDaysOrders = array ();
			}
			return $nDaysOrders;
		} else {
			$error = new Error ( 'Could not get ' . $n . ' days orders.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	} // End GetTodaysOrders
	

	//! Gets all referrers placed within the past $n days in catalogue $catalogue
	function GetNDaysReferrers($n, $catalogue) {
		$endRange = time () - ($n * 86400);
		$sql = 'SELECT Referrer_ID, Order_ID
				FROM tblOrder 
				INNER JOIN tblBasket_Skus ON tblOrder.Basket_ID = tblBasket_Skus.Basket_ID
				INNER JOIN tblProduct_SKUs ON tblProduct_SKUs.SKU_ID = tblBasket_Skus.SKU_ID
				INNER JOIN tblCategory_Products ON tblProduct_SKUs.Product_ID = tblCategory_Products.Product_ID
				INNER JOIN tblCategory ON tblCategory_Products.Category_ID = tblCategory.Category_ID
				WHERE Created_Date > ' . $endRange . ' AND tblCategory.Catalogue_ID = ' . $catalogue->GetCatalogueId () . ' AND tblOrder.Status_ID IN (3,6,7,10)
				AND Referrer_ID IS NOT NULL
				GROUP BY Referrer_ID, Order_ID';
		if ($result = $this->mDatabase->query ( $sql )) {
			$referrerResult = $result->fetchAll ( PDO::FETCH_OBJ );
			$alreadyCounted ['refId'] = array ();
			foreach ( $referrerResult as $resultObj ) {
				if (! in_array ( $resultObj->Referrer_ID, $alreadyCounted ['refId'] )) {
					// Add to array
					$alreadyCounted ['refId'] [] = $resultObj->Referrer_ID;
					$nDaysReferrers [$resultObj->Referrer_ID] = 1;
				} else {
					// Increment array
					$nDaysReferrers [$resultObj->Referrer_ID] ++;
				}
			}
			if (0 == count ( $referrerResult )) {
				$nDaysReferrers = array ();
			}
			return $nDaysReferrers;
		} else {
			$error = new Error ( 'Could not get ' . $n . ' days referrers.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	//! Gets all orders placed within the 24 hour interval before $end
	function GetDaysOrders($end) {
		$start = $end - 86400;
		$sql = 'SELECT Order_ID FROM tblOrder WHERE Created_Date BETWEEN ' . $start . ' AND ' . $end . ' ORDER BY Order_ID DESC';
		if ($result = $this->mDatabase->query ( $sql )) {
			$orders = $result->fetchAll ( PDO::FETCH_OBJ );
			// For each order, create a new instance of it and store it in the daysOrders variable
			foreach ( $orders as $order ) {
				$newOrder = new OrderModel ( $order->Order_ID );
				$daysOrders [] = $newOrder;
			}
			if (0 == count ( $orders )) {
				$daysOrders = array ();
			}
			return $daysOrders;
		} else {
			$error = new Error ( 'Could not get yesterdays orders.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	} // End GetDaysOrders
	

	function SearchOnOrderId($searchFor) {
		$sql = 'SELECT Order_ID FROM tblOrder WHERE Order_ID LIKE \'' . $searchFor . '%\' ORDER BY tblOrder.Created_Date DESC';
		if ($result = $this->mDatabase->query ( $sql )) {
			$orders = $result->fetchAll ( PDO::FETCH_OBJ );
			// For each order, create a new instance of it and store it in the daysOrders variable
			foreach ( $orders as $order ) {
				$newOrder = new OrderModel ( $order->Order_ID );
				$searchOrders [] = $newOrder;
			}
			if (0 == count ( $orders )) {
				$searchOrders = array ();
			}
			return $searchOrders;
		} else {
			$error = new Error ( 'Could not get search orders.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	function SearchOnPostcode($searchFor, $address = 'shipping') {
		if ($address == 'shipping') {
			$field = 'Shipping_Address_ID';
		} else {
			$field = 'Billing_Address_ID';
		}
		$sql = 'SELECT tblAddress.Postcode, tblAddress.Address_ID, tblOrder.Order_ID
				FROM tblOrder 
					INNER JOIN tblAddress
						ON tblOrder.' . $field . ' = tblAddress.Address_ID
				WHERE tblAddress.Postcode LIKE \'' . $searchFor . '%\' ORDER BY tblOrder.Created_Date DESC';
		if ($result = $this->mDatabase->query ( $sql )) {
			$addresses = $result->fetchAll ( PDO::FETCH_OBJ );
			// For each order, create a new instance of it and store it in the daysOrders variable
			foreach ( $addresses as $address ) {
				$newAddress = new AddressModel ( $address->Address_ID );
				$newOrder = new OrderModel ( $address->Order_ID );
				$searchOrders ['address'] [] = $newAddress;
				$searchOrders ['order'] [] = $newOrder;
			}
			if (0 == count ( $addresses )) {
				$searchOrders = array ();
			}
			return $searchOrders;
		} else {
			$error = new Error ( 'Could not get search orders.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	} // End SearchOnPostcode
	

	function SearchOnLastName($searchFor) {
		$sql = 'SELECT tblCustomer.Customer_ID, tblCustomer.Last_Name, tblOrder.Order_ID
				FROM tblOrder 
					INNER JOIN tblCustomer
						ON tblOrder.Customer_ID = tblCustomer.Customer_ID
				WHERE tblCustomer.Last_Name LIKE \'' . $searchFor . '%\' ORDER BY tblOrder.Created_Date DESC';
		if ($result = $this->mDatabase->query ( $sql )) {
			$customers = $result->fetchAll ( PDO::FETCH_OBJ );
			// For each order, create a new instance of it and store it in the daysOrders variable
			foreach ( $customers as $customer ) {
				$newCustomer = new CustomerModel ( $customer->Customer_ID, 'id' );
				$newOrder = new OrderModel ( $customer->Order_ID );
				$searchOrders ['customer'] [] = $newCustomer;
				$searchOrders ['order'] [] = $newOrder;
			}
			if (0 == count ( $customers )) {
				$searchOrders = array ();
			}
			return $searchOrders;
		} else {
			$error = new Error ( 'Could not get search orders.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	} // End SearchOnLastName
	

	//! Used by the adminMissingHandler page - this gets those orders that are authorised. Rationale - orders that are authorised should be in the packing queue - those on onder should have had their dispatch date changed. This report should pick up orders that are neither (ie. have been missed/lost)
	/*!
	 * @param $excludeNonSameDayDispatch - Boolean - If true then only those orders with same day dispatch dates are returned
	 * @return Array of Obj:OrderModel
	 */
	function GetAuthorisedOrders($excludeNonSameDayDispatch = true) {
		$retArr = array ();
		if ($excludeNonSameDayDispatch) {
			$sql = 'SELECT Order_ID FROM tblOrder WHERE Status_ID = 10 AND Dispatch_Date_ID = 1';
		} else {
			$sql = 'SELECT Order_ID FROM tblOrder WHERE Status_ID = 10';
		}
		if ($result = $this->mDatabase->query ( $sql )) {
			while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
				$tempOrder = new OrderModel ( $resultObj->Order_ID );
				$retArr [] = $tempOrder;
			}
		} else {
			$error = new Error ( 'Could not run ' . $sql . '.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		return $retArr;
	}
	
	function GetAuthorisedOrdersWithProduct($product) {
		$retArr = array ();
		$sql = 'SELECT DISTINCT tblOrder.Order_ID FROM
				tblOrder INNER JOIN tblBasket_Skus ON tblOrder.Basket_ID = tblBasket_Skus.Basket_ID
				WHERE tblBasket_Skus.SKU_ID IN 
					(SELECT tblProduct_SKUs.SKU_ID FROM tblProduct_SKUs WHERE Product_ID = ' . $product->GetProductId () . ')
				AND tblOrder.Status_ID = 10
				';
		if ($result = $this->mDatabase->query ( $sql )) {
			while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
				$tempOrder = new OrderModel ( $resultObj->Order_ID );
				$retArr [] = $tempOrder;
			}
		} else {
			$error = new Error ( 'Could not run ' . $sql . '.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		return $retArr;
	}
	
	function GetNotDownloaded() {
		$retArr = array ();
		$sql = 'SELECT Order_ID FROM tblOrder WHERE Downloaded = \'0\' AND Status_ID = 3';
		if ($result = $this->mDatabase->query ( $sql )) {
			while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
				$tempOrder = new OrderModel ( $resultObj->Order_ID );
				if ($tempOrder->GetStatus ()->IsComplete ()) {
					$retArr [] = $tempOrder;
				}
			}
		} else {
			$error = new Error ( 'Could not run ' . $sql . '.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		return $retArr;
	} // End GetNotDownloaded


} // End OrderController


/* DEBUG 
try {
	$ord = new OrderController;
	$basket = new BasketModel(1);
	$ord->CreateOrder($basket);
	$orders = $ord->GetTodaysOrders();
	foreach($orders as $order) {
		echo 'ECHO'.$order->GetOrderId().'<br>';
	}
} catch (Exception $e) {
	echo $e->GetMessage();
}*/

?>