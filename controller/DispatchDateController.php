<?php

//! Deals with dispatch date tasks (create, delete etc)
class DispatchDateController {
	
	//! Creates a new dispatch date in the database then returns this date as an object of type DispatchDateModel
	/*!
	 * @return Obj:DispatchDateModel - the new dispatch date
	 */
	function CreateDispatchDate($displayName) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$create_dd_sql = 'INSERT INTO tblDispatch_Date (`Display_Name`) VALUES (\'' . $displayName . '\')';
		if ($database->query ( $create_dd_sql )) {
			$get_latest_dd_sql = 'SELECT Dispatch_Date_ID FROM tblDispatch_Date ORDER BY Dispatch_Date_ID DESC LIMIT 1';
			if (! $result = $database->query ( $get_latest_dd_sql )) {
				$error = new Error ( 'Could not select new dispatch date' );
				$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$latest_dd = $result->fetch ( PDO::FETCH_OBJ );
			$newDispatchDate = new DispatchDateModel ( $latest_dd->Dispatch_Date_ID );
			return $newDispatchDate;
		} else {
			$error = new Error ( 'Could not insert dispatch date' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	//! Attempts to delete a dispatch date from the database, throws an exception if this fails
	/*!
	 * @return true if successful
	 * @param [in] dispatchDate : Obj:DispatchDateModel - the dispatch date to delete
	 */
	function DeleteDispatchDate($dispatchDate) {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$delete_ss_sql = 'DELETE FROM tblDispatch_Date WHERE Dispatch_Date_ID = ' . $dispatchDate->GetDispatchDateId ();
		if (! $database->query ( $delete_ss_sql )) {
			$error = new Error ( 'Could not delete dispatch date ' . $dispatchDate->GetDispatchDateId () );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->getErrorMsg () );
		} else {
			return true;
		}
	}
	
	//! Gets all dispatch dates in the database and returns them in an array
	/*!
	 * @return Array of Obj:DispatchDateModel
	 */
	function GetAllDispatchDates() {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$get_all_dispatch_dates_sql = 'SELECT Dispatch_Date_ID FROM tblDispatch_Date ORDER BY Dispatch_Date_ID ASC';
		if (! $result = $database->query ( $get_all_dispatch_dates_sql )) {
			$error = new Error ( 'Could not fetch all dispatch dates.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$dispatchDates = $result->fetchAll ( PDO::FETCH_OBJ );
		foreach ( $dispatchDates as $dispatchDate ) {
			$newDispatchDate = new DispatchDateModel ( $dispatchDate->Dispatch_Date_ID );
			$retDispatchDates [] = $newDispatchDate;
		}
		if (0 == count ( $dispatchDates )) {
			$retDispatchDates = array ();
		}
		return $retDispatchDates;
	}
	
	//! Retrieves an arbitrary dispatch date, for when ANY will do
	/*!
	 * @return Obj:DispatchDateModel - the arbitrary dispatch date
	 */
	function GetDefaultDispatchDate() {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$get_dispatch_date_sql = 'SELECT Dispatch_Date_ID FROM tblDispatch_Date LIMIT 1';
		if (! $result = $database->query ( $get_dispatch_date_sql )) {
			$error = new Error ( 'Could not fetch dispatch date.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$dispatchDate = $result->fetch ( PDO::FETCH_OBJ );
		$newDispatchDate = new DispatchDateModel ( $dispatchDate->Dispatch_Date_ID );
		return $newDispatchDate;
	}

}

?>