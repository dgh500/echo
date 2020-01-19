<?php

//! Models a single gallery (GalleryView jQuery plugin)
class GalleryModel {
	
	//! Int - The unique gallery ID
	var $mGalleryId;
	//! Int - The panel width(of the large image)
	var $mPanelWidth;
	//! Int - The panel height(of the large image)
	var $mPanelHeight;
	//! Int - The frame width(of the thumbnail image)
	var $mFrameWidth;
	//! Int - The frame height(of the thumbnail image)
	var $mFrameHeight;
	//! Int - The time taken for an image to change, in milliseconds
	var $mTransitionSpeed;
	//! Int - The time between image changes, in milliseconds
	var $mTransitionInterval;
	//! String - either 'dark' or 'light' controls the colour of the next/prev images etc
	var $mNavTheme;
	//! Array - Array of GalleryItemModel objects representing all the items in the gallery
	var $mGalleryItems;
	
	//! Constructor, initialises the gallery ID. Throws an exception if the gallery doesn't exist
	/*!
	 * @param $galleryId - The gallery ID
	 */
	function __construct($galleryId) {
		$this->mRegistry = Registry::getInstance();
		$this->mDatabase = $this->mRegistry->database;
		$sql = 'SELECT COUNT(Gallery_ID) AS GalleryCount FROM tblGallery WHERE Gallery_ID = '.$galleryId;
		$result = $this->mDatabase->query($sql);
		if($result) {
			$resultObj = $result->fetch(PDO::FETCH_OBJ);
			if($resultObj->GalleryCount > 0) {
				$this->mGalleryId = $galleryId;
			} else {
				$error = new Error('Could not initialise gallery '.$galleryId.' because it does not exist in the database.');
				$error->PdoErrorHelper($this->mDatabase->errorInfo(), __LINE__, __FILE__);
				throw new Exception($error->GetErrorMsg());
			}
		} else {
			$error = new Error('Could not initialise gallery '.$galleryId.' because it does not exist in the database.');
			$error->PdoErrorHelper($this->mDatabase->errorInfo(), __LINE__, __FILE__);
			throw new Exception($error->GetErrorMsg());
		}
	} // End __construct();
	
	//! Return the gallery ID if a string is needed
	function __toString() {
		return $this->mGalleryId;
	}

	//! Returns the unique gellery ID
	/*! 
	 * @return Int - The unique gallery ID
	 */
	function GetGalleryId() {
		return $this->mGalleryId;
	}

	//! Returns the display name for the gallery
	function GetDisplayName() {
		$sql = 'SELECT Display_Name FROM tblGallery WHERE Gallery_ID = '.$this->mGalleryId;
		$result = $this->mDatabase->query($sql);
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		return $resultObj->Display_Name;
	} // End GetDisplayName
	
	//! Returns the width for the panel property
	function GetPanelWidth() {
		$sql = 'SELECT Panel_Width FROM tblGallery WHERE Gallery_ID = '.$this->mGalleryId;
		$result = $this->mDatabase->query($sql);
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		return $resultObj->Panel_Width;
	} // End GetPanelWidth

	//! Returns the height for the panel property
	function GetPanelHeight() {
		$sql = 'SELECT Panel_Height FROM tblGallery WHERE Gallery_ID = '.$this->mGalleryId;
		$result = $this->mDatabase->query($sql);
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		return $resultObj->Panel_Height;
	} // End GetPanelHeight

	//! Returns the width for the frame property
	function GetFrameWidth() {
		$sql = 'SELECT Frame_Width FROM tblGallery WHERE Gallery_ID = '.$this->mGalleryId;
		$result = $this->mDatabase->query($sql);
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		return $resultObj->Frame_Width;
	} // End GetPanelWidth

	//! Returns the height for the frame property
	function GetFrameHeight() {
		$sql = 'SELECT Frame_Height FROM tblGallery WHERE Gallery_ID = '.$this->mGalleryId;
		$result = $this->mDatabase->query($sql);
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		return $resultObj->Frame_Height;
	} // End GetPanelWidth

	//! Returns the transition speed
	function GetTransitionSpeed() {
		$sql = 'SELECT Transition_Speed FROM tblGallery WHERE Gallery_ID = '.$this->mGalleryId;
		$result = $this->mDatabase->query($sql);
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		return $resultObj->Transition_Speed;
	} // End GetTransitionSpeed

	//! Returns the transition interval
	function GetTransitionInterval() {
		$sql = 'SELECT Transition_Interval FROM tblGallery WHERE Gallery_ID = '.$this->mGalleryId;
		$result = $this->mDatabase->query($sql);
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		return $resultObj->Transition_Interval;
	} // End GetTransitionInterval

	//! Returns the nav theme
	function GetNavTheme() {
		$sql = 'SELECT Nav_Theme FROM tblGallery WHERE Gallery_ID = '.$this->mGalleryId;
		$result = $this->mDatabase->query($sql);
		$resultObj = $result->fetch(PDO::FETCH_OBJ);
		return $resultObj->Nav_Theme;
	} // End GetNavTheme

	//! Returns the items in the gallery
	/*!
	 * @return Array of GalleryItemModel objects
	 */
	function GetGalleryItems() {
		if(!isset($this->mGalleryItems) || 0 == count($this->mGalleryItems)) {
			$sql = 'SELECT Gallery_Item_ID FROM tblGallery_Items WHERE Gallery_ID = '.$this->mGalleryId.' ORDER BY Gallery_Item_ID DESC';
			$result = $this->mDatabase->query($sql);
			$galleryItems = $result->fetchAll(PDO::FETCH_OBJ);
			// For each SKU, create a new instance of it and store it in the mSkus member variable
			foreach($galleryItems as $galleryItem) {
				$newGalleryItem = new GalleryItemModel($galleryItem->Gallery_Item_ID);
				$this->mGalleryItems[] = $newGalleryItem;
			}
			if(0 == count($galleryItems)) {
				$this->mGalleryItems = array();
			}
		}
		return $this->mGalleryItems;
	} // End GetGalleryItems

	//! Sets the display name
	/*!
	* @param [in] newDisplayName string : The new display name
	* @return Bool : true if successful
	*/
	function SetDisplayName($newDisplayName) {
		$sql = 'UPDATE tblGallery SET Display_Name = \''.$newDisplayName.'\' WHERE Gallery_ID = ' . $this->mGalleryId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the gallery display name for gallery  ' . $this->mGalleryId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
			return false;
		}
		$this->mDisplayName = $newDisplayName;
		return true;
	} // End SetDisplayName
	
	//! Sets the panel width
	function SetPanelWidth($newVal) {
		$sql = 'UPDATE tblGallery SET Panel_Width = \''.$newVal.'\' WHERE Gallery_ID = ' . $this->mGalleryId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the gallery panel width for gallery  ' . $this->mGalleryId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
			return false;
		}
		$this->mPanelWidth = $newVal;
		return true;
	} // End SetPanelWidth
	
	//! Sets the panel height
	function SetPanelHeight($newVal) {
		$sql = 'UPDATE tblGallery SET Panel_Height = \''.$newVal.'\' WHERE Gallery_ID = ' . $this->mGalleryId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the gallery panel height for gallery  ' . $this->mGalleryId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
			return false;
		}
		$this->mPanelHeight = $newVal;
		return true;
	} // End SetPanelHeight

	//! Sets the frame width
	function SetFrameWidth($newVal) {
		$sql = 'UPDATE tblGallery SET Frame_Width = \''.$newVal.'\' WHERE Gallery_ID = ' . $this->mGalleryId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the gallery frame width for gallery  ' . $this->mGalleryId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
			return false;
		}
		$this->mFrameWidth = $newVal;
		return true;
	} // End SetFrameWidth
	
	//! Sets the frame height
	function SetFrameHeight($newVal) {
		$sql = 'UPDATE tblGallery SET Frame_Height = \''.$newVal.'\' WHERE Gallery_ID = ' . $this->mGalleryId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the gallery frame height for gallery  ' . $this->mGalleryId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
			return false;
		}
		$this->mFrameHeight = $newVal;
		return true;
	} // End SetFrameHeight
	
	//! Sets the transition speed
	function SetTransitionSpeed($newVal) {
		$sql = 'UPDATE tblGallery SET Transition_Speed = \''.$newVal.'\' WHERE Gallery_ID = ' . $this->mGalleryId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the gallery transition speed for gallery  ' . $this->mGalleryId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
			return false;
		}
		$this->mTransitionSpeed = $newVal;
		return true;
	} // End SetTransitionSpeed	
	
	//! Sets the transition interval
	function SetTransitionInterval($newVal) {
		$sql = 'UPDATE tblGallery SET Transition_Interval = \''.$newVal.'\' WHERE Gallery_ID = ' . $this->mGalleryId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the gallery transition interval for gallery  ' . $this->mGalleryId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
			return false;
		}
		$this->mTransitionInterval = $newVal;
		return true;
	} // End SetTransitionInterval	
	
	//! Sets the nav theme
	function SetNavTheme($newVal) {
		$sql = 'UPDATE tblGallery SET Nav_Theme = \''.$newVal.'\' WHERE Gallery_ID = ' . $this->mGalleryId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the gallery nav theme for gallery  ' . $this->mGalleryId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
			return false;
		}
		$this->mNavTheme = $newVal;
		return true;
	} // End SetNavTheme	

} // End GalleryModel

?>