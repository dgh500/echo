<?php

//! Deals with referrer (create, delete etc)
class ReferrerController {
	
	//! Gets all referrers in the database and returns them in an array
	/*!
	 * @return Array of Obj:ReferrerModel
	 */
	function GetAllReferrers() {
		$registry = Registry::getInstance ();
		$database = $registry->database;
		$get_all_sql = 'SELECT Referrer_ID FROM tblReferrer ORDER BY Description ASC';
		if (! $result = $database->query ( $get_all_sql )) {
			$error = new Error ( 'Could not fetch all referrers.' );
			$error->PdoErrorHelper ( $database->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$referrers = $result->fetchAll ( PDO::FETCH_OBJ );
		foreach ( $referrers as $referrer ) {
			$newReferrer = new ReferrerModel ( $referrer->Referrer_ID );
			$retReferrers [] = $newReferrer;
		}
		if (0 == count ( $retReferrers )) {
			$retReferrers = array ();
		}
		return $retReferrers;
	}

}

?>