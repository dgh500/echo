<?php
header('Content-Type: text/css');
require('Colors.php');
?>
#feedbackForm {
	margin-top: 20px;	
}
#feedbackForm input {
	display: block;
	margin-bottom: 10px;
	float: left;
	padding: 5px;
}
#feedbackForm textarea {
	display: block;
	margin-bottom: 10px;
	float: left;
	padding: 5px;
	width: 280px;
	height: 100px;
}
#feedbackForm label {
	width: 150px;
	display: block;
	float: left;
	font-weight: bold;
}
#feedbackForm br {
	clear: both;	
}
#feedbackForm #errorBox {
	padding: 5px;
}
#feedbackForm legend {
	color: <?php echo $legendColor ?>;
	font-size: 14pt;
	font-weight: bold;
	border: 1px solid <?php echo $formBorderColor ?>;
	-moz-border-radius: 5px;
	padding: 10px;
}
#feedbackForm .submit {
	display: block;
	margin-left: 150px;
}
#feedbackForm fieldset {
	padding: 15px;	
	background-color: #FFF;
	border: 1px solid <?php echo $formBorderColor ?>;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
}