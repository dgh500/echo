<?php
require_once ('../autoload.php');

//! Adds a manufacturer to a catalogue
class AddManufacturerHandler extends Handler {
	
	//! Str - The new manufacturers name
	var $mManufacturerName;
	
	//! Constructor initialises the catalogue controller for usage in Process
	function __construct() {
		parent::__construct ();
		$this->mCatalogueController = new CatalogueController ( );
		$this->mManufacturerController = new ManufacturerController ( );
	} // End __construct()
	

	//! Sets the catalogue and manufacturer name
	/*!
	 * @param [in] postArr - The _POST array
	 * @return Void
	 */
	function Validate($postArr) {
		$this->mCatalogue = new CatalogueModel ( $postArr ['catalogueId'] );
		// Remove HTML
		$this->mManufacturerName = $this->mValidationHelper->RemoveHtml ( $postArr ['manufacturerName'] );
		// Trim end whitespace
		$this->mManufacturerName = $this->mValidationHelper->RemoveWhitespace ( $this->mManufacturerName );
		// Make any (MS)SQL Injection Attack attempts safe
		$this->mManufacturerName = $this->mValidationHelper->MakeMysqlSafe ( $this->mManufacturerName );
	} // End Validate()
	

	//! Insert the manufacturer into the database
	/*!
	 * @return Void
	 */
	function Process() {
		$newManufacturer = $this->mManufacturerController->CreateManufacturer ( $this->mCatalogue );
		$newManufacturer->SetDisplayName ( $this->mManufacturerName );
		
		// Reload
		echo '<div style="width: 150px; height: 20px; position: absolute; left: 250px; font-family: Arial; font-size: 10pt; font-weight: bold;">Manufacturer Added.</div>';
		echo '<script language="javascript" type="text/javascript">
				self.parent.location.href=\'' . $this->mViewDir . '/AdminCatalogueEditView.php?id=' . $_POST ['catalogueId'] . '&tab=manufacturers\';
		</script>';
	} // End Process()


} // End AddCatalogueHandler


// Only process the form if it has been posted
if (isset ( $_POST ['manufacturerName'] )) {
	try {
		$handler = new AddManufacturerHandler ( );
		$handler->Validate ( $_POST );
		$handler->Process ();
	} catch ( Exception $e ) {
		echo $e->GetMessage ();
	}
}
?>
<form action="AddManufacturerHandler.php" method="post"
	name="addManufacturerForm" id="addManufacturerForm"><input type="text"
	name="manufacturerName" id="manufacturerName" /> <br />
<input type="hidden" name="catalogueId" id="catalogueId"
	value="<?php
	echo $_GET ['catalogueId'];
	?>" /> <input type="submit"
	value="Add" id="addManufacturerFormSubmit"
	name="addManufacturerFormSubmit"
	style="width: auto; margin: 0px; margin-top: 3px;" /><br />
<br />
</form>