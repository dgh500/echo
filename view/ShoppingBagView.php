<?php

class ShoppingBagView extends View {
	
	function __construct() {
		parent::__construct ();
		if ($this->mRegistry->hasAdmin && isset($_SERVER['HTTPS'])) {
			$this->mDir = $this->mSecureBaseDir;
		} else {
			$this->mDir = $this->mBaseDir;
		}
		
	}
	
	function LoadDefault($basket) {
		$this->mPage .= <<<HTMLOUTPUT
		
			<div id="shoppingBagContainer">
				<img src="{$this->mDir}/images/shoppingBagTitle.gif" id="shoppingBagTitle" alt="Shopping Bag" />
				<div id="shoppingBagContent">
					<div id="shoppingBagItems"><strong>Items in Bag:</strong> {$basket->GetNumberOfItems()} Items</div>
					<div id="shoppingBagTotal"><strong>Total:</strong> &pound;{$this->mPresentationHelper->Money($basket->GetTotal())}</div>
				</div> <!-- Close shoppingBagContent -->
				<a href="{$this->mBaseDir}/basket"><img src="{$this->mDir}/images/shoppingBagCheckoutNow.gif" id="shoppingBagCheckoutNow" alt="Checkout Now" /></a>
			</div> <!-- Close shoppingBagContainer -->
			
HTMLOUTPUT;
		return $this->mPage;
	}

}

?>