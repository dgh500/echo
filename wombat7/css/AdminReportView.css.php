<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/****************
*   Reports Tab   *
*****************/
br {
	clear: both;	
}
#adminReportsViewContainer {
	width: 900px;
	display: block;
	text-align: center;
	margin-top: 10px;
}

#adminReportsViewContentContainer {
	border: 1px solid #aaa;
	border-top: 0px solid #FFF;
	background-color: #FFFFFF;
	width: 900px;
	height: 485px;
	display: block;
	margin-left: auto;
	margin-right: auto;
	padding-top: 5px;
	overflow: auto;
	overflow-x: hidden;
	text-align: left;
	padding: 10px;
}

#adminReportsViewContentContainer select {
	margin: 5px;
}
#reportChoice {
	width: 200px;
	height: 100px;
	display: block;
	float: left;
}

#reportDateRange {
	width: 400px;
	height: 100px;
	display: block;
	float: left;
	border-left: 1px solid #aaa;
	padding-left: 20px;
	margin-left: 15px;
}

#reportDateRange a {
	color: #000000;
	font-weight: bold;
}

#reportDateRange label,#reportDateRange input {
	width: 70px;
	display: block;
	float: left;
	margin-top: 5px;
}

#reportResults {
	width: 890px;
	padding: 5px;
	height: auto;
	border: 1px solid #aaa;
	display: none;
}

#contentLoading {
	display: none;
	width: 100px;
	height: 100px;
	margin-left: 350px;
	background-image: url(../images/contentLoading.gif);
}

hr {
	height: 1px;
	background-color: #aaa;
	border: 0px;
}
