<?php
require_once ('../autoload.php');

//! Deals with adding tags for a catalogue
class AddTagsHandler extends Handler {
	
	//! Str - The name given to the tag
	var $mTagName;
	
	//! Call the parent constructor to load common variables
	function __construct() {
		parent::__construct ();
	}
	
	//! Validate the estimate name
	function Validate($postArr) {
		// Remove HTML
		$this->mTagName = $this->mValidationHelper->RemoveHtml ( $postArr ['tagName'] );
		// Trim
		$this->mTagName = $this->mValidationHelper->RemoveWhitespace ( $this->mTagName );
		// Make HTML eitities safe
		$this->mTagName = $this->mValidationHelper->ConvertHtmlEntities ( $this->mTagName );
		// Make any (MS)SQL Injection Attack attempts safe
		$this->mTagName = $this->mValidationHelper->MakeMysqlSafe ( $this->mTagName );
		$this->mCatalogue = new CatalogueModel($postArr['catalogueId']);
	} // End Validate()
	

	//! Add the dispatch date and redirect the user
	function Process() {
		// Process
		$tagController = new TagController();
		$newTag = $tagController->CreateTag ( $this->mTagName );
		$tagController->CreateCatalogueTagLink($this->mCatalogue,$newTag);
		
		// Reload
		echo '<div style="width: 150px; height: 20px; position: absolute; left: 250px; font-family: Arial; font-size: 10pt; font-weight: bold;">Tag Added.</div>';
		echo '<script language="javascript" type="text/javascript">
				self.parent.location.href=\'' . $this->mViewDir . '/AdminCatalogueEditView.php?id=' . $_POST ['catalogueId'] . '&tab=tags\';
		</script>';
	} // End Process


} // End AddEstimatesHandler


// Only process the form if it has been posted
if (isset ( $_POST ['tagName'] )) {
	try {
		$handler = new AddTagsHandler ( );
		$handler->Validate ( $_POST );
		$handler->Process ();
	} catch ( Exception $e ) {
		echo $e->GetMessage ();
	}
}
?>

<form action="AddTagHandler.php" method="post"
	name="addTagsForm" id="addTagsForm"><input type="text"
	name="tagName" id="tagName" /> <br />
<input type="hidden" name="catalogueId" id="catalogueId"
	value="<?php
	echo $_GET ['catalogueId'];
	?>" /> <input type="submit"
	value="Add" id="addTagsFormSubmit" name="addTagsFormSubmit"
	style="width: auto; margin: 0px; margin-top: 3px;" /><br />
<br />
</form>