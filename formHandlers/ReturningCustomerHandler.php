<?php
require_once ('../autoload.php');

//! Handles returning customers when they reach the checkout stage. Creates an order for them if they successfully log in
class ReturningCustomerHandler extends Handler {

	//! Array : Validated user input
	var $mClean;
	//! Obj : BasketModel - The user's basket
	var $mBasket;
	//! Obj : SessionHelper - Used to preserve state
	var $mSessionHelper;

	//! Constructor, initialises the basket and loads parent controller
	function __construct() {
		parent::__construct ();
		$this->mSessionHelper = new SessionHelper ( );
		$this->mBasket = $this->mSessionHelper->GetBasket();
		// Initialise Debugging
		/*if ($this->mRegistry->debugMode) {
			$this->DebugInit();
			$this->DebugStartLog();
		}*/
	}

	//! Opens the file for debugging
	function DebugInit() {
		$this->mDebugFileHandle = fopen('../'.$this->mRegistry->debugDir.'/ReturningCustomerHandlerLog.txt','a+');
	}

	//! Set up debug info
	function DebugStartLog() {
		if ($this->mRegistry->debugMode) {
			fwrite($this->mDebugFileHandle, "--------------------------------------------------------------\r\nReturning Customer Handler Log Started (".date('r',time()).")");
			$browser = get_browser();
			$ip = getenv("REMOTE_ADDR");
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


	//! Cleans up the user input
	/*!
	 * @param - $postArr - The $_POST array
	 */
	function Validate($postArr) {
		$this->mClean ['loginEmail'] = $this->mValidationHelper->MakeSafe ( $postArr ['loginEmail'] );
		$this->mClean ['postageMethodId'] = $this->mValidationHelper->MakeSafe ( $postArr ['postageMethodId'] );
		$this->mClean ['loginPassword'] = $postArr ['loginPassword'];
	}

	//! Logs the user in if details are correct, and redirects them to the address input page if successful - goes to failure page otherwise
	function Login() {

		// To log them in
		$customerController = new CustomerController ( );
		// To create the order
		$orderController = new OrderController ( );
		try {
			// Try to log in
			$customer = new CustomerModel($this->mClean['loginEmail']);
			if ($customerController->Login($customer,$this->mClean['loginPassword'])) {
				// Successfully Logged In
				// Create an order ID for them
				$newOrder = $orderController->CreateOrder($this->mBasket);
				$newOrder->SetGoogleCheckout(false);

				// Store the customer and order IDs in the session
				$this->mSessionHelper->SetCustomer($customer->GetCustomerId());
				$this->mSessionHelper->SetOrder($newOrder->GetOrderId());

				// Where is it going?
				if($this->mSessionHelper->GetCountry ()) {
					$deliveryCountry = new CountryModel ( $this->mSessionHelper->GetCountry () );
				} else {
					$countryController = new CountryController;
					$deliveryCountry = $countryController->GetDefault();
				}

				// How is it being sent?
				if($this->mClean ['postageMethodId']) {
					$postageMethod = new PostageMethodModel ( $this->mClean ['postageMethodId'] );
		#			var_dump($this->mClean ['postageMethodId']);die();
				} else {
		#			die('choosing default..');
					$postageMethodController = new PostageMethodController;
					$postageMethod = $postageMethodController->GetDefault();
				}

				// Who is it going with?
				$courier = $postageMethod->GetCourier ();

				// Assign postage and customer details
				$newOrder->SetPostageMethod ( $postageMethod );
				$newOrder->SetCustomer ( $customer );
				$newOrder->SetCourier ( $courier );

				// Proceed the checkout to the next stage
				$this->mSessionHelper->SetCheckoutStage('enterAddress');
			} else {
				// Debug
			#	($this->mRegistry->debugMode ? fwrite($this->mDebugFileHandle," -> Login Failure - Wrong Password\r\n") : NULL);
				// Failure
				$this->mSessionHelper->SetCheckoutStage ( 'loginFailure' );
			}
		} catch ( Exception $e ) {
			// Debug
			#($this->mRegistry->debugMode ? fwrite($this->mDebugFileHandle," -> Login Failure (EXCEPTION)\r\n -> Redirecting to login failure stage \r\n") : NULL);
			// Set checkout stage
			#die($e->GetMessage());
			$this->mSessionHelper->SetCheckoutStage('loginFailure');
		}

		// Redirect the user back to the checkout with the appropriate stage set above
		$sendTo = $this->mSecureBaseDir . '/checkout.php';
		header ( 'Location: ' . $sendTo );
	}
} // End ReturningCustomerHandler


try {
	$handler = new ReturningCustomerHandler ( );
	$handler->Validate ( $_POST );
	$handler->Login ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>