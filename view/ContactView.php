<?php
//! The contact us page
class ContactView extends View {

	var $mSessionHelper;

	//! Constructor
	function __construct($catalogue) {
		// What catalogue are we trying to contact?
		$this->mCatalogue = $catalogue;

		// Construct
		parent::__construct($this->mCatalogue->GetDisplayName().' > Contact '.$this->mCatalogue->GetDisplayName());

		// Includes
		$this->IncludeJs('googleMap.js');

		// For the right col view
		$this->mSessionHelper  = new SessionHelper();
	} // End __construct

	//! Generic load function
	function LoadDefault() {
		$footerView = new FooterView ( );
		// Load the Google Maps code in the <body> onload attribute
		$this->mPage .= '<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key='.$this->mRegistry->GoogleMapsApiKey.'" type="text/javascript"></script>';
		$this->mPage .= $this->mPublicLayoutHelper->OpenBody ( ' onload="initialize()" onunload="GUnload()"' );
		$this->mPage .= $this->mPublicLayoutHelper->OpenCentrePageContainer ();
		parent::LoadHeaderSection ($this->mCatalogue);
		parent::LoadNavigation();
		parent::LoadLeftColumn();
		$this->mPage .= $this->mPublicLayoutHelper->OpenRightCol ();
		$this->LoadMainContentColumn ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseRightCol ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenFooterContainer ();
		$this->mPage .= $footerView->LoadDefault ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseFooterContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseCentrePageContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseBody ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseHtml ();
		return $this->mPage;
	}

	function LoadMainContentColumn() {
		$this->mPage .= $this->mPublicLayoutHelper->OpenMainColumn ();
		$this->LoadContact ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseMainColumn ();
	} // End LoadMainContentColumn()

	// Load our contact details
	function LoadContact() {
		$this->mPage .= <<<EOT
		<div class="vcard">
			<h1 id="contactLogo"><span></span>Contact Us</h1>
			<div class="fn org">
				<strong>{$this->mCatalogue->GetDisplayName()}</strong><br />
			</div>
			<div class="adr">
				<span class="street-address">919 Yeovil Road</span><br />
				<span class="locality">Slough</span><br />
				<span class="region">Berkshire</span><br />
				SL1 4NH<br /><br />
			</div>
			<strong>Contact Us</strong><br />
			Tel. <span class="tel">(01753) 572741</span><br />
			Fax. (01753) 535666<br /><br />
			E. <a href="mailto:info@echosupplements.com">info@echosupplements.com</a><br />
			W. <a href="http://www.echosupplements.com" class="url">http://www.echosupplements.com</a><br /><br />
		</div>
			VAT Number: 955765836
			<br /><br />
			Mon-Fri: 9.30am-6pm<br />
			Sat: 10.30am-4pm<br />
			Sunday: CLOSED<br /><br />
			Please note we are closed on bank holidays.
			<br /><br />
			<div id="map_canvas" name="map_canvas" style="width: 500px; height: 300px; border: 1px solid #000;"></div>
			<br /><br style="clear: both" />
			<img src="http://www.echosupplements.com/images/mapSml.gif" style="float: left;" />
EOT;
	} // End LoadContact

	//! Loads the right column, including RightColView
	function LoadRightColumn() {
		$rightColView = new RightColView ( $this->mCatalogue, $this->mSessionHelper );
		$this->mPage .= $rightColView->LoadDefault ();
	} // End LoadRightColumn

} // End ContactView

?>