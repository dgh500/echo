<?php
header('Content-Type: text/css');
require('Colors.php');
?>
br {
	clear: both;	
}
#adminLoginForm {
	clear: both;
}
#adminLoginForm fieldset {
	width: 400px;
	margin: 20px;
}

#adminLoginForm legend {
	font-weight: bold;
	font-size: 12pt;
	color: #0A57A4;
}

#adminLoginForm label {
	width: 150px;
	font-weight: bold;
	float: left;
	text-align: right;
	margin-bottom: 10px;
	margin-right: 5px;
	border: 1px solid #FFF;
	height: 20px;
	line-height: 20px;
}

#adminLoginForm input {
	width: 200px;
	float: left;
	text-align: left;
	margin-bottom: 10px;
}
#adminLoginForm #errorBox {
	display: none;
	border: solid 2px #FF0000; 
	padding: 5px;
}
#adminLoginForm .submit {
	width: auto;
	margin-left: 150px;
	display: block;
	float: none;
}