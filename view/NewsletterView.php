<?php

class NewsletterView extends View {

	function LoadDefault() {
		$this->mPage .= <<<HTMLOUTPUT
					<!-- Start Section NEWSLETTER -->
			<div class="section">
				<div class="sectionHeader">
					NEWSLETTER
				</div>
				<div class="sectionBody">
					<form method="post" action="{$this->mFormHandlersDir}/NewsletterViewHandler.php" id="newsletterSignUp">
						<div>
						<input type="text" name="signUpEmail" id="signUpEmail" value="email address" />
						<input type="image" src="{$this->mBaseDir}/images/subscribeButton.png" id="subscribeButton" name="subscribeButton" />
						</div>
					</form>
				</div>
			</div> <!-- End section - NEWSLETTER -->

HTMLOUTPUT;
		return $this->mPage;
	}
}

?>