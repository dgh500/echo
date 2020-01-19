<?php
header('Content-Type: text/css');
require('Colors.php');
?>
/* Shop By Dept */
#shopByDept {
	padding: 0px;
	width: 189px;
	border: 0px;
	position: relative;
	top: -30px;
	list-style: none;
	margin-bottom: 10px;
	margin-top: 0px;
	margin-right: 5px;
	margin-left: 35px;
	padding: 0px;
	text-indent: -1em;	
}
html>body #shopByDept {
	top: -10px;	
}

#shopByDept a {
	text-decoration: none;
	background-color: <?php echo $shopByDeptBg; ?>;
	color: #000000;
	display: block;
}

#shopByDept a:hover {
	text-decoration: none;
	background-color: <?php echo $shopByDeptBgOver; ?>;
	color: #FFFFFF;
	display: block;
}

#shopByDept li {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 10pt;
	font-weight: bold;
	display: block;
	float: left;
	height: 26px;
	margin: 0px;
	padding: 0px;
}
#shopByDept li.dept {
	border-left: 1px solid <?php echo $shopByDeptBorderColor; ?>;
	border-right: 1px solid <?php echo $shopByDeptBorderColor; ?>;
	width: 189px;
	padding: 0px;
	height: 25px;
	line-height: 25px;
	text-indent: 5px;
}
html>body #shopByDept li.dept {
	width: 187px;
}
#shopByDept #shopByDept-title {
	background-image: url(../images/shopByDeptBg.gif);
	text-align: center;
	color: #FFFFFF;
	width: 189px;
}

#shopByDept #shopByDept-title div {
	margin-top: 4px;
}

#shopByBrandContainer {
	float: right;
	margin-right: 5px;
	height: 20px;
	border: 0px solid #f00;
	position: relative;
	top: -25px;
}

#shopByBrandContainer select {
	width: 190px;
}

#shopByTagContainer {
	float: right;
	margin-right: 5px;
	height: 20px;
	border: 0px solid #FFF;
	position: relative;
	top: -37px;
}
html>body #shopByTagContainer {
	top: -18px;
}
#shopByTagContainer select {
	width: 190px;
}