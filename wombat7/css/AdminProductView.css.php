<?php
header('Content-Type: text/css');
require('Colors.php');
?>
br {
	clear: both;
}
/********************
* Product Edit Tabs *
*********************/
#adminProductViewTabContainer {
	background: url("../images/tab_b.gif") repeat-x bottom;
	width: 720px;
	height: 26px;
	display: block;
	margin-left: auto;
	margin-right: auto;
}

#adminProductViewTabContainer ul {
	list-style: none;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
	margin: 0px;
	margin-left: 5px;
	padding: 0px;
}

#adminProductViewTabContainer li {
	float: left;
	background: url("../images/prodLeft_both.gif") no-repeat left top;
	margin: 0;
	padding: 0 0 0 9px;
	height: 25px;
	border-bottom: 0px solid #FFF;
}

#adminProductViewTabContainer a {
	float: left;
	display: block;
	width: .1em;
	background: url("../images/prodRight_both.gif") no-repeat right
		top;
	padding: 5px 15px 4px 6px;
	text-decoration: none;
	font-weight: bold;
	color: #1A419D;
}

#adminProductViewTabContainer>ul a {
	width: auto;
}

#adminProductViewTabContainer li:hover {
	background-position: 0 -150px;
	color: #F00;
}

#adminProductViewTabContainer a:hover {
	background-position: 100% -150px;
	color: #F00;
}

/*********************
* Admin Product Form *
**********************/
#adminProductViewContainer {
	width: 720px;
	display: block;
	text-align: center;
	margin-top: 10px;
}

#adminProductViewContentContainer {
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

#adminProductForm {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
}

#adminProductForm label {
	width: 150px;
	font-weight: bold;
	float: left;
	text-align: left;
	margin-bottom: 10px;
	margin-right: 5px;
	border: 1px solid #FFF;
	height: 20px;
	line-height: 20px;
}

#adminProductForm input {
	width: 200px;
	float: left;
	text-align: left;
	margin-bottom: 10px;
}

#adminProductFormButtons {
	float: right;
	margin-top: 10px;
}

#adminProductFormButtons input {
	width: auto;
	text-align: center;
}

/* Admin Product Form -> Description Section */
#descriptionContentArea {
	border: 1px solid #FFF;
	height: 99%;
	text-align: left;
	padding-left: 5px;
	padding-right: 5px;
	display: block;
}

/* Admin Product Form -> Pricing Section */
#pricingContentArea {
	border: 1px solid #FFF;
	height: 99%;
	text-align: left;
	padding-left: 5px;
	padding-right: 5px;
	display: none;
}

#pricingContentArea input {
	width: 75px;
	padding: 0px;
	margin: 0px;
	text-align: left;
	text-indent: 0px;
}

#pricingContentArea #inStock,#pricingContentArea #forSale,#promotionsContentArea #onSale,#promotionsContentArea #offerOfWeek,#promotionsContentArea #onClearance,#promotionsContentArea #featured,#promotionsContentArea #hidden,#promotionsContentArea #multibuy
	{
	width: auto;
}

/* Admin Product Form -> Promotions Section */
#promotionsContentArea {
	border: 1px solid #FFF;
	height: 99%;
	text-align: left;
	padding-left: 5px;
	padding-right: 5px;
	display: none;
}
#promotionsLeftContentArea {
	border: 1px solid #FFF;
	height: 99%;
	text-align: left;
	padding-left: 5px;
	padding-right: 5px;
	width: 275px;
	float: left;
}
#promotionsRightContentArea {
	border: 1px solid #FFF;
	border-left: 1px solid #ccc;
	height: 99%;
	text-align: left;
	padding-left: 5px;
	padding-right: 5px;
	width: 340px;
	float: left;
}
#promotionsRightContentArea strong {
	display: block;
	margin-bottom: 8px;
}
#promotionsRightContentArea input {
	display: block;
	float: left;
	width: auto;
}
#promotionsRightContentArea label {
	margin: 0px;
	padding: 0px;
	display: block;
	float: left;
	font-weight: normal;
}
#promotionsContentArea th,td {
	font-size: 10pt;
	padding: 8px 8px 8px 8px;
	border: 1px dotted #000000;
	text-align: center;
}

#promotionsContentArea table {
	border: 0px;
	width: 100%;
	border-collapse: collapse;
}

#promotionsContentArea table input {
	width: 100px;
	float: none;
	margin: 0px;
}

#promotionsContentArea a {
	text-decoration: none;
	color: #1A419D;
	font-weight: bold;
}

#productAddReviewIframe {
	height: 350px;
}
#pendingReviewDialog {
	display: block;
}
/* Admin Product Form -> Options Section */
#optionsContentArea {
	border: 1px solid #FFF;
	height: 99%;
	text-align: left;
	padding-left: 5px;
	padding-right: 5px;
	display: none;
}

#optionsContentArea a {
	text-decoration: none;
	color: #1A419D;
	font-weight: bold;
}

#optionsContentArea th,td {
	font-size: 10pt;
	padding: 5px 5px 5px 5px;
	border: 1px dotted #000000;
	text-align: center;
}

#optionsContentArea table {
	border: 0px;
	width: 100%;
	border-collapse: collapse;
}

#optionsContentArea table input {
	width: 100px;
	float: none;
	margin: 0px;
}

#productAttributeIframe {
	height: 70px;
	width: 600px;
	border: 1px solid #FFF;
}

/* Admin Product Form -> Upgrades Section */
#upgradesContentArea {
	border: 1px solid #FFF;
	height: 99%;
	text-align: left;
	padding-left: 5px;
	padding-right: 5px;
	display: none;
}

.upgradesProductContainer {
	border: 1px solid #aaa;
	width: 300px;
	padding: 5px;
	float: left;
	margin-bottom: 10px;
	margin-left: 10px;
}

.upgradesProductContainer img {
	width: 50px;
	height: 50px;
	float: left;
}

.upgradesProductContainer div {
	height: 60px;
	width: 225px;
	float: left;
	margin: 0px;
	padding: 0px;
	border: 1px solid #FFF;
	line-height: 14pt;
	margin-left: 10px;
}

#upgradeList {
	border: 1px solid #FFF;
	float: left;
}

/* Admin Product Form -> Cross-Sell Section */
#relatedList {
	border: 1px solid #FFF;
	float: left;
}

#similarList {
	border: 1px solid #FFF;
	float: left;
}

#crossSellContentArea {
	border: 1px solid #FFF;
	height: 99%;
	text-align: left;
	padding-left: 5px;
	padding-right: 5px;
	display: none;
}

.relatedProductContainer {
	border: 1px solid #aaa;
	width: 300px;
	padding: 5px;
	float: left;
	margin-bottom: 10px;
	margin-left: 10px;
}

.relatedProductContainer img {
	width: 50px;
	height: 50px;
	float: left;
}

.relatedProductContainer div {
	height: 60px;
	width: 225px;
	float: left;
	margin: 0px;
	padding: 0px;
	border: 1px solid #FFF;
	line-height: 14pt;
	margin-left: 10px;
}

.similarProductContainer {
	border: 1px solid #aaa;
	width: 300px;
	padding: 5px;
	float: left;
	margin-bottom: 10px;
	margin-left: 10px;
}

.similarProductContainer img {
	width: 50px;
	height: 50px;
	float: left;
}

.similarProductContainer div {
	height: 60px;
	width: 225px;
	float: left;
	margin: 0px;
	padding: 0px;
	border: 1px solid #FFF;
	line-height: 14pt;
	margin-left: 10px;
}

/* Admin Product Form -> Images Section */
#imagesContentArea {
	border: 1px solid #FFF;
	height: 99%;
	text-align: left;
	padding-left: 5px;
	padding-right: 5px;
	display: none;
}

.imageListContainer {
	border: 1px solid #FFF;
	float: left;
}

.imageContainer {
	border: 1px solid #DEDEDE;
	float: left;
	width: 600px;
	height: 100px;
	margin-bottom: 10px;
	padding: 5px;
}

.imageContainer div {
	float: left;
	border: 1px solid #FFF;
	margin-left: 10px;
	width: 350px;
	height: 100px;
}

.imageContainer img {
	float: left;
}

#uploadImageIframe {
	height: 70px;
	width: auto;
	border: 1px solid #FFF;
}

/* Admin Product Form -> Categories Section */
#categoriesList {
	border: 1px solid #FFF;
	float: left;
}

#categoriesContentArea {
	border: 1px solid #FFF;
	height: 99%;
	text-align: left;
	padding-left: 5px;
	padding-right: 5px;
	display: none;
}

.categoriesCategoryContainer {
	border: 1px solid #aaa;
	width: 300px;
	padding: 5px;
	float: left;
	margin-bottom: 10px;
	margin-left: 10px;
}

.categoriesCategoryContainer img {
	width: 50px;
	height: 50px;
	float: left;
}

.categoriesCategoryContainer div {
	height: 60px;
	width: 225px;
	float: left;
	margin: 0px;
	padding: 0px;
	border: 1px solid #FFF;
	line-height: 14pt;
	margin-left: 10px;
}

/*************************
* End Admin Product Form *
**************************/

/** admin category tab section **/
.categorySelectorViewContainer {
	width: 465px;
	height: 175px;
	border: 1px solid #000;
	overflow: auto;
}

.categorySelectorViewContainer a {
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
	border-right: 1px solid #FFF;
	float: left;
	overflow: auto;
	overflow-x: hidden;
}

.subLevelCategoryContainer input {
	width: auto;
	float: none;
	margin: 0px;
}

.categorySelectorViewMenuItem {
	background-color: #FFFFFF;
	margin-bottom: 2px;
	padding-top: 3px;
	padding-bottom: 3px;
	padding-left: 29px;
	background-image: url(../wombat7/dtree/img/folder.gif);
	background-position: 5px center;
	background-repeat: no-repeat;
	width: 88%;
}

.categorySelectorViewMenuItem input {
	width: auto;
	float: none;
	margin: 0px;
}

.categorySelectorViewMenuItem a {
	display: block;
}

.categorySelectorViewMenuItemFocus {
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

.categoryViewOutputProductContainer {
	border: 1px solid #aaa;
	width: 300px;
	padding: 5px;
	float: left;
	margin-bottom: 10px;
	margin-left: 10px;
}

.categoryViewOutputProductContainer img {
	width: 50px;
	height: 50px;
	float: left;
}

.categoryViewOutputProductContainer div {
	height: 60px;
	width: 225px;
	float: left;
	margin: 0px;
	padding: 0px;
	border: 1px solid #FFF;
	line-height: 14pt;
	margin-left: 10px;
}
