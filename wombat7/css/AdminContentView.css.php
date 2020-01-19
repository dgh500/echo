<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/****************
* Content Form *
*****************/
br {
	clear: both;	
}
#adminContentViewContainer {
	width: 950px;
	display: block;
	text-align: center;
	margin-top: 10px;
}

#adminContentViewContentContainer {
	background-color: #FFFFFF;
	width: 950px;
	display: block;
	margin-left: auto;
	margin-right: auto;
	padding-top: 5px;
	overflow: auto;
	overflow-x: hidden;
}

#adminContentViewContentContainer #contentNavContainer {
	border: 1px solid #CCC;
	width: 220px;
	height: 500px;
	float: left;
	margin-right: 10px;
	overflow: scroll;
}

#adminContentViewContentContainer #contentNavContainer #contentNavList {
	margin: 0px;
	padding: 0px;
	list-style: none;
	width: 210px;
}

#add {
	font-weight: bold;
	border-bottom: 1px solid #0A57A4;
	text-align: center;
}

#adminContentViewContentContainer #contentNavContainer #contentNavList li
	{
	display: block;
	padding: 0px;
	margin: 0px;
	float: left;
	width: 210px;
	text-align: left;
}

#adminContentViewContentContainer #contentNavContainer #contentNavList li a
	{
	background-color: #FFF;
	padding: 5px;
	width: 100%;
	display: block;
	text-decoration: none;
	color: #000000;
}

#adminContentViewContentContainer #contentNavContainer #contentNavList li a:hover
	{
	background-color: #C0D2EC;
	width: 100%;
	display: block;
	text-decoration: underline;
	color: #000000;
}

#adminContentViewContentContainer #contentEditAreaContainer {
	width: 710px;
	float: left;
	text-align: left;
	padding-top: 10px;
}

#adminContentViewContentContainer #contentEditAreaContainer label {
	font-weight: bold;
}

#adminContentViewContentContainer #contentEditAreaContainer input {
	margin-bottom: 10px;
}

#contentDescriptionContentArea {
	border: 1px solid #84B0C7;
	border-top: 0px;
	-moz-border-radius-bottomleft: 5px;
	-moz-border-radius-bottomright: 5px;		
	text-align: left;
	height: 540px;
	padding: 5px;
}

#contentImageContentArea {
	border: 1px solid #84B0C7;
	border-top: 0px;
	-moz-border-radius-bottomleft: 5px;
	-moz-border-radius-bottomright: 5px;		
	text-align: left;
	padding: 5px;
	height: 540px;	
	display: none;
}
#contentImageContentArea iframe {
	height: 80px;
}
/* Tab Styles */
#adminContentViewTabContainer {
	background: url("../images/tab_b.gif") repeat-x bottom;
	width: 710px;
	height: 26px;
	display: block;
}
#adminContentViewTabContainer ul {
	list-style: none;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
	margin: 0px;
	margin-left: 5px;
	padding: 0px;
}
#adminContentViewTabContainer li {
	float: left;
	background: url("../images/prodLeft_both.gif") no-repeat left top;
	margin: 0;
	padding: 0 0 0 9px;
	height: 25px;
	border-bottom: 0px solid #FFF;
}
#adminContentViewTabContainer a {
	float: left;
	display: block;
	width: .1em;
	background: url("../images/prodRight_both.gif") no-repeat right top;
	padding: 5px 15px 4px 6px;
	text-decoration: none;
	font-weight: bold;
	color: #1A419D;
}

#adminContentViewTabContainer>ul a {
	width: auto;
}
#adminContentViewTabContainer li:hover {
	background-position: 0 -150px;
	color: #F00;
}
#adminContentViewTabContainer a:hover {
	background-position: 100% -150px;
	color: #F00;
}


