<?php
require_once ('../autoload.php');

//! During checkout this sets the address for the order being processed
class CheckoutAddressHandler extends Handler {

	//! Clean version of the $_POST array
	var $mClean;

	//! Constructor
	function __construct() {
		parent::__construct();
		// Keep the session rolling
		$this->mSessionHelper = new SessionHelper();
		// Get the basket
		$this->mBasket = $this->mSessionHelper->GetBasket();
		// Initialise Debugging
		/*if ($this->mRegistry->debugMode) {
			$this->DebugInit();
			$this->DebugStartLog();
		}*/
	} // End __construct()

	//! Opens the file for debugging
	function DebugInit() {
		$this->mDebugFileHandle = fopen('CheckoutAddressHandlerLog.txt','a+');
	}

	//! Set up debug info (in /debugCheckoutLog.txt)
	function DebugStartLog() {
		if ($this->mRegistry->debugMode) {
			fwrite($this->mDebugFileHandle, "--------------------------------------------------------------\r\nCheckout Address Handler Log Started (".date('r',time()).")");
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

	function Validate($postArr) {
		$this->mClean ['company'] = $this->mValidationHelper->MakeSafe ( $postArr ['company'] );
		$this->mClean ['address1'] = $this->mValidationHelper->MakeSafe ( $postArr ['address1'] );
		$this->mClean ['address2'] = $this->mValidationHelper->MakeSafe ( $postArr ['address2'] );
		$this->mClean ['address3'] = $this->mValidationHelper->MakeSafe ( $postArr ['address3'] );
		$this->mClean ['county'] = $this->mValidationHelper->MakeSafe ( $postArr ['county'] );
		$this->mClean ['postCode'] = $this->mValidationHelper->MakeSafe ( $postArr ['postCode'] );
		$this->mClean ['country'] = new CountryModel ( $this->mValidationHelper->MakeSafe ( $postArr ['country'] ) );
		$this->mClean ['bAddress1'] = $this->mValidationHelper->MakeSafe ( $postArr ['bAddress1'] );
		$this->mClean ['bAddress2'] = $this->mValidationHelper->MakeSafe ( $postArr ['bAddress2'] );
		$this->mClean ['bAddress3'] = $this->mValidationHelper->MakeSafe ( $postArr ['bAddress3'] );
		$this->mClean ['bPostCode'] = $this->mValidationHelper->MakeSafe ( $postArr ['bPostCode'] );
		$postArr ['catalogueReq'] = true;
		($postArr ['catalogueReq'] ? $this->mClean ['catalogueReq'] = true : $this->mClean ['catalogueReq'] = false);
		$this->mClean ['notes'] = $this->mValidationHelper->MakeSafe ( $postArr ['notes'] );
		$this->mClean ['referrer'] = new ReferrerModel ( $this->mValidationHelper->MakeSafe ( $postArr ['referrer'] ) );
	}

	//! Sets the addresses for the order
	function Process() {
		$order = new OrderModel($this->mSessionHelper->GetOrder());
		$addressController = new AddressController();
		// Make Shipping Address
		$shippingAddress = $addressController->CreateAddress ();
		$shippingAddress->SetCompany ( $this->mClean ['company'] );
		$shippingAddress->SetLine1 ( $this->mClean ['address1'] );
		$shippingAddress->SetLine2 ( $this->mClean ['address2'] );
		$shippingAddress->SetLine3 ( $this->mClean ['address3'] );
		$shippingAddress->SetCounty ( $this->mClean ['county'] );
		$shippingAddress->SetPostcode ( $this->mClean ['postCode'] );
		$shippingAddress->SetCountry ( $this->mClean ['country'] );

		// Make Billing Address
		$billingAddress = $addressController->CreateAddress ();
		$billingAddress->SetLine1 ( $this->mClean ['bAddress1'] );
		$billingAddress->SetLine2 ( $this->mClean ['bAddress2'] );
		$billingAddress->SetLine3 ( $this->mClean ['bAddress3'] );
		$billingAddress->SetPostcode ( $this->mClean ['bPostCode'] );

		// Update order for addresses
		$order->SetBillingAddress($billingAddress);
		$order->SetShippingAddress($shippingAddress);
		$order->SetNotes($this->mClean['notes']);
		$order->SetReferrer($this->mClean['referrer']);
		if($this->mClean['catalogueReq']) {
			$order->SetBrochure(1);
		}

		// Update checkout stage
		$this->mSessionHelper->SetCheckoutStage('billingDetails');

		// Redirect the user
		$secureBaseDir = $this->mRegistry->secureBaseDir;
		$sendTo = $secureBaseDir.'/checkout.php';
		header('Location: '.$sendTo);
	}

}

try {
	$handler = new CheckoutAddressHandler();
	$handler->Validate($_POST);
	$handler->Process();
} catch (Exception $e) {
	echo $e->getMessage();
}

?>