<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/* Overriding styles */
body {
	margin: 0;
	padding: 0;
	border: 0;
	/* This removes the border around the viewport in old versions of IE */
	width: 100%;
	min-height: 100%;
	background: url(../images/chkPageBg.gif);
	background-repeat: repeat-x;
	background-color: #000000;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
	text-align: center;
}

.threeColContainer {
	background-image: url(../images/chkPageHorizBg.gif);
}

#leftNavContainer {
	background-image: url(../images/chkLeftColBg.gif);
}

#headerLeftSection {
	background-image: url(../images/chkHeaderLeftSectionBottomBg.gif);
}

#headerMidSection {
	background-image: url(../images/chkHeaderMidSectionBg.gif);
}

#headerRightSection {
	background-image: url(../images/chkHeaderRightSectionBottomBg.gif);
}

#headerImagesContainer {
	background-image: url(../images/chkHeaderImages.jpg);
}

#logo span {
	background: url(../images/chkLogo.gif);
}

#otherSites #otherSites-dive {
	background-image: url(../images/chkOtherSitesDive.gif);
}

#otherSites #otherSites-swim {
	background-image: url(../images/chkOtherSitesSwim.gif);
}

#otherSites #otherSites-ski {
	background-image: url(../images/chkOtherSitesSki.gif);
}

#otherSites #otherSites-clay {
	background-image: url(../images/chkOtherSitesClayField.gif);
}

#otherSites #otherSites-clothing {
	background-image: url(../images/chkOtherSitesClothing.gif);
}

#accountNavigation #accountNavigation-home {
	background-image: url(../images/chkAccountNavigationHome.gif);
}

#accountNavigation #accountNavigation-contactInfo {
	background-image: url(../images/chkAccountNavigationContactInfo.gif);
}

#accountNavigation #accountNavigation-myAccount {
	background-image: url(../images/chkAccountNavigationMyAccount.gif);
}

#accountNavigation #accountNavigation-checkout {
	background-image: url(../images/chkAccountNavigationCheckout.gif);
}

#accountNavigation #accountNavigation-orderTracking {
	background-image: url(../images/chkAccountNavigationOrderTrack.gif);
}

#accountNavigation #accountNavigation-returns {
	background-image: url(../images/chkAccountNavigationReturns.gif);
}

#footer {
	background-image: url(../images/chkFooterBg.gif);
	height: 12px;
}

h1 {
	color: #000000;
}

#forgotPasswordSubmit {
	color: #000;
	background-color: transparent;
	text-decoration: underline;
	border: none;
	cursor: pointer;
	cursor: hand;
}

#passwordResetForm label {
	display: block;
	float: left;
	width: 200px;
}

#passwordResetForm input {
	margin-bottom: 10px;
}

#passwordResetForm #submit {
	margin-left: 200px;
}

#loginContainer {
	width: 540px;
	border: 0px solid #000;
	text-align: center;
	margin-left: auto;
	margin-right: auto;
}

#loginContainer h1 {
	color: #000000;
	font-size: 14pt;
}

#loginContainer #newCustomerContainer {
	border: 0px solid #000;
	width: 260px;
	background-color: #ffde00;
	height: 220px;
	float: left;
	text-align: left;
}

#loginContainer #newCustomerContainer h2 {
	background-color: #000000;
	color: #FFFFFF;
	font-size: 12pt;
	padding: 5px;
	text-align: center;
	margin-top: 0px;
}

#loginContainer #newCustomerContainer p {
	padding: 10px;
}

#loginContainer #newCustomerContainer form {
	width: 260px;
	text-align: center;
}

#loginContainer #returningCustomerContainer {
	border: 0px solid #000;
	background-color: #ffde00;
	width: 260px;
	height: 220px;
	float: left;
	text-align: center;
	position: relative;
	margin-left: 5px;
}

#loginContainer #returningCustomerContainer h2 {
	background-color: #000000;
	color: #FFFFFF;
	padding: 5px;
	font-size: 12pt;
	text-align: center;
	margin-top: 0px;
	margin-bottom: 30px;
}

#loginContainer #returningCustomerContainer input {
	margin-bottom: 10px;
}

#loginContainer #returningCustomerContainer label {
	width: 100px;
	display: block;
	float: left;
	text-align: right;
}

#newCustomerRegistrationForm {
	border: 0px solid #000;
}

#newCustomerRegistrationForm a {
	text-decoration: underline;
}

#newCustomerRegistrationForm .required {
	font-weight: bold;
	color: #FF0000;
}

.required {
	font-weight: bold;
	color: #FF0000;
}

#newCustomerRegistrationForm h1 {
	font-size: 12pt;
	text-align: center;
}

#newCustomerRegistrationForm label {
	width: 250px;
	display: block;
	float: left;
	text-align: right;
}

#newCustomerRegistrationForm input {
	margin-left: 10px;
	margin-bottom: 10px;
}

#newCustomerRegistrationForm select {
	margin-left: 10px;
	margin-bottom: 10px;
}

#newCustomerRegistrationForm .submit {
	margin-left: auto;
	margin-right: auto;
	display: block;
	width: 200px;
}

#deliveryForm {
	border: 0px solid #000;
}

#deliveryForm a {
	text-decoration: underline;
}

#deliveryForm .required {
	font-weight: bold;
	color: #FF0000;
}

#deliveryForm h1 {
	font-size: 12pt;
	text-align: center;
}

#deliveryForm label {
	width: 250px;
	display: block;
	float: left;
	text-align: right;
}

#deliveryForm input {
	margin-left: 10px;
	margin-bottom: 10px;
}

#deliveryForm select {
	margin-left: 10px;
	margin-bottom: 10px;
}

#deliveryForm textarea {
	margin-left: 10px;
	margin-bottom: 10px;
}

#deliveryForm .submit {
	margin-left: auto;
	margin-right: auto;
	display: block;
	width: 200px;
}

#error {
	border: 2px solid #F00;
	font-size: 10pt;
	display: none;
    padding: 10px;
}

#newCustomerRegistrationForm #error {
	border: 2px solid #F00;
	font-size: 10pt;
	display: none;
}

#failedLoginContainer {
	width: 540px;
	border: 0px solid #000;
	text-align: center;
	margin-left: auto;
	margin-right: auto;
}

#failedLoginContainer h1 {
	color: #000000;
	font-size: 14pt;
}

#failedLoginContainer #returningCustomerContainer {
	border: 0px solid #000;
	background-color: #ffde00;
	width: 260px;
	height: 200px;
	text-align: center;
	position: relative;
	margin-left: auto;
	margin-right: auto;
}

#failedLoginContainer #returningCustomerContainer h2 {
	background-color: #000000;
	color: #FFFFFF;
	padding: 5px;
	font-size: 12pt;
	text-align: center;
	margin-top: 0px;
	margin-bottom: 30px;
}

#failedLoginContainer #returningCustomerContainer input {
	margin-bottom: 10px;
}

#failedLoginContainer #returningCustomerContainer label {
	width: 100px;
	display: block;
	float: left;
	text-align: right;
}

#registrationError {
	text-align: center;
	width: 540px;
	font-weight: bold;
	color: #F00;
}

#firstOrderNotice {
	width: 540px;
	text-align: center;
	font-weight: bold;
	color: #F00;
}

#deliveryForm #error {
	border: 2px solid #F00;
	font-size: 10pt;
	text-align: center;
	display: none;
}

#billingForm {
	border: 0px solid #000;
}

#billingForm a {
	text-decoration: underline;
}

#billingForm .required {
	font-weight: bold;
	color: #FF0000;
}

#billingForm h1 {
	font-size: 12pt;
	text-align: center;
}

#billingForm label {
	width: 250px;
	display: block;
	float: left;
	text-align: right;
}

#billingForm input {
	margin-left: 10px;
	margin-bottom: 10px;
}

#billingForm select {
	margin-left: 10px;
	margin-bottom: 10px;
}

#billingForm .submit {
	margin-left: auto;
	margin-right: auto;
	display: block;
	width: 200px;
}

#checkoutBackForm {
	text-align: center;
	margin-top: 10px;
}