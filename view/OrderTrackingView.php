<?php
//! The order tracking page (top account nav)
class OrderTrackingView extends View {

	var $mSessionHelper;

	function __construct($catalogue) {
		// Params
		$this->mCatalogue = $catalogue;

		// Includes
		$cssIncludes = array('OrderTrackingView.css.php');
		$jsIncludes = array('OrderTrackingView.js');

		// Construct
		parent::__construct($this->mCatalogue->GetDisplayName () . ' > Order Tracking',$cssIncludes,$jsIncludes);

		// Member vars
		$this->mSessionHelper = new SessionHelper ( );
		$this->mCustomerController = new CustomerController ( );
	}

	//! Generic load function
	function LoadDefault() {
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
	}

	function LoadMainContentColumn() {
		$this->mPage .= $this->mPublicLayoutHelper->OpenMainColumn ();
		if ($this->mSessionHelper->GetTrackingStatus ()) {
			switch ($this->mSessionHelper->GetTrackingStatus ()) {
				case 'success' :
					$this->LoadOrderStatus ( $this->mSessionHelper->GetTrackingId () );
					$this->mSessionHelper->SetTrackingStatus ( false );
					break;
				case 'failure' :
					$this->LoadTrackingForm ( 'No order exists.' );
					break;
				default :
					$this->LoadTrackingForm ();
					break;
			}
		} else {
			$this->LoadTrackingForm ();
		}
		$this->mPage .= $this->mPublicLayoutHelper->CloseMainColumn ();
	} // End LoadMainContentColumn()


	function LoadOrderStatus($id) {
		$publicOrderView = new PublicOrderView ( );
		$orderTrackingExplanationView = new OrderTrackingExplanationView;
		$this->mPage .= '<h1><a href="">Order Tracking</a> > Order ECHO' . $id . '</h1>';
		$this->mPage .= $orderTrackingExplanationView->LoadDefault();
		$this->mPage .= $publicOrderView->LoadDefault ( $id );
	}

	function LoadTrackingForm($error = '') {
		$this->mPage .= <<<EOT
			{$error}
			<form name="orderTrackingForm" id="orderTrackingForm" action="{$this->mFormHandlersDir}/OrderTrackingHandler.php" method="post" onsubmit="return validateTrack(this)">
				<h1 id="orderTrackingLogo"><span></span>Order Tracking</h1>
				<p>Please enter your email address and order number below to track your order</p>
				<label for="trackEmail">Email Address: </label>
					<input type="text" name="trackEmail" id="trackEmail" /><br />
				<label for="trackOrderId">Order ID: </label>
					<input type="text" name="trackOrderId" id="trackOrderId" /><br />
				<label for="submit">&nbsp;</label>
				<input type="image" src="{$this->mBaseDir}/images/trackButton.gif" id="submit" />
				<div id="error"></div>
			</form>

EOT;
	}

	//! Loads the right column, including RightColView
	function LoadRightColumn() {
		$rightColView = new RightColView ( $this->mCatalogue, $this->mSessionHelper );
		$this->mPage .= $rightColView->LoadDefault ();
	}
} // End OrderTrackingView

?>