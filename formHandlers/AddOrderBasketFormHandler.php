<?php
require_once ('../autoload.php');

class AddOrderBasketFormHandler extends Handler {
	
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
		$this->mClean['currentPostage'] 		= $this->mValidationHelper->MakeSafe($postArr['currentPostage']);		
		$this->mClean['currentPostageMethod'] 	= $this->mValidationHelper->MakeSafe($postArr['postageMethodDropDownMenu']);		
		
	} // End Validate()
	
	//! Update the postage to reflect the changes requested on the form
	/*!
	 * @return Void
	 */
	function Process() {
		// Customer Details
		$_SESSION['currentPostage'] 		= $this->mClean['currentPostage'];
		$_SESSION['currentPostageMethod'] 	= $this->mClean['currentPostageMethod'];
	} // End Process
}

try {
	$handler = new AddOrderBasketFormHandler;
	$handler->Validate($_POST);
	$handler->Process();
} catch(Exception $e) {
	echo $e->getMessage();
}

?>