<?php
require_once ('../autoload.php');

//! Adds a gallery to the database
class AddGalleryHandler extends Handler {
	
	//! String : Display name of the gallery to add
	var $mDisplayName;
	//! Obj : GalleryController
	var $mGalleryController;
	
	//! Constructor initialises the gallery controller for usage in Process
	function __construct() {
		parent::__construct();
		$this->mGalleryController = new GalleryController;
	} // End __construct()
	

	//! Expects the gallery ID, display name and description
	/*!
	 * @param [in] displayName : The display name of the gallery
	 * @return Void
	 */
	function Validate($displayName) {
		// Remove HTML
		$this->mDisplayName = $this->mValidationHelper->RemoveHtml ( $displayName );
		// Trim end whitespace
		$this->mDisplayName = $this->mValidationHelper->RemoveWhitespace ( $displayName );
		// Make any (MS)SQL Injection Attack attempts safe
		$this->mDisplayName = $this->mValidationHelper->MakeMysqlSafe ( $displayName );
	} // End Validate()
	

	//! Insert the catalogue into the database
	/*!
	 * @return Void
	 */
	function Process() {
		$newGallery = $this->mGalleryController->CreateGallery($this->mDisplayName);
	} // End Process()


} // End AddCatalogueHandler


try {
	$handler = new AddGalleryHandler ( );
	$handler->Validate ( $_POST ['displayName'] );
	$handler->Process ();
	?>
<!-- Reload the page using js because need to load in the correct frame -->
<script language="javascript" type="text/javascript">
		self.parent.window.frames["galleryMenu"].location.reload();
	</script>
<?php
} catch ( Exception $e ) {
	echo $e->GetMessage ();
}

?>