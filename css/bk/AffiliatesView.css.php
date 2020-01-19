<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/* Affiliate Styles */
br {
	clear: both;	
}
/* Make it obvious the affiliate area is a link */
#affiliateAreaContainer a {
	text-decoration: underline;
}
/* Hide the containers on load */
#affiliateAreaMonthlyOverviewContainer, #affiliateAreaProductBreakdownContainer, #affiliateAreaDetailsContainer, #affiliateAreaHelpContainer {
	display: none;
}

#affiliateAreaDetailsContainer, #affiliateAreaHelpContainer {
	border: 1px solid #CCC;
	-moz-border-radius: 5px;	
	-webkit-border-radius: 5px;
	margin-bottom: 10px;
	float: left;
	width: 100%;
}
#affiliateAreaDetailsContainer div, #affiliateAreaHelpContainer div {
	padding: 10px;
	line-height: 20px;
}
/* Navigation */
#affiliateAreaNavigation {
	border: 1px solid #CCC;
	margin-bottom: 10px;
	float: left;
	width: 100%;
	padding-bottom: 10px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;	
}

#affiliateOptionsList {
	margin: 0px;
	margin-left: 10px;
	padding: 0px;
	list-style-image: none;
	list-style-type: none;
	list-style-position: inside;
	float: left;
}
#affiliateOptionsList li {
	background-repeat: no-repeat;
	height: 70px;
	border: 1px solid white;
	margin: 0px;
	padding: 0px;
}
#affiliateOptionsList li div {
	width: 400px;
	margin-top: 12px;
	line-height: 18px;
}
#affiliateOptionsList li img {
	display: block;
	float: left;
}
#affiliateOptionsList li input {
	display: block;
	float: left;
}
#affiliateOptionsList li p {
	display: block;
	float: left;
	margin-left: 10px;
}
#affiliateOptionsList li a {
	font-weight: bold;
}
#affiliateOptionsList li a:hover {
	text-decoration: underline;
}



#newAffiliateRegistrationForm h1 {
	font-size: 12pt;
	text-align: center;
}

#newAffiliateRegistrationForm label {
	width: 250px;
	display: block;
	float: left;
	text-align: right;
}

#newAffiliateRegistrationForm input {
	margin-left: 10px;
	margin-bottom: 10px;
}

#newAffiliateRegistrationForm select {
	margin-left: 10px;
	margin-bottom: 10px;
}

#newAffiliateRegistrationForm .submit {
	margin-left: auto;
	margin-right: auto;
	display: block;
}

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

#affiliatesContainer {
	border: 0px solid #000;
	width: 550px;
	float: left;
}

#affiliatesContainer #loginContainer,#affiliatesContainer #registerContainer {
	width: 260px;
	float: left;
	border: 1px solid #000;
	margin-right: 5px;
	margin-left: 5px;
	height: 250px;
	background-color: <?php echo $loginFormBg; ?>;
}

#affiliatesContainer #loginContainer h1,#affiliatesContainer #registerContainer h1 {
	background-color: <?php echo $loginFormHeadBg; ?>;
	font-size: 12pt;
	margin: 0px;
	color: #FFF;
	padding: 5px;
	margin-bottom: 40px;
	text-align: center;
}

#affiliatesContainer #loginContainer input {
	margin-bottom: 10px;
}

#affiliatesContainer #loginContainer .submit {
	margin-left: 100px;
}

#affiliatesContainer #loginContainer label {
	width: 100px;
	display: block;
	float: left;
	text-align: right;
}

#affiliatesContainer #registerContainer ul,#affiliatesContainer #registerContainer p
	{
	margin-left: 10px;
}

#affiliatesContainer #registerContainer input {
	margin-left: 100px;
}

#newAffiliateRegistrationForm {
	border: 0px solid #000;
}

#newAffiliateRegistrationForm a {
	text-decoration: underline;
}

#newAffiliateRegistrationForm .required {
	font-weight: bold;
	color: #FF0000;
}