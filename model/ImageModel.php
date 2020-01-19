<?php

//! A single Image
class ImageModel {
	
	//! Int : Unique image ID
	var $mImageId;
	//! String(100) : Large file name
	var $mLargeFilename;
	//! String(100) : Medium file name
	var $mMediumFilename;
	//! String(100) : Small file name
	var $mSmallFilename;
	//! String(100) : Alternative text
	var $mAltText;
	//! Boolean : Whether the product is the main one for a product
	var $mMainImage;
	//! Database PDO conenction
	var $mDatabase;
	
	//! Constructor, initialises the Image ID if it exists, throws an exception if not
	function __construct($imageId) {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
		$does_this_image_exist_sql = 'SELECT COUNT(Image_ID) FROM tblImage WHERE Image_ID = ' . $imageId;
		if ($result = $this->mDatabase->query ( $does_this_image_exist_sql )) {
			if ($result->fetchColumn () > 0) {
				$this->mImageId = $imageId;
			} else {
				$error = new Error ( 'Could not initialise image ' . $imageId . ' because it does not exist in the database.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		} else {
			$error = new Error ( 'Could not initialise image ' . $imageId . ' because it does not exist in the database.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}
	
	//! Returns the alternative text
	/*!
	* @return String(100) : The alternative text for this image
	*/
	function GetAltText() {
		if (! isset ( $this->mAltText )) {
			$get_alt_text_sql = 'SELECT Alt_Text FROM tblImage WHERE Image_ID = ' . $this->mImageId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $get_alt_text_sql )) {
				$error = new Error ( 'Could not fetch the alternative text from the database for image ' . $this->mImageId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$alt_text = $result->fetch ( PDO::FETCH_OBJ );
			$this->mAltText = $alt_text->Alt_Text;
		}
		return trim($this->mAltText);
	}
	
	//! Sets the Alternative text of the image in both the instance and the database
	/*!
	* @param [in] newAltText : String(100) : The new alternative text
	* @return Bool : true if successful
	*/
	function SetAltText($newAltText) {
		$this->mAltText = $newAltText;
		$set_alt_text_sql = 'UPDATE tblImage SET Alt_Text = \'' . $this->mAltText . '\' WHERE Image_ID = ' . $this->mImageId;
		if (! $this->mDatabase->query ( $set_alt_text_sql )) {
			$error = new Error ( 'Could not update the alternative text in the database for image ' . $this->mImageId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		return true;
	}
	
	//! Given the directory to look in for images, returns the original height of the image
	function GetOriginalHeight($imageDir) {
		$imageSize = @getimagesize ( $imageDir . $this->GetFilename () );
		$height = $imageSize [1];
		return $height;
	}
	
	//! Given the directory to look in for images, returns the original width of the image
	function GetOriginalWidth($imageDir) {
		$imageSize = @getimagesize ( $imageDir . $this->GetFilename () );
		$height = $imageSize [0];
		return $height;
	}
	
	//! Returns the Image ID (Set in constructor)
	/*!
	* @return Int : Unique image ID
	*/
	function GetImageId() {
		return $this->mImageId;
	}
	
	//! Returns the main image option
	/*!
	* @return String(1) - Either 0 or 1 (False or True)
	*/
	function GetMainImage() {
		if (! isset ( $this->mMainImage )) {
			$get_main_image_sql = 'SELECT Main_Image FROM tblImage WHERE Image_ID = ' . $this->mImageId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $get_main_image_sql )) {
				$error = new Error ( 'Could not fetch the main image option from the database for image .' . $this->mImageId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$main_image = $result->fetch ( PDO::FETCH_OBJ );
			$this->mMainImage = $main_image->Main_Image;
		}
		return $this->mMainImage;
	}
	
	//! Sets the main image option
	/*!
	* @param [in] newMainImage string(1) - Either 0 or 1 (False or True)
	* @return Bool : true if successful
	*/
	function SetMainImage($newMainImage) {
		$this->mMainImage = $newMainImage;
		$set_main_image_sql = 'UPDATE tblImage SET Main_Image = \'' . $this->mMainImage . '\' WHERE Image_ID = ' . $this->mImageId;
		if (! $this->mDatabase->query ( $set_main_image_sql )) {
			$error = new Error ( 'Could not update the main image option in the database for image ' . $this->mImageId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		return true;
	}
	
	//! Returns the image file name
	/*!
	* @return String(100) : The file name of the image
	*/
	function GetFilename() {
		if (! isset ( $this->mFilename )) {
			$sql = 'SELECT Image_Filename FROM tblImage WHERE Image_ID = ' . $this->mImageId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the filename from the database for image .' . $this->mImageId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mFilename = $resultObj->Image_Filename;
		}
		return trim($this->mFilename);
	}
	
	//! Sets the filename of the image in both the instance and the database
	/*!
	* @param [in] newFilename String(100) : The new file name
	* @return Bool : True if successful 
	*/
	function SetFilename($newFilename) {
		$sql = 'UPDATE tblImage SET Image_Filename = \'' . $newFilename . '\' WHERE Image_ID = ' . $this->mImageId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the filename in the database for image ' . $this->mImageId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mFilename = $newFilename;
		return true;
	}

}

/* DEBUG SECTION 
$imageId=2;
$image = new ImageModel($imageId);*/
#try {
#	$image->SetSmallFilename(1);
#} catch(Exception $e) {
#	echo '<strong>'.$e->getMessage().'</strong>';
#}
#try {
#	echo 'Result: ' . $image->GetSmallFilename($image);
#} catch(Exception $e) {
#	echo '<strong>'.$e->getMessage().'</strong>';
#}


?>