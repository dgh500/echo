<?php
//! Defines the account navigation bar (Home | Contact | My Account | Checkout | Order Tracking | Returns )
class AccountNavigationView extends View {

	function __construct() {
		parent::__construct ();
		if ($this->mRegistry->hasAdmin && isset($_SERVER['HTTPS'])) {
			$this->mDir = $this->mSecureBaseDir;
		} else {
			$this->mDir = $this->mBaseDir;
		}
	}

	//! Default load function
	/*!
	 * @param $basket [in] - BasketModel - The current basket
	 * @return String [in] - The HTML code for the nav bar
	 */
	function LoadDefault($basket) {
		if($basket) {
			// Basket is active if it has something in it
			if($basket->GetNumberOfItems() > 0) {
				$basketClass = 'topRightNavActive';
			} else {
				$basketClass = 'topRightNav';
			}

			// Generate HTML
			$this->mPage .= <<<HTMLOUTPUT
		<div id="{$basketClass}">
			 <a href="{$this->mBaseDir}/myAccount.php">My Account</a> | <a href="{$this->mBaseDir}/basket">Basket: {$basket->GetNumberOfItems()} Items, Total &pound;{$this->mPresentationHelper->Money($basket->GetTotal())}</a>
		</div>

HTMLOUTPUT;
			return $this->mPage;
		}
	}

}

?>