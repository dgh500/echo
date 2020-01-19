<?php
header('Content-Type: text/css');
require('Colors.php');
?>
#orderform {
	line-height: 18px;
	position: relative;
}

#cancelledOrderBg {
	background-image: url(../images/cancelled.gif);
	background-repeat: no-repeat;
	position: absolute;
	width: 600px;
	height: 600px;
	z-index: -5;
}

#orderform a { color: #000; text-decoration: underline; } 
#orderform a:visited { color: #000; text-decoration: underline; } 
#orderform a:hover { color: #000; text-decoration: underline; } 

#orderform h1 {
	font-size: 15pt;
	margin-top: 2px;
	margin-bottom: 2px;
}

#orderform label {
	width: 150px;
	display: block;
	float: left;
	font-weight: bold;
	clear: both;
}

#orderform .falseInput {
	display: block;
	float: left;
	margin-top: 2px;
}

#orderform select,#orderform input {
	display: block;
	float: left;
	margin-top: 2px;
}
#orderform select {
	width: 146px;	
}
#orderform br {
	clear: both;
}

#orderform table {
	border: 1px solid #aaa;
	width: 550px;
	margin-top: 10px;
	margin-bottom: 10px;
	border-collapse: collapse;
}

#orderform th, td {
	border: 1px solid #aaa;
	font-size: 10pt;
	padding: 5px;
}

#orderform .left {
	text-align: left;
}

#orderform .center {
	text-align: center;
}

#orderform td input {
	margin-left: 15px;
}

#orderform .right {
	text-align: right;
}

#orderform td {
	border: 1px solid #aaa;
	padding: 5px;
}

#orderform #saveChangesOrderFormButton {
	margin-left: 5px;
	margin-top: -1px;
}

#orderform .productColumn {
	text-align: left;
}
#orderform #qtyColumn {
	text-align: center;
	width: 75px;
}
#orderform .unitPriceColumn {
	text-align: center;
	width: 70px;
}
#orderform .shippedColumn {
	text-align: center;
	width: 50px;
}

#orderform #editOrderItemColumn {
	text-align: center;
	width: 25px;
}
#sageCodeChange {
	width: 480px;
}
