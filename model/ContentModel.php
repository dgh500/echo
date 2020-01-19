<?php

//! Models a piece of content on the website - Eg. about us, articles etc.
class ContentModel {

	//! Unique content ID
	var $mContentId;
	//! The title of the content
	var $mDisplayName;
	//! The content text
	var $mLongText;
	//! The thumb image
	var $mThumbImage;
	//! The header image
	var $mHeaderImage;

	//! Constructor initialises the instance with the unique content ID
	/*!
	 * @param $contentId - Int - The unique content ID
	 * @return Void
	 */
	function __construct($contentId) {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
		$sql = 'SELECT COUNT(Content_ID) AS ContentCount FROM tblContent WHERE Content_ID = ' . $contentId;
		$result = $this->mDatabase->query ( $sql );
		$resultObj = $result->fetch ( PDO::FETCH_OBJ );
		if ($resultObj->ContentCount > 0) {
			$this->mContentId = $contentId;
		} else {
			$error = new Error ( 'Could not construct content: ' . $contentId . '.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

	//! Returns the unique content ID
	/*!
	 * @return Int - The unique content ID
	 */
	function GetContentId() {
		return $this->mContentId;
	}

	//! Gets the title of the content
	/*!
	 * @return String - The title of the content
	 */
	function GetDisplayName() {
		if (! isset ( $this->mDisplayName )) {
			$sql = 'SELECT Display_Name FROM tblContent WHERE Content_ID = ' . $this->mContentId;
			$result = $this->mDatabase->query ( $sql );
			if ($result) {
				$resultObj = $result->fetch ( PDO::FETCH_OBJ );
				$this->mDisplayName = $resultObj->Display_Name;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . '.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mDisplayName;
	}

	//! Sets the display name
	/*
	 * @param $newDisplayName - Str - The new display name
	 * @return Bool - True on success
	 */
	function SetDisplayName($newDisplayName) {
		$sql = 'UPDATE tblContent SET Display_Name = \'' . $newDisplayName . '\' WHERE Content_ID = ' . $this->mContentId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the display name for content: ' . $this->mContentId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDisplayName = $newDisplayName;
		return true;
	}

	//! Gets the description of the content
	/*!
	 * @return String - The description of the content
	 */
	function GetDescription() {
		if (! isset ( $this->mDescription )) {
			$sql = 'SELECT Description FROM tblContent WHERE Content_ID = ' . $this->mContentId;
			$result = $this->mDatabase->query ( $sql );
			if ($result) {
				$resultObj = $result->fetch ( PDO::FETCH_OBJ );
				$this->mDescription = $resultObj->Description;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . '.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mDescription;
	}

	//! Sets the description
	/*
	 * @param $newDescription - Str - The new description
	 * @return Bool - True on success
	 */
	function SetDescription($newDescription) {
		$sql = 'UPDATE tblContent SET Description = \'' . $newDescription . '\' WHERE Content_ID = ' . $this->mContentId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the description for content: ' . $this->mContentId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDescription = $newDescription;
		return true;
	}

	//! Gets the long text of the content
	/*!
	 * @return String - The full content of the article
	 * This would be better if it all came from the database - at the minute it is just looking for files
	 */
	function GetLongText() {
	/*	$registry = Registry::getInstance ();
		$filename = $registry->rootDir . '/content/content' . $this->mContentId . '.php';
		$contents = @file_get_contents ( $filename );
		return $contents;*/

		if (! isset ( $this->mLongText )) {
			$sql = 'SELECT Long_Text FROM tblContent WHERE Content_ID = ' . $this->mContentId;
			$result = $this->mDatabase->query ( $sql );
			if ($result) {
				$resultObj = $result->fetch ( PDO::FETCH_OBJ );
				$this->mLongText = $resultObj->Long_Text;
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . '.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mLongText;
	}

	//! Sets the full content text
	/*!
	 * @param $newLongText - String
	 * @return Boolean - True on success
	 */
	function SetLongText($newLongText) {
/*		$filename = '../content/content' . $this->mContentId . '.php';
		$fh = fopen ( $filename, 'w+' );
		fwrite ( $fh, $newLongText );
		fclose ( $fh );
		$this->mLongText = $newLongText;*/
		$sql = 'UPDATE tblContent SET Long_Text = \'' . mysql_escape_string($newLongText) . '\' WHERE Content_ID = ' . $this->mContentId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the long text for content: ' . $this->mContentId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mLongText = $newLongText;
		return true;
		return true;
	}

	//! Returns the content Type - Eg. Dive/Manufacturer etc.
	/*!
	 * @return Obj : ContentStatusModel - The type of content it is
	 */
	function GetContentType() {
		if (! isset ( $this->mContentType )) {
			$sql = 'SELECT Content_Type FROM tblContent WHERE Content_ID = ' . $this->mContentId;
			$result = $this->mDatabase->query ( $sql );
			if ($result) {
				$resultObj = $result->fetch ( PDO::FETCH_OBJ );
				if (! is_null ( $resultObj->Content_Type )) {
					$this->mContentType = new ContentStatusModel ( $resultObj->Content_Type );
				} else {
					return NULL;
				}
			} else {
				$error = new Error ( 'Could not run query: ' . $sql . '.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		}
		return $this->mContentType;
	}

	//! Sets the content type
	/*!
	 * @param $newType - Obj : ContentStatusModel - The type of content to change it to
	 */
	function SetContentType($newType) {
		$sql = 'UPDATE tblContent SET Content_Type = \'' . $newType->GetContentStatusId () . '\' WHERE Content_ID = ' . $this->mContentId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the content type for content: ' . $this->mContentId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mContentType = $newType;
		return true;
	}

	//! Returns the Image associated with this content
	/*!
	* @return Obj:ImageModel : The image associated with the content
	*/
	function GetThumbImage() {
		if(!isset($this->mThumbImage)) {
			$sql = 'SELECT Thumb_Image_ID FROM tblContent WHERE Content_ID = '.$this->mContentId.' LIMIT 1';
			if (!$result = $this->mDatabase->query($sql)) {
				$error = new Error('Could not fetch the image ID for content '.$this->mContentId);
				$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
				throw new Exception($error->GetErrorMsg());
			}
			$resultObj = $result->fetch(PDO::FETCH_OBJ);
			try {
				$newImage = new ImageModel($resultObj->Thumb_Image_ID);
				$this->mThumbImage = $newImage;
			} catch ( Exception $e ) {
				return false;
			}
		}
		return $this->mThumbImage;
	}

	//! Sets the image of the content
	/*!
	* @param [in] newImage Obj:ImageModel : The new image
	* @return Bool : true if successful
	*/
	function SetThumbImage($newImage) {
		$sql = 'UPDATE tblContent SET Thumb_Image_ID = \''.$newImage->GetImageId().'\' WHERE Content_ID = '.$this->mContentId;
		if (! $this->mDatabase->query($sql)) {
			$error = new Error ( 'Could not update the image for content ' . $this->mContentId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mThumbImage = $newImage;
		return true;
	}

	//! Returns the Image associated with this content
	/*!
	* @return Obj:ImageModel : The image associated with the content
	*/
	function GetHeaderImage() {
		if(!isset($this->mHeaderImage)) {
			$sql = 'SELECT Header_Image_ID FROM tblContent WHERE Content_ID = '.$this->mContentId.' LIMIT 1';
			if (!$result = $this->mDatabase->query($sql)) {
				$error = new Error('Could not fetch the image ID for content '.$this->mContentId);
				$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
				throw new Exception($error->GetErrorMsg());
			}
			$resultObj = $result->fetch(PDO::FETCH_OBJ);
			try {
				$newImage = new ImageModel($resultObj->Header_Image_ID);
				$this->mHeaderImage = $newImage;
			} catch ( Exception $e ) {
				return false;
			}
		}
		return $this->mHeaderImage;
	}

	//! Sets the image of the content
	/*!
	* @param [in] newImage Obj:ImageModel : The new image
	* @return Bool : true if successful
	*/
	function SetHeaderImage($newImage) {
		$set_image_sql = 'UPDATE tblContent SET Header_Image_ID = \'' . $newImage->GetImageId () . '\' WHERE Content_ID = ' . $this->mContentId;
		if (! $this->mDatabase->query ( $set_image_sql )) {
			$error = new Error ( 'Could not update the image for content ' . $this->mContentId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mHeaderImage = $newImage;
		return true;
	}


} // End ContentModel


?>