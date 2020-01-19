<?php

//! Loads the right column for a page
class CheckoutRightColView extends View {
	
	//! Just some initialisation
	/*!
	 * @catalogue Obj:CatalogueModel - The catalogue to load the page for
	 * @sessionHelper Obj:SessionHelper - To manage the basket
	 */
	function __construct($catalogue, $sessionHelper, $secure = false) {
		parent::__construct ();
		$this->mSecure = $secure;
		$this->mCatalogue = $catalogue;
		$this->mSystemSettings = new SystemSettingsModel ( $this->mCatalogue );
		if ($secure) {
			$this->mPublicLayoutHelper = new PublicLayoutHelper ( true );
		} else {
			$this->mPublicLayoutHelper = new PublicLayoutHelper ( );
		}
		$this->mSessionHelper = $sessionHelper;
	}
	
	function LoadDefault() {
		$this->mPage .= $this->mPublicLayoutHelper->OpenRightCol ( true );
		$this->mPage .= $this->mPublicLayoutHelper->OpenRightNavContainer ();
		($this->mSystemSettings->GetShowOrderHotline () ? $this->AddOrderHotline () : false);
		$this->mPage .= $this->mPublicLayoutHelper->CloseRightNavContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseRightCol ();
		return $this->mPage;
	}
	
	function AddOrderHotline() {
		$this->mPage .= $this->mPublicLayoutHelper->CheckoutHotline ();
	}

} // End RightColView


?>