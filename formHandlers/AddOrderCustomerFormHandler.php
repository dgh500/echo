<?php
require_once ('../autoload.php');

class AddOrderCustomerFormHandler extends Handler {
	
	//! Clean array of validated input
	var $mClean;
	
	//! Initialises the validation helper
	function __construct() {
		parent::__construct();
	}
	
	//! Makes all of the values safe using ValidationHelper -> MakeSafe()
	/*!
	 * @param $postArr - The _POST array
	 * @return Void
	 */
	function Validate($postArr) {
		$this->mClean['referrerTab'] = $this->mValidationHelper->MakeSafe($postArr['referrerTab']);
		
		// Models
		$this->mClean['customer'] = new CustomerModel($this->mValidationHelper->MakeSafe($postArr['customerId']),'id');
		$this->mClean['delivery'] = new AddressModel($this->mValidationHelper->MakeSafe($postArr['deliveryId']));
		$this->mClean['billing']  = new AddressModel($this->mValidationHelper->MakeSafe($postArr['billingId']));

		// Load customer details
		$this->mClean['title']	 			= $this->mValidationHelper->MakeMysqlSafe($postArr['title']);
		$this->mClean['firstName'] 			= $this->mValidationHelper->MakeMysqlSafe($postArr['firstName']);
		$this->mClean['lastName'] 			= $this->mValidationHelper->MakeMysqlSafe($postArr['lastName']);
		$this->mClean['email'] 				= $this->mValidationHelper->MakeMysqlSafe($postArr['email']);
		$this->mClean['telNo'] 				= $this->mValidationHelper->MakeMysqlSafe($postArr['telNo']);
		$this->mClean['mobNo'] 				= $this->mValidationHelper->MakeMysqlSafe($postArr['mobNo']);
		
		// Load delivery address
		$this->mClean['company'] 			= $this->mValidationHelper->MakeMysqlSafe($postArr['company']);		
		$this->mClean['delivery1'] 			= $this->mValidationHelper->MakeMysqlSafe($postArr['delivery1']);
		$this->mClean['delivery2'] 			= $this->mValidationHelper->MakeMysqlSafe($postArr['delivery2']);
		$this->mClean['delivery3'] 			= $this->mValidationHelper->MakeMysqlSafe($postArr['delivery3']);
		$this->mClean['county'] 			= $this->mValidationHelper->MakeMysqlSafe($postArr['county']);
		$this->mClean['deliveryPostcode'] 	= $this->mValidationHelper->MakeMysqlSafe($postArr['deliveryPostcode']);
		
		// Load billing address
		$this->mClean['billing1'] 			= $this->mValidationHelper->MakeMysqlSafe($postArr['billing1']);
		$this->mClean['billingPostcode'] 	= $this->mValidationHelper->MakeMysqlSafe($postArr['billingPostcode']);
		
		// Misc Details
		$this->mClean['referrerId'] 	= $this->mValidationHelper->MakeMysqlSafe($postArr['referrerId']);
		$this->mClean['notes'] 			= $this->mValidationHelper->MakeMysqlSafe($postArr['notes']);		
		$this->mClean['staffName'] 		= $this->mValidationHelper->MakeMysqlSafe($postArr['staffName']);
		if(isset($postArr['catalogueWanted'])) {
			$this->mClean['catalogueWanted']= 1;		
		} else {
			$this->mClean['catalogueWanted']= 0;
		}
	} // End Validate()
	
	//! Update the customer to reflect the changes requested on the form
	/*!
	 * @return Void
	 */
	function Process() {
		// Customer Details
		$this->mClean['customer']->SetTitle($this->mClean['title']);
		$this->mClean['customer']->SetFirstName($this->mClean['firstName']);
		$this->mClean['customer']->SetLastName($this->mClean['lastName']);
		$this->mClean['customer']->SetEmail($this->mClean['email']);
		$this->mClean['customer']->SetDaytimeTelephone($this->mClean['telNo']);
		$this->mClean['customer']->SetMobileTelephone($this->mClean['mobNo']);
		
		// Delivery Details
		$this->mClean['delivery']->SetCompany($this->mClean['company']);
		$this->mClean['delivery']->SetLine1($this->mClean['delivery1']);
		$this->mClean['delivery']->SetLine2($this->mClean['delivery2']);
		$this->mClean['delivery']->SetLine3($this->mClean['delivery3']);
		$this->mClean['delivery']->SetCounty($this->mClean['county']);
		$this->mClean['delivery']->SetPostcode($this->mClean['deliveryPostcode']);
		
		// Billing Details
		$this->mClean['billing']->SetLine1($this->mClean['billing1']);
		$this->mClean['billing']->SetPostcode($this->mClean['billingPostcode']);
		
		// Misc Details
		$_SESSION['referrerId']  	 = $this->mClean['referrerId'];
		$_SESSION['notes'] 			 = $this->mClean['notes'];
		$_SESSION['staffName'] 		 = $this->mClean['staffName'];
		$_SESSION['catalogueWanted'] = $this->mClean['catalogueWanted'];
		
	} // End Process
}

try {
	$handler = new AddOrderCustomerFormHandler;
	$handler->Validate($_POST);
	$handler->Process();
} catch(Exception $e) {
	echo $e->getMessage();
}

?>