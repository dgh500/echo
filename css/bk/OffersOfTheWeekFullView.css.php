<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/* Offers of the week styles */
#offersOfTheWeekFullContainer {
	width: 550px;
	position: relative;
	margin-bottom: 10px;
}

#offersOfTheWeekFullContainer #offersOfTheWeekTitle {
	position: absolute;
	top: 0px;
	left: 0px;
}

#offersOfTheWeekFullContainer a {
	color: #000000;
}

#offersOfTheWeekFullContainer .offerImageContainer {
	height: 140px;
	width: 140px;
	line-height: 140px;
}

#offersOfTheWeekLogo {
	width: 250px;
	height: 40px;
	position: relative;
	margin: 0px;
	padding: 0px;
}

#offersOfTheWeekLogo span {
	background: url(../images/offersOfTheWeek.gif);
	position: absolute;
	width: 100%;
	height: 100%;
}