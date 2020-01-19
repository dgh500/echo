<?php

// Add, Remove, Play with reviews
class ReviewController {

	//! Initialise DB
	function __construct() {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
	}

	//! Creates a review for a product
	function CreateReview($product,$rating,$name,$text,$ipAddress,$date) {
		$productId = $product->GetProductId();
		$sql = "
				INSERT INTO `tblreview` (
				`Review_ID` ,
				`Product_ID` ,
				`Rating` ,
				`Name` ,
				`Review_Text` ,
				`IP_Address` ,
				`Date_Added` ,
				`Approved`
				)
				VALUES (
				NULL , '$productId', '$rating', '$name', '$text', '$ipAddress', '$date', '0'
				);
				";
		if($this->mDatabase->query($sql)) {
			return new ReviewModel($this->mDatabase->lastInsertId());
		} else {
			throw new Exception("Could not create review.".$sql);
		}
	} // End CreateReview

	//! Removes a review
	function DeleteReview($review) {

	} // End DeleteReview

	//! Counts the reviews for a product
	/*!
	 * @param $product ProductModel
	 * @return Int
	 */
	function CountReviewsForProduct($product) {
		$sql = 'SELECT COUNT(Review_ID) AS ReviewCount FROM tblreview WHERE Approved = \'1\' AND Product_ID = '.$product->GetProductId().' ';
		if(!$result = $this->mDatabase->query($sql)) {
			$error = new Error('Could not fetch review count.');
			$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
			throw new Exception($error->GetErrorMsg());
		}
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		return $resultObj->ReviewCount ;
	} // End CountReviewsForProduct

	//! Return the average review score for a product
	/*!
	 * @param $product ProductModel
	 * @return Int
	 */
	function CalculateAverageRatingForProduct($product) {

	} // End CalculateAverageRatingForProduct


} // End Review Controller

?>