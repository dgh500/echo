<?php

//! Defines the request a brochure section
class BrochureView extends View {
	
	var $mCatalogue;
	var $mSystemSettings;
	var $mSessionHelper;
	
	// Constructor
	function __construct($catalogue) {
		
		// CSS Includes
		$cssIncludes = array('BrochureView.css.php');
		$jsIncludes = array('BrochureView.js' );
		
		// Construct
		parent::__construct('Deep Blue Dive > Brochure Request',$cssIncludes,$jsIncludes);
		
		// Member vars
		$this->mCatalogue 			= $catalogue;
		$this->mSystemSettings 		= new SystemSettingsModel ( $this->mCatalogue );
		$this->mSessionHelper 		= new SessionHelper ( );
		$this->mBasketId 			= $this->mSessionHelper->GetSessionId ();
	} // End __construct
	
	//! Generic Loader
	function LoadDefault() {
		// If they fuck up their address, show failure message
		if (isset ( $_GET ['failure'] )) {
			$this->mFailureDisplay = true;
			$this->mFailureType = $_GET ['failure'];
		} else {
			$this->mFailureDisplay = false;
		}
		
		// Show page
		$footerView = new FooterView ( );
		$this->mPage .= $this->mPublicLayoutHelper->OpenBody ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenCentrePageContainer ();
		parent::LoadHeaderSection($this->mCatalogue);
		$this->mPage .= $this->mPublicLayoutHelper->OpenLayoutContainers ();
		parent::LoadLeftColumn($this->mCatalogue);
		$this->mPage .= $this->mPublicLayoutHelper->OpenCentreColumn ();
		$this->LoadMainContentColumn ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseCentreColumn ();
		$this->LoadRightColumn ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseLayoutContainers ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenFooterContainer ();
		$this->mPage .= $footerView->LoadDefault ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseFooterContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseCentrePageContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseBody ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseHtml ();
		return $this->mPage;
	} // End LoadDefault
	
	//! Handles logic for what to display in the centre column
	function LoadMainContentColumn() {
		$this->mPage .= $this->mPublicLayoutHelper->OpenMainColumn ();
		if ($this->mFailureDisplay) {
			switch ($this->mFailureType) {
				case 'send' :
					$this->mPage .= '<h1>Brochure Request</h1>There was a failure sending the request.';
					break;
				case 'valid' :
					$this->mPage .= '<h1>Brochure Request</h1>There was a failure with the details you provided.';
					break;
				case 'none' :
					$this->mPage .= '<h1>Brochure Request</h1>Your request has been received and your brochure will be delivered soon!';
					break;
			}
		} else {
			$this->LoadBrochureRequest ();
		}
		$this->mPage .= $this->mPublicLayoutHelper->CloseMainColumn ();
	} // End LoadMainContentColumn
	
	//! Loads the form for requesting a brochure
	function LoadBrochureRequest() {
		$this->mPage .= <<<EOT
			<h1 id="brochureRequestLogo"><span></span>Brochure Request</h1>
			<p>Please fill in your name and address and click Go to to receive a copy of our catalogue!</p>
			<p>Please note we can only send brochures to <strong>UK addresses</strong>.</p>
			<form action="{$this->mFormHandlersDir}/BrochureRequestHandler.php" method="post" name="brochureRequestForm" id="brochureRequestForm">
			<input type="hidden" name="catalogueId" id="catalogueId" value="{$this->mCatalogue->GetCatalogueId()}" />
				<label for="catReqName"><span class="required">*</span> Name:</label>
					<input type="text" name="catReqName" id="catReqName" /><br />
				<label for="catReqAddress1"><span class="required">*</span> Address:</label>
					<input type="text" name="catReqAddress1" id="catReqAddress1" /><br />
				<label for="catReqAddress2">&nbsp;</label>
					<input type="text" name="catReqAddress2" id="catReqAddress2" /><br />
				<label for="catReqAddress3">&nbsp;</label>
					<input type="text" name="catReqAddress3" id="catReqAddress3" /><br />
				<label for="catReqTown">Town: </label>
					<input type="text" name="catReqTown" id="catReqTown" /><br />
				<label for="catReqCounty"><span class="required">*</span> County</label>
					<input type="text" name="catReqCounty" id="catReqCounty" /><br />
				<label for="catReqPostcode"><span class="required">*</span> Postcode</label>
					<input type="text" name="catReqPostcode" id="catReqPostcode" />	<br />
				<input type="submit" value="Go" class="submit" />	
				<br /><br />
				<div id="error"></div>			
			</form>
EOT;
	} // End LoadBrochureRequest
		
	//! Loads the right column, including RightColView
	function LoadRightColumn() {
		$rightColView = new RightColView ( $this->mCatalogue, $this->mSessionHelper );
		$this->mPage .= $rightColView->LoadDefault ();
	}
} // End BrochureView

?>