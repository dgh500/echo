<?php
require_once ('../autoload.php');

//! Adds a catalogue to the database
class AddCatalogueHandler extends Handler {
	
	//! String : Display name of the category to add
	var $mDisplayName;
	//! Obj : CatalogueController
	var $mCatalogueController;
	
	//! Constructor initialises the catalogue controller for usage in Process
	function __construct() {
		parent::__construct();
		$this->mCatalogueController = new CatalogueController ( );
	} // End __construct()
	

	//! Expects the catalogue ID, display name and description
	/*!
	 * @param [in] displayName : The display name of the category
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
		$newCatalogue = $this->mCatalogueController->CreateCatalogue ( $this->mDisplayName );
	} // End Process()


} // End AddCatalogueHandler


try {
	$handler = new AddCatalogueHandler ( );
	$handler->Validate ( $_POST ['displayName'] );
	$handler->Process ();
	?>
<!-- Reload the page using js because need to load in the correct frame -->
<script language="javascript" type="text/javascript">
		self.parent.window.frames["catalogueMenu"].location.reload();
	</script>
<?php
} catch ( Exception $e ) {
	echo $e->GetMessage ();
}

?>