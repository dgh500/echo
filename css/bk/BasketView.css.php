<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/* Shopping Basket Styles */
#shoppingBasketTitleContainer {
	width: 550px;
	height: 27px;
	background-image: url(../images/shoppingBagTitleBg.gif);
}

#shoppingBasketTitleContainer h1 {
	margin: 0px;
	font-size: 12pt;
	color: #FFF;
	text-indent: 10px;
	line-height: 27px;
}

#shoppingBasketFooterContainer {
	margin: 0px;
	padding: 0px;
	width: 550px;
	height: 27px;
	background-image: url(../images/shoppingBagFooterBg.gif);
}

#shoppingBasketContainer {
	width: 528x;
	padding: 0px;
	border-left: 1px solid #AAAAAA;
	border-right: 1px solid #AAAAAA;
	background-color: #EAEAEA;
	font-size: 10pt;
	border-bottom: 1px solid #EAEAEA;
	padding: 10px;
	text-align: center;
}

#shoppingBasketContainer #basketTable {
	width: 96%;
	border: 1px solid #000;
	border-collapse: collapse;
	font-size: 10pt;
	margin-left: auto;
	margin-right: auto;
}

#shoppingBasketContainer #basketTable td {
	border: 1px solid #000;
	padding: 5px;
	text-align: left;
}

#shoppingBasketContainer #basketTable th {
	border: 1px solid #000;
	border-bottom: 0px;
	padding: 5px;
}

#shoppingBasketContainer #basketTable #productColumn {
	width: 255px;
}

#shoppingBasketContainer #basketTable #qtyColumn {
	width: 50px;
	text-align: center;
}

#shoppingBasketContainer #basketTable #qtyColumn input {
	width: 20px;
}

#shoppingBasketContainer #basketTable #unitPriceColumn {
	width: 75px;
	text-align: center;
}

#shoppingBasketContainer #basketTable #totalPriceColumn {
	width: 75px;
	text-align: center;
}

#shoppingBasketContainer #basketTable #removeColumn {
	width: 20px;
	font-weight: bold;
	text-align: center;
}

#shoppingBasketContainer #basketTable #totalsTitles {
	text-align: right;
	font-weight: bold;
}

#shoppingBasketContainer #basketTable #totalsValues {
	font-weight: bold;
}

#shoppingBasketContainer #basketTable #shippingOptions {
	text-align: right;
}

#shoppingBasketContainer #proceedToCheckoutButton {
	margin: 10px;
	margin-left: 430px;
	margin-bottom: 0px;
}

#proceedToGoogleCheckoutButtonContainer {
	width: 200px;
	margin-left: 330px;
    border: 0px solid #f00; 
    text-align: right;
}

#shoppingBasketContainer #updateCheckoutButton {
	margin: 10px;
	margin-left: 430px;
	margin-bottom: 0px;
}

#shoppingBasketContainer #postageDropDownForm {
	margin: 0px;
}
#warningMessage {
	margin-right: 20px; 
	margin-left: 20px; 
	margin-bottom: 20px; 
	text-align: center;
}
#postageMethodDropDownMenu,#countryDropDownMenu {
	width: 160px;
	margin: 2px;
}

#countryDropDownForm {
	display: inline;
}

/* Freebie section */

#freeOfferContainer {
	border: 0px solid #000; 
    position: relative;
}
#freeOfferContainer #freeOfferIncentive {
	position: absolute; 
    top: 45px; 
    left: 160px; 
    font-size: 10pt; 
    width: 340px; 
    height: 40px; 
    color: #FFF; 
    text-align: left;
}
#freeOfferContainer #freeOfferSmallprint {
	position: absolute; 
    top: 120px; 
    left: 130px; 
    font-size: 8pt; 
    width: 375px; 
    height: 40px; 
    color: #000; 
    text-align: left; 
    border: 0px solid #000;
}
#claimFreeOfferContainer {
	border: 0px solid #000; 
    position: relative;
}
#freeOfferDialog {
	text-align: center; 
    font-family: Arial;
    display: none;
}
#freeOfferDialog table {
	width: 380px;
}
#freeOfferDialog table #smallImageCell {
	width: 100px;
    height: 100px;
}
#freeOfferDialog table #productName {
    height: 25px;
}

#productOptions
	{
	border: 0px solid #f00;
	width: 220px;
	color: #939AA1;
	font-weight: bold;
    font-family: Arial, Sans-Serif;
}

#productOptions h3
	{
	width: 250px;
	height: 30px;
	line-height: 30px;
	text-indent: 10px;
	font-size: 10pt;
	background-color: #CCCCCC;
	color: #414141;
	margin: 0px;
}

#productOptions #optionsContainer
	{
	width: 250px;
	background-color: #E9E9E9;
	color: #6B6B6B;
}

#productOptions #optionsContainer select
	{
	width: 230px;
	margin: 10px;
    font-family: Arial, Sans-Serif;
    font-size: 10pt;    
}

#productOptions #optionsContainer select option {
    font-family: Arial, Sans-Serif;
    font-size: 10pt;
}

#productOptions #optionsContainer span
	{
	margin: 10px;
}

#productOptions #optionsContainer input
	{
	margin: 10px;
}

#productOptions #optionsContainer img
	{
	margin: 10px;
}


