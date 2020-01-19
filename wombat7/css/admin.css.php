<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/*
	This file contains 'general' styles for the admin side of the site.
*/ 

html,body,label,p {
	margin: 0px;
	padding: 0px;
	border: 0px;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
}

img {
	border: 0px;
}

/********************
* Admin Menu (Tabs) *
*********************/
#adminMenuHeader {
	float: left;
	width: 100%;
	height: 26px;
	background: url("../images/tab_b.gif") repeat-x bottom;
}

#adminMenuHeader ul {
	list-style: none;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
	margin: 0px;
	padding: 0px;
	padding-left: 10px;
}

#adminMenuHeader li {
	float: left;
	background: url("../images/left_both.gif") no-repeat left top;
	margin: 0;
	padding: 0 0 0 9px;
	height: 25px;
}

#adminMenuHeader a {
	float: left;
	display: block;
	width: .1em;
	background: url("../images/right_both.gif") no-repeat right top;
	padding: 5px 15px 4px 6px;
	text-decoration: none;
	font-weight: bold;
	color: #1A419D;
}

#adminMenuHeader>ul a {
	width: auto;
}

#adminMenuHeader a:hover {
	color: #F00;
}

#adminMenuHome #adminMenuNav-home,
#adminMenuProducts #adminMenuNav-products,
#adminMenuOrders #adminMenuNav-orders,
#adminMenuSettings #adminMenuNav-settings,
#adminMenuCatalogue #adminMenuNav-catalogue,
#adminMenuLogout #adminMenuNav-logout,
#adminMenuContent #adminMenuNav-content,
#adminMenuMisc #adminMenuNav-misc,
#adminMenuManufacturers #adminMenuNav-manufacturers,
#adminMenuGalleries #adminMenuNav-galleries,
#adminMenuReports #adminMenuNav-reports,
#adminMenuSalesReports #adminMenuNav-salesReports,
#adminMenuHelp #adminMenuNav-help,
#adminMenuAffiliates #adminMenuNav-affiliates, 
#adminMenuTags #adminMenuNav-tags
	{
	background-position: 0 -150px;
	border-width: 0;
	border-bottom: 1px solid #FFF;
}

#adminMenuHome #adminMenuNav-home a,
#adminMenuProducts #adminMenuNav-products a,
#adminMenuOrders #adminMenuNav-orders a,
#adminMenuSettings #adminMenuNav-settings a,
#adminMenuCatalogue #adminMenuNav-catalogue a,
#adminMenuLogout #adminMenuNav-logout a,
#adminMenuContent #adminMenuNav-content a
,#adminMenuMisc #adminMenuNav-misc a,
#adminMenuManufacturers #adminMenuNav-manufacturers a, 
#adminMenuGalleries #adminMenuNav-galleries a ,
#adminMenuReports #adminMenuNav-reports a,
#adminMenuSalesReports #adminMenuNav-salesReports a,
#adminMenuHelp #adminMenuNav-help a,
#adminMenuAffiliates #adminMenuNav-affiliates a, 
#adminMenuTags #adminMenuNav-tags a
	{
	background-position: 100% -150px;
	padding-bottom: 5px;
	color: #1A419D;
}

#adminMenuHeader li:hover,#adminMenuHeader li:hover a {
	background-position: 0% -150px;
	color: #1A419D;
}

#adminMenuHeader li:hover a {
	background-position: 100% -150px;
}

/*************
* Admin Logo *
**************/
#adminHeaderLogo {
	margin: 20px;
	margin-top: 10px;
	height: 60px;
	width: 283px;
}

/**********************
* Product Menu (Tree) *
***********************/
#productMenuCatalogueSelection {
	border: 1px solid #FFF;
	width: 300px;
	height: 35px;
	line-height: 25px;
	text-align: center;
	float: left;
	margin-top: 10px;
}

#productMenuProductTree {
	border: 1px solid #aaa;
	padding: 0px;
	margin: 0px;
	width: 295px;
	height: 550px;
	overflow: scroll;
	float: left;
	clear: left;
	padding-top: 5px;
	padding-left: 5px;
}

#productMenu {
	margin-top: 10px;
	width: 295px;
	height: 550px;
	border: 1px solid #aaa;
}

/************
* Edit Area *
*************/
#editAreaContainer {
	border: 0px solid #aaa;
	width: 750px;
	height: 587px;
	overflow: auto;
	float: left;
	margin-top: 10px;
	margin-left: 10px;
}