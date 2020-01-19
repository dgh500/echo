<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/****************
* Settings Form *
*****************/
#adminTagsViewContainer {
	display: block;
	text-align: center;
	margin-top: 10px;
}

#adminTagsViewContentContainer {
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
	text-align: left;
}

#adminTagsViewContentContainer label {
	display: inline;
}

#adminTagsViewContentContainer input {
	display: inline;
}

#adminTagsViewContentContainer h1 {
	margin: 0px;
}
#tagMenuContainer {
	float: left; 
	width: 300px;	
}
#tagMenu {
	width: 295px;
	height: 400px;
	border: 1px solid #aaa;
}
#editTagArea {
	float: left; 
	width: 750px; 
	overflow: visible;
}