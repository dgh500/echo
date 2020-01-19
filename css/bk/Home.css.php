<?php
header('Content-Type: text/css');
require('Colors.php');
?>

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