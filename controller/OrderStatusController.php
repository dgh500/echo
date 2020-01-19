<?php

class OrderStatusController {
	
	//! Gets the default order status (Looks for "Awaiting Authorisation" - if not found will throw an exception)
	function GetDefault() {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$sql = 'SELECT Status_ID FROM tblOrder_Status WHERE Description LIKE \'Awaiting Authorisation\'';
		if ($result = $database->query ( $sql )) {
			$resultObj = $result->fetchObject ();
			return new OrderStatusModel ( $resultObj->Status_ID );
		} else {
			$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	function GetInTransit() {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$sql = 'SELECT Status_ID FROM tblOrder_Status WHERE Description LIKE \'In Transit\'';
		if ($result = $database->query ( $sql )) {
			$resultObj = $result->fetchObject ();
			return new OrderStatusModel ( $resultObj->Status_ID );
		} else {
			$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	function GetFailed() {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$sql = 'SELECT Status_ID FROM tblOrder_Status WHERE Description LIKE \'Failed\'';
		if ($result = $database->query ( $sql )) {
			$resultObj = $result->fetchObject ();
			return new OrderStatusModel ( $resultObj->Status_ID );
		} else {
			$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	function GetAuthorised() {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$sql = 'SELECT Status_ID FROM tblOrder_Status WHERE Description LIKE \'Authorised\'';
		if ($result = $database->query ( $sql )) {
			$resultObj = $result->fetchObject ();
			return new OrderStatusModel ( $resultObj->Status_ID );
		} else {
			$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	function GetCancelledByMerchant() {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$sql = 'SELECT Status_ID FROM tblOrder_Status WHERE Description LIKE \'Cancelled By Merchant\'';
		if ($result = $database->query ( $sql )) {
			$resultObj = $result->fetchObject ();
			return new OrderStatusModel ( $resultObj->Status_ID );
		} else {
			$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	function GetCancelledByUser() {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$sql = 'SELECT Status_ID FROM tblOrder_Status WHERE Description LIKE \'Cancelled By User\'';
		if ($result = $database->query ( $sql )) {
			$resultObj = $result->fetchObject ();
			return new OrderStatusModel ( $resultObj->Status_ID );
		} else {
			$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	function GetInTransitCharged() {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$sql = 'SELECT Status_ID FROM tblOrder_Status WHERE Description LIKE \'In Transit - Charged\'';
		if ($result = $database->query ( $sql )) {
			$resultObj = $result->fetchObject ();
			return new OrderStatusModel ( $resultObj->Status_ID );
		} else {
			$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	function GetAll() {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$sql = 'SELECT Status_ID FROM tblOrder_Status ORDER BY Description ASC';
		if ($result = $database->query ( $sql )) {
			$statuses = $result->fetchAll ( PDO::FETCH_OBJ );
			foreach ( $statuses as $status ) {
				$newStatus = new OrderStatusModel ( $status->Status_ID );
				$statusesArr [] = $newStatus;
			}
			if (0 == count ( $statuses )) {
				$statusesArr = array ();
			}
			return $statusesArr;
		} else {
			$error = new Error ( 'Could not run query: ' . $sql . ' and get a result.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	} // End GetAll


}

?>