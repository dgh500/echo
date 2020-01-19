<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/* Postal Rates Styles */
#postalRatesLogo {
	width: 550px;
	height: 94px;
	position: relative;
	margin: 0px;
	padding: 0px;
}

#postalRatesLogo span {
	background: url(../images/postalRates.gif);
	position: absolute;
	width: 100%;
	height: 100%;
}

#postalRatesContainer {
	padding: 5px;
}

#postalRatesContainer a {
	font-weight: bold;
}

#postalRatesContainer table {
	border: 1px solid #000;
	border-collapse: collapse;
	margin-top: 10px;
	margin-bottom: 10px;
}

#postalRatesContainer td {
	border: 1px solid #000;
	padding: 5px;
}

#postalRatesContainer th {
	text-align: center;
}

#postalRatesContainer th {
	border: 1px solid #000;
	border-bottom: 0px;
	padding: 5px;
}

#postalRatesNavigation {
	padding: 0px;
	list-style: none;
	clear: both;
}

#postalRatesNavigation li {
	display: inline;
	margin: 0px;
}

#postalRatesNavigation li a {
	display: block;
	margin-bottom: 10px;
	padding: 11px;
	float: left;
	border: 0px solid #000;
	background-color: #e2e2e2;
}

#postalRatesNavigation li a:hover {
	display: block;
	margin-bottom: 10px;
	padding: 11px;
	float: left;
	border: 0px solid #000;
	background-color: #000;
	color: #FFFFFF;
}