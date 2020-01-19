<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/* Header Styles */
#header {
	clear: both;
	float: left;
	margin: 0px;
	padding: 0px;
	height: 210px;
	text-align: left;
}

#headerLeftSection {
	width: 375px;
	height: 210px;
	background-image: url(../images/headerLeftSectionBottomBg.gif);
	background-repeat: no-repeat;
	background-position: bottom;
	float: left;
	position: relative;
}

#headerMidSection {
	height: 210px;
	position: relative;
	width: 10px;
	background-image: url(../images/headerMidSectionBg.gif);
	background-repeat: repeat-x;
	float: left;
}

#headerRightSection {
	height: 210px;
	width: 625px;
	background-image: url(../images/headerRightSectionBottomBg.gif);
	background-repeat: no-repeat;
	float: left;
	position: relative;
}

#headerImagesContainer {
	width: 625px;
	height: 157px;
	background-image: url(../images/headerImages.jpg);
    background-repeat: no-repeat;
}

#logo {
	width: 375px;
	height: 135px;
	position: relative;
	margin: 0px;
	padding: 0px;
	display: block;
}

#logo span {
	background: url(../images/logo.gif);
	position: absolute;
	width: 100%;
	height: 100%;
	display: block;
}