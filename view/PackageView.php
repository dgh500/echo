<?php
//! Defines the package page
class PackageView extends View {

	var $mSystemSettings;
	var $mSessionHelper;

	function __construct($packageId) {
		try {
		// Params
		$this->mPackage 		= new PackageModel($packageId);
		$this->mCatalogue		= $this->mPackage->GetCatalogue();
		} catch(Exception $e) {
			echo '<img src="http://www.echosupplements.com/images/echoWatermarkLarge.jpg" /><br />';
			echo '<p style="font-family: Arial, Sans-Serif; font-size: 14pt;">Sorry this offer does not exist, redirecting you to www.echosupplements.com please wait...</p>';
			echo '<script type="text/javascript">
			<!--
			setTimeout("top.location.href = \'http://www.echosupplements.com\'",4000);
			//-->
			</script>';
			die();

		}

		// Includes
		#$cssIncludes = array('PackageView.css.php');
		$cssIncludes = array();

		// Title
		$pHelper = new PresentationHelper;
		$price = $pHelper->Money($this->mPackage->GetActualPrice());
		$saving = $this->mPackage->GetSaving(true).'%';
		$freeDelivery = '';
		if($this->mPackage->GetActualPrice() > 45) {
			$freeDelivery = ' Free Delivery';
		}

		$title = $this->mPackage->GetDisplayName().' - Only &pound;'.$price.' - Save '.$saving.$freeDelivery;

		// Construct
		parent::__construct($title,$cssIncludes);

		// Member Vars
		$this->mSystemSettings 	= new SystemSettingsModel ( $this->mCatalogue );
		$this->mSessionHelper 	= new SessionHelper ( );

		// Set recently viewed
		$this->mSessionHelper->SetRecentlyViewedProduct ( $packageId, true );
	}

	//! Generic loader
	function LoadDefault() {
		$footerView = new FooterView ( );
		$this->mPage .= $this->mPublicLayoutHelper->OpenBody ();
		$this->mPage .= $this->mPublicLayoutHelper->AddTopRelativeAnchor ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenCentrePageContainer ();
		parent::LoadHeaderSection($this->mCatalogue);
		parent::LoadNavigation($this->mCatalogue);
		parent::LoadLeftColumn($this->mCatalogue);
		$this->mPage .= $this->mPublicLayoutHelper->OpenRightCol();
		$this->LoadMainContentColumn ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseRightCol();
		$this->mPage .= $this->mPublicLayoutHelper->OpenFooterContainer ();
		$this->mPage .= $footerView->LoadDefault ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseFooterContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseCentrePageContainer ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseBody ();
		$this->mPage .= $this->mPublicLayoutHelper->CloseHtml ();
		return $this->mPage;
	} // End LoadDefault

	//! Loads PackageDetailView
	function LoadMainContentColumn() {
		$packageDetailView = new PackageDetailView ( );
		$this->mPage .= $this->mPublicLayoutHelper->OpenMainColumn ();
		$this->mPage .= $packageDetailView->LoadDefault ( $this->mPackage, $this->mSessionHelper->GetBasket ()->GetBasketId () );
		$this->mPage .= $this->mPublicLayoutHelper->CloseMainColumn ();
	} // End LoadMainContentColumn()

} // End PackageView

?>