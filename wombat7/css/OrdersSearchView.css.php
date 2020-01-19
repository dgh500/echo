<?php
header('Content-Type: text/css');
require('Colors.php');
?>
#searchBarContainer input {
	display: inline;
}

#ordersSearchText, #addressSearchText {
	width: 300px;
}

#searchBarContainer #suggestions {
	border: 1px solid #AAA;
	border-top: 0px;
	padding: 5px;
	width: 288px;
	position: absolute;
	left: 53px;
	top: 21px;
	display: none;
}
