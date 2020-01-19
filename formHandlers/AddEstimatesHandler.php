<?php
require_once ('../autoload.php');

//! Deals with adding estimates for a catalogue
class AddEstimatesHandler extends Handler {
	
	//! Str - The name given to the estimate (Eg. 3-5 days)
	var $mEstimateName;
	
	//! Call the parent constructor to load common variables
	function __construct() {
		parent::__construct ();
	}
	
	//! Validate the estimate name
	function Validate($postArr) {
		// Remove HTML
		$this->mEstimateName = $this->mValidationHelper->RemoveHtml ( $postArr ['estimateName'] );
		// Trim
		$this->mEstimateName = $this->mValidationHelper->RemoveWhitespace ( $this->mEstimateName );
		// Make HTML eitities safe
		$this->mEstimateName = $this->mValidationHelper->ConvertHtmlEntities ( $this->mEstimateName );
		// Make any (MS)SQL Injection Attack attempts safe
		$this->mEstimateName = $this->mValidationHelper->MakeMysqlSafe ( $this->mEstimateName );
	} // End Validate()
	

	//! Add the dispatch date and redirect the user
	function Process() {
		// Process
		$dispatchDateController = new DispatchDateController ( );
		$newDispatchDate = $dispatchDateController->CreateDispatchDate ( $this->mEstimateName );
		
		// Reload
		echo '<div style="width: 150px; height: 20px; position: absolute; left: 250px; font-family: Arial; font-size: 10pt; font-weight: bold;">Estimate Added.</div>';
		echo '<script language="javascript" type="text/javascript">
				self.parent.location.href=\'' . $this->mViewDir . '/AdminCatalogueEditView.php?id=' . $_POST ['catalogueId'] . '&tab=estimates\';
		</script>';
	} // End Process


} // End AddEstimatesHandler


// Only process the form if it has been posted
if (isset ( $_POST ['estimateName'] )) {
	try {
		$handler = new AddEstimatesHandler ( );
		$handler->Validate ( $_POST );
		$handler->Process ();
	} catch ( Exception $e ) {
		echo $e->GetMessage ();
	}
}
?>

<form action="AddEstimatesHandler.php" method="post"
	name="addEstimatesForm" id="addEstimatesForm"><input type="text"
	name="estimateName" id="estimateName" /> <br />
<input type="hidden" name="catalogueId" id="catalogueId"
	value="<?php
	echo $_GET ['catalogueId'];
	?>" /> <input type="submit"
	value="Add" id="addEstimatesFormSubmit" name="addEstimatesFormSubmit"
	style="width: auto; margin: 0px; margin-top: 3px;" /><br />
<br />
</form>