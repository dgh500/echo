<?php

class GalleryController {

	//! Constructor, initialises the database connection
	function __construct() {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
	}

	//! Creates a gallery
	/*!
	 * @return Obj : GalleryModel	 
	 */
	function CreateGallery($displayName='New Gallery') {
		$sql = 'INSERT INTO tblGallery 
					(`Display_Name`) 
				VALUES 
					(\''.$displayName.'\')';
		// Can use PDO::excc here because if it affects 0 rows then a failure HAS happened
		if ($this->mDatabase->query ( $sql )) {
			$sql = 'SELECT Gallery_ID FROM tblGallery ORDER BY Gallery_ID DESC LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not select new gallery' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			// Use the newest gallery as the current one
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$newGallery = new GalleryModel ( $resultObj->Gallery_ID );
		} else {
			$error = new Error ( 'Could not insert gallery' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		return $newGallery;
	} // End CreateGallery
	
	//! Gets all galleries in the database and returns them in an array
	/*!
	 * @return Array of Obj:CatalogueModel
	 */
	function GetAllGalleries() {
		$sql = 'SELECT Gallery_ID FROM tblGallery ORDER BY Gallery_ID ASC';
		if (! $result = $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not fetch all galleries.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$resultSet = $result->fetchAll ( PDO::FETCH_OBJ );
		foreach($resultSet as $resultObj) {
			$newGallery = new GalleryModel($resultObj->Gallery_ID);
			$retArr [] = $newGallery;
		}
		if(0 == count($resultSet)) {
			$retArr = array();
		}
		return $retArr;
	} // End GetAllGalleries
	
	//! Attempts to delete a gallery from the database, throws an exception if this fails
	/*!
	 * @return true if successful
	 * @param [in] gallery : Obj: GalleryModel - the gallery to delete
	 */
	function DeleteGallery($gallery) {
		$sql = 'DELETE FROM tblGallery WHERE Gallery_ID = ' . $gallery->GetGalleryId ();
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not delete gallery ' . $gallery->GetGalleryId () );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->getErrorMsg () );
		} else {
			return true;
		}
	} // End DeleteGallery
	
} // End GalleryController

?>