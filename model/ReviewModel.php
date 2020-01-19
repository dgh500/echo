<?php

//! Models a review for a given product
class ReviewModel {

	//! Int - The unique Review ID
	var $mReviewId;
	//! ProductModel - The product that this review is for
	var $mProduct;
	//! Int - The rating for this review
	var $mRating;
	//! String - The reviewer's name
	var $mName;
	//! String - The actual review text
	var $mText;
	//! Varchar - The IP Address of the reviewer
	var $mIPAddress;
	//! Int - The date the review was added (UNIX Timestamp)
	var $mDateAdded;
	//! Bool - Whether the review is approved or not
	var $mApproved;

	//! Initialises the review
	function __construct($reviewId) {
		$this->mRegistry = Registry::getInstance();
		$this->mDatabase = $this->mRegistry->database;
		$sql = 'SELECT COUNT(Review_ID) AS ReviewCount FROM tblreview WHERE Review_ID = ' . $reviewId;
		$result = $this->mDatabase->query($sql);
		if ($result) {
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			if ($resultObj->ReviewCount > 0) {
				$this->mReviewId = $reviewId;
			} else {
				$error = new Error('Could not initialise review '.$reviewId.' because it does not exist in the database.');
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		} else {
			$error = new Error ( 'Could not initialise review ' . $reviewId . ' because it does not exist in the database.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	} // End __construct()

	//! Return the reviewer's name if a string is needed
	function __toString() {
		return $this->GetName();
	}

	//! Gets the Review ID
	function GetReviewId() {
		return $this->mReviewId;
	} // End GetReviewId

	//! Returns the Product this review is for
	/*!
	* @return ProduceModel
	*/
	function GetProduct() {
		if (! isset ( $this->mProduct )) {
			$sql = 'SELECT Product_ID FROM tblreview WHERE Review_ID = ' . $this->mReviewId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the product for review ' . $this->mReviewId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mProduct = new ProductModel($resultObj->Product_ID);
		}
		return $this->mProduct;
	} // End GetProduct

	//! Sets the product of the review
	/*!
	* @param [in] newProduct ProductModel : The new product
	* @return Bool : true if successful
	*/
	function SetProduct($newProduct) {
		$sql = 'UPDATE tblreview SET Product_ID = \'' . $newProduct->GetProductId() . '\' WHERE Review_ID = ' . $this->mReviewId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the product for review ' . $this->mReviewId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mProduct = $newProduct;
		return true;
	} // End SetProduct

	//! Returns the Rating
	/*!
	* @return Int - Between 1 - 5
	*/
	function GetRating() {
		if (! isset ( $this->mRating )) {
			$sql = 'SELECT Rating FROM tblreview WHERE Review_ID = ' . $this->mReviewId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the rating for review ' . $this->mReviewId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mRating = $resultObj->Rating;
		}
		return $this->mRating;
	} // End GetRating

	//! Sets the rating of the review
	/*!
	* @param [in] newRating Int : The new rating (between 1 and 5)
	* @return Bool : true if successful
	*/
	function SetRating($newRating) {
		$sql = 'UPDATE tblreview SET Rating = \'' . $newRating . '\' WHERE Review_ID = ' . $this->mReviewId;
		if (is_numeric ( $newRating )) {
			if (! $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not update the rating for review ' . $this->mReviewId );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		$this->mRating = $newRating;
		return true;
	} // End SetRating

	//! Returns the Reviewer's name
	/*!
	* @return Str
	*/
	function GetName() {
		if (! isset ( $this->mName )) {
			$sql = 'SELECT Name FROM tblreview WHERE Review_ID = ' . $this->mReviewId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the name for review ' . $this->mReviewId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mName = $resultObj->Name;
		}
		return $this->mName;
	} // End GetName

	//! Sets the reviewer's name of the review
	/*!
	* @param [in] newName Str : The new name
	* @return Bool : true if successful
	*/
	function SetName($newName) {
		$sql = 'UPDATE tblreview SET Name = \'' . $newName . '\' WHERE Review_ID = ' . $this->mReviewId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the name for review ' . $this->mReviewId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mName = $newName;
		return true;
	} // End SetName

	//! Returns the Review text
	/*!
	* @return Str
	*/
	function GetText() {
		if (! isset ( $this->mText )) {
			$sql = 'SELECT Review_Text FROM tblreview WHERE Review_ID = ' . $this->mReviewId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the text for review ' . $this->mReviewId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mText = $resultObj->Review_Text;
		}
		return nl2br($this->mText);
	} // End GetText

	//! Sets the text of the review
	/*!
	* @param [in] newText Str : The new text
	* @return Bool : true if successful
	*/
	function SetText($newText) {
		$sql = 'UPDATE tblreview SET Review_Text = \'' . $newText . '\' WHERE Review_ID = ' . $this->mReviewId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the text for review ' . $this->mReviewId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mText = $newText;
		return true;
	} // End SetName

	//! Returns the Reviewer's IP Address
	/*!
	* @return Str
	*/
	function GetIPAddress() {
		if (! isset ( $this->mIPAddress )) {
			$sql = 'SELECT IP_Address FROM tblreview WHERE Review_ID = ' . $this->mReviewId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the IP Address for review ' . $this->mReviewId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mIPAddress = $resultObj->IP_Address;
		}
		return $this->mIPAddress;
	} // End GetIPAddress

	//! Sets the reviewer's IP Address
	/*!
	* @param [in] newIPAddress Str : The new IP Address
	* @return Bool : true if successful
	*/
	function SetIPAddress($newIPAddress) {
		$sql = 'UPDATE tblreview SET IP_Address = \'' . $newIPAddress . '\' WHERE Review_ID = ' . $this->mReviewId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the IP Address for review ' . $this->mReviewId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mIPAddress = $newIPAddress;
		return true;
	} // End SetName

	//! Returns the Date the Reivew was added as a UNIX timestamp
	/*!
	* @param $humanReadable - Whether to return this in a human readable format
	* @return Int
	*/
	function GetDateAdded($humanReadable=false) {
		if (! isset ( $this->mDateAdded )) {
			$sql = 'SELECT Date_Added FROM tblreview WHERE Review_ID = ' . $this->mReviewId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the Date Added for review ' . $this->mReviewId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mDateAdded = $resultObj->Date_Added;
		}
		if($humanReadable) {
			return date('D jS M',$this->mDateAdded);
		} else {
			return $this->mDateAdded;
		}
	} // End GetDateAdded

	//! Sets the date the review was added
	/*!
	* @param [in] newDateAdded Int : The new Date Added
	* @return Bool : true if successful
	*/
	function SetDateAdded($newDateAdded) {
		$sql = 'UPDATE tblreview SET Date_Added = \'' . $newDateAdded . '\' WHERE Review_ID = ' . $this->mReviewId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the Date Added for review ' . $this->mReviewId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDateAdded = $newDateAdded;
		return true;
	} // End SetDateAdded

	//! Returns the Approval status of the review
	/*!
	* @return Bool
	*/
	function GetApproved() {
		if (! isset ( $this->mApproved )) {
			$sql = 'SELECT Approved FROM tblreview WHERE Review_ID = ' . $this->mReviewId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the Approval Status for review ' . $this->mReviewId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mApproved = $resultObj->Approved;
		}
		return $this->mApproved;
	} // End GetApproved

	//! Sets the approval status of the review
	/*!
	* @param [in] newApprovalStatus Int : The new Approval Status
	* @return Bool : true if successful
	*/
	function SetApproved($newApproval) {
		$sql = 'UPDATE tblreview SET Approved = \'' . $newApproval . '\' WHERE Review_ID = ' . $this->mReviewId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the approval status for review ' . $this->mReviewId );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mApproved = $newApproval;
		return true;
	} // End SetApproved

} // End ReviewModel

?>