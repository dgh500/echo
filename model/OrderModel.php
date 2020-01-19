<?php

//! Class that defines the data model for an order
class OrderModel {

	//! Int : Unique order ID
	var $mOrderId;
	//! Int : Unix timestamp
	var $mCreatedDate;
	//! Obj:BasketModel : The basket which contains the products that this order is for
	var $mBasket;
	//! Obj:OrderStatusModel : The current order status (Eg. In Transit)
	var $mStatus;
	//! String : Any notes associated with the order
	var $mNotes;
	//! String : Any staff notes associated with the order
	var $mStaffNotes;
	//! Obj:CurrencyModel XXX
	var $mCurrency;
	//! Decimal : The total price of the order
	var $mTotalPrice;
	//! Decimal : The total tax on this order
	var $mTotalTax;
	//! Decimal : The total postage to be paid on this order
	var $mTotalPostage;
	//! Decimal : The total amount of the postage that is tax
	var $mTotalPostageTax;
	//! Obj:CourierModel : The courier this order was shipped with
	var $mCourier;
	//! Int : Unix Timestamp
	var $mShippedDate;
	//! String : Tracking number for whatever courier was used
	var $mTrackingNumber;
	//! Obj:CustomerModel : The customer that placed the order
	var $mCustomer;
	//! String : A transaction ID from HSBC
	var $mTransactionId;
	//! Int : Unix Timestamp
	var $mTransactionDate;
	//! Obj:AffiliateModel : The affiliate that the order was placed through
	var $mAffiliate;
	//! Obj:PostageMethodModel : The way the order was sent (Eg. Special Del.)
	var $mPostageMethod;
	//! Int : The weight (in grams) of the order
	var $mWeight;
	//! Bool : Whether the order has been downloaded (By Sync software)
	var $mDownloaded;
	//! Int : Unix Timestamp
	var $mDownloadedDate;
	//! Obj:AddressModel : Where the card is registered
	var $mBillingAddress;
	//! Obj:AddressModel : Where the order will be dispatched to
	var $mShippingAddress;
	//! Obj:DispatchDateModel : When the order will be dispatched
	var $mDispatchDate;
	//! Obj:PDO : Database used to access the underlying SQL
	var $mDatabase;
	//! Boolean : Whether to send a brochure
	var $mBrochure;
	//! Decimal : The total actually taken by the shop - minus non-shipped items etc.
	var $mTotalActuallyTaken;
	//! String - Who took the order
	var $mStaffName;
	//! Obj : ReferrerModel : The medium through which the order came about
	var $mReferrer;
	//! Str - The security code returned by Protx
	var $mSecurityKey;
	//! Int - The authorisation identifier, just an auto-inc beacuse Protx wants it, OrderController->ConstructShipRequest() uses GetNextAuthRef() to generate the next one
	var $mAuthRef;
	//! The 3D-Secure Status (where applicable, ie. customer orders not staff) result
	var $m3DSecureStatus;
	//! A unique value which indicated that a 3D-Secure authentication was successful
	var $mCavv;
	//! Boolean - Whether or not the order has been processed through Google Checkout
	var $mGoogleCheckout;

	//! Constructor, initialises the Order ID - throws an exception if the product does not exist
	/*!
	 * @param [in] $orderId : Int - The order to be initiated
	 * @param [in] $googleOrder - Bool - Whether to treat the order id as a google order number
	 * @return Bool : True if successful, exception if not
	 */
	function __construct($orderId,$googleOrder=false) {
		// Registry
		$this->mRegistry = Registry::getInstance ();
		// Database
		$this->mDatabase = $this->mRegistry->database;
		// Check the order exists
		if($googleOrder) {
			$check_sql = 'SELECT COUNT(*) Order_ID FROM tblOrder WHERE Transaction_ID = \''.$orderId.'\'';
		} else {
			$check_sql = 'SELECT COUNT(*) Order_ID FROM tblOrder WHERE Order_ID = ' . $orderId;
		}
		if ($result = $this->mDatabase->query ( $check_sql )) {
			if ($result->fetchColumn () > 0) {
				if($googleOrder) {
					$this->mOrderId = $this->GetOrderIdFromGoogleId($orderId);
				} else {
					$this->mOrderId = $orderId;
				}
				return true;
			} else {
				$error = new Error ( 'The order ID supplied is not in the database.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		} else {
			$error = new Error ( 'Could not run query: ' . $check_sql . ' and get a result.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	} // End __construct()

	function GetOrderIdFromGoogleId($googleId) {
		$sql = 'SELECT Order_ID FROM tblOrder WHERE Transaction_ID = \''.$googleId.'\'';
		if ($result = $this->mDatabase->query ( $sql )) {
			$resultObj = $result->fetchObject ();
			$this->mOrderId = $resultObj->Order_ID;
		} else {
			$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		return $this->mOrderId;
	}

	//! Gets the date that the order was created, as a UNIX timestamp. Use TimeHelper class to manipulate this
	/*!
	 * @return Int : Unix Timestamp
	 */
	function GetCreatedDate() {
		if (! isset ( $this->mCreatedDate )) {
			$sql = 'SELECT Created_Date FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mCreatedDate = $resultObj->Created_Date;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mCreatedDate;
	}

	//! Set the date that the order was created, generally this should only be used by OrderController::CreateOrder
	/*!
	 * @param [in] newCreatedDate : Int - The new created date, as a UNIX timestamp
	 * @return true if successful, exception thrown if not
	 */
	function SetCreatedDate($newCreatedDate) {
		$sql = 'UPDATE tblOrder SET Created_Date = ' . $newCreatedDate . ' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the created date for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mCreatedDate = $newCreatedDate;
		return true;
	}

	//! Gets the date that the order was shipped, as a UNIX timestamp. Use TimeHelper class to manipulate this
	/*!
	 * @return Int : Unix Timestamp
	 */
	function GetShippedDate() {
		if (! isset ( $this->mShippedDate )) {
			$sql = 'SELECT Shipped_Date FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mShippedDate = $resultObj->Shipped_Date;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mShippedDate;
	}

	//! Set the date that the order was shipped
	/*!
	 * @param [in] newShippedDate : Int - The new shipped date, as a UNIX timestamp
	 * @return true if successful, exception thrown if not
	 */
	function SetShippedDate($newShippedDate) {
		$sql = 'UPDATE tblOrder SET Shipped_Date = ' . $newShippedDate . ' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the shipped date for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mShippedDate = $newShippedDate;
		return true;
	}

	//! Gets the date that the transaction completed, as a UNIX timestamp. Use TimeHelper class to manipulate this
	/*!
	 * @return Int : Unix Timestamp
	 */
	function GetTransactionDate() {
		if (! isset ( $this->mTransactionDate )) {
			$sql = 'SELECT Transaction_Date FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mTransactionDate = $resultObj->Transaction_Date;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mTransactionDate;
	}

	//! Set the date that the transaction completed
	/*!
	 * @param [in] newTransactionDate : Int - The new transaction date, as a UNIX timestamp
	 * @return true if successful, exception thrown if not
	 */
	function SetTransactionDate($newTransactionDate) {
		$sql = 'UPDATE tblOrder SET Transaction_Date = ' . $newTransactionDate . ' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the transaction date for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mTransactionDate = $newTransactionDate;
		return true;
	}

	//! Gets the date that the order was downloaded, as a UNIX timestamp. Use TimeHelper class to manipulate this
	/*!
	 * @return Int : Unix Timestamp
	 */
	function GetDownloadDate() {
		if (! isset ( $this->mDownloadDate )) {
			$sql = 'SELECT Download_Date FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mDownloadDate = $resultObj->Download_Date;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mDownloadDate;
	}

	//! Set the date that the order was downloaded
	/*!
	 * @param [in] newDownloadDate : Int - The new download date, as a UNIX timestamp
	 * @return true if successful, exception thrown if not
	 */
	function SetDownloadDate($mDownloadDate) {
		$sql = 'UPDATE tblOrder SET Download_Date = ' . $mDownloadDate . ' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the download date for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDownloadDate = $mDownloadDate;
		return true;
	}

	//! Get the dispatch date estimate for this order
	/*!
	 * @return Obj:DispatchDateModel
	 */
	function GetDispatchDate() {
		if (! isset ( $this->mDispatchDate )) {
			$sql = 'SELECT Dispatch_Date_ID FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mDispatchDate = new DispatchDateModel ( $resultObj->Dispatch_Date_ID );
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mDispatchDate;
	}

	//! Set the dispatch date of this order
	/*!
	 * @param [in] newDispatchDate : Obj:DispatchDateModel - the dispatch date of the order
	 */
	function SetDispatchDate($newDispatchDate) {
		$sql = 'UPDATE tblOrder SET Dispatch_Date_ID = ' . $newDispatchDate->GetDispatchDateId () . ' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the dispatch date for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDispatchDate = $newDispatchDate;
		return true;
	}

	//! Gets the notes section of this order
	/*!
	 * @return String
	 */
	function GetNotes() {
		if (! isset ( $this->mNotes )) {
			$sql = 'SELECT Notes FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mNotes = $resultObj->Notes;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mNotes;
	}

	//! Set the notes section of this order
	/*!
	 * @param [in] newNotes : String - the new notes
	 * @return Boolean : true if successful
	 */
	function SetNotes($newNotes) {
		$sql = 'UPDATE tblOrder SET Notes = \'' . $newNotes . '\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the notes section for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mNotes = $newNotes;
		return true;
	}

	//! Set the referrer section of this order
	/*!
	 * @param [in] newReferrer : String - the new referrer
	 * @return Boolean : true if successful
	 */
	function SetReferrer($newReferrer) {
		$sql = 'UPDATE tblOrder SET Referrer_Id = \'' . $newReferrer->GetReferrerId () . '\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the referrer section for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mReferrer = $newReferrer;
		return true;
	}

	//! Gets the referrer for this order
	/*!
	 * @return String
	 */
	function GetReferrer() {
		if (! isset ( $this->mReferrer )) {
			$sql = 'SELECT Referrer_ID FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mReferrer = new ReferrerModel($resultObj->Referrer_ID);
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mReferrer;
	}

	//! Gets the staff notes section of this order
	/*!
	 * @return String
	 */
	function GetStaffNotes() {
		if (! isset ( $this->mStaffNotes )) {
			$sql = 'SELECT Staff_Notes FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mStaffNotes = $resultObj->Staff_Notes;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mStaffNotes;
	}

	//! Set the staff notes section of this order
	/*!
	 * @param [in] newStaffNotes : String - the new staff notes
	 * @return Boolean : true if successful
	 */
	function SetStaffNotes($newStaffNotes) {
		$sql = 'UPDATE tblOrder SET Staff_Notes = \'' . $newStaffNotes . '\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the staff notes section for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mStaffNotes = $newStaffNotes;
		return true;
	}

	//! Gets the transaction id of the order
	/*!
	 * @return String
	 */
	function GetTransactionId() {
		if (! isset ( $this->mTransactionId )) {
			$sql = 'SELECT Transaction_ID FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mTransactionId = $resultObj->Transaction_ID;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mTransactionId;
	}

	//! Bool check - whether or not the transaction ID has been set; should fix the Protx error 2015 problem
	/*!
	 * @return Boolean - True if the transaction details are set, false otherwise
	 */
	function IsTransactionDetailsSet() {
		$transactionID = $this->GetTransactionId ();
		if (empty ( $transactionID ) || is_null ( $transactionID ) || $transactionID == '' || $transactionID == ' ') {
			return false;
		} else {
			return true;
		}
	}

	//! Set the transaction id of this order
	/*!
	 * @param [in] newTransactionId : String - the new transaction id
	 * @return Boolean : true if successful
	 */
	function SetTransactionId($newTransactionId) {
		$sql = 'UPDATE tblOrder SET Transaction_ID = \'' . $newTransactionId . '\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the transaction ID for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mTransactionId = $newTransactionId;
		return true;
	}

	//! Gets the order tracking number
	/*!
	 * @return String
	 */
	function GetTrackingNumber() {
		if (! isset ( $this->mTrackingNumber )) {
			$sql = 'SELECT Tracking_Number FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mTrackingNumber = $resultObj->Tracking_Number;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mTrackingNumber;
	}

	//! Set the tracking number of this order
	/*!
	 * @param [in] newTrackingNumber : String - the new tracking number
	 * @return Boolean : true if successful
	 */
	function SetTrackingNumber($newTrackingNumber) {
		$sql = 'UPDATE tblOrder SET Tracking_Number = \'' . $newTrackingNumber . '\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the tracking number for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mTrackingNumber = $newTrackingNumber;
		return true;
	}

	//! Gets the total price of the order
	/*!
	 * @return Decimal
	 */
	function GetTotalPrice() {
		if (! isset ( $this->mTotalPrice )) {
			$sql = 'SELECT Total_Price FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mTotalPrice = $resultObj->Total_Price;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mTotalPrice;
	}

	//! Set the total price of the order
	/*!
	 * @param [in] newTotalPrice : Decimal - the new price
	 * @return Boolean : true if successful
	 */
	function SetTotalPrice($newTotalPrice) {
		$sql = 'UPDATE tblOrder SET Total_Price = \'' . $newTotalPrice . '\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the total price for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mTotalPrice = $newTotalPrice;
		return true;
	}

	//! Gets the actual amount taken for the order
	/*!
	 * @return Decimal
	 */
	function GetActualTaken() {
		if (! isset ( $this->mTotalActuallyTaken )) {
			$sql = 'SELECT Total_Actually_Taken FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mTotalActuallyTaken = $resultObj->Total_Actually_Taken;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mTotalActuallyTaken;
	}

	//! Set the actual amount taken for the order
	/*!
	 * @param [in] newActuallyTaken : Decimal - the actual money taken
	 * @return Boolean : true if successful
	 */
	function SetActualTaken($newActuallyTaken) {
		$sql = 'UPDATE tblOrder SET Total_Actually_Taken = \'' . $newActuallyTaken . '\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the actual taken total for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mTotalActuallyTaken = $newActuallyTaken;
		return true;
	}

	//! Gets the total tax of the order
	/*!
	 * @return Decimal
	 */
	function GetTotalTax() {
		if (! isset ( $this->mTotalTax )) {
			$sql = 'SELECT Total_Tax FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mTotalTax = $resultObj->Total_Tax;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mTotalTax;
	}

	//! Set the total tax of the order
	/*!
	 * @param [in] newTotalTax : Decimal - the new tax
	 * @return Boolean : true if successful
	 */
	function SetTotalTax($newTotalTax) {
		$sql = 'UPDATE tblOrder SET Total_Tax = \'' . $newTotalTax . '\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the total tax for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mTotalTax = $newTotalTax;
		return true;
	}

	//! Gets the total postage of the order
	/*!
	 * @return Decimal
	 */
	function GetTotalPostage() {
		if (! isset ( $this->mTotalPostage )) {
			$sql = 'SELECT Total_Postage FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mTotalPostage = $resultObj->Total_Postage;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mTotalPostage;
	}

	//! Set the total postage of the order
	/*!
	 * @param [in] newTotalPostage : Decimal - the new postage
	 * @return Boolean : true if successful
	 */
	function SetTotalPostage($newTotalPostage) {
		$sql = 'UPDATE tblOrder SET Total_Postage = ' . $newTotalPostage . ' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the total postage for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mTotalPostage = $newTotalPostage;
		return true;
	}

	//! Gets the total postage tax of the order
	/*!
	 * @return Decimal
	 */
	function GetTotalPostageTax() {
		if (! isset ( $this->mTotalPostageTax )) {
			$sql = 'SELECT Total_Postage_Tax FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mTotalPostageTax = $resultObj->Total_Postage_Tax;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mTotalPostageTax;
	}

	//! Set the total postage of the order
	/*!
	 * @param [in] newTotalPostageTax : Decimal - the new postage tax
	 * @return Boolean : true if successful
	 */
	function SetTotalPostageTax($newTotalPostageTax) {
		$sql = 'UPDATE tblOrder SET Total_Postage_Tax = \'' . $newTotalPostageTax . '\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the total postage tax for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mTotalPostageTax = $newTotalPostageTax;
		return true;
	}

	//! Returns whether the order has been downloaded
	/*!
	* @return String(1) - Either 0 or 1 (False or True)
	*/
	function GetDownloaded() {
		if (! isset ( $this->mDownloaded )) {
			$sql = 'SELECT Downloaded FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mDownloaded = $resultObj->Downloaded;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mDownloaded;
	}

	//! Sets whether the order has been downloaded
	/*!
	 * @param [in] newDownloaded : String(1) - Either 0 or 1
	 * @return Boolean : true if successful
	 */
	function SetDownloaded($newDownloaded) {
		$sql = 'UPDATE tblOrder SET Downloaded = \'' . $newDownloaded . '\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the downloaded information for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDownloaded = $newDownloaded;
		return true;
	}

	//! Returns whether the order should have a brochure sent with it
	/*!
	* @return String(1) - Either 0 or 1 (False or True)
	*/
	function GetBrochure() {
		if (! isset ( $this->mBrochure )) {
			$sql = 'SELECT Brochure FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mBrochure = $resultObj->Brochure;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mBrochure;
	}

	//! Sets whether the order should have a brochure sent with it
	/*!
	 * @param [in] newBrochure : String(1) - Either 0 or 1
	 * @return Boolean : true if successful
	 */
	function SetBrochure($newBrochure) {
		$sql = 'UPDATE tblOrder SET Brochure = \'' . $newBrochure . '\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the brochure information for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mBrochure = $newBrochure;
		return true;
	}

	//! Returns whether the customer has received an email asking them to review the products
	/*!
	* @return String(1) - Either 0 or 1 (False or True)
	*/
	function GetReviewEmailSent() {
		if (! isset ( $this->mReviewEmailSent )) {
			$sql = 'SELECT Review_Email_Sent FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mReviewEmailSent = $resultObj->Review_Email_Sent;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mReviewEmailSent;
	}

	//! Sets whether the the customer has been sent an email asking them to review their products
	/*!
	 * @param [in] newSent : String(1) - Either 0 or 1
	 * @return Boolean : true if successful
	 */
	function SetReviewEmailSent($newSent) {
		$sql = 'UPDATE tblOrder SET Review_Email_Sent = \'' . $newSent . '\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the review email sent information for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mReviewEmailSent = $newSent;
		return true;
	}

	//! Gets the status (Eg. In Transit) of the order
	/*!
	 * @return Obj:OrderStatusModel - the status of the order
	 */
	function GetStatus() {
		if (! isset ( $this->mStatus )) {
			$sql = 'SELECT Status_ID FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mStatus = new OrderStatusModel ( $resultObj->Status_ID );
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mStatus;
	}

	//! Sets the status of the order
	/*!
	 * @param [in] newStatus : Obj:OrderStatusModel - the new status
	 * @return Boolean : true if successful
	 */
	function SetStatus($newStatus) {
		$sql = 'UPDATE tblOrder SET Status_ID = \'' . $newStatus->GetStatusId () . '\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the status for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mStatus = $newStatus;
		return true;
	}

	//! Gets the courier (Eg. Royal Mail) of the order
	/*!
	 * @return Obj:CourierModel - the courier for the order
	 */
	function GetCourier() {
		if (! isset ( $this->mCourier )) {
			$sql = 'SELECT Courier_ID FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mCourier = new CourierModel ( $resultObj->Courier_ID );
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mCourier;
	}

	//! Sets the courier for the order
	/*!
	 * @param [in] newCourier : Obj:CourierModel - the new courier
	 * @return Boolean : true if successful
	 */
	function SetCourier($newCourier) {
		$sql = 'UPDATE tblOrder SET Courier_ID = \'' . $newCourier->GetCourierId () . '\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the courier for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mCourier = $newCourier;
		return true;
	}

	//! Gets the postage method (Eg. 'First Class Recorded') of the order
	/*!
	 * @return Obj:PostageMethodModel - the postage method of the order
	 */
	function GetPostageMethod() {
		if (! isset ( $this->mPostageMethod )) {
			$sql = 'SELECT Postage_Method_ID FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mPostageMethod = new PostageMethodModel ( $resultObj->Postage_Method_ID );
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mPostageMethod;
	}

	//! Sets the postage method for the order
	/*!
	 * @param [in] newPostageMethod : Obj:PostageMethodModel - the new method
	 * @return Boolean : true if successful
	 */
	function SetPostageMethod($newPostageMethod) {
		$sql = 'UPDATE tblOrder SET Postage_Method_ID = \'' . $newPostageMethod->GetPostageMethodId () . '\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the postage method for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mPostageMethod = $newPostageMethod;
		return true;
	}

	//! Gets the billing address of the order
	/*!
	 * @return Obj:AddressModel - the address where the card is registered
	 */
	function GetBillingAddress() {
		if (! isset ( $this->mBillingAddress )) {
			$sql = 'SELECT Billing_Address_ID FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mBillingAddress = new AddressModel ( $resultObj->Billing_Address_ID );
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mBillingAddress;
	}

	//! Sets the billing address for the order
	/*!
	 * @param [in] newBillingAddress : Obj:AddressModel - the new address
	 * @return Boolean : true if successful
	 */
	function SetBillingAddress($newBillingAddress) {
		$sql = 'UPDATE tblOrder SET Billing_Address_ID = \'' . $newBillingAddress->GetAddressId () . '\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the billing address for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mBillingAddress = $newBillingAddress;
		return true;
	}

	//! Gets the shipping address of the order
	/*!
	 * @return Obj:AddressModel - the address where the order will be dispatched to
	 */
	function GetShippingAddress() {
		if (! isset ( $this->mShippingAddress )) {
			$sql = 'SELECT Shipping_Address_ID FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mShippingAddress = new AddressModel ( $resultObj->Shipping_Address_ID );
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mShippingAddress;
	}

	//! Sets the shipping address for the order
	/*!
	 * @param [in] newShippingAddress : Obj:AddressModel - the new address
	 * @return Boolean : true if successful
	 */
	function SetShippingAddress($newShippingAddress) {
		$sql = 'UPDATE tblOrder SET Shipping_Address_ID = \'' . $newShippingAddress->GetAddressId () . '\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the shipping address for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mShippingAddress = $newShippingAddress;
		return true;
	}

	//! Gets the affiliate that referred the order (if any)
	/*!
	 * @return Obj:AffiliateModel or NULL - the affiliate that referred the customer to this order
	 */
	function GetAffiliate() {
		if (! isset ( $this->mAffiliate )) {
			$sql = 'SELECT Affiliate_ID FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				if (NULL === $resultObj->Affiliate_ID) {
					return NULL;
				} else {
					$this->mAffiliate = new AffiliateModel ( $resultObj->Affiliate_ID );
				}
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mAffiliate;
	}

	//! Sets the affiliate for the order
	/*!
	 * @param [in] newAffiliate : Obj:AffiliateModel - the new affiliate
	 * @return Boolean : true if successful
	 */
	function SetAffiliate($newAffiliate) {
		$sql = 'UPDATE tblOrder SET Affiliate_ID = \'' . $newAffiliate->GetAffiliateId () . '\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the affiliate for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mAffiliate = $newAffiliate;
		return true;
	}

	//! Gets the currency of the order
	/*!
	 * @return Obj:CurrencyModel
	 */
	function GetCurrency() {
		if (! isset ( $this->mCurrency )) {
			$sql = 'SELECT Currency_ID FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mCurrency = new CurrencyModel ( $resultObj->Currency_ID );
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mCurrency;
	}

	//! Sets the currency for the order
	/*!
	 * @param [in] newCurrency : Obj:CurrencyModel - the new currency
	 * @return Boolean : true if successful
	 */
	function SetCurrency($newCurrency) {
		$sql = 'UPDATE tblOrder SET Currency_ID = \'' . $newCurrency->GetCurrencyId () . '\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the currency for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mCurrency = $newCurrency;
		return true;
	}

	//! Gets the customer that placed the order
	/*!
	 * @return Obj:CustomerModel
	 */
	function GetCustomer() {
		if (! isset ( $this->mCustomer )) {
			$sql = 'SELECT Customer_ID FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mCustomer = new CustomerModel ( $resultObj->Customer_ID, 'id' );
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mCustomer;
	}

	//! Sets the customer for the order
	/*!
	 * @param [in] newCustomer : Obj:CustomerModel - the new customer
	 * @return Boolean : true if successful
	 */
	function SetCustomer($newCustomer) {
		$sql = 'UPDATE tblOrder SET Customer_ID = \'' . $newCustomer->GetCustomerId () . '\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the customer for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mCustomer = $newCustomer;
		return true;
	}

	//! Gets the basket with the SKUs in
	/*!
	 * @return Obj:BasketModel
	 */
	function GetBasket() {
		if (! isset ( $this->mBasket )) {
			$sql = 'SELECT Basket_ID FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mBasket = new BasketModel ( $resultObj->Basket_ID );
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mBasket;
	}

	//! Gets any SKUs that haven't been shipped (because not in stock)
	/*!
	 * @return Array of Obj:SkuModel Objects / Exception
	 */
	function GetUnshippedItems() {
		$retArr = array ();
		$sql = 'SELECT Order_Item_ID FROM tblOrder_Items WHERE Order_ID = \''.$this->mOrderId.'\' AND Shipped = \'0\'';
		if ($result = $this->mDatabase->query ( $sql )) {
			while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
				$newOrderItem = new OrderItemModel ( $resultObj->Order_Item_ID );
				$retArr [] = $newOrderItem;
			}
		} else {
			$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		return $retArr;
	}

	//! Gets any SKUs that have been shipped
	/*!
	 * @return Array of Obj:SkuModel Objects / Exception
	 */
	function GetShippedItems() {
		$retArr = array ();
		$sql = 'SELECT Order_Item_ID FROM tblOrder_Items WHERE Shipped = \'1\' AND Order_ID = '.$this->mOrderId;
		if ($result = $this->mDatabase->query ( $sql )) {
			while($resultObj = $result->fetch(PDO::FETCH_OBJ)) {
				$newItem = new OrderItemModel($resultObj->Order_Item_ID);
				$retArr[] = $newItem;
			}
		} else {
			$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		return $retArr;
	}

	//! Bool - were all items shipped?
	function IsAllItemsShipped() {
		$sql = 'SELECT COUNT(Shipped) AS ShipCount FROM tblOrder_Items WHERE Order_ID = \'' . $this->mOrderId. '\' AND Shipped = \'0\'';
		if ($result = $this->mDatabase->query ( $sql )) {
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			if ($resultObj->ShipCount > 0) {
				return false;
			} else {
				return true;
			}
		} else {
			$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

	//! Gets the staff that took the order
	/*!
	 * @return String
	 */
	function GetStaffName() {
		if (! isset ( $this->mStaffName )) {
			$sql = 'SELECT Member_Of_Staff FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mStaffName = $resultObj->Member_Of_Staff;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mStaffName;
	}

	//! Set the member of staff that took the order
	/*!
	 * @param [in] newStaffName : String - the new staff name
	 * @return Boolean : true if successful
	 */
	function SetStaffName($newStaffName) {
		$sql = 'UPDATE tblOrder SET Member_Of_Staff = \'' . $newStaffName . '\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the staff name for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mStaffName = $newStaffName;
		return true;
	}

	function GetSecurityKey() {
		if (! isset ( $this->mSecurityKey )) {
			$sql = 'SELECT Transaction_Security_Key FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mSecurityKey = $resultObj->Transaction_Security_Key;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mSecurityKey;
	}

	function SetSecurityKey($newKey) {
		$sql = 'UPDATE tblOrder SET Transaction_Security_Key = \'' . $newKey . '\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the security key for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mSecurityKey = $newKey;
		return true;
	}

	function GetAuthRef() {
		if (! isset ( $this->mAuthRef )) {
			$sql = 'SELECT Authorisation_Reference_Number FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mAuthRef = $resultObj->Authorisation_Reference_Number;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mAuthRef;
	}

	function GetNextAuthRef() {
		$sql = 'SELECT Authorisation_Reference_Number FROM tblOrder ORDER BY Authorisation_Reference_Number DESC LIMIT 1';
		if ($result = $this->mDatabase->query ( $sql )) {
			$resultObj = $result->fetchObject ();
			$currentAuthRef = $resultObj->Authorisation_Reference_Number;
		} else {
			$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$nextAuthRef = $currentAuthRef + 1;
		return $nextAuthRef;
	}

	function SetAuthRef($newRef) {
		$sql = 'UPDATE tblOrder SET Authorisation_Reference_Number = \'' . $newRef . '\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the authorisation reference for order: ' . $this->mOrderId .$sql);
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mAuthRef = $newRef;
		return true;
	}

	function GetCavv() {
		if (! isset ( $this->mCavv )) {
			$sql = 'SELECT CAVV FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mCavv = $resultObj->CAVV;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mCavv;
	}

	function SetCavv($newCavv) {
		$sql = 'UPDATE tblOrder SET CAVV = \'' . $newCavv . '\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the CAVV for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mCavv = $newCavv;
		return true;
	}

	function Get3DSecureStatus() {
		if (! isset ( $this->m3DSecureStatus )) {
			$sql = 'SELECT ThreeD_Secure_Status FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->m3DSecureStatus = $resultObj->ThreeD_Secure_Status;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->m3DSecureStatus;
	}

	function Set3DSecureStatus($newStatus) {
		$sql = 'UPDATE tblOrder SET ThreeD_Secure_Status = \'' . $newStatus . '\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the 3D secure status for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->m3DSecureStatus = $newStatus;
		return true;
	}

	function GetTxAuthNo() {
		if (! isset ( $this->mTxAuthNo )) {
			$sql = 'SELECT TxAuthNo FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mTxAuthNo = $resultObj->TxAuthNo;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mTxAuthNo;
	}

	function SetTxAuthNo($newTxAuthNo) {
		$sql = 'UPDATE tblOrder SET TxAuthNo = \'' . $newTxAuthNo . '\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the TxAuthNo for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mTxAuthNo = $newTxAuthNo;
		return true;
	}


	//! Converts a basket into an order items array, which solves the 'missing SKU' problem on the admin side by removing the dependancy on the product still existing
	function ConvertBasketIntoOrder() {
		$this->mMoneyHelper = new MoneyHelper;
		$this->mRegistry = Registry::getInstance();

		$skuBucket 	= $this->GetBasket()->GetSkus(false,false);
		$packages 	= $this->GetBasket()->GetPackages();
		#$fh = fopen('OrderModelDebug.txt','w+');

		// Are we VAT-Free?
		#($this->GetShippingAddress()->GetCountry()->IsVatFree() ? $this->mVatFree = true : $this->mVatFree = false );
		$this->mVatFree = false;

		// Fix the 'array_search reads a 0 index as false' bug
		$preppedArr = array();
		// The SQL array
		$sql = array();

		/*fwrite($fh,"Original Products:");
		foreach($skuBucket as $index=>$sku) {
			fwrite($fh,"\n\r- [".$index."]".$sku->GetParentProduct()->GetDisplayName()."");
		}*/

		// Remove package SKUS from the sku bucket
		$i=0;
		$packageCount=1;
		foreach($packages as $package) {
			if($package) {
				// VAT Free?
				(($this->mVatFree && $this->mRegistry->packageVatFreeAllowed) ? $packagePrice = $this->mMoneyHelper->RemoveVAT($this->GetBasket()->GetOverruledPackagePrice($package)) : $packagePrice = $this->GetBasket()->GetOverruledPackagePrice($package));

				// Shipped?
				$shipped = $this->GetBasket()->IsShippedPackage($package);

				// Add the package to the basket
				$preppedArr[$i]['Display_Name'] 	= $package->GetDisplayName();
				$preppedArr[$i]['Price']			= $packagePrice;
				$preppedArr[$i]['Shipped']			= $shipped;
				$preppedArr[$i]['Package_ID']		= $packageCount;
				$preppedArr[$i]['Package_Product']	= 0;
				$preppedArr[$i]['Package_Upgrade']	= 0;
				$preppedArr[$i]['Sage_Code']		= 'PACKAGE';
				$preppedArr[$i]['Taxable'] 			= 0;
				$i++;
				#fwrite($fh,"\n\nAdded Package: ".$package->GetDisplayName()."");

				// Package Contents
				foreach($package->GetContents() as $product) {
					#fwrite($fh,"\n\n- Looking At Product: ".$product->GetDisplayName()."");
					// If this is still false after looking at all the products then it must have been upgraded
					$productFound = false;
					foreach($product->GetSkus() as $sku) {
						if(!$productFound) {
							#fwrite($fh,"\n\r  - Looking At SKU: ".$sku->GetSkuId()."");
								// If the SKU is in the bucket, remove it and add it to the prepped array
								if(FALSE !== array_search($sku,$skuBucket)) {
									$key = array_search($sku,$skuBucket);
									if(!$this->GetBasket()->IsPackageUpgrade($sku) && $this->GetBasket()->IsPackage($sku)) {
										#fwrite($fh,"\n\r    - Found SKU (Item): ".$sku->GetSkuId().", Key = ".$key);
										// Taxable?
										($sku->GetParentProduct()->GetTaxCode()->GetRate() == 0 ? $taxable=0 : $taxable = 1);

										// VAT Free?
										($this->mVatFree ? $skuPrice = $this->mMoneyHelper->RemoveVAT($this->GetBasket()->GetOverruledSkuPrice($sku,true)) : $skuPrice = $this->GetBasket()->GetOverruledSkuPrice($sku,true));
										// Historic purposes - if no price found...
										if(!$skuPrice) { $skuPrice = '0.0'; }

										// Sage Code?
										(trim($sku->GetSageCode()) ? $sageCode = $sku->GetSageCode() : $sageCode = 'DUMMYSAGECODE');

										// Shipped?
										$shipped = $this->GetBasket()->IsShipped($sku);
										for($j=0;$j<$package->GetProductQty($sku->GetParentProduct());$j++) {
											// Get the details
											$preppedArr[$i]['Display_Name'] 	= $sku->GetParentProduct()->GetDisplayName().' '.$sku->GetAttributeList();
											$preppedArr[$i]['Price']			= $skuPrice;
											$preppedArr[$i]['Shipped']			= $shipped;
											$preppedArr[$i]['Package_ID']		= 0;
											$preppedArr[$i]['Package_Product']	= $packageCount;	// Set which package it belongs to
											$preppedArr[$i]['Package_Upgrade']	= 0;
											$preppedArr[$i]['Sage_Code']		= $sageCode;
											$preppedArr[$i]['Taxable'] 			= $taxable;
											// Increment the array index
											$i++;
										}
										// Remove the SKU from the bucket
										array_splice($skuBucket,$key,1);
										// Say we've found the product (so as not to bother looking at upgrades)
										$productFound = true;
									} // End if isnt upgrade, is package
								} // End if in bucket
							} // End if($productFound...
					} // End foreach($product->getSkus...
					if(!$productFound) {
						#fwrite($fh,"\n\r- Product Not Found! Looking For Upgrades...");
						// Must have been upgraded
						$upgradeFound = false;
						foreach($package->GetUpgradesFor($product) as $upgrade) {
							if(!$upgradeFound) {
							#fwrite($fh,"\n\r- Looking At Upgrade: ".$upgrade->GetDisplayName()."");
							foreach($upgrade->GetSkus() as $sku) {
								#fwrite($fh,"\n\r  - Looking At SKU (".$upgradeFound."): ".$sku->GetSkuId()."");
									// If the SKU is in the bucket, remove it and add it to the prepped array
									if(FALSE !== array_search($sku,$skuBucket)) {
										$key = array_search($sku,$skuBucket);
										if($this->GetBasket()->IsPackageUpgrade($sku)) {
											#fwrite($fh,"\n\r    - Found SKU (Upgrade): ".$sku->GetSkuId().", Key = ".$key);
											// Taxable?
											($sku->GetParentProduct()->GetTaxCode()->GetRate() == 0 ? $taxable=0 : $taxable = 1);

											// VAT Free?
											($this->mVatFree ? $skuPrice = $this->mMoneyHelper->RemoveVAT($this->GetBasket()->GetOverruledSkuPrice($sku,false,true)) : $skuPrice = $this->GetBasket()->GetOverruledSkuPrice($sku,false,true));

											// Sage Code?
											(trim($sku->GetSageCode()) ? $sageCode = $sku->GetSageCode() : $sageCode = 'DUMMYSAGECODE');

											// Historic purposes - if no price found...
											if(!$skuPrice) { $skuPrice = '0.0'; }

											// Shipped?
											$shipped = $this->GetBasket()->IsShipped($sku);
											// The upgrade isnt "in" the package... its 'parent' is
											for($j=0;$j<$package->GetUpgradeQty($sku->GetParentProduct());$j++) {
												// Get the details
												$preppedArr[$i]['Display_Name'] 	= $sku->GetParentProduct()->GetDisplayName().' '.$sku->GetAttributeList();
												$preppedArr[$i]['Price']			= $skuPrice;
												$preppedArr[$i]['Shipped']			= $shipped;
												$preppedArr[$i]['Package_ID']		= 0;
												$preppedArr[$i]['Package_Product']	= 0;
												$preppedArr[$i]['Package_Upgrade']	= $packageCount;	// Set which package it belongs to
												$preppedArr[$i]['Sage_Code']		= $sageCode;
												$preppedArr[$i]['Taxable'] 			= $taxable;
												// Increment the array index
												$i++;
											}
											// Remove the SKU from the bucket
											array_splice($skuBucket,$key,1);
											// Say we've found the upgrade
											$upgradeFound = true;
										} // End if is upgrade
									} // End If sku is in the bucket
								} // End  Foreach($upgrade->SKUs
							} // End If upgrade not found
						} // End Foreach($package->Upgrades...

					} // End If($productFound...
					#fwrite($fh,"\n\n\rProducts after product: ".$product->GetDisplayName());
					/*foreach($skuBucket as $index=>$sku) {
						fwrite($fh,"\n\r- [".$index."]".$sku->GetParentProduct()->GetDisplayName()."");
					}*/
				} // End foreach($package->Product..
				$packageCount++;
			} // End if (is_object($package))
		} // End forach($package...

		#fwrite($fh,"\n\nProducts after packages:");
		/*foreach($skuBucket as $sku) {
			fwrite($fh,"\n- ".$sku->GetParentProduct()->GetDisplayName()."");
		}*/

		// Now for the 'normal' products
		foreach($skuBucket as $sku) {
			// Taxable?
			($sku->GetParentProduct()->GetTaxCode()->GetRate() == 0  ? $taxable=0 : $taxable = 1);

			// VAT Free?
			(($this->mVatFree && $taxable==1 )? $skuPrice = $this->mMoneyHelper->RemoveVAT($this->GetBasket()->GetOverruledSkuPrice($sku)) : $skuPrice = $this->GetBasket()->GetOverruledSkuPrice($sku));

			// Historic purposes - if no price found...
			if(!$skuPrice) { $skuPrice = '0.0'; }

			// Sage Code?
			(trim($sku->GetSageCode()) ? $sageCode = $sku->GetSageCode() : $sageCode = 'DUMMYSAGECODE');

			// Shipped
			$shipped = $this->GetBasket()->IsShipped($sku);

			// Sold Out? (can do this because this happens BEFORE stock levels are updated - so if this is zero then it was
			// zero at the time of checkout
			if($sku->GetQty() == 0) {
				$soldOutMessage = ' - SOLD OUT';
			} else {
				$soldOutMessage = '';
			}

			// Get the details
			$preppedArr[$i]['Display_Name'] 	= $sku->GetParentProduct()->GetDisplayName().' '.$sku->GetAttributeList().$soldOutMessage;
			$preppedArr[$i]['Price']			= $skuPrice;
			$preppedArr[$i]['Shipped']			= $shipped;
			$preppedArr[$i]['Package_ID']		= 0;
			$preppedArr[$i]['Package_Product']	= 0;
			$preppedArr[$i]['Package_Upgrade']	= 0;
			$preppedArr[$i]['Sage_Code']		= $sageCode;
			$preppedArr[$i]['Taxable'] 			= $taxable;
			// Remove the SKU from the bucket
			unset($skuBucket[key($skuBucket)]);
			// Increment the array index
			$i++;
		}

		// Due to the way PHP round() works (with VAT-Free), sometimes the total is 1p less than the number of products
		// added up. This takes 1p off one of the items (arbitrarily) to counter this.
		$preppedArr = $this->FixVatFreeBug($preppedArr);

		foreach($preppedArr as $item) {
			// Add it to the order
			$sql[] = '
					INSERT INTO tblOrder_Items
					(
					 `Order_ID`,`Display_Name`,`Price`,`Shipped`,`Package_Product`,`Package_Upgrade`,`Sage_Code`,`Taxable`,`Package_ID`
					 )
					VALUES
					(
					 \''.$this->mOrderId.'\',
					 \''.$item['Display_Name'].'\',
					 \''.$item['Price'].'\',
					 \''.$item['Shipped'].'\',
					 \''.$item['Package_Product'].'\',
					 \''.$item['Package_Upgrade'].'\',
					 \''.$item['Sage_Code'].'\',
					 \''.$item['Taxable'].'\',
					 \''.$item['Package_ID'].'\'
					 )
					';
		}
		// Only add items to an order if the order is unlocked to prevent refreshing doubling this up
		if($this->IsUnlocked()) {
			// Actually do the 'adding' to the database
			foreach($sql as $addItemSql) {
				if(FALSE === $this->mDatabase->query($addItemSql)) {
					$error = new Error ( 'Could not convert the basket to an order: ' . $this->mOrderId );
					$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
					throw new Exception ( $error->GetErrorMsg () );
				}
			}
			$this->Lock();
			return true;
		} else {
			return false;
		}
	} // End ConvertBasketIntoOrder

	//! Takes the prepped array, and takes 1p off one of the items if it would cause a '1p overflow' when trying to ship
	function FixVatFreeBug($preppedArr) {
		$runningTotal = 0;
		foreach($preppedArr as $orderItem) {
			$runningTotal += $orderItem['Price'];
		}
		if($this->GetTotalPrice() == $runningTotal) {
			return $preppedArr;
		} else {
			$preppedArr[0]['Price'] = ($preppedArr[0]['Price']-0.01);
			return $preppedArr;
		}
	}

	function Lock() {
		$sql = 'UPDATE tblOrder SET `Lock` = \'1\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the lock for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		return true;
	}

	function IsUnlocked() {
		$sql = 'SELECT `Lock` FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
		if ($result = $this->mDatabase->query ( $sql )) {
			$resultObj = $result->fetchObject();
			if($resultObj->Lock) {
				return false;
			} else {
				return true;
			}
		} else {
			$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

	function SetCatalogue($catalogue) {
		$sql = 'UPDATE tblOrder SET Catalogue_ID = \'' . $catalogue->GetCatalogueId() . '\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the Catalogue ID for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mCatalogue = $catalogue;
		return true;
	}

	function GetCatalogue() {
		if (! isset ( $this->mCatalogue )) {
			$sql = 'SELECT Catalogue_ID FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mCatalogue = new CatalogueModel($resultObj->Catalogue_ID);
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mCatalogue;
	}

	//! Set whether or not the order went through Google Checkout
	/*!
	 * @param $newVal - Boolean - True if it is
	 * @return True on success
	 */
	function SetGoogleCheckout($newVal) {
		$sql = 'UPDATE tblOrder SET Google_Checkout = \''.$newVal.'\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the google checkout status for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		return true;
	}

	//! Set whether or not the order went through Paypal
	/*!
	 * @param $newVal - Boolean - True if it is
	 * @return True on success
	 */
	function SetPaypalOrder($newVal) {
		$sql = 'UPDATE tblOrder SET Paypal_Order = \''.$newVal.'\' WHERE Order_ID = ' . $this->mOrderId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the paypal order status for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		return true;
	}

	//! Whether or not the order is a Paypal order
	/*!
	 * @return Boolean - True if the order went through Paypal
	 */
	function GetPaypalOrder() {
		$sql = 'SELECT Paypal_Order FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
		if ($result = $this->mDatabase->query ( $sql )) {
			$resultObj = $result->fetchObject();
			if($resultObj->Paypal_Order) {
				return true;
			} else {
				return false;
			}
		} else {
			$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

	//! Whether or not the order is a Google Checkout order
	/*!
	 * @return Boolean - True if the order went through Google Checkout
	 */
	function GetGoogleCheckout() {
		$sql = 'SELECT Google_Checkout FROM tblOrder WHERE Order_ID = ' . $this->mOrderId;
		if ($result = $this->mDatabase->query ( $sql )) {
			$resultObj = $result->fetchObject();
			if($resultObj->Google_Checkout) {
				return true;
			} else {
				return false;
			}
		} else {
			$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

	//! Gets the Order ID
	/*!
	 * @return Int
	 */
	function GetOrderId() {
		return $this->mOrderId;
	}

} // End OrderModel Class


/* DEBUG
try {
	$order = new OrderModel(1);
/*	echo '---<br />';
	echo $order->GetCustomer()->GetFirstName();
	$cust = new CustomerModel(2);
	$order->SetCustomer($cust);
	echo $order->GetCustomer()->GetFirstName();

} catch(Exception $e) {
	echo $e->getMessage();
}*/

?>