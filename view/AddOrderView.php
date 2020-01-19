<?php
require_once ('../autoload.php');

class AddOrderView extends AdminView {
	
	var $mDatabase;
	var $mBasketController;
	var $mCatalogueId;
	var $mBasket;
	
	function __construct() {
		parent::__construct(true);
		$this->IncludeCss('admin/css/AddOrderView.css.php',false);
		$this->IncludeJavaScript('AddOrderView.js');
		$this->mBasketController = new BasketController ( );
		$this->mReferrerController = new ReferrerController ( );
	}
	
	function LoadDefault() {
		$this->LoadExistingCustomerChoice ();
		return $this->mPage;
	}
	
	function LoadExistingCustomerChoice() {
		$secureViewDir = str_replace ( 'http', 'https', $this->mViewDir );
		$catalogueController = new CatalogueController ( );
		$allCatalogues = $catalogueController->GetAllCatalogues ();
		$this->mPage .= <<<EOT
			<div style="width: 500px; text-align: center; border: 1px solid #aaa; padding-top: 20px;">
			<strong>Existing Customer?</strong><br /><br />
			<form action="{$secureViewDir}/AddOrderView.php" id="newCustomerChoiceForm" name="newCustomerChoiceForm" method="post">
			Catalogue: 
			<select name="catalogue">
EOT;
		foreach ( $allCatalogues as $catalogue ) {
			$this->mPage .= '<option value="' . $catalogue->GetCatalogueId () . '">' . $catalogue->GetDisplayName () . '</option>';
		}
		
		$this->mPage .= <<<EOT
		</select><br /><br />
				<input type="submit" name="yes" id="yes" value="Yes" />
				<input type="submit" name="no" id="no" value="No" />				
			</form>
		</div>
EOT;
	}
	
	function LoadAddressSearch() {
		$secureViewDir = str_replace ( 'http', 'https', $this->mViewDir );
		$this->mPage .= '
		<div name="addressSearchContainer" id="addressSearchContainer">
			<iframe src="' . $secureViewDir . '/AddressSearchView.php" name="addressSearch" id="addressSearch" frameborder="0" border="0"></iframe>
		</div>';
	}
	
	function InitialiseANewBasket() {
		session_regenerate_id ();
		$this->mBasket = $this->mBasketController->CreateBasket ( session_id () );
	}
	
	function LoadNewCustomerOrderForm($catalogue) {
		$this->mCatalogueId = $catalogue;
		$this->InitialiseANewBasket ();
		$registry = Registry::getInstance ();
		$secureFormsDir = str_replace ( 'http', 'https', $registry->formHandlersDir );
		
		// Referrer Drop-down
		$allReferrers = $this->mReferrerController->GetAllReferrers ();
		$referrerDropDown = '<label for="referrer">Referrer</label>';
		$referrerDropDown .= '<select name="referrer" id="referrer"><option value="NA"></option>';
		foreach ( $allReferrers as $referrer ) {
			$referrerDropDown .= '<option value="' . $referrer->GetReferrerId () . '">' . $referrer->GetDescription () . '</option>';
		}
		$referrerDropDown .= '</select><br />';
		
		// Create page
		$this->mPage .= <<<EOT
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
			<form action="{$secureFormsDir}/NewCustomerOrderHandler2.php?sessionId={$this->mBasket->GetBasketId()}" method="post" name="newCustomerOrderForm" id="newCustomerOrderForm"  target="ordersEdit">
			<input type="hidden" name="vatFreeOrder" id="vatFreeOrder" value="0" />
			<label for="customerName">Name: </label>
				<input type="text" name="customerName" id="customerName" autocomplete="off" /><br />
			<label for="delivery1">Delivery Address: </label>
				<input type="text" name="delivery1" id="delivery1" autocomplete="off"  /><br />
			<label>&nbsp;</label>
				<input type="text" name="delivery2" id="delivery2" autocomplete="off"  /><br />
			<label>&nbsp;</label>
				<input type="text" name="delivery3" id="delivery3" autocomplete="off"  /><br />
			<label for="county">County</label>
				<input type="text" name="county" id="county" autocomplete="off"  /><br />
			<label for="deliveryPostcode">Postcode</label>
				<input type="text" name="deliveryPostcode" id="deliveryPostcode" autocomplete="off"  /><br />
EOT;
		$countryController = new CountryController ( );
		$allCountries = $countryController->GetAll ();
		$this->mPage .= '<label for="countryDropDownMenu">Country</label><select name="countryDropDownMenu" id="countryDropDownMenu">';
		foreach ( $allCountries as $country ) {
			if ($country->GetThreeLetter () == 'GBR') {
				$this->mPage .= '<option value="' . $country->GetCountryId () . '" selected="selected">' . $country->GetShortDescription () . '</option>';
			} else {
				$this->mPage .= '<option value="' . $country->GetCountryId () . '">' . $country->GetShortDescription () . '</option>';
			}
		}
		$this->mPage .= '</select><br />';
		$this->mPage .= <<<EOT
			<label for="same">Same as Delivery?</label>
				<input type="checkbox" name="same" id="same" /><br />
			<label for="billing1">Billing Address: </label>
				<input type="text" name="billing1" id="billing1" autocomplete="off"  /><br />
			<label for="billingPostcode">Postcode: </label>
				<input type="text" name="billingPostcode" id="billingPostcode" autocomplete="off"  /><br />
			{$referrerDropDown}
			<label for="notes">Notes: </label>
				<input type="text" name="notes" id="notes" autocomplete="off"  /><br />

EOT;
		$this->LoadContentsDisplay ();
		$this->mPage .= <<<EOT
			</form>
EOT;
		return $this->mPage;
	}
	
	//! Loads the order contents section
	function LoadContentsDisplay() {
		$orderContentsView = new OrderContentsView ( );
		$this->mPage .= '<div>';
		$this->mPage .= $orderContentsView->LoadDefault ( $this->mCatalogueId );
		$this->mPage .= '<div id="orderContentsList">';
		$this->mPage .= '</div>'; // End packageContentsList
		$this->mPage .= <<<EOT
		<br />
		<div id="orderFormFooter">
			<!-- <div id="orderAdjustment">
				<label for="orderAdjustmentTotal">Adjust Total To: £</label> 
				<input type="text" id="orderAdjustmentTotal" name="orderAdjustmentTotal" onchange="CalculateTotalPrice('ORDERCONTENTS')" value="0" />
			</div> -->
			<div id="orderPostage">
				<label for="orderPostageTotal">Postage: £</label> 
				<input type="text" id="orderPostageTotal" name="orderPostageTotal" onchange="CalculateTotalPrice('ORDERCONTENTS')" value="0" />&nbsp;&nbsp;<a href="{$this->mBaseDir}/postalRates" target="_blank">[?]</a>
			</div>
			<div id="orderTotal">
				<label for="orderTotalPrice">Total: £</label> 
				<input type="text" id="orderTotalPrice" name="orderTotalPrice" readonly="true" style="background-color: #D5E1E8; color: #000; font-weight: bold;" value="0" />			
			</div>
		</div>
		<br />		
		<div id="cardDetailsLeft">
		<label for="cardHoldersName">Name on Card</label>
			<input type="text" id="cardHoldersName" name="cardHoldersName" style="width: 150px" autocomplete="off" /><br />		
		<label for="cardType">Card Type</label>
			<select name="cardType" id="cardType" style="width: 150px">
				<option>Maestro</option>
				<option>Mastercard</option>
				<option>Solo</option>
				<option>Switch</option>
				<option selected="selected">Visa</option>
				<option>Visa Electron</option>
			</select><br />
		<label for="cardNumber">Card Number</label>
			<input type="text" name="cardNumber" id="cardNumber" style="width: 150px" maxlength="19" autocomplete="off"  /><br />
		<label for="validFromMonth">Valid From</label>
			<select name="validFromMonth" id="validFromMonth">
EOT;
		for($i = 1; $i < 13; $i ++) {
			if ($i < 10) {
				$month = '0' . $i;
			} else {
				$month = $i;
			}
			$this->mPage .= '<option>' . $month . '</option>';
		}
		$this->mPage .= <<<EOT
			</select>
			<select name="validFromYear" id="validFromYear">
EOT;
		$currentTime = time ();
		for($i = 1; $i < 10; $i ++) {
			$this->mPage .= '<option>' . date ( 'Y', $currentTime ) . '</option>';
			$currentTime = $currentTime - 31556926; // Number of seconds in a year = 31556926, use 32556926 to overshoot it a bit (11 days ish) to compensate on new years eve
		}
		$this->mPage .= <<<EOT
			</select><br />
		<label for="expiryDateMonth">Expiry Date</label>
			<select name="expiryDateMonth" id="expiryDateMonth">
EOT;
		for($i = 1; $i < 13; $i ++) {
			if ($i < 10) {
				$month = '0' . $i;
			} else {
				$month = $i;
			}
			$this->mPage .= '<option>' . $month . '</option>';
		}
		$this->mPage .= <<<EOT
			</select>
			<select name="expiryDateYear" id="expiryDateYear">
EOT;
		$currentTime = time ();
		for($i = 1; $i < 10; $i ++) {
			$this->mPage .= '<option>' . date ( 'Y', $currentTime ) . '</option>';
			$currentTime = $currentTime + 31556926; // Number of seconds in a year
		}
		$this->mPage .= <<<EOT
			</select>					
		</div>
		<div id="cardDetailsRight">		
		<label for="issueNumber" id="issueNoLabel" name="issueNoLabel">Issue No.</label> <input type="text" name="issueNumber" id="issueNumber" maxlength="2" style="width: 20px" autocomplete="off" >
		<label for="cardVerificationNumber" id="cvnLabel">CVN.</label> <input type="text" name="cardVerificationNumber" id="cardVerificationNumber" maxlength="3" autocomplete="off"  style="width: 30px">		
		</div>	
		<label for="telephoneNumber">Telephone No.</label>
			<input type="text" name="telephoneNumber" id="telephoneNumber" autocomplete="off"  /><br />
		<label for="emailAddress">Email Address</label>
			<input type="text" name="emailAddress" id="emailAddress" autocomplete="off"  /><br />
		<label for="brochure">Brochure?</label>
			<input type="checkbox" name="brochure" id="brochure" /><br />
		<label for="staffName">Staff Name</label>
			<select name="staffName" id="staffName" />
				<option>Chris</option>
				<option>Dave</option>
				<option>Holly</option>
				<option>Jackson</option>
				<option>Jake</option>
				<option>JP</option>
				<option>Mitch</option>
				<option>Phil</option>
				<option>Sam</option>
				<option>Scott</option>
			</select><br />
			<label for="staffNotes"><strong>Staff</strong> Notes: </label>
				<input type="text" name="staffNotes" id="staffNotes" autocomplete="off"  />			
		<br /><input type="submit" value="Place Order" /><br /><br />
		<div style="border: 2px solid #F00; width: 400px; margin: 5px; padding: 5px; display: none;" id="error" name="error"></div>
EOT;
		$this->mPage .= '</div>'; // End contentsContentArea
	}

}

$page = new AddOrderView ( );


if (! isset ( $_POST ['yes'] ) && ! isset ( $_POST ['no'] )) {
	echo $page->LoadDefault ();
} elseif (isset ( $_POST ['yes'] )) {
	echo $page->LoadAddressSearch ();
	echo $page->LoadNewCustomerOrderForm ( $_POST ['catalogue'] );
} elseif (isset ( $_POST ['no'] )) {
	echo $page->LoadNewCustomerOrderForm ( $_POST ['catalogue'] );
}

?>