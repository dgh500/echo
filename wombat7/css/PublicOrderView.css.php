<?php
header('Content-Type: text/css');
require('Colors.php');
?>
#publicOrderView {
	margin-left: 10px;
	font-size: 10pt;
	font-family: Arial, Helvetica, sans-serif;
	color: #000;
}

#publicOrderView h1 {
	color: #000;
	margin: 0px;
	padding: 0px;
}

#publicOrderView br {
	margin-top: 2px;
	margin-bottom: 2px;
}

#publicOrderView .indent {
	display: block;
	width: 150px;
	float: left;
}

#publicOrderView table {
	margin-top: 10px;
	margin-bottom: 10px;
	border: 1px solid #000;
	border-collapse: collapse;
	width: 530px;
	font-size: 10pt;
}

#publicOrderView td {
	border: 1px solid #000;
	padding: 5px;
}

#publicOrderView .right {
	text-align: right;
}

#publicOrderView th {
	border: 1px solid #000;
	border-bottom: 0px;
	padding: 5px;
}

#publicOrderView #productColumn {
	text-align: left;
}
#publicOrderView #qtyColumn {
	text-align: center;
	width: 75px;
}
#publicOrderView #unitPriceColumn {
	text-align: center;
	width: 70px;
}
#publicOrderView #shippedColumn {
	text-align: center;
	width: 50px;
}


