<?php
require_once ('../autoload.php');

//! Handles new customers registration and progression to the address entry stage of the checkout
class NewCustomerRegistrationHandler extends Handler {
	
	var $mClean;
	
	//! Initialise session and basket
	function __construct() {
		parent::__construct ();
		$this->mSessionHelper = new SessionHelper ( );
		$this->mBasket = $this->mSessionHelper->GetBasket ();
		$this->mCustomerController = new CustomerController ( );
	}
	
	//! Validates the user input and stores it in $mClean variable
	/*!
	 * @param $postArr - The $_POST array
	 */
	function Validate($postArr) {
		$this->mClean ['firstName'] = $this->mValidationHelper->MakeSafe ( $postArr ['firstName'] );
		$this->mClean ['lastName'] = $this->mValidationHelper->MakeSafe ( $postArr ['lastName'] );
		$this->mClean ['email'] = $this->mValidationHelper->MakeSafe ( $postArr ['email'] );
		$this->mClean ['telNo'] = $this->mValidationHelper->MakeSafe ( $postArr ['telNo'] );
		$this->mClean ['mobNo'] = $this->mValidationHelper->MakeSafe ( $postArr ['mobNo'] );
		$this->mClean ['title'] = $this->mValidationHelper->MakeSafe ( $postArr ['title'] );
		$this->mClean ['postageMethodId'] = $this->mValidationHelper->MakeSafe ( $postArr ['postageMethodId'] );
		$this->mClean ['password'] = $postArr ['password'];
		
		// Does the customer already exist?
		if ($this->mCustomerController->CustomerAlreadyExists ( $this->mClean ['email'] )) {
			$this->mClean ['customerExists'] = true;
		} else {
			$this->mClean ['customerExists'] = false;
		}
	}
	
	//! Creates a customer if the email address isn't already in use, or redirects them if it is
	function CreateCustomer() {
		// To create the order for the customer
		$orderController = new OrderController ( );
		
		// Only create the customer if they aren't alreday on the system
		if ($this->mClean ['customerExists']) {
			$this->mSessionHelper->SetCheckoutStage ( 'registrationFailure' );
			$this->mSessionHelper->SetRegistrationError ( 'this email address is already in use' );
		} else {
			// Create the customer
			$newCustomer = $this->mCustomerController->CreateCustomer ();
			
			// Where is it going?
			$deliveryCountry = new CountryModel ( $this->mSessionHelper->GetCountry () );
			
			// Fill in the customer details
			$newCustomer->SetFirstName($this->mClean['firstName']);
			$newCustomer->SetTitle($this->mClean['title']);
			$newCustomer->SetLastName($this->mClean['lastName']);
			$newCustomer->SetPassword($this->mClean['password']);
			$newCustomer->SetEmail($this->mClean['email']);
			$newCustomer->SetDaytimeTelephone($this->mClean['telNo']);
			$newCustomer->SetMobileTelephone($this->mClean['mobNo']);
			
			// How is it going?
			$postageMethod = new PostageMethodModel ( $this->mClean ['postageMethodId'] );
			
			// With whom?
			$courier = $postageMethod->GetCourier ();
			
			// Create their order
			$newOrder = $orderController->CreateOrder ( $this->mBasket );
			$newOrder->SetGoogleCheckout(false);
			
			// Fill in the order details
			$newOrder->SetCustomer ( $newCustomer );
			$newOrder->SetPostageMethod ( $postageMethod );
			$newOrder->SetCourier ( $courier );
			
			// Proceed to the next stage
			$this->mSessionHelper->SetCheckoutStage ( 'enterAddress' );
			$this->mSessionHelper->SetCustomer ( $newCustomer->GetCustomerId () );
			$this->mSessionHelper->SetOrder ( $newOrder->GetOrderId () );
		}
		// Redirect the user
		$sendTo = $this->mSecureBaseDir . '/checkout.php';
		header ( 'Location: ' . $sendTo );
	}
} // End NewCustomerRegistrationHandler


try {
	$handler = new NewCustomerRegistrationHandler ( );
	$handler->Validate ( $_POST );
	$handler->CreateCustomer ();
} catch ( Exception $e ) {
	echo $e->getMessage ();
}

?>