<?php
//! Shows the page with all brands
class ManufacturersView extends View {

	var $mSessionHelper;

	function __construct($catalogue) {
		// Params
		$this->mCatalogue = $catalogue;

		// Construct
		parent::__construct($this->mCatalogue->GetDisplayName().' > All Brands');

		// Member vars
		$this->mSessionHelper = new SessionHelper ( );
		$this->mManufacturerController = new ManufacturerController ( );
	}

	//! Generic load function
	function LoadDefault() {
		$footerView = new FooterView ( );
		$this->mPage .= $this->mPublicLayoutHelper->OpenBody ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenCentrePageContainer ();
		parent::LoadHeaderSection($this->mCatalogue);
		parent::LoadNavigation();
		parent::LoadLeftColumn ();
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
	} // End LoadDefault

	//! Loads the central column
	function LoadMainContentColumn() {
		$this->mPage .= $this->mPublicLayoutHelper->OpenMainColumn ();
		$this->LoadManufacturers ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseMainColumn ();
	} // End LoadMainContentColumn()

	//! Loads the manufacturer list display
	function LoadManufacturers() {
		$publicLayoutHelper = new PublicLayoutHelper ( );
		$this->mPage .= '<h1>Shop By Brand</h1>';
		$allManufacturers = $this->mManufacturerController->GetAllManufacturersFor ( $this->mCatalogue, false );
		foreach ( $allManufacturers as $manufacturer ) {
			$linkTo = $this->mBaseDir . '/brand/' . $this->mValidationHelper->MakeLinkSafe ( trim ( $manufacturer->GetDisplayName () ) ) . '/' . $manufacturer->GetManufacturerId ();
			$this->mPage .= '<div class="brandContainer">
								<a href="' . $linkTo . '">';
			$this->mPage .= $publicLayoutHelper->ManufacturerImage ( $manufacturer );
			$this->mPage .= '</a>
								<h3><a href="' . $linkTo . '">' . $manufacturer->GetDisplayName () . '</a></h3>
							</div> <!-- Close brandContainer -->';
		}
	}

	//! Loads the right column, including RightColView
	function LoadRightColumn() {
		$rightColView = new RightColView ( $this->mCatalogue, $this->mSessionHelper );
		$this->mPage .= $rightColView->LoadDefault ();
	}
} // End ManufacturersView

?>