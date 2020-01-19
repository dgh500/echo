<?php
//! When the website is disabled (set in autoload.php) then this view is shown
class DisabledView extends View {

	var $mSessionHelper;

	// Init
	function __construct($catalogue) {
		// Params
		$this->mCatalogue = $catalogue;

		// Construct
		parent::__construct($this->mCatalogue->GetDisplayName().' > Website Maintenance in Progress');

		// Keep the session going
		$this->mSessionHelper = new SessionHelper();
	} // End __construct()

	//! Generic Load function
	function LoadDefault() {
		$footerView = new FooterView ( );
		$this->mPage .= $this->mPublicLayoutHelper->OpenBody ();
		$this->mPage .= $this->mPublicLayoutHelper->AddTopRelativeAnchor ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenCentrePageContainer ();
		parent::LoadHeaderSection($this->mCatalogue);
		$this->mPage .= $this->mPublicLayoutHelper->OpenLayoutContainers ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenCentreColumn ();
		$this->LoadMainContentColumn ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseCentreColumn ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseLayoutContainers ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenFooterContainer ();
		$this->mPage .= $footerView->LoadDefault ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseFooterContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseCentrePageContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseBody ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseHtml ();
		return $this->mPage;
	}

	//! Load the central column
	function LoadMainContentColumn() {
		$this->mPage .= $this->mPublicLayoutHelper->OpenMainColumn ();
		$this->LoadDisabledMessage ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseMainColumn ();
	} // End LoadMainContentColumn()

	//! Loads the disabled message
	function LoadDisabledMessage() {
		$this->mPage .= <<<EOT
		<div style="margin-left: 200px; border: 0px solid #f00; width: 600px; padding: 10px; height: 400px">
			<h1>Website Down</h1>
			We are currently performing maintenance work on the website - we will be back online within 1 hour!
		</div>
EOT;
	} // End LoadDisabledMessage
} // End DisabledView

?>