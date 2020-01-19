<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/* Footer Styles */
#footer {
	clear: both;
	float: left;
	width: 1010px;
	background-image: url(../images/footerBg.gif);
	background-repeat: no-repeat;
	background-position: bottom;
	height: 64px;
}

#footer #footerLinksContainer {
	width: 1010px;
	background-image: url(../images/footerVertBg.gif);
	background-repeat: repeat-y;
	height: 50px;
	text-align: center;
}

#footer a:hover {
	text-decoration: underline;
}