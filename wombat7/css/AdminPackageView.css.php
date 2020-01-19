<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/*********************
* Admin Package Form *
**********************/
br {
	clear: both;	
}
#adminPackageForm {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
}

#adminPackageViewContainer {
	width: 720px;
	display: block;
	text-align: center;
	margin-top: 10px;
}

#adminPackageForm label {
	width: 100px;
	font-weight: bold;
	float: left;
	text-align: left;
	margin-bottom: 10px;
	margin-right: 5px;
	border: 1px solid #FFF;
	height: 20px;
	line-height: 20px;
}

#adminPackageForm input {
	width: 200px;
	float: left;
	text-align: left;
	margin-bottom: 10px;
}

#adminPackageFormButtons {
	float: right;
	margin-top: 10px;
}

#adminPackageFormButtons input {
	width: auto;
	text-align: center;
}

/* Admin Package Form -> Details Section */
#detailsContentArea {
	border: 1px solid #FFF;
	height: 99%;
	text-align: left;
	padding-left: 5px;
	padding-right: 5px;
	display: block;
}

#detailsContentArea #wasPrice,#detailsContentArea #actualPrice,#detailsContentArea #postage
	{
	width: 70px;
}

#detailsContentArea #offerOfTheWeek {
	width: auto;
}

#detailsContentArea .halfWidthContainer {
	border: 1px solid #FFF;
	width: 48%;
	float: left;
}

/* Admin Package Form -> Contents Section */
#packageContentsList {
	border: 1px solid #FFF;
	float: left;
}

.packageContentProductContainer {
	border: 1px solid #aaa;
	width: 680px;
	padding: 5px;
	float: left;
	margin-bottom: 10px;
	margin-left: 10px;
    clear: both;
}
.packageContentProductContainer input {
	float: left;
    display: block;
    width: 20px !important;
    margin: 5px;
    margin-left: 10px;
    margin-right: 0px;
    border: 0px;
    text-align: center;
}
.packageContentProductContainer img {
	float: left;
    display: block;
    margin: 5px;
}
.packageContentProductContainer strong {
    margin: 5px;
    float: left;
    margin-top: 8px;
    display: block;
}

#contentsContentArea {
	border: 1px solid #FFF;
	height: 99%;
	text-align: left;
	padding-left: 5px;
	padding-right: 5px;
	display: none;
}

/* Admin Package Form -> Upgrades Section */
#packageUpgradesList {
	border: 1px solid #FFF;
	float: left;
}

.packageUpgradeProductContainer {
	border: 1px solid #aaa;
	width: 300px;
	padding: 5px;
	float: left;
	margin-bottom: 10px;
	margin-left: 10px;
}

.packageUpgradeProductContainer img {
	width: 50px;
	height: 50px;
	float: left;
}

.packageUpgradeProductContainer div {
	height: 60px;
	width: 225px;
	float: left;
	margin: 0px;
	padding: 0px;
	border: 1px solid #FFF;
	line-height: 14pt;
	margin-left: 10px;
}

#upgradesContentArea {
	border: 1px solid #FFF;
	height: 99%;
	text-align: left;
	padding-left: 5px;
	padding-right: 5px;
	display: none;
}

#upgradesContentArea .packageContentContainer {
	border: 1px solid #FFF;
	float: left;
	width: 700px;
}

#upgradesContentArea .packageContentContainer h2 {
	font-size: 10pt;
	font-weight: bold;
}

#upgradesContentArea .packageContentContainer .price {
	width: 50px;
	margin-right: 10px;
}

/* Admin Package Form -> Image Section */
#imageContentArea {
	border: 1px solid #FFF;
	height: 99%;
	text-align: left;
	padding-left: 5px;
	padding-right: 5px;
	display: none;
}

.packageImageContainer {
	border: 1px solid #DEDEDE;
	float: left;
	margin-bottom: 10px;
	padding: 5px;
}

.packageImageContainer div {
	float: left;
	border: 1px solid #FFF;
	margin-left: 10px;
	width: 350px;
	height: 100px;
}

.packageImageContainer img {
	float: left;
}

/*************************
* End Admin Package Form *
**************************/

/*********************
* Admin Package Tabs *
**********************/
#adminPackageViewContentContainer {
	border: 1px solid #aaa;
	border-top: 0px solid #FFF;
	background-color: #FFFFFF;
	width: 720px;
	height: 485px;
	display: block;
	margin-left: auto;
	margin-right: auto;
	padding-top: 5px;
	overflow: auto;
	overflow-x: hidden;
}

#adminPackageViewTabContainer {
	background: url("../images/tab_b.gif") repeat-x bottom;
	width: 720px;
	height: 26px;
	display: block;
	margin-left: auto;
	margin-right: auto;
}

#adminPackageViewTabContainer ul {
	list-style: none;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
	margin: 0px;
	margin-left: 5px;
	padding: 0px;
}

#adminPackageViewTabContainer li {
	float: left;
	background: url("../images/prodLeft_both.gif") no-repeat left top;
	margin: 0;
	padding: 0 0 0 9px;
	height: 25px;
	border-bottom: 0px solid #FFF;
}

#adminPackageViewTabContainer a {
	float: left;
	display: block;
	width: .1em;
	background: url("../images/prodRight_both.gif") no-repeat right top;
	padding: 5px 15px 4px 6px;
	text-decoration: none;
	font-weight: bold;
	color: #1A419D;
}

#adminPackageViewTabContainer>ul a {
	width: auto;
}

#adminPackageViewTabContainer li:hover {
	background-position: 0 -150px;
	color: #F00;
}

#adminPackageViewTabContainer a:hover {
	background-position: 100% -150px;
	color: #F00;
}

/*************************
* End Admin Package Tabs *
**************************/ 