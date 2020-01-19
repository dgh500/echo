<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/* Account Navigation */
#accountNavBarContainer {
	width: 625px;
	height: 50px;
	background-color: #999999;
}

#accountNavigation {
	margin: 0px;
	padding: 0px;
	height: 50px;
}

#accountNavigation li {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
	color: <?php $accNavLink ?>;
	font-weight: bold;
	display: inline;
	display: block;
	float: left;
}

#accountNavigation a {
	text-decoration: none;
	color: <?php $accNavLink ?>;
}

#accountNavigation a:hover {
	text-decoration: none;
	color: <?php echo $accNavHover; ?>;
}

#accountNavigation #accountNavigation-home {
	background-image: url(../images/accountNavigationHome.gif);
	height: 50px;
	width: 111px;
}

#accountNavigation #accountNavigation-home div {
	margin-top: 25px;
	margin-left: 65px;
}

#accountNavigation #accountNavigation-contactInfo {
	background-image: url(../images/accountNavigationContactInfo.gif);
	height: 50px;
	width: 122px;
}

#accountNavigation #accountNavigation-contactInfo div {
	margin-top: 25px;
	margin-left: 36px;
}

#accountNavigation #accountNavigation-myAccount {
	background-image: url(../images/accountNavigationMyAccount.gif);
	height: 50px;
	width: 91px;
}

#accountNavigation #accountNavigation-myAccount div {
	margin-top: 25px;
}

#accountNavigation #accountNavigation-checkout {
	background-image: url(../images/accountNavigationCheckout.gif);
	height: 50px;
	width: 70px;
}

#accountNavigation #accountNavigation-checkout div {
	margin-top: 25px;
	margin-left: 0px;
}

#accountNavigation #accountNavigation-orderTracking {
	background-image: url(../images/accountNavigationOrderTrack.gif);
	height: 50px;
	width: 102px;
}

#accountNavigation #accountNavigation-orderTracking div {
	margin-top: 25px;
}

#accountNavigation #accountNavigation-returns {
	background-image: url(../images/accountNavigationReturns.gif);
	height: 50px;
	width: 129px;
}

#accountNavigation #accountNavigation-returns div {
	margin-top: 25px;
	margin-left: 10px;
}