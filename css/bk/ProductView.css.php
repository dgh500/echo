<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/* Product Styles */
#productDetailContainer {
	float: left;
	width: 550px;
	position: relative;
}

#productDetailContainer #productTitle {
	height: 32px;
	width: 550px;
	background-image: url(../images/productTitleBg.gif);
	background-repeat: no-repeat;
	position: relative;
	float: left;
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
	height: 32px;
	width: 530px;
	font-size: 12pt;
	position: relative;
	top: 5px;
	left: 10px;
	color: #FFFFFF;
}

#productDetailContainer #productTitle a {
	color: #FFFFFF;
}

#productDetailContainer #productDetailsTopSection {
	border: 1px solid #B5B5B5;
	border-top: 0px;
	width: 548px;
	height: 370px;
	position: relative;
	float: left;
}

#productDetailContainer #productDetailsOverviewSection {
	border: 1px solid #B5B5B5;
	border-top: 0px;
	width: 548px;
	position: relative;
	float: left;
    line-height: 1.5em;    
}

#productDetailsOverviewSection  a {
	text-decoration: underline;
}


#productDetailContainer #productDetailsOverviewSection div {
	margin: 10px;
}

#productDetailContainer #productDetailsOverviewSection div div {
	margin: 0px;
}

/* Package Cross Sell Section */
#productDetailContainer #packageCrosssellSection {
	border: 1px solid #B5B5B5;
	border-top: 0px;
	width: 548px;
	position: relative;
	float: left;
	padding-bottom: 10px;
}

#packageCrosssellSection  a {
	text-decoration: underline;
}


#productDetailContainer #packageCrosssellSection div {
	margin: 10px;
}

#packageCrosssellSection .stackContainer {
	width: 520px;
	border: 1px solid #ccc;
	float: left;
	margin: 6px !important;	/* Override the rule above */
}

.stackContainer img {
	display: block;
	float: left;	
	border: 1px solid #FFF;
	margin: 10px;
	padding: 0px;
}

.stackContainer .stackDescription {
	display: block;	
	float: left;	
	border: 1px solid #FFF;
	margin-right: 0px !important;
	margin-left: 0px !important;
	margin-top: 10px !important;
	margin-bottom: 10px !important;
	padding: 0px;
	width: 390px;
}
.stackContainer .stackDescription .wasPrice {
	color: #666;
	font-weight: bold;
	text-decoration: line-through;
}
.stackContainer .stackDescription .nowPrice {
	color: #F38B28;
	font-weight: bold;
}

/* End Package Cross Sell Section */

#readMoreBar {
	width: 530px;
	background-color: #CCC;
	text-align: center;
	padding-top: 5px;
	padding-bottom: 5px;
	font-weight: bold;
	margin-top: 10px;
}

#readMoreLink :hover {
	cursor: pointer;
}

#productDetailContainer #overviewTitle, #productDetailContainer #packageCrosssellTitle {
	height: 32px;
	width: 550px;
	background-image: url(../images/productTitleBg.gif);
	background-repeat: no-repeat;
	position: relative;
	float: left;
	margin-top: 10px;
}

#productDetailContainer #overviewTitle h2 {
	margin: 0px;
	height: 32px;
	width: 530px;
	font-size: 12pt;
	position: relative;
	top: 5px;
	left: 10px;
	color: #FFFFFF;
}

#productDetailContainer #additionalImagesSection {
	border: 1px solid #B5B5B5;
	border-top: 0px;
	width: 548px;
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
	height: 32px;
	width: 550px;
	background-image: url(../images/productTitleBg.gif);
	background-repeat: no-repeat;
	position: relative;
	float: left;
	margin-top: 10px;
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

#productDetailContainer #multibuySection {
	border: 1px solid #B5B5B5;
	border-top: 0px;
	width: 548px;
	position: relative;
	float: left;
}

#productDetailContainer #multibuySection div {
	margin: 10px;
	float: left;
	width: 110px;
	height: 110px;
	text-align: center;
}

#multibuyTable {
	margin-left: 15px;
	margin-top: 10px;
	margin-bottom: 10px;
	border: 3px solid #CCC;
	border-collapse: collapse;
	width: 520px;
}

#multibuyTable th {
	padding: 10px;
}

#multibuyTable td {
	font-size: 10pt;
	padding-top: 7px;
	padding-bottom: 7px;
	padding-left: 4px;
	padding-right: 4px;
	text-align: center;
}

#multibuyTable td.altCell {
	background-color: #DEDEDE;
}

#multibuyTable td.priceAltCell {
	color: #EA742C;
	font-weight: bold;
	background-color: #DEDEDE;
}

#multibuyTable td.priceCell {
	color: #EA742C;
	font-weight: bold;
	background-color: #FFFFFF;
}

#multibuyTable tr {
	border-bottom: 1px solid #CCC;
}

#multibuyTable .multibuyHeading {
	font-weight: bold;
	font-size: 10pt;
	text-align: right;
	width: 150px;
}

#productDetailContainer #multibuyTitle {
	height: 32px;
	width: 550px;
	background-image: url(../images/productTitleBg.gif);
	background-repeat: no-repeat;
	position: relative;
	float: left;
	margin-top: 10px;
}

#productDetailContainer #multibuyTitle h2 {
	margin: 0px;
	height: 32px;
	width: 530px;
	font-size: 12pt;
	position: relative;
	top: 5px;
	left: 10px;
	color: #FFFFFF;
}

#productDetailContainer #productDetailsSimilarSection {
	border: 1px solid #B5B5B5;
	border-top: 0px;
	width: 548px;
	position: relative;
	float: left;
}

#productDetailContainer #similarTitle {
	height: 32px;
	width: 550px;
	background-image: url(../images/productTitleBg.gif);
	background-repeat: no-repeat;
	position: relative;
	float: left;
	margin-top: 10px;
}

#productDetailContainer #similarTitle h2 {
	margin: 0px;
	height: 32px;
	width: 530px;
	font-size: 12pt;
	position: relative;
	top: 5px;
	left: 10px;
	color: #FFFFFF;
}

#productDetailContainer #productDetailsRelatedSection {
	border: 1px solid #B5B5B5;
	border-top: 0px;
	width: 548px;
	padding: 0px;
	position: relative;
	float: left;
}

#productDetailContainer #relatedTitle {
	height: 32px;
	width: 550px;
	background-image: url(../images/productTitleBg.gif);
	background-repeat: no-repeat;
	position: relative;
	float: left;
	margin-top: 10px;
}

#productDetailContainer #relatedTitle h2 {
	margin: 0px;
	height: 32px;
	width: 530px;
	font-size: 12pt;
	position: relative;
	top: 5px;
	left: 10px;
	color: #FFFFFF;
}

#productDetailContainer #productDetailsTopSection #productImage {
	border: 0px solid #F00;
	height: 300px;
	width: 300px;
	position: absolute;
	top: 10px;
	left: 10px;
	text-align: center;
	line-height: 300px;
}

#productDetailContainer #productDetailsTopSection #productImage img {
	vertical-align: middle;
}

#productDetailContainer #productDetailsTopSection #productText {
	border: 0px solid #F00;
	width: 220px;
	position: absolute;
	top: 10px;
	left: 320px;
}

#productDetailContainer #productDetailsTopSection #productText h1 {
	width: 220px;
	font-size: 12pt;
	border: 0px solid #f00;
	margin: 0px;
	color: #000000;
}

#productDetailContainer #productDetailsTopSection #productText #productNowPrice
	{
	border: 0px solid #f00;
	width: 220px;
	height: 20px;
	color: #EA742C;
	font-weight: bold;
}

#productDetailContainer #productDetailsTopSection #productText #productWasPrice
	{
	border: 0px solid #f00;
	width: 220px;
	height: 20px;
	color: #939AA1;
	font-weight: bold;
}

#productDetailContainer #productDetailsTopSection #productText #productSaving
	{
	border: 0px solid #f00;
	width: 220px;
	height: 20px;
	color: #FF0000;
	font-weight: bold;
}

#productDetailContainer #productDetailsTopSection #sizeChart {
	border: 0px solid #000;
	position: absolute;
	width: 220px;
	height: 40px;
	top: 310px;
	right: 0px;
}

#productDetailContainer #productDetailsTopSection #productText #productOptions
	{
	border: 0px solid #f00;
	width: 220px;
	color: #939AA1;
	font-weight: bold;
}

#productDetailContainer #productDetailsTopSection #productText #productOptions h3
	{
	width: 220px;
	height: 30px;
	line-height: 30px;
	text-indent: 10px;
	font-size: 10pt;
	background-color: #CCCCCC;
	color: #414141;
	margin: 0px;
}

#productDetailContainer #productDetailsTopSection #productText #productOptions #optionsContainer
	{
	width: 220px;
	background-color: #E9E9E9;
	color: #6B6B6B;
}

#productDetailContainer #productDetailsTopSection #productText #productOptions #optionsContainer select
	{
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

#productDetailContainer #productDetailsTopSection #productText #productOptions #optionsContainer img
	{
	margin: 10px;
}

#productDetailContainer #productDetailsTopSection #productButtons {
	border: 0px solid #f00;
	width: 530px;
	height: 50px;
	position: absolute;
	top: 320px;
	left: 10px;
}

#productDetailContainer #productDetailsTopSection #productButtons #inStock
	{
	border: 0px solid #f00;
	width: 110px;
	height: 24px;
	position: absolute;
	top: 0px;
	left: 20px;
}

#productDetailContainer #productDetailsTopSection #productButtons #enlarge
	{
	border: 0px solid #f00;
	width: 119px;
	height: 24px;
	position: absolute;
	top: 0px;
	left: 160px;
}

#relatedProductContainer {
	border: 0px solid #000;
	width: 520px;
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
	color: #EA742C;
	font-weight: bold;
	font-size: 10pt;
	position: absolute;
	bottom: 0px;
	left: 0px;
	width: 170px;
	height: 30px;
}

#relatedProductContainer .relatedProduct .pricesContainer #productWasPrice
	{
	color: #939AA1;
	text-decoration: line-through;
	height: 18px;
	overflow: hidden;
}

#similarProductContainer {
	border: 0px solid #000;
	width: 520px;
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
	color: #EA742C;
	font-weight: bold;
	font-size: 10pt;
	position: absolute;
	bottom: 0px;
	left: 0px;
	width: 170px;
	height: 30px;
}

#similarProductContainer .similarProduct .pricesContainer #productWasPrice
	{
	color: #939AA1;
	text-decoration: line-through;
	height: 18px;
	overflow: hidden;
}