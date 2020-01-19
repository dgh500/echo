<?php
header('Content-Type: text/css');
require('Colors.php');
?>
#addOrderTabContainer {
	width: 800px;
    float: left;
    position: relative;
    font-family: Arial;
    font-size: 12pt;
}

#addOrderTabContainer #loading {
	position: absolute;
    top: 6px;
    right: 7px;
    width: 34px;
    height: 34px;
    background-image: url(../images/ajaxLoaderBg.gif);
    text-align: center;
    line-height: 37px;
}

#addOrderTabContainer h2 {
	margin: 5px;
    clear: both;
    font-size: 12pt;
    font-weight: bold;
    color: #454545;
    font-family: Arial;
    border: 0px solid #000;
    width: 150px;
    margin-left: 215px;
    margin-bottom: 0px;
}

.required {
	border: 1px solid #A5ACB2;
	border-left: 2px solid #f00;
    padding-left: 2px !important;
}

.notRequired {
	border: 1px solid #A5ACB2;
	border-left: 2px solid #A5ACB2;
    padding-left: 2px !important;
}

#processingOrderLoading h2 {
	margin: 0px;
    width: auto;
}

/* Customer Tab */
#addOrderTabContainer #customerTab label {
	width: 200px;
    float: left;
    display: block;
    clear: both;
    margin: 5px;
    font-weight: bold;
    text-align: right;
    line-height: 20px;
}

#addOrderTabContainer #customerTab input {
	width: 200px;
    float: left;
    display: block;	
    margin: 5px;
    padding: 0px;
	font-size: 10pt;
    font-family: Arial;    
}
#catalogueWanted {
	width: 20px !important;
}
#addOrderTabContainer #customerTab select {
	width: 50px;
    float: left;
    display: block;
    margin: 3px;
    padding: 0px;
}
#addOrderCustomerForm {
	float: left;
	margin-bottom: 20px !important;
}
#title {
	width: 80px !important;
}
#referrerId, #staffName {
	width: 205px !important;
}
#customerTab option, #customerTab select {
	font-size: 10pt;
    font-family: Arial;
}
/* Billing Tab */
#addOrderTabContainer #billingTab label {
	width: 150px;
    float: left;
    display: block;
    clear: both;
    margin: 3px;
    font-weight: bold;
    text-align: right;
    line-height: 25px;
}

#addOrderTabContainer #billingTab input {
	width: 150px;
    float: left;
    display: block;	
    margin: 5px;
    padding: 0px;
    font-family: Arial;
    font-size: 10pt;
}
#addOrderTabContainer #billingTab select {
    font-family: Arial;
    font-size: 10pt;
}
#addOrderTabContainer #billingTab #cardType {
	width: 205px !important;
    float: left;
    display: block;
    margin: 5px;
    padding: 0px;
}
#addOrderBillingForm {
	float: left;
	margin-bottom: 20px !important;
}
#cardNumber, #cardHoldersName {
	width: 200px !important;
}
#cardType {
	width: 212px !important;
}
#validFromMonth, #expiryDateMonth {
	width: 92px !important;
    float: left;
    display: block;
    margin: 5px;
    padding: 0px;    
}
#validFromYear, #expiryDateYear {
	width: 102px !important;
    float: left;
    display: block;
    margin: 5px;
    padding: 0px;
}
#cardVerificationNumber {
	width: 68px !important;
}
#issueNumber {
	width: 67px !important;
}
#issueNumberLabel {
	clear: none !important;
    width: 45px !important;
}
/* Complete Order Bar */
#completeOrder {
	width: 750px;
    height: 50px;
    margin-left: 20px;
    text-align: center;
    clear: both;
}

/* Customer Lookup */
#customerLookupDialog {
	display: none;
}
#customerLookupDialog iframe {
    width: 570px;
    height: 150px;
}