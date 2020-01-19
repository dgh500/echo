<?php

class ContentController {
	
	function __construct() {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
	}
	
	function GetAll() {
		$sql = 'SELECT Content_ID FROM tblContent ORDER BY Display_Name ASC';
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not fetch all content.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$contents = $result->fetchAll ( PDO::FETCH_OBJ );
		foreach ( $contents as $content ) {
			$newContent = new ContentModel ( $content->Content_ID );
			$retContent [] = $newContent;
		}
		if (0 == count ( $contents )) {
			$retContent = array ();
		}
		return $retContent;
	}
	
	function CreateContent() {
		$create_sql = 'INSERT INTO tblContent (`Display_Name`,`Long_Text`) VALUES (\'\',\'\')';
		if ($this->mDatabase->query ( $create_sql )) {
			$latest_sql = 'SELECT Content_ID FROM tblContent ORDER BY Content_ID DESC LIMIT 1';
			if (! $result = $this->mDatabase->query ( $latest_sql )) {
				$error = new Error ( 'Could not select new content.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$latest_content = $result->fetch ( PDO::FETCH_OBJ );
			$newContent = new ContentModel ( $latest_content->Content_ID );
			return $newContent;
		} else {
			$error = new Error ( 'Could not create new content.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	} // End CreateContent
	

	function DeleteContent($content) {
		$sql = 'DELETE FROM tblContent WHERE Content_ID = ' . $content->GetContentId ();
		if ($this->mDatabase->query ( $sql )) {
			return true;
		} else {
			$error = new Error ( 'Could not delete content.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	} // End DeleteContent()
	

	function IsAManufacturerPage($content) {
		$sql = 'SELECT COUNT(Size_Chart_ID) AS ContentCount FROM tblManufacturer WHERE Size_Chart_ID = ' . $content->GetContentId ();
		$result = $this->mDatabase->query ( $sql );
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		if ($resultObj->ContentCount == 0) {
			return false;
		} else {
			return true;
		}
	}
	
	//! Gets all the statuses that exist for content type
	function GetAllContentStatus() {
		$sql = 'SELECT Content_Status_ID FROM tblContent_Status ORDER BY Display_Name ASC';
		$result = $this->mDatabase->query ( $sql );
		$retArr = array ();
		while ( $resultObj = $result->fetch ( PDO::FETCH_OBJ ) ) {
			$retArr [] = new ContentStatusModel ( $resultObj->Content_Status_ID );
		}
		return $retArr;
	}
	
	function GetManufacturerFor($content) {
		$sql = 'SELECT Manufacturer_ID FROM tblManufacturer WHERE Size_Chart_ID = ' . $content->GetContentId ();
		$result = $this->mDatabase->query ( $sql );
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		return new ManufacturerModel ( $resultObj->Manufacturer_ID );
	}
	
	function GetContentForStatus($status) {
		$sql = 'SELECT Content_ID FROM tblContent WHERE Content_Type = ' . $status->GetContentStatusId () . ' ORDER BY Display_Name ASC';
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not fetch all content.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$resultObjs = $result->fetchAll ( PDO::FETCH_OBJ );
		$retArr = array ();
		foreach ( $resultObjs as $resultObj ) {
			$newContent = new ContentModel ( $resultObj->Content_ID );
			$retArr [] = $newContent;
		}
		return $retArr;
	}

}

?>