<?php

//! Models a customer
class CustomerModel {

	//! Unique identifier for a customer
	var $mCustomerId;
	//! String
	var $mFirstName;
	//! String
	var $mLastName;
	//! String
	var $mUserName;
	//! String
	var $mPassword;
	//! String
	var $mEmail;
	//! String
	var $mDaytimeTelephone;
	//! String
	var $mMobileTelephone;
	//! Int
#	var $mOrderCount;
	//! Array of AddressModel objects
	var $mPreviousAddresses;
	//! Obj:PDO - database connection used to access database level
	var $mDatabase;

	//! Constructor, initialises the customer
	/*
	 * @param $email [in] String - The customer's email address
	 * @param $method [in] String - The method of looking up the customer, defaults to 'email' other option is 'id'
	 * @return Itself / Exception
	 */
	function __construct($email, $method = 'email') {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
		switch ($method) {
			case 'email' :
				$check_sql = 'SELECT COUNT(Email) FROM tblCustomer WHERE Email = \'' . $email . '\'';
				if (! $result = $this->mDatabase->query ( $check_sql )) {
					$error = new Error ( 'Could not construct customer.' );
					$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
					throw new Exception ( $error->GetErrorMsg () );
				}
				if ($result->fetchColumn () > 0) {
					$this->mEmail = $email;
					$this->mCustomerId = $this->GetCustomerIdFromEmail ();
				} else {
					$error = new Error ( 'Could not initialise customer ' . $email . ' because it does not exist in the database.' );
					$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
					throw new Exception ( $error->GetErrorMsg () );
				}
				break;
			case 'id' :
				$check_sql = 'SELECT COUNT(Customer_ID) FROM tblCustomer WHERE Customer_ID = \'' . $email . '\'';
				if (! $result = $this->mDatabase->query ( $check_sql )) {
					$error = new Error ( 'Could not construct customer.' );
					$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
					throw new Exception ( $error->GetErrorMsg () );
				}
				if ($result->fetchColumn () > 0) {
					$this->mCustomerId = $email;
				} else {
					$error = new Error ( 'Could not initialise customer ' . $email . ' because it does not exist in the database.' );
					$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
					throw new Exception ( $error->GetErrorMsg () );
				}
				break;
		}
	}

	//! Returns the first name of the customer
	/*!
	 * @return String
	 */
	function GetFirstName() {
		if (! isset ( $this->mFirstName )) {
			$sql = 'SELECT First_Name FROM tblCustomer WHERE Customer_ID = ' . $this->mCustomerId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mFirstName = $resultObj->First_Name;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return trim($this->mFirstName);
	}

	//! Set the first name for this customer
	/*!
	 * @param [in] newFirstName : String - the new first name
	 * @return Boolean : true if successful
	 */
	function SetFirstName($newFirstName) {
		$sql = 'UPDATE tblCustomer SET First_Name = \'' . $newFirstName . '\' WHERE Customer_ID = ' . $this->mCustomerId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the first name of customer: ' . $this->mCustomerId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mFirstName = $newFirstName;
		return true;
	}

	//! Returns the last name of the customer
	/*!
	 * @return String
	 */
	function GetLastName() {
		if (! isset ( $this->mLastName )) {
			$sql = 'SELECT Last_Name FROM tblCustomer WHERE Customer_ID = ' . $this->mCustomerId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mLastName = $resultObj->Last_Name;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return trim($this->mLastName);
	}

	//! Set the last name for this customer
	/*!
	 * @param [in] newLastName : String - the new last name
	 * @return Boolean : true if successful
	 */
	function SetLastName($newLastName) {
		$sql = 'UPDATE tblCustomer SET Last_Name = \'' . $newLastName . '\' WHERE Customer_ID = ' . $this->mCustomerId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the last name of customer: ' . $this->mCustomerId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mLastName = $newLastName;
		return true;
	}

	//! Returns the user name of the customer
	/*!
	 * @return String
	 */
	function GetTitle() {
		if (! isset ( $this->mTitle )) {
			$sql = 'SELECT Title FROM tblCustomer WHERE Customer_ID = ' . $this->mCustomerId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mTitle = $resultObj->Title;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return trim($this->mTitle);
	}

	//! Set the title for this customer
	/*!
	 * @param [in] newTitle : String - the new title
	 * @return Boolean : true if successful
	 */
	function SetTitle($newTitle) {
		$sql = 'UPDATE tblCustomer SET Title = \'' . $newTitle . '\' WHERE Customer_ID = ' . $this->mCustomerId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the title of customer: ' . $this->mCustomerId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mTitle = $newTitle;
		return true;
	}

	//! Returns the password of the customer
	/*!
	 * @return String
	 */
	function GetPassword() {
		if (! isset ( $this->mPassword )) {
			$sql = 'SELECT Password FROM tblCustomer WHERE Customer_ID = ' . $this->mCustomerId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mPassword = $resultObj->Password;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mPassword;
	}

	//! Set the password for this customer
	/*!
	 * @param [in] newPassword : String - the new password
	 * @return Boolean : true if successful
	 */
	function SetPassword($newPassword) {
		$sql = 'UPDATE tblCustomer SET Password = \'' . sha1 ( $newPassword ) . '\' WHERE Customer_ID = ' . $this->mCustomerId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the password of customer: ' . $this->mCustomerId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mPassword = $newPassword;
		return true;
	}

	//! Returns the email address of the customer
	/*!
	 * @return String
	 */
	function GetEmail() {
		if (! isset ( $this->mEmail )) {
			$sql = 'SELECT Email FROM tblCustomer WHERE Customer_ID = ' . $this->mCustomerId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mEmail = $resultObj->Email;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return trim($this->mEmail);
	}

	//! Set the email address for this customer
	/*!
	 * @param [in] newEmail : String - the new email address
	 * @return Boolean : true if successful
	 */
	function SetEmail($newEmail) {
		$sql = 'UPDATE tblCustomer SET Email = \'' . $newEmail . '\' WHERE Customer_ID = ' . $this->mCustomerId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the email address of customer: ' . $this->mCustomerId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mEmail = $newEmail;
		return true;
	}

	//! Returns the daytime telephone number of the customer
	/*!
	 * @return String
	 */
	function GetDaytimeTelephone() {
		if (! isset ( $this->mDaytimeTelephone )) {
			$sql = 'SELECT Daytime_Telephone FROM tblCustomer WHERE Customer_ID = ' . $this->mCustomerId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mDaytimeTelephone = $resultObj->Daytime_Telephone;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return trim($this->mDaytimeTelephone);
	}

	//! Set the daytime telephone number for this customer
	/*!
	 * @param [in] newDaytimeTelephone : String - the new daytime telephone number
	 * @return Boolean : true if successful
	 */
	function SetDaytimeTelephone($newDaytimeTelephone) {
		$sql = 'UPDATE tblCustomer SET Daytime_Telephone = \'' . $newDaytimeTelephone . '\' WHERE Customer_ID = ' . $this->mCustomerId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the daytime telephone number of customer: ' . $this->mCustomerId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDaytimeTelephone = $newDaytimeTelephone;
		return true;
	}

	//! Returns the mobile telephone number of the customer
	/*!
	 * @return String
	 */
	function GetMobileTelephone() {
		if (! isset ( $this->mMobileTelephone )) {
			$sql = 'SELECT Mobile_Phone FROM tblCustomer WHERE Customer_ID = ' . $this->mCustomerId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mMobileTelephone = $resultObj->Mobile_Phone;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return trim($this->mMobileTelephone);
	}

	//! Set the mobile telephone number for this customer
	/*!
	 * @param [in] newMobileTelephone : String - the new mobile telephone number
	 * @return Boolean : true if successful
	 */
	function SetMobileTelephone($newMobileTelephone) {
		$sql = 'UPDATE tblCustomer SET Mobile_Phone = \'' . $newMobileTelephone . '\' WHERE Customer_ID = ' . $this->mCustomerId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the mobile telephone number of customer: ' . $this->mCustomerId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mMobileTelephone = $newMobileTelephone;
		return true;
	}

	//! Retrieves previous addresses for the customer
	/*!
	 * @return Array of Obj:AddressModel objects (possibly empty) / Exception
	 */
	function GetPreviousAddresses() {
		if (! isset ( $this->mPreviousAddresses )) {
			$sql = 'SELECT Billing_Address_ID,Shipping_Address_ID FROM tblOrder WHERE Customer_ID = ' . $this->mCustomerId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$addresses = $result->fetchAll ( PDO::FETCH_OBJ );
				foreach ( $addresses as $address ) {
					if ($address->Billing_Address_ID == $address->Shipping_Address_ID) {
						$newAddress = new AddressModel ( $address->Billing_Address_ID );
						$this->mPreviousAddresses [] = $newAddress;
					} else {
						$newAddress1 = new AddressModel ( $address->Billing_Address_ID );
						$newAddress2 = new AddressModel ( $address->Shipping_Address_ID );
						$this->mPreviousAddresses [] = $newAddress1;
						$this->mPreviousAddresses [] = $newAddress2;
					}
				}
				if (0 == count ( $skus )) {
					$this->mPreviousAddresses = array ();
				}
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mPreviousAddresses;
	}

	//! Returns the unique customer ID
	function GetCustomerId() {
		return $this->mCustomerId;
	}

	//! Return customer ID, given that the email is known (used internally to initialise the ID for use by other methods)
	/*!
	 * @return Int
	 */
	function GetCustomerIdFromEmail() {
		if (! isset ( $this->mCustomerId )) {
			$sql = 'SELECT Customer_ID FROM tblCustomer WHERE Email = \'' . $this->mEmail . '\'';
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mCustomerId = $resultObj->Customer_ID;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mCustomerId;
	}

	//! Returns whether the order is the customers first
	/*!
	* @return String(1) - Either 0 or 1 (False or True)
	*/
	function GetFirstOrder() {
		if (! isset ( $this->mFirstOrder )) {
			$sql = 'SELECT First_Order FROM tblCustomer WHERE Customer_ID = ' . $this->mCustomerId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the first order information for customer ' . $this->mCustomerId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mFirstOrder = $resultObj->First_Order;
		}
		return $this->mFirstOrder;
	}

	//! Sets the first order option of the customer
	/*!
	* @param [in] newFirstOrder String(1) : Either 0 or 1
	* @return Bool : true if successful
	*/
	function SetFirstOrder($newFirstOrder) {
		$sql = 'UPDATE tblCustomer SET First_Order = \'' . $newFirstOrder . '\' WHERE Customer_ID = ' . $this->mCustomerId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the first order option for customer ' . $this->mCustomerId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mFirstOrder = $newFirstOrder;
		return true;
	}

	//! How many orders (in transited has the customer made?)
	/*!
	* @return Int - The Order Count
	*/
	function GetOrderCount() {
		if (! isset ( $this->mOrderCount )) {
			$sql = '
					SELECT
						COUNT(tblOrder.Order_ID) As Order_Count
					FROM
						tblOrder
					INNER JOIN
						tblCustomer ON tblCustomer.Customer_ID = tblOrder.Customer_ID
					WHERE
						tblOrder.Status_ID = 6
					AND
						tblCustomer.Email = \''.$this->GetEmail().'\'
					';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the order count for customer ' . $this->mCustomerId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mOrderCount = $resultObj->Order_Count;
		}
		return $this->mOrderCount;
	}

	//! How many orders (in transited has the customer made?)
	/*!
	* @return Int - The Order Count
	*/
/*	function GetOrderCount() {
		if (! isset ( $this->mOrderCount )) {
			$sql = 'SELECT Order_Count FROM tblCustomer WHERE Customer_ID = ' . $this->mCustomerId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the order count for customer ' . $this->mCustomerId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mOrderCount = $resultObj->Order_Count;
		}
		return $this->mOrderCount;
	}*/

	//! Sets the order count for a customer
	/*!
	* @param [in] newCount Int : The new order count
	* @return Bool : true if successful
	*/
/*	function SetOrderCount($newCount) {
		$sql = 'UPDATE tblCustomer SET Order_Count = \'' . $newCount . '\' WHERE Customer_ID = ' . $this->mCustomerId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the order count for customer ' . $this->mCustomerId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mOrderCount = $newCount;
		return true;
	}*/

	//! Retrieves the most recent delivery address for the customer
	/*!
	 * @return Obj:AddressModel / Boolean False if none exist
	 */
	function GetPreviousDeliveryAddress() {
		$sql = 'SELECT Shipping_Address_ID
				FROM tblOrder
				WHERE tblOrder.Customer_ID = ' . $this->mCustomerId . '
				AND Shipping_Address_ID IS NOT NULL
				ORDER BY Created_Date DESC
				LIMIT 1';
		$result = $this->mDatabase->query ( $sql );
		if ($result) {
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			if ($resultObj && ! is_null ( $resultObj->Shipping_Address_ID ) && ! is_null ( $result )) {
				$address = new AddressModel ( $resultObj->Shipping_Address_ID );
				return $address;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	//! Retrieves the most recent billing address for the customer
	/*!
	 * @return Obj:AddressModel / Boolean False if none exist
	 */
	function GetPreviousBillingAddress() {
		$sql = 'SELECT Billing_Address_ID
				FROM tblOrder
				WHERE tblOrder.Customer_ID = ' . $this->mCustomerId . '
				AND Billing_Address_ID IS NOT NULL
				ORDER BY Created_Date DESC
				LIMIT 1';
		$result = $this->mDatabase->query ( $sql );
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		if (! is_null ( $resultObj->Billing_Address_ID ) && ! is_null ( $result )) {
			$address = new AddressModel ( $resultObj->Billing_Address_ID );
			return $address;
		} else {
			return false;
		}
	}

}

?>