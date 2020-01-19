<?php
header('Content-Type: text/css');
?>
html, body {
	margin: 0px;
	padding: 0px;
	font-family: Arial, Sans-Serif;
	font-size: 10pt;
}
body {
	background-image: url('../images/horizBg.gif');
	background-position: center;
	background-attachment: fixed;
	background-repeat: repeat-y;
	background-color: #000;
	text-align: center;
}
img {
	border: 0px;
}
a, a:visited {
	color: #000;
	text-decoration: none;
	border: none;
}
a:hover {
	color: #000;
	text-decoration: underline;
}

hr {
	height: 1px;
	color: #000;
	background-color: #000;
	border: 0px;
	width: 90%;
}

/************
	SUGGEST STYLES
*************/
.ac_results {
	border: 1px solid gray;
	background-color: white;
	padding: 0;
	margin: 0;
	list-style: none;
	position: absolute;
	z-index: 10000;
	display: none;
}

.ac_results li {
	padding: 2px 5px;
	white-space: nowrap;
	color: #101010;
	text-align: left;
}

.ac_over {
	cursor: pointer;
	background-color: #FECB9C;
}

.ac_match {
	text-decoration: underline;
	color: black;
}

/************
	LAYOUT STYLES
*************/
#centreCol {
	margin-left: auto;
	margin-right: auto;
	padding: 0px;
	width: 978px;
	border: 0px solid #000;
	background-color: #FFF;
	text-align: left;
}
#header {
	width: 978px;
	height: 102px;
	padding: 0px;
	margin: 0px;
	background-image: url('../images/headerBg.gif');
	background-position: right;
	background-repeat: no-repeat;
	background-color: #000;
}
#header #headerRight {
	float: right;
	width: 300px;
	height: 102px;
	position: relative;
}
#header #headerLeft {
	float: left;
	width: 400px;
	height: 102px;
	position: relative;
	border: 0px solid #FFF;
	padding: 0px;
	margin: 0px;
}
#header #headerLeft img {
	position: absolute;
	bottom: 0px;
	left: 0px;
}
#header #headerRight #topRightNav {
	height: 30px;
	padding-right: 20px;
	line-height: 30px;
	text-align: right;
	font-weight: bold;
	width: 275px;
	float: right;
	font-size: 9pt;
	background-image: url('../images/headerTopRightBg.gif');
}
#header #headerRight #topRightNavActive {
	height: 30px;
	padding-right: 20px;
	line-height: 30px;
	text-align: right;
	font-weight: bold;
	width: 275px;
	float: right;
	font-size: 9pt;
	background-image: url('../images/headerTopRightBgActive.gif');
}
#header #headerRight #searchBar {
	border: 1px solid #000;
	width: 300px;
	height: 29px;
	background-image: url('../images/searchBarBg.gif');
	position: absolute;
	bottom: 5px;
	right: 5px;
}
#header #headerRight #searchBar div {
	position: relative;
	width: 300px;
	height: 29px;
}
#header #headerRight #searchBar div #q {
	position: absolute;
	top: 4px;
	left: 45px;
	width: 170px;
}
#header #headerRight #searchBar div #searchIcon {
	position: absolute;
	top: 3px;
	left: 10px;
}
#header #headerRight #searchBar div #searchButton {
	position: absolute;
	top: 3px;
	right: 3px;
	width: auto !important;
	height: auto !important;
}

/* END HEADER, START NAV */

#navigation {
	width: 978px;
	height: 29px;
	line-height: 28px;
	float: left;
	background-image: url("../images/navBg.gif");
	color: #FFF;
	font-weight: bold;
}
#navigation ul {
	margin: 0px;
	padding: 0px;
	list-style: none;
}
#navigation li {
	margin: 0px;
	padding: 0px;
	display: block;
	float: left;
	border: 0px solid #000;
	width: 120px;
	text-align: center;
}
#navigation li a, #navigation li a:visited {
	color: #FFF;
	text-decoration: none;
}
#navigation li a:hover {
	color: #FFF;
	text-decoration: underline;
}

/* END NAV, START TOP BRANDS */

#topBrands {
	width: 978px;
	height: 100px;
	padding: 0px;
	margin: 0px;
	background-color: #FFF;
	border-bottom: 1px solid #000;
	float: left;
}
#topBrands .singleTopBrand {
	width: 185px;
	height: 90px;
	background-color: #FFF;
	float: left;
	text-align: center;
	padding: 0px;
	margin: 0px;
	padding-top: 10px;
}
#topBrands #allBrandsLinkContainer {
	height: 100px;
	float: right;
}
#topBrands a, #topBrands a:visited {
	text-decoration: none;
	color: #000;
}
#topBrands a:hover {
	text-decoration: underline;
}
#topBrands img {
	border: 0px;
}

/* END TOP BRANDS, START LEFT/RIGHT COL + FOOTER */

#leftCol {
	width: 165px;
	background-color: #FFF;
	float: left;
	margin-top: 5px;
}
#rightCol {
	width: 795px;
	padding-left: 10px;
	background-color: #FFF;
	float: right;
	margin: 0px;
	padding: 0px;
	margin-top: 5px;
	margin-right: 3px;
}
#rightCol img {
	float: right;
}
#footer {
	width: 978px;
	background-image: url("../images/footerBg.gif");
	background-position: top;
	background-repeat: repeat-x;
	float: left;
	margin-top: 5px;
	padding-top: 10px;
	padding-bottom: 10px;
	text-align: center;
	font-weight: bold;
	font-size: 9pt;
}

/* SECTION STYLES */
.section {
	width: 160px;
	background-color: #FFF;
	margin-left: 5px;
	margin-bottom: 5px;
}
.section #signUpEmail {
	width: 130px;
	margin-top: 5px;
	margin-left: 8px;
	color: #CCC;
}
.section #signUpEmail.focused {
	width: 130px;
	margin-top: 5px;
	margin-left: 8px;
	color: #000;
}
.section #subscribeButton {
	margin-top: 5px;
	margin-left: 35px;
}
.sectionHeader {
	width: 162px;
	height: 29px;
	line-height: 27px;
	text-align: center;
	color: #FFFFFF;
	font-weight: bold;
	border: 1px solid #000;
	border-bottom: 0px;
	background-image: url('../images/sectionHeaderBg.gif');
}
.sectionBody {
	width: 162px;
	border: 1px solid #000;
	background-image: url('../images/sectionBg.gif');
	background-position: bottom;
	background-repeat: repeat-x;
}
.sectionBody ul {
	margin: 0px;
	padding: 10px 0px 10px 0px;
	list-style: none;
}
.sectionBody li {
	margin: 0px;
	padding: 0px;
	padding-left: 10px;
	font-weight: bold;
}
.sectionBody a, .sectionBody a:visited {
	text-decoration: none;
	color: #000;
}
.sectionBody a:hover {
	text-decoration: underline;
}

/* HOMEPAGE RIGHT COL */
#welcomeText {
	padding: 5px;
}
#welcomeText p {
	margin: 0px;
	line-height: 17px;
}
#indexPageContainer a {
	text-decoration: underline;
}
#indexPageContainer #welcomeBox {
	width: 781px;
	height: 411px;
	border: 2px solid #000;
	margin-bottom: 5px;
	background-color: #000;
	background-image: url("../images/blackWatermark.jpg");
	background-position: bottom left;
	background-repeat: no-repeat;
	color: #FFF;
	position: relative;
}
#indexPageContainer #welcomeBox a {
	color: #FFF;
	text-decoration: none;
}
#indexPageContainer #welcomeBox a:hover {
	color: #FFF;
	text-decoration: underline;
}
#indexPageContainer #welcomeBox p {
	margin: 10px;
	font-size: 9pt;
}
#indexPageContainer #welcomeBox h1 {
	color: #FFF;
	background-image: url("../images/blackSectionBg.gif");
	background-repeat: repeat-x;
	height: 29px;
	line-height: 29px;
	font-size: 12pt;
	padding-left: 10px;
	margin: 0px;
}
/**** Welcome Text ***/
#welcomeText {
	border: 0px solid #FF0;
	width: 520px;
	height: 220px;
	overflow: hidden;
	margin: 0px;
	padding: 0px;
}

/***** Front Page Brands Section *******/
.fpBrands {
	width: 781px;
	border: 2px solid #000;
	margin-bottom: 5px;
	margin-top: 5px;
	background-color: #FFF;
	background-position: bottom left;
	background-repeat: no-repeat;
	color: #000;
	position: relative;
}
.fpBrands a.white {
	color: #FFF;
	text-decoration: none;
}
.fpBrands a {
	color: #000;
	text-decoration: none;
}
.fpBrands a:hover {
	color: #0;
	text-decoration: underline;
}
.fpBrands p {
	margin: 10px;
	font-size: 9pt;
}
.fpBrands h1 {
	color: #FFF;
	background-image: url("../images/blackSectionBg.gif");
	background-repeat: repeat-x;
	height: 29px;
	line-height: 29px;
	font-size: 12pt;
	padding-left: 10px;
	margin: 0px;
	text-align: center;
}
.gradBg {
	background-image: url("../images/fpBrandsGradient.png");
	background-repeat: repeat-y;
	background-position: right;
}


/***** Deal of the Day Start *****/
#indexPageContainer #dealOfTheDayContainer {
	border: 0px solid #CCC;
	width: 500px;
	height: 140px;
	margin: 10px;
	position: relative;
	padding: 0px;
}
#indexPageContainer #dealOfTheDayContainer h2 {
	position: absolute;
	right: 160px;
	top: 4px;
	text-align: right;
	margin: 0px;
	font-size: 16pt;
	font-weight: bold;
	color: #F00;
}
#indexPageContainer #dealOfTheDayContainer img {
	position: absolute;
	right: 0px;
	top: 0px;
	border: 2px solid #f00;
}
#indexPageContainer #dealOfTheDayContainer p {
	position: absolute;
	right: 160px;
	top: 35px;
	text-align: right;
	margin: 0px;
	font-size: 10pt;
	width: 300px;
}
#indexPageContainer #dealOfTheDayContainer #dealOfTheDayWasPrice {
	position: absolute;
	right: 380px;
	bottom: 3px;
	text-align: right;
	margin: 0px;
	font-size: 14pt;
	font-weight: bold;
}
#indexPageContainer #dealOfTheDayContainer #dealOfTheDayNowPrice {
	position: absolute;
	right: 160px;
	bottom: 3px;
	text-align: right;
	margin: 0px;
	font-size: 16pt;
	font-weight: bold;
	color: #F00;
}
/***** Brand New Start *****/
#indexPageContainer #indexBrandNewContainer {
	width: 250px;
	height: 190px;
	border: 0px solid #CCC;
	background-color: #FFF;
	float: right;
	position: absolute;
	top: 30px;
	right: 0px;
}
#indexPageContainer #indexBrandNewContainer #indexBrandNewBanner {
	position: absolute;
	top: 0px;
	left: 0px;
}
#indexPageContainer #indexBrandNewContainer #indexBrandNewHeader {
	position: absolute;
	top: 0px;
	right: 0px;
	background-image: url("../images/redSectionBg.gif");
	background-repeat: repeat-x;
	margin: 0px;
	width: 221px;
	font-size: 10pt;
	text-align: center;
	padding: 6px 0px 6px 0px;
}
#indexPageContainer #indexBrandNewContainer img {
	position: absolute;
	top: 40px;
	right: 40px;
}
/***** Best Seller Start *****/
#indexPageContainer #indexBestSellerContainer {
	width: 250px;
	height: 190px;
	border: 0px solid #CCC;
	background-color: #FFF;
	position: absolute;
	top: 221px;
	right: 0px;
}
#indexPageContainer #indexBestSellerContainer #indexBestSellerBanner {
	position: absolute;
	top: 0px;
	left: 0px;
}
#indexPageContainer #indexBestSellerContainer #indexBestSellerHeader {
	position: absolute;
	top: 0px;
	right: 0px;
	background-image: url("../images/redSectionBg.gif");
	background-repeat: repeat-x;
	margin: 0px;
	width: 221px;
	font-size: 10pt;
	text-align: center;
	padding: 2px 0px 2px 0px;
}

#indexPageContainer #indexBestSellerContainer img {
	position: absolute;
	top: 40px;
	right: 40px;
}
.homepageProduct {
	width: 800px;
	height: 150px;
	background-color: #ae0001;
	border: 2px solid #000;
	margin-top: 10px;
	background-image: url("../images/echoWatermark.gif");
	background-repeat: no-repeat;
	background-position: 98% 90%;
	position: relative;
}
.homepageProduct img {
	position: absolute;
	top: 30px;
	left: 20px;
}
.homepageProduct a {
	color: #FFF;
}
.homepageProduct h3 {
	margin: 0px;
	position: absolute;
	top: 5px;
	left: 20px;
	color: #FFF;
}
.homepageProduct p {
	margin: 0px;
	position: absolute;
	top: 30px;
	left: 140px;
	width: 650px;
	color: #FFF;
}
.homepageProduct .homepageProductPrice {
	position: absolute;
	top: 100px;
	left: 140px;
	font-size: 16px;
	font-weight: bold;
}
.homepageProduct .homepageProductPrice .wasPrice {
	color: #FFF;
	text-decoration: line-through;
}
.homepageProduct .homepageProductPrice .nowPrice {
	color: #f00;
	font-size: 20px;
}
.homepageProduct .homepageProductMore {
	position: absolute;
	top: 100px;
	color: #FFF;
	border-left: 2px solid #D87F81;
	left: 265px;
	padding-left: 20px;
	font-size: 14px;
	font-weight: bold;
	line-height: 20px;
}

/* CATEGORY VIEW */
#categoryViewListContainer {
	border: 1px solid #000;
}
#categoryViewListContainerTitle {
	border: 1px solid #555;
	height: 29px;
	background-image: url("../images/blackSectionBg.gif");
	background-repeat: repeat-x;
	background-position: top;
	position: relative;
	color: #FFFFFF;
	margin: 0px;
	padding: 0px;
}
#categoryViewListContainer #categoryViewListContainerTitle h2 {
	margin: 0px;
	padding: 0px;
	height: 29px;
	width: 320px;
	font-size: 11pt;
	position: relative;
	top: 5px;
	left: 10px;
}

#categoryViewListContainer #categoryViewListContainerTitle a {
	color: #FFFFFF;
}

#categoryViewListContainer #categoryViewListContainerTitle #sortByContainer {
	width: 200px;
	height: 32px;
	position: absolute;
	right: 5px;
	top: 3px;
}

#categoryViewListContainer #categoryViewListContainerTitle #sortByContainer select {
	width: 200px;
}
#categoryViewListContainer #manufacturerListContainer {
	font-weight: bold;
	font-size: 10pt;
	clear: both;
	float: left;
	display: block;
	width: 793px;
	background-image: url("../images/brandListBg.gif");
	background-position: bottom;
	background-repeat: repeat-x;
	border-bottom: 1px solid #000;
}

#categoryViewListContainer #manufacturerList {
	margin: 0px;
	padding: 0px;
	list-style-image: none;
	list-style-type: none;
	clear: both;
	float: left;
}

#categoryViewListContainer #manufacturerList li {
	margin: 0px;
	padding: 0px;
	width: 110px;
	float: left;
	height: 32px;
	position: relative;
	text-align: center;
}

#categoryViewListContainer #manufacturerList li a {
	display: block;
	height: 32px;
	padding-left: 5px;
	padding-right: 5px;
	position: relative;
}

#categoryViewListContainer #manufacturerList li a:hover {
	text-decoration: underline;
}

/* CategoryListBrandNewView */
#brandNewContainer {
	width: 397px;
	height: 144px;
	position: relative;
	border: 0px;
	border-bottom: 1px solid #000;
	border-right: 0px solid #000;
	background-image: url(../images/brandNewBanner.jpg);
	background-repeat: no-repeat;
    float: left;
}
#brandNewContainer #brandNewContent {
	width: 367px;
	height: 144px;
	float: right;
	background-color: #FFF;
	border-left: 1px solid #000;
    text-align: left;
	position: relative;
	overflow: hidden;
}
#brandNewContainer #brandNewContent img {
	float: left;
	margin: 0px;
	border: 0px;
}
#brandNewContainer #brandNewContent #brandNewName {
	margin: 0px;
	padding: 0px;
	position: absolute;
	top: 5px;
	font-size: 10pt;
    left: 150px;
    text-align: left;
	overflow: hidden;
}

#brandNewContainer #brandNewContent #brandNewName a {
	color: #000;
}

#brandNewContainer #brandNewContent #brandNewName a:hover {
	text-decoration: underline;
	color: #000;
}
#brandNewContainer #brandNewContent p {
	position: absolute;
	top: 40px;
    left: 150px;
    width: 200px;
	margin: 0px;
	font-size: 10pt;
	text-align: left;
}
#brandNewContainer #brandNewContent #brandNewWasPrice {
	position: absolute;
	bottom: 5px;
    left: 150px;
	margin: 0px;
	font-size: 11pt;
	text-align: left;
	color: #555;
	font-weight: bold;
	text-decoration: line-through;
}
#brandNewContainer #brandNewContent #brandNewNowPrice {
	position: absolute;
	bottom: 5px;
    left: 250px;
	margin: 0px;
	font-size: 11pt;
	text-align: left;
	color: #F00;
	font-weight: bold;
}
/* CategoryListBestSellerView */
#bestSellerContainer {
	width: 395px;
	height: 144px;
	position: relative;
	border: 0px;
	border-bottom: 1px solid #000;
	border-right: 1px solid #000;
	background-image: url(../images/bestSellerBanner.jpg);
	background-repeat: no-repeat;
    float: left;
}
#bestSellerContainer #bestSellerContent {
	width: 366px;
	height: 144px;
	float: right;
	background-color: #FFF;
	border-left: 1px solid #000;
    text-align: left;
	position: relative;
	overflow: hidden;
}
#bestSellerContainer #bestSellerContent img {
	float: left;
	margin: 0px;
	border: 0px;
}
#bestSellerContainer #bestSellerContent #bestSellerName {
	margin: 0px;
	padding: 0px;
	position: absolute;
	top: 5px;
	font-size: 10pt;
    left: 150px;
    text-align: left;
	overflow: hidden;
}

#bestSellerContainer #bestSellerContent #bestSellerName a {
	color: #000;
}

#bestSellerContainer #bestSellerContent #bestSellerName a:hover {
	text-decoration: underline;
	color: #000;
}
#bestSellerContainer #bestSellerContent p {
	position: absolute;
	top: 40px;
    left: 150px;
    width: 200px;
	margin: 0px;
	font-size: 10pt;
	text-align: left;
}
#bestSellerContainer #bestSellerContent #bestSellerWasPrice {
	position: absolute;
	bottom: 5px;
    left: 150px;
	margin: 0px;
	font-size: 11pt;
	text-align: left;
	color: #555;
	font-weight: bold;
	text-decoration: line-through;
}
#bestSellerContainer #bestSellerContent #bestSellerNowPrice {
	position: absolute;
	bottom: 5px;
    left: 250px;
	margin: 0px;
	font-size: 11pt;
	text-align: left;
	color: #F00;
	font-weight: bold;
}

.topProductContainer {
	width: 390px;
	height: 100px;
	float: left;
	border: 1px solid #000;
	margin: 5px 2px 5px 2px;
	position: relative;
}
.topProductContainer .wasPrice {
	position: absolute;
	bottom: 5px;
    left: 50px;
	margin: 0px;
	font-size: 11pt;
	text-align: left;
	color: #555;
	font-weight: bold;
	text-decoration: line-through;
}
.topProductContainer .nowPrice {
	position: absolute;
	bottom: 5px;
    left: 150px;
	margin: 0px;
	font-size: 16pt;
	text-align: left;
	color: #F00;
	font-weight: bold;
}


/***	Featured Product	***/
#featuredProductContainer {
	width: 793px;
	height: 169px;
	position: relative;
    margin: 0px;
	border-bottom: 1px solid #000;
	background-image: url("../images/sectionBg.gif");
	background-position: bottom;
	background-repeat: repeat-x;
    clear: both;
}

#featuredProductContainer #featuredProductTitle {
	width: 793px;
	height: 29px;
	margin: 0px;
	padding: 0px;
	border: 0px;
	background-image: url("../images/blackSectionBg.gif");
	background-repeat: repeat-x;
	background-position: top;
	color: #FFF;
}

#featuredProductContainer #featuredProductTitle h2 {
	width: 500px;
	line-height: 29px;
	margin: 0px;
	padding: 0px;
	padding-left: 10px;
	font-size: 11pt;
}

#featuredProductContainer #featuredProductContent {
	width: 793px;
	height: 140px;
	position: relative;
}

#featuredProductContainer #featuredProductContent #featuredProductName {
	margin: 0px;
	padding: 0px;
	border: 0px solid #F00;
	width: 550px;
	height: 20px;
	overflow: hidden;
	position: absolute;
	left: 150px;
	top: 10px;
}
#featuredProductContainer #featuredProductContent #featuredProductDescription {
	margin: 0px;
	padding: 0px;
	border: 0px solid #F00;
	width: 550px;
	height: 70px;
	overflow: hidden;
	position: absolute;
	left: 150px;
	top: 30px;
}
#featuredProductContainer #featuredProductContent #featuredProductImage {
	margin: 0px;
	padding: 0px;
	border: 0px solid #F00;
	border-right: 1px solid #000;
	overflow: hidden;
	position: absolute;
	left: 0px;
	top: 0px;
}

#featuredProductContainer #featuredProductContent #featuredProductWasPrice {
	margin: 0px;
	padding: 0px;
	border: 0px solid #F00;
	width: 100px;
	height: 20px;
	overflow: hidden;
	position: absolute;
	left: 150px;
	top: 110px;
	font-size: 12pt;
	color: #555;
	text-decoration: line-through;
}

#featuredProductContainer #featuredProductContent #featuredProductNowPrice {
	margin: 0px;
	padding: 0px;
	border: 0px solid #F00;
	width: 100px;
	height: 20px;
	overflow: hidden;
	position: absolute;
	left: 260px;
	top: 110px;
	font-size: 12pt;
	color: #F00;
}
#featuredProductContainer #featuredProductContent #featuredProductBuyNowButton {
	margin: 0px;
	padding: 0px;
	border: 0px solid #F00;
	overflow: hidden;
	position: absolute;
	left: 360px;
	top: 110px;
	font-size: 12pt;
	color: #F00;
}

/* CATEGORY LIST PRODUCT */
.categoryViewProductContainer {
	border: 0px solid #000;
	border-bottom: 1px solid #CCC;
	width: 394px;
	position: relative;
	height: 160px;
	float: left;
	overflow: hidden;
}

.categoryViewProductImageContainer {
	width: 140px;
	height: 140px;
	margin-top: 3px;
	text-align: center;
	line-height: 140px;
}

.categoryViewProductImageContainer img {
	vertical-align: middle;
	border: 0px;
}

.categoryViewProductContainer .productDetailsContainer {
	width: 230px;
	height: 155px;
	position: absolute;
	top: 3px;
	left: 160px;
	background-color: #fff;
}

.categoryViewProductContainer .productDetailsContainer h3 {
	margin: 0px;
	font-size: 11pt;
	height: 20px;
	width: 220px;
	border: 0px solid #f00;
	position: absolute;
	top: 5px;
	left: 10px;
	overflow: hidden;
	color: #000;
	text-decoration: none;
}
.categoryViewProductContainer .productDetailsContainer .wasPrice {
	width: 100px;
	height: 25px;
	line-height: 25px;
	position: absolute;
	top: 95px;
	left: 10px;
	text-decoration: line-through;
	font-weight: bold;
	font-size: 9pt;
}
.categoryViewProductContainer .productDetailsContainer .nowPrice {
	width: 100px;
	position: absolute;
	top: 115px;
	left: 10px;
	height: 25px;
	line-height: 25px;
	font-weight: bold;
	font-size: 11pt;
	color: #F00;
}
.categoryViewProductContainer .productDetailsContainer .multibuyPrice {
	width: 200px;
	position: absolute;
	top: 132px;
	left: 10px;
	height: 25px;
	line-height: 25px;
	font-weight: bold;
	font-size: 9pt;
	color: #159807;
	font-style: italic;
}
.categoryViewProductContainer .productDetailsContainer .description {
	font-size: 10pt;
	height: 50px;
	width: 220px;
	position: absolute;
	top: 25px;
	left: 10px;
}
.categoryViewProductContainer .productDetailsContainer #viewButton {
	position: absolute;
	left: 130px;
	bottom: 20px;
}
.categoryViewProductContainer .productDetailsContainer #buyNowButton {
	position: absolute;
	left: 130px;
	bottom: 20px;
}
.categoryViewProductContainer .productDetailsContainer #oneHundredSecureButton {
	position: absolute;
	left: 120px;
	bottom: 11px;
}
/* PAGINATION VIEW */
#pageNumbersContainer {
	width: 793px;
	height: 29px;
	line-height: 27px;
	font-weight: bold;
	margin: 0px;
	padding: 0px;
	border: 0px;
	background-image: url("../images/blackSectionBg.gif");
	background-repeat: repeat-x;
	background-position: top;
	color: #FFF;
	clear: both;
}

#pageNumbersContainer a {
	font-weight: normal;
	color: #FFF;
}

#pageNumbersContainer span {
	margin-left: 10px;
	margin-right: 10px;
	margin-top: 0px;
	margin-bottom: 0px;
}

#pageNumbersContainer a.currentPageNumber {
	font-weight: bold;
	color: #FFF;
}

/* MANUFACTURER VIEW */
#manufacturerDescriptionContainer {
	height: 90px;
	border: 0px solid #000;
	text-align: left;
}
#manufacturerDescriptionContainer img {
	float: left;
	border: 0px;
	margin-left: 0px;
	margin-top: 0px;
}
#manufacturerDescriptionText {
	border: 0px solid #f00;
	margin-left: 5px;
	margin-top: 3px;
}
#manufacturerDescriptionText .manufacturerSubDesc {
	clear: both;
	float: left;
	width: 450px;
}
#manufacturerDescriptionText h1 {
	margin: 0px;
	padding: 0px;
}
#manufacturerDescriptionText table {
	width: 300px;
	float: right;
	border: 1px solid #000;
	margin-bottom: 8px
}
#manufacturerDescriptionText td.headerRow {
	background-image: url("../images/blackSectionBg.gif");
	background-repeat: repeat-x;
	background-position: top;
	color: #FFF;
	padding: 5px;
	text-align: center;
}
#manufacturerDescriptionText td.headerRow a {
	color: #FFF;
}
#manufacturerFooterContainer {
	border: 0px solid #000;
	text-align: left;
	padding: 10px;
/*	background-image: url("../images/logoCorner.gif");
	background-position: top left;
	background-repeat: no-repeat;*/
}
#manufacturerFooterContainer img {
	float: left;
	border: 1px solid #000;
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 10px;
}
#manufacturerViewListContainer {
	border: 1px solid #000;
}
#manufacturerViewListContainerTitle {
	border: 1px solid #555;
	height: 29px;
	background-image: url("../images/blackSectionBg.gif");
	background-repeat: repeat-x;
	background-position: top;
	position: relative;
	color: #FFFFFF;
}
#manufacturerViewListContainer #manufacturerViewListContainerTitle h2 {
	margin: 0px;
	height: 32px;
	width: 320px;
	font-size: 11pt;
	position: relative;
	top: 5px;
	left: 10px;
}

#manufacturerViewListContainer #manufacturerViewListContainerTitle a {
	color: #FFFFFF;
}

#manufacturerViewListContainer #manufacturerViewListContainerTitle #sortByContainer {
	width: 200px;
	height: 32px;
	position: absolute;
	right: 5px;
	top: 3px;
}

#manufacturerViewListContainer #manufacturerViewListContainerTitle #sortByContainer select {
	width: 200px;
}
#categoryListContainer #categoryList {
	font-weight: bold;
	font-size: 10pt;
	clear: both;
	float: left;
	display: block;
	width: 793px;
	background-image: url("../images/brandListBg.gif");
	background-position: bottom;
	background-repeat: repeat-x;
	border-bottom: 1px solid #000;
}

#categoryListContainer #categoryList {
	margin: 0px;
	padding: 0px;
	list-style-image: none;
	list-style-type: none;
	clear: both;
	float: left;
}

#categoryListContainer #categoryList li {
	margin: 0px;
	padding: 0px;
	width: 110px;
	float: left;
	height: 32px;
	position: relative;
	text-align: center;
}

#categoryListContainer #categoryList li a {
	display: block;
	height: 32px;
	padding-left: 5px;
	padding-right: 5px;
	position: relative;
}

#categoryListContainer #categoryList li a:hover {
	text-decoration: underline;
}

/* PRODUCT VIEW STYLES */
#productDetailContainer {
	float: left;
	position: relative;
	border: 0px solid #000;
	clear: both;
}

#productDetailContainer #productTitle {
	border: 1px solid #555;
	height: 29px;
	width: 794px;
	background-image: url("../images/blackSectionBg.gif");
	background-repeat: repeat-x;
	background-position: top;
	position: relative;
	color: #FFFFFF;
}
#productDetailContainer #overviewTitle, #productDetailContainer #packageCrosssellTitle, #productDetailContainer #echoDescriptionTitle {
	height: 29px;
	width: 796px;
	background-image: url("../images/blackSectionBg.gif");
	background-repeat: repeat-x;
	background-position: top;
	position: relative;
	float: left;
	margin-top: 5px;
}

#productDetailContainer #overviewTitle h2 {
	margin: 0px;
	height: 29px;
	font-size: 12pt;
	position: relative;
	top: 5px;
	left: 10px;
	color: #FFFFFF;
	font-size: 11pt;
}
#productDetailContainer #echoDescriptionTitle h2 {
	margin: 0px;
	height: 29px;
	font-size: 12pt;
	position: relative;
	top: 5px;
	left: 10px;
	color: #FFFFFF;
	font-size: 11pt;
}
#mainProductImageContainer {
	display: none;
}
.zoomedProductName {
	font-size: 12pt;
	margin: 0px;
}
.zoomedProductText {
	font-size: 8pt;
	margin: 0px;
	margin-bottom: 5px;
}
.zoomedImageContainer {
	display: none;
}
.zoomedImageInnerContainer {
	display: block;
	width: 400px;
	line-height: 400px;
	height: 400px;
	text-align: center;
}
.zoomedImageInnerContainer img {
	line-height: 400px;
}
#productDetailContainer table {
	border-collapse: collapse;
}
#productImage table, #productImage td, #productImage th {
	border: 0px !important;
}
#productDetailContainer table.w100 {
	width: 100%;
}
#productDetailContainer caption {
	font-size: 10pt;
}
#productDetailContainer th {
	font-weight: bold;
	font-size: 10pt;
	padding: 5px;
	border: 1px solid #CCC;
}
#productDetailContainer td {
	font-size: 10pt;
	padding: 5px;
	border: 1px solid #CCC;
}
#productDetailContainer #productTitle h2, #productDetailContainer #packageCrosssellTitle h2 {
	margin: 0px;
	height: 29px;
	width: 770px;
	font-size: 11pt;
	position: relative;
	top: 5px;
	left: 10px;
	color: #FFFFFF;
}

#productDetailContainer #productTitle a {
	color: #FFFFFF;
}

#productDetailContainer #productDetailsTopSection {
	border-left: 1px solid #000;
	border-right: 1px solid #000;
	border-bottom: 1px solid #000;
	width: 794px;
	height: 320px;
	position: relative;
	float: left;
}

#productDetailContainer #productDetailsOverviewSection {
	border: 1px solid #B5B5B5;
	border-top: 0px;
	width: 794px;
	position: relative;
	float: left;
    line-height: 1.5em;
	clear: both;
}

#productDetailsOverviewSection  a {
	text-decoration: none;
}

#productDetailContainer #echoDescriptionOverviewSection {
	border: 1px solid #B5B5B5;
	border-top: 0px;
	width: 794px;
	position: relative;
	float: left;
    line-height: 1.5em;
	clear: both;
}

#productDetailContainer #productSocialNetworkingSection {
	border: 1px solid #000;
	border-top: 0px;
	width: 794px;
	position: relative;
	float: left;
	clear: both;
	overflow: hidden;
	padding: 0px;
	margin: 0px;
}

#productDetailContainer #productSocialNetworkingSection #facebookLikeBox {
	border: 0px solid #000;
	width: 270px;
	float: left;
	padding: 0px;
	margin: 0px;
	text-align: center;
}

#productDetailContainer #productSocialNetworkingSection #trustPilotBox {
	border-left: 1px solid #000;
	width: 498px;
	height: 90px;
	float: left;
	padding: 0px;
	margin: 0px;
}
#productDetailContainer #productSocialNetworkingSection #trustPilotBox img {
	float: none;
}

#echoDescriptionOverviewSection  a {
	text-decoration: none;
}
#productDetailContainer #echoDescriptionOverviewSection div {
	margin: 10px;
	margin-top: 0px;
}

#productDetailContainer #echoDescriptionOverviewSection div div {
	margin: 0px;
}
/* make bold a diff colour??

#productDetailContainer #productDetailsOverviewSection strong, #productDetailContainer #productDetailsOverviewSection strong a {
	color: #00A701;
	font-size: 11pt;
}
#productDetailContainer #productDetailsOverviewSection td strong {
	color: #000;
}*/
#productDetailContainer #productDetailsOverviewSection div {
	margin: 10px;
	margin-top: 0px;
}

#productDetailContainer #productDetailsOverviewSection div div {
	margin: 0px;
}

#readMoreBar {
	width: 775px;
	background-image: url("../images/sectionHeaderBg.gif");
	background-repeat: repeat-x;
	text-align: center;
	height: 29px;
	line-height: 29px;
	color: #FFFFFF;
	font-weight: bold;
	margin-top: 10px;
}
#readMoreLink {
	width: 775px;
	display: block;
	color: #FFF;
}
#readMoreLink:hover {
	cursor: pointer;
	cursor: hand;
	color: #FFF;
}

/* Package Cross Sell Section */
#productDetailContainer #packageCrosssellSection {
	border: 1px solid #B5B5B5;
	border-top: 0px;
	width: 794px;
	position: relative;
	float: left;
    line-height: 1.5em;
	clear: both;
}

#packageCrosssellSection  a {
	text-decoration: underline;
}

#packageCrosssellSection .stackContainer {
	width: 380px;
	height: 150px;
	overflow: hidden;
	border: 1px solid #ccc;
	float: left;
	margin: 6px !important;	/* Override the rule above */
}

.stackContainer .imageContainer {
	display: block;
	float: left;
	height: 100px;
	line-height: 100px;
	width: 100px;
	border: 1px solid #FFF;
	margin: 10px;
	padding: 0px;
}

.stackContainer .stackDescription {
	display: block;
	float: left;
	border: 1px solid #FFF;
	padding: 0px;
	width: 250px;
	margin-top: 10px;
}
.stackContainer .stackDescription .wasPrice {
	color: #666;
	font-weight: bold;
	text-decoration: line-through;
}
.stackContainer .stackDescription .nowPrice {
	color: #F00;
	font-weight: bold;
}

/* End Package Cross Sell Section */

#productDetailContainer #additionalImagesSection {
	border: 1px solid #B5B5B5;
	border-top: 0px;
	width: 793px;
	position: relative;
	float: left;
}

#productDetailContainer #additionalImagesSection div {
	margin: 10px;
	float: left;
	width: 110px;
	height: 110px;
	text-align: center;
}

#productDetailContainer #additionalImagesTitle {
	height: 29px;
	width: 795px;
	background-image: url("../images/blackSectionBg.gif");
	background-repeat: repeat-x;
	background-position: center top;
	position: relative;
	float: left;
	margin-top: 5px;
}

#productDetailContainer #additionalImagesTitle h2 {
	margin: 0px;
	height: 32px;
	width: 530px;
	font-size: 12pt;
	position: relative;
	top: 5px;
	left: 10px;
	color: #FFFFFF;
}
#multibuyTable {
	float: right;
	border: 0px solid #000;
	border-collapse: collapse;
	width: 220px;
	margin-top: 35px;
}

#multibuyTable th {
	padding: 10px;
}

#multibuyTable td {
	font-size: 10pt;
	text-align: center;
	font-weight: bold;
	border: 0px;
}
#multibuyTable td.altCell {
	background-color: #FFF;
	color: #b60000;
}

#multibuyTable td.priceAltCell {
	background-color: #DEDEDE;
}

#multibuyTable td.priceCell {
	color: #F00;
	font-weight: bold;
	background-color: #FFFFFF;
}

#multibuyTable tr {
	border-bottom: 0px solid #CCC;
}

#multibuyTable .multibuyHeading {
	font-weight: bold;
	font-size: 10pt;
	text-align: right;
	width: 150px;
}
#multibuySection {
	border: 0px solid #f00;
	border-left: 1px solid #000;
	position: absolute;
	top: 0px;
	right: 0px;
	width: 248px;
	height: 150px;
	background-image: url("../images/multibuyBg.png");
	background-repeat: repeat-y;
}
#multibuySection #multibuyBanner {
	position: absolute;
	top: 0px;
}
#multibuySection #multibuyHeader {
	position: absolute;
	top: 0px;
	right: 0px;
}

#secureSection {
	border: 0px solid #f00;
	border-left: 1px solid #000;
	position: absolute;
	top: 0px;
	right: 0px;
	width: 248px;
	height: 150px;
	background-image: url("../images/secureBg.jpg");
	background-repeat: no-repeat;
	background-position: bottom right;
}
#secureSection #secureBanner {
	position: absolute;
	top: 0px;
}
#secureSection #secureHeader {
	position: absolute;
	top: 0px;
	right: 0px;
}
#productDetailContainer #productDetailsSimilarSection {
	border: 1px solid #B5B5B5;
	border-top: 0px;
	width: 793px;
	position: relative;
	float: left;
}

#productDetailContainer #similarTitle {
	height: 29px;
	width: 795px;
	background-image: url("../images/blackSectionBg.gif");
	background-repeat: repeat-x;
	background-position: center top;
	position: relative;
	float: left;
	margin-top: 5px;
}

#productDetailContainer #similarTitle h2 {
	margin: 0px;
	width: 750px;
	font-size: 11pt;
	position: relative;
	top: 5px;
	left: 10px;
	color: #FFFFFF;
}

#productDetailContainer #productDetailsRelatedSection {
	border: 1px solid #B5B5B5;
	border-top: 0px;
	width: 793px;
	position: relative;
	float: left;
}

#productDetailContainer #relatedTitle {
	height: 29px;
	width: 795px;
	background-image: url("../images/blackSectionBg.gif");
	background-repeat: repeat-x;
	background-position: center top;
	position: relative;
	float: left;
	margin-top: 5px;
}

#productDetailContainer #relatedTitle h2 {
	margin: 0px;
	width: 750px;
	font-size: 11pt;
	position: relative;
	top: 5px;
	left: 10px;
	color: #FFFFFF;
}

/* START PRODUCT REVIEW STYLES */

#productDetailContainer #productDetailsReviewSection {
	border: 1px solid #B5B5B5;
	border-top: 0px;
	width: 793px;
	position: relative;
	float: left;
}

#productDetailContainer #reviewTitle {
	height: 29px;
	width: 795px;
	background-image: url("../images/blackSectionBg.gif");
	background-repeat: repeat-x;
	background-position: center top;
	position: relative;
	float: left;
	margin-top: 5px;
}

#productDetailContainer #reviewTitle h2 {
	margin: 0px;
	width: 750px;
	font-size: 11pt;
	position: relative;
	top: 5px;
	left: 10px;
	color: #FFFFFF;
}

#productDetailContainer #addReviewForm .falseLabel {
	border: 0px solid #f00;
	width: 48%;
	float: left;
	height: 28px;
	line-height: 28px;
	text-align: right;
	font-weight: bold;
}
#productDetailContainer #addReviewForm .falseInput {
	border: 0px solid #0f0;
	width: 48%;
	float: left;
	height: 28px;
	line-height: 28px;
}

#productDetailContainer #addReviewForm #reviewName {
	width: 260px;
}
#productDetailContainer #addReviewForm #reviewName.focused {
	color: #000;
	border: 1px solid #A5ACB2;
	font-weight: normal;
}
#productDetailContainer #addReviewForm h4 {
	text-align: center;
	border: 0px solid #ccc;
	margin: 0px;
	font-size: 14pt;
}
#productDetailContainer #addReviewForm #reviewButtonContainer {
	text-align: center;
}
#productDetailContainer #addReviewForm textarea {
	margin-left: 130px;
	margin-top: 3px;
	color: #999;
	font-family: Arial, Sans-Serif;
	font-size: 10pt;
}
#productDetailContainer #addReviewForm input.highlight {
	border: 1px solid #F00;
	color: #F00;
	font-weight: bold;
}
#productDetailContainer #addReviewForm #productRatingLabel.highlight {
	font-weight: bold;
	color: #F00;
	text-decoration: underline;
}
#productDetailContainer #addReviewForm  textarea.focused {
	color: #000;
}
#productDetailContainer #addReviewForm textarea.highlight {
	border: 1px solid #F00;
	color: #F00;
	font-weight: bold;
}
#productDetailContainer #addReviewForm .falseInput #reviewName {
	margin-left: 17px;
}
#productDetailContainer #addReviewForm .falseInput .star {
	margin-top: 6px;
}
.reviewerName {
	margin-left: 40px;
	float: left;
}
.starsBox {
	float: left;
	border: 0px solid #000;
	margin-top: 12px;
	margin-left: 10px;
}
.starsBox img {
	float: none !important;
}
.speechBubble {
	position:relative;
	padding:3px 10px 3px 10px;
	margin: 0px 0 0.5em;
	color:#FFF;
	background:#777;
	text-align: center;
	width: 90%;
	margin-left: auto;
	margin-right: auto;

	/* css3 */
	-moz-border-radius:10px;
	-webkit-border-radius:10px;
	border-radius:10px;
}

.speechBubble p {
	font-size:10pt;
	line-height:1.25em;
	font-weight: bold;
	font-style: italic;
}

/* creates the triangle */
.speechBubble:after {
	content:"";
	display:block; /* reduce the damage in FF3.0 */
	position:absolute;
	top:-20px;
	left:25px;
	width:0;
	height:0;
	border:10px solid transparent;
	border-bottom-color:#777;
}

/* display of quote author (alternatively use a class on the element following the blockquote) */
.speechBubble + p { font:10pt Arial, sans-serif;}

/* END PRODUCT REVIEW STYLES */

#productDetailContainer #productDetailsTopSection #productImage {
	border: 0px solid #F00;
	height: 306px;
	width: 306px;
	position: absolute;
	top: 0px;
	left: 0px;
	text-align: center;
	line-height: 300px;
}

#productDetailContainer #productDetailsTopSection #productImage img {
	vertical-align: middle;
}

#productDetailContainer #productDetailsTopSection #productText {
	border: 0px solid #F00;
	width: 240px;
	position: absolute;
	top: 5px;
	left: 310px;
}

#productDetailContainer #productDetailsTopSection #productText h1 {
	width: 220px;
	font-size: 12pt;
	border: 0px solid #f00;
	margin: 0px;
	color: #000000;
	text-decoration: underline;
	text-align: center;
}

#productDetailContainer #productDetailsTopSection #productText #productNowPrice {
	border: 0px solid #f00;
	width: 100px;
	height: 20px;
	position: absolute;
	top: 45px;
	left: 120px;
	color: #FF0000;
	font-weight: bold;
	font-size: 13pt;
}

#productDetailContainer #productDetailsTopSection #productText #productWasPrice {
	border: 0px solid #f00;
	width: 100px;
	height: 20px;
	color: #555555;
	position: absolute;
	top: 45px;
	font-weight: bold;
	font-size: 13pt;
}

#productDetailContainer #productDetailsTopSection #productText #productSaving {
	border: 0px solid #f00;
	width: 220px;
	height: 20px;
	position: absolute;
	top: 70px;
	left: 0px;
	color: #FF0000;
	font-weight: bold;
	text-align: center;
}

#productDetailContainer #productDetailsTopSection #sizeChart {
	border: 0px solid #000;
	position: absolute;
	width: 220px;
	height: 40px;
	top: 310px;
	right: 0px;
}

#productDetailContainer #productDetailsTopSection #productText #productOptions {
	border: 1px solid #000;
	width: 220px;
	color: #939AA1;
	font-weight: bold;
	position: absolute;
	top: 90px;
}

#productDetailContainer #productDetailsTopSection #productText #productOptions h3 {
	width: 220px;
	height: 29px;
	line-height: 30px;
	text-indent: 10px;
	font-size: 10pt;
	background-image: url("../images/sectionHeaderBg.gif");
	color: #FFFFFF;
	margin: 0px;
	text-align: center;
}

#productDetailContainer #productDetailsTopSection #productText #productOptions #optionsContainer {
	width: 220px;
	background-image: url("../images/sectionBg.gif");
	background-position: bottom;
	background-repeat: repeat-x;
	color: #6B6B6B;
	text-align: center;
}

#productDetailContainer #productDetailsTopSection #productText #productOptions #optionsContainer select {
	width: 200px;
	margin: 10px;
	margin-bottom: 0px;
}

#productDetailContainer #productDetailsTopSection #productText #productOptions #optionsContainer span
	{
	margin: 10px;
}

#productDetailContainer #productDetailsTopSection #productText #productOptions #optionsContainer input
	{
	margin: 10px;
}

#productDetailContainer #productDetailsTopSection #productText #productOptions #optionsContainer img {
	margin: 10px;
}

#productDetailContainer #productDetailsTopSection #productButtons {
	border: 0px solid #f00;
	width: 270px;
	height: 25px;
	position: absolute;
	top: 280px;
	left: 310px;
}

#productDetailContainer #productDetailsTopSection #productButtons #inStock {
	border: 0px solid #f00;
	display: inline;
	float: left;
	width: 110px;
}

#productDetailContainer #productDetailsTopSection #productButtons #enlarge {
	border: 0px solid #f00;
	display: inline;
	float: left;
	width: 119px;
}

#relatedProductContainer {
	border: 0px solid #000;
	height: 220px;
	margin: 10px;
	padding: 0px;
	float: left;
	text-align: center;
	position: relative;
}

#relatedProductContainer .relatedProduct {
	border: 0px solid #000;
	width: 170px;
	height: 220px;
	float: left;
	position: relative;
}

#relatedProductContainer .relatedProduct h3 {
	margin: 0px;
}

#relatedProductContainer .relatedProduct .titleContainer {
	border: 0px solid #f00;
	position: absolute;
	bottom: 30px;
	left: 0px;
	width: 170px;
	height: 34px;
	overflow: hidden;
}

#relatedProductContainer .relatedProduct .imageContainer {
	border: 0px solid #f00;
	position: absolute;
	bottom: 80px;
	left: 15px;
	width: 140px;
	height: 140px;
	line-height: 140px;
}

#relatedProductContainer .relatedProduct .imageContainer img {
	vertical-align: middle;
}

#relatedProductContainer .relatedProduct .titleContainer a {
	color: #000000;
	font-size: 10pt;
}

#relatedProductContainer .relatedProduct .pricesContainer {
	border: 0px solid #f00;
	color: #F00;
	font-weight: bold;
	font-size: 10pt;
	position: absolute;
	bottom: 0px;
	left: 0px;
	width: 170px;
	height: 30px;
}

#relatedProductContainer .relatedProduct .pricesContainer #productWasPrice {
	color: #939AA1;
	text-decoration: line-through;
	height: 18px;
	overflow: hidden;
}

#similarProductContainer {
	border: 0px solid #000;
	height: 220px;
	padding: 0px;
	float: left;
	margin: 10px;
	text-align: center;
}

#similarProductContainer .similarProduct {
	border: 0px solid #000;
	width: 170px;
	height: 220px;
	float: left;
	position: relative;
}

#similarProductContainer .similarProduct h3 {
	margin: 0px;
}

#similarProductContainer .similarProduct .titleContainer {
	border: 0px solid #f00;
	position: absolute;
	bottom: 30px;
	left: 0px;
	width: 170px;
	height: 34px;
	overflow: hidden;
}

#similarProductContainer .similarProduct .imageContainer {
	border: 0px solid #f00;
	position: absolute;
	bottom: 80px;
	left: 15px;
	width: 140px;
	height: 140px;
	line-height: 140px;
}

#similarProductContainer .similarProduct .imageContainer img {
	vertical-align: middle;
}

#similarProductContainer .similarProduct .titleContainer a {
	color: #000000;
	font-size: 10pt;
}

#similarProductContainer .similarProduct .pricesContainer {
	border: 0px solid #f00;
	color: #F00;
	font-weight: bold;
	font-size: 10pt;
	position: absolute;
	bottom: 0px;
	left: 0px;
	width: 170px;
	height: 30px;
}

#similarProductContainer .similarProduct .pricesContainer #productWasPrice {
	color: #939AA1;
	text-decoration: line-through;
	height: 18px;
	overflow: hidden;
}
/* PRICE MATCH */
#priceMatchSection {
	border: 0px solid #f00;
	border-left: 1px solid #000;
	position: absolute;
	top: 150px;
	right: 0px;
	width: 248px;
	height: 133px;
	padding-top: 37px;
	background-image: url("../images/multibuyBg.png");
	background-repeat: repeat-y;
	background-position: right;
}
#priceMatchSection #submitPriceMatchButton {
	width: auto !important;
	margin-left: 90px;
	margin-top: 2px;
}
#priceMatchSection input {
	margin-left: 37px;
	margin-top: 4px;
	width: 200px;
	color: #CCCCCC;
}
#priceMatchSection input.focused {
	margin-left: 37px;
	margin-top: 4px;
	width: 200px;
	color: #000000;
}
#priceMatchSection #priceMatchBanner {
	position: absolute;
	top: 0px;
}
#priceMatchSection #priceMatchHeader {
	position: absolute;
	top: 0px;
	right: 0px;
}
/* PRICE MATCH */
#bestDealSection {
	border: 0px solid #f00;
	border-left: 1px solid #000;
	position: absolute;
	top: 150px;
	right: 0px;
	width: 248px;
	height: 133px;
	padding-top: 37px;
	background-color: #FFF;
	background-repeat: repeat-y;
	background-position: right;
}
#bestDealSection #bestDealHeader {
	position: absolute;
	top: 0px;
	right: 0px;
}
#bestDealSection #bestDealPackage {
	border: 0px solid #00f;
	border-right: 1px solid #CCC;
	padding-right: 5px;
}
#bestDealSection #bestDealProduct {
	border: 0px solid #00f;
	border-right: 1px solid #CCC;
	padding-right: 5px;
}
#bestDealSection #bestDealText {
	font-style: italic;
}
#bestDealSection #bestDealManufacturerLogo {
	float: none !important;
}
/********* Package Styles **********/
#packageDetailContainer {
	float: left;
	border: 0px solid #000;
	position: relative;
	clear: both;
}
#packageDetailContainer table.w100 {
	width: 100%;
}
#packageDetailContainer caption {
	font-size: 10pt;
}
#packageDetailContainer th {
	font-weight: bold;
	font-size: 10pt;
	padding: 5px;
	border: 1px solid #CCC;
}
#packageDetailContainer td {
	font-size: 10pt;
	padding: 5px;
	border: 1px solid #CCC;
}
#packageDetailContainer #packageTitle {
	border: 1px solid #555;
	height: 29px;
	width: 794px;
	background-image: url("../images/blackSectionBg.gif");
	background-repeat: repeat-x;
	background-position: top;
	position: relative;
	color: #FFFFFF;
}
#mainPackageImageContainer {
	display: none;
}
.zoomedPackageName {
	font-size: 12pt;
	margin: 0px;
}
.zoomedPackageText {
	font-size: 8pt;
	margin: 0px;
	margin-bottom: 5px;
}
#packageDetailContainer #packageTitle h2 {
	margin: 0px;
	height: 29px;
	font-size: 12pt;
	position: relative;
	top: 5px;
	left: 10px;
	color: #FFFFFF;
	font-size: 11pt;
}

#packageDetailContainer #packageTitle a {
	color: #FFFFFF;
}

#packageDetailContainer #packageDetailsTopSection {
	border-left: 1px solid #000;
	border-right: 1px solid #000;
	border-bottom: 1px solid #000;
	width: 794px;
	height: 320px;
	position: relative;
	float: left;
	background-image: url("../images/multibuyBg.png");
	background-repeat: repeat-y;
	background-position: right;
}

#packageDetailContainer #packageDetailsOverviewSection {
	border: 1px solid #B5B5B5;
	border-top: 0px;
	width: 794px;
	position: relative;
	float: left;
    line-height: 1.5em;
	clear: both;
}
html>body #packageDetailContainer #packageDetailsOverviewSection {
	width: 794px;
}
#packageDetailContainer #packageDetailsOverviewSection div {
	margin: 10px;
}

#packageDetailContainer #overviewTitle {
	height: 29px;
	width: 796px;
	background-image: url("../images/blackSectionBg.gif");
	background-repeat: repeat-x;
	background-position: top;
	position: relative;
	float: left;
	margin-top: 5px;
}

#packageDetailContainer #overviewTitle h2 {
	margin: 0px;
	height: 29px;
	font-size: 12pt;
	position: relative;
	top: 5px;
	left: 10px;
	color: #FFFFFF;
	font-size: 11pt;
}

#upgradesSection {
	border: 0px solid #f00;
	border-left: 1px solid #000;
	position: absolute;
	top: 0px;
	right: 0px;
	width: 248px;
	height: 320px;
	background-image: url("../images/upgradesBg.png");
	background-repeat: no-repeat;
	background-position: bottom;
	position: absolute;
}
#upgradesSection #upgradesExplanation {
	margin: 0px;
	padding: 0px;
	border: 0px solid #000;
	position: absolute;
	bottom: 0px;
	right: 10px;
	width: 200px;
	text-align: center;
	font-weight: bold;
}
#upgradesSection div {
	margin-left: 40px;
	margin-top: 40px;
}
#upgradesSection #upgradesBanner {
	position: absolute;
	top: 0px;
}
#upgradesSection #upgradesHeader {
	position: absolute;
	top: 0px;
	right: 0px;
}
#packageDetailContainer #packageDetailsTopSection #packageImage {
	border: 0px solid #F00;
	height: 300px;
	width: 300px;
	position: absolute;
	top: 10px;
	left: 10px;
	text-align: center;
	line-height: 300px;
}

#packageDetailContainer #packageDetailsTopSection #packageImage img {
	vertical-align: middle;
}

#packageDetailContainer #packageDetailsTopSection #packageText {
	border: 0px solid #F00;
	width: 240px;
	position: relative;
	top: 5px;
	left: 310px;
}

#packageDetailContainer #packageDetailsTopSection #packageText h1 {
	width: 220px;
	font-size: 12pt;
	border: 0px solid #f00;
	margin: 0px;
	color: #000000;
	text-decoration: underline;
}

#packageDetailContainer #packageDetailsTopSection #packageText #packageNowPrice
	{
	border: 0px solid #f00;
	width: 100px;
	height: 20px;
	position: absolute;
	top: 45px;
	left: 120px;
	color: #FF0000;
	font-weight: bold;
	font-size: 13pt;
}

#packageDetailContainer #packageDetailsTopSection #packageText #packageWasPrice
	{
	border: 0px solid #f00;
	width: 100px;
	height: 20px;
	color: #555555;
	position: absolute;
	top: 45px;
	font-weight: bold;
	font-size: 13pt;
}
#packageDetailContainer #packageDetailsTopSection #packageText #packageSaving
	{
	border: 0px solid #f00;
	width: 240px;
	height: 20px;
	position: absolute;
	top: 65px;
	left: 0px;
	color: #FF0000;
	font-weight: bold;
	text-align: center;
}

#packageDetailContainer #packageDetailsTopSection #packageText #packageOptions {
	border: 1px solid #000;
	width: 220px;
	color: #939AA1;
	font-weight: bold;
	position: absolute;
	top: 85px;
}
html>body #packageDetailContainer #packageDetailsTopSection #packageText #packageOptions {
	top: 85px;
}

#packageDetailContainer #packageDetailsTopSection #packageText #packageOptions h3 {
	width: 220px;
	height: 29px;
	line-height: 30px;
	text-indent: 10px;
	font-size: 10pt;
	background-image: url("../images/sectionHeaderBg.gif");
	color: #FFFFFF;
	margin: 0px;
	text-align: center;
}

#packageDetailContainer #packageDetailsTopSection #packageText #packageOptions #optionsContainer {
	width: 220px;
	background-image: url("../images/sectionBg.gif");
	background-position: bottom;
	background-repeat: repeat-x;
	color: #6B6B6B;
	text-align: left;
}

#packageDetailContainer #packageDetailsTopSection #packageText #packageOptions #optionsContainer ol
	{
	margin: 0px;
	padding: 0px;
	margin-left: 30px;
	padding-top: 5px;
}

#packageDetailContainer #packageDetailsTopSection #packageText #packageOptions #optionsContainer ol li
	{
	margin: 0px;
	padding: 0px;
	margin-bottom: 5px;
}

#packageDetailContainer #packageDetailsTopSection #packageText #packageOptions #optionsContainer select
	{
	width: 180px;
	margin-top: 5px;
	margin-bottom: 5px;
}

#packageDetailContainer #packageDetailsTopSection #packageText #packageOptions #optionsContainer input
	{
	margin: 10px;
	z-index: 10;
}

#packageDetailContainer #packageDetailsTopSection #packageText #packageOptions #optionsContainer #errorBox
	{
	border: 0px solid #f00;
	text-align: center;
	color: #F00;
}

#packageDetailContainer #packageDetailsTopSection #packageButtons {
	border: 0px solid #f00;
	width: 230px;
	height: 50px;
	position: absolute;
	bottom: 0px;
	left: 260px;
	z-index: 0;
}

#packageDetailContainer #packageDetailsTopSection #packageButtons #inStock
	{
	border: 0px solid #f00;
	width: 110px;
	height: 24px;
	position: absolute;
	top: 0px;
	left: 40px;
}

#packageDetailContainer #packageDetailsTopSection #packageButtons #enlarge
	{
	border: 0px solid #f00;
	width: 119px;
	height: 24px;
	position: absolute;
	top: 0px;
	left: 160px;
}

#packageContentsContainer {
	border: 1px solid #ccc;
	float: left;
	width: 385px;
	height: 170px;
	position: relative;
	margin-top: 10px;
	margin-left: 5px;
	margin-right: 5px;
}

.packageContentsProductContainer {
	border: 0px solid #000;
	width: 385px;
	position: relative;
	height: 150px;
	clear: both;
	float: left;
}

.packageContentsProductImageContainer {
	width: 140px;
	height: 140px;
	margin-top: 3px;
	text-align: center;
	border: 0px solid #f00;
	line-height: 140px;
}

.packageContentsProductImageContainer img {
	vertical-align: middle;
}

.packageContentsProductContainer .productDetailsContainer {
	width: 380px;
	height: 140px;
	position: absolute;
	top: 3px;
	left: 160px;
	border: 0px solid #000;
}

.packageContentsProductContainer .productDetailsContainer h3 {
	margin: 0px;
	font-size: 12pt;
	border: 0px solid #000;
	width: 220px;
	height: 45px;
	text-align: center;
	position: absolute;
	top: 0px;
	left: 0px;
}

.packageContentsProductContainer .productDetailsContainer a {
	color: #000000;
}

.packageContentsProductContainer .productDetailsContainer .prices {
	font-size: 10pt;
	border: 0px solid #000;
	height: 25px;
	width: 380px;
	position: absolute;
	top: 37px;
	left: 0px;
}

.packageContentsProductContainer .productDetailsContainer .prices .wasPrice
	{
	color: #939AA1;
	width: 150px;
	height: 25px;
	line-height: 25px;
	float: left;
	text-decoration: line-through;
	font-weight: bold;
}

.packageContentsProductContainer .productDetailsContainer .prices .nowPrice
	{
	color: #F00;
	width: 200px;
	float: left;
	height: 25px;
	line-height: 25px;
	font-weight: bold;
}

.packageContentsProductContainer .productDetailsContainer .description {
	font-size: 10pt;
	border: 0px solid #000;
	height: 95px;
	overflow: hidden;
	width: 210px;
	position: absolute;
	text-align: justify;
	top: 50px;
	left: 0px;
}

.packageContentsProductContainer .productDetailsContainer .categoryViewButtonsContainer
	{
	border: 0px solid #000;
	height: 30px;
	width: 380px;
	position: absolute;
	top: 110px;
	left: 0px;
}

.packageContentsProductContainer .productDetailsContainer .categoryViewButtonsContainer #buyNowButton
	{
	width: 74px;
	height: 24px;
	position: absolute;
	left: 0px;
	top: 0px;
}

.packageContentsProductContainer .productDetailsContainer .categoryViewButtonsContainer #viewButton
	{
	width: 74px;
	height: 24px;
	position: absolute;
	left: 0px;
	top: 0px;
}

.packageContentsProductContainer .productDetailsContainer .categoryViewButtonsContainer #oneHundredSecureButton
	{
	width: 89px;
	height: 24px;
	position: absolute;
	left: 80px;
	bottom: 7px;
}
/*** All brands page ***/
.brandContainer {
	height: 110px;
	width: 190px;
	float: left;
	margin-top: 10px;
	margin-right: 10px;
	margin-left: 10px;
	text-align: center;
	overflow: hidden;
}

.brandContainer img {
	border: 3px solid #FFF;
	width: 175px;
}

.brandContainer img:hover {
	border: 3px solid #F00;
}

.brandContainer h3 {
	margin: 0px;
}

.brandContainer a {
	color: #000;
}
.brandContainer a:visited {
	color: #000;
}

.brandContainer a:hover {
	color: #000;
	text-decoration: underline;
}
/***** Top Stacks Page ****/
#topStackContainer {
	border: 1px solid #000;
	height: 355px;
	padding: 0px;
	position: relative;
}
#topStackContainer #topStackHeading {
	height: 29px;
	width: 794px;
	background-image: url("../images/blackSectionBg.gif");
	background-repeat: repeat-x;
	background-position: top;
	position: relative;
	float: left;
}
#topStackContainer #topStackHeading h2 {
	margin: 0px;
	height: 29px;
	font-size: 12pt;
	position: relative;
	top: 5px;
	left: 10px;
	color: #FFFFFF;
	font-size: 11pt;
}
#topStackContainer #topStackHeading h2 a, #topStackContainer #topStackHeading h2 a:visited {
	color: #FFF;
}
#topStackContainer img#leftBanner {
	position: absolute;
	top: 29px;
	left: 0px;
}
#topStackContainer img#rightBanner {
	position: absolute;
	top: 29px;
	right: 0px;
}
#topStackContainer img#numOneStackImage {
	position: absolute;
	top: 40px;
	right: 50px;
}
#topStackContainer img#watermark {
	position: absolute;
	top: 260px;
	left: 50px;
}
#topStackContainer div#numOneProductName {
	position: absolute;
	top: 35px;
	left: 50px;
	width: 400px;
	height: 20px;
	font-size: 16px;
	font-weight: bold;
	border: 0px solid #000;
}
#topStackContainer div#numOneDescription {
	position: absolute;
	top: 60px;
	left: 50px;
	width: 380px;
	height: 120px;
	border: 0px solid #000;
}

#topStackContainer div#wasPrice {
	position: absolute;
	top: 180px;
	left: 140px;
	width: 150px;
	height: 50px;
	line-height: 50px;
	text-align: right;
	border: 0px solid #000;
	font-size: 20px;
	font-weight: bold;
	text-decoration: line-through;
}
#topStackContainer div#nowPrice {
	position: absolute;
	top: 180px;
	left: 300px;
	width: 150px;
	height: 50px;
	line-height: 50px;
	border: 0px solid #000;
	font-size: 24px;
	font-weight: bold;
	color: #FF0000;
}
#topStackContainer img#secure {
	position: absolute;
	top: 222px;
	left: 250px;
}
#topStackContainer #button {
	position: absolute;
	top: 220px;
	left: 350px;
}
/***********  SUB-STACKS  **********/
.topStackSubContainer {
	border: 1px solid #000;
	height: 355px;
	width: 390px;
	padding: 0px;
	position: relative;
	float: left;
	margin-right: 2px;
	margin-left: 2px;
	margin-top: 5px;
}
.topStackSubContainer .topStackSubHeading {
	height: 29px;
	width: 390px;
	background-image: url("../images/blackSectionBg.gif");
	background-repeat: repeat-x;
	background-position: top;
	position: relative;
	float: left;
}
.topStackSubContainer .topStackSubHeading h2 {
	margin: 0px;
	height: 29px;
	font-size: 12pt;
	position: relative;
	top: 5px;
	left: 10px;
	color: #FFFFFF;
	font-size: 11pt;
}
.topStackSubContainer .topStackSubHeading h2 a, .topStackSubContainer .topStackSubHeading h2 a:visited {
	color: #FFF;
}
.topStackSubContainer div.topStackImage {
	position: absolute;
	top: 75px;
	left: 120px;
}
.topStackSubContainer div.numOneProductName {
	position: absolute;
	top: 35px;
	left: 10px;
	width: 370px;
	text-align: center;
	height: 20px;
	font-size: 16px;
	font-weight: bold;
	border: 0px solid #000;
}
.topStackSubContainer div.numOneDescription {
	position: absolute;
	top: 220px;
	left: 10px;
	width: 360px;
	height: 120px;
	border: 0px solid #000;
}

.topStackSubContainer div.wasPrice {
	position: absolute;
	bottom: 0px;
	right: 170px;
	width: 150px;
	height: 30px;
	line-height: 30px;
	text-align: right;
	border: 0px solid #000;
	font-size: 20px;
	font-weight: bold;
	text-decoration: line-through;
}
.topStackSubContainer div.nowPrice {
	position: absolute;
	bottom: 0px;
	right: 10px;
	width: 150px;
	height: 30px;
	line-height: 30px;
	border: 0px solid #000;
	font-size: 24px;
	font-weight: bold;
	color: #FF0000;
}
.topStackSubContainer img.secure {
	position: absolute;
	bottom: 35px;
	left: 200px;
}
.topStackSubContainer .button {
	position: absolute;
	bottom: 35px;
	right: 10px;
}

/******
	Basket Page
				******/
#shoppingBasketTitleContainer {
	border: 1px solid #555;
	height: 29px;
	width: 794px;
	background-image: url("../images/blackSectionBg.gif");
	background-repeat: repeat-x;
	background-position: top;
	position: relative;
	color: #FFFFFF;
}

#shoppingBasketTitleContainer h1 {
	margin: 0px;
	font-size: 12pt;
	color: #FFF;
	text-indent: 10px;
	line-height: 27px;
}

#shoppingBasketFooterContainer {
	border: 1px solid #555;
	height: 29px;
	width: 794px;
	background-image: url("../images/blackSectionBg.gif");
	background-repeat: repeat-x;
	background-position: top;
	position: relative;
	color: #FFFFFF;
}

#shoppingBasketContainer {
	width: 774px;
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
#shoppingBasketContainer #checkoutOptionsTable {
	width: 100%;
	border: 0px solid #000;
	float: right;
	border-collapse: collapse;
}
#shoppingBasketContainer #checkoutOptionsTable td {
	border: 0px solid #000;
	text-align: right;
}
#shoppingBasketContainer #checkoutOptionsTable td.basketDivider {
	background-image: url('../images/basketDivider.jpg');
	height: 20px;
	padding: 0px !important;
	margin: 0px !important;
	background-repeat: repeat-x;
	background-position: bottom;
}
#shoppingBasketContainer #checkoutOptionsTable td.button {
	width: 230px;
}
#shoppingBasketContainer #proceedToCheckoutButton {
	margin: 0px;
	margin-right: 20px;
	border: 0px;
	float: right;
}
#proceedToGoogleCheckoutButtonContainer {
	width: 200px;
	float: right;
	margin: 10px;
}
#proceedToPaypalCheckoutButtonContainer {
	float: right;
	margin: 10px;
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
	width: 210px;
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
/***********
	MY ACCOUNT SECTION
***********/
#loginTitle {
	border: 1px solid #555;
	height: 29px;
	width: 794px;
	background-image: url("../images/blackSectionBg.gif");
	background-repeat: repeat-x;
	background-position: top;
	position: relative;
	color: #FFFFFF;
}

#loginTitle h2 {
	margin: 0px;
	font-size: 12pt;
	color: #FFF;
	text-indent: 10px;
	line-height: 27px;
}
#loginForm {
	background-color: #EAEAEA;
	border-left: 1px solid #AAAAAA;
	border-right: 1px solid #AAAAAA;
	border-bottom: 1px solid #AAAAAA;
	width: 794px;
	padding-top: 20px;
}
#loginForm input {
	margin-bottom: 10px;
}
#loginForm #loginButton {
	margin-left: auto;
	margin-right: auto;
	display: block;
}
#loginForm label {
	width: 300px;
	display: block;
	float: left;
	text-align: right;
    font-weight: bold;
	margin-right: 10px;
}
#loginForm #loginEmail, #loginForm #loginPassword {
	width: 200px;
	display: block;
	float: left;
	text-align: left;
    font-weight: bold;
}
#changeMyDetailsForm label {
	width: 150px;
	display: block;
	float: left;
}
#changeMyDetailsForm input, #changeMyDetailsForm select {
	margin-bottom: 10px;
}

#changeMyPasswordForm label {
	width: 150px;
	display: block;
	float: left;
}

#changeMyPasswordForm input {
	margin-bottom: 10px;
}

#forgotPasswordSubmit {
	color: #000;
	background-color: transparent;
	text-decoration: underline;
	border: none;
	cursor: pointer;
	cursor: hand;
}
#passwordResetForm {
	width: 550px;
	display: block;
	margin-left: auto;
	margin-right: auto;
	margin-top: 20px;
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

#myAccountOptionsContainer {
	border: 1px solid #CCC;
	margin-bottom: 10px;
	margin-top: 10px;
	float: left;
	width: 98%;
	padding-bottom: 10px;
}

#myAccountOptionsList {
	margin: 0px;
	margin-left: 10px;
	padding: 0px;
	list-style-image: none;
	list-style-type: none;
	list-style-position: inside;
	float: left;
}
#myAccountOptionsList li {
	background-repeat: no-repeat;
	height: 70px;
	border: 1px solid white;
	margin: 0px;
	padding: 0px;
}
#myAccountOptionsList li div {
	width: 400px;
	margin-top: 12px;
	line-height: 18px;
}
#myAccountOptionsList li img {
	display: block;
	float: left;
}
#myAccountOptionsList li p {
	display: block;
	float: left;
	margin-left: 10px;
}
#myAccountOptionsList li a {
	font-weight: bold;
}
#myAccountOptionsList li a:hover {
	text-decoration: underline;
}
#logoutForm input {
	margin-left: auto;
	margin-right: auto;
	display: block;
}

#myAccountChangeDetailsContainer {
	border: 1px solid #CCC;
	margin-bottom: 10px;
	float: left;
	width: 100%;
}
#myAccountChangeDetailsContainer form {
	margin: 10px;
}

#myAccountViewDetailsContainer {
	border: 1px solid #CCC;
	margin-bottom: 10px;
	float: left;
	width: 100%;
	line-height: 20px;
}
#myAccountViewDetailsContainer div {
	margin: 10px;
}

#myAccountChangePasswordContainer {
	border: 1px solid #CCC;
	margin-bottom: 10px;
	float: left;
	width: 100%;
	line-height: 20px;
}
#myAccountChangePasswordContainer div {
	margin: 10px;
}

/*****
	CHECKOUT SECTION
				******/
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
	background-image: url("../images/sectionBg.gif");
	background-position: bottom;
	background-repeat: repeat-x;
	border: 1px solid #000;
	height: 220px;
	float: left;
	text-align: left;
}

#loginContainer #newCustomerContainer h2 {
	background-image: url("../images/blackSectionBg.gif");
	background-repeat: repeat-x;
	background-position: top;
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
	background-image: url("../images/sectionBg.gif");
	background-position: bottom;
	background-repeat: repeat-x;
	border: 1px solid #000;
	width: 260px;
	height: 220px;
	float: left;
	text-align: center;
	position: relative;
	margin-left: 5px;
}

#loginContainer #returningCustomerContainer h2 {
	background-image: url("../images/blackSectionBg.gif");
	background-repeat: repeat-x;
	background-position: top;
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
	width: 600px;
	margin-left: auto;
	margin-right: auto;
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
	width: 540px;
	margin-left: auto;
	margin-right: auto;
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
	background-image: url("../images/sectionBg.gif");
	background-position: bottom;
	background-repeat: repeat-x;
	width: 260px;
	height: 200px;
	text-align: center;
	position: relative;
	margin-left: auto;
	margin-right: auto;
	border: 1px solid #000;
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
	width: 540px;
	margin-left: auto;
	margin-right: auto;
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
#orderTrackingTable {
	width: 98%;
	border: 1px solid #000;
	border-collapse: collapse;
}
#orderTrackingTable th {
	border: 1px solid #000;
	padding: 10px;
}
#orderTrackingTable td {
	border: 1px solid #000;
	padding: 10px;
}
#publicOrderView {
	line-height: 18px;
}
#publicOrderViewTable {
	border: 1px solid #000;
	border-collapse: collapse;
}
#publicOrderViewTable th {
	border: 1px solid #000;
	padding: 10px;
}
#publicOrderViewTable td {
	border: 1px solid #000;
	padding: 10px;
}

/********  Content Styles  ********/
.comparisonTable {
	width: 100%;
	border-collapse: collapse;
	border: 1px solid #CCC;
}
.comparisonTable td {
	border-bottom: 1px solid #CCC;
	border-right: 0px;
	border-left: 0px;
	border-top: 0px;
	padding: 5px;
}


