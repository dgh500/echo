<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/* Recently Viewed */
#recentlyViewedContainer {
	height: 160px;
	width: 170px;
	position: relative;
	margin-bottom: 5px;
	text-align: center;
}

#recentlyViewedProductContainer {
	height: 100px;
	width: 170px;
	text-align: center;
	line-height: 100px;
	margin-top: 5px;
}

#recentlyViewedProductContainer img,#recentlyViewedTitle {
	vertical-align: middle;
	margin-bottom: 5px !important;
}

#trainingContainer {
	height: 182px;
	width: 170px;
	position: relative;
	margin-bottom: 5px;
}

#recentlyViewedContainer #recentlyViewedTitle {
	position: absolute;
	left: 0px;
	top: 0px;
}

#recentlyViewedContainer #recentlyViewedContent {
	position: absolute;
	left: 0px;
	top: 27px;
	width: 170px;
	height: 130px;
	border: 1px solid #CCC;
}

#recentlyViewedContainer #recentlyViewedContent img {
	border: 0px solid #000;
	margin-left: auto;
	margin-right: auto;
}

#recentlyViewedContainer #recentlyViewedContent h4 {
	font-size: 10pt;
	margin: 0px;
}