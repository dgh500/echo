<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/* Details Section */
#manufacturerDetailsContentArea {
	width: 400px;
	height: 480px;
	padding: 10px;
	border: 1px solid #84B0C7;
	-moz-border-radius-bottomright: 5px;
	-moz-border-radius-bottomleft: 5px;
	border-top: 0px;
	float: left;
}

/* Image Section */
#manufacturerImageContentArea {
	width: 400px;
	height: 300px;
	border: 1px solid #84B0C7;
	display: none;
	padding: 10px;
	border-top: 0px;
	float: left;
}
#uploadImageIframe {
	height: 80px;
	margin-bottom: 20px;
}

/* Buttons */
#buttonsContainer {
	width: 410px;
	padding: 10px;
	padding-right: 0px;
	float: left;
	text-align: right;
	clear: both;
}
#buttonsContainer input {
	position: relative;
	top: -9px;
}
/* Help Styles */
#helpBox {
	height: 300px;
	width: 250px;
	float: left;
	padding: 10px;
}

#helpBox .helpText {
	display: none;
	line-height: 18px;
	background-color: #84B0C7;
	-moz-border-radius-topleft: 5px;
	-moz-border-radius-bottomright: 5px;
	border: 1px solid #000;
	padding: 10px;
	text-align: center;
}
#helpBox .helpText img {
	margin: 5px;
	border: 2px dashed #000;
}
#manufacturerDisplayHelp {
	margin-top: 30px;
}
#manufacturerDescriptionHelp {
	margin-top: 90px;
}
#manufacturerContentHelp {
	margin-top: 185px;
}
#manufacturerImageHelp {
	margin-top: 90px;
}

/* Tag Styles */
br {
	clear: both;
}
#manufacturerEditForm input[type=text] {
	float: left;
	display: block;
	margin: 5px;
	width: 220px;
}
#manufacturerEditForm input[type=checkbox] {
	margin: 8px;
}
#manufacturerEditForm label {
	float: left;
	display: block;
	width: 150px;
	font-weight: bold;
	margin: 5px;
}
#manufacturerEditForm textarea {
	width: 380px;
	margin: 5px;
}

/* Tab Styles */
#adminManufacturerViewTabContainer {
	background: url("../images/tab_b.gif") repeat-x bottom;
	width: 422px;
	height: 26px;
	display: block;
}
#adminManufacturerViewTabContainer ul {
	list-style: none;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
	margin: 0px;
	margin-left: 5px;
	padding: 0px;
}
#adminManufacturerViewTabContainer li {
	float: left;
	background: url("../images/prodLeft_both.gif") no-repeat left top;
	margin: 0;
	padding: 0 0 0 9px;
	height: 25px;
	border-bottom: 0px solid #FFF;
}
#adminManufacturerViewTabContainer a {
	float: left;
	display: block;
	width: .1em;
	background: url("../images/prodRight_both.gif") no-repeat right top;
	padding: 5px 15px 4px 6px;
	text-decoration: none;
	font-weight: bold;
	color: #1A419D;
}

#adminManufacturerViewTabContainer>ul a {
	width: auto;
}
#adminManufacturerViewTabContainer li:hover {
	background-position: 0 -150px;
	color: #F00;
}
#adminManufacturerViewTabContainer a:hover {
	background-position: 100% -150px;
	color: #F00;
}
