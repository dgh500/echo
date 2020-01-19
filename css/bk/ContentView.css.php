<?php
header('Content-Type: text/css');
require('Colors.php');
?>
#contentContainer {
	line-height: 20px;	
}

#contentContainer a {
	text-decoration: underline;
	color: #00F;
}

#contentContainer a.hide, #contentContainer a.hide:visited, #contentContainer a.hide:hover {
	text-decoration: none;
	color: #000;
}

#contentContainer table {
	border-collapse: collapse;
	border: 1px solid #CCC;
	width: 100%;
}
#contentContainer th {
	font-weight: bold;
	font-size: 10pt;
	border: 1px solid #CCC;	
	padding: 5px;
}
#contentContainer td {
	font-size: 10pt;
	border: 1px solid #CCC;
	padding: 5px;
}
#contentContainer td.green {
	background-color: #0F0;	
}
