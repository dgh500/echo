<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/* Affiliate Styles */
#affiliateBackForm {
	text-align: center;
	margin-top: 10px;
}

#affiliateTable {
	width: 550px;
	border: 1px solid #000;
	border-collapse: collapse;
}

#affiliateTable td {
	border: 1px solid #000;
	padding: 5px;
	font-size: 10pt;
	color: #000;
	background-color: <?php echo $affTd; ?>;
}

.left {
	text-align: left;
}
.center {
	text-align: center;
}
.productRow {
	display: none;
}
#affiliateTable th {
	border: 1px solid #000;
	border-bottom: 0px;
	padding: 5px;
	font-size: 10pt;
	background-color: <?php echo $affTh; ?>;
	color: #FFFFFF;
	text-align: center;
}

#affiliateTable tfoot td {
	border: 1px solid #000;
	font-weight: bold;
	border-bottom: 0px;
	padding: 5px;
	font-size: 10pt;
	background-color: <?php echo $affTh; ?>;
	color: #FFFFFF;
}