<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/* Package Styles */
#packageDetailContainer {
	float: left;
	width: 550px;
	position: relative;
}

#packageDetailContainer #packageTitle {
	height: 32px;
	width: 550px;
	background-image: url(../images/productTitleBg.gif);
	background-repeat: no-repeat;
	position: relative;
	float: left;
	overflow: hidden;
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
	height: 20px;
	overflow: hidden;
	width: 530px;
	font-size: 12pt;
	position: relative;
	top: 5px;
	left: 10px;
	color: #FFFFFF;
}

#packageDetailContainer #packageTitle a {
	color: #FFFFFF;
}

#packageDetailContainer #packageDetailsTopSection {
	border: 1px solid #B5B5B5;
	border-top: 0px;
	width: 548px;
	height: 370px;
	position: relative;
	float: left;
	z-index: 10;
}

#packageDetailContainer #packageDetailsOverviewSection {
	border: 1px solid #B5B5B5;
	border-top: 0px;
	width: 550px;
	position: relative;
	float: left;
}
html>body #packageDetailContainer #packageDetailsOverviewSection {
	width: 548px;	
}
#packageDetailContainer #packageDetailsOverviewSection div {
	margin: 10px;
}

#packageDetailContainer #overviewTitle {
	height: 32px;
	width: 550px;
	background-image: url(../images/productTitleBg.gif);
	background-repeat: no-repeat;
	position: relative;
	float: left;
	margin-top: 10px;
}

#packageDetailContainer #overviewTitle h2 {
	margin: 0px;
	height: 32px;
	width: 530px;
	font-size: 12pt;
	position: relative;
	top: 5px;
	left: 10px;
	color: #FFFFFF;
}

#packageDetailContainer #packageDetailsUpgradesSection {
	border: 1px solid #B5B5B5;
	border-top: 0px;
	width: 550px;
	position: relative;
	float: left;
}
html>body #packageDetailContainer #packageDetailsUpgradesSection {
	width: 548px;	
}

#packageDetailContainer #packageDetailsUpgradesSection div {
	margin: 10px;
}

#upgradesTitle {
	height: 32px;
	width: 550px;
	background-image: url(../images/productTitleBg.gif);
	background-repeat: no-repeat;
	position: relative;
	float: left;
	margin-top: 10px;
}

#upgradesTitle h2 {
	margin: 0px;
	height: 32px;
	width: 530px;
	font-size: 12pt;
	position: relative;
	top: 5px;
	left: 10px;
	color: #FFFFFF;
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
	height: 300px;
	width: 220px;
	position: absolute;
	top: 10px;
	left: 320px;
}

#packageDetailContainer #packageDetailsTopSection #packageText h1 {
	width: 220px;
	font-size: 12pt;
	border: 0px solid #f00;
	margin: 0px;
	color: #000000;
}

#packageDetailContainer #packageDetailsTopSection #packageText #packageNowPrice
	{
	border: 0px solid #f00;
	width: 220px;
	height: 20px;
	color: #EA742C;
	font-weight: bold;
}

#packageDetailContainer #packageDetailsTopSection #packageText #packageWasPrice
	{
	border: 0px solid #f00;
	width: 220px;
	height: 20px;
	color: #939AA1;
	font-weight: bold;
	text-decoration: line-through;
}
#packageDetailContainer #packageDetailsTopSection #packageText #packageSaving
	{
	border: 0px solid #f00;
	width: 220px;
	height: 20px;
	color: #FF0000;
	font-weight: bold;
}

#packageDetailContainer #packageDetailsTopSection #packageOptions {
	border: 0px solid #f00;
	width: 220px;
	color: #939AA1;
	font-weight: bold;
	margin: 0px;
	padding: 0px;
	position: relative;
	top: -20px;
}
html>body #packageDetailContainer #packageDetailsTopSection #packageOptions {
	top: 0px;	
}

#packageDetailContainer #packageDetailsTopSection #packageOptions h3 {
	width: 220px;
	height: 30px;
	line-height: 30px;
	text-indent: 10px;
	font-size: 10pt;
	background-color: #CCCCCC;
	color: #414141;
	margin: 0px;
}

#packageDetailContainer #packageDetailsTopSection #packageText #packageOptions #optionsContainer {
	width: 220px;
	background-color: #E9E9E9;
	color: #6B6B6B;
	margin: 0px;
	padding: 0px;
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
	top: 320px;
	left: 10px;
	z-index: 0;
}

#packageDetailContainer #packageDetailsTopSection #packageButtons #inStock
	{
	border: 0px solid #f00;
	width: 110px;
	height: 24px;
	position: absolute;
	top: 0px;
	left: 20px;
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
	width: 550px;
	height: 170px;
	position: relative;
	margin-top: 10px;
}

.packageContentsProductContainer {
	width: 550px;
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
	height: 25px;
	width: 380px;
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
	color: #EA742C;
	width: 200px;
	float: left;
	height: 25px;
	line-height: 25px;
	font-weight: bold;
}

.packageContentsProductContainer .productDetailsContainer .description {
	font-size: 10pt;
	border: 0px solid #000;
	height: 35px;
	overflow: hidden;
	width: 380px;
	position: absolute;
	top: 20px;
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