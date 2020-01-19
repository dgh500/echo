<?php
header('Content-Type: text/css');
require('Colors.php');
?>
br {
	clear: both;
}

#adminCatalogueViewTabContainer {
	background: url("../images/tab_b.gif") repeat-x bottom;
	width: 512px;
	height: 26px;
	display: block;
}
#adminCatalogueViewTabContainer ul {
	list-style: none;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
	margin: 0px;
	margin-left: 5px;
	padding: 0px;
}
#adminCatalogueViewTabContainer li {
	float: left;
	background: url("../images/prodLeft_both.gif") no-repeat left top;
	margin: 0;
	padding: 0 0 0 9px;
	height: 25px;
	border-bottom: 0px solid #FFF;
}
#adminCatalogueViewTabContainer a {
	float: left;
	display: block;
	width: .1em;
	background: url("../images/prodRight_both.gif") no-repeat right top;
	padding: 5px 15px 4px 6px;
	text-decoration: none;
	font-weight: bold;
	color: #1A419D;
}

#adminCatalogueViewTabContainer>ul a {
	width: auto;
}
#adminCatalogueViewTabContainer li:hover {
	background-position: 0 -150px;
	color: #F00;
}
#adminCatalogueViewTabContainer a:hover {
	background-position: 100% -150px;
	color: #F00;
}



#catalogueEditFormContainer {
	border: 1px solid #84B0C7;
	border-top: 0px;
	padding: 5px;
	width: 500px;
}

#catalogueEditFormContainer label {
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

#catalogueEditFormContainer input {
	float: left;
	text-align: left;
	margin-bottom: 10px;
}

#catalogueEditFormButtons {
	float: right;
	margin-top: 10px;
	border: 1px solid #fff;
	margin-right: 12px;
}

#catalogueEditForm {
	display: inline;
}

#catalogueEditFormButtons input {
	width: auto;
	text-align: center;
}

#catalogueDetailsContentArea {
	border: 1px solid #FFF;
	height: 400px;
	text-align: left;
	padding-left: 5px;
	padding-right: 5px;
	display: block;
}

#catalogueManufacturersContentArea {
	border: 1px solid #FFF;
	height: 400px;
	text-align: left;
	padding-left: 5px;
	padding-right: 5px;
	display: none;
	overflow-y: scroll;
}
#catalogueTagsContentArea {
	border: 1px solid #FFF;
	height: 400px;
	text-align: left;
	padding-left: 5px;
	padding-right: 5px;
	display: none;
	overflow-y: scroll;
}
#catalogueEstimatesContentArea {
	border: 1px solid #FFF;
	height: 400px;
	text-align: left;
	padding-left: 5px;
	padding-right: 5px;
	display: none;
	overflow-y: scroll;
}

#addManufacturerIframe {
	height: 70px;
}
#addEstimatesIframe {
	height: 70px;
}
#addTagsIframe {
	height: 70px;
}

#adminCatalogueViewContentContainer {
	border-top: 0px solid #FFF;
	background-color: #FFFFFF;
	width: 1000px;
	display: block;
	margin-left: auto;
	margin-right: auto;
	padding-top: 5px;
	overflow: auto;
	overflow-x: hidden;
}
