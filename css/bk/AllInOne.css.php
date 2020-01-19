<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/* Screen Styles */ 
/*
 * Contains styles for the page layout and general tag styles (<H1>, <A> etc.)
 */
body {
	margin: 0;
	padding: 0;
	border: 0;
	width: 100%;
	min-height: 100%;
	background: url(../images/pageBg.gif);
	background-repeat: repeat-x;
	background-color: <?php echo $pageBg; ?>;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
	text-align: center;
}

img {
	border: 0px;
}

h1 {
	color: <?php echo $pageH1; ?>;
	font-size: 14pt;
	margin: 0px;
}

h1 a {
	color: <?php echo $pageH1; ?> !important;
}

a {
	text-decoration: none;
	color: #000000;
}

a:visited {
	text-decoration: none;
	color: #000000;
}

/* new styles */
.threeColContainer {
	border: 0px solid #f00;
	width: 1010px;
	float: left;
	background-image: url(../images/pageHorizBg.gif);
	background-repeat: repeat-y;
	text-align: left;
	overflow: a
}

.leftCol {
	width: 230px;
	height: auto;
	float: left;
	border: 0px solid #0f0;
}

.centreCol {
	width: 550px;
	float: left;
	border: 0px solid #00f;
	margin-left: 10px;
}

.rightCol {
	width: 210px;
	float: right;
	border: 0px solid #f00;
}

/*** Other columns ***/
#centerAlignPageContainer {
	margin-right: auto;
	margin-left: auto;
	width: 1010px;
	position: relative;
}

#rightNavContainer {
	width: 180px;
	border: 0px solid #f00;
	position: relative;
	float: left;
	top: -20px;
	right: -10px;
}

#rightNavContainer img .spaceTop {
	margin-top: 5px;
}

#leftNavContainer {
	width: 230px;
	min-height: 100%;
	margin: 0px;
	background-image: url(../images/leftColBg.gif);
	background-repeat: repeat-y;
	float: left;
}
/* Account Navigation */
#accountNavBarContainer {
	width: 625px;
	height: 50px;
	background-color: #999999;
}

#accountNavigation {
	margin: 0px;
	padding: 0px;
	height: 50px;
}

#accountNavigation li {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
	color: <?php $accNavLink ?>;
	font-weight: bold;
	display: inline;
	display: block;
	float: left;
}

#accountNavigation a {
	text-decoration: none;
	color: <?php $accNavLink ?>;
}

#accountNavigation a:hover {
	text-decoration: none;
	color: <?php echo $accNavHover; ?>;
}

#accountNavigation #accountNavigation-home {
	background-image: url(../images/accountNavigationHome.gif);
	height: 50px;
	width: 111px;
}

#accountNavigation #accountNavigation-home div {
	margin-top: 25px;
	margin-left: 65px;
}

#accountNavigation #accountNavigation-contactInfo {
	background-image: url(../images/accountNavigationContactInfo.gif);
	height: 50px;
	width: 122px;
}

#accountNavigation #accountNavigation-contactInfo div {
	margin-top: 25px;
	margin-left: 36px;
}

#accountNavigation #accountNavigation-myAccount {
	background-image: url(../images/accountNavigationMyAccount.gif);
	height: 50px;
	width: 91px;
}

#accountNavigation #accountNavigation-myAccount div {
	margin-top: 25px;
}

#accountNavigation #accountNavigation-checkout {
	background-image: url(../images/accountNavigationCheckout.gif);
	height: 50px;
	width: 70px;
}

#accountNavigation #accountNavigation-checkout div {
	margin-top: 25px;
	margin-left: 0px;
}

#accountNavigation #accountNavigation-orderTracking {
	background-image: url(../images/accountNavigationOrderTrack.gif);
	height: 50px;
	width: 102px;
}

#accountNavigation #accountNavigation-orderTracking div {
	margin-top: 25px;
}

#accountNavigation #accountNavigation-returns {
	background-image: url(../images/accountNavigationReturns.gif);
	height: 50px;
	width: 129px;
}

#accountNavigation #accountNavigation-returns div {
	margin-top: 25px;
	margin-left: 10px;
}
/* Footer Styles */
#footer {
	clear: both;
	float: left;
	width: 1010px;
	background-image: url(../images/footerBg.gif);
	background-repeat: no-repeat;
	background-position: bottom;
	height: 64px;
}

#footer #footerLinksContainer {
	width: 1010px;
	background-image: url(../images/footerVertBg.gif);
	background-repeat: repeat-y;
	height: 50px;
	text-align: center;
}

#footer a:hover {
	text-decoration: underline;
}
/* Header Styles */
#header {
	clear: both;
	float: left;
	margin: 0px;
	padding: 0px;
	height: 210px;
	text-align: left;
}

#headerLeftSection {
	width: 375px;
	height: 210px;
	background-image: url(../images/headerLeftSectionBottomBg.gif);
	background-repeat: no-repeat;
	background-position: bottom;
	float: left;
	position: relative;
}

#headerMidSection {
	height: 210px;
	position: relative;
	width: 10px;
	background-image: url(../images/headerMidSectionBg.gif);
	background-repeat: repeat-x;
	float: left;
}

#headerRightSection {
	height: 210px;
	width: 625px;
	background-image: url(../images/headerRightSectionBottomBg.gif);
	background-repeat: no-repeat;
	float: left;
	position: relative;
}

#headerImagesContainer {
	width: 625px;
	height: 157px;
	background-image: url(../images/headerImages.jpg);
    background-repeat: no-repeat;
}

#logo {
	width: 375px;
	height: 135px;
	position: relative;
	margin: 0px;
	padding: 0px;
	display: block;
}

#logo span {
	background: url(../images/logo.gif);
	position: absolute;
	width: 100%;
	height: 100%;
	display: block;
}

/* Home Page Styles */
#dealOfTheWeekContainer {
	width: 550px;
	height: 250px;
	position: relative;
	margin-bottom: 10px;
	background-image: url(../images/dealOfTheWeekBg.gif);
	background-repeat: no-repeat;
}

#dealOfTheWeekContainer #dealOfTheWeekTitle {
	width: 530px;
	height: 40px;
	position: absolute;
	top: 5px;
	left: 7px;
	background-color: <?php echo $dowHeaderBg; ?>;
	border-left: 3px solid <?php echo $dowBorderColor; ?>;
	border-right: 3px solid <?php echo $dowBorderColor; ?>;
	border-top: 3px solid <?php echo $dowBorderColor; ?>;
}

#dealOfTheWeekContainer #dealOfTheWeekTitle h2 {
	color: <?php echo $dowH2; ?>;
	margin: 0px;
	padding: 0px;
	margin-left: 10px;
	margin-top: 7px;
}

#dealOfTheWeekContainer #dealOfTheWeekContent {
	width: 530px;
	height: 180px;
	position: absolute;
	top: 45px;
	left: 7px;
	background-color: <?php echo $dowContentBg; ?>;
	border-left: 3px solid <?php echo $dowBorder; ?>;
	border-right: 3px solid <?php echo $dowBorder; ?>;
	border-bottom: 3px solid <?php echo $dowBorder; ?>;
}

#dealOfTheWeekContainer #dealOfTheWeekContent #dealOfTheWeekProductName {
	margin: 0px;
	padding: 0px;
	position: absolute;
	top: 10px;
	left: 10px;
	border: 1px solid <?php echo $dowContentBg; ?>;
	width: 200px;
	height: 40px;
	overflow: hidden;
}

#dealOfTheWeekContainer #dealOfTheWeekContent #dealOfTheWeekProductName a {
	color: <?php echo $dowProductName; ?>;
}

#dealOfTheWeekContainer #dealOfTheWeekContent #dealOfTheWeekProductName a:hover {
	text-decoration: underline;
}

#dealOfTheWeekContainer #dealOfTheWeekContent #dealOfTheWeekProductDescription {
	border: 1px solid <?php echo $dowContentBg; ?>;
	position: absolute;
	top: 55px;
	left: 10px;
	width: 200px;
	height: 100px;
}

#dealOfTheWeekContainer #dealOfTheWeekContent #dealOfTheWeekProductImage {
	border: 1px solid <?php echo $dowContentBg; ?>;
	position: absolute;
	top: 5px;
	left: 215px;
}

#dealOfTheWeekContainer #dealOfTheWeekContent #dealOfTheWeekProductWasPrice {
	border: 1px solid <?php echo $dowContentBg; ?>;
	position: absolute;
	top: 50px;
	left: 390px;
	width: 130px;
	height: 20px;
	margin: 0px;
	font-size: 10pt;
	color: <?php echo $dowWasPrice; ?>;
	text-decoration: line-through;
	text-align: center;
}

#dealOfTheWeekContainer #dealOfTheWeekContent #dealOfTheWeekProductNowPrice {
	border: 1px solid <?php echo $dowContentBg; ?>;
	position: absolute;
	top: 80px;
	left: 390px;
	width: 130px;
	height: 40px;
	margin: 0px;
	font-size: 12pt;
	color: <?php echo $dowNowPrice; ?>;
	text-align: center;
}

#dealOfTheWeekContainer #dealOfTheWeekContent #dealOfTheWeekBuyNowButton {
	border: 1px solid <?php echo $dowContentBg; ?>;
	position: absolute;
	top: 120px;
	left: 415px;
}

/*** Offers of the Week ***/
#offersOfTheWeekContainer {
	width: 550px;
	height: 216px;
	position: relative;
	margin-bottom: 10px;
}

#offersOfTheWeekContainer #offersOfTheWeekTitle {
	position: absolute;
	top: 0px;
	left: 0px;
}

#offersOfTheWeekContainer #offersOfTheWeekContent {
	position: absolute;
	border: 1px solid <?php echo $oowBorder ?>;
	width: 550px;
	height: 200px;
	top: 25px;
	left: 0px;
}

#offersOfTheWeekContainer #offersOfTheWeekContent a {
	color: <?php echo $oowProductName; ?>;
}

#offersOfTheWeekContainer #offersOfTheWeekContent .offerImageContainer {
	height: 140px;
	width: 140px;
	line-height: 140px;
}

.offerContainer {
	width: 175px;
	text-align: center;
	float: left;
	margin-left: 3px;
	margin-right: 3px;
}

.offerContainer h3 {
	margin: 0px;
	font-size: 11pt;
}

.offerContainer h6 {
	color: <?php echo $oowPrice; ?>;
	margin: 0px;
	font-size: 10pt;
}
.offerImageContainer {
	margin: auto;
	margin-bottom: 5px;
}

/* Top Brands */
#topBrandsContainer {
	width: 550px;
	height: 380px;
	position: relative;
	margin-bottom: 10px;
	margin-top: 10px;
}

#topBrandsContainer #topBrandsTitle {
	position: absolute;
	top: 0px;
	left: 0px;
}

#topBrandsContainer #topBrandsContent {
	position: absolute;
	border: 1px solid <?php echo $tbBorder ?>;
	width: 550px;
	height: 360px;
	top: 25px;
	left: 0px;
}

.brandContainer {
	height: 110px;
	width: 170px;
	float: left;
	margin-top: 3px;
	margin-right: 3px;
	margin-left: 3px;
	text-align: center;
	overflow: hidden;
}

.brandContainer img {
	border: 3px solid <?php echo $tbBrandContainerBorder; ?>;
	width: 175px;
}

.brandContainer img:hover {
	border: 3px solid <?php echo $tbBrandContainerBorderOver; ?>;
}

.brandContainer h3 {
	margin: 0px;
}

.brandContainer a {
	color: <?php echo $tbBrandName; ?> !important;
}
.brandContainer a:visited {
	color: <?php echo $tbBrandName; ?> !important;
}

.brandContainer a:hover {
	color: <?php echo $tbBrandName; ?>;
	text-decoration: underline;
}
/* Other sites styles */
#otherSitesContainer {
	width: 625px;
	height: 63px;
}

#otherSites {
	margin: 0px;
	padding: 0px;
	height: 50px;
}

#otherSites li {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12pt;
	color: #4B4B4B;
	font-weight: bold;
	display: inline;
	display: block;
	float: left;
}

#otherSites a {
	text-decoration: none;
	color: #FFFFFF;
}

#otherSites a:hover {
	text-decoration: none;
	color: #FFFFFF;
}

#otherSites #otherSites-dive {
	background-image: url(../images/otherSitesDive.gif);
	height: 63px;
	width: 122px;
	text-align: center;
}

#otherSites #otherSites-dive div {
	margin-top: 6px;
}

#otherSites #otherSites-swim {
	background-image: url(../images/otherSitesSwim.gif);
	height: 63px;
	width: 122px;
	text-align: center;
}

#otherSites #otherSites-swim div {
	margin-top: 6px;
}

#otherSites #otherSites-ski {
	background-image: url(../images/otherSitesSki.gif);
	height: 63px;
	width: 122px;
	text-align: center;
}

#otherSites #otherSites-ski div {
	margin-top: 6px;
}

#otherSites #otherSites-clay {
	background-image: url(../images/otherSitesClayField.gif);
	height: 63px;
	width: 122px;
	text-align: center;
}

#otherSites #otherSites-clay div {
	margin-top: 6px;
}

#otherSites #otherSites-clothing {
	background-image: url(../images/otherSitesClothing.gif);
	height: 63px;
	width: 122px;
	text-align: center;
}

#otherSites #otherSites-clothing div {
	margin-top: 6px;
}
/* Shop By Dept */
#shopByDept {
	padding: 0px;
	width: 189px;
	border: 0px;
	position: relative;
	top: -30px;
	list-style: none;
	margin-bottom: 10px;
	margin-top: 0px;
	margin-right: 5px;
	margin-left: 35px;
	padding: 0px;
	text-indent: -1em;	
}
html>body #shopByDept {
	top: -10px;	
}

#shopByDept a {
	text-decoration: none;
	background-color: <?php echo $shopByDeptBg; ?>;
	color: #000000;
	display: block;
}

#shopByDept a:hover {
	text-decoration: none;
	background-color: <?php echo $shopByDeptBgOver; ?>;
	color: #FFFFFF;
	display: block;
}

#shopByDept li {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
	font-weight: bold;
	display: block;
	float: left;
	height: 26px;
	margin: 0px;
	padding: 0px;
}
#shopByDept li.dept {
	border-left: 1px solid <?php echo $shopByDeptBorderColor; ?>;
	border-right: 1px solid <?php echo $shopByDeptBorderColor; ?>;
	width: 189px;
	padding: 0px;
	height: 25px;
	line-height: 25px;
	text-indent: 5px;
}
html>body #shopByDept li.dept {
	width: 187px;
}
#shopByDept #shopByDept-title {
	background-image: url(../images/shopByDeptBg.gif);
	text-align: center;
	color: #FFFFFF;
	width: 189px;
}

#shopByDept #shopByDept-title div {
	margin-top: 4px;
}

#shopByBrandContainer {
	float: right;
	margin-right: 5px;
	height: 20px;
	border: 0px solid #f00;
	position: relative;
	top: -25px;
}

#shopByBrandContainer select {
	width: 190px;
}

#shopByTagContainer {
	float: right;
	margin-right: 5px;
	height: 20px;
	border: 0px solid #FFF;
	position: relative;
	top: -37px;
}
html>body #shopByTagContainer {
	top: -18px;
}
#shopByTagContainer select {
	width: 190px;
}
/* Shopping Bag Styles */
#shoppingBagContainer {
	width: 170px;
	height: 120px;
	margin: 0px;
	padding: 0px;
	position: relative;
	margin-bottom: 5px;
}

#shoppingBagContainer #shoppingBagTitle {
	position: absolute;
	top: 0px;
}

#shoppingBagContainer #shoppingBagCheckoutNow {
	position: absolute;
	top: 90px;
}

#shoppingBagContainer #shoppingBagItems {
	position: absolute;
	width: 150px;
	top: 5px;
	left: 10px;
	text-align: right;
}

#shoppingBagContainer #shoppingBagItems strong {
	position: absolute;
	left: 0px;
	font-size: 10pt;
}

#shoppingBagContainer #shoppingBagTotal {
	position: absolute;
	width: 150px;
	top: 25px;
	left: 10px;
	text-align: right;
	font-size: 10pt;
}

#shoppingBagContainer #shoppingBagTotal strong {
	position: absolute;
	left: 0px;
	font-size: 10pt;
}

#shoppingBagContainer #shoppingBagContent {
	height: 50px;
	width: 168px;
	background-image: url(../images/shopBagBg.gif);
	background-repeat: repeat-y;
	color: <?php echo $shopBagText; ?>;
	margin: 0px;
	padding: 0px;
	position: absolute;
	top: 40px;
	font-size: 10pt;
}