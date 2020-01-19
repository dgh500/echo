<?php

//! Loads the left column on the checkout pages
class CheckoutLeftColView extends View {
	
	//! Generic loader
	function LoadDefault() {
		// Load the page
		$this->mPage .= '
				<div id="leftNavContainer">
					<div style="background-image: url('.$this->mSecureBaseDir.'/images/chkLeftColMidSection.gif); height: 22px; background-repeat: no-repeat; "></div>
					<div id="shopByBrandContainer">
					<img src="'.$this->mSecureBaseDir.'/images/chkCheckoutIcon.gif" />
					</div><br style="clear: both" />
				</div> <!-- Close leftNavContainer -->
				';
		return $this->mPage;
	} // End LoadDefault
} // End CheckoutLeftColView

?>