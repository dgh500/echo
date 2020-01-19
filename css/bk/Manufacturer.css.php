<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/* Manufacturer View Styles */
#manufacturerViewListContainer {
	float: left;
	width: 550px;
	position: relative;
}

#manufacturerDescriptionContainer {
	width: 550px;
	height: 90px;
}

#manufacturerDescriptionContainer img {
	float: left;
}

#manufacturerDescriptionContainer #manufacturerDescriptionText {
	width: 350px;
	height: 75px;
	margin-left: 10px;
	float: left;
}

#manufacturerViewListContainer #manufacturerViewListContainerTitle {
	width: 550px;
	height: 32px;
	background-image: url(../images/categoryListTitleBg.gif);
	background-repeat: no-repeat;
	color: #FFFFFF;
}

#manufacturerViewListContainer #manufacturerViewListContainerTitle h2 {
	margin: 0px;
	height: 32px;
	width: 320px;
	font-size: 12pt;
	position: relative;
	top: 5px;
	left: 10px;
}

#manufacturerViewListContainer #manufacturerViewListContainerTitle a {
	color: #FFFFFF;
}

#manufacturerViewListContainer #manufacturerViewListContainerTitle #sortByContainer
	{
	width: 200px;
	height: 32px;
	position: absolute;
	right: 10px;
	top: 5px;
}

#manufacturerViewListContainer #manufacturerViewListContainerTitle #sortByContainer select
	{
	width: 200px;
}

#manufacturerViewListContainer #pageNumbersContainer {
	height: 32px;
	line-height: 32px;
	font-weight: bold;
	padding-left: 10px;
	padding-right: 10px;
	width: 530px;
	font-size: 10pt;
	background-color: #CCCCCC;
	color: #383838;
	float: left;
	clear: both;
}

#manufacturerViewListContainer #pageNumbersContainer a {
	font-weight: normal;
}

#manufacturerViewListContainer #pageNumbersContainer a.currentPageNumber
	{
	font-weight: bold;
}

#manufacturerViewListContainer #categoryListContainer {
	font-weight: bold;
	width: 550px;
	font-size: 10pt;
	background-color: <?php echo $manListBg; ?>;
	clear: both;
	float: left;
	display: block;
}

#manufacturerViewListContainer #categoryList {
	margin: 0px;
	padding: 0px;
	list-style-image: none;
	list-style-type: none;
	clear: both;
	float: left;
}

#manufacturerViewListContainer #categoryList li {
	margin: 0px;
	padding: 0px;
	width: 110px;
	float: left;
	height: 32px;
	position: relative;
	text-align: center;
}

#manufacturerViewListContainer #categoryList li a {
	background-color: <?php echo $manListBg; ?>;
	color: <?php echo $manListLink; ?>;
	display: block;
	height: 32px;
	padding-left: 5px;
	padding-right: 5px;
	position: relative;
}

#manufacturerViewListContainer #categoryList li a:hover {
	background-color: <?php echo $manListLink; ?>;
	color: <?php echo $manListBg; ?>;
}