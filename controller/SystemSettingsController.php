<?php

class SystemSettingsController {
	
	function GetAll() {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
		$sql = 'SELECT * FROM tblSystem_Settings';
		$retSystemSettings = array ();
		$result = $this->mDatabase->query ( $sql );
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$catalogue = new CatalogueModel ( $resultObj->Catalogue_ID );
			$retSystemSettings [] = new SystemSettingsModel ( $catalogue );
		}
		return $retSystemSettings;
	}

}

?>