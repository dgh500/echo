<?php

class FeedbackView extends View {
	
	function __construct($catalogue) {
		$this->mCatalogue = $catalogue;
		$cssIncludes = array('FeedbackView.css.php');
		$jsIncludes = array('jquery.corners.min.js','FeedbackView.js');
		parent::__construct($this->mCatalogue->GetDisplayName().' Feedback',$cssIncludes,$jsIncludes);	
		$this->mSessionHelper = new SessionHelper();
	}
	
	function LoadDefault() {
		if (isset ( $_GET ['failure'] )) {
			$this->mFailureDisplay = true;
			$this->mFailureType = $_GET ['failure'];
		} else {
			$this->mFailureDisplay = false;
		}		
		$footerView = new FooterView ( );
		$this->mPage .= $this->mPublicLayoutHelper->OpenBody();
		$this->mPage .= $this->mPublicLayoutHelper->AddTopRelativeAnchor ();
		$this->mPage .= $this->mPublicLayoutHelper->OpenCentrePageContainer ();
		parent::LoadHeaderSection ($this->mCatalogue);
		$this->mPage .= $this->mPublicLayoutHelper->OpenLayoutContainers ();
		parent::LoadLeftColumn ($this->mCatalogue);
		$this->mPage .= $this->mPublicLayoutHelper->OpenCentreColumn ();
		
		// Only display the form if there is no failure!
		if ($this->mFailureDisplay) {
		$this->mPage .= <<<EOT
			<form id="feedbackForm">
				<fieldset>
EOT;
			
			switch ($this->mFailureType) {
				case 'send' :
					$this->mPage .= '<legend>Feedback</legend><strong>There was a failure sending the request.</strong>';
					break;
				case 'valid' :
					$this->mPage .= '<legend>Feedback</legend><strong>There was a failure with the details you provided.</strong>';
					break;
				case 'none' :
					$this->mPage .= '<legend>Feedback</legend><strong>Your feedback has been received - thank you!</strong>';
					break;
			}
		$this->mPage .= <<<EOT
				</fieldset>
			</form>
EOT;
			
		} else {
			$this->mPage .= $this->LoadMainContentColumn();
		}

		$this->mPage .= $this->mPublicLayoutHelper->CloseCentreColumn ();
		$this->LoadRightColumn();
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
		$this->mPage .= <<<EOT
			<form action="{$this->mFormHandlersDir}/FeedbackHandler.php" method="post" id="feedbackForm" name="feedbackForm">
				<fieldset>
					<legend>Feedback</legend>
					<br />
					<label for="feedbackName">Your Name:</label>
					<input id="feedbackName" name="feedbackName" type="text" /><br />
					<label for="feedbackEmail">Your Email:</label>
					<input id="feedbackEmail" name="feedbackEmail" type="text" /><br />
					<label for="feedbackText">Feedback:</label>
					<textarea id="feedbackText" name="feedbackText">Please enter any details you feel would help us improve the website!</textarea><br />
					<input type="submit" value="Submit Feedback" class="submit" /><br />
				</fieldset>
				<div id="errorBox"></div>
			</form>
EOT;
	}

	//! Loads the right column, including RightColView
	function LoadRightColumn() {
		$rightColView = new RightColView ( $this->mCatalogue, $this->mSessionHelper );
		$this->mPage .= $rightColView->LoadDefault ();
	}
}

?>