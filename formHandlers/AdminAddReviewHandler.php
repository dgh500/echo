<?php

include ('../autoload.php');
#include ('../phpMailer/class.phpmailer.php');
error_reporting ( E_ALL );

//! Validates, preps and sends add review requests
class AdminAddReviewHandler {

	//! Cleaned array of user input
	var $mClean;
	//! PHPMailer Mail class
	var $mMail;

	//! Initialises mail and validation (and session) classes
	function __construct() {
		$this->mValidationHelper = new ValidationHelper ( );
		$this->mSessionHelper = new SessionHelper ( );}

	//! Validates the user input
	function Validate($postArr) {
		$this->mClean ['reviewProduct'] = $this->mValidationHelper->MakeSafe($postArr['reviewProduct'] );
		$this->mClean ['reviewRating'] 	= $this->mValidationHelper->MakeSafe($postArr['reviewRating'] );
		$this->mClean ['reviewName'] 	= $this->mValidationHelper->MakeSafe($postArr['reviewName'] );
		$this->mClean ['reviewIP'] 		= $this->mValidationHelper->MakeSafe($postArr['reviewIP'] );
		$this->mClean ['reviewText'] 	= $this->mValidationHelper->MakeSafe($postArr['reviewText'] );
		return true;
	}

	//! Prepares the PHPMailer class
	function AddReview() {
		// Create Review
		$registry = Registry::getInstance();
		$reviewController = new ReviewController;
		$product = new ProductModel($this->mClean['reviewProduct']);
		$review = $reviewController->CreateReview($product,$this->mClean['reviewRating'],$this->mClean['reviewName'],$this->mClean['reviewText'],$this->mClean['reviewIP'],time());

	#	die('die');
		// Redirect to admin page
		echo '<div style="width: 150px; height: 20px; position: absolute; left: 250px; font-family: Arial; font-size: 10pt; font-weight: bold;">Review Added.</div>';
		echo '<script language="javascript" type="text/javascript">
					self.parent.location.href=\'' . $registry->viewDir . '/AdminProductView.php?id=' . $product->GetProductId() . '&tab=promotions\';
			</script>';
	}

	function ValidationFailure() {
		$registry = Registry::getInstance ();
		header ( 'Location:' . $registry->baseDir . '/priceMatch/failure/valid' );
	}

}

/////// Submit if needed
if(isset($_POST['reviewProduct'])) {
	$handler = new AdminAddReviewHandler ( );
	if ($handler->Validate ( $_POST )) {
		$handler->AddReview();
	} else {
		$handler->ValidationFailure ( 'Validation failure.' );
	}
}

//////// Display Add Review Form
echo '
<style type="text/css">
#addReviewForm {
	font-family: Arial, Sans-Serif;
	font-size: 10pt;
}
#addReviewForm label {
	width: 80px;
	font-weight: bold;
	text-align: left;
	margin-bottom: 10px;
	margin-right: 5px;
	height: 20px;
	line-height: 20px;
	display: block;
	float: left;
	clear: both;
}
#addReviewForm #reviewRating, #addReviewForm #reviewName, #addReviewForm #reviewIP {
	width: 190px;
	font-weight: bold;
	text-align: left;
	margin-bottom: 10px;
	margin-right: 5px;
	height: 20px;
	line-height: 20px;
	display: block;
	float: left;
}
#addReviewForm textarea {
	width: 190px;
	font-weight: bold;
	text-align: left;
	margin-bottom: 10px;
	margin-right: 5px;
	display: block;
	float: left;
}

</style>
<form name="addReviewForm" id="addReviewForm" action="AdminAddReviewHandler.php" method="post">
	<input type="hidden" name="reviewProduct" id="reviewProduct" value="'.$_GET['productId'].'" />
	<label for="reviewRating">Rating: </label><input type="text" name="reviewRating" id="reviewRating" /><br />
	<label for="reviewName"><b>Name:</b> </label><input type="text" name="reviewName" id="reviewName" /><br />
	<label for="reviewIP"><b>IP:</b> </label><input type="text" name="reviewIP" id="reviewIP" /><br />
	<label for="reviewText"><b>Text:</b> </label><textarea name="reviewText" id="reviewText" rows="10" cols="20"></textarea><br style="clear: both" />
	<input type="submit" value="Add Review" style="margin-left: 90px" />
</form>
';
?>