<?php
require_once ('../autoload.php');

//! New, AJAX-Based Telephone Order Form
class AddOrderCustomerTabView extends AdminView {

	function __construct() {
		$this->mSessionHelper = new SessionHelper;
		$cssIncludes = array('jqueryUI.css','jquery.tooltip.css','AddOrderView2.css.php','AddressSearchView2.css.php');
		$jsIncludes  = array('jqueryUi.js','jquery.tooltip.min.js','AddOrderView2.js');
		parent::__construct('1',$cssIncludes,$jsIncludes);

		// Initialise customer
		$this->mCustomer = new CustomerModel($_SESSION['customerId'],'id');
		$this->mBilling  = new AddressModel($_SESSION['billingId']);
		$this->mDelivery = new AddressModel($_SESSION['deliveryId']);
		$this->mSecureViewDir = str_replace('http','https',trim($this->mRegistry->viewDir));
	}

	function LoadDefault() {

	// Make current title selected
	$currentTitle = $this->mCustomer->GetTitle();
	$allTitles = array('Mr','Mrs','Miss','Ms','Dr','Prof','Rev','Lord');
	$titleDropdown = '<select name="title" id="title">';
	foreach($allTitles as $title) {
		if($title == $currentTitle) {
			$sel = ' selected = "selected"';
		} else { $sel = ''; }
		$titleDropdown .= '<option value="'.$title.'" '.$sel.'>'.$title.'</option>';
	}
	$titleDropdown .= '</select>';

	// Referrer Section
	$this->mReferrerController = new ReferrerController();
	$allReferrers = $this->mReferrerController->GetAllReferrers ();
	$referrerDropDown = '<label for="referrerId" id="referrerIdLabel">Referrer </label>';
	$referrerDropDown .= '<select name="referrerId" id="referrerId" class="required"><option value="NA"></option>';
	foreach ( $allReferrers as $referrer ) {
		if(isset($_SESSION['referrerId'])) {
			if($_SESSION['referrerId'] == $referrer->GetReferrerId()) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}
		} else {
			$selected = '';
		}
		$referrerDropDown .= '<option value="'.$referrer->GetReferrerId().'" '.$selected.'>'.$referrer->GetDescription().'</option>';
	}
	$referrerDropDown .= '</select><br />';

	// Misc Section
	if(isset($_SESSION['catalogueWanted']) && $_SESSION['catalogueWanted'] == 1) {
		$catalogueWantedVal = 'checked="yes"';
	} else {
		$catalogueWantedVal = '';
	}
	(!isset($_SESSION['notes']) ? $notes = '' : $notes = $_SESSION['notes']);

	// Staff Section
	$staff = array('Dave','Holly');
	$staffOptions = '';
	foreach($staff as $staffName) {
		if(isset($_SESSION['staffName'])) {
			if($_SESSION['staffName'] == $staffName) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}
		} else {
			$selected = '';
		}
		$staffOptions .= '<option '.$selected.'>'.$staffName.'</option>';
	}

	$this->mPage .= <<<EOT
	<div id="customerLookupDialog">

			<div id="searchBarContainer">
				<form method="get" action="" name="addressSuggestForm" id="addressSuggestForm">
					<strong style="margin-left: 50px;">Search&nbsp;</strong>
					<input type="text" name="addressSearchText" id="addressSearchText" autocomplete="off" />
					<input type="hidden" name="id" id="id" /><input type="hidden" name="line1" id="line1" />
					<input type="hidden" name="line2" id="line2" /><input type="hidden" name="line3" id="line3" />
					<input type="hidden" name="selectedCustomerName" id="selectedCustomerName" />
					<input type="hidden" name="selectedCustomerEmail" id="selectedCustomerEmail" />
					<input type="hidden" name="selectedCustomerPhone" id="selectedCustomerPhone" />
					<input type="hidden" name="selectedCity" id="selectedCity" /><input type="hidden" name="selectedCounty" id="selectedCounty" />
					<input type="hidden" name="selectedPostcode" id="selectedPostcode" />
					on
					<select name="method" id="method">
						<option value="postcode">Postcode</option>
					</select>
				</form>
				<div id="suggestions"></div>
			</div>

	</div>
	<div id="customerTab">
		<form action="{$this->mFormHandlersDir}/AddOrderCustomerFormHandler.php" method="post" id="addOrderCustomerForm" name="addOrderCustomerForm">
		<input type="hidden" name="customerId" id="customerId" value="{$this->mCustomer->GetCustomerId()}" />
		<input type="hidden" name="deliveryId" id="deliveryId" value="{$this->mDelivery->GetAddressId()}" />
		<input type="hidden" name="billingId" id="billingId" value="{$this->mBilling->GetAddressId()}" />
		<h2><a id="customerLink" href="#">Customer</a></h2>
		<label for="title">Title: </label>
			{$titleDropdown}<br />
		<label for="firstName" id="firstNameLabel">First Name </label>
			<input type="text" name="firstName" id="firstName" autocomplete="off" value="{$this->mCustomer->GetFirstName()}" class="required" /><br />
		<label for="lastName" id="lastNameLabel">Last Name </label>
			<input type="text" name="lastName" id="lastName" autocomplete="off"  value="{$this->mCustomer->GetLastName()}"  class="required" /><br />
		<label for="email" id="emailLabel">Email </label>
			<input type="text" name="email" id="email" autocomplete="off" value="{$this->mCustomer->GetEmail()}" class="notRequired" /><br />
		<label for="telNo" id="telNoLabel">Telephone No. </label>
			<input type="text" name="telNo" id="telNo" autocomplete="off" value="{$this->mCustomer->GetDaytimeTelephone()}" class="required" /><br />
		<label for="mobNo" id="mobNoLabel">Mobile No. </label>
			<input type="text" name="mobNo" id="mobNo" autocomplete="off" value="{$this->mCustomer->GetMobileTelephone()}" class="notRequired" /><br />

		<h2>Delivery Address</h2>
		<label for="company" id="companyLabel">Company</label>
			<input type="text" name="company" id="company" autocomplete="off" value="{$this->mDelivery->GetCompany()}" class="notRequired" /><br />
		<label for="delivery1" id="delivery1Label">Delivery Address </label>
			<input type="text" name="delivery1" id="delivery1" autocomplete="off" value="{$this->mDelivery->GetLine1()}" class="required" /><br />
		<label id="delivery2Label">Delivery Line 2 </label>
			<input type="text" name="delivery2" id="delivery2" autocomplete="off" value="{$this->mDelivery->GetLine2()}" class="required" /><br />
		<label>&nbsp;</label>
			<input type="text" name="delivery3" id="delivery3" autocomplete="off" value="{$this->mDelivery->GetLine3()}" class="notRequired" /><br />
		<label for="county" id="countyLabel">County </label>
			<input type="text" name="county" id="county" autocomplete="off" value="{$this->mDelivery->GetCounty()}" class="required" /><br />
		<label for="deliveryPostcode" id="deliveryPostcodeLabel">Delivery Postcode </label>
			<input type="text" name="deliveryPostcode" id="deliveryPostcode" autocomplete="off" value="{$this->mDelivery->GetPostcode()}" class="required" /><br />
		<h2>Billing Address</h2>
		<label for="billing1" id="billing1Label">Billing Address </label>
			<input type="text" name="billing1" id="billing1" autocomplete="off" value="{$this->mBilling->GetLine1()}" class="required" /><br />
		<label for="billingPostcode" id="billingPostcodeLabel">Billing Postcode </label>
			<input type="text" name="billingPostcode" id="billingPostcode" autocomplete="off" value="{$this->mBilling->GetPostcode()}" class="required" /><br />
		<h2>Misc Details</h2>
		{$referrerDropDown}
		<label for="notes" id="notesLabel">Notes</label>
			<input type="text" name="notes" id="notes" autocomplete="off" value="{$notes}" class="notRequired" /><br />
		<label for="staffName">Staff Name</label>
			<select name="staffName" id="staffName">
				{$staffOptions}
			</select><br />
		<label for="catalogueWanted">Catalogue Wanted? </label>
			<input type="checkbox" name="catalogueWanted" id="catalogueWanted" {$catalogueWantedVal} /><br />


		<!-- This is reset on submit -->
		<input type="hidden" name="referrerTab" id="referrerTab" />
		</form>
	</div>
EOT;
	return $this->mPage;
	} // End LoadDefault();

} // End AddOrderCustomerTabView

$page = new AddOrderCustomerTabView;
echo $page->LoadDefault();

?>