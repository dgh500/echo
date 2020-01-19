<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/*********************
* Add Catalogue Form *
**********************/
#addCatalogueForm {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
}

#addCatalogueForm fieldset {
	padding-top: 10px;
	margin-left: 15px;
	margin-top: 10px;
}

#addCatalogueForm legend {
	font-weight: bold;
}

#addCatalogueForm label {
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

#addCatalogueForm input {
	width: 200px;
	float: left;
	text-align: left;
	margin-bottom: 10px;
}

#addCatalogueForm .submit {
	width: 100px;
	float: left;
	clear: both;
	margin-left: 260px;
	text-align: center;
}

#addCatalogueForm #errorBox {
	width: 100px;
	float: left;
	margin-left: 15px;
	padding: 5px;
}
