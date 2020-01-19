<?php

//! Models a particular postage method, Eg. Special Delivery
class PostageMethodModel {
	
	//! Int - Unique identifier for a postage method
	var $mPostageMethodId;
	//! String - a textual description of the method (Eg. International Signed For)
	var $mDisplayName;
	//! String - a short description of the method (Eg. Up to 7 Days)
	var $mDescription;
	//! String - a more in depth description of the method (Eg. Explaining working days etc.)
	var $mLongDescription;
	//! Obj:CourierModel - the courier that offers this method (Eg. Parcelforce offers 'Parcelforce 48' method)
	var $mCourier;
	//! Int - The maximum weight in grams that can be sent using this method
	var $mMaxWeight;
	//! Obj:PDO - database connection used to access database level
	var $mDatabase;
	
	//! Constructor, initialises the postage method
	function __construct($postageMethodId) {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
		$check_sql = 'SELECT COUNT(Postage_Method_ID) FROM tblPostage_Method WHERE Postage_Method_ID = ' . $postageMethodId;
		if (! $result = $this->mDatabase->query ( $check_sql )) {
			$error = new Error ( 'Could not construct postage method.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		if ($result->fetchColumn () > 0) {
			$this->mPostageMethodId = $postageMethodId;
		} else {
			$error = new Error ( 'Could not initialise postage method ' . $postageMethodId . ' because it does not exist in the database.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	//! Returns the display name of the method
	/*!
	 * @return String
	 */
	function GetDisplayName() {
		if (! isset ( $this->mDisplayName )) {
			$sql = 'SELECT Display_Name FROM tblPostage_Method WHERE Postage_Method_ID = ' . $this->mPostageMethodId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mDisplayName = $resultObj->Display_Name;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mDisplayName;
	}
	
	//! Set the display name of this method
	/*!
	 * @param [in] newDisplayName : String - the new display name
	 * @return Boolean : true if successful
	 */
	function SetDisplayName($newDisplayName) {
		$sql = 'UPDATE tblPostage_Method SET Display_Name = \'' . $newDisplayName . '\' WHERE Postage_Method_ID = ' . $this->mPostageMethodId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the display name for method: ' . $this->mPostageMethodId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDisplayName = $newDisplayName;
		return true;
	}
	
	//! Returns the description of the method
	/*!
	 * @return String
	 */
	function GetDescription() {
		if (! isset ( $this->mDescription )) {
			$sql = 'SELECT Description FROM tblPostage_Method WHERE Postage_Method_ID = ' . $this->mPostageMethodId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mDescription = $resultObj->Description;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mDescription;
	}
	
	//! Set the description of this courier
	/*!
	 * @param [in] newDescription : String - the new description
	 * @return Boolean : true if successful
	 */
	function SetDescription($newDescription) {
		$sql = 'UPDATE tblPostage_Method SET Description = \'' . $newDescription . '\' WHERE Postage_Method_ID = ' . $this->mPostageMethodId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the description for method: ' . $this->mPostageMethodId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDescription = $newDescription;
		return true;
	}
	
	//! Returns the long description of the method
	/*!
	 * @return String
	 */
	function GetLongDescription() {
		if (! isset ( $this->mLongDescription )) {
			$sql = 'SELECT Long_Description FROM tblPostage_Method WHERE Postage_Method_ID = ' . $this->mPostageMethodId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mLongDescription = $resultObj->Long_Description;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mLongDescription;
	}
	
	//! Set the long description of this courier
	/*!
	 * @param [in] newLongDescription : String - the new long description
	 * @return Boolean : true if successful
	 */
	function SetLongDescription($newLongDescription) {
		$sql = 'UPDATE tblPostage_Method SET Long_Description = \'' . $newLongDescription . '\' WHERE Postage_Method_ID = ' . $this->mPostageMethodId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the long description for method: ' . $this->mPostageMethodId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mLongDescription = $newLongDescription;
		return true;
	}
	
	//! Gets the courier (Eg. Royal Mail) of the method
	/*!
	 * @return Obj:CourierModel - the courier for the method
	 */
	function GetCourier() {
		if (! isset ( $this->mCourier )) {
			$sql = 'SELECT Courier_ID FROM tblPostage_Method WHERE Postage_Method_ID = ' . $this->mPostageMethodId;
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
	
	//! Sets the courier for the method
	/*!
	 * @param [in] newCourier : Obj:CourierModel - the new courier
	 * @return Boolean : true if successful
	 */
	function SetCourier($newCourier) {
		$sql = 'UPDATE tblPostage_Method SET Courier_ID = \'' . $newCourier->GetCourierId () . '\' WHERE Postage_Method_ID = ' . $this->mPostageMethodId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the courier for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mCourier = $newCourier;
		return true;
	}
	
	//! Gets the maximum weight in grams of the method
	/*!
	 * @return Int
	 */
	function GetMaxWeight() {
		if (! isset ( $this->mMaxWeight )) {
			$sql = 'SELECT Max_Weight FROM tblPostage_Method WHERE Postage_Method_ID = ' . $this->mPostageMethodId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mMaxWeight = $resultObj->Max_Weight;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mMaxWeight;
	}
	
	//! Gets the minimum weight in grams of the method
	/*!
	 * @return Int
	 */
	function GetMinWeight() {
		if (! isset ( $this->mMinWeight )) {
			$sql = 'SELECT Min_Weight FROM tblPostage_Method WHERE Postage_Method_ID = ' . $this->mPostageMethodId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mMinWeight = $resultObj->Min_Weight;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mMinWeight;
	}
	
	//! Sets the maximum weight for the method
	/*!
	 * @param [in] newMaxWeight : Int
	 * @return Boolean : true if successful
	 */
	function SetMaxWeight($newMaxWeight) {
		$sql = 'UPDATE tblPostage_Method SET Max_Weight = \'' . $newMaxWeight . '\' WHERE Postage_Method_ID = ' . $this->mPostageMethodId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the max weight for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mMaxWeight = $newMaxWeight;
		return true;
	}
	
	//! Gets the postage upgrade cost
	/*!
	 * @return Int
	 */
	function GetUpgradeCost() {
		if (! isset ( $this->mUpgradeCost )) {
			$sql = 'SELECT Upgrade_Price FROM tblPostage_Method WHERE Postage_Method_ID = ' . $this->mPostageMethodId;
			if ($result = $this->mDatabase->query ( $sql )) {
				$resultObj = $result->fetchObject ();
				$this->mUpgradeCost = $resultObj->Upgrade_Price;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mUpgradeCost;
	}
	
	//! Sets the postage upgrade cost
	/*!
	 * @param [in] newUpgradeCost : Decimal
	 * @return Boolean : true if successful
	 */
	function SetUpgradeCost($newUpgradeCost) {
		$sql = 'UPDATE tblPostage_Method SET Upgrade_Price = \'' . $newUpgradeCost . '\' WHERE Postage_Method_ID = ' . $this->mPostageMethodId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the max weight for order: ' . $this->mOrderId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mUpgradeCost = $newUpgradeCost;
		return true;
	}
	
	//! Return method identifier
	function GetPostageMethodId() {
		return $this->mPostageMethodId;
	}

}

?>