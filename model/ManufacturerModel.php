<?php

//! A single manufacturer (Eg. Scubapro)
class ManufacturerModel {

	//! Int : The unique manufacturer ID
	var $mManufacturerId;
	//! Int : The catalog the manufacturer belongs to
	var $mCatalogue;
	//! String(250) : The manufacturers name (Eg. Mares)
	var $mDisplayName;
	//! String(3000) : A short description of the manufacturer
	var $mDescription;
	//! Obj:ImageModel : The image related to this manufacturer
	var $mImage;
	//! Obj:ContentModel : The content that contains the size chart for this manfuacturers
	var $mSizeChart;

	//! Constructor, initialises the manufacturer ID. Throws an exception if the manufacturerId doesnt exist, otherwise sets the manufacturer ID
	function __construct($manufacturerId) {
		$registry = Registry::getInstance ();
		$this->mDatabase = $registry->database;
		$does_this_manufacturer_exist_sql = 'SELECT COUNT(Manufacturer_ID) AS ManufacturerCount FROM tblManufacturer WHERE Manufacturer_ID = ' . $manufacturerId;
		$result = $this->mDatabase->query ( $does_this_manufacturer_exist_sql );
		if ($result) {
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			if ($resultObj->ManufacturerCount > 0) {
				$this->mManufacturerId = $manufacturerId;
			} else {
				$error = new Error ( 'Could not initialise manufacturer ' . $manufacturerId . ' because it does not exist in the database.' );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
		} else {
			$error = new Error ( 'Could not initialise manufacturer ' . $manufacturerId . ' because query: ' . $does_this_manufacturer_exist_sql . ' failed.' );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
	}

	//! Returns the Catalog the manufacturer is in
	/*!
	* @return Obj:CatalogueModel
	*/
	function GetCatalogue() {
		if (! isset ( $this->mCatalogue )) {
			$get_catalogue_sql = 'SELECT Catalogue_ID FROM tblManufacturer WHERE Manufacturer_ID = ' . $this->mManufacturerId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $get_catalogue_sql )) {
				$error = new Error ( 'Could not fetch the catalogue ID for manufacturer ' . $this->mManufacturerId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$catalog_id = $result->fetch ( PDO::FETCH_OBJ );
			$this->mCatalogue = new CatalogueModel ( $catalog_id->Catalogue_ID );
		}
		return $this->mCatalogue;
	}

	//! Sets the catalogue of the manufacturer
	/*!
	* @param [in] newCatalogue Obj:CatalogueModel : The new catalogue
	* @return Bool : true if successful
	*/
	function SetCatalogue($newCatalogue) {
		$set_catalogue_sql = 'UPDATE tblManufacturer SET Catalogue_ID = \'' . $newCatalogue->GetCatalogueId () . '\' WHERE Manufacturer_ID = ' . $this->mManufacturerId;
		if (! $this->mDatabase->query ( $set_catalogue_sql )) {
			$error = new Error ( 'Could not update the catalog ID for manufacturer ' . $this->mManufacturerId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mCatalogue = $newCatalogue;
		return true;
	}

	//! Returns the display option of the manufacturer
	/*!
	* @return Boolean - True if the image is to be displayed
	*/
	function GetDisplay() {
		if (! isset ( $this->mDisplay )) {
			$sql = 'SELECT Display FROM tblManufacturer WHERE Manufacturer_ID = ' . $this->mManufacturerId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the display for manufacturer ' . $this->mManufacturerId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$display = $result->fetch ( PDO::FETCH_OBJ );
			if (is_null ( $display->Display )) {
				$this->mDisplay = false;
			} else {
				$this->mDisplay = $display->Display;
			}
		}
		return $this->mDisplay;
	}

	//! Sets the display option of the manufacturer - whether they are to be displayed on the front page
	/*!
	* @param [in] newDisplay String/Int - Either 0 or 1
	* @return Bool : true if successful
	*/
	function SetDisplay($newDisplay) {
		$sql = 'UPDATE tblManufacturer SET Display = \'' . $newDisplay . '\' WHERE Manufacturer_ID = ' . $this->mManufacturerId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the display for manufacturer ' . $this->mManufacturerId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDisplay = $newDisplay;
		return true;
	}

	//! Returns the manufacturer description
	/*!
	* @return String(3000)
	*/
	function GetDescription($stripTags=true) {
		if (! isset ( $this->mDescription )) {
			$get_manufacturer_desc_sql = 'SELECT Description FROM tblManufacturer WHERE Manufacturer_ID = ' . $this->mManufacturerId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $get_manufacturer_desc_sql )) {
				$error = new Error ( 'Could not fetch the manufacturer description for manufacturer ' . $this->mManufacturerId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$description = $result->fetch ( PDO::FETCH_OBJ );
			$this->mDescription = $description->Description;
		}
		if($stripTags) {
			return trim(strip_tags($this->mDescription));
		} else {
			return trim($this->mDescription);
		}
	}

	//! Sets the description of the manufacturer
	/*!
	* @param [in] newDescription string : The new manufacturer description
	* @return Bool : true if successful
	*/
	function SetDescription($newDescription) {
		$set_description_sql = 'UPDATE tblManufacturer SET Description = \'' . mysql_escape_string($newDescription) . '\' WHERE Manufacturer_ID = ' . $this->mManufacturerId;
		if (! $this->mDatabase->query ( $set_description_sql )) {
			$error = new Error ( 'Could not update the manufacturer description for manufacturer ' . $this->mManufacturerId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
			return false;
		}
		$this->mDescription = $newDescription;
		return true;
	}

	//! Returns the manufacturer name
	/*!
	* @return String(250)
	*/
	function GetDisplayName() {
		if (! isset ( $this->mDisplayName )) {
			$get_display_name_sql = 'SELECT Display_Name FROM tblManufacturer WHERE Manufacturer_ID = ' . $this->mManufacturerId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $get_display_name_sql )) {
				$error = new Error ( 'Could not fetch the manufacturer display name for manufacturer ' . $this->mManufacturerId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$display_name = $result->fetch ( PDO::FETCH_OBJ );
			$this->mDisplayName = $display_name->Display_Name;
		}
		return trim($this->mDisplayName);
	}

	//! Sets the display name of the manufacturer
	/*!
	* @param [in] newDisplayName string : The new manufacturer display name
	* @return Bool : true if successful
	*/
	function SetDisplayName($newDisplayName) {
		$set_display_name_sql = 'UPDATE tblManufacturer SET Display_Name = \'' . $newDisplayName . '\' WHERE Manufacturer_ID = ' . $this->mManufacturerId;
		if (! $this->mDatabase->query ( $set_display_name_sql )) {
			$error = new Error ( 'Could not update the manufacturer display name for manufacturer ' . $this->mManufacturerId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mDisplayName = $newDisplayName;
		return true;
	}

	//! Returns the manufacturer banner URL
	/*!
	* @return String(250)
	*/
	function GetBannerUrl() {
		if (! isset ( $this->mBannerUrl )) {
			$sql = 'SELECT BannerUrl FROM tblManufacturer WHERE Manufacturer_ID = ' . $this->mManufacturerId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the manufacturer display name for manufacturer ' . $this->mManufacturerId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			$this->mBannerUrl = $resultObj->BannerUrl;
		}
		return trim($this->mBannerUrl);
	}

	//! Sets the banner URL of the manufacturer
	/*!
	* @param [in] newUrl string : The new manufacturer banner url
	* @return Bool : true if successful
	*/
	function SetBannerUrl($newUrl) {
		$sql = 'UPDATE tblManufacturer SET BannerUrl = \'' . trim($newUrl) . '\' WHERE Manufacturer_ID = ' . $this->mManufacturerId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the manufacturer banner url for manufacturer ' . $this->mManufacturerId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mBannerUrl = $newUrl;
		return true;
	}

	//! Returns the Image associated with this manufacturer
	/*!
	* @return Obj:ImageModel : The image associated with the manufacturer
	*/
	function GetImage() {
		if (! isset ( $this->mImage )) {
			$get_image_sql = 'SELECT Image_ID FROM tblManufacturer WHERE Manufacturer_ID = ' . $this->mManufacturerId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $get_image_sql )) {
				$error = new Error ( 'Could not fetch the image ID for manufacturer ' . $this->mManufacturerId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$image_id = $result->fetch ( PDO::FETCH_OBJ );
			try {
				$newImage = new ImageModel ( $image_id->Image_ID );
				$this->mImage = $newImage;
			} catch ( Exception $e ) {
				return false;
			}
		}
		return $this->mImage;
	}

	//! Sets the image of the manufacturer
	/*!
	* @param [in] newImage Obj:ImageModel : The new image
	* @return Bool : true if successful
	*/
	function SetImage($newImage) {
		$set_image_sql = 'UPDATE tblManufacturer SET Image_ID = \'' . $newImage->GetImageId () . '\' WHERE Manufacturer_ID = ' . $this->mManufacturerId;
		if (! $this->mDatabase->query ( $set_image_sql )) {
			$error = new Error ( 'Could not update the manufacturer image for manufacturer ' . $this->mManufacturerId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mImage = $newImage;
		return true;
	}

	//! Returns the size chart associated with this manufacturer
	/*!
	* @return Obj:ContentModel : The size chart associated with the manufacturer (or NULL)
	*/
	function GetSizeChart() {
		if (! isset ( $this->mSizeChart )) {
			$sql = 'SELECT Size_Chart_ID FROM tblManufacturer WHERE Manufacturer_ID = ' . $this->mManufacturerId.' LIMIT 1';
			if (! $result = $this->mDatabase->query ( $sql )) {
				$error = new Error ( 'Could not fetch the size chart ID for manufacturer ' . $this->mManufacturerId );
				$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
				throw new Exception ( $error->GetErrorMsg () );
			}
			$resultObj = $result->fetch ( PDO::FETCH_OBJ );
			if ($resultObj->Size_Chart_ID) {
				$newSizeChart = new ContentModel ( $resultObj->Size_Chart_ID );
				$this->mSizeChart = $newSizeChart;
			} else {
				$this->mSizeChart = NULL;
			}
		}
		return $this->mSizeChart;
	}

	//! Sets the size chart of the manufacturer
	/*!
	* @param [in] newSizeChart Obj:ContentModel : The new size chart
	* @return Bool : true if successful
	*/
	function SetSizeChart($newSizeChart) {
		$sql = 'UPDATE tblManufacturer SET Size_Chart_ID = \'' . $newSizeChart->GetContentId () . '\' WHERE Manufacturer_ID = ' . $this->mManufacturerId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the size chart for manufacturer ' . $this->mManufacturerId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		$this->mSizeChart = $newSizeChart;
		return true;
	}

	function ResetSizeChart() {
		$sql = 'UPDATE tblManufacturer SET Size_Chart_ID = NULL WHERE Manufacturer_ID = ' . $this->mManufacturerId;
		if (! $this->mDatabase->query ( $sql )) {
			$error = new Error ( 'Could not update the size chart for manufacturer ' . $this->mManufacturerId );
			$error->PdoErrorHelper ( $this->mDatabase->errorInfo (), __LINE__, __FILE__ );
			throw new Exception ( $error->GetErrorMsg () );
		}
		return true;
	}

	//! Returns the Manufacturer ID (Set in constructor)
	/*!
	* @return Int
	*/
	function GetManufacturerId() {
		return $this->mManufacturerId;
	}

}

/* DEBUG SECTION
$manufacturerId = 2;
try {
	$manufacturer = new ManufacturerModel($manufacturerId);
	$catalogue = new CatalogueModel(2);
	$manufacturer->SetCatalogue($catalogue);
} catch(Exception $e) {
	echo $e->getMessage();
}*/

?>