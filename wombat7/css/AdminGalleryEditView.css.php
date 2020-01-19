<?php
header('Content-Type: text/css');
require('Colors.php');
?>
br {
	clear: both;
}

#adminGalleryViewTabContainer {
	background: url("../images/tab_b.gif") repeat-x bottom;
	width: 750px;
	height: 26px;
	display: block;
}
#galleryEditFormContainer {
	border: 1px solid #84B0C7;
	border-top: 0px;
	padding: 5px;
	width: 738px;
}

#adminGalleryViewTabContainer ul {
	list-style: none;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
	margin: 0px;
	margin-left: 5px;
	padding: 0px;
}
#adminGalleryViewTabContainer li {
	float: left;
	background: url("../images/prodLeft_both.gif") no-repeat left top;
	margin: 0;
	padding: 0 0 0 9px;
	height: 25px;
	border-bottom: 0px solid #FFF;
}
#adminGalleryViewTabContainer a {
	float: left;
	display: block;
	width: .1em;
	background: url("../images/prodRight_both.gif") no-repeat right top;
	padding: 5px 15px 4px 6px;
	text-decoration: none;
	font-weight: bold;
	color: #1A419D;
}

#adminGalleryViewTabContainer>ul a {
	width: auto;
}
#adminGalleryViewTabContainer li:hover {
	background-position: 0 -150px;
	color: #F00;
}
#adminGalleryViewTabContainer a:hover {
	background-position: 100% -150px;
	color: #F00;
}


#galleryEditFormContainer label {
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

#galleryEditFormContainer input {
	float: left;
	text-align: left;
	margin-bottom: 10px;
    width: 150px;
}

#galleryEditFormButtons {
	float: right;
	margin-top: 10px;
	border: 1px solid #fff;
	margin-right: 12px;
}

#galleryEditForm {
	display: inline;
}

#galleryEditFormButtons input {
	width: auto;
	text-align: center;
}

#galleryDetailsContentArea {
	border: 1px solid #FFF;
	height: 400px;
	text-align: left;
	padding-left: 5px;
	padding-right: 5px;
	display: block;
}
#galleryItemsContentArea {
	border: 1px solid #FFF;
	height: 400px;
	text-align: left;
	padding-left: 5px;
	padding-right: 5px;
	display: none;
	overflow-y: scroll;
}

#addGalleryItemIframe {
	height: 150px;
    width: 710px;
}
hr {
	width: 95%;
    height: 1px;
    color: #000;
    background-color: #000;    
}
#adminGalleryViewContentContainer {
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

#galleryPanelWidth, #galleryPanelHeight, #galleryFrameHeight, #galleryFrameWidth, #galleryTransitionSpeed, #galleryTransitionInterval,#galleryNavTheme {
	width: 50px !important;
    margin-right: 20px !important;
}
#newGalleryItemDescription {
	font-family: Arial;
    font-size: 10pt;
}
#currentGalleryContainer {
	border: 1px solid #999; 
    width: 571px; 
    height: 150px; 
    margin-bottom: 10px;
}

#addGalleryCaptionContainer {
	border: 0px solid #000; 
    width: 230px;
    padding: 10px; 
    height: 80px;
    margin-top: 25px; 
    float: left;
}

#addGalleryImageContainer {
	border: 0px solid #000; 
    width: 250px; 
    height: 150px; 
    float: left;
}
#addGalleryImageContainer img {
    width: 250px; 
    height: 150px; 
    border-left: 1px solid #999999; 
}
#addGalleryButtonContainer {
	border-left: 1px solid #999999; 
    width: 70px; 
    height: 150px; 
    float: left; 
    background-color: #D3D3D3;
}

#addGalleryButtonContainer img {
	margin: 0px; 
	float: left;
}
#addGalleryButtonContainer img:hover {
	cursor: pointer; cursor: hand;
}




