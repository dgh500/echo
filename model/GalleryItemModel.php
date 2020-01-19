<?php 

//! Models a single gallery item
class GalleryItemModel {

	//! Int - The unique gallery item ID
	var $mGalleryItemId;
	//! Int - The gallery that the item is a member of 
	var $mGallery;
	//! Int - The image for this gallery item
	var $mImage;
	//! Str - The caption text for this item
	var $mCaptionText;

	//! Constructor, initialises the gallery item ID. Throws an exception if the gallery item doesn't exist
	/*!
	 * @param $galleryItemId - The ID for the gallery item
	 */
	function __construct($galleryItemId) {
		$this->mRegistry = Registry::getInstance();
		$this->mDatabase = $this->mRegistry->database;
		$sql = 'SELECT COUNT(Gallery_Item_ID) AS GalleryItemCount FROM tblGallery_Items WHERE Gallery_Item_ID = '.$galleryItemId;
		$result = $this->mDatabase->query($sql);
		if($result) {
			$resultObj = $result->fetch(PDO::FETCH_OBJ);
			if($resultObj->GalleryItemCount > 0) {
				$this->mGalleryItemId = $galleryItemId;
			} else {
				$error = new Error('Could not initialise gallery item '.$galleryItemId.' because it does not exist in the database.');
				$error->PdoErrorHelper($this->mDatabase->errorInfo(), __LINE__, __FILE__);
				throw new Exception($error->GetErrorMsg());
			}
		} else {
			$error = new Error('Could not initialise gallery item'.$galleryItemId.' because it does not exist in the database.');
			$error->PdoErrorHelper($this->mDatabase->errorInfo(), __LINE__, __FILE__);
			throw new Exception($error->GetErrorMsg());
		}
	} // End __construct();

	//! Return the gallery item ID if a string is needed
	function __toString() {
		return $this->mGalleryItemId;
	} // End __toString

	//! Returns the unique gellery item ID
	/*! 
	 * @return Int - The unique gallery item ID
	 */
	function GetGalleryItemId() {
		return $this->mGalleryItemId;
	} // End GetGalleryItemId
	
	//! Returns the gallery that the item belongs to
	function GetGallery() {
		$sql = 'SELECT Gallery_ID FROM tblGallery_Items WHERE Gallery_Item_ID = '.$this->mGalleryItemId;
		$result = $this->mDatabase->query($sql);
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		return new GalleryModel($resultObj->Gallery_ID);
	} // End GetGalleryId

	//! Returns the image for this item
	function GetImage() {
		$sql = 'SELECT Image_ID FROM tblGallery_Items WHERE Gallery_Item_ID = '.$this->mGalleryItemId;
		$result = $this->mDatabase->query($sql);
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		return new ImageModel($resultObj->Image_ID);
	} // End GetImage

	//! Returns the caption text for this item
	function GetCaptionText() {
		$sql = 'SELECT Caption_Text FROM tblGallery_Items WHERE Gallery_Item_ID = '.$this->mGalleryItemId;
		$result = $this->mDatabase->query($sql);
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		return $resultObj->Caption_Text;
	} // End GetCaptionText

	//! Sets the image of the gallery item
	/*!
	* @param [in] newImage Obj:ImageModel : The new image
	* @return Bool : true if successful
	*/
	function SetImage($newImage) {
		$sql = 'UPDATE tblGallery_Items SET Image_ID = \''.$newImage->GetImageId().'\' WHERE Gallery_Item_ID = ' . $this->mGalleryItemId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the gallery item image for gallery item '.$this->mGalleryItemId);
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mImage = $newImage;
		return true;
	} // End SetImage

	//! Sets the gallery of the gallery item (the gallery that the item is in)
	/*!
	* @param [in] newGallery Obj: GalleryModel : The new gallery
	* @return Bool : true if successful
	*/
	function SetGallery($newGallery) {
		$sql = 'UPDATE tblGallery_Items SET Gallery_ID = \''.$newGallery->GetGalleryId().'\' WHERE Gallery_Item_ID = ' . $this->mGalleryItemId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the gallery for gallery item '.$this->mGalleryItemId);
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mGallery = $newGallery;
		return true;
	} // End SetGallery

	//! Sets the caption text
	/*!
	* @param [in] newCaptionText string : The new caption text
	* @return Bool : true if successful
	*/
	function SetCaptionText($newCaptionText) {
		$sql = 'UPDATE tblGallery_Items SET Caption_Text = \''.$newCaptionText.'\' WHERE Gallery_Item_ID = ' . $this->mGalleryItemId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the gallery caption text for gallery item ' . $this->mGalleryItemId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
			return false;
		}
		$this->mCaptionText = $newCaptionText;
		return true;
	} // End SetCaptionText

} // End GalleryItemModel

?>