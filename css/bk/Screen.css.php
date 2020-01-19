<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/* Screen Styles */ 
/*
 * Contains styles for the page layout and general tag styles (<H1>, <A> etc.)
 */
body {
	margin: 0;
	padding: 0;
	border: 0;
	width: 100%;
	min-height: 100%;
	background: url(../images/pageBg.gif);
	background-repeat: repeat-x;
	background-color: <?php echo $pageBg; ?>;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
	text-align: center;
}

img {
	border: 0px;
}

h1 {
	color: <?php echo $pageH1; ?>;
	font-size: 14pt;
	margin: 0px;
}

h1 a {
	color: <?php echo $pageH1; ?> !important;
}

a {
	text-decoration: none;
	color: #000000;
}

a:visited {
	text-decoration: none;
	color: #000000;
}

/* new styles */
.threeColContainer {
	border: 0px solid #f00;
	width: 1010px;
	float: left;
	background-image: url(../images/pageHorizBg.gif);
	background-repeat: repeat-y;
	text-align: left;
	overflow: a
}

.leftCol {
	width: 230px;
	height: auto;
	float: left;
	border: 0px solid #0f0;
}

.centreCol {
	width: 550px;
	float: left;
	border: 0px solid #00f;
	margin-left: 10px;
}

.rightCol {
	width: 210px;
	float: right;
	border: 0px solid #f00;
}

/*** Other columns ***/
#centerAlignPageContainer {
	margin-right: auto;
	margin-left: auto;
	width: 1010px;
	position: relative;
}

#rightNavContainer {
	width: 180px;
	border: 0px solid #f00;
	position: relative;
	float: left;
	top: -20px;
	right: -10px;
}

#rightNavContainer img .spaceTop {
	margin-top: 5px;
}

#leftNavContainer {
	width: 230px;
	min-height: 100%;
	margin: 0px;
	background-image: url(../images/leftColBg.gif);
	background-repeat: repeat-y;
	float: left;
}