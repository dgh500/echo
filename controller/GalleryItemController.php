<?php

class GalleryItemController {

	//! Constructor, initialises the database connection
	function __construct() {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
	} // End __construct()
	
	//! Creates a gallery item
	/*!
	 * @return Obj: GalleryItemModel - The new gallery item
	 */
	function CreateGalleryItem() {
		$sql = 'INSERT INTO tblGallery_Items 
					(`Caption_Text`) 
				VALUES 
					(\'New Caption Text\')';
		// Can use PDO::excc here because if it affects 0 rows then a failure HAS happened
		if ($this->mDatabase->query ( $sql )) {
			$sql = 'SELECT Gallery_Item_ID FROM tblGallery_Items ORDER BY Gallery_Item_ID DESC LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not select new gallery' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			// Use the newest gallery as the current one
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$newGalleryItem = new GalleryItemModel( $resultObj->Gallery_Item_ID );
		} else {
			$error = new Error ( 'Could not insert gallery item' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		return $newGalleryItem;
	} // End CreateGalleryItem
	
	//! Deletes the item supplied
	function DeleteGalleryItem($galleryItem) {
		$sql = 'DELETE FROM tblGallery_Items WHERE Gallery_Item_ID = '.$galleryItem->GetGalleryItemId();
		if (! $this->mDatabase->query($sql)) {
			$error = new Error ( 'Could not delete gallery item '.$galleryItem->GetGalleryItemId());
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->getErrorMsg () );
		} else {
			return true;
		}	
	}

} // End GalleryItemController

?>