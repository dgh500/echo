<?php

//! Class for dealing with money site-wide
class MoneyHelper {

	//! The rate of VAT
	var $mVatRate;

	//! Sets the rate of VAT
	function __construct() {
		$this->mVatRate = 17.5;
	}

	//! Get the VAT rate as a decimal - so 17.5% is 0.175
	function GetVatRateAsDecimal() {
		$asDecimal = $this->mVatRate / 100;
		return $asDecimal;
	}

	//! Works out VAT, given the normal price (inclusive of VAT)
	/*!
	 * @param $price_with_vat : Decimal - The price inclusive of VAT
	 * @return : The amount of VAT that was included in the amount
	 */
	function VAT($price_with_vat) {
		//( 100 / ( 100 + [VAT Rate] ) * [Final Price] = [Pre-VAT Price]
		//( 100 / ( 100 + 17.5 ) ) * 84.99 = 72.33 (rounded)
		// 72.33 / ( 100 / ( 100 + 17.5 ) ) = 84.99
		/*$price_without_vat = (100 / (100 + $this->mVatRate)) * $price_with_vat;
		$tax_paid = $price_with_vat - $price_without_vat;
		return round($tax_paid,2);*/
		$vatFreePrice = $price_with_vat / 1.175;
		return round($price_with_vat - $vatFreePrice,2);
	} // End VAT

	//! Returns a price including VAT from a VAT-Free price
	function AddVAT($price_without_vat) {
		return $price_without_vat * ($this->mVatRate/100 + 1);
	}

	//! Removes the VAT on a price inclusive of VAT
	/*!
	 * @param $price_with_vat : Decimal - The price inclusive of VAT
	 * @return : The VAT-free price
	 */
	function RemoveVAT($price_with_vat) {
		$vat = $this->VAT ( $price_with_vat );
		$vatFreePrice = $price_with_vat - $vat;
		return $vatFreePrice;
	} // End VAT


} // End MoneyHelper


?>