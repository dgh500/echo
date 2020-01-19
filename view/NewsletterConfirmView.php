<?php

//! Loads the home page
class NewsletterConfirmView extends View {

	//! The catalogue to load for
	var $mCatalogue;
	//! Settings to do with the catalogue such as whether to display different components
	var $mSystemSettings;
	//! Deals with managing the basket and any session variables
	var $mSessionHelper;
	//! Holds HTML code for public viewing
	var $mPublicLayoutHelper;
	//! ID of the current basket
	var $mBasketId;

	//! Constructor, sets some member variables based on the catalogue
	function __construct($catalogue) {
		parent::__construct ($catalogue->GetIndexTitle().' > '.$catalogue->GetDisplayName());
		$this->mCatalogue = $catalogue;
		$this->mSessionHelper 		= new SessionHelper ( );
		$this->mPublicLayoutHelper 	= new PublicLayoutHelper ( );
		$this->mSystemSettings 		= new SystemSettingsModel ( $this->mCatalogue );
		$this->mBasketId 			= $this->mSessionHelper->GetSessionId ();
	}

	//! Main page load function
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
	}

	//! Loads the horizontal navigation bar
	function LoadTopBrands() {
		$topBrandsView = new TopBrandsView;
		$this->mPage .= $topBrandsView->LoadDefault($this->mCatalogue);
	}

	//! Loads the centre column
	function LoadMainContentColumn() {
		$this->mPage .= '
			<h1>Newsletter Signup</h1>
			<p>Thanks for signing up! We hope you enjoy the newsletter!</p>
		';
	}

} // End NewsletterConfirmView


?>