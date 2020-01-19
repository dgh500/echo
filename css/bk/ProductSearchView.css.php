<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/* Product Search Styles */
#productSearchContainer {
	height: 65px;
	width: 190px;
	background-image: url(../images/productSearchBg.gif);
	background-repeat: no-repeat;
	margin-left: 35px;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
	color: #FFFFFF;
	font-weight: bold;
	position: relative;
}

#productSearchText {
	position: absolute;
	top: 5px;
	left: 10px;
	width: 100px;
}

#productSearchContainer #q {
	position: absolute;
	left: 8px;
	top: 28px;
	width: 140px;
}

#productSearchContainer #sa {
	position: absolute;
	top: 25px;
	left: 160px;
}