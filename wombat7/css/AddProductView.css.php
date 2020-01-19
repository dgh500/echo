<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/*******************
* Product Add Form *
********************/
#addProductForm {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
}

#addProductForm fieldset {
	width: 400px;
	padding-top: 10px;
	margin-left: 15px;
	margin-top: 10px;
	position: relative;
}

#addProductForm legend {
	font-weight: bold;
}

#addProductForm label {
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

#addProductForm input {
	width: 200px;
	float: left;
	text-align: left;
	margin-bottom: 10px;
}

#addProductForm .submit {
	width: 100px;
	float: right;
	margin-right: 40px;
	text-align: center;
}

#addProductForm #errorBox {
	width: 100px;
	float: left;
	margin-left: 15px;
	width: 605px;
	padding: 5px;
}

