<?php
require_once ('../autoload.php');

//! Adds a manufacturer to a catalogue
class AddGalleryItemHandler extends Handler {
	
	//! Str - The new manufacturers name
	var $mManufacturerName;
	
	//! Constructor initialises the catalogue controller for usage in Process
	function __construct() {
		parent::__construct ();
		$this->mCatalogueController = new CatalogueController ( );
		$this->mManufacturerController = new ManufacturerController ( );
	} // End __construct()
	

	//! Clean up the gallery item caption/description
	/*!
	 * @param [in] postArr - The _POST array
	 * @return Void
	 */
	function Validate($postArr) {		
		// Save Description
		$this->mClean['newGalleryItemDescription'] = $this->mValidationHelper->MakeMysqlSafe($postArr['newGalleryItemDescription']);	
		$this->mClean['newGalleryItemImage'] = $this->mValidationHelper->MakeSafe($_FILES['newGalleryItemImage']['tmp_name']);		
		$this->mClean['newGalleryItemGalleryId'] = $this->mValidationHelper->MakeSafe($postArr['newGalleryItemGalleryId']);				
	} // End Validate()
	

	//! Insert the manufacturer into the database
	/*!
	 * @return Void
	 */
	function Process() {
		$uploadHelper 			= new UploadHelper; // To upload the image
		$imageController 		= new ImageController; // To create the image
		$galleryItemController 	= new GalleryItemController; // To create the gallery	
		$gallery 				= new GalleryModel($this->mClean['newGalleryItemGalleryId']);
		
		// Create the image + gallery
		$newImage 			= $imageController->CreateImage();
		$newGalleryItem 	= $galleryItemController->CreateGalleryItem();
		$imageFilename 		= 'gallery'.$newGalleryItem->GetGalleryItemId().'image';
		$newImage->SetFilename($imageFilename);
		
		// Upload the image
		$uploadHelper->uploadOriginalImage($this->mClean['newGalleryItemImage'],$imageFilename);

		// Set the gallery
		$newGalleryItem->SetGallery($gallery);

		// Associate the image with the gallery item
		$newGalleryItem->SetImage($newImage);
		
		// Set the description
		$newGalleryItem->SetCaptionText($this->mClean['newGalleryItemDescription']);
				
	} // End Process()


} // End AddCatalogueHandler


// Only process the form if it has been posted
if(isset($_POST['newGalleryItemDescription'])) {
	try {
		$handler = new AddGalleryItemHandler;
		$handler->Validate($_POST);
		$handler->Process();
	} catch ( Exception $e ) {
		echo $e->GetMessage ();
	}
}
?>
<form action="AddGalleryItemHandler.php" method="post" name="addGalleryItemForm" id="addGalleryItemForm" enctype="multipart/form-data">
	<input type="hidden" name="newGalleryItemGalleryId" id="newGalleryItemGalleryId" value="<?php echo $_GET['galleryId'] ?>" />
	<div id="addGalleryFormContainer" style="border: 1px solid #FFF; width: 660px; height: 130px; font-family: Arial, Helvetica, sans-serif; font-size: 10pt;">
        <div id="addGalleryDescriptionContainer" style="border: 1px solid #FFF; width: 400px; height: 130px; float: left;">
        	<label for="newGalleryItemDescription"><strong>Description</strong>: </label>
            <textarea cols="45" rows="5" name="newGalleryItemDescription" id="newGalleryItemDescription"></textarea>
        </div>
        <div id="addGalleryImageContainer" style="border: 1px solid #FFF; width: 250px; height: 130px; float: left;">
            <label for="newGalleryItemImage"><strong>Image</strong>: </label>
            <input type="file" name="newGalleryItemImage" id="newGalleryItemImage" />
            <input type="submit" value="Add Gallery Item" id="addGalleryItemFormSubmit" name="addGalleryItemFormSubmit" style="width: auto; margin: 0px; margin-top: 3px;" />
        </div>
	</div>
    <br style="clear: both" />
</form>