<?php
header('Content-Type: text/css');
require('../../css/Colors.php');
?>
.macViewContainer {
	width: 750px;
	height: 175px;
	border: 1px solid #000;
	overflow: auto;
    font-family: Arial;
}

.macViewContainer a {
	text-decoration: none;
	color: #000000;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
}

.topLevelCategoryContainer {
	width: 230px;
	height: 99%;
	border: 1px solid #fff;
	float: left;
	overflow: auto;
	overflow-x: hidden;
}

.subLevelCategoryContainer {
	width: 230px;
	height: 99%;
	border-right: 1px solid #000;
	float: left;
	overflow: auto;
	overflow-x: hidden;
}

.subLevelCategoryContainer input {
	width: auto;
	float: none;
	margin: 0px;
}

.productLevelCategoryContainer {
	width: 280px;
	height: 99%;
	border: 1px solid #FFF;
	float: left;
	overflow: auto;
	overflow-x: hidden;
}

.productLevelCategoryContainer input {
	width: auto;
	float: none;
	margin: 0px;
}

.macViewOutputProductContainerOrderForm {
	border: 1px solid #FFF;
	width: 675px;
	padding: 5px;
	padding-bottom: 0px;
	float: left;
	margin-left: 10px;
	clear: both;
}

.macViewOutputProductContainerOrderForm .productName,.macViewOutputProductContainerOrderForm .packageName
	{
	width: 510px;
	float: left;
}

.macViewOutputProductContainerOrderForm .productQuantity,.macViewOutputProductContainerOrderForm .packageQuantity
	{
	width: 60px;
	margin-left: 10px;
	float: left;
}

.macViewOutputProductContainerOrderForm .productPrice,.macViewOutputProductContainerOrderForm .packagePrice
	{
	width: 60px;
	clear: none;
	float: left;
}

.poundSymbol {
	display: inline;
	float: left;
	margin-left: 10px;
	margin-top: 2px;
	margin-right: 2px;
	font-weight: bold;
}

.macViewOutputProductContainer {
	border: 1px solid #aaa;
	width: 300px;
	padding: 5px;
	float: left;
	margin-bottom: 10px;
	margin-left: 10px;
}

.macViewOutputProductContainer img {
	width: 50px;
	height: 50px;
	float: left;
}

.macViewOutputProductContainer div {
	height: 60px;
	width: 225px;
	float: left;
	margin: 0px;
	padding: 0px;
	border: 1px solid #FFF;
	line-height: 14pt;
	margin-left: 10px;
}

.macViewMenuItemPackages {
	background-color: #FFFFFF;
	margin-bottom: 2px;
	padding-top: 3px;
	padding-bottom: 3px;
	padding-left: 25px;
	background-image: url(images/package.gif);
	background-position: 5px center;
	background-repeat: no-repeat;
	width: 88%;
}

.macViewMenuItemPackagesFocus {
	background-color: #C0D2EC;
	margin-bottom: 2px;
	padding-top: 3px;
	padding-bottom: 3px;
	padding-left: 25px;
	background-image: url(images/package.gif);
	background-position: 5px center;
	background-repeat: no-repeat;
	width: 88%;
	font-weight: bold;
}

.macViewMenuItemPackages a {
	display: block;
}

.macViewMenuItem {
	background-color: #FFFFFF;
	margin-bottom: 2px;
	padding-top: 3px;
	padding-bottom: 3px;
	padding-left: 25px;
	background-image: url(images/folder.gif);
	background-position: 5px center;
	background-repeat: no-repeat;
	width: 88%;
}

.macViewMenuItemFocus {
	background-color: #C0D2EC;
	margin-bottom: 2px;
	padding-top: 3px;
	padding-bottom: 3px;
	padding-left: 25px;
	background-image: url(images/folderopenBlue.gif);
	background-position: 5px center;
	background-repeat: no-repeat;
	width: 88%;
	font-weight: bold;
}

.macViewProductMenuItem {
	background-color: #FFFFFF;
	margin-bottom: 2px;
	padding-top: 3px;
	padding-bottom: 3px;
	padding-left: 25px;
	background-image: url(images/page.gif);
	background-position: 5px top;
	background-repeat: no-repeat;
	width: 94%;
}

.macViewMenuItem a {
	display: block;
}

.macViewMenuItem input {
	margin-left: -4px; 
    margin-right: 4px; 
    width: auto;
}

.attributesAndUpgradesContainer {
	background-color: #D8E2E9;
	width: 660px;
	margin: 0px;
	padding: 0px;
	clear: both;
	float: left;
	padding: 5px;
	margin-bottom: 2px;
}

.attributesAndUpgradesContainer2 {
	background-color: #B2CCDC;
	width: 660px;
	margin: 0px;
	padding: 0px;
	clear: both;
	float: left;
	padding: 5px;
	margin-bottom: 2px;
}

.attributesAndUpgradesContainerProduct {
	background-color: #D8E2E9;
	width: 659px;
	margin: 0px;
	padding: 0px;
	clear: both;
	float: left;
	padding: 5px;
	margin-bottom: 2px;
	border: 1px solid #aaa;
}

.packageContentDropDown {
	width: 150px !important;
	margin-right: 10px !important;
}

.packageContentLabel {
	width: 75px !important;
	clear: both !important;
}

.productAttributeDropDown {
	width: auto !important;
	margin-right: 10px !important;
}

.productAttributeLabel {
	width: 75px !important;
	clear: both !important;
}

.upgradeHeading {
	clear: both !important;
	margin: 0px !important;
	font-weight: bold;
}

.packageContentUpgradeLabel {
	clear: none !important;
	display: inline !important;
	font-weight: normal !important;
	float: left;
	margin-left: 10px;
}

.productSingleAttributeContainer {
	display: block;
	float: left;
	clear: both;
	margin: 0px;
}

.packageContentSingleAttributeContainer {
	display: block;
	float: left;
	clear: both;
	margin: 0px;
	width: 250px;
}

.packageContentProductName {
	display: block;
	font-weight: bold;
	width: 100%;
	margin: 0px;
	padding: 0px;
	float: left;
	margin-bottom: 2px;
}

.packageContentAttributesContainer {
	float: left;
	width: 40%;
}

.packageContentUpgradesContainer {
	float: left;
	width: 40%;
}

.packageContentCheckbox {
	display: inline;
	clear: both;
	margin: 0px;
	float: left;
	margin-left: 0px;
}

.packageNumber {
	font-weight: bold;
	margin-top: 5px;
	float: left;
	width: 660px;
	padding: 5px;
	text-align: center;
	background-color: #000000;
	color: #FFFFFF;
	position: relative;
}

.packageNumberUpDown {
	position: absolute;
	right: 0px;
	top: 0px;
	width: 50px;
	height: 28px;
	background-color: #000;
}

.productNumber {
	font-weight: bold;
	margin-top: 5px;
	float: left;
	width: 650px;
	padding: 5px;
	text-align: center;
	background-color: #000000;
	color: #FFFFFF;
}

.productExtraInfoBar {
	width: 670px;
	border-bottom: 1px solid #000;
	float: left;
}

#packageContentsDialogContainer h3 {
    width: 260px;
    height: 30px;    
    line-height: 30px;
    text-indent: 10px;
    font-size: 10pt;
    background-color: #cccccc;
    color: #414141;
    margin: 0px;
    display: block;
    position: relative;
}
#packageContentsDialogContainer img {
	position: absolute;
    right: 10px;
    top: 11px;
}
#packageContentsDialogContainer #optionsContainer {
	width: 260px;
	background-color: #e9e9e9;
	color: #6b6b6b;
	margin: 0px;
	padding: 0px;
    font-size: 10pt;
    font-family: Arial;   
    float: left; 
}
#packageContentsDialogContainer #optionsContainer select {
	width: 200px;
    margin-top: 5px;
}
#packageContentsDialogContainer #optionsContainer select option {
	font-family: Arial;
    font-size: 10pt;
}
#packageContentsDialogContainer #optionsContainer ol {
    margin: 0px;
    padding-top: 10px;
    padding-bottom: 10px;
}

#packageContentsDialogContainer #optionsContainer ol li {
    margin: 0px;
    padding: 0px;
    margin-bottom: 5px;
}
#packageContentsDialogContainer #optionsContainer ol li a {
	text-decoration: none;
}
#packageContentsDialogContainer #upgradesContainer {
	width: 260px;
	background-color: #e9e9e9;
	color: #6b6b6b;
	margin: 0px;
	padding: 0px;
    font-size: 10pt;
    font-family: Arial;    
    float: left;
    margin-left: 20px;
}
#packageContentsDialogContainer #upgradesContainer ol {
    margin: 0px;
    padding-left: 10px;
    padding-top: 10px;
    padding-bottom: 10px;
    list-style-type: none;
    text-indent: 0px;
}
#packageContentsDialogContainer #upgradesContainer ol li {
    margin: 0px;
    padding: 0px;
    margin-bottom: 5px;
    text-indent: 0px;
}
#packageContentsDialogContainer #upgradesContainer ol li a {
	text-decoration: none;
}
#productOptionsDialogContainer {
	font-family: Arial;
    font-size: 10pt;
}
#productOptionsDialogContainer select {
	margin: 5px;
    width: 150px;
}
#productOptionsDialogContainer select li {
	font-family: Arial;
}
#allProductSkus h3 {
	margin: 0px;
    font-size: 10pt;
    font-weight: bold;
}
#catalogueSelection {
	font-family: Arial;
    font-size: 10pt;
    margin-bottom: 5px;
    font-weight: bold;
    width: 740px;
    text-align: center;
    padding: 5px;
}
#basketContents {
	font-family: Arial;
    font-size: 10pt;
    width: 750px;
}
#basketTable {
	width: 750px;
	border: 1px solid #000;
	border-collapse: collapse;
	font-size: 10pt;
	margin-left: auto;
	margin-right: auto;
    font-family: Arial;
}
#basketTable td {
	border: 1px solid #000;
	padding: 5px;
	text-align: left;
}

#basketTable th {
	border: 1px solid #000;
	border-bottom: 0px;
	padding: 5px;
}

#basketTable input {
	font-family: Arial !important;
}

#basketTable #productColumn {
	width: 255px;
}

#basketTable #qtyColumn {
	width: 30px;
	text-align: center;
}

#basketTable #qtyColumn input {
	width: 20px;
}

#basketTable #unitPriceColumn {
	width: 60px;
	text-align: center;
}

#basketTable #unitPriceColumn input {
	width: 50px;
}

#basketTable #totalPriceColumn {
	width: 30px;
	text-align: center;
}

#basketTable #removeColumn {
	width: 60px;
	font-weight: bold;
	text-align: center;
}

#basketTable #totalsTitles {
	text-align: right;
	font-weight: bold;
}

#basketTable #totalsValues {
	font-weight: bold;
}

#basketTable #totalsValues input {
	width: 50px !important;
}

#basketTable #shippingOptions {
	text-align: right;
}
#updateCheckoutButton {
	margin: 10px;
	margin-left: 430px;
	margin-bottom: 0px;
}

#postageDropDownForm {
	margin: 0px;
}
#postageMethodDropDownMenu,#countryDropDownMenu {
	width: 160px;
	margin: 2px;
	font-family: Arial;    
}

#countryDropDownForm {
	display: inline;
	font-family: Arial;
}
