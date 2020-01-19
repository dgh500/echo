<?php
header('Content-Type: text/css');
require('Colors.php');
?>
.macViewContainer {
	width: 700px;
	height: 175px;
	border: 1px solid #000;
	overflow: auto;
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
	width: 230px;
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
	background-image: url(../wombat7/dtree/img/package.gif);
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
	background-image: url(../wombat7/dtree/img/package.gif);
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
	background-image: url(../wombat7/dtree/img/folder.gif);
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
	background-image: url(../wombat7/dtree/img/folderopenBlue.gif);
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
	background-image: url(../wombat7/dtree/img/page.gif);
	background-position: 5px top;
	background-repeat: no-repeat;
	width: 94%;
}

.macViewMenuItem a {
	display: block;
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