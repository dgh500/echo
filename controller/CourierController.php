<?php

class CourierController {

	function GetAll() {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$sql = 'SELECT Courier_ID FROM tblCourier ORDER BY Courier_ID ASC';
		if ($result = $database->query ( $sql )) {
			$couriers = $result->fetchAll ( PDO::FETCH_OBJ );
			foreach ( $couriers as $courier ) {
				$newCourier = new CourierModel ( $courier->Courier_ID );
				$couriersArr [] = $newCourier;
			}
			if (0 == count ( $couriers )) {
				$couriersArr = array ();
			}
			return $couriersArr;
		} else {
			$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	} // End GetAll

	//! Returns an instance of the Interlink Express courier
	/*!
	 * @return CourierModel - The courier that corresponds to Interlink Express
	 */
	function GetInterlinkExpress() {
		return new CourierModel(2);
	} // End GetDPD

	//! Returns an instance of the Royal Mail courier
	/*!
	 * @return CourierModel - The courier that corresponds to Royal Mail
	 */
	function GetRoyalMail() {
		return new CourierModel(1);
	} // End GetRoyalMail

	//! Gets the default courier
	function GetDefault() {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$sql = 'SELECT Courier_ID FROM tblCourier LIMIT 1';
		if ($result = $database->query ( $sql )) {
			$resultObj = $result->fetchObject ();
			return new CourierModel ( $resultObj->Courier_ID );
		} else {
			$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	} // End GetDefault

} // End CourierController

?>