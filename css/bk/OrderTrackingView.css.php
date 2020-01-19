<?php
header('Content-Type: text/css');
require('Colors.php');
?>
#orderTrackingTable {
	border: 1px solid #000;
	border-collapse: collapse;
	margin-left: auto;
	margin-right: auto;
	font-size: 10pt;
}

#orderTrackingTable a {
	text-decoration: underline;
}

#orderTrackingTable td {
	border: 1px solid #000;
	padding: 5px;
	background-color: #e4e4e4;
}

#orderTrackingTable th {
	border: 1px solid #000;
	background-color: #adadad;
	border-bottom: 0px;
	padding: 5px;
	color: #FFFFFF;
}

#orderTrackingTable .right {
	text-align: right;
}

#orderTrackingTable .center {
	text-align: center;
}

#orderTrackingTable .left {
	text-align: left;
}

#orderTrackingForm .required {
	font-weight: bold;
	color: #FF0000;
}

#orderTrackingForm label {
	width: 150px;
	display: block;
	float: left;
	text-align: right;
}

#orderTrackingForm input {
	margin-left: 10px;
	margin-bottom: 10px;
}