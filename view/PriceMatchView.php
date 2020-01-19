<?php
//! Price match page
class PriceMatchView extends View {
	
	var $mSessionHelper;
	
	function __construct($catalogue) {
		// Params
		$this->mCatalogue = $catalogue;
		
		// Includes	
		$cssIncludes = array('PriceMatchView.css.php');
		$jsIncludes = array('PriceMatchView.js');
		
		// Construct
		parent::__construct($this->mCatalogue->GetDisplayName().' > Price Match Guarantee',$cssIncludes,$jsIncludes);
		// Member Vars
		$this->mSessionHelper = new SessionHelper ( );
	}
	
	function LoadDefault() {
		if (isset ( $_GET ['failure'] )) {
			$this->mFailureDisplay = true;
			$this->mFailureType = $_GET ['failure'];
		} else {
			$this->mFailureDisplay = false;
		}
		$footerView = new FooterView ( );
		$this->mPage .= $this->mPublicLayoutHelper->OpenBody ();
		$this->mPage .= $this->mPublicLayoutHelper->AddTopRelativeAnchor ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenCentrePageContainer ();
		parent::LoadHeaderSection ($this->mCatalogue);
		$this->mPage .= $this->mPublicLayoutHelper->OpenLayoutContainers ();
		parent::LoadLeftColumn ($this->mCatalogue);
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
		if ($this->mFailureDisplay) {
			switch ($this->mFailureType) {
				case 'send' :
					$this->mPage .= '<h1>Price Match Request</h1>There was a failure sending the request.';
					break;
				case 'valid' :
					$this->mPage .= '<h1>Price Match Request</h1>There was a failure with the details you provided.';
					break;
				case 'none' :
					$this->mPage .= '<h1>Price Match Request</h1>Your request has been received and your request will be dealt with soon!';
					break;
			}
		} else {
			$this->mPage .= $this->LoadPriceMatch ();
		}
		$this->mPage .= $this->mPublicLayoutHelper->CloseMainColumn ();
	} // End LoadMainContentColumn()
	

	function LoadPriceMatch() {
		$this->mPage .= '<div id="priceMatchContainer">';
		$this->mPage .= '<h1>Price Match</h1><br />';
		$this->mPage .= <<<EOT
			<form method="post" action="{$this->mBaseDir}/formHandlers/priceMatchHandler.php" onsubmit="return validatePriceMatch(this)">
				<label for="productName">Product: </label>
					<input type="text" name="productName" id="productName" /><br />
				<label for="personName">Your Name: </label>
					<input type="text" name="personName" id="personName" /><br />
				<label for="personTel">Telephone Number: </label>
					<input type="text" name="personTel" id="personTel" /><br />
				<label for="personEmail">Email Address: </label>
					<input type="text" name="personEmail" id="personEmail" /><br />
				<label for="competitorsPrice">Competitors Price: </label>
					<input type="text" name="competitorsPrice" id="competitorsPrice" /><br />
				<label for="ourPrice">Our Price: </label>
					<input type="text" name="ourPrice" id="ourPrice" /><br />
				<label for="whereSeen">Where have you seen this? </label>
					<input type="text" name="whereSeen" id="whereSeen" /><br />
				<input type="submit" value="Send Price Match" class="submit" /><br /><br />
					Once we have recieved your request we will call you with our price! Please note we can only match UK websites and do <strong>not</strong> match eBay auctions.
				<br /><br />
					<div id="error"></div>	
			</form>
EOT;
		$this->mPage .= '</div>';
	}
	//! Loads the right column, including RightColView
	function LoadRightColumn() {
		$rightColView = new RightColView ( $this->mCatalogue, $this->mSessionHelper );
		$this->mPage .= $rightColView->LoadDefault ();
	}
} // End PriceMatchView

?>