<?php

//! Deals with tasks to control affiliates
class AffiliateController {
	
	//! Database connection
	var $mDatabase;
	
	//! Initialises database connection
	function __construct() {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
	}
	
	//! Create an affiliate
	/*!
	 * @return Obj:AffiliateModel - the new affiliate, throws exception otherwise
	 */
	function CreateAffiliate() {
		$sql = 'INSERT INTO tblAffiliate (`Name`,`Email`,`Url`,`Address_ID`,`Telephone`,`Password`) 
			VALUES (\'\',\'\',\'\',\'\',\'\',\'\')';
		if ($result = $this->mDatabase->query ( $sql )) {
			$get_latest_sql = 'SELECT Affiliate_ID FROM tblAffiliate ORDER BY Affiliate_ID DESC LIMIT 1';
			if ($result = $this->mDatabase->query ( $get_latest_sql )) {
				$resultObj = $result->fetchObject ();
				return new AffiliateModel ( $resultObj->Affiliate_ID );
			} else {
				$error = new Error ( 'Could not select the affiliate just created.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		} else {
			$error = new Error ( 'Could not create an affiliate.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	//! Log in an affiliate
	/*!
	 * @param [in] $affiliate : Obj:AffiliateModel
	 * @param [in] $password : Str
	 * @return Boolean - true/false depending whether the login was successful
	 */
	function Login($affiliate, $password) {
		$sql = 'SELECT COUNT(Affiliate_ID) AS AffiliateCount FROM tblAffiliate WHERE Email = \'' . $affiliate->GetEmail () . '\' AND Password = \'' . sha1 ( $password ) . '\'';
		$result = $this->mDatabase->query ( $sql );
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		if ($resultObj->AffiliateCount > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	//! Checks whether an affiliate already exists
	/*!
	 * @param $emailAddress [in] : String - The email address to check against
	 * @return Boolean - True if the affiliate does already exist
	 */
	function AffiliateAlreadyExists($emailAddress) {
		$sql = 'SELECT COUNT(Affiliate_ID) AS AffiliateCount FROM tblAffiliate WHERE Email = \'' . $emailAddress . '\'';
		$result = $this->mDatabase->query ( $sql );
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		if ($resultObj->AffiliateCount > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	//! Gets the order count for a given affiliate
	/*!
	 * @param $affiliate [in] : Obj:AffiliateModel - The affiliate to check
	 * @return Int - The number of orders that the affiliate is responsible for
	 */
	function OrderCount($affiliate) {
		$sql = 'SELECT Count(Order_ID) AS OrderCount FROM tblOrder WHERE Affiliate_ID = ' . $affiliate->GetAffiliateId () . ' AND Status_ID = 3';
		$result = $this->mDatabase->query ( $sql );
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		return $resultObj->OrderCount;
	}
	
	//! Gets the orders for a given affiliate for a given month
	/*!
	 * @param $affiliate [in] : Obj:AffiliateModel - The affiliate to get records for
	 * @param $endOfMonthTimeStamp [in] : Int - A Unix timestamp denoting the END of the month to check
	 */
	function OrdersByMonth($affiliate, $endOfMonthTimestamp) {
		$startOfMonthTimestamp = $endOfMonthTimestamp - 2629743;
		$sql = '
			SELECT Order_ID 
			FROM tblOrder 
			WHERE Affiliate_ID = ' . $affiliate->GetAffiliateId () . ' 
			AND Created_Date BETWEEN '.$startOfMonthTimestamp.' AND ' . $endOfMonthTimestamp . '
			AND Status_ID = 3';
		$result = $this->mDatabase->query ( $sql );
		$retOrders = array ();
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$retOrders [] = new OrderModel ( $resultObj->Order_ID );
		}
		return $retOrders;
	}
	
	function GetTotalSpend($affiliate) {
		$sql = 'SELECT SUM(tblOrder.Total_Price) AS TotalSpend FROM tblOrder WHERE Affiliate_ID = ' . $affiliate->GetAffiliateId () . ' AND Status_ID = 3';
		$result = $this->mDatabase->query ( $sql );
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		return $resultObj->TotalSpend;
	}
	
	function GetTotalSinceLastClaim($affiliate) {
		$sql = 'SELECT SUM(tblOrder.Total_Price) AS TotalSpend FROM tblOrder WHERE Affiliate_ID = ' . $affiliate->GetAffiliateId () . ' AND Created_Date > ' . $affiliate->GetLastClaim () . ' AND Status_ID = 3';
		$result = $this->mDatabase->query ( $sql );
		if ($result) {
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			return $resultObj->TotalSpend;
		} else {
			return NULL;
		}
	}
	
	function GetAll() {
		$sql = 'SELECT Affiliate_ID FROM tblAffiliate';
		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$retAffs [] = new AffiliateModel ( $resultObj->Affiliate_ID );
		}
		return $retAffs;
	}

}
?>