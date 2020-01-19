<?php

//! Models an affiliate
class AffiliateModel {
	
	//! Unique identifier for an affiliate
	var $mAffiliateId;
	//! String - the name of the affiliate (Eg. John Smith)
	var $mName;
	//! String - their email address (Eg. john@smith.com)
	var $mEmail;
	//! String - their website (Eg. www.johnsmiths.com)
	var $mUrl;
	//! String - their contact number
	var $mTelephone;
	//! Obj:AddressModel - their address
	var $mAddress;
	//! String - password of affiliate
	var $mPassword;
	//! Date of last claim
	var $mLastClaim;
	//! Total amount claimed
	var $mAmountClaimed;
	//! Obj:PDO - database connection used to access database level
	var $mDatabase;
	
	//! Constructor, initialises the affiliate
	function __construct($identifier, $method = 'id') {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
		switch ($method) {
			case 'id' :
				$check_sql = 'SELECT COUNT(Affiliate_ID) FROM tblAffiliate WHERE Affiliate_ID = ' . $identifier;
				break;
			case 'email' :
				$check_sql = 'SELECT COUNT(Affiliate_ID) FROM tblAffiliate WHERE Email = \'' . $identifier . '\'';
				break;
		}
		if (! $result = $this->mDatabase->query ( $check_sql )) {
			$error = new Error ( 'Could not construct affiliate.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		if ($result->fetchColumn () > 0) {
			if ($method == 'id') {
				$this->mAffiliateId = $identifier;
			} else {
				$this->mEmail = $identifier;
				$this->mAffiliateId = $this->GetAffiliateIdFromEmail ();
			}
		} else {
			$error = new Error ( 'Could not initialise affiliate ' . $identifier . ' because it does not exist in the database.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	//! Returns the password of the affiliate
	/*!
	 * @return String
	 */
	function GetPassword() {
		if (! isset ( $this->mPassword )) {
			$sql = 'SELECT Password FROM tblAffiliate WHERE Affiliate_ID = ' . $this->mAffiliateId;
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
	
	//! Set the password for this affiliate
	/*!
	 * @param [in] newPassword : String - the new password
	 * @return Boolean : true if successful
	 */
	function SetPassword($newPassword) {
		$sql = 'UPDATE tblAffiliate SET Password = \'' . $newPassword . '\' WHERE Affiliate_ID = ' . $this->mAffiliateId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the password of affiliate: ' . $this->mAffiliateId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mPassword = $newPassword;
		return true;
	}
	
	//! Gets the affiliate ID for the affiliate (assuming it has been initialised with the email)
	/*!
	 * @return Int - The affiliate ID
	 */
	function GetAffiliateIdFromEmail() {
		if (! isset ( $this->mAffiliateId )) {
			$sql = 'SELECT Affiliate_ID FROM tblAffiliate WHERE Email = \'' . $this->mEmail . '\'';
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mAffiliateId = $resultObj->Affiliate_ID;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mAffiliateId;
	}
	
	//! Returns the name of the affiliate
	/*!
	 * @return String
	 */
	function GetName() {
		if (! isset ( $this->mName )) {
			$sql = 'SELECT Name FROM tblAffiliate WHERE Affiliate_ID = ' . $this->mAffiliateId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mName = $resultObj->Name;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mName;
	}
	
	//! Set the name of this affiliate
	/*!
	 * @param [in] newName : String - the new name
	 * @return Boolean : true if successful
	 */
	function SetName($newName) {
		$sql = 'UPDATE tblAffiliate SET Name = \'' . $newName . '\' WHERE Affiliate_ID = ' . $this->mAffiliateId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the name for affiliate: ' . $this->mAffiliateId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mName = $newName;
		return true;
	}
	
	//! Returns the email address of the affiliate
	/*!
	 * @return String
	 */
	function GetEmail() {
		if (! isset ( $this->mEmail )) {
			$sql = 'SELECT Email FROM tblAffiliate WHERE Affiliate_ID = ' . $this->mAffiliateId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mEmail = $resultObj->Email;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mEmail;
	}
	
	//! Set the email of this affiliate
	/*!
	 * @param [in] newEmail : String - the new email
	 * @return Boolean : true if successful
	 */
	function SetEmail($newEmail) {
		$sql = 'UPDATE tblAffiliate SET Email = \'' . $newEmail . '\' WHERE Affiliate_ID = ' . $this->mAffiliateId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the email for affiliate: ' . $this->mAffiliateId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mEmail = $mName;
		return true;
	}
	
	//! Returns the URL of the affiliate
	/*!
	 * @return String
	 */
	function GetUrl() {
		if (! isset ( $this->mUrl )) {
			$sql = 'SELECT Url FROM tblAffiliate WHERE Affiliate_ID = ' . $this->mAffiliateId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mUrl = $resultObj->Url;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mUrl;
	}
	
	//! Set the URL of this affiliate
	/*!
	 * @param [in] newUrl : String - the new URL
	 * @return Boolean : true if successful
	 */
	function SetUrl($newUrl) {
		$sql = 'UPDATE tblAffiliate SET Url = \'' . $newUrl . '\' WHERE Affiliate_ID = ' . $this->mAffiliateId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the URL for affiliate: ' . $this->mAffiliateId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mUrl = $newUrl;
		return true;
	}
	
	//! Returns the telephone number
	/*!
	 * @return String
	 */
	function GetTelephone() {
		if (! isset ( $this->mTelephone )) {
			$sql = 'SELECT Telephone FROM tblAffiliate WHERE Affiliate_ID = ' . $this->mAffiliateId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mTelephone = $resultObj->Telephone;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mTelephone;
	}
	
	//! Set the telephone number of this affiliate
	/*!
	 * @param [in] newTelephone : String - the new telephone number
	 * @return Boolean : true if successful
	 */
	function SetTelephone($newTelephone) {
		$sql = 'UPDATE tblAffiliate SET Telephone = \'' . $newTelephone . '\' WHERE Affiliate_ID = ' . $this->mAffiliateId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the Telephone for affiliate: ' . $this->mAffiliateId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mTelephone = $newTelephone;
		return true;
	}
	
	//! Get the last claim date (as a timestamp) for the affiliate
	/*!
	 * @return Int : The timestamp of the last claim
	 */
	function GetLastClaim() {
		if (! isset ( $this->mLastClaim )) {
			$sql = 'SELECT Last_Claim FROM tblAffiliate WHERE Affiliate_ID = ' . $this->mAffiliateId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mLastClaim = $resultObj->Last_Claim;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mLastClaim;
	}
	
	//! Set the last claim date (as a timestamp) for the affiliate
	/*!
	 * @param $newClaim Int : The timestamp of the newest claim
	 * @return Boolean - True if successful / Exception
	 */
	function SetLastClaim($newClaim) {
		$sql = 'UPDATE tblAffiliate SET Last_Claim = \'' . $newClaim . '\' WHERE Affiliate_ID = ' . $this->mAffiliateId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the Last_Claim for affiliate: ' . $this->mAffiliateId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mLastClaim = $mLastClaim;
		return true;
	}
	
	//! Get the amount claimed by the affiliate
	/*!
	 * @return Decimal - The amount claimed by the affiliate
	 */
	function GetClaimedAmount() {
		if (! isset ( $this->mClaimedAmount )) {
			$sql = 'SELECT Claimed_Amount FROM tblAffiliate WHERE Affiliate_ID = ' . $this->mAffiliateId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mClaimedAmount = $resultObj->Claimed_Amount;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mClaimedAmount;
	}
	
	//! Set the amount claimed by the affiliate
	/*!
	 * @param $newAmount : Decimal : The new amount claimed by the affiliate
	 * @return Boolean - True if successful / Exception
	 */
	function SetClaimedAmount($newAmount) {
		$sql = 'UPDATE tblAffiliate SET Claimed_Amount = \'' . $newAmount . '\' WHERE Affiliate_ID = ' . $this->mAffiliateId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the Claimed_Amount for affiliate: ' . $this->mAffiliateId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mClaimedAmount = $mLastClaim;
		return true;
	}
	
	//! Returns the address of the affiliate
	/*!
	 * @return String
	 */
	function GetAddress() {
		if (! isset ( $this->mAddress )) {
			$sql = 'SELECT Address_ID FROM tblAffiliate WHERE Affiliate_ID = ' . $this->mAffiliateId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mAddress = new AddressModel ( $resultObj->Address_ID );
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mAddress;
	}
	
	//! Set the address of this affiliate
	/*!
	 * @param [in] newAddress : Obj:AddressModel - the new address
	 * @return Boolean : true if successful
	 */
	function SetAddress($newAddress) {
		$sql = 'UPDATE tblAffiliate SET Address_ID = \'' . $newAddress->GetAddressId () . '\' WHERE Affiliate_ID = ' . $this->mAffiliateId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the address for affiliate: ' . $this->mAffiliateId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mAddress = $newAddress;
		return true;
	}
	
	//! Return affiliate identifier
	function GetAffiliateId() {
		return $this->mAffiliateId;
	}

}

?>