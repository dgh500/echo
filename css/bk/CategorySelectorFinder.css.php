<?php
header('Content-Type: text/css');
require('Colors.php');
?>
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