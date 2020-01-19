<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/****************
* Settings Form *
*****************/
#adminSettingsViewContainer {
	width: 720px;
	display: block;
	text-align: center;
	margin-top: 10px;
}

#adminSettingsViewContentContainer {
	border: 1px solid #aaa;
	border-top: 0px solid #FFF;
	background-color: #FFFFFF;
	width: 720px;
	height: 485px;
	display: block;
	margin-left: auto;
	margin-right: auto;
	padding-top: 5px;
	overflow: auto;
	overflow-x: hidden;
	text-align: left;
}

#adminSettingsViewContentContainer label {
	display: inline;
}

#adminSettingsViewContentContainer input {
	display: inline;
}

#adminSettingsViewContentContainer h1 {
	margin: 0px;
}
