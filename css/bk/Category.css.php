<?php
header('Content-Type: text/css');
require('Colors.php');
?>
#categoryViewListContainer {
	border: 0px solid #000;
	float: left;
	width: 550px;
	position: relative;
}

#categoryDescriptionContainer {
	width: 550px;
}

#categoryDescriptionContainer #categoryDescriptionText {
	height: auto;
	float: left;
}

#categoryDescriptionContainer #categoryDescriptionText strong {
	font-size: 12pt;
}
#categoryViewListContainer #categoryViewListContainerTitle {
	width: 550px;
	height: 32px;
	background-image: url(../images/categoryListTitleBg.gif);
	background-repeat: no-repeat;
	color: #FFFFFF;
}

#categoryViewListContainer #categoryViewListContainerTitle h2 {
	margin: 0px;
	height: 32px;
	width: 320px;
	font-size: 12pt;
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
	right: 10px;
	top: 5px;
}

#categoryViewListContainer #categoryViewListContainerTitle #sortByContainer select {
	width: 200px;
}

#categoryViewListContainer #pageNumbersContainer {
	height: 32px;
	line-height: 32px;
	font-weight: bold;
	width: 550px;
	font-size: 10pt;
	background-color: #CCCCCC;
	color: #383838;
	float: left;
	clear: both;
}

#categoryViewListContainer #pageNumbersContainer a {
	font-weight: normal;
}

#categoryViewListContainer #pageNumbersContainer span {
	margin-left: 10px;
	margin-right: 10px;
	margin-top: 0px;
	margin-bottom: 0px;
}

#categoryViewListContainer #pageNumbersContainer a.currentPageNumber {
	font-weight: bold;
}

#categoryViewListContainer #manufacturerListContainer {
	font-weight: bold;
	width: 550px;
	font-size: 10pt;
	background-color: <?php echo $catManListBg; ?>;
	clear: both;
	float: left;
	display: block;
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
	background-color: <?php echo $catManListBg; ?>;
	color: <?php echo $catManListLink; ?>;
	display: block;
	height: 32px;
	padding-left: 5px;
	padding-right: 5px;
	position: relative;
}

#categoryViewListContainer #manufacturerList li a:hover {
	background-color: <?php echo $catManListLink; ?>;
	color: <?php echo $catManListBg; ?>;
}

.categoryViewProductContainer {
	border-bottom: 1px solid <?php echo $catProductSeparator; ?>;
	width: 550px;
	position: relative;
	height: 150px;
	clear: both;
	float: left;
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
}

.categoryViewProductContainer .productDetailsContainer {
	width: 380px;
	height: 140px;
	position: absolute;
	top: 3px;
	left: 160px;
}

.categoryViewProductContainer .productDetailsContainer h3 {
	margin: 0px;
	font-size: 12pt;
	height: 25px;
	width: 380px;
	position: absolute;
	top: 0px;
	left: 0px;
}

.categoryViewProductContainer .productDetailsContainer a {
	color: <?php echo $catProductName; ?>;
}

.categoryViewProductContainer .productDetailsContainer .prices {
	font-size: 10pt;
	height: 25px;
	width: 380px;
	position: absolute;
	top: 37px;
	left: 0px;
}

.categoryViewProductContainer .productDetailsContainer .prices .wasPrice {
	color: <?php echo $catWasPrice; ?>;
	width: 150px;
	height: 25px;
	line-height: 25px;
	float: left;
	text-decoration: line-through;
	font-weight: bold;
}

.categoryViewProductContainer .productDetailsContainer .prices .nowPrice {
	color: <?php echo $catNowPrice; ?>;
	width: 200px;
	float: left;
	height: 25px;
	line-height: 25px;
	font-weight: bold;
}

.categoryViewProductContainer .productDetailsContainer .description {
	font-size: 10pt;
	height: 50px;
	width: 380px;
	position: absolute;
	top: 60px;
	left: 0px;
}

.categoryViewProductContainer .productDetailsContainer .categoryViewButtonsContainer {
	height: 30px;
	width: 380px;
	position: absolute;
	top: 110px;
	left: 0px;
}

.categoryViewProductContainer .productDetailsContainer .categoryViewButtonsContainer #buyNowButton {
	width: 74px;
	height: 24px;
	position: absolute;
	left: 0px;
	top: 0px;
}

.categoryViewProductContainer .productDetailsContainer .categoryViewButtonsContainer #viewButton {
	width: 74px;
	height: 24px;
	position: absolute;
	left: 0px;
	top: 0px;
}

.categoryViewProductContainer .productDetailsContainer .categoryViewButtonsContainer #oneHundredSecureButton {
	width: 89px;
	height: 24px;
	position: absolute;
	left: 80px;
	bottom: 7px;
}

<?php /***	Featured Product	***/ ?>
#featuredProductContainer {
	width: 550px;
	height: 250px;
	position: relative;
    margin: 0px;
	background-image: url(../images/featuredProductBg.gif);
	background-repeat: no-repeat;
    clear: both;
}

#featuredProductContainer #featuredProductTitle {
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

#featuredProductContainer #featuredProductTitle h2 {
	color: <?php echo $dowH2; ?>;
	margin: 0px;
	padding: 0px;
	margin-left: 10px;
	margin-top: 7px;
    text-align: center;
}

#featuredProductContainer #featuredProductContent {
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

#featuredProductContainer #featuredProductContent #featuredProductName {
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

#featuredProductContainer #featuredProductContent #featuredProductName a {
	color: <?php echo $dowProductName; ?>;
}

#featuredProductContainer #featuredProductContent #featuredProductName a:hover {
	text-decoration: underline;
}

#featuredProductContainer #featuredProductContent #featuredProductDescription {
	border: 1px solid <?php echo $dowContentBg; ?>;
	position: absolute;
	top: 55px;
	left: 10px;
	width: 200px;
	height: 100px;
}

#featuredProductContainer #featuredProductContent #featuredProductImage {
	border: 1px solid <?php echo $dowContentBg; ?>;
	position: absolute;
	top: 5px;
	left: 215px;
}

#featuredProductContainer #featuredProductContent #featuredProductWasPrice {
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

#featuredProductContainer #featuredProductContent #featuredProductNowPrice {
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

#featuredProductContainer #featuredProductContent #featuredProductBuyNowButton {
	border: 1px solid <?php echo $dowContentBg; ?>;
	position: absolute;
	top: 120px;
	left: 415px;
}

<?php /***	Best Selling Product	***/ ?>
#bestSellerContainer {
	width: 270px;
	height: 250px;
	position: relative;
	margin-bottom: 10px;
	background-image: url(../images/bestSellerProductBg.gif);
	background-repeat: no-repeat;
    float: left;
}

#bestSellerContainer #bestSeller {
	position: absolute;
    top: 2px;
    left: 2px;
    z-index: 10;
}

#bestSellerContainer #bestSellerTitle {
	width: 250px;
	height: 40px;
	position: absolute;
	top: 5px;
	left: 7px;
	background-color: #000;
	border-left: 3px solid #000;
	border-right: 3px solid #000;
	border-top: 3px solid #000;
}

#bestSellerContainer #bestSellerTitle h2 {
	color: #FFF;
	margin: 0px;
	padding: 0px;
	margin-left: 10px;
	margin-top: 7px;
    text-align: center;
}

#bestSellerContainer #bestSellerContent {
	width: 250px;
	height: 195px;
	position: absolute;
	top: 45px;
	left: 7px;
	background-color: #FFF;
	border-left: 3px solid #000;
	border-right: 3px solid #000;
	border-bottom: 3px solid #000;
    text-align: center;
}
#bestSellerContainer #bestSellerContent table {
	width: 100%;
    height: 100%;
}
#bestSellerContainer #bestSellerContent #bestSellerName {
	margin: 0px;
	padding: 0px;
	position: absolute;
	top: 145px;
    left: 0px;
	width: 100%;
    text-align: center;
	overflow: hidden;
    line-height: 16px;
}

#bestSellerContainer #bestSellerContent #bestSellerName a {
	color: <?php echo $dowProductName; ?>;
}

#bestSellerContainer #bestSellerContent #bestSellerName a:hover {
	text-decoration: underline;
}

#bestSellerContainer #bestSellerContent #bestSellerProductImage {
	border: 1px solid <?php echo $dowContentBg; ?>;
    margin-bottom: 10px;
}

#bestSellerContainer #bestSellerContent #bestSellerNowPrice {
	position: absolute;
	top: 175px;
    left: 0px;
    width: 100%;
	margin: 0px;
	font-size: 11pt;
	color: <?php echo $dowNowPrice; ?>;
	text-align: center;
}

#brandNewContainer {
	width: 270px;
	height: 250px;
	position: relative;
	margin-bottom: 10px;
	background-image: url(../images/brandNewProductBg.gif);
	background-repeat: no-repeat;
    float: left;
    margin-left: 10px;
}

#brandNewContainer #brandNew {
	position: absolute;
    top: 2px;
    left: 2px;
    z-index: 10;
}

#brandNewContainer #brandNewTitle {
	width: 250px;
	height: 40px;
	position: absolute;
	top: 5px;
	left: 7px;
	background-color: #000;
	border-left: 3px solid #000;
	border-right: 3px solid #000;
	border-top: 3px solid #000;
}

#brandNewContainer #brandNewTitle h2 {
	color: #FFF;
	margin: 0px;
	padding: 0px;
	margin-left: 10px;
	margin-top: 7px;
    text-align: center;
}

#brandNewContainer #brandNewContent {
	width: 250px;
	height: 195px;
	position: absolute;
	top: 45px;
	left: 7px;
	background-color: #FFF;
	border-left: 3px solid #000;
	border-right: 3px solid #000;
	border-bottom: 3px solid #000;
    text-align: center;
}

#brandNewContainer #brandNewContent table {
	width: 100%;
    height: 100%;
}

#brandNewContainer #brandNewContent #brandNewName {
	margin: 0px;
	padding: 0px;
	position: absolute;
	top: 145px;
    left: 0px;
    width: 100%;
    text-align: center;
	height: 40px;
	overflow: hidden;
    line-height: 16px;
}

#brandNewContainer #brandNewContent #brandNewName a {
	color: <?php echo $dowProductName; ?>;
}

#brandNewContainer #brandNewContent #brandNewName a:hover {
	text-decoration: underline;
}

#brandNewContainer #brandNewContent #brandNewProductImage {
	border: 1px solid <?php echo $dowContentBg; ?>;
    margin-bottom: 10px;
}

#brandNewContainer #brandNewContent #brandNewNowPrice {
	position: absolute;
	top: 175px;
    left: 0px;
    width: 100%;
	margin: 0px;
	font-size: 11pt;
	color: <?php echo $dowNowPrice; ?>;
	text-align: center;
}
