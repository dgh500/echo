<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/******************************
* Add Top Level Category Form *
*******************************/
br {
	clear: both;	
}
#addTopLevelCategoryForm {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
}

#addTopLevelCategoryForm fieldset {
	width: 600px;
	padding-top: 10px;
	margin-left: 15px;
	margin-top: 10px;
}

#addTopLevelCategoryForm legend {
	font-weight: bold;
}

#addTopLevelCategoryForm label {
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

#addTopLevelCategoryForm input {
	width: 200px;
	float: left;
	text-align: left;
	margin-bottom: 10px;
}

#addTopLevelCategoryForm .submit {
	width: 100px;
	float: left;
	clear: both;
	margin-left: 260px;
	text-align: center;
}

#addTopLevelCategoryForm #errorBox {
	width: 100px;
	float: left;
	margin-left: 15px;
	width: 605px;
	padding: 5px;
}
