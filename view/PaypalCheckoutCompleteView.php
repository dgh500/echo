<?php

//! Loads the paypal receipt view
class PaypalCheckoutCompleteView extends View {

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
		parent::__construct ('Paypal Checkout Complete');
		$this->mCatalogue = $catalogue;
		$this->mSessionHelper 		= new SessionHelper ( );
		$this->mPublicLayoutHelper 	= new PublicLayoutHelper ( );
		$this->mSystemSettings 		= new SystemSettingsModel ( $this->mCatalogue );
		$this->mBasketId 			= $this->mSessionHelper->GetSessionId ();
	}

	//! Main page load function
	function LoadDefault($result) {
		$this->mResult = $result;
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

	//! Loads the centre column
	function LoadMainContentColumn() {
		if($this->mResult == 'fail') {
			$this->mPage .= '<h2>PAYPAL CHECKOUT FAILED</h2>';
			$this->mPage .= 'Please give us a call on <b>01753 572741</b> and we will look into this and take your order!';
		} else {
			$trackingCode = '<!-- Google Code for Echo Checkout Complete Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 994440035;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "8RwOCNWM5ggQ4-aX2gM";
var google_conversion_value = 0;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/994440035/?value=0&amp;label=8RwOCNWM5ggQ4-aX2gM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>';

			$this->mPage .= '<h2>Paypal Checkout Successful</h2>';
			$this->mPage .= '<p>Thanks for ordering with Echo Supplements!<br />You will receive an order receipt from PayPal - this is your order confirmation.</p>';
			$this->mPage .= $trackingCode;
		}
	} // End LoadMainContentColumn


}
?>