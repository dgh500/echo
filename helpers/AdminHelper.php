<?php

class AdminHelper {

	//! The current session ID
	var $mSessionId;

	function __construct() {
		if (isset ( $_GET ['s'] )) {
			session_id ( $_GET ['s'] );
		}
		@session_start ();
		$this->mSessionId = session_id ();
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
	}

	function SetLoggedIn($value) {
		$_SESSION ['loggedIn'] = $value;
	}

	function FirstLoad() {
		if (isset ( $_SESSION ['loggedIn'] )) {
			return false;
		} else {
			return true;
		}
	}

	function LoginCheck() {
		if (isset ( $_SESSION ['loggedIn'] ) && $_SESSION ['loggedIn']) {
			return true;
		} else {
			return false;
		}
	}

	// Increments the amount of attempts to login from an IP address, sets to 1 if none exists
	function IncAttempts($ip) {
		// If no attempts so far then INSERT, otherwise UPDATE
		if($this->NumberOfAttempts($ip) == 0) {
			$sql = 'INSERT INTO tblloginattempts (`IP`,`Attempts`,`LastLogin`) VALUES (\''.$ip.'\',\'1\',\''.time().'\')';
		} else {
			$attempts = $this->NumberOfAttempts($ip) + 1;
			$sql = 'UPDATE tblloginattempts SET Attempts = '.$attempts.' WHERE IP = \''.$ip.'\'';
		}
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update attempts.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	} // End IncAttempts

	//! Returns the number of (failed) attempts a user has made to log in
	function NumberOfAttempts($ip) {
		$sql = 'SELECT Attempts AS AttemptCount FROM tblloginattempts WHERE IP = \''.$ip.'\'';
		$result = $this->mDatabase->query($sql);
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		return $resultObj->AttemptCount;
	} // End NumberOfAttempts

}

?>