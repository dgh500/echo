<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/* Shopping Bag Styles */
#shoppingBagContainer {
	width: 170px;
	height: 120px;
	margin: 0px;
	padding: 0px;
	position: relative;
	margin-bottom: 5px;
}

#shoppingBagContainer #shoppingBagTitle {
	position: absolute;
	top: 0px;
}

#shoppingBagContainer #shoppingBagCheckoutNow {
	position: absolute;
	top: 90px;
}

#shoppingBagContainer #shoppingBagItems {
	position: absolute;
	width: 150px;
	top: 5px;
	left: 10px;
	text-align: right;
}

#shoppingBagContainer #shoppingBagItems strong {
	position: absolute;
	left: 0px;
	font-size: 10pt;
}

#shoppingBagContainer #shoppingBagTotal {
	position: absolute;
	width: 150px;
	top: 25px;
	left: 10px;
	text-align: right;
	font-size: 10pt;
}

#shoppingBagContainer #shoppingBagTotal strong {
	position: absolute;
	left: 0px;
	font-size: 10pt;
}

#shoppingBagContainer #shoppingBagContent {
	height: 50px;
	width: 168px;
	background-image: url(../images/shopBagBg.gif);
	background-repeat: repeat-y;
	color: <?php echo $shopBagText; ?>;
	margin: 0px;
	padding: 0px;
	position: absolute;
	top: 40px;
	font-size: 10pt;
}