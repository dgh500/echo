<?php

//! Models a single tag (Eg. Lose Weight) - tags are used to classify products on a one-to-many basis, essentially allowing another version of categories but they can have different titles - for example: shop by 'goal', shop by 'hobby', shop by 'sport' etc.
class TagModel {
	
	//! Int : Unique Tag ID
	var $mTagId;
	//! String : The tag display name (Eg. Lose Weight)
	var $mDisplayName;
	//! Int : The catalog the tag belongs to
	var $mCatalogue;
	//! String(3000) : A short description of the tag
	var $mDescription;
	//! Obj:ImageModel : The image related to this tag
	var $mImage;

	//! Constructor, initialises the tag ID
	function __construct($tagId) {
		$this->mRegistry = Registry::getInstance ();
		$this->mDatabase = $this->mRegistry->database;
		$sql = 'SELECT COUNT(Tag_ID) FROM tblTag WHERE Tag_ID = '.$tagId;
		if (!$result = $this->mDatabase->query($sql)) {
			$error = new Error('Could not construct tag.');
			$error->PdoErrorHelper($this->mDatabase->errorInfo(), __LINE__, __FILE__ );
			throw new Exception($error->GetErrorMsg());
		}
		if ($result->fetchColumn () > 0) {
			$this->mTagId = $tagId;
		} else {
			$error = new Error ( 'Could not initialise tag ' . $tagId . ' because it does not exist in the database.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

	//! Returns the Image associated with this tag
	/*!
	* @return Obj:ImageModel : The image associated with the tag
	*/
	function GetImage() {
		if(!isset($this->mImage)) {
			$sql = 'SELECT Image_ID FROM tblTag WHERE Tag_ID = '.$this->mTagId.' LIMIT 1';
			if (!$result = $this->mDatabase->query($sql)) {
				$error = new Error('Could not fetch the image ID for tag '.$this->mTagId);
				$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
				throw new Exception($error->GetErrorMsg());
			}
			$resultObj = $result->fetch(PDO::FETCH_OBJ);
			try {
				$newImage = new ImageModel($resultObj->Image_ID);
				$this->mImage = $newImage;
			} catch ( Exception $e ) {
				return false;
			}
		}
		return $this->mImage;
	}

	//! Returns the tag description
	/*!
	* @return String(3000)
	*/
	function GetDescription() {
		if (! isset ( $this->mDescription )) {
			$sql = 'SELECT Description FROM tblTag WHERE Tag_ID = ' . $this->mTagId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the tag description for tag ' . $this->mTagId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mDescription = $resultObj->Description;
		}
		return $this->mDescription;
	}

	//! Returns the Catalog the manufacturer is in
	/*!
	* @return Obj:CatalogueModel
	*/
	function GetCatalogue() {
		if (! isset ( $this->mCatalogue )) {
			$sql = 'SELECT Catalogue_ID FROM tblCatalogue_Tags WHERE Tag_ID = ' . $this->mTagId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the catalogue ID for tag ' . $this->mTagId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mCatalogue = new CatalogueModel ( $resultObj->Catalogue_ID );
		}
		return $this->mCatalogue;
	}
	
	//! Returns the name of the tag
	/*!
	* @return String
	*/
	function GetDisplayName() {
		if (!isset($this->mDisplayName)) {
			$sql = 'SELECT Display_Name FROM tblTag WHERE Tag_ID = '.$this->mTagId.' LIMIT 1';
			if (!$result = $this->mDatabase->query($sql)) {
				$error = new Error('Could not fetch the display name for tag ' . $this->mTagId);
				$error->PdoErrorHelper($this->mDatabase->errorInfo(),__LINE__,__FILE__);
				throw new Exception($error->GetErrorMsg());
			}
			$display_name = $result->fetch(PDO::FETCH_OBJ);
			$this->mDisplayName = $display_name->Display_Name;
		}
		return $this->mDisplayName;
	}
	
	//! Sets the name of the dispatch date
	/*!
	* @param [in] newDisplayName : String
	* @return Bool : true if successful
	*/
	function SetDisplayName($newDisplayName) {
		$sql = 'UPDATE tblTag SET Display_Name = \'' . $newDisplayName . '\' WHERE Tag_ID = ' . $this->mTagId;
		if (!$this->mDatabase->query($sql)) {
			$error = new Error ( 'Could not update the display name for tag ' . $this->mTagId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDisplayName = $newDisplayName;
		return true;
	}

	//! Sets the description of the dispatch date
	/*!
	* @param [in] newDescription : String
	* @return Bool : true if successful
	*/
	function SetDescription($newDescription) {
		$sql = 'UPDATE tblTag SET Description = \'' . $newDescription . '\' WHERE Tag_ID = ' . $this->mTagId;
		if (!$this->mDatabase->query($sql)) {
			$error = new Error ( 'Could not update the description for tag ' . $this->mTagId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDescription = $newDescription;
		return true;
	}

	//! Sets the image of the tag
	/*!
	* @param [in] newImage Obj:ImageModel : The new image
	* @return Bool : true if successful
	*/
	function SetImage($newImage) {
		$set_image_sql = 'UPDATE tblTag SET Image_ID = \'' . $newImage->GetImageId () . '\' WHERE Tag_ID = ' . $this->mTagId;
		if (! $this->mDatabase->query ( $set_image_sql )) {
			$error = new Error ( 'Could not update the tag image for tag ' . $this->mTagId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mImage = $newImage;
		return true;
	}
	
	//! Returns the unique dispatch date ID (Set in the constructor)
	/*!
	* @return Int
	*/
	function GetTagId() {
		return $this->mTagId;
	}

}

?>