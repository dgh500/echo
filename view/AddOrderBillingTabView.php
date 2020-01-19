<?php
require_once ('../autoload.php');

class AddOrderBillingTabView extends AdminView {

	function __construct() {
		$cssIncludes = array('jqueryUI.css','jquery.tooltip.css','AddOrderView2.css.php');
		$jsIncludes  = array('jqueryUi.js','jquery.tooltip.min.js','AddOrderView2.js');
		parent::__construct('1',$cssIncludes,$jsIncludes);

		$this->mCustomer = new CustomerModel($_SESSION['customerId'],'id');
	}

	function LoadDefault() {

	// Load customers name as card holders name if none already there
	$cardHoldersName = $this->LoadCardHoldersName();

	// Card Type
	$cardTypeDropdown = $this->LoadCardType();

	// Card Number
	(!isset($_SESSION['cardNumber']) ? $cardNumber = '' : $cardNumber = $_SESSION['cardNumber']);

	// Valid From/To
	$validFromMonth = $this->LoadValidFromMonth();
	$validFromYear 	= $this->LoadValidFromYear();

	// Expiry Date
	$expiryDateMonth = $this->LoadExpiryMonth();
	$expiryDateYear  = $this->LoadExpiryYear();

	// CVN
	(!isset($_SESSION['cardVerificationNumber']) ? $cardVerificationNumber = '' : $cardVerificationNumber = $_SESSION['cardVerificationNumber']);

	// Issue
	(!isset($_SESSION['issueNumber']) ? $issueNumber = '' : $issueNumber = $_SESSION['issueNumber']);

	// Display the page
	$this->mPage .= <<<EOT
	<div id="billingTab">
		<form action="{$this->mFormHandlersDir}/AddOrderBillingFormHandler.php" method="post" id="addOrderBillingForm" name="addOrderBillingForm">
		<h2>Card Details</h2>
		<label for="cardHoldersName" id="cardHoldersNameLabel">Card Holders Name </label>
			<input type="text" id="cardHoldersName" name="cardHoldersName" style="width: 150px" autocomplete="off" value="{$cardHoldersName}" class="required" /><br />
		<label for="cardType" id="cardTypeLabel">Card Type</label>
			{$cardTypeDropdown}<br />
		<label for="cardNumber" id="cardNumberLabel">Card Number </label>
			<input type="text" name="cardNumber" id="cardNumber" maxlength="19" autocomplete="off" value="{$cardNumber}" class="required"  /><br />
		<label for="validFromMonth">Valid From</label>
			{$validFromMonth}
			{$validFromYear}
		<label for="expiryDateMonth">Expiry Date</label>
			{$expiryDateMonth}
			{$expiryDateYear}
		<label for="cardVerificationNumber" id="cardVerificationNumberLabel">CVN </label>
			<input type="text" name="cardVerificationNumber" id="cardVerificationNumber" maxlength="3" autocomplete="off" value="{$cardVerificationNumber}" class="required"  />
		<label for="issueNumber" id="issueNumberLabel">Issue </label>
			<input type="text" name="issueNumber" id="issueNumber" maxlength="2" autocomplete="off" value="{$issueNumber}"  /><br />

		</form>
	</div>
EOT;
	return $this->mPage;
	} // End LoadDefault();

	function LoadCardHoldersName() {
		#if(!isset($_SESSION['cardHoldersName']) || trim($_SESSION['cardHoldersName']) == '') {
			$str = $this->mCustomer->GetTitle().' '.$this->mCustomer->GetFirstName().' '.$this->mCustomer->GetLastName();
		#} else {
		#	$str = $_SESSION['cardHoldersName'];
		#}
		return $str;
	}

	function LoadCardType() {
		// Card Type
		if(isset($_SESSION['cardType'])) {
			$currentType = $_SESSION['cardType'];
		} else {
			$currentType = 'Visa';
		}
		$allTypes = array('Maestro','Mastercard','Solo','Switch','Visa','Visa Electron');
		$str = '<select name="cardType" id="cardType" class="required">';
		foreach($allTypes as $cardType) {
			if($cardType == $currentType) {
				$sel = ' selected = "selected"';
			} else { $sel = ''; }
			$str .= '<option value="'.$cardType.'" '.$sel.'>'.$cardType.'</option>';
		}
		$str .= '</select>';
		return $str;
	}

	function LoadValidFromMonth() {
		$str = '<select name="validFromMonth" id="validFromMonth">';
		if(isset($_SESSION['validFromMonth'])) {
			for($i = 1; $i < 13; $i ++) {
				if ($i < 10) {
					$month = '0' . $i;
				} else {
					$month = $i;
				}
				if($_SESSION['validFromMonth'] == $month) {
					$sel = ' selected="selected"';
				} else {
					$sel = '';
				}
				$str .= '<option '.$sel.'>' . $month . '</option>';
			}
		} else {
			for($i = 1; $i < 13; $i ++) {
				if ($i < 10) {
					$month = '0' . $i;
				} else {
					$month = $i;
				}
				$str .= '<option>' . $month . '</option>';
			}
		}
		$str .= '</select>';
		return $str;
	}

	function LoadValidFromYear() {
		$currentTime = time();
		$str = '<select name="validFromYear" id="validFromYear">';
		if(isset($_SESSION['validFromYear'])) {
			for($i = 1; $i < 10; $i ++) {
				$year = date('Y',$currentTime);
				if($_SESSION['validFromYear'] == $year) {
					$sel = ' selected="selected"';
				} else {
					$sel = '';
				}
				$str .= '<option '.$sel.'>'.$year.'</option>';
				$currentTime = $currentTime - 31556926; // Number of seconds in a year
			}
		} else {
			for($i = 1; $i < 10; $i ++) {
				$str .= '<option>' . date ( 'Y', $currentTime ) . '</option>';
				$currentTime = $currentTime - 31556926; // Number of seconds in a year
			}
		}
		$str .= '</select><br />';
		return $str;
	}

	function LoadExpiryMonth() {
		$str = '<select name="expiryDateMonth" id="expiryDateMonth" class="required">';
		if(isset($_SESSION['expiryDateMonth'])) {
			for($i = 1; $i < 13; $i ++) {
				if ($i < 10) {
					$month = '0' . $i;
				} else {
					$month = $i;
				}
				if($_SESSION['expiryDateMonth'] == $month) {
					$sel = ' selected="selected"';
				} else {
					$sel = '';
				}
				$str .= '<option '.$sel.'>' . $month . '</option>';
			}
		} else {
			for($i = 1; $i < 13; $i ++) {
				if ($i < 10) {
					$month = '0' . $i;
				} else {
					$month = $i;
				}
				$str .= '<option>' . $month . '</option>';
			}
		}
		$str .= '</select>';
		return $str;
	}

	function LoadExpiryYear() {
		$currentTime = time();
		$str = '<select name="expiryDateYear" id="expiryDateYear" class="required">';
		if(isset($_SESSION['expiryDateYear'])) {
			for($i = 1; $i < 10; $i ++) {
				$year = date('Y',$currentTime);
				if($_SESSION['expiryDateYear'] == $year) {
					$sel = ' selected="selected"';
				} else {
					$sel = '';
				}
				$str .= '<option '.$sel.'>'.$year.'</option>';
				$currentTime = $currentTime + 31556926; // Number of seconds in a year
			}
		} else {
			for($i = 1; $i < 10; $i ++) {
				$str .= '<option>' . date ( 'Y', $currentTime ) . '</option>';
				$currentTime = $currentTime + 31556926; // Number of seconds in a year
			}
		}
		$str .= '</select><br />';
		return $str;
	}

} // End AddOrderBillingTabView

$page = new AddOrderBillingTabView;
echo $page->LoadDefault();

?>