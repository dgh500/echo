<?php
header('Content-Type: text/css');
require('Colors.php');
?>

<?php // Log in form ?>
#loginForm {
	border: 0px solid #000;
	background-color: <?php echo $loginFormBg ?>;
	width: 260px;
	height: 200px;
	text-align: center;
	position: relative;
	margin-left: auto;
	margin-right: auto;
}

#loginForm h1 {
	background-color: <?php echo $loginFormHeadBg ?>;
	color: #FFFFFF;
	padding: 5px;
	font-size: 12pt;
	text-align: center;
	margin-top: 0px;
	margin-bottom: 30px;
}

#loginForm input {
	margin-bottom: 10px;
}

#loginForm label {
	width: 100px;
	display: block;
	float: left;
	text-align: right;
    font-weight: bold;
}

<?php // Change Details Form ?>
#changeMyDetailsForm label {
	width: 150px;
	display: block;
	float: left;
}

#changeMyDetailsForm input, #changeMyDetailsForm select {
	margin-bottom: 10px;
}

#changeMyPasswordForm label {
	width: 150px;
	display: block;
	float: left;
}

#changeMyPasswordForm input {
	margin-bottom: 10px;
}

#forgotPasswordSubmit {
	color: #000;
	background-color: transparent;
	text-decoration: underline;
	border: none;
	cursor: pointer;
	cursor: hand;
}

<?php // Password Reset Form ?>
#passwordResetForm label {
	display: block;
	float: left;
	width: 200px;
}

#passwordResetForm input {
	margin-bottom: 10px;
}

#passwordResetForm #submit {
	margin-left: 200px;
}

<?php // My Account Section ?>
#myAccountOptionsContainer {
	border: 1px solid #CCC;
	margin-bottom: 10px;
	margin-top: 10px;	
	float: left;
	width: 100%;
	padding-bottom: 10px;
}

#myAccountOptionsList {
	margin: 0px;
	margin-left: 10px;
	padding: 0px;
	list-style-image: none;
	list-style-type: none;
	list-style-position: inside;
	float: left;
}
#myAccountOptionsList li {
	background-repeat: no-repeat;
	height: 70px;
	border: 1px solid white;
	margin: 0px;
	padding: 0px;
}
#myAccountOptionsList li div {
	width: 400px;
	margin-top: 12px;
	line-height: 18px;
}
#myAccountOptionsList li img {
	display: block;
	float: left;
}
#myAccountOptionsList li p {
	display: block;
	float: left;
	margin-left: 10px;
}
#myAccountOptionsList li a {
	font-weight: bold;
}
#myAccountOptionsList li a:hover {
	text-decoration: underline;
}
#logoutForm {
	margin-left: 475px;
}

#myAccountChangeDetailsContainer {
	border: 1px solid #CCC;
	margin-bottom: 10px;
	float: left;
	width: 100%;
}
#myAccountChangeDetailsContainer form {
	margin: 10px;
}

#myAccountViewDetailsContainer {
	border: 1px solid #CCC;
	margin-bottom: 10px;
	float: left;
	width: 100%;
	line-height: 20px;
}
#myAccountViewDetailsContainer div {
	margin: 10px;	
}

#myAccountChangePasswordContainer {
	border: 1px solid #CCC;
	margin-bottom: 10px;
	float: left;
	width: 100%;
	line-height: 20px;
}
#myAccountChangePasswordContainer div {
	margin: 10px;	
}